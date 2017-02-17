(function ($, window) {
    'use strict';
    /**
     * Review and place order
     */
    $.widget('lotusbreath.onestepcheckout', $.lotusbreath.onestepcheckout, {
        options: {
            review: {
                showEditCartBtn: '#edit_cart_action',
                updateCartBtn: '#update_cart_action',
                cancelCartBtn: '#cancel_cart_action'
            }
        },
        _create: function () {
            this._super();
            var _this = this;
            this.element
                .on('click', this.options.review.showEditCartBtn, function (e) {
                    e.preventDefault();
                    $(".spinner-qty").show();
                    $(".spinner-qty").parent().show();
                    $(_this.options.review.updateCartBtn).show();
                    $(_this.options.review.cancelCartBtn).show();
                    $(".remove-item-diplay").show();
                    this.hide();
                })
                .on('click', this.options.review.cancelCartBtn, function (e) {
                    $(_this.options.review.showEditCartBtn).show();
                    $(_this.options.review.updateCartBtn).hide();
                    $(_this.options.review.cancelCartBtn).hide();
                    $(".spinner-qty").hide();
                    $(".spinner-qty").parent().hide();
                    $(".remove-item-diplay").hide();
                })
                .on('click', this.options.review.updateCartBtn, function (e) {
                    //var params = $("#checkout-review-table-wrapper input").serializeArray();
                    var params = $("#checkout_form").serializeArray();
                    //params.step = 'payment_method';
                    $.ajax({
                        url: _this.options.review.updateCarUrl,
                        type: 'POST',
                        //async : false,
                        data: params,
                        beforeSend: function () {
                            _this._loadWait('review_partial');
                            _this._loadWait('shipping_partial');
                            _this._loadWait('payment_partial');
                        },
                        complete: function (response) {
                            try {
                                var responseObject = $.parseJSON(response.responseText);

                            } catch (ex) {
                                _this._removeWait();
                                return false;
                            }
                            _this._updateHtml(responseObject);
                        }

                    });
                })
            ;
            this._addRemoveCartEvent();
        },
        _addRemoveCartEvent: function () {
            var _this = this;
            $(".cart-remove-btn").click(function (e) {
                e.preventDefault();
                var params = {id: $(this).attr('rel')};
                $.magnificPopup.open(
                    {
                        items: {
                            type: 'inline',
                            src: '#confirm_dialog',
                            modal: true
                        },
                        callbacks: {
                            open: function () {
                                $("#confirm_dialog .content").html(_this.options.review.corfirmRemoveCartItemMsg);
                                $("#confirm_dialog .btn_cancel").click(function () {
                                    $.magnificPopup.close();
                                });
                                $("#confirm_dialog .btn_ok").click(function () {
                                    $.magnificPopup.close();
                                    $.ajax({
                                        url: _this.options.review.clearCartItemUrl,
                                        type: 'POST',
                                        data: params,
                                        //async : false,
                                        beforeSend: function () {
                                            _this._loadWait('review_partial');
                                            _this._loadWait('payment_partial');
                                            _this._loadWait('shipping_partial');
                                        },
                                        complete: function (response) {
                                            try {
                                                var responseObject = $.parseJSON(response.responseText);

                                            } catch (ex) {
                                                _this._removeWait();
                                                return false;
                                            }

                                            if (responseObject.results == false) {
                                                if (responseObject.cart_is_empty) {
                                                    window.location.reload();
                                                }
                                            }
                                            _this._updateHtml(responseObject);

                                        }
                                    });

                                })
                            },
                            close: function () {
                            }
                        }
                    }
                );

                return false;

            });
        },
        _addInputCartQty: function () {
            $(".spinner-qty").each(function () {
                var maxQty = 100000;
                if ($(this).attr('data-max'))
                    maxQty = $(this).attr('data-max');
                $(this).spinner({min: 1, max: maxQty});
            })

        },
        _checkAgreements: function () {
            var agreements = this.element.find('[name^="agreement["]');
            if (agreements.length == 0) return true;
            if (agreements.length && agreements.filter('input:checkbox:checked').length == agreements.length) {
                return true;
            }
            $("#agreenment-error").html($.mage.__(this.options.termErrorMsg));
            return false;
        }
    });
})(jQuery);