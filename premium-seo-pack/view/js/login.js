if (jQuery('#psp_blocklogin').length > 0) {
    psp_blocklogin();
} else {
    jQuery(document).ready(function () {
        psp_blocklogin();
    });
}
function psp_blocklogin() {
    jQuery('#sq_email').on('keypress', function (event) {

        if (event.keyCode === 13)
            sq_autoLogin();

        return event.keyCode !== 13;
    });

    jQuery('#sq_user').on('keypress', function (event) {

        if (event.keyCode === 13)
            jQuery('#sq_login').trigger('click');

        return event.keyCode !== 13;
    });

    jQuery('#sq_password').on('keypress', function (event) {

        if (event.keyCode === 13)
            jQuery('#sq_login').trigger('click');

        return event.keyCode !== 13;
    });

    jQuery('#sq_signin').on('click', function (event) {
        jQuery('#sq_autologin').hide();
        jQuery('#psp_blocklogin').find('ul').show();

        //jQuery('#psp_blocklogin').find('.sq_message').html(response.info).show();
        jQuery('#sq_user').val(jQuery('#sq_email').val());
        jQuery('#sq_email').focus();
    });

    jQuery('#sq_signup').on('click', function (event) {
        jQuery('#sq_autologin').show();
        jQuery('#psp_blocklogin').find('ul').hide();

        //jQuery('#psp_blocklogin').find('.sq_message').html(response.info).show();
        //jQuery('#sq_user').val(jQuery('#sq_email').val());
        jQuery('#sq_email').focus();
    });

    jQuery('#sq_login').on('click', function () {
        jQuery('#sq_login').addClass('sq_minloading');
        jQuery('#sq_login').attr("disabled", "disabled");
        jQuery('#sq_login').val('');

        jQuery.post(
            psp_Query.ajaxurl,
                {
                    action: 'psp_login',
                    user: jQuery('#sq_user').val(),
                    password: jQuery('#sq_password').val(),
                    psp_nonce: psp_Query.nonce
                }
        ).done(function (response) {
            if (typeof response.error !== 'undefined')
                if (response.error === 'invalid_token') {

                    jQuery.post(
                        psp_Query.ajaxurl,
                            {
                                action: 'psp_reset',
                                psp_nonce: psp_Query.nonce
                            }
                    ).done(function (response) {
                        if (typeof response.reset !== 'undefined')
                            if (response.reset === 'success')
                                location.reload();
                    }, 'json');
                }
            jQuery('#sq_login').removeAttr("disabled");
            jQuery('#sq_login').val('Login');
            jQuery('#sq_login').removeClass('sq_minloading');
            if (typeof response.token !== 'undefined') {
                __token = response.token;
                sq_reload(response);
            } else
            if (typeof response.error !== 'undefined') {
                jQuery('#psp_blocklogin').find('.sq_error').html(response.error);
                jQuery('#psp_blocklogin').find('.sq_error').show();
            }

        }).fail(function (response) {
            if (response.status === 200 && response.responseText.indexOf('{') > 0) {
                response.responseText = response.responseText.substr(response.responseText.indexOf('{'), response.responseText.lastIndexOf('}'));
                try {
                    response = jQuery.parseJSON(response.responseText);
                    jQuery('#sq_login').removeAttr("disabled");
                    jQuery('#sq_login').val('Login');
                    jQuery('#sq_login').removeClass('sq_minloading');

                    if (typeof response.token !== 'undefined') {
                        __token = response.token;
                        sq_reload(response);
                    } else
                    if (typeof response.error !== 'undefined') {
                        jQuery('#psp_blocklogin').find('.sq_error').html(response.error);
                        jQuery('#psp_blocklogin').find('.sq_error').show();
                    }
                } catch (e) {
                }

            } else {
                jQuery('#sq_login').removeAttr("disabled");
                jQuery('#sq_login').val('Login');
                jQuery('#sq_login').removeClass('sq_minloading');
                jQuery('#psp_blocklogin').find('.sq_error').html(__error_login);
                jQuery('#psp_blocklogin').find('.sq_error').show();
            }
        }, 'json');
    });
}

