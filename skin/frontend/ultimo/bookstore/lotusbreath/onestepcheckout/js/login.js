(function ($, window) {
    'use strict';
    /* Login */
    $.widget('lotusbreath.onestepcheckout', $.lotusbreath.onestepcheckout, {

        _create: function () {
            this._super();
            var _this = this;
            var that = this;
            $(".login_link").magnificPopup(
                {
                    items: {
                        type: 'inline',
                        src: '#loginFrm',
                        prependTo: that.options.checkout.checkoutForm
                    }
                }
            );

            $("#loginFrm").submit(function (event) {
                event.preventDefault();
                if (( $(this).validation() && $(this).validation('isValid') )) {
                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: $(this).serializeArray(),
                        //async : false,
                        beforeSend: function () {
                            $("#loginFrm .mage-error").html('');
                            _this._loadWait('loginFrm');
                        },
                        success: function (response) {
                            var resultO = $.parseJSON(response);
                            if (resultO.success) {
                                //window.location.reload();
                                history.go(0);
                            } else {
                                $("#loginFrmErrMsg").html(resultO.messages[0]);
                            }
                            _this._removeWait('loginFrm');
                            //
                        }
                    });
                }
            });

            $("#loginFrmForgotLink").magnificPopup(
                {
                    items: {
                        type: 'inline',
                        src: '#forgotFrm',
                        prependTo: that.options.checkout.checkoutForm
                    }
                }
            );

            $("#forgotFrm").submit(function (event) {
                event.preventDefault();
                if (( $(this).validation() && $(this).validation('isValid') )) {
                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: $(this).serializeArray(),
                        //async : false,
                        beforeSend: function () {
                            $("#forgotFrm .mage-error").html('');
                            _this._loadWait('forgotFrm');

                        },
                        success: function (response) {
                            var resultO = $.parseJSON(response);
                            if (resultO.success) {

                                $("#loginFrmSuccessMsg").html(resultO.messages[0]);
                                $("#forgotBackLoginLink").trigger('click');

                            } else {
                                $("#forgotFrmErrMsg").html(resultO.messages[0]);
                            }
                            _this._removeWait('forgotFrm');
                            //
                        }
                    });
                }
            });
        },


    })
})(jQuery);