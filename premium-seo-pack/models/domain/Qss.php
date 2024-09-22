<?php
/**
 * PSP_Models_Domain_Qss
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Domain_Qss extends PSP_Models_Abstract_Domain {

	protected $_doseo;
	//
	protected $_title;
	protected $_description;
	protected $_keywords;
	protected $_canonical;
	protected $_noindex;
	protected $_nofollow;
	protected $_robots;
	protected $_cornerstone;
	//
	protected $_tw_media;
	protected $_tw_title;
	protected $_tw_description;
	protected $_tw_type;
	//
	protected $_og_title;
	protected $_og_description;
	protected $_og_author;
	protected $_og_type;
	protected $_og_media;

	//    protected $_jsonld_title;
	//    protected $_jsonld_description;
	//    protected $_jsonld_type;
	//    protected $_jsonld_media;

	// lengths
	protected $_title_maxlength;
	protected $_description_maxlength;
	protected $_og_title_maxlength;
	protected $_og_description_maxlength;
	protected $_tw_title_maxlength;
	protected $_tw_description_maxlength;
	protected $_jsonld_title_maxlength;
	protected $_jsonld_description_maxlength;

	// for psp_adm patterns
	protected $_patterns;
	//get custom post type separator
	protected $_sep;

	public function getTitle_maxlength() {
		$metas = PSP_Classes_Tools::getOption( 'psp_metas' );

		return $metas['title_maxlength'];
	}

	public function getDescription_maxlength() {
		$metas = PSP_Classes_Tools::getOption( 'psp_metas' );

		return $metas['description_maxlength'];
	}

	public function getOg_title_maxlength() {
		$metas = PSP_Classes_Tools::getOption( 'psp_metas' );

		return $metas['og_title_maxlength'];
	}

	public function getOg_description_maxlength() {
		$metas = PSP_Classes_Tools::getOption( 'psp_metas' );

		return $metas['og_description_maxlength'];
	}

	public function getTw_title_maxlength() {
		$metas = PSP_Classes_Tools::getOption( 'psp_metas' );

		return $metas['tw_title_maxlength'];
	}

	public function getTw_description_maxlength() {
		$metas = PSP_Classes_Tools::getOption( 'psp_metas' );

		return $metas['tw_description_maxlength'];
	}

	public function getJsonld_title_maxlength() {
		$metas = PSP_Classes_Tools::getOption( 'psp_metas' );

		return $metas['jsonld_title_maxlength'];
	}

	public function getJsonld_description_maxlength() {
		$metas = PSP_Classes_Tools::getOption( 'psp_metas' );

		return $metas['jsonld_description_maxlength'];
	}

	public function getDoseo() {
		if ( ! isset( $this->_doseo ) ) {
			$this->_doseo = 1;
		}

		return (int) $this->_doseo;
	}

	public function getNoindex() {
		if ( ! isset( $this->_noindex ) ) {
			$this->_noindex = 0;
		}

		return (int) $this->_noindex;
	}

	public function getNofollow() {
		if ( ! isset( $this->_nofollow ) ) {
			$this->_nofollow = 0;
		}

		return (int) $this->_nofollow;
	}

	public function getSep() {
		return array(
			'sc-dash'   => '-',
			'sc-ndash'  => '&ndash;',
			'sc-mdash'  => '&mdash;',
			'sc-middot' => '&middot;',
			'sc-bull'   => '&bull;',
			'sc-star'   => '*',
			'sc-smstar' => '&#8902;',
			'sc-pipe'   => '|',
			'sc-tilde'  => '~',
			'sc-laquo'  => '&laquo;',
			'sc-raquo'  => '&raquo;',
			'sc-lt'     => '&lt;',
			'sc-gt'     => '&gt;',
		);
	}

}
