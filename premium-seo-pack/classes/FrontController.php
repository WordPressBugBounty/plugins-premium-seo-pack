<?php
/**
 * PSP_Classes_FrontController
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * The main class for controllers
 *
 */
class PSP_Classes_FrontController {

	/** @var object of the model class */
	public $model;

	/** @var object of the view class */
	public $view;

	/** @var string name of the  class */
	protected $name;

	public function __construct() {
		/* Load error class */
		PSP_Classes_ObjController::getClass( 'PSP_Classes_Error' );

		/* Load Tools */
		PSP_Classes_ObjController::getClass( 'PSP_Classes_Tools' );

		/* get the name of the current class */
		$this->name = get_class( $this );

		/* load the model and hooks here for WordPress actions to take effect */
		/* create the model and view instances */
		$this->model = PSP_Classes_ObjController::getClass( str_replace( 'Controllers', 'Models', $this->name ) );

		//IMPORTANT TO LOAD HOOKS HERE
		/* check if there is a hook defined in the controller clients class */
		PSP_Classes_ObjController::getClass( 'PSP_Classes_HookController' )->setHooks( $this );


		/* Load the Main classes Actions Handler */
		PSP_Classes_ObjController::getClass( 'PSP_Classes_Action' );
		PSP_Classes_ObjController::getClass( 'PSP_Classes_DisplayController' );

		//abstract classes
		PSP_Classes_ObjController::getClass( 'PSP_Models_Abstract_Domain' );
		PSP_Classes_ObjController::getClass( 'PSP_Models_Abstract_Models' );
		PSP_Classes_ObjController::getClass( 'PSP_Models_Abstract_Seo' );

	}

	/**
	 * load sequence of classes
	 * Function called usually when the controller is loaded in WP
	 *
	 * @return PSP_Classes_FrontController
	 */
	public function init() {
		return $this;
	}

	/**
	 * Get the block view
	 *
	 * @param null $view
	 *
	 * @return mixed
	 */
	public function getView( $view = null ) {
		if ( ! isset( $view ) ) {
			if ( $class = PSP_Classes_ObjController::getClassPath( $this->name ) ) {
				$view = $class['name'];
			}
		}

		if ( isset( $view ) ) {
			$this->view = PSP_Classes_ObjController::getClass( 'PSP_Classes_DisplayController' );

			return $this->view->getView( $view, $this );
		}

		return '';
	}

	/**
	 * Called as menu callback to show the block
	 *
	 */
	public function show() {
		echo $this->init()->getView();
	}

	/**
	 * first function call for any class
	 *
	 */
	protected function action() {
		// generated nonce we created
		if ( function_exists( 'wp_verify_nonce' ) ) {
			if ( ! wp_verify_nonce( PSP_Classes_Tools::getValue( 'psp_nonce' ), _PSP_NONCE_ID_ ) ) {
				die( 'Invalid request!' );
			}
		}
	}

	/**
	 * initialize settings
	 * Called from index
	 *
	 * @return void
	 */
	public function runAdmin() {
		/* show the admin menu and post actions */
		PSP_Classes_ObjController::getClass( 'PSP_Controllers_Admin' );
	}

	public function runFrontend() {
		PSP_Classes_ObjController::getClass( 'PSP_Controllers_Admin' );

		/* load the frontend  */
		PSP_Classes_ObjController::getClass( 'PSP_Controllers_Frontend' )->frontInit();

	}


	/**
	 * check the user to be active in order to access the admin panel
	 *
	 * Called by WordPress on Admin Init
	 */
	public function hookInit() {

	}

	/**
	 * Hook the admin head
	 * This function will load the media in the header for each class
	 *
	 * @return void
	 */
	public function hookHead() {

	}

}
