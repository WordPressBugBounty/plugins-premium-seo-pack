<?php
/**
 * PSP_Models_Abstract_Seo
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

abstract class PSP_Models_Abstract_Seo {
	protected $_post;
	protected $_sq_use;
	protected $author;

	public function __construct() {
		$this->_post = PSP_Classes_ObjController::getClass( 'PSP_Models_Frontend' )->getPost();

	}

	/**************************** CLEAR THE VALUES *************************************/
	/***********************************************************************************/
	/**
	 * Clear and format the title for all languages
	 *
	 * @param $title
	 *
	 * @return string
	 */
	public function clearTitle( $title ) {

		if ($title <> '') {

			$search = array(
				"/[\n\r]/si",
				"/[\n]/si",
				"/&nbsp;/si",
				"/\s{2,}/",
			);
			$title = preg_replace($search, " ", $title);

			$title = PSP_Classes_Tools::i18n(trim(esc_html(ent2ncr(wp_strip_all_tags($title)))));

		}

		return $title;
	}

	/**
	 * Clear and format the description for all languages
	 *
	 * @param $description
	 *
	 * @return mixed|string
	 */
	public function clearDescription( $description ) {

		if ($description <> '') {

			$search = array(
				"'<!--(.*?)-->'is",
				"'<script[^>]*?>.*?<\/script>'si", // strip out javascript
				"'<style[^>]*?>.*?<\/style>'si", // strip out styles
				"'<form.*?<\/form>'si",
				"'<iframe.*?<\/iframe>'si",
				"'&lt;!--(.*?)--&gt;'is",
				"'&lt;script&gt;.*?&lt;\/script&gt;'si", // strip out javascript
				"'&lt;style&gt;.*?&lt;\/style&gt;'si", // strip out styles
			);
			$description = preg_replace($search, "", $description);
			$search = array(
				"/[\n\r]/si",
				"/[\n]/si",
				"/&nbsp;/si",
				"/\s{2,}/",
			);
			$description = preg_replace($search, " ", $description);

			$description = PSP_Classes_Tools::i18n(trim(esc_html(ent2ncr(wp_strip_all_tags($description)))));
		}

		return $description;
	}

	/**
	 * Get the image from post
	 *
	 * @return array
	 */
	public function getPostImages() {
		$images = array();

		if ( (int) $this->_post->ID == 0 ) {
			return $images;
		}

		if ( has_post_thumbnail( $this->_post->ID ) ) {
			$attachment = get_post( get_post_thumbnail_id( $this->_post->ID ) );
			$url        = wp_get_attachment_image_src( $attachment->ID, 'full' );
			$images[]   = array(
				'src'         => esc_url( $url[0] ),
				'title'       => $this->clearTitle( $this->_post->post_title ),
				'description' => $this->clearDescription( $this->_post->post_excerpt ),
				'width'       => $url[1],
				'height'      => $url[2],
			);
		}

		if ( empty( $images ) ) {
			if ( isset( $this->_post->post_content ) ) {
				preg_match( '/<img[^>]*src="([^"]*)"[^>]*>/i', $this->_post->post_content, $match );

				if ( ! empty( $match ) ) {
					preg_match( '/alt="([^"]*)"/i', $match[0], $alt );

					if ( strpos( $match[1], '//' ) === false ) {
						$match[1] = get_bloginfo( 'url' ) . $match[1];
					}

					$images[] = array(
						'src'         => esc_url( $match[1] ),
						'title'       => $this->clearTitle( ! empty( $alt[1] ) ? $alt[1] : '' ),
						'description' => '',
						'width'       => '500',
						'height'      => null,
					);
				}
			}
		}


		return $images;
	}

	/**
	 * Get the video from content
	 *
	 * @return array
	 */
	public function getPostVideos() {
		$videos = array();

		if ( (int) $this->_post->ID == 0 ) {
			return $videos;
		}

		if ( isset( $this->_post->post_content ) ) {
			preg_match( '/(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed)\/)([^\?&\"\'>\s]+)/si', $this->_post->post_content, $match );

			if ( isset( $match[0] ) ) {
				if ( strpos( $match[0], '//' ) !== false && strpos( $match[0], 'http' ) === false ) {
					$match[0] = 'http:' . $match[0];
				}
				$videos[] = esc_url( $match[0] );
			}

			preg_match( '/(?:http(?:s)?:\/\/)?(?:fwd4\.wistia\.com\/(?:medias)\/)([^\?&\"\'>\s]+)/si', $this->_post->post_content, $match );

			if ( isset( $match[0] ) ) {
				$videos[] = esc_url( 'http://fast.wistia.net/embed/iframe/' . $match[1] );
			}

			preg_match( '/class=["|\']([^"\']*wistia_async_([^\?&\"\'>\s]+)[^"\']*["|\'])/si', $this->_post->post_content, $match );

			if ( isset( $match[0] ) ) {
				$videos[] = esc_url( 'http://fast.wistia.net/embed/iframe/' . $match[2] );
			}

			preg_match( '/src=["|\']([^"\']*(.mpg|.mpeg|.mp4|.mov|.wmv|.asf|.avi|.ra|.ram|.rm|.flv)["|\'])/i', $this->_post->post_content, $match );

			if ( isset( $match[1] ) ) {
				$videos[] = esc_url( $match[1] );
			}
		}

		return $videos;
	}

	/**
	 * Check if is the homepage
	 *
	 * @return bool
	 */
	public function isHomePage() {
		return PSP_Classes_ObjController::getClass( 'PSP_Models_Frontend' )->isHomePage();
	}

	public function getPost() {
		return PSP_Classes_ObjController::getClass( 'PSP_Models_Frontend' )->getPost();
	}

	public function returnFalse() {
		return false;
	}

	public function truncate( $text, $min = 100, $max = 110 ) {
		if ( $text <> '' && strlen( $text ) > $max ) {
			if ( function_exists( 'strip_tags' ) ) {
				$text = strip_tags( $text );
			}
			$text = str_replace( ']]>', ']]&gt;', $text );
			$text = @preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text );
			$text = strip_tags( $text );

			if ( $max < strlen( $text ) ) {
				while ( $text[ $max ] != ' ' && $max > $min ) {
					$max --;
				}
			}
			$text = substr( $text, 0, $max );

			return trim( stripcslashes( $text ) );
		}

		return $text;
	}

	/**
	 * Get the author
	 *
	 * @param string $what
	 *
	 * @return bool|mixed|string
	 */
	protected function getAuthor( $what = 'user_nicename' ) {

		if ( ! isset( $this->author ) ) {
			if ( is_author() ) {
				$this->author = get_userdata( get_query_var( 'author' ) );
			} elseif ( isset( $this->_post->post_author ) ) {
				if ( $author = get_userdata( (int) $this->_post->post_author ) ) {
					$this->author = $author->data;
				}
			}
		}


		if ( isset( $this->author ) ) {
			if ( $what == 'user_url' && $this->author->$what == '' ) {
				return get_author_posts_url( $this->author->ID, $this->author->user_nicename );
			}
			if ( isset( $this->author->$what ) ) {
				return $this->author->$what;
			}
		}

		return false;
	}

}