<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following:
 *
 * 1. This script is executed when the user clicks "Delete" on the plugin screen.
 * 2. It should clean up all plugin-specific data from the database.
 * 3. It should NOT remove data that might be shared with other plugins or themes.
 * 4. It should NOT remove user-generated content unless explicitly requested.
 * 5. It should be idempotent (running it multiple times has the same effect).
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Ensure WordPress database utilities are available
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

// Include the database class
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-database.php';
$database = new AS_Laburd