function sq_autoLogin() {
    if (!checkEmail(jQuery('#sq_email').val())) {
        jQuery('#psp_blocklogin').find('.sq_error').html(__invalid_email);
        jQuery('#psp_blocklogin').find('.sq_error').show();
        jQuery('#sq_register_email').show();
        jQuery('#sq_register').html(__try_again);
        return false;
    }

    jQuery('#sq_register').html(__connecting);
    jQuery('#sq_register_wait').addClass('sq_minloading');
    jQuery('#psp_blocklogin').find('.sq_message').hide();


    jQuery.post(
        psp_Query.ajaxurl,
            {
                action: 'psp_register',
                email: jQuery('#sq_email').val(),
                psp_nonce: psp_Query.nonce
            }
    ).done(function (response) {

        jQuery('#sq_register_wait').removeClass('sq_minloading');
        if (typeof response.token !== 'undefined') {
            __token = response.token;
            if (typeof response.success !== 'undefined') {
                jQuery('#sq_login_success').html(response.success);
            }
            //window.sq_main.load();
            sq_reload(response);
        } else {
            if (typeof response.info !== 'undefined') {
                jQuery('#sq_autologin').hide();
                jQuery('#psp_blocklogin').find('ul').show();

                jQuery('#psp_blocklogin').find('.sq_message').html(response.info).show();
                jQuery('#sq_user').val(jQuery('#sq_email').val());
                jQuery('#sq_password').focus();
            } else {
                if (typeof response.error !== 'undefined') {
                    jQuery('#psp_blocklogin').find('.sq_error').html(response.error);
                    jQuery('#psp_blocklogin').find('.sq_error').show();
                    jQuery('#sq_register_email').show();
                    jQuery('#sq_register').html(__try_again);
                }
            }

        }

    }).fail(function (response) {
        if (response.status === 200 && response.responseText.indexOf('{') > 0) {
            response.responseText = response.responseText.substr(response.responseText.indexOf('{'), response.responseText.lastIndexOf('}'));
            try {
                response = jQuery.parseJSON(response.responseText);
                if (typeof response.info !== 'undefined') {
                    jQuery('#sq_autologin').hide();
                    jQuery('#psp_blocklogin').find('ul').show();

                    jQuery('#psp_blocklogin').find('.sq_message').html(response.info).show();
                    jQuery('#sq_user').val(jQuery('#sq_email').val());
                    jQuery('#sq_password').focus();
                } else {
                    if (typeof response.error !== 'undefined') {
                        jQuery('#psp_blocklogin').find('.sq_error').html(response.error);
                        jQuery('#psp_blocklogin').find('.sq_error').show();
                        jQuery('#sq_register_email').show();
                        jQuery('#sq_register').html(__try_again);
                    }
                }
            } catch (e) {
            }

        } else {

            jQuery('#sq_register_wait').removeClass('sq_minloading');
            jQuery('#psp_blocklogin').find('.sq_error').html(__error_login);
            jQuery('#psp_blocklogin').find('.sq_error').show();
            jQuery('#sq_register_email').show();
            jQuery('#sq_register').html(__try_again);
        }
    }, 'json');
}

function sq_reload(response) {
    if (typeof response.success !== 'undefined') {
        jQuery('#sq_login_success').html(response.success);
    }
    if (jQuery('#content-html').length > 0) {
        jQuery('#psp_blocklogin').remove();
        location.reload();
    } else {
        if (jQuery('#psp_blocklogin').length === 0)
            jQuery('#sq_settings').prepend('<div id="psp_blocklogin">');
        jQuery('#psp_blocklogin').addClass('sq_login_done');
        jQuery('#psp_blocklogin').html(jQuery('#sq_login_success'));

        jQuery('#psp_blocklogin').append(jQuery('#sq_goto_dashboard'));
        jQuery('#sq_login_success').show();
        jQuery('#sq_goto_dashboard').show();
        jQuery('.sq_login_link').after(jQuery('#sq_goto_dashboard').clone());
        jQuery('.sq_login_link').remove();
    }
}

function checkEmail(email) {
    var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;

    if (email !== '')
        if (emailRegEx.test(email)) {
            return true;
        } else {
            return false;
        }

    return true;
}
