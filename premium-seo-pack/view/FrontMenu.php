<?php
/**
 * View File
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );
if ( ! isset( $view ) ) {
	exit();
}

if ( isset( $view->post->hash ) && $view->post->hash <> '' ) {
	if ( ! is_admin() && ! is_network_admin() && PSP_Classes_Tools::getOption( 'psp_import_opt' ) == 1 ) {
		$import = $view->post->importSEO();
	}
	$patterns = PSP_Classes_Tools::getOption( 'patterns' );
	?>
    <div id="psp_settings_body" style="display: none">
        <div class="container">
            <button id="psp_close">x</button>
            <form class="psp_settings_form" method="post">
                <!-- ================= Tabs ==================== -->
                <div class="row b-b">
                    <ul id="psp_tabs" class="psp_tab">
                        <li class="one-forth column">
                            <a href="#" class="psp_tablinks tab1 active"
                               data-tab="tab1"><?php echo esc_html__( 'META', _PSP_PLUGIN_NAME_ ) ?></a>
                        </li>
						<?php if ( PSP_Classes_Tools::getOption( 'psp_og_opt' ) ) { ?>
                            <li class="one-forth column">
                                <a href="#" class="psp_tablinks tab2"
                                   data-tab="tab2"><?php echo esc_html__( 'FACEBOOK', _PSP_PLUGIN_NAME_ ) ?></a>
                            </li>
						<?php } ?>
						<?php if ( PSP_Classes_Tools::getOption( 'psp_tw_opt' ) ) { ?>
                            <li class="one-forth column">
                                <a href="#" class="psp_tablinks tab3"
                                   data-tab="tab3"><?php echo esc_html__( 'TWITTER', _PSP_PLUGIN_NAME_ ) ?></a>
                            </li>
						<?php } ?>
                        <li class="one-forth column">
                            <a href="#" class="psp_tablinks tab4"
                               data-tab="tab4"><?php echo esc_html__( 'ADVANCED', _PSP_PLUGIN_NAME_ ) ?></a>
                        </li>
                    </ul>
                </div>

                <!-- =================== Optimize ==================== -->
                <div class="row b-b m-b-md">
                    <div class="six columns">
                        <div id="psp_message"><?php echo esc_html__( 'Optimize this page for SEO', _PSP_PLUGIN_NAME_ ) ?>
                            :
                        </div>
                    </div>
                    <div class="three columns right ">
                        <input type="submit" id="psp_save" value="<?php echo esc_attr__( 'Save', _PSP_PLUGIN_NAME_ ) ?>"/>
                    </div>
                    <div class="three columns left">
                        <div class="row">
                            <div class="psp_option_content" style="margin-top: 15px !important;">
                                <div class="psp_switch-field psp_option_content">
                                    <input id="psp_doseo_on" type="radio" name="psp_doseo" value="1" <?php echo ( $view->post->psp_adm->doseo == 1 ) ? 'checked="checked"' : ''; ?> >
                                    <label for="psp_doseo_on" class="psp_switch-label-on"><?php echo esc_html__( 'Yes', _PSP_PLUGIN_NAME_ ) ?></label>
                                    <input id="psp_doseo_off" type="radio" name="psp_doseo" value="0" <?php echo ( $view->post->psp_adm->doseo == 0 ) ? 'checked="checked"' : ''; ?> >
                                    <label for="psp_doseo_off" class="psp_switch-label-off"><?php echo esc_html__( 'No', _PSP_PLUGIN_NAME_ ) ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="two right columns" style="display: none; height: 60px !important;">
                        <div id="psp_circle"></div>
                    </div>
                    <div class="four right columns" style="display: none">
                        <div id="psp_seo_status">
							<?php echo esc_html__( 'Your SEO for this page is awesome.', _PSP_PLUGIN_NAME_ ) ?>
                        </div>
                    </div>
                </div>

                <div id="psp_settings_body_content">
                    <div id="psp_tab_meta" class="psp_tabcontent" style="display: block;">
                        <div class="psp_showhide"
                             style="<?php echo ( $view->post->psp_adm->doseo == 0 ) ? 'display: none' : ''; ?>">
							<?php if ( PSP_Classes_Tools::getOption( 'psp_title_opt' ) ) { ?>
                                <div class="row">
                                    <div class="three columns psp_text"><?php echo esc_html__( 'SEO Title', _PSP_PLUGIN_NAME_ ) ?>
                                        :
                                    </div>
                                    <div class="nine columns">
                                        <div class="input-group">
                                            <input type="text" id="psp_title" name="psp_title"  class="form-control input-lg psp-toggle"  value="<?php echo esc_attr( $view->post->psp_adm->title ) ?>" placeholder="<?php echo esc_attr__( 'Pattern: ', _PSP_PLUGIN_NAME_ ) . esc_attr($view->post->psp_adm->patterns->title) ?>"/>

                                            <div class="psp-actions">
												<?php if ( ! is_admin() && ! is_network_admin() ) { ?>
                                                    <div class="psp-action">
                                                        <span style="display: none" class="psp-value psp-title-value"></span>
                                                        <span class="psp-action-title"><?php echo esc_html__( 'Current Title', _PSP_PLUGIN_NAME_ ) ?>: <span class="psp-title-value"></span></span>
                                                    </div>
												<?php } ?>
												<?php if ( isset( $view->post->post_title ) && $view->post->post_title <> '' ) { ?>
                                                    <div class="psp-action">
                                                        <span style="display: none" class="psp-value"><?php echo esc_html( $view->post->post_title ) ?></span>
                                                        <span class="psp-action-title" title="<?php echo esc_attr( $view->post->post_title ) ?>"><?php echo esc_html__( 'Default Title', _PSP_PLUGIN_NAME_ ) ?>: <span><?php echo esc_html( $view->post->post_title ) ?></span></span>
                                                    </div>
												<?php } ?>
												<?php
												if ( ! empty( $import ) ) {
													foreach ( $import as $name => $meta ) {
														if ( isset( $meta->title ) && $meta->title <> '' ) {
															?>
                                                            <div class="psp-action">
                                                                <span style="display: none" class="psp-value"><?php echo esc_html( $meta->title ) ?></span>
                                                                <span class="psp-action-title" title="<?php echo esc_attr( $meta->title ) ?>"><?php echo esc_html( $name ) . " " . esc_html__( 'Title', _PSP_PLUGIN_NAME_ ) ?>: <span><?php echo esc_html( $meta->title ) ?></span></span>
                                                            </div>
															<?php
														}
													}
												}
												?>
												<?php if ( $view->post->psp_adm->patterns->title <> '' ) { ?>
                                                    <div class="psp-action">
                                                        <span style="display: none" class="psp-value"><?php echo esc_html( $view->post->psp_adm->patterns->title ) ?></span>
                                                        <span class="psp-action-title" title="<?php echo esc_attr( $view->post->psp_adm->patterns->title ) ?>"><?php echo esc_html__( 'Pattern', _PSP_PLUGIN_NAME_ ) ?>: <span><?php echo esc_html( $view->post->psp_adm->patterns->title ) ?></span></span>
                                                    </div>
												<?php } ?>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="two right columns small">
                                                <span id="psp_title_length" class="psp_length" data-maxlength="<?php echo esc_attr( $view->post->psp_adm->title_maxlength ) ?>">0</span>/<span id="psp_title_maxlength" class="psp_maxlength"><?php echo esc_html( $view->post->psp_adm->title_maxlength ) ?></span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
							<?php }
							if ( PSP_Classes_Tools::getOption( 'psp_description_opt' ) ) { ?>
                                <div class="row">
                                    <div class="three columns psp_text"><?php echo esc_html__( 'META Description', _PSP_PLUGIN_NAME_ ) ?>
                                        :
                                    </div>
                                    <div class="nine columns">
                                        <div class="input-group">
                                            <textarea style="color: black;" class="form-control psp-toggle" name="psp_description" id="psp_description" placeholder="<?php echo esc_attr__( 'Pattern: ', _PSP_PLUGIN_NAME_ ) . esc_attr( $view->post->psp_adm->patterns->description ) ?>"><?php echo esc_html( $view->post->psp_adm->description ) ?></textarea>
                                            <div class="psp-actions">
												<?php if ( ! is_admin() && ! is_network_admin() ) { ?>
                                                    <div class="psp-action">
                                                        <span style="display: none" class="psp-value psp-description-value"></span>
                                                        <span class="psp-action-title"><?php echo esc_html__( 'Current Description', _PSP_PLUGIN_NAME_ ) ?>: <span class="psp-description-value"></span></span>
                                                    </div>
												<?php } ?>
												<?php if ( isset( $view->post->post_excerpt ) && $view->post->post_excerpt <> '' ) { ?>
                                                    <div class="psp-action">
                                                        <span style="display: none" class="psp-value"><?php echo esc_html($view->post->post_excerpt) ?></span>
                                                        <span class="psp-action-title" title="<?php echo esc_attr($view->post->post_excerpt) ?>"><?php echo esc_html__( 'Default Description', _PSP_PLUGIN_NAME_ ) ?>: <span><?php echo esc_html($view->post->post_excerpt) ?></span></span>
                                                    </div>
												<?php } ?>
												<?php if ( ! empty( $import ) ) {
													foreach ( $import as $name => $meta ) {
														if ( isset( $meta->description ) && $meta->description <> '' ) {
															?>
                                                            <div class="psp-action">
                                                                <span style="display: none" class="psp-value"><?php echo esc_html($meta->description) ?></span>
                                                                <span class="psp-action-title" title="<?php echo esc_attr($meta->description) ?>"><?php echo esc_html($name) . " " . esc_html__( 'Description', _PSP_PLUGIN_NAME_ ) ?>: <span><?php echo esc_html($meta->description) ?></span></span>
                                                            </div>
															<?php
														}
													}
												} ?>
												<?php if ( $view->post->psp_adm->patterns->description <> '' ) { ?>
                                                    <div class="psp-action">
                                                        <span style="display: none" class="psp-value"><?php echo esc_html($view->post->psp_adm->patterns->description) ?></span>
                                                        <span class="psp-action-title" title="<?php echo esc_attr($view->post->psp_adm->patterns->description) ?>"><?php echo esc_html__( 'Pattern', _PSP_PLUGIN_NAME_ ) ?>: <span><?php echo esc_html($view->post->psp_adm->patterns->description) ?></span></span>
                                                    </div>
												<?php } ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="two right columns small">
                                                <span id="psp_description_length" class="psp_length" data-maxlength="<?php echo esc_attr($view->post->psp_adm->description_maxlength) ?>">0</span>/<span id="psp_description_maxlength" class="psp_maxlength"><?php echo esc_html($view->post->psp_adm->description_maxlength) ?></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
							<?php } ?>
							<?php if ( PSP_Classes_Tools::getOption( 'psp_keywords_opt' ) ) { ?>
                                <div class="row">
                                    <div class="three columns psp_text"><?php echo esc_html__( 'Add keywords', _PSP_PLUGIN_NAME_ ) ?>
                                        :
                                    </div>
                                    <div class="nine columns">
                                        <div id="psp_tags">
                                            <div class="psp_tag_input">
                                                <input type="text" name="psp_keywords" id="psp_keywords" class="form-control" value="<?php echo esc_attr($view->post->psp_adm->keywords) ?>" placeholder="<?php echo esc_attr__( '+ Add keyword', _PSP_PLUGIN_NAME_ ) ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							<?php } ?>
							<?php if ( PSP_Classes_Tools::getOption( 'psp_canonical_opt' ) ) { ?>
                                <div class="row">
                                    <div class="three columns psp_text"><?php echo esc_html__( 'Canonical link', _PSP_PLUGIN_NAME_ ) ?>
                                        :
                                    </div>
                                    <div class="nine columns">
                                        <div class="input-group">
                                            <input type="text" id="psp_canonical" name="psp_canonical" class="form-control input-lg psp-toggle" value="<?php echo esc_url($view->post->psp_adm->canonical) ?>" placeholder="<?php echo esc_attr__( 'Found: ', _PSP_PLUGIN_NAME_ ) . esc_url($view->post->url) ?>"/>

                                            <div class="psp-actions" data-position="top">
												<?php if ( ! is_admin() && ! is_network_admin() ) { ?>
                                                    <div class="psp-action">
                                                        <span style="display: none" class="psp-value psp-canonical-value"></span>
                                                        <span class="psp-action-title"><?php echo esc_html__( 'Current', _PSP_PLUGIN_NAME_ ) ?>: <span class="psp-canonical-value"></span></span>
                                                    </div>
												<?php } ?>
												<?php if ( isset( $view->post->url ) && $view->post->url <> '' ) { ?>
                                                    <div class="psp-action">
                                                        <span style="display: none" class="psp-value"><?php echo esc_url($view->post->url) ?></span>
                                                        <span class="psp-action-title" title="<?php echo esc_url($view->post->url) ?>"><?php echo esc_html__( 'Default Link', _PSP_PLUGIN_NAME_ ) ?>: <span><?php echo esc_url($view->post->url) ?></span></span>
                                                    </div>
												<?php } ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                    <div id="psp_tab_facebook" class="psp_tabcontent" style="display: none;">
                        <div class="psp_showhide"
                             style="<?php echo ( $view->post->psp_adm->doseo == 0 ) ? 'display: none' : ''; ?>">

							<?php
							if ( PSP_Classes_Tools::getOption( 'psp_og_opt' ) ) { ?>
                                <div class="row">
                                    <div class="three columns psp_text"><?php echo esc_html__( 'Media Image', _PSP_PLUGIN_NAME_ ) ?>
                                        :
                                    </div>
                                    <div class="nine columns">
                                        <div class="row">
                                            <div class="nine columns">
                                                <button id="psp_get_og_media"><?php echo esc_html__( 'Upload', _PSP_PLUGIN_NAME_ ) ?></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <input type="hidden" name="psp_og_media" id="psp_og_media" value="<?php echo esc_attr($view->post->psp_adm->og_media) ?>"/>
                                            <img id="psp_og_media_preview" src="" alt=""/>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="three columns psp_text"><?php echo esc_html__( 'OG Title', _PSP_PLUGIN_NAME_ ) ?> :</div>
                                    <div class="nine columns">
                                        <input type="text" name="psp_og_title" id="psp_og_title" value="<?php echo esc_attr($view->post->psp_adm->og_title) ?>"/>
                                        <div class="row">
                                            <div class="two right columns small">
                                                <span id="psp_og_title_length" class="psp_length" data-maxlength="<?php echo esc_attr($view->post->psp_adm->og_title_maxlength) ?>">0</span>/<span id="psp_og_title_maxlength" class="psp_maxlength"><?php echo esc_html($view->post->psp_adm->og_title_maxlength) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="three columns psp_text"><?php echo esc_html__( 'OG Description', _PSP_PLUGIN_NAME_ ) ?>
                                        :
                                    </div>
                                    <div class="nine columns">
                                        <textarea style="color: black;" name="psp_og_description" id="psp_og_description"><?php echo esc_html($view->post->psp_adm->og_description) ?></textarea>
                                        <div class="row">
                                            <div class="two right columns small">
                                                <span id="psp_og_description_length" class="psp_length" data-maxlength="<?php echo esc_attr($view->post->psp_adm->og_description_maxlength) ?>">0</span>/<span id="psp_og_description_maxlength" class="psp_maxlength"><?php echo esc_html($view->post->psp_adm->og_description_maxlength) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="three columns psp_text"><?php echo esc_html__( 'Author Link', _PSP_PLUGIN_NAME_ ) ?>
                                        :
                                    </div>
                                    <div class="nine columns">
                                        <input type="text" name="psp_og_author" id="psp_og_author" value="<?php echo esc_attr($view->post->psp_adm->og_author) ?>"/>
                                        <div class="row">
                                            <div class="nine right columns small">
                                                <span><?php echo esc_html__( 'if there are more authors, separate their facebook links with commas', _PSP_PLUGIN_NAME_ ) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="three columns psp_text"><?php echo esc_html__( 'Page type', _PSP_PLUGIN_NAME_ ) ?>
                                        :
                                    </div>
                                    <div class="nine columns">
                                        <select id="psp_og_type" name="psp_og_type">
                                            <option <?php $view->showPostType( 'og:type', 'website' ); ?> value="website">
												<?php echo esc_html__( 'Website', _PSP_PLUGIN_NAME_ ) ?>
                                            </option>
                                            <option <?php $view->showPostType( 'og:type', 'profile' ); ?> value="profile">
												<?php echo esc_html__( 'Author', _PSP_PLUGIN_NAME_ ) ?>
                                            </option>
                                            <option <?php $view->showPostType( 'og:type', 'article' ); ?> value="article">
												<?php echo esc_html__( 'Article', _PSP_PLUGIN_NAME_ ) ?>
                                            </option>
                                            <option <?php $view->showPostType( 'og:type', 'book' ); ?> value="book">
												<?php echo esc_html__( 'Book', _PSP_PLUGIN_NAME_ ) ?>
                                            </option>
                                            <option <?php $view->showPostType( 'og:type', 'music' ); ?> value="music">
												<?php echo esc_html__( 'Music', _PSP_PLUGIN_NAME_ ) ?>
                                            </option>
                                            <option <?php $view->showPostType( 'og:type', 'product' ); ?> value="product">
												<?php echo esc_html__( 'Product', _PSP_PLUGIN_NAME_ ) ?>
                                            </option>
                                            <option <?php $view->showPostType( 'og:type', 'video' ); ?> value="video">
												<?php echo esc_html__( 'Video', _PSP_PLUGIN_NAME_ ) ?>
                                            </option>

                                        </select>
                                    </div>
                                </div>
							<?php } ?>

                        </div>
                    </div>
                    <div id="psp_tab_twitter" class="psp_tabcontent" style="display: none;">
                        <div class="psp_showhide"
                             style="<?php echo ( $view->post->psp_adm->doseo == 0 ) ? 'display: none' : ''; ?>">

							<?php if ( PSP_Classes_Tools::getOption( 'psp_tw_opt' ) ) { ?>
                                <div class="row">
                                    <div class="three columns psp_text"><?php echo esc_html__( 'Twitter Image', _PSP_PLUGIN_NAME_ ) ?></div>
                                    <div class="nine columns">
                                        <div class="row">
                                            <div class="nine columns">
                                                <button id="psp_get_tw_media"><?php echo esc_html__( 'Upload', _PSP_PLUGIN_NAME_ ) ?></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <input type="hidden" name="psp_tw_media" id="psp_tw_media" value="<?php echo esc_attr($view->post->psp_adm->tw_media) ?>"/>
                                            <img id="psp_tw_media_preview" src="" alt=""/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="three columns psp_text"><?php echo esc_html__( 'Twitter Card Title', _PSP_PLUGIN_NAME_ ) ?></div>
                                    <div class="nine columns">
                                        <input type="text" name="psp_tw_title" id="psp_tw_title" value="<?php echo esc_attr($view->post->psp_adm->tw_title) ?>"/>
                                        <div class="row">
                                            <div class="two right columns small">
                                                <span id="psp_tw_title_length" class="psp_length" data-maxlength="<?php echo esc_attr($view->post->psp_adm->tw_title_maxlength) ?>">0</span>/<span id="psp_tw_title_maxlength" class="psp_maxlength"><?php echo esc_html($view->post->psp_adm->tw_title_maxlength) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="three columns psp_text"><?php echo esc_html__( 'Twitter Card Description', _PSP_PLUGIN_NAME_ ) ?></div>
                                    <div class="nine columns">
                                        <textarea style="color: black;" name="psp_tw_description" id="psp_tw_description"><?php echo esc_html($view->post->psp_adm->tw_description) ?></textarea>
                                        <div class="row">
                                            <div class="two right columns small">
                                                <span id="psp_tw_description_length" class="psp_length" data-maxlength="<?php echo esc_attr($view->post->psp_adm->tw_description_maxlength) ?>">0</span>/<span id="psp_tw_description_maxlength" class="psp_maxlength"><?php echo esc_html($view->post->psp_adm->tw_description_maxlength) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                    <div id="psp_tab_advanced" class="psp_tabcontent" style="display: none;">
                        <div class="psp_showhide"
                             style="<?php echo ( $view->post->psp_adm->doseo == 0 ) ? 'display: none' : ''; ?>">
							<?php if ( PSP_Classes_Tools::getOption( 'psp_noindex_opt' ) ) { ?>
                                <div class="row">
                                    <div class="five columns psp_text"><?php echo esc_html__( 'Let Google Index This Page', _PSP_PLUGIN_NAME_ ) ?></div>
                                    <div class="seven columns">
                                        <div class="psp_option_content psp_option_content">
                                            <div class="psp_switch-field">
                                                <input id="psp_noindex_on" type="radio" name="psp_noindex"
                                                       value="0" <?php echo( $view->post->psp_adm->noindex == 0 ? 'checked="checked"' : '' ); ?> >
                                                <label for="psp_noindex_on"
                                                       class="psp_switch-label-on"><?php echo esc_html__( 'Yes', _PSP_PLUGIN_NAME_ ) ?></label>
                                                <input id="psp_noindex_off" type="radio" name="psp_noindex"
                                                       value="1" <?php echo( $view->post->psp_adm->noindex == 1 ? 'checked="checked"' : '' ); ?> >
                                                <label for="psp_noindex_off"
                                                       class="psp_switch-label-off"><?php echo esc_html__( 'No', _PSP_PLUGIN_NAME_ ) ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="five columns psp_text"><?php echo esc_html__( 'Pass Link Juice to This Page', _PSP_PLUGIN_NAME_ ) ?></div>
                                    <div class="seven columns">
                                        <div class="psp_option_content psp_option_content">
                                            <div class="psp_switch-field">
                                                <input id="psp_nofollow_on" type="radio" name="psp_nofollow"
                                                       value="0" <?php echo( $view->post->psp_adm->nofollow == 0 ? 'checked="checked"' : '' ); ?> >
                                                <label for="psp_nofollow_on"
                                                       class="psp_switch-label-on"><?php echo esc_html__( 'Yes', _PSP_PLUGIN_NAME_ ) ?></label>
                                                <input id="psp_nofollow_off" type="radio" name="psp_nofollow"
                                                       value="1" <?php echo( $view->post->psp_adm->nofollow == 1 ? 'checked="checked"' : '' ); ?> >
                                                <label for="psp_nofollow_off"
                                                       class="psp_switch-label-off"><?php echo esc_html__( 'No', _PSP_PLUGIN_NAME_ ) ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                    <div id="psp_tab_preview" class="psp_tabcontent" style="display: none;">
                        <div id="psp_snippet" style="display: none;">
                            <div id="psp_snippet_image_container">
                                <img id="psp_snippet_img" src="" alt=""/>
                            </div>
                            <div id="psp_snippet_container">
                                <div id="out_title"></div>
                                <div id="out_url"></div>
                                <div id="out_snippet"></div>
                            </div>
                        </div>
                        <div id="psp_og_snippet" style="display: none;">
                            <div class="unclickable">
                                <div class="_6ks">
                                    <img class="scaledImageFitWidth img og_image" alt="" src="">
                                </div>
                                <div class="_3ekx _29_4">
                                    <div class="og_title">
                                        <a href="javascript:return false;">Cool bird...</a>
                                    </div>
                                    <div class="og_description">Some small description...</div>
                                    <div class="og_url">www.domain.com</div>
                                </div>
                            </div>
                        </div>
                        <div id="psp_tw_snippet" style="display: none;">
                            <div class="Twitter-preview">
                                <div class="SummaryCard-imageContainer">
                                    <div class="tcu-imageWrapper" style="opacity: 1; background-image: url(https://pbs.twimg.com/card_img/840930485559779328/MaMOv9Qq?format=png&name=144x144_2); background-size: cover;"></div>
                                </div>
                                <div class="SummaryCard-contentContainer">
                                    <h2></h2>
                                    <p id="psp_tw_description"></p>
                                    <span id="psp_tw_url"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ================ End Tabs ================= -->
                </div>

                <input type="hidden" name="action" value="psp_savesettings_adminbar">
                <input type="hidden" name="psp_url" id="psp_url" value="<?php echo esc_url($view->post->url); ?>">

                <input type="hidden" name="psp_date_time" id="psp_date_time" value="<?php echo esc_attr(gmdate( "Y-m-d H:i:s" )); ?>">
                <input type="hidden" name="psp_nonce" value="<?php echo esc_attr(wp_create_nonce( _PSP_NONCE_ID_ )) ?>">

                <div id="psp_footer">
                    <input type="hidden" readonly="readonly" name="psp_hash" id="psp_hash" value="<?php echo esc_attr($view->post->hash); ?>">
                    <div class="row">
                        <div class="two columns ">
                            <img src=" <?php echo esc_url(_PSP_THEME_URL_) ?>/img/logo.png" alt="">
                        </div>
                        <div class="ten columns" style="font-style: italic; margin: 8px 0; color: #999;">
							<?php echo esc_html__( 'Info', _PSP_PLUGIN_NAME_ ) ?>
                            : <?php echo esc_html__( 'post type', _PSP_PLUGIN_NAME_ ) ?>
                            =<?php echo esc_attr($view->post->post_type) ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

	<?php
} else { ?>

    <div id="psp_settings_body" style="display: none">
        <div class="container">
            <button id="psp_close">x</button>
            <div id="psp_settings_body_content">
                <div id="psp_footer">
                    <div class="row">
                        <div class="two columns ">
                            <img src="<?php echo esc_url(_PSP_THEME_URL_ . '/img/logo.png') ?>" alt="">
                        </div>

                        <div class="nine columns" style="margin: 10px;">
							<?php echo esc_html__( "No data for this URL.", _PSP_PLUGIN_NAME_ ) ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

	<?php
}
?>
