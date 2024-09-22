<?php
/**
 * PSP_Classes_Error
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Classes_Error extends PSP_Classes_FrontController {

	/** @var array */
	private static $errors = array();

	/**
	 * Show version error
	 */
	public function phpVersionError() {
		echo '<div class="update-nag"><span style="color:red; font-weight:bold;">' . esc_html__( 'For Post Plugins to work, the PHP version has to be equal or greater than 5.1', _PSP_PLUGIN_NAME_ ) . '</span></div>';
	}

	/**
	 * Show the error in WordPress
	 *
	 * @param string $error
	 * @param string $type
	 * @param integer $index
	 *
	 * @return void;
	 */
	public static function setError( $error = '', $type = 'notice', $index = null ) {
		if ( ! isset( $index ) ) {
			$index = count( self::$errors );
		}

		self::$errors[ $index ] = array(
			'type' => $type,
			'text' => $error
		);
	}

	public static function setMessage( $message = '' ) {
		self::$errors[] = array(
			'type' => 'success',
			'text' => $message
		);
	}

	/**
	 * This hook will show the error in WP header
	 */
	public function hookNotices() {
		if ( is_array( self::$errors ) ) {
			foreach ( self::$errors as $error ) {
				self::showError( $error['text'], $error['type'] );
			}
		}
		self::$errors = array();
	}

	/**
	 * Show the notices to WP
	 *
	 * @param string $message
	 * @param string $type Error type
	 */
	public static function showError( $message, $type = '' ) {
		if ( file_exists( _PSP_THEME_DIR_ . 'Notices.php' ) ) {
			include( _PSP_THEME_DIR_ . 'Notices.php' );
		} else {
			echo wp_kses_post($message);
		}
	}

}
