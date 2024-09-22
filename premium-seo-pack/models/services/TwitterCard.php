<?php
/**
 * PSP_Models_Services_TwitterCard
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Services_TwitterCard extends PSP_Models_Abstract_Seo {

	public function __construct() {
		parent::__construct();

		if ( $this->_post->psp->doseo ) {
			add_filter( 'psp_twitter_card', array( $this, 'generateTwitterCard' ), 10 );
			add_filter( 'psp_twitter_card', array( $this, 'packTwitterCard' ), 99 );
		} else {
			add_filter( 'psp_twitter_card', array( $this, 'returnFalse' ) );
		}

	}

	public function generateTwitterCard( $tw = array() ) {

		if ( PSP_Classes_Tools::getOption( 'psp_tw_opt' ) ) {
			if ( $this->_post->url <> '' ) {
				$tw['twitter:url'] = $this->_post->url;
			}

			if ( $this->_post->psp->tw_title <> '' ) {
				$tw['twitter:title'] = $this->clearTitle( $this->_post->psp->tw_title );
			} else {
				$tw['twitter:title'] = $this->clearTitle( $this->_post->psp->title );
			}

			if ( $this->_post->psp->tw_description <> '' ) {
				$tw['twitter:description'] = $this->clearDescription( $this->_post->psp->tw_description );
			} else {
				$tw['twitter:description'] = $this->clearDescription( $this->_post->psp->description );
			}


			if ( $this->_post->psp->tw_media <> '' ) {
				$tw['twitter:image'] = $this->_post->psp->tw_media;
			} else {
				$this->_setMedia( $tw );
			}

			if ( $account = $this->_getTwitterAccount() ) {
				$tw['twitter:creator'] = $account;
				$tw['twitter:site']    = $account;
			}

			$tw['twitter:domain'] = get_bloginfo( 'title' );
			$tw['twitter:card']   = $this->_post->socials->twitter_card_type;

		}

		return $tw;
	}

	protected function _setMedia( &$tw ) {
		$images = $this->getPostImages();
		if ( ! empty( $images ) ) {
			$image = current( $images );
			if ( isset( $image['src'] ) ) {
				$tw['twitter:image'] = $image['src'];
			}
		}
	}

	protected function _getTwitterAccount() {
		$account = PSP_Classes_Tools::getOption( 'psp_tw_account' );
		if ( $account == '' ) {
			if ( class_exists( 'SQ_Tools' ) ) {
				$account = SQ_Tools::$options['sq_twitter_account'];
			}
		}

		if ( $account <> '' ) {
			if ( strpos( $account, 'twitter.com' ) !== false ) {
				preg_match( '/twitter.com\/([@1-9a-z_-]+)/i', $account, $result );
				if ( ! empty( $result[1] ) ) {
					return '@' . str_replace( '@', '', $result[1] );
				}
			} else {
				preg_match( '/([@1-9a-z_-]+)/i', $account, $result );
				if ( ! empty( $result[1] ) ) {
					return '@' . str_replace( '@', '', $result[1] );
				}
			}
		} else {
			return '';
		}

		return false;
	}


	public function packTwitterCard( $tw = array() ) {
		if ( ! empty( $tw ) ) {
			foreach ( $tw as $key => &$value ) {
				$value = '<meta property="' . $key . '" content="' . $value . '" />';
			}

			return "\n" . join( "\n", array_values( $tw ) );
		}

		return false;
	}

}
