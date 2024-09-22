<?php
/**
 * PSP_Controllers_Admin
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Controllers_Admin extends PSP_Classes_FrontController {

	public $post;

	public function __construct() {
		parent::__construct();
		if ( PSP_Classes_Tools::getOption( 'psp_api' ) <> '' ) {
			add_action( 'admin_bar_menu', array( $this, 'hookTopmenu' ), 999 );
		}
	}

	public function hookInit() {
		//load wp_admin_bar button
		if ( current_user_can( 'manage_options' ) ) {
			//Check if there are expected upgrades
			PSP_Classes_Tools::checkSquirrlyApi();

			//check if activated
			if ( get_transient( 'psp_activate' ) == 1 ) {
				PSP_Classes_Tools::createTable();

				// Delete the redirect transient
				delete_transient( 'psp_activate' );

				wp_safe_redirect( admin_url( 'admin.php?page=psp_sub_menu_settings' ) );
				exit();
			}
		}

		if ( is_user_logged_in() ) {
			PSP_Classes_ObjController::getClass( 'PSP_Classes_DisplayController' )->loadMedia( 'backend' );
			PSP_Classes_ObjController::getClass( 'PSP_Classes_DisplayController' )->loadMedia( 'menu' );
		}
	}

	/**
	 * Creates the Setting menu in WordPress
	 *
	 * @return void
	 */
	public function hookMenu() {
		/* add the plugin menu in admin */
		if ( current_user_can( 'manage_options' ) ) {
			$this->model->addMenu( array(
				ucfirst( _PSP_NAME_ ) . __( ' Settings', _PSP_PLUGIN_NAME_ ),
				ucfirst( _PSP_NAME_ ),
				'edit_posts',
				'psp_sub_menu_settings',
				array( PSP_Classes_ObjController::getClass( 'PSP_Controllers_Settings' ), 'show' ),
				'dashicons-psplogo',
			) );
		}

		add_action( 'add_meta_boxes', array( $this, 'addMetabox' ), 1 );

	}


	public function addMetabox() {
		add_meta_box( 'psp_div', ucfirst( _PSP_NAME_ ), array(
			PSP_Classes_ObjController::getClass( 'PSP_Controllers_FrontMenu' ),
			'show'
		), null, 'normal', 'core' );
	}

	public function hookTopmenu( $wp_admin_bar ) {
		global $tag, $wp_the_query;

		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( is_admin() ) {
			$current_screen = get_current_screen();
			$post           = get_post();
			if ( 'post' == $current_screen->base && ( $post_type_object = get_post_type_object( $post->post_type ) ) && current_user_can( 'edit_post', $post->ID ) && ( $post_type_object->public ) ) {
			} elseif ( 'edit' == $current_screen->base && ( $post_type_object = get_post_type_object( $current_screen->post_type ) ) && ( $post_type_object->show_in_admin_bar ) && ! ( 'edit-' . $current_screen->post_type === $current_screen->id ) ) {
			} elseif ( 'term' == $current_screen->base && isset( $tag ) && is_object( $tag ) && ! is_wp_error( $tag ) && ( $tax = get_taxonomy( $tag->taxonomy ) ) && $tax->public ) {
			} else {
				return;
			}

			//Add the snippet in all post types
			$this->addMetabox();
		} else {

			if ( ! current_user_can( 'manage_options' ) ) {
				$current_object = $wp_the_query->get_queried_object();

				if ( empty( $current_object ) ) {
					return;
				}

				if ( ! empty( $current_object->post_type ) && ( $post_type_object = get_post_type_object( $current_object->post_type ) ) && current_user_can( 'edit_post', $current_object->ID ) && $post_type_object->show_in_admin_bar && $edit_post_link = get_edit_post_link( $current_object->ID ) ) {
				} elseif ( ! empty( $current_object->taxonomy ) && ( $tax = get_taxonomy( $current_object->taxonomy ) ) && current_user_can( 'edit_term', $current_object->term_id ) && $edit_term_link = get_edit_term_link( $current_object->term_id, $current_object->taxonomy ) ) {
				} else {
					return;
				}
			}
		}

		$wp_admin_bar->add_node( array(
			'id'     => 'psp_bar_menu',
			'title'  => '<span class="dashicons-psplogo"></span> ' . __( 'Custom SEO', _PSP_PLUGIN_NAME_ ),
			'parent' => 'top-secondary',
		) );
		$wp_admin_bar->add_menu( array(
			'id'     => 'psp_bar_submenu',
			'parent' => 'psp_bar_menu',
			'meta'   => array(
				'html'     => $this->getView( 'FrontMenu' ),
				'tabindex' => PHP_INT_MAX,
			),
		) );

		//for dahboard
		if ( is_admin() || is_network_admin() ) {
			PSP_Classes_ObjController::getClass( 'PSP_Classes_DisplayController' )->loadMedia( 'frontmenu' );
			PSP_Classes_ObjController::getClass( 'PSP_Classes_DisplayController' )->loadMedia( 'tagsinput' );
		}
	}


	public function hookWpheadinit() {
		if ( current_user_can( 'publish_posts' ) ) {

			PSP_Classes_ObjController::getClass( 'PSP_Classes_DisplayController' )->loadMedia( 'frontmenu' );
			PSP_Classes_ObjController::getClass( 'PSP_Classes_DisplayController' )->loadMedia( 'tagsinput' );

			//loade the media library
			wp_enqueue_media();

			echo '<script type="text/javascript">
                  var psp_Query = {
                    "ajaxurl": "' . esc_url(admin_url( 'admin-ajax.php' )) . '",
                    "nonce": "' . esc_attr(wp_create_nonce( _PSP_NONCE_ID_ )) . '"
                  }
              </script>';

			//Set the current post domain with all the data
			$this->post = PSP_Classes_ObjController::getClass( 'PSP_Models_Frontend' )->getPost();


		}
	}

	/**
	 * Called when Post action is triggered
	 *
	 * @return void
	 */
	public function action() {
		parent::action();

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		PSP_Classes_Tools::setHeader( 'json' );

		switch ( PSP_Classes_Tools::getValue( 'action' ) ) {
			case 'psp_getfrontmenu':
				$json    = array();
				$post_id = PSP_Classes_Tools::getValue( 'post_id', 0 );
				if ( $this->setPostByID( $post_id ) ) {
					$json['html'] = $this->getView( 'FrontMenu' );
				}
				echo json_encode( $json );
				exit();
		}
	}

	public function getPostType( $for, $post_type = null ) {

		if ( $for == 'og:type' ) {
			if ( isset( $this->post->psp->og_type ) && $this->post->psp->og_type <> '' ) {
				if ( $this->post->psp->og_type == $post_type ) {
					return 'selected="selected"';
				}
			} else {
				switch ( $post_type ) {
					case 'website':
						if ( $this->post->post_type == 'home' ) {
							return 'selected="selected"';
						}
						break;
					default:
						if ( $this->post->post_type == $post_type ) {
							return 'selected="selected"';
						}
				}
			}
		}

		return false;
	}

	public function showPostType( $for, $post_type = null ) {
		echo $this->getPostType( $for, $post_type );
	}

	public function getImportList() {
		return apply_filters( 'psp_importList', false );
	}

	public function setPostByURL( $url ) {
		$post_id    = url_to_postid( $url );
		$this->post = get_post( $post_id );

		if ( $post_id > 0 ) {
			add_filter( 'psp_current_post', array( $this, 'setCurrentPost' ), 10, 0 );
			PSP_Classes_ObjController::getClass( 'PSP_Models_Frontend' )->setPost();
			$this->post = PSP_Classes_ObjController::getClass( 'PSP_Models_Frontend' )->getPost();

			return $this->post;
		}

		return false;
	}

	public function setPostByID( $post_id = 0 ) {
		if ( $post_id > 0 ) {
			if ( $this->post = get_post( $post_id ) ) {
				add_filter( 'psp_current_post', array( $this, 'setCurrentPost' ), 10, 0 );
				PSP_Classes_ObjController::getClass( 'PSP_Models_Frontend' )->setPost();
				$this->post = PSP_Classes_ObjController::getClass( 'PSP_Models_Frontend' )->getPost();

				return $this->post;
			}
		}

		return false;
	}

	public function setCurrentPost() {
		return $this->post;
	}

}
