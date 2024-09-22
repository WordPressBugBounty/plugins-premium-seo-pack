<?php
/**
 * PSP_Models_Services_Description
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Services_Description extends PSP_Models_Abstract_Seo {


	public function __construct() {
		parent::__construct();

		if ( $this->_post->psp->doseo ) {
			add_filter( 'psp_description', array( $this, 'generateDescription' ) );
			add_filter( 'psp_description', array( $this, 'clearDescription' ), 98 );
			add_filter( 'psp_description', array( $this, 'packDescription' ), 99 );
		} else {
			add_filter( 'psp_description', array( $this, 'returnFalse' ) );
		}

	}

	public function generateDescription( $description = '' ) {

		if ( $this->_post->psp->description <> '' ) {
			$description = $this->_post->psp->description;
		}

		return $description;
	}


	public function packDescription( $description ) {
		if ( $description <> '' ) {
			return sprintf( "<meta name=\"description\" content=\"%s\" />", $description );
		}

		return false;
	}
}
