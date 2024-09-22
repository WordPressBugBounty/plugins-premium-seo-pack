<?php
/**
 * PSP_Models_Services_Canonical
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Services_Canonical extends PSP_Models_Abstract_Seo {


	public function __construct() {
		parent::__construct();
		if ( $this->_post->psp->doseo ) {
			add_filter( 'psp_canonical', array( $this, 'generateCanonical' ) );
			add_filter( 'psp_canonical', array( $this, 'packCanonical' ), 99 );
		} else {
			add_filter( 'psp_canonical', array( $this, 'returnFalse' ) );
		}
	}

	public function generateCanonical( $canonical = '' ) {
		if ( PSP_Classes_Tools::getOption( 'psp_canonical_opt' ) && isset( $this->_post->psp->canonical ) && $this->_post->psp->canonical <> '' ) {
			$canonical = $this->_post->psp->canonical;
		} else {
			$canonical = $this->_post->url;
		}

		return $canonical;
	}

	public function packCanonical( $canonical = '' ) {
		if ( $canonical <> '' ) {
			return sprintf( "<link rel=\"canonical\" href=\"%s\" />", $canonical );
		}

		return false;
	}
}
