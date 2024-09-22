<?php
/**
 * PSP_Models_Frontend
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Frontend {

	private $_post;
	private $_loaded = false;

	public function __construct() {
		//initiate the post
		$this->_post = false;

		add_filter( 'psp_post', array( $this, 'getWpPost' ), 11, 1 );
		add_filter( 'psp_post', array( $this, 'getPostDetails' ), 12, 1 );
		add_filter( 'psp_post', array( $this, 'replacePatterns' ), 13, 1 );
		add_filter( 'psp_post', array( $this, 'addPaged' ), 13, 1 );

		//change the buffer
		add_filter( 'psp_buffer', array( $this, 'setMetaInBuffer' ), 10, 1 );
		//pack html prefix if needed
		add_filter( 'psp_html_prefix', array( $this, 'packPrefix' ), 99 );
	}

	public function setStart() {
		return "\n\n<!-- Premium SEO Pack Plugin " . PSP_VERSION . " -->";

	}

	/**
	 * End the signature
	 *
	 * @return string
	 */
	public function setEnd() {
		return "<!-- /Premium SEO Pack Plugin -->\n";
	}

	/**
	 * Start the buffer record
	 *
	 * @return void
	 */
	public function startBuffer() {
		//Remove options from other plugins
		$this->checkOtherPlugins();
		if ( ! did_action( 'template_redirect' ) ) {
			remove_action( 'template_redirect', array( $this, 'startBuffer' ), 5 );
		}

		ob_start( array( $this, 'getBuffer' ) );
	}

	/**
	 * Get the loaded buffer and change it
	 *
	 * @param string $buffer
	 *
	 * @return string
	 */
	public function getBuffer( $buffer ) {
		//prevent from loading again
		if ( $this->_loaded ) {
			return $buffer;
		}

		return apply_filters( 'psp_buffer', $buffer );
	}

	/**
	 * Check and remove other plugins options
	 */
	public function checkOtherPlugins() {
		//Let Squirrly is SEO is activated
		if ( class_exists( 'SQ_Tools' ) ) {
			SQ_Tools::getOptions();
			if ( PSP_Classes_Tools::getOption( 'psp_title_opt' ) ) {
				SQ_Tools::$options['sq_auto_title'] = 0;
			}
			if ( PSP_Classes_Tools::getOption( 'psp_description_opt' ) ) {
				SQ_Tools::$options['sq_auto_description'] = 0;
			}
			if ( PSP_Classes_Tools::getOption( 'psp_canonical_opt' ) ) {
				SQ_Tools::$options['sq_auto_canonical'] = 0;
			}
			if ( PSP_Classes_Tools::getOption( 'psp_og_opt' ) ) {
				SQ_Tools::$options['sq_auto_facebook'] = 0;
			}
		}
	}

	/**
	 * Change the title, description and keywords in site's buffer
	 *
	 * @param string $buffer
	 *
	 * @return string
	 */
	public function setMetaInBuffer( $buffer ) {
		//if is enabled psp for this page
		if ( $this->runSEOForThisPage() ) {
			if ( strpos( $buffer, '</head' ) !== false ) {
				if ( $header = $this->getHeader() ) {

					//set loaded true
					$this->_loaded = true;

					if ( PSP_Classes_Tools::getOption( 'psp_remove_duplicates_opt' ) ) {
						//clear the existing tags to avoid duplicates
						if ( isset( $header['psp_title'] ) && $header['psp_title'] <> '' && PSP_Classes_Tools::getOption( 'psp_title_opt' ) ) {
							$buffer = @preg_replace( '/<title[^<>]*>([^<>]*)<\/title>/si', '', $buffer, - 1 );
						}
						if ( isset( $header['psp_description'] ) && $header['psp_description'] <> '' && PSP_Classes_Tools::getOption( 'psp_description_opt' ) ) {
							$buffer = @preg_replace( '/<meta[^>]*(name|property)=["\']description["\'][^>]*content=["\'][^"\'>]*["\'][^>]*>[\n\r]*/si', '', $buffer, - 1 );
						}
						if ( isset( $header['psp_keywords'] ) && $header['psp_keywords'] <> '' && PSP_Classes_Tools::getOption( 'psp_keywords_opt' ) ) {
							$buffer = @preg_replace( '/<meta[^>]*(name|property)=["\']keywords["\'][^>]*content=["\'][^"\'>]*["\'][^>]*>[\n\r]*/si', '', $buffer, - 1 );
						}
						if ( isset( $header['psp_canonical'] ) && $header['psp_canonical'] <> '' && PSP_Classes_Tools::getOption( 'psp_canonical_opt' ) ) {
							$buffer = @preg_replace( '/<link[^>]*rel=[^>]*canonical[^>]*>[\n\r]*/si', '', $buffer, - 1 );
						}
						if ( isset( $header['psp_prevnext'] ) && $header['psp_prevnext'] <> '' && PSP_Classes_Tools::getOption( 'psp_prevnext_opt' ) ) {
							$buffer = @preg_replace( '/<link[^>]*rel=[^>]*(prev|next)[^>]*>[\n\r]*/si', '', $buffer, - 1 );
						}
						if ( isset( $header['psp_sitemap'] ) && $header['psp_sitemap'] <> '' && PSP_Classes_Tools::getOption( 'psp_sitemap_opt' ) ) {
							$buffer = @preg_replace( '/<link[^>]*rel=[^>]*alternate[^>]*>[\n\r]*/si', '', $buffer, - 1 );
						}
						if ( isset( $header['psp_noindex'] ) && $header['psp_noindex'] <> '' && PSP_Classes_Tools::getOption( 'psp_noindex_opt' ) ) {
							$buffer = @preg_replace( '/<meta[^>]*name=[^>]*robots[^>]*>[\n\r]*/si', '', $buffer, - 1 );
						}
						if ( isset( $header['psp_open_graph'] ) && $header['psp_open_graph'] <> '' && PSP_Classes_Tools::getOption( 'psp_og_opt' ) ) {
							$buffer = @preg_replace( '/<meta[^>]*(name|property)=["\'](og:|article:)[^"\'>]+["\'][^>]*content=["\'][^"\'>]+["\'][^>]*>[\n\r]*/si', '', $buffer, - 1 );
						}
						if ( isset( $header['psp_twitter_card'] ) && $header['psp_twitter_card'] <> '' && PSP_Classes_Tools::getOption( 'psp_tw_opt' ) ) {
							$buffer = @preg_replace( '/<meta[^>]*(name|property)=["\'](twitter:)[^"\'>]+["\'][^>]*content=["\'][^"\'>]+["\'][^>]*>[\n\r]*/si', '', $buffer, - 1 );
						}
						if ( PSP_Classes_Tools::getOption( 'psp_jsonld_clearcode' ) ) {
							if ( isset( $header['psp_json_ld'] ) && $header['psp_json_ld'] <> '' && PSP_Classes_Tools::getOption( 'psp_jsonld_opt' ) ) {
								$buffer = @preg_replace( '/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>[^>]*<\/script>[\n\r]*/si', '', $buffer, - 1 );
							}
						}

					}

					$buffer = @preg_replace( '/(<head(\s[^>]*|)>)/si', sprintf( "$1\n%s", join( "\n", $header ) ) . "\n", $buffer, 1 );
					$buffer = @preg_replace( '/(<html(\s[^>]*|))/si', sprintf( "$1%s", apply_filters( 'psp_html_prefix', false ) ), $buffer, 1 );

				}
			}
		}

		return $buffer;
	}


	/**
	 * Overwrite the header with the correct parameters
	 *
	 * @return string | false
	 */
	public function getHeader() {
		$header              = array();
		$header['psp_title'] = apply_filters( 'psp_title', false );


		//Get all header in array
		$header['psp_start'] = $this->setStart();

		$header['psp_noindex'] = apply_filters( 'psp_noindex', false ); //
		//Add description in homepage if is set or add description in other pages if is not home page
		$header['psp_description'] = apply_filters( 'psp_description', false ); //
		$header['psp_keywords']    = apply_filters( 'psp_keywords', false ); //

		$header['psp_canonical'] = apply_filters( 'psp_canonical', false ); //
		$header['psp_prevnext']  = apply_filters( 'psp_prevnext', false ); //

		$header['psp_sitemap']     = apply_filters( 'psp_sitemap', false );
		$header['psp_favicon']     = apply_filters( 'psp_favicon', false );
		$header['psp_language']    = apply_filters( 'psp_language', false );
		$header['psp_copyright']   = apply_filters( 'psp_copyright', false );
		$header['psp_dublin_core'] = apply_filters( 'psp_dublin_core', false );

		$header['psp_open_graph']   = apply_filters( 'psp_open_graph', false ); //
		$header['psp_publisher']    = apply_filters( 'psp_publisher', false ); //
		$header['psp_twitter_card'] = apply_filters( 'psp_twitter_card', false ); //

		/* SEO optimizer tool */
		$header['psp_verify']           = apply_filters( 'psp_verify', false ); //
		$header['psp_google_analytics'] = apply_filters( 'psp_google_analytics', false ); //
		$header['psp_facebook_pixel']   = apply_filters( 'psp_facebook_pixel', false ); //

		/* Structured Data */
		$header['psp_json_ld'] = apply_filters( 'psp_json_ld', false );
		$header['sq_end']      = $this->setEnd();

		//flush the header
		$header = @array_filter( $header );

		if ( count( $header ) == 2 ) {
			return false;
		}

		return $header;
	}

	/**************************************************************************************************/

	/**
	 * Load all SEO classes
	 */
	public function loadSeoLibrary() {
		if ( $this->_post && $this->_post->psp->doseo ) {
			//load all services
			if ( PSP_Classes_Tools::getOption( 'psp_title_opt' ) ) {
				PSP_Classes_ObjController::getClass( 'PSP_Models_Services_Title' );
			}
			if ( PSP_Classes_Tools::getOption( 'psp_description_opt' ) ) {
				PSP_Classes_ObjController::getClass( 'PSP_Models_Services_Description' );
			}
			if ( PSP_Classes_Tools::getOption( 'psp_keywords_opt' ) ) {
				PSP_Classes_ObjController::getClass( 'PSP_Models_Services_Keywords' );
			}
			if ( PSP_Classes_Tools::getOption( 'psp_canonical_opt' ) ) {
				PSP_Classes_ObjController::getClass( 'PSP_Models_Services_Canonical' );
			}
			if ( PSP_Classes_Tools::getOption( 'psp_prevnext_opt' ) ) {
				PSP_Classes_ObjController::getClass( 'PSP_Models_Services_PrevNext' );
			}
			if ( PSP_Classes_Tools::getOption( 'psp_sitemap_opt' ) ) {
				PSP_Classes_ObjController::getClass( 'PSP_Models_Services_Sitemap' );
			}
			if ( PSP_Classes_Tools::getOption( 'psp_noindex_opt' ) ) {
				PSP_Classes_ObjController::getClass( 'PSP_Models_Services_Noindex' );
			}
			if ( PSP_Classes_Tools::getOption( 'psp_og_opt' ) ) {
				PSP_Classes_ObjController::getClass( 'PSP_Models_Services_OpenGraph' );
			}
			if ( PSP_Classes_Tools::getOption( 'psp_tw_opt' ) ) {
				PSP_Classes_ObjController::getClass( 'PSP_Models_Services_TwitterCard' );
			}

			//
			PSP_Classes_ObjController::getClass( 'PSP_Models_Services_Publisher' );
			PSP_Classes_ObjController::getClass( 'PSP_Models_Services_Verify' );
			PSP_Classes_ObjController::getClass( 'PSP_Models_Services_Pixel' );
			PSP_Classes_ObjController::getClass( 'PSP_Models_Services_Analytics' );

			if ( PSP_Classes_Tools::getOption( 'psp_jsonld_opt' ) ) {
				PSP_Classes_ObjController::getClass( 'PSP_Models_Services_JsonLD' );
			}


			//PSP_Classes_ObjController::getClass('PSP_Models_Services_JsonLD');
		}
	}

	public function setPost() {
		//Set the current post
		$this->_post = apply_filters( 'psp_post', false );

		//Load the SEO after we have the post
		$this->loadSeoLibrary();
	}

	public function getPost() {
		return $this->_post;
	}

	/**
	 * Replace the patterns from tags
	 *
	 * @param PSP_Models_Domain_Post $post
	 *
	 * @return PSP_Models_Domain_Post | false
	 */
	public function replacePatterns( $post ) {
		if ( $post instanceof PSP_Models_Domain_Post ) {
			$patterns = PSP_Classes_ObjController::getDomain( 'PSP_Models_Domain_Patterns', $post->toArray() );

			if ( $psp_array = $post->psp->toArray() ) {
				if ( ! empty( $psp_array ) ) {
					foreach ( $psp_array as $name => $value ) {
						if ( $name == 'sep' && $value <> '' ) {
							//set the current post type sep loaded in PSP_Models_Domain_Post domain
							$patterns->sep = $value;
						}

						if (strpos($value, '%%') !== false) { //in case there are still patterns from Yoast
							$value = preg_replace('/%%([^\%\s]+)%%/s', '{{$1}}', $value);
						}

						if (strpos($value, '%') !== false) { //in case there are still patterns from Rank Math
							$value = preg_replace('/%([^\%\s]+)%/s', '{{$1}}', $value);
						}

						if ( strpos( $value, '{{' ) !== false && strpos( $value, '}}' ) !== false ) {
							$psp_with_patterns[ $name ] = $value;
						}
					}

					if ( ! empty( $psp_with_patterns ) ) {
						foreach ( $patterns->getPatterns() as $key => $pattern ) {
							foreach ( $psp_with_patterns as $name => $value ) {

								if ( strpos( $value, $pattern ) !== false ) {
									if($patterns->$key){
										$post->psp->$name = str_replace( $pattern, $patterns->$key, $post->psp->$name );
									}else{
										$post->psp->$name = str_replace( $pattern, '', $post->psp->$name );
									}
								}
							}
						}
					}

					return $post;
				}
			}
		}

		return false;
	}


	/**
	 * Get the current post from WordPress
	 *
	 * @return WP_Post
	 */
	public function getWpPost() {
		global $post;
		$current_post = false;

		if ( function_exists( 'is_shop' ) && is_shop() ) {
			$current_post = get_post( wc_get_page_id( 'shop' ) );
		} elseif ( ( is_single() || is_singular() ) && isset( $post->ID ) ) {
			$current_post = get_post( $post->ID );
		}

		return apply_filters( 'psp_current_post', $current_post );
	}

	/**
	 * Get the Psp from database
	 *
	 * @param $hash
	 *
	 * @return mixed|null
	 */
	public function getPspSeo( $hash ) {
		global $wpdb;
		$blog_id = get_current_blog_id();

		if ( $hash <> '' ) {
			$query = "SELECT * FROM " . $wpdb->prefix . strtolower( _PSP_DB_ ) . " WHERE blog_id = '" . (int) $blog_id . "' AND url_hash = '" . $hash . "';";

			if ( $row = $wpdb->get_row( $query, OBJECT ) ) {
				return PSP_Classes_ObjController::getDomain( 'PSP_Models_Domain_Qss', unserialize( stripslashes( $row->seo ) ) );
			}
		}

		return PSP_Classes_ObjController::getDomain( 'PSP_Models_Domain_Qss' );
	}

	/**
	 * Build the current post with all the data required
	 *
	 * @param WP_Post $post
	 *
	 * @return PSP_Models_Domain_Post | false
	 */
	public function getPostDetails( $post ) {
		if ( $post instanceof WP_Post ) {

			if ( is_feed() ) {
				return false;
			}

			$post = PSP_Classes_ObjController::getDomain( 'PSP_Models_Domain_Post', $post );

			if ( $this->isHomePage() ) {
				$post->post_type = 'home';

				if ( is_front_page() && $post_id = get_option( 'page_on_front' ) ) {
					$post->ID   = $post_id;
					$post->hash = md5( $post->ID );
				} elseif ( $post_id = get_option( 'page_for_posts' ) ) {
					$post->ID   = $post_id;
					$post->hash = md5( $post->ID );
				}

				$post->url = home_url();

				return $post;
			}

			if ( isset( $post->ID ) ) {

				if ( function_exists( 'is_product' ) && is_product() ) {
					$post->post_type = 'product';
					$post->hash      = md5( $post->ID );
					$post->url       = get_permalink( $post->ID );
					$cat             = get_the_terms( $post->ID, 'product_cat' );
					if ( ! empty( $cat ) && count( $cat ) > 0 ) {
						$post->category = $cat[0]->name;
						if ( isset( $cat[0]->description ) ) {
							$post->category_description = $cat[0]->description;
						}
					}

					return $post;
				}

				if ( function_exists( 'is_shop' ) && is_shop() ) {
					$post->post_type = 'shop';
					$post->hash      = md5( $post->post_type . $post->ID );
					$post->url       = get_permalink( $post->ID );

					return $post;
				}

				if ( $post->post_type == 'post' || $post->post_type == 'page' || $post->post_type == 'product' ) {
					$post->hash = md5( $post->ID );
					$post->url  = get_permalink( $post->ID );

					return $post;
				}


				if ( $post->post_type == 'attachment' ) {
					$post->hash = md5( $post->ID );
					$post->url  = get_permalink( $post->ID );

					return $post;
				}

				if ( $post->post_type = $this->getCutomPostType() ) {
					$post->hash = md5( $post->post_type . $post->ID );
					$post->url  = get_permalink( $post->ID );

					return $post;
				}

			}

			if ( $post->post_type = $this->getCutomPostType() ) {
				if ( $post->post_name <> '' ) {
					$post->hash = md5( $post->post_type . $post->post_name );
				} else {
					$post->hash = md5( $post->post_type );
				}
				$post->url = get_post_type_archive_link( $post->post_type );

				return $post;
			}

		}

		$post = PSP_Classes_ObjController::getDomain( 'PSP_Models_Domain_Post' );

		if ( $this->isHomePage() ) {
			$post->post_type = 'home';
			$post->hash      = md5( 'wp_homepage' );

			$post->url = home_url();

			return $post;
		}

		if ( is_tag() ) {
			$tag             = $this->getTagDetails();
			$post->post_type = 'tag';
			if ( isset( $tag->term_id ) ) {
				$post->hash = md5( $post->post_type . $tag->term_id );
				$post->url  = get_tag_link( $tag->term_id );
			}

			return $post;
		}

		if ( is_tax() ) {
			if ( $tax = $this->getTaxonomyDetails() ) {
				if ( isset( $tax->taxonomy ) && $tax->taxonomy <> '' ) {
					$post->post_type = 'tax-' . $tax->taxonomy;
					if ( isset( $tax->term_id ) ) {
						$post->hash         = md5( $post->post_type . $tax->term_id );
						$post->url          = get_term_link( $tax->term_id );
						$post->post_title   = ( ( isset( $tax->name ) ) ? $tax->name : '' );
						$post->post_excerpt = ( ( isset( $tax->description ) ) ? $tax->description : '' );
					}

					return $post;
				}
			}

		}

		if ( is_category() ) {
			$category        = $this->getCategoryDetails();
			$post->post_type = 'category';
			if ( isset( $category->term_id ) ) {
				$post->hash                 = md5( $post->post_type . $category->term_id );
				$post->guid                 = $category->slug;
				$post->url                  = get_term_link( $category->term_id );
				$post->category             = $category->cat_name;
				$post->category_description = $category->description;
			}

			return $post;
		}

		if ( is_search() ) {
			$post->post_type = 'search';
			$post->hash      = md5( $post->post_type );

			//Set the search guid
			$post->url = home_url() . '/' . $post->post_type . '/';
			$search    = get_query_var( 's' );
			if ( $search !== '' ) {
				$post->url  .= $search;
				$post->hash = md5( $post->post_type . $search );
			}

			if ( $post->post_name <> '' ) {
				$post->hash = md5( $post->guid );
			}

			return $post;
		}

		if ( is_author() ) {
			if ( $author = $this->getAuthorDetails() ) {
				$post->post_type = 'profile';
				if ( isset( $author->ID ) ) {
					$post->hash         = md5( $post->post_type . $author->ID );
					$post->post_author  = $author->display_name;
					$post->post_excerpt = $author->description;

					//If buddypress installed
					if ( function_exists( 'bp_core_get_user_domain' ) ) {
						$post->url = bp_core_get_user_domain( $author->ID );
					} else {
						$post->url = get_author_posts_url( $author->ID );
					}

				}

				return $post;
			}

		}

		if ( is_archive() ) {
			if ( $archive = $this->getArchiveDetails() ) {
				$post->post_type = 'archive';
				if ( $archive->path <> '' ) {
					$post->hash      = md5( $post->post_type . $archive->path );
					$post->url       = $archive->url;
					$post->post_date = date( get_option( 'date_format' ), strtotime( $archive->path ) );
				}

				return $post;
			}
		}

		if ( $post->post_type = $this->getCutomPostType() ) {
			$post->hash = md5( $post->post_type );
			$post->url  = get_post_type_archive_link( $post->post_type );

			return $post;
		}

		if ( is_404() ) {
			$post->post_type = '404';
			$post->hash      = md5( $post->post_type );
			if ( $post->post_name <> '' ) {
				$post->hash = md5( $post->post_type . $post->post_name );
			}

			return $post;
		}

		return false;
	}

	/**
	 * Add page if needed
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public function addPaged( $post ) {
		if ( is_paged() && isset( $post->url ) && $post->url <> '' ) {
			$page = (int) get_query_var( 'paged' );
			if ( $page && $page > 1 ) {
				$post->url = trailingslashit( $post->url ) . "page/" . "$page/";
			}
		}

		return $post;
	}

	/**
	 * Get information about the Archive
	 *
	 * @return array|bool|mixed|object
	 */
	private function getArchiveDetails() {
		if ( is_date() ) {
			$archive = false;
			if ( is_day() ) {
				$archive = array(
					'path' => get_query_var( 'year' ) . '-' . get_query_var( 'monthnum' ) . '-' . get_query_var( 'day' ),
					'url'  => get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) ),
				);
			} elseif ( is_month() ) {
				$archive = array(
					'path' => get_query_var( 'year' ) . '-' . get_query_var( 'monthnum' ),
					'url'  => get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) ),
				);
			} elseif ( is_year() ) {
				$archive = array(
					'path' => get_query_var( 'year' ),
					'url'  => get_year_link( get_query_var( 'year' ) ),
				);
			}

			if ( ! empty( $archive ) ) {
				return json_decode( json_encode( $archive ) );
			}
		}

		return false;
	}

	/**
	 * Get the keyword fof this URL
	 *
	 * @return array|bool|false|mixed|null|object|string|WP_Error|WP_Term
	 */
	private function getTagDetails() {
		global $tag;
		$temp = str_replace( '&#8230;', '...', single_tag_title( '', false ) );

		foreach ( get_taxonomies() as $tax ) {
			if ( $tax <> 'category' ) {
				if ( $tag = get_term_by( 'name', $temp, $tax ) ) {
					break;
				}
			}
		}

		return $tag;
	}

	/**
	 * Get the taxonomies details for this URL
	 *
	 * @return array|bool|false|mixed|null|object|string|WP_Error|WP_Term
	 */
	private function getTaxonomyDetails() {
		$term = false;

		if ( $id = get_queried_object_id() ) {
			if ( $term = get_term( $id ) ) {
				if ( is_archive() ) {
					// $term->name .= ' ' . __('Archive', _PSP_PLUGIN_NAME_);
				}
			}
		}

		return $term;
	}

	/**
	 * Get the category details for this URL
	 *
	 * @return array|null|object|WP_Error
	 */
	private function getCategoryDetails() {
		return get_category( get_query_var( 'cat' ), false );
	}

	/**
	 * Get the profile details for this URL
	 *
	 * @return object
	 */
	public function getAuthorDetails() {
		$author = false;
		global $authordata;
		if ( isset( $authordata->data ) ) {
			$author              = $authordata->data;
			$author->description = get_the_author_meta( 'description' );
		}

		return $author;
	}


	/**
	 * Get the custom post type
	 *
	 * @return false|object
	 */
	public function getCutomPostType() {
		if ( $post_type = get_query_var( 'post_type' ) ) {
			if ( is_array( $post_type ) && ! empty( $post_type ) ) {
				$post_type = current( $post_type );
			}
		}

		if ( $post_type <> '' ) {
			return $post_type;
		}

		return false;
	}

	/**
	 * Check if is the homepage
	 *
	 * @return bool
	 */
	public function isHomePage() {
		global $wp_query;

		return ( is_home() || ( isset( $wp_query->query ) && empty( $wp_query->query ) && ! is_preview() ) );
	}

	/**
	 * Check if the header is an HTML Header
	 *
	 * @return bool
	 */
	public function isHtmlHeader() {
		$headers = headers_list();

		foreach ( $headers as $index => $value ) {
			if ( strpos( $value, ':' ) !== false ) {
				$exploded = @explode( ': ', $value );
				if ( count( $exploded ) > 1 ) {
					$headers[ $exploded[0] ] = $exploded[1];
				}
			}
		}

		if ( isset( $headers['Content-Type'] ) ) {
			if ( strpos( $headers['Content-Type'], 'text/html' ) !== false ) {
				return true;
			}
		} else {
			return true;
		}

		return false;
	}

	/**
	 * Is Premium SEO Pack enabled for this page?
	 *
	 * @return bool
	 */
	public function runSEOForThisPage() {

		if ( ! $this->isHtmlHeader() ) {
			return false;
		}

		if ( $this->_post && isset( $this->_post->hash ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Pack HTML prefix if exists
	 *
	 * @param $prefix
	 *
	 * @return string
	 */
	public function packPrefix( $prefix ) {
		if ( $prefix <> '' ) {
			return ' prefix="' . $prefix . '"';
		}

		return '';
	}

}
