<?php
/*
  Copyright (c) 2013 - 2024, Squirrly.
  License: GPL2+
  The copyrights to the software code in this file are licensed under the (revised) BSD open source license.

  Plugin Name: Premium SEO Pack
  Plugin URI: https://wordpress.org/plugins/premium-seo-pack/
  Author: WP SEO - Calin Vingan
  Description: Premium SEO Pack helps you increase the SEO value for all your pages, decide how you want them to look like on search engine results, and let's not forget about social media.
  Version: 1.6.002
  Author URI: https://profiles.wordpress.org/calinvingan
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

if (!defined('QSS_VERSION')) {
    define('PSP_VERSION', '1.6.002');

    /* Call config files */
    require(dirname(__FILE__) . '/config/config.php');


	/* import main classes */
	require_once(_PSP_CLASSES_DIR_ . 'ObjController.php');
	PSP_Classes_ObjController::getClass('PSP_Classes_FrontController');

	if (is_admin() || is_network_admin()) {
		/* Main class call for admin */

		PSP_Classes_ObjController::getClass('PSP_Classes_FrontController')->runAdmin();
		register_activation_hook(__FILE__, array(PSP_Classes_ObjController::getClass('PSP_Classes_Tools'), 'psp_activate'));
		register_deactivation_hook(__FILE__, array(PSP_Classes_ObjController::getClass('PSP_Classes_Tools'), 'psp_deactivate'));
	} else {
		PSP_Classes_ObjController::getClass('PSP_Classes_FrontController')->runFrontend();
	}

    /**
     * Called in Notice Hook
     */
    function psp_showError() {
        echo '<div class="update-nag"><span style="color:red; font-weight:bold;">' . sprintf(__('For %s to work, the PHP version has to be equal or greater then 5.1', _PSP_PLUGIN_NAME_), ucfirst(str_replace('_', ' ', _PSP_PLUGIN_NAME_))) . '</span></div>';
    }

}