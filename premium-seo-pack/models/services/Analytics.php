<?php
/**
 * PSP_Models_Services_Analytics
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Services_Analytics extends PSP_Models_Abstract_Seo {


	public function __construct() {
		parent::__construct();
		if ( $this->_post->psp->doseo ) {
			add_filter( 'psp_google_analytics', array( $this, 'generateGoogleAnalytics' ) );
			add_filter( 'psp_google_analytics', array( $this, 'packGoogleAnalytics' ), 99 );
		} else {
			add_filter( 'psp_google_analytics', array( $this, 'returnFalse' ) );
		}
	}

	public function generateGoogleAnalytics( $track = '' ) {
		$codes = json_decode( json_encode( PSP_Classes_Tools::getOption( 'codes' ) ) );

		if ( isset( $codes->google_analytics ) && $codes->google_analytics <> '' ) {
			PSP_Classes_ObjController::getClass( 'PSP_Classes_DisplayController' )->loadMedia( 'https://www.google-analytics.com/analytics.js' );
			$track = $codes->google_analytics;
		}

		return $track;
	}

	public function packGoogleAnalytics( $track = '' ) {
		if ( $track <> '' ) {
			return sprintf( "<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) })(window,document,'script','//www.google-analytics.com/analytics.js','ga'); ga('create', '%s', 'auto');ga('send', 'pageview');</script>", $track );
		}

		return false;
	}
}