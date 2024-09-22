<?php
/**
 * PSP_Controllers_Frontend
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Controllers_Frontend extends PSP_Classes_FrontController {

	public function frontInit() {
		//If not array
		if ( ! PSP_Classes_Tools::isAjax() ) {
			//HOOK THE BUFFER
			if ( ! defined( 'CE_FILE' ) ) {
				add_action( 'plugins_loaded', array( $this->model, 'startBuffer' ), 9 );
			}
			add_action( 'template_redirect', array( $this->model, 'startBuffer' ), 5 );
			add_action( 'shutdown', array( $this->model, 'getBuffer' ), 99 );

			//SET THE POST FROM THE BEGINNING
			if ( defined( 'BP_REQUIRED_PHP_VERSION' ) ) {
				add_action( 'template_redirect', array( $this->model, 'setPost' ), 10 );
			} else {
				add_action( 'template_redirect', array( $this->model, 'setPost' ), 9 );
			}

		}
	}
}
