<?php
/**
 * PSP_Models_Services_Noindex
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Services_Noindex extends PSP_Models_Abstract_Seo {

	public function __construct() {
		parent::__construct();
		if ( $this->_post->psp->doseo ) {
			add_filter( 'psp_noindex', array( $this, 'generateNoindex' ) );
			add_filter( 'psp_noindex', array( $this, 'packNoindex' ), 99 );
		} else {
			add_filter( 'psp_noindex', array( $this, 'returnFalse' ) );
		}
	}

	public function generateNoindex( $robots = array() ) {

		if ( PSP_Classes_Tools::$options['psp_noindex_opt'] == 1 ) {

			if ( (int) $this->_post->psp->noindex == 1 ) {
				$robots[] = 'noindex';
			}
			if ( (int) $this->_post->psp->nofollow == 1 ) {
				$robots[] = 'nofollow';
			} elseif ( ! empty( $robots ) ) {
				$robots[] = 'follow';
			}
		}

		return $robots;
	}

	public function packNoindex( $robots = array() ) {
		if ( ! empty( $robots ) ) {
			return sprintf( "<meta name=\"robots\" content=\"%s\">", join( ',', $robots ) );
		}

		return false;
	}

}
