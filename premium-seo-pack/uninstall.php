<?php
/**
 * Uninstall Page
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * Called on plugin uninstall
 */
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

/* Call config files */
require(dirname(__FILE__) . '/config/config.php');

/* Delete the record from database */
global $wpdb;

delete_option(_PSP_OPTION_);
$wpdb->query($wpdb->prepare("DROP TABLE %s;", $wpdb->prefix . strtolower(_PSP_DB_)));

