<?php
/**
 * View File
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );
if(!isset($type) || !isset($message) ) exit();
?>
<div class="notice notice-<?php echo esc_attr($type) ?> is-dismissible">
    <p>
        <?php echo wp_kses_post($message) ?>
    </p>
</div>

