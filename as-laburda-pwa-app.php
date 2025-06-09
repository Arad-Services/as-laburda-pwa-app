<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://arad-services.com
 * @since             1.0.0
 * @package           AS_Laburda_PWA_App
 *
 * @wordpress-plugin
 * Plugin Name:       AS Laburda PWA App Creator
 * Plugin URI:        https://arad-services.com/pwa-app-creator-plugin-uri/
 * Description:       A comprehensive WordPress plugin to create PWA apps, manage business listings, run an affiliate program, and more.
 * Version:           2.0.0
 * Author:            Arad Services
 * Author URI:        https://arad-services.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       as-laburda-pwa-app
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Current plugin version.
 * This is used for cache busting and version control.
 */
define( 'AS_LABURDA_PWA_APP_VERSION', '2.0.0' );

/**
 * Include all core and manager classes needed globally.
 * Order matters for dependencies.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-utils.php'; // Required first by many
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-database.php'; // Required by Activator, other managers

// Include Activator and Deactivator for their hooks
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-activator.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-deactivator.php';

// Include the main plugin class (AS_Laburda_PWA_App) itself
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app.php';

// Include other manager classes that are instantiated within AS_Laburda_PWA_App's constructor
// These must be available in the global scope when AS_Laburda_PWA_App is constructed.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-app-builder.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-affiliates.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-analytics.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-ai-agent.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-events.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-listing-plans.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-memberships.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-menus.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-notifications.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-products.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-custom-fields.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-i18n.php'; // Required for text domain loading
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-seo.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-app-plans.php'; // New App Plans manager
require_once plugin_dir_path( __FILE__ ) . 'includes/class-as-laburda-pwa-app-loader.php'; // The loader itself needs to be available for main plugin class

// Admin and Public classes contain the partials and hook definitions, and are not strictly "managers" in the same sense
require_once plugin_dir_path( __FILE__ ) . 'admin/class-as-laburda-pwa-app-admin.php';
require_once plugin_dir_path( __FILE__ ) . 'public/class-as-laburda-pwa-app-public.php';


/**
 * The code that runs during plugin activation.
 */
function activate_as_laburda_pwa_app() {
    // Static method called. All dependencies for Activator are now loaded globally above.
    AS_Laburda_PWA_App_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_as_laburda_pwa_app() {
    // Static method called. All dependencies for Deactivator are now loaded globally above.
    AS_Laburda_PWA_App_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_as_laburda_pwa_app' );
register_deactivation_hook( __FILE__, 'deactivate_as_laburda_pwa_app' );


/**
 * Begins execution of the plugin.
 *
 * This function instantiates the main plugin class and runs it.
 * It's called after all necessary files are included and hooks are registered.
 */
function run_as_laburda_pwa_app() {
    // Get the single instance of the main plugin class
    $plugin = AS_Laburda_PWA_App::get_instance();
    $plugin->run();
}
run_as_laburda_pwa_app();

/**
 * Maps meta capabilities to primitive capabilities.
 *
 * This function is a callback for the 'map_meta_cap' filter. It allows
 * custom capabilities defined by the plugin (e.g., 'aslp_create_apps')
 * to be translated into WordPress's built-in primitive capabilities
 * (e.g., 'edit_posts', 'read'). This ensures that users with custom roles
 * can perform actions without needing direct primitive capabilities.
 *
 * @param array  $caps    The capabilities that the user is being checked against.
 * @param string $cap     The capability being checked.
 * @param int    $user_id The user ID.
 * @param array  $args    Additional arguments that may be passed to the cap check.
 * @return array The primitive capabilities required.
 */
function aslp_map_meta_cap( $caps, $cap, $user_id, $args ) {
    switch ( $cap ) {
        case 'aslp_manage_options':
            $caps = array( 'manage_options' );
            break;
        case 'aslp_create_apps':
            // Users who can publish pages can create apps (can be adjusted)
            $caps = array( 'publish_pages' );
            break;
        case 'aslp_manage_apps':
            // Users who can manage options can manage all apps
            $caps = array( 'manage_options' );
            break;
        case 'aslp_manage_own_apps':
            // Users who can edit their own pages can manage their own apps
            $caps = array( 'edit_pages' );
            break;
        case 'aslp_submit_business_listing':
            // Users who can publish posts can submit listings
            $caps = array( 'publish_posts' );
            break;
        case 'aslp_manage_all_business_listings':
            // Users who can manage options can manage all listings
            $caps = array( 'manage_options' );
            break;
        case 'aslp_manage_own_business_listings':
            // Users who can edit their own posts can manage their own listings
            $caps = array( 'edit_posts' );
            break;
        case 'aslp_manage_listing_plans':
            $caps = array( 'manage_options' );
            break;
        case 'aslp_manage_app_plans':
            $caps = array( 'manage_options' );
            break;
        case 'aslp_manage_affiliates':
            $caps = array( 'manage_options' );
            break;
        case 'aslp_view_affiliate_dashboard':
            // Any logged-in user can view their dashboard if they are an affiliate
            $caps = array( 'read' );
            break;
        case 'aslp_manage_notifications':
            $caps = array( 'manage_options' );
            break;
        case 'aslp_manage_custom_fields':
            $caps = array( 'manage_options' );
            break;
        case 'aslp_manage_menus':
            $caps = array( 'manage_options' );
            break;
        case 'aslp_view_analytics':
            $caps = array( 'manage_options' );
            break;
        case 'aslp_use_ai_assistant':
            // Any user with 'edit_posts' can use AI assistant (can be adjusted)
            $caps = array( 'edit_posts' );
            break;
        case 'aslp_use_seo_tools':
            $caps = array( 'edit_posts' );
            break;
        case 'aslp_use_admin_tools':
            $caps = array( 'manage_options' );
            break;
    }
    return $caps;
}
