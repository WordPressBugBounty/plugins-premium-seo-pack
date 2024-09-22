<?php
/**
 * PSP_Classes_Tools
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * Handles the parameters and url
 *
 * @author StarBox
 */
class PSP_Classes_Tools extends PSP_Classes_FrontController {

	/** @var array Saved options in database */
	public static $options = array();
	public static $debug = array();
	public static $is_ajax = null;

	public function __construct() {
		parent::__construct();

		self::$options = $this->getOptions();
	}

	/**
	 * Load the Options from user option table in DB
	 *
	 * @param string $action
	 *
	 * @return array
	 */
	public static function getOptions( $action = 'load' ) {

		$init = array(
			'psp_ver'                   => 0,
			'psp_api'                   => '',
			//
			'psp_separator'             => '|',
			'psp_remove_duplicates_opt' => 1,
			'psp_import_opt'            => 1,
			//
			'psp_title_opt'             => 1,
			'psp_description_opt'       => 1,
			'psp_keywords_opt'          => 1,
			'psp_canonical_opt'         => 1,
			'psp_prevnext_opt'          => 1,
			'psp_sitemap_opt'           => 0,
			'psp_media_opt'             => 0,
			//
			'psp_og_opt'                => 1,
			'psp_fb_admin'              => '',
			'psp_tw_opt'                => 1,
			'psp_tw_account'            => '',
			//
			'psp_jsonld_opt'            => 1,
			'psp_noindex_opt'           => 1,
			//
			'psp_jsonld_clearcode'      => 0,
			'psp_jsonld_breadcrumbs'    => 1,
			'psp_jsonld_type'           => 'Organization',
			'psp_jsonld'                => array(
				'Organization' => array(
					'name'        => '',
					'logo'        => '',
					'telephone'   => '',
					'contactType' => '',
					'description' => ''
				),
				'Person'       => array(
					'name'        => '',
					'logo'        => '',
					'telephone'   => '',
					'jobTitle'    => '',
					'description' => ''
				)
			),

			'socials' => array(
				'fb_admins'    => array(),
				'fbconnectkey' => "",
				'fbadminapp'   => "",

				'facebook_site'     => "",
				'twitter_site'      => "",
				'twitter'           => "",
				'instagram_url'     => "",
				'linkedin_url'      => "",
				'myspace_url'       => "",
				'pinterest_url'     => "",
				'youtube_url'       => "",
				'twitter_card_type' => "",
			),

			'codes' => array(
				'google_wt'        => "",
				'google_analytics' => "",
				'facebook_pixel'   => "",

				'bing_wt'          => "",
				'pinterest_verify' => "",
				'alexa_verify'     => "",
			),

			'psp_metas' => array(
				'title_maxlength'              => 75,
				'description_maxlength'        => 320,
				'og_title_maxlength'           => 75,
				'og_description_maxlength'     => 110,
				'tw_title_maxlength'           => 75,
				'tw_description_maxlength'     => 280,
				'jsonld_title_maxlength'       => 75,
				'jsonld_description_maxlength' => 110,
			),

			'patterns' => array(
				'home'                       => array(
					'sep'         => '|',
					'title'       => '{{sitename}} {{page}} {{sep}} {{sitedesc}}',
					'description' => '{{excerpt}} {{page}} {{sep}} {{sitename}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'post'                       => array(
					'sep'         => '|',
					'title'       => '{{title}} {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'page'                       => array(
					'sep'         => '|',
					'title'       => '{{title}} {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'category'                   => array(
					'sep'         => '|',
					'title'       => '{{category}} {{page}} {{sep}} {{sitename}}',
					'description' => '{{category_description}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'tag'                        => array(
					'sep'         => '|',
					'title'       => '{{tag}} {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'tax-post_format'            => array(
					'sep'         => '|',
					'title'       => '{{term_title}} ' . __( 'Format', _PSP_PLUGIN_NAME_ ) . ' {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'tax-category'               => array(
					'sep'         => '|',
					'title'       => '{{term_title}} ' . __( 'Category', _PSP_PLUGIN_NAME_ ) . ' {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'tax-post_tag'               => array(
					'sep'         => '|',
					'title'       => '{{term_title}} ' . __( 'Tag', _PSP_PLUGIN_NAME_ ) . ' {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'tax-product_cat'            => array(
					'sep'         => '|',
					'title'       => '{{term_title}} ' . __( 'Category', _PSP_PLUGIN_NAME_ ) . ' {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'tax-product_tag'            => array(
					'sep'         => '|',
					'title'       => '{{term_title}} ' . __( 'Tag', _PSP_PLUGIN_NAME_ ) . ' {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'tax-product_shipping_class' => array(
					'sep'         => '|',
					'title'       => '{{term_title}} ' . __( 'Shipping Option', _PSP_PLUGIN_NAME_ ) . ' {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'profile'                    => array(
					'sep'         => '|',
					'title'       => '{{name}}, ' . __( 'Author at', _PSP_PLUGIN_NAME_ ) . ' {{sitename}} {{page}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'shop'                       => array(
					'sep'         => '|',
					'title'       => '{{title}} {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'product'                    => array(
					'sep'         => '|',
					'title'       => '{{title}} {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'archive'                    => array(
					'sep'         => '|',
					'title'       => '{{date}} {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'search'                     => array(
					'sep'         => '|',
					'title'       => __( 'You searched for', _PSP_PLUGIN_NAME_ ) . ' {{searchphrase}} {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 1,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'attachment'                 => array(
					'sep'         => '|',
					'title'       => '{{title}} {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
				'404'                        => array(
					'sep'         => '|',
					'title'       => __( 'Page not found', _PSP_PLUGIN_NAME_ ) . ' {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 1,
					'nofollow'    => 1,
					'disable'     => 0,
				),
				'custom'                     => array(
					'sep'         => '|',
					'title'       => '{{title}} {{page}} {{sep}} {{sitename}}',
					'description' => '{{excerpt}}',
					'noindex'     => 0,
					'nofollow'    => 0,
					'disable'     => 0,
				),
			)
		);


		if ( is_multisite() ) {
			$options = json_decode( get_blog_option( get_current_blog_id(), _PSP_OPTION_ ), true );
		} else {
			$options = json_decode( get_option( _PSP_OPTION_ ), true );
		}

		if ( $action == 'reset' ) {
			$init['psp_ver'] = $options['psp_ver'];
			$init['psp_api'] = $options['psp_api'];

			return $init;
		}

		if ( ! empty( $options ) ) {
			$options = @array_merge( $init, $options );
		} else {
			return $init;
		}

		return $options;
	}

	/**
	 * Get the option from database
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public static function getOption( $key ) {
		if ( ! isset( self::$options[ $key ] ) ) {
			self::$options = self::getOptions();

			if ( ! isset( self::$options[ $key ] ) ) {
				self::$options[ $key ] = false;
			}
		}

		return self::$options[ $key ];
	}

	/**
	 * Get a value from $_POST / $_GET and sanitize them for all plugin usage
	 * if unavailable, take a default value
	 *
	 * @param  string $key           Value key
	 * @param  mixed  $defaultValue  (optional)
	 * @param  bool   $keep_newlines
	 * @return mixed Value
	 */
	public static function getValue($key, $defaultValue = false, $keep_newlines = false)
	{
		if ( empty($key) || $key == -1) {
			return $defaultValue;
		}

		//Get the params from forms
		if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : ''));
		} else {
			$ret = (isset($_GET[$key]) ? $_GET[$key] : '');
		}

		//Start sanitization of each param
		//based on the type
		if (is_array($ret)) {
			if (!empty($ret)) {
				foreach ($ret as &$row) {
					if (!is_array($row)) {
						$row = sanitize_text_field($row); //sanitize
					}
				}
			}
		} elseif (is_string($ret) && $ret <> '' && $keep_newlines && function_exists('sanitize_textarea_field')) {
			$ret = sanitize_textarea_field($ret);
		}  else{
			$ret = sanitize_text_field($ret);
		}

		return wp_unslash($ret);

	}

	/**
	 * Check if the parameter is set
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public static function getIsset( $key ) {
		if ( ! isset( $key ) or empty( $key ) or ! is_string( $key ) ) {
			return false;
		}

		return isset( $_POST[ $key ] ) ? true : ( isset( $_GET[ $key ] ) ? true : false );
	}

	/**
	 * Set the header type
	 *
	 * @param string $type
	 */
	public static function setHeader( $type ) {
		if ( self::getValue( 'psp_debug' ) === 'on' ) {
			return;
		}

		if ( $type == 'json' ) {
			header( 'Content-Type: application/json' );
		}
	}

	public static function setValue( $key, $value ) {
		$_POST[ $key ] = $value;
		$_GET[ $key ]  = $value;
	}


	/**
	 * Connect remote with CURL if exists
	 *
	 * @param string $url
	 * @param array $param
	 * @param array $options
	 *
	 * @return bool|string
	 */
	public static function psp_remote_get( $url, $param = array(), $options = array() ) {
		$parameters           = '';
		$options['method']    = 'get';
		$options['sslverify'] = false;
		$options['timeout']   = ( isset( $options['timeout'] ) ) ? $options['timeout'] : 30;

		if ( isset( $param ) ) {
			foreach ( $param as $key => $value ) {
				if ( isset( $key ) && $key <> '' && $key <> 'timeout' && $value <> '' ) {
					$parameters .= ( $parameters == "" ? "" : "&" ) . $key . "=" . $value;
				}
			}
			if ( $parameters <> '' ) {
				$url .= ( ( strpos( $url, "?" ) === false ) ? "?" : "&" ) . $parameters;
			}
		}


		if ( ! $response = self::psp_wpcall( $url, $options ) ) {
			if ( function_exists( 'curl_init' ) && ! ini_get( 'safe_mode' ) && ! ini_get( 'open_basedir' ) ) {
				$response = self::psp_curl( $url, $options );
			} else {
				return false;
			}
		}

		return $response;
	}

	/**
	 * Connect remote with CURL if exists
	 */
	public static function psp_remote_post( $url, $param = array(), $options = array() ) {
		$parameters           = '';
		$options['method']    = 'post';
		$options['sslverify'] = false;
		$options['timeout']   = ( isset( $options['timeout'] ) ) ? $options['timeout'] : 30;

		if ( ! $response = self::psp_wpcall( $url, $options, $param ) ) {
			if ( function_exists( 'curl_init' ) && ! ini_get( 'safe_mode' ) && ! ini_get( 'open_basedir' ) ) {
				$response = self::psp_curl( $url, $options, $param );
			}
		}

		if ( $json = json_decode( $response ) ) {
			if ( isset( $json->error ) && $json->error == "subscription_expired" ) {
				PSP_Classes_ObjController::getClass( 'PSP_Classes_Error' )->setError( sprintf( __( 'You can\'t use this plugin with your current subscription. %s' ), '<a href="https://plugin.squirrly.co" target="_blank">Click here</a>' ), 'error' );
			}
		}

		return $response;
	}


	/**
	 * Call remote UR with CURL
	 *
	 * @param string $url
	 * @param array $options
	 *
	 * @return string
	 */
	private static function psp_curl( $url, $options, $data = array() ) {

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		//--
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		//--
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $options['timeout'] );

		if ( isset( $options['followlocation'] ) ) {
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt( $ch, CURLOPT_MAXREDIRS, 1 );
		}

		if ( $options['method'] == 'post' ) {
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data, '', '&' ) );
		}

		if ( isset( $options['User-Agent'] ) && $options['User-Agent'] <> '' ) {
			curl_setopt( $ch, CURLOPT_USERAGENT, $options['User-Agent'] );
		}

		$response = curl_exec( $ch );
		$response = self::cleanResponce( $response );

		curl_close( $ch );

		return $response;
	}

	/**
	 * Use the WP remote call
	 *
	 * @param string $url
	 * @param array $param
	 *
	 * @return string
	 */
	private static function psp_wpcall( $url, $options, $data = array() ) {
		if ( $options['method'] == 'post' ) {
			unset( $options['method'] );
			$options['body'] = $data;
			$response        = wp_remote_post( $url, $options );
		} else {
			unset( $options['method'] );
			$response = wp_remote_get( $url, $options );
		}


		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response = self::cleanResponce( wp_remote_retrieve_body( $response ) ); //clear and get the body

		return $response;
	}

	/**
	 * Get the Json from responce if any
	 *
	 * @param string $response
	 *
	 * @return string
	 */
	private static function cleanResponce( $response ) {

		$response = trim( $response, '(' );
		$response = trim( $response, ')' );

		return $response;
	}

	/**
	 * Connect remote with CURL if exists
	 *
	 * @param string $url
	 *
	 * @return array|WP_Error
	 */
	public static function psp_remote_head( $url ) {
		$response = array();

		if ( function_exists( 'curl_exec' ) ) {
			$ch = curl_init( $url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
			curl_exec( $ch );

			$response['headers']['content-type'] = curl_getinfo( $ch, CURLINFO_CONTENT_TYPE );
			$response['response']['code']        = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
			curl_close( $ch );

			return $response;
		} else {
			return wp_remote_head( $url, array( 'timeout' => 30 ) );
		}

	}


	public static function checkMemory() {
		$memory_avail = ini_get( 'memory_limit' );
		$memory_used  = number_format( memory_get_usage( true ) / ( 1024 * 1024 ), 2 );
		if ( strpos( $memory_avail, 'M' ) !== false ) {
			$memory_avail = str_replace( 'M', '', $memory_avail );

			return ( $memory_avail - $memory_used );
		}

		return false;
	}

	/**
	 * Support for i18n with wpml, polyglot or qtrans
	 *
	 * @param string $in
	 *
	 * @return string $in localized
	 */
	public static function i18n( $in ) {
		if ( function_exists( 'langswitch_filter_langs_with_message' ) ) {
			$in = langswitch_filter_langs_with_message( $in );
		}
		if ( function_exists( 'polyglot_filter' ) ) {
			$in = polyglot_filter( $in );
		}
		if ( function_exists( 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
			$in = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage( $in );
		}
		$in = apply_filters( 'localization', $in );

		return $in;
	}

	/**
	 * Convert integer on the locale format.
	 *
	 * @param int $number The number to convert based on locale.
	 * @param int $decimals Precision of the number of decimal places.
	 *
	 * @return string Converted number in string format.
	 */
	public static function i18n_number_format( $number, $decimals = 0 ) {
		global $wp_locale;
		$formatted = number_format( $number, absint( $decimals ), $wp_locale->number_format['decimal_point'], $wp_locale->number_format['thousands_sep'] );

		return apply_filters( 'number_format_i18n', $formatted );
	}

	public static function emptyCache() {
		if ( function_exists( 'w3tc_pgcache_flush' ) ) {
			w3tc_pgcache_flush();
		}

		if ( function_exists( 'w3tc_minify_flush' ) ) {
			w3tc_minify_flush();
		}
		if ( function_exists( 'w3tc_dbcache_flush' ) ) {
			w3tc_dbcache_flush();
		}
		if ( function_exists( 'w3tc_objectcache_flush' ) ) {
			w3tc_objectcache_flush();
		}

		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache();
		}

		if ( function_exists( 'rocket_clean_domain' ) ) {
			// Remove all cache files
			rocket_clean_domain();
		}

		if ( function_exists( 'rocket_clean_minify' ) ) {
			// Remove all minify cache files
			rocket_clean_minify();
		}

		if ( function_exists( 'opcache_reset' ) ) {
			// Remove all opcache if enabled
			opcache_reset();
		}

		if ( function_exists( 'apc_clear_cache' ) ) {
			// Remove all apc if enabled
			apc_clear_cache();
		}

		//Clear the fastest cache
		global $wp_fastest_cache;
		if ( isset( $wp_fastest_cache ) && method_exists( $wp_fastest_cache, 'deleteCache' ) ) {
			$wp_fastest_cache->deleteCache();
		}
	}

	public static function isAjax() {
		if ( isset( self::$is_ajax ) ) {
			return self::$is_ajax;
		}

		self::$is_ajax = false;

		if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
			self::$is_ajax = true;
		} else {
			$url = ( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : false );
			if ( $url && ( strpos( $url, str_replace( get_bloginfo( 'url' ), '', admin_url( 'admin-ajax.php', 'relative' ) ) ) !== false ) ) {
				self::$is_ajax = true;
			}
		}

		return self::$is_ajax;
	}

	/**
	 * This hook will save the current version in database
	 *
	 * @return void
	 */
	function hookInit() {

		$this->loadMultilanguage();

		//add setting link in plugin
		add_filter( 'plugin_action_links', array( $this, 'hookActionlink' ), 5, 2 );
	}


	/**
	 * Load the multilanguage support from .mo
	 */
	private function loadMultilanguage() {
		load_plugin_textdomain( _PSP_PLUGIN_NAME_, false, _PSP_PLUGIN_NAME_ . '/languages/' );
	}

	/**
	 * Add a link to settings in the plugin list
	 *
	 * @param array $links
	 * @param string $file
	 *
	 * @return array
	 */
	public function hookActionlink( $links, $file ) {
		if ( $file == _PSP_PLUGIN_NAME_ . '/' . _PSP_PLUGIN_NAME_ . '.php' ) {
			$link = '<a href="' . admin_url( 'admin.php?page=psp_sub_menu_settings' ) . '">' . __( 'Settings', _PSP_PLUGIN_NAME_ ) . '</a>';
			array_unshift( $links, $link );
		}

		return $links;
	}

	public function psp_activate() {
		set_transient( 'psp_activate', true );
		self::saveOptions( 'importList', false );
		self::createTable();
	}

	public static function checkSquirrlyApi() {
		if ( ! self::getOption( 'psp_api' ) ) {
			if ( class_exists( 'SQ_Tools' ) ) {
				$options = SQ_Tools::getOptions();
				if ( $options['sq_api'] <> '' ) {
					self::$options['psp_api'] = $options['sq_api'];
					self::saveOptions();
				}
			}
		}
	}

	public function psp_deactivate() {
		set_transient( 'psp_activate', false );
	}


	/**
	 * Save the Options in user option table in DB
	 *
	 * @param null $key
	 * @param string $value
	 */
	public static function saveOptions( $key = null, $value = '' ) {
		if ( isset( $key ) ) {
			self::$options[ $key ] = $value;
		}

		if ( is_multisite() ) {
			update_blog_option( get_current_blog_id(), _PSP_OPTION_, json_encode( self::$options ) );
		} else {
			update_option( _PSP_OPTION_, json_encode( self::$options ) );
		}
	}

	public static function createTable() {
		global $wpdb, $charset_collate;
		$psp_table_query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . strtolower( _PSP_DB_ ) . ' (
                      `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                      `blog_id` INT(10) NOT NULL,
                      `URL` VARCHAR(255) NOT NULL,
                      `url_hash` VARCHAR(32) NOT NULL,
                      `seo` text NOT NULL,
                      `date_time` DATETIME NOT NULL,
                      PRIMARY KEY(id),
                      UNIQUE url_hash(url_hash) USING BTREE,
                      INDEX blog_id_url_hash(blog_id, url_hash) USING BTREE)  ' . $charset_collate . ';';
		dbDelta( $psp_table_query, true );
	}


}
