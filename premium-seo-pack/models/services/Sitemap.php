<?php
/**
 * PSP_Models_Services_Sitemap
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Services_Sitemap extends PSP_Models_Abstract_Seo {

	public function __construct() {
		parent::__construct();

		if ( $this->_post->psp->doseo ) {
			add_filter( 'psp_sitemap', array( $this, 'generateMeta' ) );
			add_filter( 'psp_sitemap', array( $this, 'packMeta' ), 99 );
		} else {
			add_filter( 'psp_sitemap', array( $this, 'returnFalse' ) );
		}

	}

	public function generateMeta() {
		return $this->_getSitemapURL();
	}

	/**
	 * Get the url for each sitemap
	 *
	 * @param string $sitemap
	 *
	 * @return string
	 */
	protected function _getSitemapURL( $sitemap = 'sitemap' ) {
		if ( class_exists( 'SQ_Classes_ObjController' ) ) {
			return SQ_Classes_ObjController::getClass('SQ_Controllers_Sitemaps')->getXmlUrl( $sitemap );
		}

		return false;
	}


	public function packMeta( $meta = '' ) {
		if ( $meta <> '' ) {
			return '<link rel="alternate" type="application/rss+xml" href="' . $meta . '" />';
		}

		return false;
	}

}
