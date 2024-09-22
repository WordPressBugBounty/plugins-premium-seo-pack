<?php
/**
 * PSP_Classes_DisplayController
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * The class handles the theme part in WP
 */
class PSP_Classes_DisplayController {

	private static $cache;

	/**
	 * echo the css link from theme css directory
	 *
	 * @param string $uri The name of the css file or the entire uri path of the css file
	 * @param string $media
	 *
	 * @return void
	 */
	public static function loadMedia( $uri = '', $params = array( 'pass-ajax' => false ), $media = 'all' ) {
		$css_uri = '';
		$js_uri  = '';

		if ( ! isset( $params['pass-ajax'] ) || $params['pass-ajax'] === false ) {
			if ( PSP_Classes_Tools::isAjax() ) {
				return;
			}
		}

		if ( isset( self::$cache[ $uri ] ) ) {
			return;
		}

		self::$cache[ $uri ] = true;

		/* if is a custom css file */
		if ( strpos( $uri, '//' ) === false ) {
			if ( strpos( $uri, '.' ) !== false ) {
				$name = strtolower( _PSP_NAMESPACE_ . substr( $uri, 0, strpos( $uri, '.' ) ) );
			} else {
				$name = strtolower( _PSP_NAMESPACE_ . $uri );
			}
			if ( file_exists( _PSP_THEME_DIR_ . 'css/' . strtolower( $uri ) ) ) {
				$css_uri = _PSP_THEME_URL_ . 'css/' . strtolower( $uri );
			}
			if ( file_exists( _PSP_THEME_DIR_ . 'js/' . strtolower( $uri ) ) ) {
				$js_uri = _PSP_THEME_URL_ . 'js/' . strtolower( $uri );
			}

			if ( file_exists( _PSP_THEME_DIR_ . 'css/' . strtolower( $uri ) . '.css' ) ) {
				$css_uri = _PSP_THEME_URL_ . 'css/' . strtolower( $uri ) . '.css';
			}
			if ( file_exists( _PSP_THEME_DIR_ . 'js/' . strtolower( $uri ) . '.js' ) ) {
				$js_uri = _PSP_THEME_URL_ . 'js/' . strtolower( $uri ) . '.js';
			}
		} else {
			$name = strtolower( basename( $uri ) );
			if ( strpos( $uri, '.css' ) !== false ) {
				$css_uri = $uri;
			} elseif ( strpos( $uri, '.js' ) !== false ) {
				$js_uri = $uri;
			}
		}

		if ( $css_uri <> '' ) {

			if ( ! wp_style_is( $name ) ) {
				wp_enqueue_style( $name, $css_uri, null, PSP_VERSION, $media );
			}
		}

		if ( $js_uri <> '' ) {
			if ( ! wp_script_is( $name ) ) {
				wp_enqueue_script( $name, $js_uri, array( 'jquery' ), PSP_VERSION, true );
			}
		}
	}

	/**
	 * return the block content from theme directory
	 *
	 * @return string
	 */
	public function getView( $block, $view ) {
		$output = null;

		if ( file_exists( _PSP_THEME_DIR_ . $block . '.php' ) ) {
			ob_start();
			include( _PSP_THEME_DIR_ . $block . '.php' );
			$output .= ob_get_clean();
		}

		return $output;
	}

}
