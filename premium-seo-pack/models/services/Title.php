<?php
/**
 * PSP_Models_Services_Title
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Services_Title extends PSP_Models_Abstract_Seo {


	public function __construct() {
		parent::__construct();

		if ( $this->_post->psp->doseo ) {
			add_filter( 'psp_title', array( $this, 'generateTitle' ) );
			add_filter( 'psp_title', array( $this, 'clearTitle' ), 98 );
			add_filter( 'psp_title', array( $this, 'packTitle' ), 99 );
		} else {
			add_filter( 'psp_title', array( $this, 'returnFalse' ) );
		}

	}

	public function generateTitle( $title = '' ) {

		if ( $this->_post->psp->title <> '' ) {
			$title = $this->_post->psp->title;
		} else {
			$title = $this->_post->post_title = get_the_title();
		}

		return $title;
	}

	public function packTitle( $title = '' ) {
		if ( $title <> '' ) {
			return sprintf( "<title>%s</title>", $title );
		}

		return false;
	}

}
