(function ($, window) {
    'use strict';
    /* Billing */
    $.widget('lotusbreath.onestepcheckout', $.lotusbreath.onestepcheckout, {
        options: {
            coupon: {
                applyCouponBtn: "#apply_coupon_btn",
                cancelCouponBtn: "#cancel_coupon_btn",
                couponInput: "#coupon_code"
            }
        },
        _submitCoupon: function (isRemove) {
            var _this = this;
            var params = $("#checkout_form").serializeArray();
            params[params.length] = {'name': 'coupon_code', 'value': $(_this.options.coupon.couponInput).val()};
            params[params.length] = {'name': 'remove', 'value': ((isRemove) ? 1 : 0)};
            $.ajax({
                url: _this.options.coupon.applyCouponUrl,
                type: 'POST',
                data: params,
                //async : false,
                beforeSend: function () {
                    //$("#shippingmethod-error").html("");
                    _this._loadWait('review_partial');
                    _this._loadWait('payment_partial');
                    _this._loadWait('shipping_partial');
                    $("#coupon-success-msg").html('');
                    $("#coupon-error-msg").html('');
                },
                complete: function (response) {
                    try {
                        var responseObject = $.parseJSON(response.responseText);

                    } catch (ex) {

                        _this._removeWait();
                        return false;
                    }


                    if (responseObject.results.success == true) {
                        $("#coupon-success-msg").html(responseObject.results.message);
                        if (isRemove) {
                            $(_this.options.coupon.cancelCouponBtn).addClass('hidden');
                            $(_this.options.coupon.couponInput).val('')
                        } else {
                            $(_this.options.coupon.cancelCouponBtn).removeClass('hidden');

                        }
                        $(_this.options.coupon.applyCouponBtn).addClass('disabled');
                        $(_this.options.coupon.applyCouponBtn).attr('disabled', 'disabled');
                    }
                    if (responseObject.results.success == false) {

                        $("#coupon-error-msg").html(responseObject.results.message);
                    }
                    _this._updateHtml(responseObject);

                }

            });
        },
        _create: function () {
            this._super();
            var _this = this;
            this.element
                .on('click', _this.options.coupon.applyCouponBtn,
                function (event) {
                    _this._submitCoupon();

                })
                .on('click', _this.options.coupon.cancelCouponBtn, function () {
                //_this.options.coupon.cancelCouponBtn
                _this._submitCoupon(true);
            })
                .on('keyup', _this.options.couponInput, function () {
                var btnApply = $(_this.options.coupon.applyCouponBtn);

                if (!$("#coupon_code").val()) {
                    btnApply.addClass('disabled');
                    btnApply.attr('disabled', 'disabled');
                } else {
                    btnApply.removeClass('disabled');
                    //btnApply.attr('disabled', '');
                    btnApply.removeAttr('disabled');
                }
            })
            ;
        }

    });
})(jQuery);