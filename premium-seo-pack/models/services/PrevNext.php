<?php
/**
 * PSP_Models_Services_PrevNext
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Services_PrevNext extends PSP_Models_Abstract_Seo {

	public function __construct() {
		parent::__construct();

		if ( $this->_post->psp->doseo ) {
			add_filter( 'psp_prevnext', array( $this, 'generateMeta' ) );
			add_filter( 'psp_prevnext', array( $this, 'packMeta' ), 99 );
		} else {
			add_filter( 'psp_prevnext', array( $this, 'returnFalse' ) );
		}

	}

	public function generateMeta( $meta = "" ) {
		global $paged;

		if ( ! $this->isHomePage() ) {
			if ( get_previous_posts_link() ) {
				$meta['prev'] = get_pagenum_link( $paged - 1 );
			}
			if ( get_next_posts_link() ) {
				$meta['next'] = get_pagenum_link( $paged + 1 );
			}
		}

		return $meta;
	}

	public function packMeta( $metas = array() ) {
		if ( ! empty( $metas ) ) {
			foreach ( $metas as $key => &$value ) {
				$value = '<link rel="' . $key . '" href="' . $value . '" />';
			}

			return "\n" . join( "\n", array_values( $metas ) );
		}

		return false;
	}

}
