<?php
/**
 * PSP_Models_Services_Pixel
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Services_Pixel extends PSP_Models_Abstract_Seo {


	public function __construct() {
		parent::__construct();
		if ( $this->_post->psp->doseo ) {
			add_filter( 'psp_facebook_pixel', array( $this, 'generatePixel' ) );
			add_filter( 'psp_facebook_pixel', array( $this, 'packPixel' ), 99 );
		} else {
			add_filter( 'psp_facebook_pixel', array( $this, 'returnFalse' ) );
		}
	}

	public function addOGPrefix( $prefix = '' ) {
		$prefix .= 'og: http://ogp.me/ns#';

		return $prefix;
	}

	public function generatePixel( $track = '' ) {
		$codes = json_decode( json_encode( PSP_Classes_Tools::getOption( 'codes' ) ) );

		if ( isset( $codes->facebook_pixel ) && $codes->facebook_pixel <> '' ) {
			$domain = str_replace( array( 'http://', 'http://', 'www.' ), '', get_bloginfo( 'url' ) );

			if ( $this->isHomePage() ) {
				$events[] = array(
					'type'   => 'track',
					'name'   => 'PageView',
					'params' => array( 'page' => get_bloginfo( 'url' ), 'domain' => $domain )
				);
			} else {
				if ( isset( $this->_post->ID ) ) {
					$params['content_ids'] = array( (string) $this->_post->ID );
				}

				$params['content_type'] = $this->_post->post_type;

				if ( $this->_post->post_type == 'category' ) {
					$category = get_category( get_query_var( 'cat' ), false );
					if ( isset( $category->name ) ) {
						$params['content_category'] = $category->name;
					}
				} elseif ( $this->_post->post_type == 'product' ) {
					$params['content_name'] = $this->_post->post_title;
					$cat                    = get_the_terms( $this->_post->ID, 'product_cat' );
					if ( ! empty( $cat ) ) {
						$params['content_category'] = $cat[0]->name;
					}

					if ( isset( $_POST['product_id'] ) && isset( $params['content_ids'] ) && isset( $params['content_type'] ) ) {
						if ( function_exists( 'wc_get_product' ) && function_exists( 'get_woocommerce_currency' ) ) {
							if ( $product = wc_get_product( (int) $_POST['product_id'] ) ) {
								$params['value']    = $product->get_price();
								$params['currency'] = get_woocommerce_currency();
							}
						}

						$events[] = array(
							'type'   => 'track',
							'name'   => 'AddToCart',
							'params' => $params
						);
					}
				} elseif ( $this->_post->post_type == 'search' ) {
					$search = get_search_query( true );
					if ( $search <> '' ) {
						$params['search_string'] = $search;
						$events[]                = array(
							'type'   => 'track',
							'name'   => 'Search',
							'params' => $params
						);
					}
				} elseif ( $this->_post->post_type == 'checkout' && isset( $this->_post->ID ) ) {
					global $woocommerce;
					if ( isset( $woocommerce->cart->total ) && $woocommerce->cart->total > 0 ) {
						$params['value'] = $woocommerce->cart->total;

						if ( isset( $woocommerce->cart->cart_contents ) && ! empty( $woocommerce->cart->cart_contents ) ) {
							$quantity = 0;
							foreach ( $woocommerce->cart->cart_contents as $product ) {
								$quantity += $product['quantity'];
							}
							if ( $quantity > 0 ) {
								$params['num_items'] = $quantity;
							}
						}
						$events[] = array(
							'type'   => 'track',
							'name'   => 'InitiateCheckout',
							'params' => $params
						);
					} elseif ( PSP_Classes_Tools::getIsset( 'key' ) ) {
						$params['content_type'] = 'purchase';
						global $wpdb;
						$sql = "SELECT `post_id`
                                FROM `" . $wpdb->postmeta . "`
                                WHERE `meta_key` = '_order_key' AND `meta_value`='" . PSP_Classes_Tools::getValue( 'key' ) . "'";

						if ( $post = $wpdb->get_row( $sql ) ) {
							if(function_exists('wc_get_order')){
								if ( $order = wc_get_order( $post->post_id ) ) {
									$params['content_type'] = "checkout";
									$params['value']        = $order->get_total();
									$params['currency']     = $order->get_order_currency();

									$events[] = array(
										'type'   => 'track',
										'name'   => 'Purchase',
										'params' => $params
									);
								}
							}
						}
					}


				} else {
					$cat = get_the_terms( $this->_post->ID, 'category' );
					if ( ! empty( $cat ) ) {
						$params['content_category'] = $cat[0]->name;
					}
				}

				$params['page']   = $this->_post->url;
				$params['domain'] = $domain;

				if ( isset( $params['content_ids'] ) && isset( $params['content_type'] ) ) {
					$events[] = array(
						'type'   => 'track',
						'name'   => 'ViewContent',
						'params' => $params
					);
				} else {
					$events[] = array(
						'type'   => 'trackCustom',
						'name'   => 'GeneralEvent',
						'params' => $params
					);
				}

				$events[] = array(
					'type'   => 'track',
					'name'   => 'PageView',
					'params' => array( 'page' => $params['page'], 'domain' => $params['domain'] )
				);
			}

			foreach ( $events as $event ) {
				$track .= "fbq('" . $event['type'] . "', '" . $event['name'] . "', '" . json_encode( $event['params'] ) . "');";
			}

		}

		return $track;
	}

	public function packPixel( $track = '' ) {
		if ( $track <> '' ) {
			$codes = json_decode( json_encode( PSP_Classes_Tools::getOption( 'codes' ) ) );

			return sprintf( "<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init', '%s');%s</script><noscript><img height='1' width='1' alt='' style='display:none'src='https://www.facebook.com/tr?id=%s&ev=PageView&noscript=1'/></noscript>" . "\n", $codes->facebook_pixel, $track, $codes->facebook_pixel );
		}

		return false;
	}
}
