<?php
/**
 * PSP_Models_Domain_Post
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Domain_Post extends PSP_Models_Abstract_Domain {

	protected $_ID;
	protected $_post_type;
	protected $_url; //set the canonical link for this post type
	protected $_hash;
	protected $_psp;
	protected $_psp_adm;
	protected $_socials;
	protected $_patterns;
	//
	protected $_post_name;
	protected $_guid;
	protected $_post_author;
	protected $_post_date;
	protected $_post_title;
	protected $_post_excerpt;
	protected $_post_content;
	protected $_post_status;
	protected $_post_password;

	protected $_post_parent;
	protected $_post_modified;
	protected $_category;
	protected $_category_description;
	protected $_noindex;

	public function getSocials() {
		if ( ! isset( $this->_socials ) ) {
			$this->_socials = json_decode( json_encode( PSP_Classes_Tools::getOption( 'socials' ) ) );
		}

		return $this->_socials;
	}

	public function getPsp() {
		if ( ! isset( $this->_psp ) && isset( $this->_post_type ) && $this->_post_type <> '' ) {
			//Get the saved psp settings
			$this->_psp = PSP_Classes_ObjController::getClass( 'PSP_Models_Frontend' )->getPspSeo( $this->_hash );
			if ( ! empty( $this->_psp ) ) {
				$patterns = PSP_Classes_Tools::getOption( 'patterns' );
				//print_R($this);
				if ( ! empty( $patterns ) && $psp_array = $this->_psp->toArray() ) {
					if ( ! empty( $psp_array ) ) {
						foreach ( $psp_array as $key => $value ) {
							if ( $value == '' ) {
								if ( isset( $patterns[ $this->_post_type ] ) ) {
									if ( isset( $patterns[ $this->_post_type ][ $key ] ) ) {
										$this->_psp->$key = $patterns[ $this->_post_type ][ $key ];
										if ( isset( $patterns[ $this->_post_type ]['sep'] ) ) {
											$this->_psp->sep = $patterns[ $this->_post_type ]['sep'];
										}
										if ( isset( $patterns[ $this->_post_type ]['noindex'] ) ) {
											$this->_psp->noindex = $patterns[ $this->_post_type ]['noindex'];
										}
										if ( isset( $patterns[ $this->_post_type ]['nofollow'] ) ) {
											$this->_psp->nofollow = $patterns[ $this->_post_type ]['nofollow'];
										}
									}
								} else {
									if ( isset( $patterns['custom'][ $key ] ) ) {
										$this->_psp->$key = $patterns['custom'][ $key ];
										if ( isset( $patterns['custom']['sep'] ) ) {
											$this->_psp->sep = $patterns['custom']['sep'];
										}
										if ( isset( $patterns['custom']['noindex'] ) ) {
											$this->_psp->noindex = $patterns['custom']['noindex'];
										}
										if ( isset( $patterns['custom']['nofollow'] ) ) {
											$this->_psp->nofollow = $patterns['custom']['nofollow'];
										}
									}
								}
							}
						}
					}
				}
			}


		}

		return $this->_psp;
	}

	public function getPsp_adm() {

		if ( ! isset( $this->_psp_adm ) && isset( $this->_post_type ) && $this->_post_type <> '' ) {
			if ( is_user_logged_in() ) {
				$this->_psp_adm = PSP_Classes_ObjController::getClass( 'PSP_Models_Frontend' )->getPspSeo( $this->_hash );

				if ( ! empty( $this->_psp_adm ) ) {
					$patterns = PSP_Classes_Tools::getOption( 'patterns' );
					//print_R($this);
					if ( ! empty( $patterns ) && $psp_array = $this->_psp_adm->toArray() ) {
						if ( ! empty( $psp_array ) ) {
							foreach ( $psp_array as $key => $value ) {
								if ( $value == '' ) {
									if ( isset( $patterns[ $this->_post_type ] ) ) {
										$this->_psp_adm->patterns = json_decode( json_encode( $patterns[ $this->_post_type ] ) );
									} else {
										$this->_psp_adm->patterns = json_decode( json_encode( $patterns['custom'] ) );
									}
								}
							}
						}
					}
				}
			}
		}

		return $this->_psp_adm;
	}

	public function getID() {
		return $this->_ID;
	}


	public function importSEO() {
		$import    = array();

		if ( isset( $this->_ID ) ) {
			$platforms = apply_filters( 'psp_importList', false );

			if ( ! empty( $platforms ) ) {
				foreach ( $platforms as $path => &$metas ) {
					if ( $metas = PSP_Classes_ObjController::getClass( 'PSP_Models_Admin' )->getDBSeo( $this->_ID, $metas ) ) {
						if ( strpos( $metas, '%%' ) !== false ) {
							$metas = preg_replace( '/%%([^\%]+)%%/', '{{$1}}', $metas );
						}
						$import[ PSP_Classes_ObjController::getClass( 'PSP_Models_Admin' )->getName( $path ) ] = $metas;
					}
				}
			}

		}

		return $import;
	}

}
