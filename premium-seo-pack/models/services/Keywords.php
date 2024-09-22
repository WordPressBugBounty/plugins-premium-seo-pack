<?php
/**
 * PSP_Models_Services_Keywords
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Services_Keywords extends PSP_Models_Abstract_Seo {


	public function __construct() {
		parent::__construct();
		if ( $this->_post->psp->doseo ) {
			add_filter( 'psp_keywords', array( $this, 'generateKeywords' ) );
			add_filter( 'psp_keywords', array( $this, 'packKeywords' ), 99 );
		} else {
			add_filter( 'psp_keywords', array( $this, 'returnFalse' ) );
		}

		if ( $this->_sq_use ) {
			add_filter( 'psp_keywords', array( $this, 'generateSquirrlyKeywords' ), 11 );
		}
	}

	public function generateKeywords( $keywords = '' ) {

		if ( $this->_post->psp->keywords <> '' ) {
			$keywords = $this->_post->psp->keywords;
		}

		return $keywords;
	}

	public function generateSquirrlyKeywords( $keywords = '' ) {
		return $keywords;
	}

	public function packKeywords( $keywords = '' ) {
		if ( $keywords <> '' ) {
			return sprintf( "<meta name=\"keywords\" content=\"%s\" />", $keywords );
		}

		return '';
	}
}
