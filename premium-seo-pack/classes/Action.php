<?php
/**
 * PSP_Classes_Action
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * Set the ajax action and call for WordPress
 */
class PSP_Classes_Action extends PSP_Classes_FrontController {

	/** @var array with all form and ajax actions */
	var $actions = array();

	/** @var array from core config */
	private static $config;


	/**
	 * The hookAjax is loaded as custom hook in hookController class
	 *
	 * @return void
	 */
	function hookInit() {
		/* Only if ajax */
		if ( PSP_Classes_Tools::isAjax() ) {
			$this->actions = array();
			$this->getActions();
		} elseif ( ! is_admin() && ! is_network_admin() ) {
			$this->actions = array();
			$this->getActions();
		}
	}

	/**
	 * The hookHead is loaded as admin hook in hookController class for script load
	 * Is needed for security check as nonce
	 *
	 * @return void
	 */
	public function hookHead() {
		echo '<script type="text/javascript">
                  var psp_Query = {
                    "ajaxurl": "' . esc_url(admin_url( 'admin-ajax.php' )) . '",
                    "nonce": "' . esc_attr( wp_create_nonce( _PSP_NONCE_ID_ )) . '"
                  }
              </script>';
	}

	/**
	 * The hookSubmit is loaded when action si posted
	 *
	 * @return void
	 */
	function hookMenu() {
		/* Only if post */
		if ( ! PSP_Classes_Tools::isAjax() ) {
			$this->actions = array();
			$this->getActions();
		}
	}


	/**
	 * Get all actions from config.json in core directory and add them in the WP
	 *
	 * @return void
	 */
	public function getActions() {

		if (!is_admin()) {
			return;
		}

		$cur_action = PSP_Classes_Tools::getValue('action');

		if ( $cur_action <> '' ) {
			/* if config already in cache */
			if ( ! isset( self::$config ) ) {
				$config_file = _PSP_ROOT_DIR_ . '/config.json';
				if ( ! file_exists( $config_file ) ) {
					return;
				}

				/* load configuration blocks data from core config files */
				self::$config = json_decode( file_get_contents( $config_file ), 1 );
			}

			if ( is_array( self::$config ) ) {
				foreach ( self::$config['blocks']['block'] as $block ) {
					if ( isset( $block['active'] ) && $block['active'] == 1 ) {
						/* if there is a single action */
						if ( isset( $block['actions']['action'] ) ) /* if there are more actions for the current block */ {
							if ( ! is_array( $block['actions']['action'] ) ) {
								/* add the action in the actions array */
								if ( $block['actions']['action'] == $cur_action ) {
									$this->actions[] = array( 'class' => $block['name'] );
								}
							} else {
								/* if there are more actions for the current block */
								foreach ( $block['actions']['action'] as $action ) {
									/* add the actions in the actions array */
									if ( $action == $cur_action ) {
										$this->actions[] = array( 'class' => $block['name'] );
									}
								}
							}
						}
					}
				}
			}

			/* add the actions in WP */
			foreach ( $this->actions as $actions ) {
				PSP_Classes_ObjController::getClass( $actions['class'] )->action();
			}
		}
	}

	/**
	 * Call the Squirrly Api Server
	 *
	 * @param string $module
	 * @param array $args
	 *
	 * @return false|string
	 */
	public static function apiCall( $module, $args = array(), $timeout = 90 ) {

		if ( PSP_Classes_Tools::$options['psp_api'] == '' && $module <> 'sq/login' && $module <> 'sq/register' ) {
			return false;
		}

		$extra = array(
			'user_url' => get_bloginfo('url'),
			'plugin'   => _PSP_PLUGIN_NAME_,
			'token'    => PSP_Classes_Tools::getOption( 'psp_api' )
		);

		if ( is_array( $args ) ) {
			$args = array_merge( $args, $extra );
		} else {
			$args = $extra;
		}

		$url = _PSP_API_URL_ . ( $module <> "" ? $module . "/" : "" );

		return PSP_Classes_Tools::psp_remote_get( $url, $args, array( 'timeout' => $timeout ) );
	}

}
