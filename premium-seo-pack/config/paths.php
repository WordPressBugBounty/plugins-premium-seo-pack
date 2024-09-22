<?php
/**
 * Paths Config
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

$currentDir = dirname( __FILE__ );

define( '_PSP_NAMESPACE_', 'PSP' );
define( '_PSP_DB_', 'PSP' );
define( '_PSP_PLUGIN_NAME_', 'premium-seo-pack' ); // same with WordPress url
define( '_PSP_NAME_', 'SEO Pack' );
define( '_PSP_THEME_NAME_', 'default' );
define( '_PSP_SUPPORT_EMAIL_', 'support@squirrly.co' );
define( '_PSP_OPTION_', '_psp_options' );


/* Directories */
define( '_PSP_ROOT_DIR_', realpath( $currentDir . '/..' ) );
define( '_PSP_CLASSES_DIR_', _PSP_ROOT_DIR_ . '/classes/' );
define( '_PSP_CONTROLLER_DIR_', _PSP_ROOT_DIR_ . '/controllers/' );
define( '_PSP_MODEL_DIR_', _PSP_ROOT_DIR_ . '/models/' );
define( '_PSP_SERVICE_DIR_', _PSP_MODEL_DIR_ . '/services/' );
define( '_PSP_TRANSLATIONS_DIR_', _PSP_ROOT_DIR_ . '/languages/' );
define( '_PSP_THEME_DIR_', _PSP_ROOT_DIR_ . '/view/' );

/* URLS */
define( '_PSP_URL_', plugins_url() . '/' . _PSP_PLUGIN_NAME_ );
define( '_PSP_THEME_URL_', _PSP_URL_ . '/view/' );

