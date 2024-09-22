<?php
/**
 * PSP_Models_Admin
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Admin {

	public $menu = array();

	public function __construct() {
		add_filter( 'psp_plugins', array( $this, 'getAvailablePlugins' ), 10, 1 );
		add_filter( 'psp_themes', array( $this, 'getAvailableThemes' ), 10, 1 );
		add_filter( 'psp_importList', array( $this, 'importList' ) );
	}

	/**
	 * Add a submenu menu in WP admin page
	 *
	 * @param array $param
	 *
	 * @return void
	 */
	public function addSubmenu( $param = null ) {
		if ( $param ) {
			$this->menu = $param;
		}

		if ( is_array( $this->menu ) ) {

			if ( $this->menu[0] <> '' && $this->menu[1] <> '' ) {
				/* add the translation */
				$this->menu[0] = __( $this->menu[0], _PSP_PLUGIN_NAME_ );
				$this->menu[1] = __( $this->menu[1], _PSP_PLUGIN_NAME_ );

				if ( ! isset( $this->menu[5] ) ) {
					$this->menu[5] = null;
				}

				/* add the menu with WP */
				add_submenu_page( $this->menu[0], $this->menu[1], $this->menu[2], $this->menu[3], $this->menu[4], $this->menu[5] );
			}
		}
	}

	/**
	 * Add a menu in WP admin page
	 *
	 * @param array $param
	 *
	 * @return void
	 */
	public function addMenu( $param = null ) {
		if ( $param ) {
			$this->menu = $param;
		}

		if ( is_array( $this->menu ) ) {

			if ( $this->menu[0] <> '' && $this->menu[1] <> '' ) {
				/* add the translation */
				$this->menu[0] = __( $this->menu[0], _PSP_PLUGIN_NAME_ );
				$this->menu[1] = __( $this->menu[1], _PSP_PLUGIN_NAME_ );

				if ( ! isset( $this->menu[5] ) ) {
					$this->menu[5] = null;
				}
				if ( ! isset( $this->menu[6] ) ) {
					$this->menu[6] = null;
				}

				/* add the menu with WP */
				add_menu_page( $this->menu[0], $this->menu[1], $this->menu[2], $this->menu[3], $this->menu[4], $this->menu[5], $this->menu[6] );
			}
		}
	}


	public function generateBackup() {
		global $wpdb;
		$tableName = $wpdb->prefix . strtolower( _PSP_DB_ );
		$query     = $wpdb->prepare( "SHOW TABLES LIKE %s", $tableName );
		$rows      = $wpdb->get_results( $query );
		$count     = count( $rows );

		$output = 'INSERT INTO `' . $tableName . '` (`blog_id`, `URL`, `url_hash`, `seo`, `date_time`) VALUES ';
		$i      = 0;
		foreach ( $rows as $row ) {
			$output .= '(\'' . $row->blog_id . '\', \'' . $row->URL . '\', \'' . $row->url_hash . '\', \'' . $row->seo . '\', \'' . $row->date_time . '\')';
			$output .= ++ $i == $count ? ';' : ',
            ';
		}

		return $count > 0 ? base64_encode( $output ) : null;
	}

	/**
	 * Get the PostMetas from database
	 *
	 * @param $id
	 * @param array $meta_keys
	 *
	 * @return PSP_Models_Domain_Qss
	 */
	public function getDBSeo( $id, $meta_keys = array() ) {
		global $wpdb;
		$metas = array();

		if ( (int) $id > 0 && ! empty( $meta_keys ) ) {
			$query = "SELECT * FROM " . $wpdb->postmeta . " WHERE post_id = " . (int) $id . " AND meta_key IN ('" . join( "','", array_values( $meta_keys ) ) . "');";
			if ( $rows = $wpdb->get_results( $query, OBJECT ) ) {
				$meta_keys = array_flip( $meta_keys );

				foreach ( $rows as $row ) {
					if ( isset( $meta_keys[ $row->meta_key ] ) ) {
						$metas[ $meta_keys[ $row->meta_key ] ] = stripslashes( $row->meta_value );
					}
				}
			}
		}

		if ( ! empty( $metas ) ) {
			return PSP_Classes_ObjController::getDomain( 'PSP_Models_Domain_Qss', $metas );
		}

		return PSP_Classes_ObjController::getDomain( 'PSP_Models_Domain_Qss' );
	}


	public function importList() {
		if ( $list = PSP_Classes_Tools::getOption( 'importList' ) ) {
			return $list;
		}

		$themes = array(
			'builder'      => array(
				'title'       => '_builder_seo_title',
				'description' => '_builder_seo_description',
				'keywords'    => '_builder_seo_keywords',
			),
			'catalyst'     => array(
				'title'       => '_catalyst_title',
				'description' => '_catalyst_description',
				'keywords'    => '_catalyst_keywords',
				'noindex'     => '_catalyst_noindex',
				'nofollow'    => '_catalyst_nofollow',
				'noarchive'   => '_catalyst_noarchive',
			),
			'frugal'       => array(
				'title'       => '_title',
				'description' => '_description',
				'keywords'    => '_keywords',
				'noindex'     => '_noindex',
				'nofollow'    => '_nofollow',
			),
			'genesis'      => array(
				'title'       => '_genesis_title',
				'description' => '_genesis_description',
				'keywords'    => '_genesis_keywords',
				'noindex'     => '_genesis_noindex',
				'nofollow'    => '_genesis_nofollow',
				'noarchive'   => '_genesis_noarchive',
				'canonical'   => '_genesis_canonical_uri',
				'redirect'    => 'redirect',
			),
			'headway'      => array(
				'title'       => '_title',
				'description' => '_description',
				'keywords'    => '_keywords',
			),
			'hybrid'       => array(
				'title'       => 'Title',
				'description' => 'Description',
				'keywords'    => 'Keywords',
			),
			'thesis'       => array(
				'title'       => 'thesis_title',
				'description' => 'thesis_description',
				'keywords'    => 'thesis_keywords',
				'redirect'    => 'thesis_redirect',
			),
			'wooframework' => array(
				'title'       => 'seo_title',
				'description' => 'seo_description',
				'keywords'    => 'seo_keywords',
			),
		);

		$plugins = array(
			'add-meta-tags'              => array(
				'title'       => '_amt_title',
				'description' => '_amt_description',
				'keywords'    => '_amt_keywords',
			),
			'gregs-high-performance-seo' => array(
				'title'       => '_ghpseo_secondary_title',
				'description' => '_ghpseo_alternative_description',
				'keywords'    => '_ghpseo_keywords',
			),
			'headspace2'                 => array(
				'title'       => '_headspace_page_title',
				'description' => '_headspace_description',
				'keywords'    => '_headspace_keywords',
			),
			'wpmu-dev-seo'               => array(
				'title'       => '_wds_title',
				'description' => '_wds_metadesc',
				'keywords'    => '_wds_keywords',
				'noindex'     => '_wds_meta-robots-noindex',
				'nofollow'    => '_wds_meta-robots-nofollow',
				'robots'      => '_wds_meta-robots-adv',
				'canonical'   => '_wds_canonical',
				'redirect'    => '_wds_redirect',
			),
			'platinum-seo-pack'          => array(
				'title'       => 'title',
				'description' => 'description',
				'keywords'    => 'keywords',
			),
			'seo-pressor'                => array(
				'title'       => '_seopressor_meta_title',
				'description' => '_seopressor_meta_description',
			),
			'seo-title-tag'              => array(
				'Custom Doctitle'  => 'title_tag',
				'META Description' => 'meta_description',
			),
			'seo-ultimate'               => array(
				'title'       => '_su_title',
				'description' => '_su_description',
				'keywords'    => '_su_keywords',
				'noindex'     => '_su_meta_robots_noindex',
				'nofollow'    => '_su_meta_robots_nofollow',
			),
			'wordpress-seo'              => array(
				'title'          => '_yoast_wpseo_title',
				'description'    => '_yoast_wpseo_metadesc',
				'keywords'       => '_yoast_wpseo_focuskw',
				'noindex'        => '_yoast_wpseo_meta-robots-noindex',
				'nofollow'       => '_yoast_wpseo_meta-robots-nofollow',
				'robots'         => '_yoast_wpseo_meta-robots-adv',
				'canonical'      => '_yoast_wpseo_canonical',
				'redirect'       => '_yoast_wpseo_redirect',
				'cornerstone'    => 'yst_is_cornerstone',
				'og_title'       => '_yoast_wpseo_opengraph-title',
				'og_description' => '_yoast_wpseo_opengraph-description',
				'og_media'       => '_yoast_wpseo_opengraph-image',
				'tw_title'       => '_yoast_wpseo_twitter-title',
				'tw_description' => '_yoast_wpseo_twitter-description',
				'tw_media'       => '_yoast_wpseo_twitter-image',
			),
			'all-in-one-seo-pack'        => array(
				'title'       => '_aioseop_title',
				'description' => '_aioseop_description',
				'keywords'    => '_aioseop_keywords',
				'noindex'     => '_aioseop_noindex',
				'nofollow'    => '_aioseop_nofollow',
				'canonical'   => '_aioseop_custom_link',
			),
		);
		$themes  = apply_filters( 'psp_themes', $themes );
		$plugins = apply_filters( 'psp_plugins', $plugins );

		$list = array_merge( (array) $plugins, (array) $themes );

		return $list;
	}

	/**
	 * Get the actual name of the plugin/theme
	 *
	 * @param $path
	 *
	 * @return string
	 */
	public function getName( $path ) {
		switch ( $path ) {
			case 'wpmu-dev-seo':
				return 'Infinite SEO';
			case 'wordpress-seo':
				return 'Yoast SEO';;
			default:
				return ucwords( str_replace( '-', ' ', $path ) );
		}
	}


	/**
	 * Rename all the plugin names with a hash
	 */
	public function getAvailablePlugins( $plugins ) {
		$found = array();

		$all_plugins = array_keys( get_plugins() );
		if ( is_multisite() ) {
			$all_plugins = array_merge( $all_plugins, array_keys( get_mu_plugins() ) );
		}
		foreach ( $all_plugins as $plugin ) {
			if ( strpos( $plugin, '/' ) !== false ) {
				$plugin = substr( $plugin, 0, strpos( $plugin, '/' ) );
			}
			if ( isset( $plugins[ $plugin ] ) ) {
				$found[ $plugin ] = $plugins[ $plugin ];
			}
		}

		return $found;
	}

	/**
	 * Rename all the themes name with a hash
	 */
	public function getAvailableThemes( $themes ) {
		$found = array();

		$all_themes = search_theme_directories();

		foreach ( $all_themes as $theme => $value ) {
			if ( isset( $themes[ $theme ] ) ) {
				$found[] = $themes[ $theme ];
			}
		}

		return $found;
	}

	/**
	 * @param $platform
	 *
	 * @return array
	 */
	public function importDBSettings( $platform ) {
		$platforms = apply_filters( 'psp_importList', false );
		if ( $platform <> '' && isset( $platforms[ $platform ] ) ) {

			if ( $platform == 'wordpress-seo' ) {

				if ( $yoast_socials = get_option( 'wpseo_social' ) ) {
					$socials = PSP_Classes_Tools::getOption( 'socials' );
					$codes   = PSP_Classes_Tools::getOption( 'codes' );
					foreach ( $yoast_socials as $key => $yoast_social ) {
						if ( $yoast_social <> '' && isset( $socials[ $key ] ) ) {
							$socials[ $key ] = $yoast_social;
						}
					}
					if ( ! empty( $socials ) ) {
						if ( isset( $yoast_socials['pinterestverify'] ) && $yoast_socials['plus-publisher'] <> '' ) {
							$codes['pinterest_verify'] = $yoast_socials['pinterestverify'];
						}
						PSP_Classes_Tools::saveOptions( 'socials', $socials );
						PSP_Classes_Tools::saveOptions( 'codes', $codes );
					}
				}
			}

			if ( $platform == 'all-in-one-seo-pack' ) {
				if ( $options = get_option( 'aioseop_options' ) ) {
					$socials = PSP_Classes_Tools::getOption( 'socials' );
					$codes   = PSP_Classes_Tools::getOption( 'codes' );

					PSP_Classes_Tools::saveOptions( 'socials', $socials );

					if ( isset( $options['aiosp_google_verify'] ) && $options['aiosp_google_verify'] <> '' ) {
						$codes['google_wt'] = $options['aiosp_google_verify'];
					}
					if ( isset( $options['aiosp_bing_verify'] ) && $options['aiosp_bing_verify'] <> '' ) {
						$codes['bing_wt'] = $options['aiosp_bing_verify'];
					}
					if ( isset( $options['aiosp_pinterest_verify'] ) && $options['aiosp_pinterest_verify'] <> '' ) {
						$codes['pinterest_verify'] = $options['aiosp_pinterest_verify'];
					}
					if ( isset( $options['aiosp_google_analytics_id'] ) && $options['aiosp_google_analytics_id'] <> '' ) {
						$codes['google_analytics'] = $options['aiosp_google_analytics_id'];
					}

					PSP_Classes_Tools::saveOptions( 'codes', $codes );
				}
			}

			if ( $platform == 'squirrly-seo' ) {
				if ( $options = json_decode( get_option( 'sq_options' ), true ) ) {
					$socials = PSP_Classes_Tools::getOption( 'socials' );
					$codes   = PSP_Classes_Tools::getOption( 'codes' );
					$jsonld  = PSP_Classes_Tools::getOption( 'psp_jsonld' );

					if ( isset( $options['sq_facebook_insights'] ) && $options['sq_facebook_insights'] <> '' ) {
						$socials['fb_admins'] = array( array( 'id' => $options['sq_facebook_insights'] ) );
					}
					if ( isset( $options['sq_facebook_account'] ) && $options['sq_facebook_account'] <> '' ) {
						$socials['facebook_site'] = $options['sq_facebook_account'];
					}
					if ( isset( $options['sq_twitter_account'] ) && $options['sq_twitter_account'] <> '' ) {
						$socials['twitter_site'] = $options['sq_twitter_account'];
					}
					if ( isset( $options['sq_twitter_account'] ) && $options['sq_twitter_account'] <> '' ) {
						$socials['twitter'] = $options['sq_twitter_account'];
					}
					if ( isset( $options['sq_instagram_account'] ) && $options['sq_instagram_account'] <> '' ) {
						$socials['instagram_url'] = $options['sq_instagram_account'];
					}
					if ( isset( $options['sq_linkedin_account'] ) && $options['sq_linkedin_account'] <> '' ) {
						$socials['linkedin_url'] = $options['sq_linkedin_account'];
					}
					if ( isset( $options['sq_pinterest_account'] ) && $options['sq_pinterest_account'] <> '' ) {
						$socials['pinterest_url'] = $options['sq_pinterest_account'];
					}
					if ( isset( $options['sq_auto_twittersize'] ) && $options['sq_auto_twittersize'] <> '' ) {
						$socials['twitter_card_type'] = ( $options['sq_auto_twittersize'] == 0 ) ? 'summary' : 'summary_large_image';
					}

					PSP_Classes_Tools::saveOptions( 'socials', $socials );

					if ( isset( $options['sq_google_wt'] ) && $options['sq_google_wt'] <> '' ) {
						$codes['google_wt'] = $options['sq_google_wt'];
					}
					if ( isset( $options['sq_google_analytics'] ) && $options['sq_google_analytics'] <> '' ) {
						$codes['google_analytics'] = $options['sq_google_analytics'];
					}
					if ( isset( $options['sq_facebook_analytics'] ) && $options['sq_facebook_analytics'] <> '' ) {
						$codes['facebook_pixel'] = $options['sq_facebook_analytics'];
					}
					if ( isset( $options['sq_bing_wt'] ) && $options['sq_bing_wt'] <> '' ) {
						$codes['bing_wt'] = $options['sq_bing_wt'];
					}
					if ( isset( $options['sq_pinterest'] ) && $options['sq_pinterest'] <> '' ) {
						$codes['pinterest_verify'] = $options['sq_pinterest'];
					}
					if ( isset( $options['sq_alexa'] ) && $options['sq_alexa'] <> '' ) {
						$codes['alexa_verify'] = $options['sq_alexa'];
					}

					PSP_Classes_Tools::saveOptions( 'codes', $codes );

					if ( isset( $options['sq_jsonld_type'] ) && $options['sq_jsonld_type'] <> '' ) {
						PSP_Classes_Tools::saveOptions( 'sq_jsonld_type', $options['sq_jsonld_type'] );
					}
					if ( isset( $options['sq_jsonld_type'] ) && $options['sq_jsonld_type'] <> '' ) {
						$jsonld[ $options['sq_jsonld_type'] ] = $options['sq_jsonld'][ $options['sq_jsonld_type'] ];
					}

					PSP_Classes_Tools::saveOptions( 'psp_jsonld', $jsonld );
				}
			}
		}

	}

	public function importDBSeo( $platform ) {
		global $wpdb;

		$platforms = apply_filters( 'psp_importList', false );
		if ( $platform <> '' && isset( $platforms[ $platform ] ) ) {
			$meta_keys = $platforms[ $platform ];
			$metas     = array();

			if ( ! empty( $meta_keys ) ) {

				$query     = "SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key IN ('" . join( "','", array_values( $meta_keys ) ) . "');";
				$meta_keys = array_flip( $meta_keys );

				if ( $rows = $wpdb->get_results( $query, OBJECT ) ) {
					foreach ( $rows as $row ) {
						if ( isset( $meta_keys[ $row->meta_key ] ) && $row->meta_value <> '' ) {
							$metas[ md5( $row->post_id ) ]['url']                          = get_permalink( $row->post_id );
							$metas[ md5( $row->post_id ) ][ $meta_keys[ $row->meta_key ] ] = stripslashes( $row->meta_value );
						}
					}
				}

				if ( $platform == 'wordpress-seo' ) {
					//get taxonomies
					if ( $taxonomies = get_option( 'wpseo_taxonomy_meta' ) ) {
						if ( ! empty( $taxonomies ) ) {
							foreach ( $taxonomies as $taxonomie => $terms ) {
								if ( ! empty( $terms ) ) {
									if ( $taxonomie <> 'category' ) {
										$taxonomie = 'tax-' . $taxonomie;
									}
									foreach ( $terms as $term_id => $taxmetas ) {
										if ( ! empty( $taxmetas ) ) {
											if ( ! is_wp_error( get_term_link( $term_id ) ) ) {
												$metas[ md5( $taxonomie . $term_id ) ]['url'] = get_term_link( $term_id );
												foreach ( $taxmetas as $meta_key => $meta_value ) {
													if ( $meta_key == 'wpseo_desc' ) {
														$meta_key = '_yoast_wpseo_metadesc';
													} else {
														$meta_key = '_yoast_' . $meta_key;
													}

													if ( isset( $meta_keys[ $meta_key ] ) ) {
														$metas[ md5( $taxonomie . $term_id ) ][ $meta_keys[ $meta_key ] ] = stripslashes( $meta_value );
													}
												}
											}
										}
									}
								}
							}
						}
					}

					//get all patterns from Yoast
					if ( $yoast_patterns = get_option( 'wpseo_titles' ) ) {
						if ( ! empty( $yoast_patterns ) ) {
							$patterns = PSP_Classes_Tools::getOption( 'patterns' );
							foreach ( $patterns as $path => &$values ) {
								if ( $path == 'profile' ) {
									$path = 'author';
								}
								if ( isset( $yoast_patterns['separator'] ) && $yoast_patterns['separator'] <> '' ) {
									$values['sep'] = $yoast_patterns['separator'];
								}
								if ( isset( $yoast_patterns["title-$path-wpseo"] ) && $yoast_patterns["title-$path-wpseo"] <> '' ) {
									$values['title'] = preg_replace( '/%%([^\%]+)%%/', '{{$1}}', $yoast_patterns["title-$path-wpseo"] );
								}
								if ( isset( $yoast_patterns["metadesc-$path-wpseo"] ) && $yoast_patterns["metadesc-$path-wpseo"] <> '' ) {
									$values['description'] = preg_replace( '/%%([^\%]+)%%/', '{{$1}}', $yoast_patterns["metadesc-$path-wpseo"] );
								}
								if ( isset( $yoast_patterns["noindex-$path-wpseo"] ) ) {
									$values['noindex'] = (int) $yoast_patterns["noindex-$path-wpseo"];
								}
								if ( isset( $yoast_patterns["disable-$path-wpseo"] ) ) {
									$values['disable'] = (int) $yoast_patterns["disable-$path-wpseo"];
								}

								if ( isset( $yoast_patterns["title-$path"] ) && $yoast_patterns["title-$path"] <> '' ) {
									$values['title'] = preg_replace( '/%%([^\%]+)%%/', '{{$1}}', $yoast_patterns["title-$path"] );
								}
								if ( isset( $yoast_patterns["metadesc-$path"] ) && $yoast_patterns["metadesc-$path"] <> '' ) {
									$values['description'] = preg_replace( '/%%([^\%]+)%%/', '{{$1}}', $yoast_patterns["metadesc-$path"] );
								}
								if ( isset( $yoast_patterns["noindex-$path"] ) ) {
									$values['noindex'] = (int) $yoast_patterns["noindex-$path"];
								}
								if ( isset( $yoast_patterns["disable-$path"] ) ) {
									$values['disable'] = (int) $yoast_patterns["disable-$path"];
								}
							}

							PSP_Classes_Tools::saveOptions( 'patterns', $patterns );
						}
					}
				}

				if ( $platform == 'all-in-one-seo-pack' ) {
					if ( $options = get_option( 'aioseop_options' ) ) {
						$patterns = PSP_Classes_Tools::getOption( 'patterns' );

						$find    = array(
							'page_title',
							'post_title',
							'archive_title',
							'blog_title',
							'blog_description',
							'category_title',
							'author',
							'page_author_nicename',
							'description',
							'request_words',
							'search',
							'current_date'
						);
						$replace = array(
							'title',
							'title',
							'title',
							'sitename',
							'sitedesc',
							'category',
							'name',
							'name',
							'excerpt',
							'searchphrase',
							'searchphrase',
							'currentdate'
						);

						if ( isset( $options['aiosp_page_title_format'] ) && $options['aiosp_page_title_format'] <> '' ) {
							$patterns['home']['title'] = preg_replace( '/%([^\%]+)%/', '{{$1}}', str_replace( $find, $replace, $options['aiosp_page_title_format'] ) );
						}
						if ( isset( $options['aiosp_post_title_format'] ) && $options['aiosp_post_title_format'] <> '' ) {
							$patterns['post']['title'] = preg_replace( '/%([^\%]+)%/', '{{$1}}', str_replace( $find, $replace, $options['aiosp_post_title_format'] ) );
						}
						if ( isset( $options['aiosp_category_title_format'] ) && $options['aiosp_category_title_format'] <> '' ) {
							$patterns['category']['title'] = preg_replace( '/%([^\%]+)%/', '{{$1}}', str_replace( $find, $replace, $options['aiosp_category_title_format'] ) );
						}
						if ( isset( $options['aiosp_archive_title_format'] ) && $options['aiosp_archive_title_format'] <> '' ) {
							$patterns['archive']['title'] = preg_replace( '/%([^\%]+)%/', '{{$1}}', str_replace( $find, $replace, $options['aiosp_archive_title_format'] ) );
						}
						if ( isset( $options['aiosp_author_title_format'] ) && $options['aiosp_author_title_format'] <> '' ) {
							$patterns['profile']['title'] = preg_replace( '/%([^\%]+)%/', '{{$1}}', str_replace( $find, $replace, $options['aiosp_author_title_format'] ) );
						}
						if ( isset( $options['aiosp_tag_title_format'] ) && $options['aiosp_tag_title_format'] <> '' ) {
							$patterns['tag']['title'] = preg_replace( '/%([^\%]+)%/', '{{$1}}', str_replace( $find, $replace, $options['aiosp_tag_title_format'] ) );
						}
						if ( isset( $options['aiosp_search_title_format'] ) && $options['aiosp_search_title_format'] <> '' ) {
							$patterns['search']['title'] = preg_replace( '/%([^\%]+)%/', '{{$1}}', str_replace( $find, $replace, $options['aiosp_search_title_format'] ) );
						}
						if ( isset( $options['aiosp_404_title_format'] ) && $options['aiosp_404_title_format'] <> '' ) {
							$patterns['404']['title'] = preg_replace( '/%([^\%]+)%/', '{{$1}}', str_replace( $find, $replace, $options['aiosp_404_title_format'] ) );
						}
						if ( isset( $options['aiosp_product_title_format'] ) && $options['aiosp_product_title_format'] <> '' ) {
							$patterns['product']['title'] = preg_replace( '/%([^\%]+)%/', '{{$1}}', str_replace( $find, $replace, $options['aiosp_product_title_format'] ) );
						}

						PSP_Classes_Tools::saveOptions( 'patterns', $patterns );
					}
				}


			}

			return $metas;
		}

		return false;
	}
}
