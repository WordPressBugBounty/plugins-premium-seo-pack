/**
 * Created by Andrew on 1/27/2017.
 */

jQuery(document).ready(function () {

    jQuery('#psp_settings').find('#psp_backup').on('click', function () {
        psp_backupSettings();
    });

    jQuery('#psp_settings').find('#psp_restore').on('click', function () {
        psp_restoreSettings();
    });

    jQuery('#psp_settings').find('#psp_post_types').on('change', function () {
        jQuery('#psp_settings').find('.show_hide').hide();
        jQuery('#psp_settings').find('.show_hide.psp' + jQuery(this).val()).show();
    });
    jQuery('#psp_settings').find('.show_hide.psphome').show();

    jQuery(document).on('after-autosave.update-post-slug', function (e, data) {
        if (jQuery('#post_ID').length > 0) {
            if (jQuery('#wp-admin-bar-psp_bar_menu').find("#psp_save").length > 0) {
                jQuery('#wp-admin-bar-psp_bar_menu').saveSEO();
            }
            psp_getFrontMenu(jQuery('#post_ID').val());
        }
    });

    if (jQuery('#post_ID').length > 0) {
        psp_getFrontMenu(jQuery('#post_ID').val());
    }

    if (pspTabParam()) {
        var tab = pspTabParam();
        jQuery('a.nav-tab#' + tab + '-tab').addClass('nav-tab-active');
        jQuery('#' + tab).addClass('active');
    } else {
        jQuery('a.nav-tab#tab1-tab').addClass('nav-tab-active');
        jQuery('#tab1').addClass('active');
    }

    //JsonLD switch types
    jQuery('.sq_jsonld_type').on('change', function () {
        jQuery('.sq_jsonld_types').hide();
        jQuery('.sq_jsonld_' + jQuery('#psp_settings').find('select[name=psp_jsonld_type] option:selected').val()).show();

    });

    //Upload image from library
    jQuery('#sq_json_imageselect').on('click', function (event) {
        var frame;

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if (frame) {
            frame.open();
            return;
        }

        // Create a new media frame
        frame = wp.media({
            title: 'Select or Upload Media Of Your Chosen Persuasion',
            button: {
                text: 'Use this media'
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });


        // When an image is selected in the media frame...
        frame.on('select', function () {

            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON();

            // Send the attachment URL to our custom image input field.
            jQuery('#psp_settings').find('#sq_jsonld_logo').val(attachment.url);

        });

        // Finally, open the modal on click
        frame.open();
    });

    jQuery('#psp_settings').find('a.nav-tab').click(function (event) {
        //get displaying tab content jQuery selector
        var active_tab_selector = jQuery('a.nav-tab.nav-tab-active').attr('href').replace('##', '#');

        //find actived navigation and remove 'active' css
        var actived_nav = jQuery('a.nav-tab.nav-tab-active');
        actived_nav.removeClass('nav-tab-active');

        //add 'active' css into clicked navigation
        jQuery(this).addClass('nav-tab-active');

        //hide displaying tab content
        jQuery(active_tab_selector).removeClass('active');

        //show target tab content
        var target_tab_selector = jQuery(this).attr('href').replace('##', '#');
        jQuery(target_tab_selector).addClass('active');
    });
});

function psp_getFrontMenu(post_id) {
    if (post_id) {
        jQuery.post(
            psp_Query.ajaxurl,
            {
                action: 'psp_getfrontmenu',
                post_id: post_id,
                psp_nonce: psp_Query.nonce
            }
        ).done(function (response) {
            if (typeof response.html !== 'undefined') {
                if(jQuery('#psp_div').length == 0) {
                    jQuery('#wp-admin-bar-psp_bar_menu').find('#psp_settings_body').replaceWith(response.html);
                    jQuery('#wp-admin-bar-psp_bar_menu').psp_TopMenu();
                }else {
                    //if embeded in post edit
                    jQuery('#psp_div').find('#psp_settings_body').replaceWith(response.html);
                    jQuery('#psp_div').psp_TopMenu();
                }
            }
        }, 'json');
    }
}

function psp_backupSettings() {
    jQuery.post(
        psp_Query.ajaxurl,
        {
            action: 'psp_backup',
            psp_nonce: psp_Query.nonce
        }
    ).done(function (response) {
        pspShowSaved(2000);
    }, 'json');
}

function psp_restoreSettings() {
    jQuery.post(
        psp_Query.ajaxurl,
        {
            action: 'psp_restore',
            psp_nonce: psp_Query.nonce
        }
    ).done(function (response) {
        if (typeof response.saved !== 'undefined') {
            pspShowSaved(2000);
        } else {
            alert('No backup found!');
        }
    }, 'json');
}

function pspShowSaved(time) {
    jQuery('.psp_settings_form').prepend('<div class="sq_savenotice sq_absolute" ><span class="sq_success">Saved!</span></div>');
    if (typeof sq_help_reload == 'function') {
        sq_help_reload();
    }
    if (typeof time !== 'undefined') {
        setTimeout(function () {
            jQuery('.sq_savenotice').hide();
        }, time);
    }
}

function pspTabParam() {
    if (location.href.indexOf("##") !== -1 && window.location.href.split('##')[1] !== '') {
        return window.location.href.split('##')[1];
    }

    return false;
}
