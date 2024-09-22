<?php
/**
 * PSP_Models_Services_OpenGraph
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Services_OpenGraph extends PSP_Models_Abstract_Seo {

	public function __construct() {
		parent::__construct();

		if ( $this->_post->psp->doseo ) {
			add_filter( 'locale', array( $this, 'setLocale' ) );
			add_filter( 'psp_html_prefix', array( $this, 'addOGPrefix' ) );

			add_filter( 'psp_open_graph', array( $this, 'generateOpenGraph' ) );
			add_filter( 'psp_open_graph', array( $this, 'packOpenGraph' ), 99 );
		} else {
			add_filter( 'psp_open_graph', array( $this, 'returnFalse' ) );
		}

		if ( $this->_sq_use ) {
			add_filter( 'psp_open_graph', array( $this, 'getSquirrlyImage' ), 9 );
		}

	}

	public function addOGPrefix( $prefix = '' ) {
		$prefix .= 'og: http://ogp.me/ns#';
		if ( ! empty( $this->_post->socials->fb_admins ) || $this->_post->socials->fbadminapp <> '' ) {
			$prefix .= ' fb: http://ogp.me/ns/fb#';
		}

		return $prefix;
	}

	public function generateOpenGraph( $og = array() ) {

		if ( PSP_Classes_Tools::getOption( 'psp_og_opt' ) ) {
			if ( $this->_post->url <> '' ) {
				$og['og:url'] = $this->_post->url;
			}

			if ( $this->_post->psp->og_title <> '' ) {
				$og['og:title'] = $this->clearTitle( $this->_post->psp->og_title );
			} else {
				$og['og:title'] = $this->clearTitle( $this->_post->psp->title );
			}

			if ( $this->_post->psp->og_description <> '' ) {
				$og['og:description'] = $this->clearDescription( $this->_post->psp->og_description );
			} else {
				$og['og:description'] = $this->clearDescription( $this->_post->psp->description );
			}


			if ( $this->_post->psp->og_type <> '' ) {
				$og['og:type'] = $this->_post->psp->og_type;
			} else {
				$og['og:type'] = $this->_getType( $this->_post->post_type );
			}

			if ( ! isset( $og['og:image'] ) ) {
				if ( $this->_post->psp->og_media <> '' ) {
					$og['og:image']       = $this->_post->psp->og_media;
					$og['og:image:width'] = '500';
					if ( $og['og:type'] == 'video' ) {
						$this->_setMedia( $og );
					}
				} else {
					$this->_setMedia( $og );
				}
			}

			$og['og:site_name'] = get_bloginfo( 'title' );
			$og['og:locale']    = get_locale();

			if ( $this->_post->socials->fbadminapp <> '' ) {
				$og['fb:app_id'] = $this->_post->socials->fbadminapp;
			}

			if ( ! empty( $this->_post->socials->fb_admins ) ) {
				foreach ( $this->_post->socials->fb_admins as $admin ) {
					if ( isset( $admin->id ) ) {
						$og['fb:admins'][] = $admin->id;
					}
				}
			}
			if ( $this->_post->post_type == 'post' ) {
				if ( isset( $this->_post->post_date ) && $this->_post->post_date <> '' ) {
					$og['article:published_time'] = $this->_post->post_date;
				}
				if ( isset( $this->_post->post_modified ) && $this->_post->post_modified <> '' ) {
					$og['article:modified_time'] = $this->_post->post_modified;
				}
				if ( isset( $this->_post->category ) && $this->_post->category <> '' ) {
					$og['article:section'] = $this->_post->category;
				} else {
					$category = get_the_category( $this->_post->ID );
					if ( ! empty( $category ) && $category[0]->cat_name <> 'Uncategorized' ) {
						$og['article:section'] = $category[0]->cat_name;
					}
				}

				if ( $this->_post->psp->keywords <> '' ) {
					$keywords = explode( ',', $this->_post->psp->keywords );
				}
				if ( ! empty( $keywords ) ) {
					foreach ( $keywords as $keyword ) {
						$og['article:tags'][] = $keyword;
					}
				}
			} elseif ( $this->_post->post_type == 'profile' && $this->_post->post_author <> '' ) {
				if ( strpos( $this->_post->post_author, " " ) !== false ) {
					$author = explode( " ", $this->_post->post_author );
				} else {
					$author = array( $this->_post->post_author );
				}
				$og['profile:first_name'] = $author[0];
				if ( isset( $author[1] ) ) {
					$og['profile:last_name'] = $author[1];
				}
			} elseif ( $this->_post->post_type === 'product' ) {
				if ( $this->_post->category <> '' ) {
					$og['product:category'] = $this->_post->category;
				}

				global $product;
				if ( $product instanceof WC_Product ) {
					$currency      = 'USD';
					$regular_price = $sale_price = $price = $sales_price_from = $sales_price_to = 0;

					if ( method_exists( $product, 'get_regular_price' ) ) {
						$regular_price = $product->get_regular_price();
					}
					if ( method_exists( $product, 'get_sale_price' ) ) {
						$sale_price = $product->get_sale_price();
						if ( $sale_price > 0 && method_exists( $product, 'get_date_on_sale_to' ) && method_exists( $product, 'get_date_on_sale_from' ) ) {
							if ( $product->get_date_on_sale_from() ) {
								$sales_price_from = gmdate( 'Y-m-d', $product->get_date_on_sale_from()->getTimestamp() );
							}
							if ( $product->get_date_on_sale_to() ) {
								$sales_price_to = gmdate( 'Y-m-d', $product->get_date_on_sale_to()->getTimestamp() );
							}
						}
					}
					if ( method_exists( $product, 'get_price' ) ) {
						$price = $product->get_price();
					}

					if ( function_exists( 'get_woocommerce_currency' ) ) {
						$currency = get_woocommerce_currency();
					}

					if ( $regular_price > 0 && $regular_price <> $price ) {
						$og['product:original_price:amount']   = wc_format_decimal( $regular_price, wc_get_price_decimals() );
						$og['product:original_price:currency'] = $currency;
					}

					if ( $price > 0 ) {
						$og['product:price:amount']   = wc_format_decimal( $price, wc_get_price_decimals() );
						$og['product:price:currency'] = $currency;
					}

					if ( $sale_price > 0 ) {
						$og['product:sale_price:amount']   = wc_format_decimal( $sale_price, wc_get_price_decimals() );
						$og['product:sale_price:currency'] = $currency;

						if ( $sales_price_from ) {
							$og['product:sale_price:start'] = $sales_price_from;
						}
						if ( $sales_price_to ) {
							$og['product:sale_price:end'] = $sales_price_to;
						}

					}


				}
			}

		}

		return $og;
	}

	public function getSquirrlyImage( $og = array() ) {
		return $og;
	}

	protected function _setMedia( &$og ) {
		if ( $og['og:type'] == 'video' ) {
			$videos = $this->getPostVideos();
			if ( ! empty( $videos ) ) {
				$video = current( $videos );
				if ( $video <> '' ) {
					$video = preg_replace( '/(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"\'>\s]+)/si', "https://www.youtube.com/v/$1", $video );

					$og['og:video']        = $video;
					$og['og:video:width']  = '500';
					$og['og:video:height'] = '280';
				}
			}
		} else {
			$images = $this->getPostImages();
			if ( ! empty( $images ) ) {
				$image = current( $images );
				if ( isset( $image['src'] ) ) {
					$og['og:image'] = $image['src'];
					if ( isset( $image['width'] ) ) {
						$og['og:image:width'] = $image['width'];
					}
					if ( isset( $image['height'] ) ) {
						$og['og:image:height'] = $image['height'];
					}
				}
			}
		}
	}

	protected function _getType( $type ) {
		$types = array(
			'home'    => 'website',
			'profile' => 'profile',
			'post'    => 'article',
			'page'    => 'article',
			'book'    => 'book',
			'music'   => 'music',
			'product' => 'product',
			'video'   => 'video'
		);

		if ( in_array( $type, array_keys( $types ) ) ) {
			return $types[ $type ];
		}

		return $types['home'];
	}

	public function setLocale( $locale ) {
		if ( function_exists( 'wpml_get_language_information' ) && (int) $this->_post->ID > 0 ) {
			if ( $language = wpml_get_language_information( (int) $this->_post->ID ) ) {
				if ( isset( $language['locale'] ) ) {
					$locale = $language['locale'];
				}
			}
		}

		return $locale;
	}


	public function packOpenGraph( $og = array() ) {
		if ( ! empty( $og ) ) {
			foreach ( $og as $key => &$value ) {
				if ( is_array( $value ) ) {
					$str = '';
					foreach ( $value as $subvalue ) {
						$str .= '<meta property="' . $key . '" content="' . $subvalue . '" />' . ( ( count( $value ) > 1 ) ? "\n" : '' );
					}
					$value = $str;
				} else {
					$value = '<meta property="' . $key . '" content="' . $value . '" />';
				}
			}

			return "\n" . join( "\n", array_values( $og ) );
		}

		return false;
	}

}