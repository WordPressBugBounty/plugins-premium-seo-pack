/**
 * Created by Andrew on 1/17/2017.
 */
//Blogs
PSP_DEBUG = false;
(function ($) {
    $.fn.psp_TopMenu = function (options) {
        var $this = this;

        /**
         * Set the fields in vars
         */
        var settings = $.extend({
            'psp_settings_form': $this.find('.psp_settings_form'),
            'saveButton': $this.find('#psp_save'),
            //'previewButton': $this.find('#psp_preview'),
            'last_tab': null,
            //'previewing': null,
            //'is_preview': false,
            'closeButton': $this.find('#psp_close'),
            'psp_url': $this.find('#psp_url'),
            'psp_doseo': $this.find('input[name=psp_doseo]'),
            'psp_toggle': $this.find('.psp-toggle'),
            /* ==== meta inputs ==== */
            'psp_title': $this.find('#psp_title'),
            'psp_description': $this.find('#psp_description'),
            'psp_keywords': $this.find('#psp_keywords'),
            'psp_noindex': $this.find('input[name=psp_noindex]'),
            'psp_nofollow': $this.find('input[name=psp_nofollow]'),
            'psp_canonical': $this.find('#psp_canonical'),
            /* ==== og inputs ==== */
            'psp_og_media': $this.find('#psp_og_media'),
            'psp_og_media_preview': $this.find('#psp_og_media_preview'),
            'psp_og_title': $this.find('#psp_og_title'),
            'psp_og_description': $this.find('#psp_og_description'),
            'psp_og_author': $this.find('#psp_og_author'),
            'psp_og_type': $this.find('#psp_og_type'),
            'psp_og_pixel': $this.find("#psp_og_pixel_id"),
            /* ==== tw inputs ==== */
            'psp_tw_media': $this.find('#psp_tw_media'),
            'psp_tw_media_preview': $this.find('#psp_tw_media_preview'),
            'psp_tw_title': $this.find('#psp_tw_title'),
            'psp_tw_description': $this.find('#psp_tw_description'),
            /* ==== google preview ==== */
            'gg_image': $this.find('#psp_snippet_img'),
            'gg_title': $this.find('#out_title'),
            'gg_url': $this.find('#out_url'),
            'gg_description': $this.find('#out_snippet'),
            /* ==== OG preview ==== */
            'og_preview_image': $this.find('#psp_og_snippet .og_image'),
            'og_preview_title': $this.find('#psp_og_snippet .og_title'),
            'og_preview_description': $this.find('#psp_og_snippet .og_description'),
            'og_preview_url': $this.find('#psp_og_snippet .og_url'),
            /* ==== TW preview ==== */
            'tw_preview_image': $this.find('#psp_tw_snippet .tcu-imageWrapper'),
            'tw_preview_title': $this.find('#psp_tw_snippet .SummaryCard-contentContainer > h2'),
            'tw_preview_description': $this.find('#psp_tw_snippet #psp_tw_description'),
            'tw_preview_url': $this.find('#psp_tw_snippet #psp_tw_url'),

            'validKeyword': false
        }, options);


        /**
         * Remove the Wordpress Events and Add the Psp events
         */
        $this.initNav = function () {
            if ($('#psp_div').length > 0){
                //remove the hover event from Wordpress
                $this.off("hover");
                //check the top menu from Wordpress
                $this.find('.ab-item').on("click", function () {
                    $('html,body').scrollTop( $('#psp_div').offset().top );
                });
                settings.closeButton.hide();

            }else {
                //remove the hover event from Wordpress
                $this.off("hover");
                //check the top menu from Wordpress
                $this.find('.ab-item').on("click", function () {
                    $this.addClass('open');
                });
                settings.closeButton.on("click", function () {
                    $this.removeClass('open');
                });

                $this.find('#psp_settings_body').show();
            }
        };


        //Initiate the Nav events
        $this.initNav();

        //Listen the DoSeo button
        $('#psp_doseo_on').on('click', function () {
            $('.psp_showhide').show();
        });
        $('#psp_doseo_off').on('click', function () {
            $('.psp_showhide').hide();
        });


        /*settings.previewButton.on('click', function () {
         if (!settings.is_preview) {
         $this.find('.psp_tabcontent').each(function () {
         if ($(this).is(":visible") && $(this).attr('id') !== 'psp_tab_preview')
         settings.last_tab = $(this);
         settings.is_preview = true;
         $(this).hide();
         });
         var tempid = settings.last_tab.attr('id');

         $this.find('#psp_snippet, #psp_og_snippet, #psp_tw_snippet').hide();

         if (tempid === 'psp_tab_meta') {
         settings.previewing = "#psp_snippet";
         }
         else if (tempid === 'psp_tab_facebook') {
         settings.previewing = "#psp_og_snippet";
         }
         else if (tempid === 'psp_tab_twitter') {
         settings.previewing = "#psp_tw_snippet";
         }
         settings.previewButton.val("Go Back");
         $this.find('#psp_tab_preview').show();
         $this.find(settings.previewing).show();
         }
         else if (settings.is_preview) {
         $this.find('#psp_tab_preview').hide();
         settings.last_tab.show();
         $this.find(settings.previewing).hide();
         settings.previewButton.val("PREVIEW");
         settings.is_preview = false;
         settings.previewing = null;
         }
         });
         */

        $this.createPreview = function () {
            /* Meta for preview */

            settings.gg_title.text(settings.psp_title.val());
            settings.gg_url.text(settings.psp_canonical.val());
            settings.gg_description.text(settings.psp_description.val());

            settings.og_preview_title.text(settings.psp_og_title.val());
            settings.og_preview_description.text(settings.psp_og_description.val());
            if ($('meta[property="og:url"]').attr('content') !== undefined)
                settings.og_preview_url.text($('meta[property="og:url"]').attr('content').replace(/(^\w+:|^)\/\//, ''));

            var bgimg = "url(" + settings.psp_og_media.attr("src") + ")";
            //PSP_DEBUG && console.log(bgimg);
            var $imgHeight = 0, $imgWidth = 0;

            settings.og_preview_image.css("background-image", bgimg);

            settings.og_preview_image
                .attr("src", settings.psp_og_media.attr("src"))
                .load(function () {
                    $imgWidth = this.width;
                    $imgHeight = this.height;
                    this.src = "";

                    if ($imgWidth > $imgHeight * 2) {
                        //PSP_DEBUG && console.log("W > H*2");
                        settings.og_preview_image.css("background-size", "auto 255px");
                    }
                    else if ($imgWidth > $imgHeight) {
                        //PSP_DEBUG && console.log("W > H");
                        settings.og_preview_image.css("background-size", "526px auto");
                    }
                    else if ($imgWidth * 2 < $imgHeight) {
                        //PSP_DEBUG && console.log("W*2 > H");
                        settings.og_preview_image.css("background-size", "526px auto");
                    }
                    else {
                        //PSP_DEBUG && console.log("W=H");
                        settings.og_preview_image.css("background-size", "526px auto");
                    }
                });

            settings.tw_preview_title.text(settings.psp_tw_title.val());
            settings.tw_preview_description.text(settings.psp_tw_description.val());
            if ($('meta[name="twitter:url"]').attr('content') !== undefined)
                settings.tw_preview_url.text($('meta[name="twitter:url"]').attr('content').replace(/(^\w+:|^)\/\//, ''));

            bgimg = "url(" + settings.psp_tw_media.attr("src") + ")";
            //PSP_DEBUG && console.log(bgimg);
            settings.tw_preview_image.css("background-image", bgimg);


        };

        $this.tabsListen = function () {
            /* =========== Tabs ============= */
            $this.find('#psp_tabs').find('li a').on('click', function (event) {
                event.preventDefault();

                $a = $(this);
                $this.find('#psp_tabs').find('li a').each(function () {
                    $(this).removeClass('active');
                });
                $this.find('.psp_tabcontent').each(function () {
                    $(this).hide();
                });

                //settings.is_preview = false;
                //settings.previewButton.val("PREVIEW");
                $this.find('#psp_tab_' + $a.text().toString().toLowerCase()).show();
                $a.addClass('active');
            });
        };

        /**
         * Save the SEO into database
         * Send Sanitize and ajax to PSP_Settings
         */
        $this.saveSEO = function () {
            $this.preventLeave(false);
            settings.saveButton.addClass('psp_minloading');

            var $psp_hash = $this.find('#psp_hash');
            if ($psp_hash.val() !== '') {

                $.post(psp_Query.ajaxurl,
                    {
                        "action": "psp_savesettings_adminbar",
                        "psp_title": settings.psp_title.length > 0 ? $this.escapeHtml(settings.psp_title.val()) : -1,
                        "psp_description": settings.psp_description.length > 0 ? $this.escapeHtml(settings.psp_description.val()) : -1,
                        "psp_keywords": settings.psp_keywords.length > 0 ? $this.escapeHtml(settings.psp_keywords.val()) : -1,
                        "psp_canonical": settings.psp_canonical.length > 0 ? $this.escapeHtml(settings.psp_canonical.val()) : -1,
                        //
                        "psp_noindex": settings.psp_noindex.length > 0 ? parseInt($this.find('input[name=psp_noindex]:checked').val()) : -1,
                        "psp_nofollow": settings.psp_nofollow.length > 0 ? parseInt($this.find('input[name=psp_nofollow]:checked').val()) : -1,
                        //
                        "psp_tw_title": settings.psp_tw_title.length > 0 ? $this.escapeHtml(settings.psp_tw_title.val()) : -1,
                        "psp_tw_description": settings.psp_tw_description.length > 0 ? $this.escapeHtml(settings.psp_tw_description.val()) : -1,
                        "psp_tw_media": settings.psp_tw_media.length > 0 ? settings.psp_tw_media.val() : -1,
                        //
                        "psp_og_title": settings.psp_og_title.length > 0 ? $this.escapeHtml(settings.psp_og_title.val()) : -1,
                        "psp_og_description": settings.psp_og_description.length > 0 ? $this.escapeHtml(settings.psp_og_description.val()) : -1,
                        "psp_og_type": settings.psp_og_type.length > 0 ? settings.psp_og_type.find('option:selected').val() : 'website',
                        "psp_og_author": settings.psp_og_author.length > 0 ? $this.escapeHtml(settings.psp_og_author.val()) : -1,

                        "psp_og_media": settings.psp_og_media.length > 0 ? settings.psp_og_media.val() : -1,
                        //
                        // "psp_page_tw_media": _psp_page_tw_media,
                        "psp_url": settings.psp_url.length > 0 ? $this.escapeHtml(settings.psp_url.val()) : -1,
                        "psp_hash": $psp_hash.val(),
                        "psp_doseo": parseInt($this.find('input[name=psp_doseo]:checked').val()),
                        "psp_nonce": psp_Query.nonce
                    }, function () {
                    }
                ).done(function (response) {
                    settings.saveButton.removeClass('psp_minloading');
                    if (typeof response.saved !== 'undefined') {

                        if (typeof response.html !== 'undefined') {
                            var $ctab = $this.find('#psp_tabs').find('li a.active').data('tab');
                            $this.find('#psp_settings_body').replaceWith(response.html);
                            $this.psp_TopMenu();
                            $this.find('#psp_tabs').find('li a.' + $ctab).trigger('click');
                        }

                        $this.showSaved(2000);
                        PSP_DEBUG && console.log("done and success");
                    }
                    else {
                        $this.showError(2000);
                    }
                }).error(function () {
                    $this.showError(2000);
                });
            }
        };

        $this.showSaved = function (time) {
            jQuery('.psp_settings_form').prepend('<div class="sq_savenotice sq_absolute" ><span class="sq_success">Saved! Reload to see the changes.</span></div>');
            if (typeof time !== 'undefined') {
                setTimeout(function () {
                    jQuery('.sq_savenotice').hide();
                }, time);
            }
        };
        $this.showError = function (time) {
            jQuery('.psp_settings_form').prepend('<div class="sq_savenotice sq_absolute" ><span class="sq_warning">ERROR! Could not save the data. Please refres</span></div>');
            if (typeof time !== 'undefined') {
                setTimeout(function () {
                    jQuery('.sq_savenotice').hide();
                }, time);
            }
        };

        /**
         * Populates all titles and descriptions
         */
        $this.populateInputs = function () {
            // $apple_icons = $('link[rel="apple-touch-icon"]');
            //
            // $apple_icons.each(function () {
            //     if ($(this).attr('sizes') == "76x76")
            //         settings.gg_image.attr('src', $(this).attr('href'));
            // });

            /* Meta Inputs */
            if ($this.find('.psp-title-value').length > 0) {
                var $title = $(document).find("head title").text();
                $this.find('.psp-title-value').text($title);
                $this.find('.psp-title-value').attr('title', $title);
                $this.find('psp-value.psp-title-value').checkMax();
            }
            if ($this.find('.psp-description-value').length > 0) {
                var $description = $('meta[name="description"]').attr('content');
                PSP_DEBUG && console.log($description);
                $this.find('.psp-description-value').text($description);
                $this.find('.psp-description-value').attr('title', $description);
                $this.find('psp-value.psp-description-value').checkMax();
            }
            if (settings.psp_keywords && $('meta[name="keywords"]').length > 0) {
                settings.psp_keywords.val($('meta[name="keywords"]').attr('content'));
            }

            if ($this.find('.psp-canonical-value').length > 0) {
                var $canonical = $('link[rel="canonical"]').attr('href');
                $this.find('.psp-canonical-value').text($canonical);
                $this.find('.psp-canonical-value').attr('title', $canonical);
            }


            if (settings.psp_noindex.length > 0) {
                var $robots = $('meta[name="robots"]').attr('content');
                if (typeof $robots !== 'undefined' && $robots.indexOf('noindex') !== -1) {
                    settings.psp_noindex.toggleSwitch(false);
                }
                if (typeof $robots !== 'undefined' && $robots.indexOf('nofollow') !== -1) {
                    settings.psp_nofollow.toggleSwitch(false);
                }
            }
            if (settings.psp_og_media_preview && settings.psp_og_media.val() != '') {
                settings.psp_og_media_preview.attr('src', settings.psp_og_media.val());
            }
            if (settings.psp_tw_media_preview && settings.psp_tw_media.val() != '') {
                settings.psp_tw_media_preview.attr('src', settings.psp_tw_media.val());
            }

            $this.keywordsListen();
        };

        /**
         * Listen the Image Media from Wordpress
         */
        $this.mediaListen = function () {
            $('#psp_get_og_media, #psp_get_tw_media').click(function (e) {

                e.preventDefault();

                var btn_id = this.id;

                var image_frame;
                if (image_frame) {
                    image_frame.open();
                }
                // Define image_frame as wp.media object
                image_frame = wp.media({
                    title: 'Select Media',
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                image_frame.on('close', function () {
                    // On close, get selections and save to the hidden input
                    // plus other AJAX stuff to refresh the image preview
                    var selection = image_frame.state().get('selection');
                    var gallery_ids = null;
                    var my_index = 0;
                    selection.each(function (attachment) {
                        gallery_ids = attachment['attributes']['url'];
                        my_index++;
                    });
                    if (btn_id === 'psp_get_tw_media' && gallery_ids !== null) {
                        settings.psp_tw_media.val(gallery_ids);
                        settings.psp_tw_media_preview.attr('src', gallery_ids);
                    }
                    else if (btn_id === 'psp_get_og_media' && gallery_ids !== null) {
                        settings.psp_og_media.val(gallery_ids);
                        settings.psp_og_media_preview.attr('src', gallery_ids);
                    }
                    //$this.createPreview();
                    //settings.circle.getColorForPercentage();
                    //$this.saveSEO();
                });
                image_frame.on('open', function () {
                    // On open, get the id from the hidden input
                    // and select the appropiate images in the media manager
                    var selection = image_frame.state().get('selection');
                });

                image_frame.open();
            });
        };

        //Init
        $this.dropDownListen = function () {
            var actionDivSelected, actionDiv, dropdown, input, next;
            settings.psp_toggle.on('focus', function () {
                $(this).trigger('click');
            });
            settings.psp_toggle.on('click', function () {
                input = $(this);
                dropdown = input.parent('.input-group').find(".psp-actions");
                if (dropdown.data('position') == 'top') {
                    dropdown.css('top', '-82px');
                    dropdown.css('height', '80px');
                } else if (dropdown.data('position') == 'small') {
                    dropdown.css('top', '35px');
                    dropdown.css('height', '36px');
                }
                actionDiv = dropdown.find(".psp-action");
                dropdown.show();

                actionDiv.on('click keyup', function (e) {
                    if (typeof actionDivSelected !== 'undefined' && e.which !== 1) {
                        var actionValue = actionDivSelected.find('.psp-value');
                    } else {
                        var actionValue = $(this).find('.psp-value');
                    }

                    if (typeof actionValue !== "undefined" && actionValue !== "") {
                        if (e.which === 13 || e.which === 1) {
                            if ($(this).hasClass("focused")) {
                                $(this).removeClass("focused");
                            }
                        }

                        //Set the Value
                        input.val(actionValue.html());
                        input.checkMax();
                    }

                });

                input.outside("click", function () {
                    $(this).parent('.input-group').find(".psp-actions").hide();
                });
            });

            settings.psp_toggle.on('click keyup', function (e) {
                PSP_DEBUG && console.log("Actions is visible. Start navigation: " + e.which);

                //If enter press, trigger click for this filter
                if (e.which === 13) {
                    PSP_DEBUG && console.log("enter pressed")
                    dropdown.find(".psp-action.focused").trigger('click');
                    return false;
                }

                if (e.which === 27) {
                    $this.find(".psp-actions").hide();
                }

                //Listen for arrows.
                if (e.which === 40) {
                    if (actionDivSelected) {
                        actionDivSelected.removeClass('focused');
                        next = actionDivSelected.nextAll().first();
                        if (next.length > 0) {
                            actionDivSelected = next.addClass('focused');
                        } else {
                            actionDivSelected = actionDiv.eq(0).addClass('focused');
                        }
                    } else {
                        actionDivSelected = actionDiv.eq(0).addClass('focused');
                    }
                    actionDiv.trigger('keyup');
                } else if (e.which === 38) {
                    if (actionDivSelected) {
                        actionDivSelected.removeClass('focused');
                        next = actionDivSelected.prevAll().first();
                        if (next.length > 0) {
                            actionDivSelected = next.addClass('focused');
                        } else {
                            actionDivSelected = actionDiv.last().addClass('focused');
                        }
                    } else {
                        actionDivSelected = actionDiv.last().addClass('focused');
                    }
                    actionDiv.trigger('keyup');
                } else if (e.which > 1) {
                    $this.find(".psp-actions").hide();
                }

            });

        };

        $this.keywordsListen = function () {
            settings.psp_keywords.tagsinput('items');
        };

        $this.escapeHtml = function (text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };

            return text.toString().replace(/[&<>"']/g, function (m) {
                return map[m];
            });
        };

        $this.preventLeave = function (on) {
            if (on) {
                $(window).on('beforeunload', function () {
                    return 'SEO Pack has unsaved changes. Are you sure you want to leave?';
                });
            } else {
                $(window).off('beforeunload');
            }

        };

        // Uploading files
        $this.mediaListen();
        $this.tabsListen();
        $this.populateInputs();
        $this.dropDownListen();

        $this.find('input[type="text"], textarea').on('change input paste', function () {
            $this.preventLeave(true);
            $(this).checkMax();
        });

        settings.psp_settings_form.submit(function (event) {

            event.preventDefault();

            $this.preventLeave(false);
            $this.saveSEO();
            return false;
        });


        return $this;
    };

    $.fn.checkMax = function () {
        var $element = $(this);

        if (!$element.length > 0)
            return;

        var val = $element.val();
        var valLength = val.length;
        var maxCount = parseInt($element.parents('.row:last').find('.psp_length').data('maxlength'));

        $element.parents('.row:last').find('.psp_length').text(valLength);

        if (valLength === 0 || valLength > maxCount) {
            $element.css('border', 'solid 1px red');
        } else {
            $element.css('border', 'solid 1px white');
        }
    };

    $.fn.outside = function (ename, cb) {
        return this.each(function () {
            var $this = $(this),
                self = this;

            $(document).on(ename, function psptempo(e) {
                if (e.target !== self && !$.contains(self, e.target)) {
                    cb.apply(self, [e]);
                    $(document).off(ename, psptempo);
                }
            });

            $this.on('keydown blur', function psptabpress(e) {
                if (e.which === 9) {
                    cb.apply(self, [e]);
                    $this.off('keydown', psptabpress);
                }
            });
        });
    };

    $.fn.toggleSwitch = function (checked) {
        var element = $(this);

        if (( element.prop('checked') && checked == false ) || ( !element.prop('checked') && checked == true )) {
            element.trigger('click');
        }
    };


})(jQuery);


jQuery(document).ready(function () {
    //li id from topbar
    jQuery('#wp-admin-bar-psp_bar_menu').psp_TopMenu();
});
