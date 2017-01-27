(function ($, window) {
    'use strict';
    /* Payment */
    $.widget('lotusbreath.onestepcheckout', $.lotusbreath.onestepcheckout, {
        options: {
            payment: {
                continueSelector: '#payment-buttons-container .button',
                form: '#co-payment-form',
                divId: '#payment_partial',
                methodsContainer: '#checkout-payment-method-load',
                rewardPointsCheckBoxSelector: '#use-reward-points',
                customerBalanceCheckBoxSelector: '#use-customer-balance',
                freeInput: {
                    tmpl: '<input id="hidden-free" type="hidden" name="payment[method]" value="free">',
                    selector: '#hidden-free'
                }
            }
        },
        _create: function () {
            this._super();

            this.element

                .on('updateCheckoutPrice', $.proxy(function (event, data) {
                    if (data.price) {
                        this.checkoutPrice += data.price;
                    }
                    if (data.totalPrice) {
                        data.totalPrice = this.checkoutPrice;
                    }
                    if (this.checkoutPrice < this.options.minBalance) {
                        // Add free input field, hide and disable unchecked checkbox payment method and all radio button payment methods
                        this._disablePaymentMethods();
                    } else {
                        // Remove free input field, show all payment method
                        this._enablePaymentMethods();
                    }
                }, this))
                .on('contentUpdated', this.options.payment.divId, $.proxy(function () {
                    $(this.options.payment.form).find('dd [name^="payment["]').prop('disabled', true);
                    var checkoutPrice = this.element.find(this.options.payment.form).find('[data-checkout-price]').data('checkout-price');
                    $(this.options.payment.customerBalanceCheckBoxSelector)
                        .prop({
                            'checked': this.options.customerBalanceSubstracted,
                            'disabled': false
                        }).change().parent().show();
                    $(this.options.payment.rewardPointsCheckBoxSelector)
                        .prop({
                            'checked': this.options.rewardPointsSubstracted,
                            'disabled': false
                        }).change().parent().show();
                    if ($.isNumeric(checkoutPrice)) {
                        this.checkoutPrice = checkoutPrice;
                    }
                    if (this.checkoutPrice < this.options.minBalance) {
                        this._disablePaymentMethods();
                    } else {
                        this._enablePaymentMethods();
                    }
                }, this))
                .on('click', this.options.payment.divId + ' dt input:radio:visible', $.proxy(this._paymentMethodHandler, this))
                .find(this.options.payment.form).validation();
            //$(this.options.payment.divId + ' dt input:radio:checked').trigger('click');
            this._updateAfterReloadPayment();

        },
        _updateAfterReloadPayment: function () {
            var methods = this.element.find('[name="payment[method]"]');
            if (methods.length == 1) {
                $(methods[0]).parent().parent().nextUntil('dt').find('ul').show().find('[name^="payment["]').prop('disabled', false);
            } else {
                if (methods.length > 1) {
                    var _ele = methods.filter('input:radio:checked');
                    if (_ele.length) {
                        var parentsDl = _ele.closest('dl');
                        parentsDl.find('dt input:radio').prop('checked', false);
                        _ele.prop('checked', true);
                        parentsDl.find('dd ul').hide().find('[name^="payment["]').prop('disabled', true);
                        _ele.parent().nextUntil('dt').find('ul').show().find('[name^="payment["]').prop('disabled', false);
                    }
                }
            }
        },

        /**
         * Display payment details when payment method radio button is checked
         * @private
         * @param e
         */
        _paymentMethodHandler: function (e) {
            var _this = $(e.target),
                parentsDl = _this.closest('dl');
            parentsDl.find('dt input:radio').prop('checked', false);

            _this.prop('checked', true);
            parentsDl.find('dd ul').hide().find('[name^="payment["]').prop('disabled', true);

            _this.parent().nextUntil('dt').find('ul').show().find('[name^="payment["]').prop('disabled', false);
            _this.parent().nextUntil('dt').find('div').show().find('[name^="payment["]').prop('disabled', false);
            this._savePayment();
        },
        current_payment_method : function(){
            this.options.currentPaymentMethod = $('input[name="payment[method]"]:checked').val();
            return  this.options.currentPaymentMethod;
        },
        _savePayment: function () {
            var _this = this;
            //var params = $("#payment_partial input").serializeArray();
            window.oscObserver.beforeUpdatePaymentMethod();
            var params = $("#checkout_form").serializeArray();
            params[params.length] = {'name': 'step', 'value': 'payment_method'};

            $.ajax({
                url: _this.options.saveStepUrl,
                type: 'POST',
                data: params,
                //async : false,
                beforeSend: function () {
                    _this._loadWait('review_partial');
                    if (_this.options.payment.loading_shipping_method)
                        _this._loadWait('shipping_partial');
                },
                complete: function (response) {
                    try {
                        var responseObject = $.parseJSON(response.responseText);
                        window.oscObserver.afterUpdatePaymentMethod(responseObject);
                    } catch (ex) {
                        _this._removeWait();
                        return false;
                    }

                    _this._updateHtml(responseObject);
                }

            });
        },

        /**
         * make sure one payment method is selected
         * @private
         * @return {Boolean}
         */
        _validatePaymentMethod: function () {
            $("#payment-error").html('');
            var methods = this.element.find('[name^="payment["]');
            if (methods.length === 0) {
                this._showError("#payment-error", $.mage.__('Your order cannot be completed at this time as there is no payment methods available for it.'));
                return false;
            }
            if (this.checkoutPrice < this.options.minBalance) {
                return true;
            } else if (methods.filter('input:radio:checked').length) {
                return true;
            }
            this._showError("#payment-error", $.mage.__('Please specify payment method.'));
            return false;
        },

        /**
         * Disable and enable payment methods
         * @private
         */
        _disablePaymentMethods: function () {
            var paymentForm = $(this.options.payment.form);
            paymentForm.find('input[name="payment[method]"]').prop('disabled', true);
            paymentForm.find(this.options.payment.methodsContainer).hide().find('[name^="payment["]').prop('disabled', true);
            paymentForm.find('input[id^="use"][name^="payment[use"]:not(:checked)').prop('disabled', true).parent().hide();
            paymentForm.find(this.options.payment.freeInput.selector).remove();
            $.tmpl(this.options.payment.freeInput.tmpl).appendTo(paymentForm);
        },
        /**
         * Enable and enable payment methods
         * @private
         */
        _enablePaymentMethods: function () {
            var paymentForm = $(this.options.payment.form);
            paymentForm.find('input[name="payment[method]"]').prop('disabled', false);
            paymentForm.find(this.options.payment.methodsContainer).show();
            paymentForm.find('input[id^="use"][name^="payment[use"]:not(:checked)').prop('disabled', false).parent().show();
            paymentForm.find(this.options.payment.freeInput.selector).remove();
        }
    });

})(jQuery, window);