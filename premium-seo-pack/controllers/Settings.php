<?php
/**
 * PSP_Controllers_Settings
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Controllers_Settings extends PSP_Classes_FrontController {

	public $options;
	public $tabs;

	public function hookHead() {
		if ( ! defined( '_SQ_PLUGIN_NAME_' ) ) {
			PSP_Classes_ObjController::getClass( 'PSP_Classes_DisplayController' )->loadMedia( 'global.css' );
		}
	}

	/**
	 * Called when Post action is triggered
	 *
	 * @return void
	 * @throws Exception
	 */
	public function action() {
		parent::action();

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}
		if ( PSP_Classes_Tools::isAjax() ) {
			PSP_Classes_Tools::setHeader( 'json' );
		}

		switch ( PSP_Classes_Tools::getValue( 'action' ) ) {
			case 'psp_resetsettings':
				PSP_Classes_Tools::$options = PSP_Classes_Tools::getOptions( 'reset' );
				PSP_Classes_Tools::saveOptions();
				break;

			case 'psp_importsettings':
				$platform = PSP_Classes_Tools::getValue( 'psp_import_platform', '' );
				if ( $platform <> '' ) {
					PSP_Classes_ObjController::getClass( 'PSP_Models_Admin' )->importDBSettings( $platform );
					PSP_Classes_Error::setMessage( __( 'All the Plugin settings were imported successfully!', _PSP_PLUGIN_NAME_ ) );
				}
				break;
			case 'psp_importseo':
				$platform = PSP_Classes_Tools::getValue( 'psp_import_platform', '' );
				if ( $platform <> '' ) {
					$seo = PSP_Classes_ObjController::getClass( 'PSP_Models_Admin' )->importDBSeo( $platform );
					if ( ! empty( $seo ) ) {
						foreach ( $seo as $psp_hash => $metas ) {
							$this->model->db_insert( ( isset( $metas['url'] ) ? $metas['url'] : '' ), $psp_hash, addslashes( maybe_serialize( $metas ) ), gmdate( 'Y-m-d H:i:s' ) );
						}
						PSP_Classes_Error::setMessage( sprintf( __( '%s SEO records were imported successfully!', _PSP_PLUGIN_NAME_ ), count( $seo ) ) );
					}
				}
				break;
			case 'psp_savesettings_seo':

				PSP_Classes_Tools::saveOptions( "psp_title_opt", PSP_Classes_Tools::getValue( 'psp_title_opt', 1 ) );
				PSP_Classes_Tools::saveOptions( "psp_description_opt", PSP_Classes_Tools::getValue( 'psp_description_opt', 1 ) );
				PSP_Classes_Tools::saveOptions( "psp_keywords_opt", PSP_Classes_Tools::getValue( 'psp_keywords_opt', 1 ) );
				PSP_Classes_Tools::saveOptions( "psp_canonical_opt", PSP_Classes_Tools::getValue( 'psp_canonical_opt', 1 ) );
				PSP_Classes_Tools::saveOptions( "psp_prevnext_opt", PSP_Classes_Tools::getValue( 'psp_prevnext_opt', 1 ) );
				//

				PSP_Classes_Tools::saveOptions( "psp_noindex_opt", PSP_Classes_Tools::getValue( 'psp_noindex_opt', 1 ) );

				$patterns = PSP_Classes_Tools::getValue( 'psp_patterns', array() );
				if ( ! empty( $patterns ) ) {
					PSP_Classes_Tools::saveOptions( "patterns", $patterns );
				}

				//If a new post type is added
				$newposttype = PSP_Classes_Tools::getValue( 'psp_select_post_types', '' );
				if ( $newposttype <> '' ) {
					$patterns[ $newposttype ] = $patterns['custom'];
					if ( ! empty( $patterns ) ) {
						PSP_Classes_Tools::saveOptions( "patterns", $patterns );
					}
				}


				if ( PSP_Classes_Tools::isAjax() ) {
					echo json_encode( array( 'saved' => true ) );
				}
				break;
			case 'psp_savesettings_social':

				PSP_Classes_Tools::saveOptions( "psp_og_opt", PSP_Classes_Tools::getValue( 'psp_og_opt', 1 ) );
				PSP_Classes_Tools::saveOptions( "psp_tw_opt", PSP_Classes_Tools::getValue( 'psp_tw_opt', 1 ) );

				$socials = PSP_Classes_Tools::getValue( 'psp_socials', array() );
				if ( ! empty( $socials ) ) {
					if ( ! empty( $socials['fb_admins'] ) ) {
						foreach ( $socials['fb_admins'] as $index => $value ) {
							if ( isset( $value['id'] ) && $value['id'] == '' ) {
								unset( $socials['fb_admins'][ $index ] );
							} else {
								$socials['fb_admins'][ $index ]['id'] = $this->model->checkFacebookAdminCode( $value['id'] );
							}
						}
					}

					if ( isset( $socials['twitter_site'] ) ) {
						$socials['twitter_site'] = $this->model->checkTwitterAccount( $socials['twitter_site'] );
					}
					if ( isset( $socials['facebook_site'] ) ) {
						$socials['facebook_site'] = $this->model->checkFacebookAccount( $socials['facebook_site'] );
					}
					if ( isset( $socials['linkedin_url'] ) ) {
						$socials['linkedin_url'] = $this->model->checkLinkeinAccount( $socials['linkedin_url'] );
					}
					if ( isset( $socials['pinterest_url'] ) ) {
						$socials['pinterest_url'] = $this->model->checkPinterestAccount( $socials['pinterest_url'] );
					}
					if ( isset( $socials['instagram_url'] ) ) {
						$socials['instagram_url'] = $this->model->checkInstagramAccount( $socials['instagram_url'] );
					}
					if ( isset( $socials['myspace_url'] ) ) {
						$socials['myspace_url'] = $this->model->checkMySpaceAccount( $socials['myspace_url'] );
					}
					if ( isset( $socials['youtube_url'] ) ) {
						$socials['youtube_url'] = $this->model->checkYoutubeAccount( $socials['youtube_url'] );
					}

					PSP_Classes_Tools::saveOptions( "socials", $socials );
				}


				if ( PSP_Classes_Tools::isAjax() ) {
					echo json_encode( array( 'saved' => 1 ) );
				}
				break;
			case 'psp_savesettings_tracking':
				$codes = PSP_Classes_Tools::getValue( 'psp_codes', array() );
				if ( ! empty( $codes ) ) {
					if ( isset( $codes['bing_wt'] ) ) {
						$codes['bing_wt'] = $this->model->checkBingWTCode( $codes['bing_wt'] );
					}
					if ( isset( $codes['pinterest_verify'] ) ) {
						$codes['pinterest_verify'] = $this->model->checkPinterestCode( $codes['pinterest_verify'] );
					}
					if ( isset( $codes['google_wt'] ) ) {
						$codes['google_wt'] = $this->model->checkGoogleWTCode( $codes['google_wt'] );
					}
					if ( isset( $codes['google_analytics'] ) ) {
						$codes['google_analytics'] = $this->model->checkGoogleAnalyticsCode( $codes['google_analytics'] );
					}

					PSP_Classes_Tools::saveOptions( "codes", $codes );
				}

				if ( PSP_Classes_Tools::isAjax() ) {
					echo json_encode( array( 'saved' => 1 ) );
				}
				break;
			case 'psp_savesettings_jsonld':

				PSP_Classes_Tools::saveOptions( "psp_jsonld_opt", PSP_Classes_Tools::getValue( 'psp_jsonld_opt', 1 ) );

				$jsonld_type = PSP_Classes_Tools::getValue( 'psp_jsonld_type', 'Person' );
				PSP_Classes_Tools::saveOptions( "psp_jsonld_type", $jsonld_type );
				PSP_Classes_Tools::saveOptions( "psp_jsonld_clearcode", PSP_Classes_Tools::getValue( 'psp_jsonld_clearcode', 1 ) );
				$jsonld                 = PSP_Classes_Tools::getOption( "psp_jsonld" );
				$jsonld[ $jsonld_type ] = array_merge( $jsonld[ $jsonld_type ], PSP_Classes_Tools::getValue( 'psp_jsonld', array() ) );

				if ( ! empty( $jsonld ) ) {
					PSP_Classes_Tools::saveOptions( "psp_jsonld", $jsonld );
				}

				if ( PSP_Classes_Tools::isAjax() ) {
					echo json_encode( array( 'saved' => 1 ) );
				}
				break;

			case 'psp_savesettings_adminbar':
				$json = array();
				if ( PSP_Classes_Tools::getIsset( 'psp_hash' ) ) {
					$psp_hash = preg_replace('/[^a-zA-Z0-9]/', '', PSP_Classes_Tools::getValue( 'psp_hash' ));
					$url      = PSP_Classes_Tools::getValue( 'psp_url' );

					$qss        = PSP_Classes_ObjController::getDomain( 'PSP_Models_Domain_Qss' );
					$qss->doseo = PSP_Classes_Tools::getValue( 'psp_doseo', 0 );

					$qss->title       = PSP_Classes_Tools::getValue( 'psp_title' );
					$qss->description = PSP_Classes_Tools::getValue( 'psp_description' );
					$qss->keywords    = PSP_Classes_Tools::getValue( 'psp_keywords', array() );
					$qss->canonical   = PSP_Classes_Tools::getValue( 'psp_canonical' );
					$qss->noindex     = PSP_Classes_Tools::getValue( 'psp_noindex', 0 );
					$qss->nofollow    = PSP_Classes_Tools::getValue( 'psp_nofollow', 0 );


					$qss->og_title       = PSP_Classes_Tools::getValue( 'psp_og_title' );
					$qss->og_description = PSP_Classes_Tools::getValue( 'psp_og_description' );
					$qss->og_author      = PSP_Classes_Tools::getValue( 'psp_og_author' );
					$qss->og_type        = PSP_Classes_Tools::getValue( 'psp_og_type', 'website' );
					$qss->og_media       = PSP_Classes_Tools::getValue( 'psp_og_media' );

					$qss->tw_title       = PSP_Classes_Tools::getValue( 'psp_tw_title' );
					$qss->tw_description = PSP_Classes_Tools::getValue( 'psp_tw_description' );
					$qss->tw_media       = PSP_Classes_Tools::getValue( 'psp_tw_media' );


					//empty the cache from cache plugins
					PSP_Classes_Tools::emptyCache();

					if ( $this->model->db_insert( $url, $psp_hash, addslashes( serialize( $qss->toArray() ) ), gmdate( 'Y-m-d H:i:s' ) ) ) {
						$json['saved'] = $psp_hash;

						if ( PSP_Classes_Tools::isAjax() ) {
							if ( PSP_Classes_ObjController::getClass( "PSP_Controllers_Admin" )->setPostByURL( $url ) ) {
								$json['html'] = PSP_Classes_ObjController::getClass( "PSP_Controllers_Admin" )->getView( 'FrontMenu' );
							}
						}
					} else {
						$json['error'] = 1;
					}

				} else {
					$json['error'] = 1;
				}

				if ( PSP_Classes_Tools::isAjax() ) {
					echo json_encode( $json );
				}
				break;

			case 'psp_backup':
				update_option( "psp_backup", PSP_Classes_ObjController::getClass( "PSP_Models_Admin" )->generateBackup() );
				break;
			case 'psp_restore':
				global $wpdb;
				$json = array();

				if ( $backup = get_option( 'psp_backup' ) ) {
					if ( $bkp = base64_decode( $backup ) ) {
						if ( strlen( $bkp ) > 10 ) {
							$wpdb->query( $bkp );
							$json['saved'] = 1;
						}
					} else {
						$json['error'] = 1;
					}
				} else {
					$json['error'] = 1;
				}

				echo json_encode( $json );
				break;
		}

		if ( PSP_Classes_Tools::isAjax() ) {
			exit();
		}

	}

}
