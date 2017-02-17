/**
 * lotusbreath
 * File osc.js
 */
(function ($, window) {
    'use strict';
    $.widget('lotusbreath.onestepcheckout', {
        options: {
            checkout: {
                loginFormSelector: '#login-form',
                continueSelector: '#lbonepage-place-order-btn',
                registerCustomerPasswordSelector: '#register-customer-password',
                suggestRegistration: false,
                checkoutForm: '#checkout_form'
            },
            currentPaymentMethod : false,
            currentShippingMethod : false
        },
        _previous_data: {}
        ,

        _create: function () {
            var _this = this;
            this._addInputCartQty();
            $(document).bind("ajaxSend", function (e, xhr, ajaxOptions) {
                var parametters = $.parseParams(ajaxOptions.data);
                if (_this.options.autoDetectUrl == ajaxOptions.url)
                    return false;
                if (parametters.step) {
                    var stop = false;
                    $("#checkout_form").validation();
                    var validator = $("#checkout_form").validate();
                    var isCheckedBilling = false;
                    var isCheckedShipping = false;
                    if (
                        parametters.step == 'payment_method' ||
                        (parametters.step == 'shipping_method' && $("#billing\\:use_for_shipping_yes").is(":checked") )) {
                        isCheckedBilling = false;

                    } else {
                        if (parametters.step == 'shipping_method') {
                            isCheckedShipping = true;
                        }
                    }
                    if (isCheckedBilling) {
                        $("#billing-new-address-form .change_location_field.required-entry").each(function () {
                            if ($(this).is(":visible")) {
                                if (!( $(this).valid())) {
                                    stop = true;
                                    validator.focusInvalid();
                                }
                            }
                        });
                    }
                    if (isCheckedShipping) {
                        $("#shipping-new-address-form .change_location_field.required-entry").each(function () {
                            if ($(this).is(":visible")) {
                                if (!( $(this).valid())) {
                                    stop = true;
                                    validator.focusInvalid();
                                }
                            }
                        });
                    }
                    if (stop) {
                        xhr.abort();
                        _this._removeWait();
                        return false;
                    }
                }
                $("p.mage-error").html('');
                $("span.mage-error").html('');
                $("div.mage-error").html('');
                $(".error").html('');
                _this._runLoadWait();
            });

            $(document).bind("ajaxStop", function (e) {
                _this._removeWait();
            });
            $(document).bind("ajaxcomplete", function (e) {
                _this._removeWait();
            });
            this.bindSubmitOrderEvent();
            var that = this;
            $(".a-agreement").each(function () {
                $(this).magnificPopup(
                    {
                        items: {
                            type: 'inline',
                            src: "#" + $(this).attr('rel'),
                            prependTo: that.options.checkout.checkoutForm
                        },
                        fixedContentPos: true
                        //modal: true
                    }
                );
            });



            return this;

        },
        bindSubmitOrderEvent: function(){
            var _this = this;
            $(this.options.checkout.continueSelector).bind('click.lotusOSC', function(e){
                e.preventDefault();
                $.proxy(_this._save($(this)), _this);
                $(".mage-error").show();
                return false;
            });
        },
        _showError: function (errIdSel, message) {
            if (errIdSel) {
                try {
                    if (errIdSel && message) {
                        $(errIdSel).html(message);
                        var ext_scrolled = 20;
                        if ($("#mj-topbar").length) {
                            ext_scrolled = $("#mj-topbar").outerHeight();
                        }
                        var scrollPos = $(errIdSel).offset().top - $(errIdSel).outerHeight() - ext_scrolled;
                        $('html,body').animate({scrollTop: scrollPos}, 500);
                    }

                } catch (e) {
                }
            }

        },
        _removeWait: function (elID) {
            //overlayBlock
            if ($("#osc-loader").length) {
                $("#osc-loader").remove();
            }

        },
        _runLoadWait: function () {
            if (this._itemsLoading.length == 0)
                return false;
            var _this = this;
            var isOverlay = false;
            var loaderTemplateScript = $("#loader-template").html();  
            var loaderTemplate = Handlebars.compile(loaderTemplateScript);
            var isPopup = false;
            var popupId = '';

            for (var elID in this._itemsLoading) {
                if (elID == 'loginFrm' || elID == 'forgotFrm') {
                    isPopup = true;
                    popupId = elID;
                }
                delete this._itemsLoading[elID];
            }
            if (isPopup) {
                $('#' + popupId).append(loaderTemplate(loaderJson));
            } else {
                $('body').append(loaderTemplate(loaderJson));
            }

            return false;

        },
        _itemsLoading: {},
        _loadWait: function (elID, isOverlay) {
            if (!isOverlay) isOverlay = false;
            this._itemsLoading[elID] = isOverlay;
        },

        _updateHtml: function (responseObject) {
            var _this = this;
            if (responseObject.previous_data) {
                this._previous_data = responseObject.previous_data;
            }
            _this._removeWait();
            var updateItems = new Array();
            if (responseObject.htmlUpdates) {
                for (var idx in responseObject.htmlUpdates) {
                    if (responseObject.update_items.indexOf(idx) >= 0) {
                        $("#" + idx).html(responseObject.htmlUpdates[idx]);
                        if (idx == 'review_partial') {
                            this.bindSubmitOrderEvent();
                            this._addInputCartQty();
                            this._addRemoveCartEvent();
                            $(".a-agreement").each(function () {
                                //console.log();
                                $(this).magnificPopup(
                                    {
                                        items: {
                                            type: 'inline',
                                            src: "#" + $(this).attr('rel'),
                                            prependTo: _this.options.checkout.checkoutForm
                                        },
                                        fixedContentPos: true
                                        //modal: true
                                    }
                                );
                            });
                        }
                        updateItems.push(idx);
                    }
                }
                _this._updatePreviousData(updateItems);
                window.oscObserver.afterLoadingNewContent(updateItems, this._previous_data );

            }

        },

        _updatePreviousData: function (updateItems) {
            for (var idx in updateItems) {
                if (updateItems[idx] == 'payment_partial') {

                    if (this._previous_data['payment']) {
                        var data = this._previous_data['payment'];
                        data.method = null;
                        this._updatePartialForm('payment', data);
                    }

                    this._updateAfterReloadPayment();

                }
            }
        },
        _updatePartialForm: function (formName, data) {
            if (data) {
                for (var idx in data) {
                    if (idx == 'method')
                        continue;
                    var paymentMethodInput = $('input[name="' + formName + '[' + idx + ']"]');
                    if (paymentMethodInput.length && data[idx] != '') {
                        paymentMethodInput.val(data[idx]);
                    }
                    var paymentMethodSelect = $('select[name="' + formName + '[' + idx + ']"]');
                    if (paymentMethodSelect.length && data[idx] != '') {
                        paymentMethodSelect.val(data[idx]);
                    }
                }
            }

        },
        _openConfirmExistEmail: function () {
            var _this = this;
            $("#confirm_dialog .content").html(_this.options.billing.checkExistsMsg);

            $("#confirm_dialog .btn_ok").click(function () {
                $("#login-email").val($("#billing\\:email").val());
                var mfp = $.magnificPopup.instance;
                mfp.items[0] =
                {
                    type: 'inline',
                    src: '#loginFrm',
                    prependTo: _this.options.checkout.checkoutForm
                }
                ;
                mfp.updateItemHTML();
                return false;

            }).find('.btn_text').html(_this.options.confirmCheckEmail.login_btn_text);

            $("#confirm_dialog .btn_cancel").click(function () {
                $.magnificPopup.close();
                _this._removeWait();
                $("#billing\\:email").focus();
            }).find('.btn_text').html(_this.options.confirmCheckEmail.cancel_btn_text);

            $.magnificPopup.open(
                {
                    items: {
                        type: 'inline',
                        src: '#confirm_dialog',
                        modal: true
                    }
                }
            );
        },

        placeOrder: function () {
            window.oscObserver.beforeSubmitOrder();

            /**
             * stop submitting order
             */
            if(window.oscObserver.stopSubmittingOrder && window.oscObserver.stopSubmittingOrder == true)
                return;

            var _this = this;
            var data = $("#checkout_form").serializeArray();
            //alert($("#braintree_nonce").val());
            //braintree_nonce
            /*
            if($("#braintree_nonce")){
                data[data.length] = {'name': 'payment[nonce]', 'value': $("#braintree_nonce").val() };
            }
            */

            var url = this.options.submitUrl;

            $.ajax({
                url: url,
                type: 'post',
                context: this,
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    $(".error").html('');
                    _this._loadWait('checkoutSteps', true);
                },

                error: function (request, status, error) {
                },
                complete: function (response) {
                    try {
                        var responseObject = $.parseJSON(response.responseText);
                        var result = responseObject.results;
                    } catch (ex) {
                        _this._removeWait();
                        return false;
                    }
                    var isError = false;
                    _this._updateHtml(responseObject);
                    _this._removeWait('checkoutSteps');


                    $("#saveOder-error").html('');
                    if (result.save_order && result.save_order.error && result.save_order.error == true) {
                        //error_messages
                        $("#saveOder-error").html(result.save_order.error_messages);
                        isError = true;
                    }

                    $(".mage-error").show();
                    if (result.billing && typeof(result.billing.error) != "undefined" && result.billing.error != 0) {
                        _this._showError("#billing-error", result.billing.message);
                        isError = true;
                    } else {
                        $("#billing-error").html('');
                    }
                    //payment-error
                    if (result.payment && typeof(result.payment.error) != "undefined" && result.payment.error != 0) {
                        _this._showError("#payment-error", result.payment.message);
                        isError = true;
                    } else {
                        $("#payment-error").html('');
                    }
                    if (result.shipping_method && typeof(result.shipping_method.error) != "undefined" && result.shipping_method.error != 0) {
                        _this._showError("#shippingmethod-error", result.shipping_method.message);
                        isError = true;
                    } else {
                        $("#shippingmethod-error").html('');
                    }

                    if (isError == true)
                        return false;

                    result = window.oscObserver.afterSaveOrder(responseObject, result);

                    if (responseObject.success == false) {
                        if (responseObject.update_section) {
                            if (responseObject.update_section.name == 'paypaliframe') {
                                _this._removeWait('checkoutSteps');
                                $("#lbonepage-place-order-btn").hide();
                                $("#checkout-paypaliframe-load").html(responseObject.update_section.html);
                                $.magnificPopup.open(
                                    {
                                        items: {
                                            type: 'inline',
                                            src: '#checkout-paypaliframe-load',
                                            modal: true
                                        },
                                        modal: true
                                    }
                                );
                                return;
                            }
                        }
                    }

                    if (result.payment && result.payment.redirect) {
                        window.location = result.payment.redirect;
                        return;
                    }
                    if (result.save_order && result.save_order.success == true) {
                        var redirectUrl = this.options.review.successUrl;
                        if (result.save_order.redirect) {
                            redirectUrl = result.save_order.redirect;
                        }

                        window.location = redirectUrl;
                    }
                }
            });
        },

        validate: function() {
            var isValid = true;
            $("#checkout_form").validation();

            $("#checkout_form").validation('clearError')
            if (!($("#checkout_form").valid('isValid'))){
                var validator = $("#checkout_form").validate();
                validator.focusInvalid();
                isValid = false;
            }
            //isValid = false;

            isValid = isValid & this._validateShippingMethod();

            isValid = isValid & this._validatePaymentMethod();

            isValid = isValid & this._checkAgreements();

            return isValid;
        },
        save : function(){
            this._save();
        },
        _save: function () {
            var _this = this;
            if (this.validate()){
                var isCheckExistEmail = $("input[name='billing[create_new_account]']").is(":checked") || $("#billing\\:email").hasClass('check-email-exists');
                var checkEmailOk = true;
                if (isCheckExistEmail) {
                    checkEmailOk = false;
                    $.ajax({
                        url: _this.options.billing.checkExistsUrl,
                        type: 'POST',
                        context: this,
                        data: {email: $("#billing\\:email").val()},
                        complete: function (response) {
                            try {
                                var responseObject = $.parseJSON(response.responseText);
                                var result = responseObject.results;
                            } catch (ex) {
                                _this._removeWait();
                                return false;
                            }
                            _this._removeWait('checkoutSteps');
                            if (responseObject && responseObject.success == false) {
                                _this._openConfirmExistEmail();
                                checkEmailOk = false;
                            } else {
                                _this.placeOrder();
                                checkEmailOk = true;
                            }
                        }
                    });
                } else {
                    _this.placeOrder();
                }
                if (checkEmailOk == false)
                    return;
            } else {
            }

        },
        /* Call when update location of address that cause to change shipping rates, shipping methods ,or payment  */
        _updateLocation: function (data, typeUpdate) {
            var _this = this;
            if (!typeUpdate)
                typeUpdate = 'shipping';
            var _this = this;
            var params = $("#checkout_form").serializeArray();
            if (typeUpdate == 'billing') {
                params[params.length] = {'name': 'step', 'value': 'update_location_billing'};
                window.oscObserver.beforeUpdateBilling();
                window.oscObserver.beforeUpdateShipping();

            } else {
                if (typeUpdate == 'billing_shipping') {
                    window.oscObserver.beforeUpdateBilling();
                    window.oscObserver.beforeUpdateShipping();
                    params[params.length] = {'name': 'step', 'value': 'update_location_billing_shipping'};
                } else {
                    oscObserver.beforeUpdateShipping();
                    params[params.length] = {'name': 'step', 'value': 'update_location'};
                }
            }
            if (_this.isSavingAddress)
                return;
            $.ajax({
                url: _this.options.saveStepUrl,
                type: 'POST',
                data: params,
                //async : false,
                beforeSend: function () {
                    _this.isSavingAddress = true;
                    if (typeUpdate == 'billing') {
                        _this._loadWait('payment_partial');
                    }
                    if (typeUpdate == 'billing_shipping') {
                        _this._loadWait('shipping_partial');
                        _this._loadWait('payment_partial');
                    }
                    if (typeUpdate == 'shipping') {
                        _this._loadWait('shipping_partial');
                    }
                    _this._loadWait('review_partial');
                },
                complete: function (response) {
                    try {
                        var responseObject = $.parseJSON(response.responseText);
                        _this._updateHtml(responseObject);
                        if (typeUpdate == 'billing') {
                            _this._loadWait('payment_partial');
                        }else{
                            if (typeUpdate == 'billing_shipping') {
                                window.oscObserver.afterUpdatingBilling(responseObject);
                                window.oscObserver.afterUpdateShipping(responseObject);
                            }else{
                                window.oscObserver.afterUpdateShipping(responseObject);
                            }
                        }
                        _this.isSavingAddress = false;
                    } catch (ex) {
                        _this.isSavingAddress = false;
                    }
                },
                error: function () {
                    _this.isSavingAddress = false;
                }

            });
        }
    });


    /*
     $.widget('lotusbreath.onestepcheckout', $.lotusbreath.onestepcheckout, {
     _create: function() {
     this._super();
     var _this = this;
     $.magnificPopup.open(
     {
     items: {
     type: 'inline',
     src : '#lb_osc_login',
     modal: true
     },
     //modal: true

     }

     );
     onepageLogin = function(){
     var form = $("#lb_osc_login_frm");
     if (( form.validation() && form.validation('isValid') )) {
     $.ajax({
     url : _this.options.login.loginUrl,
     type : 'POST',
     data : form.serializeArray(),
     beforeSend : function(){

     $("#loginFrm .mage-error").html('');
     _this._loadWait('loginFrm');
     },
     success : function(response){
     var resultO = $.parseJSON (response);
     if (resultO.success) {
     window.location.reload();
     }else {
     $("#loginFrmErrMsg").html(resultO.messages[0]);
     }
     _this._removeWait('loginFrm');
     //
     }
     });
     }
     }
     }
     });
     */


})(jQuery, window);
