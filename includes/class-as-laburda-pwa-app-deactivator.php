<?php
/**
 * Fired during plugin deactivation.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Deactivator {

    /**
     * Perform deactivation tasks.
     *
     * @since    1.0.0
     * @since    2.0.0 Updated to remove new custom roles and capabilities.
     */
    public static function deactivate() {
        self::remove_custom_roles_and_capabilities();
        // Optionally, remove all app configurations upon deactivation.
        // This is commented out to prevent accidental data loss if the plugin is temporarily deactivated.
        // self::drop_custom_tables(); // Uncomment this if you want to remove all plugin data on deactivation

        // Flush rewrite rules to ensure manifest.json and service-worker.js URLs are removed
        flush_rewrite_rules();
    }

    /**
     * Remove custom database tables for the plugin.
     * This method is optional and should only be called if full data removal is desired upon deactivation.
     *
     * @since 2.0.0
     */
    private static function drop_custom_tables() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-database.php';
        $database = new AS_Laburda_PWA_App_Database();
        $database->drop_custom_tables();
    }

    /**
     * Remove custom roles and capabilities upon plugin deactivation.
     *
     * @since 1.0.0
     * @since 2.0.0 Updated to remove new capabilities for app builder, affiliates, and AI.
     */
    private static function remove_custom_roles_and_capabilities() {
        // Define all custom capabilities that were added
        $all_custom_caps = array(
            'aslp_manage_global_settings',
            'aslp_view_app_dashboard',
            'aslp_submit_business_listing',
            'aslp_manage_own_business_listings',
            'aslp_manage_all_business_listings',
            'aslp_manage_listing_plans',
            'aslp_manage_custom_fields',
            'aslp_manage_notifications',
            'aslp_send_business_notifications',
            'aslp_manage_products',
            'aslp_manage_events',
            'aslp_manage_app_menus',
            'aslp_create_apps',
            'aslp_manage_own_apps',
            'aslp_manage_apps',
            'aslp_manage_app_templates',
            'aslp_manage_affiliates',
            'aslp_view_affiliate_dashboard',
            'aslp_register_as_affiliate',
            'aslp_request_payout',
            'aslp_manage_seo',
            'aslp_view_analytics',
            'aslp_use_ai_agent',
            'aslp_manage_ai_settings',
        );

        // Remove these capabilities from the Administrator role
        $admin_role = get_role( 'administrator' );
        if ( $admin_role ) {
            foreach ( $all_custom_caps as $cap ) {
                $admin_role->remove_cap( $cap );
            }
        }

        // Remove custom roles
        remove_role( 'business_owner' );
        remove_role( 'app_user' );

        // Remove capabilities from default roles if they were added
        $subscriber_role = get_role( 'subscriber' );
        if ( $subscriber_role ) {
            $subscriber_role->remove_cap( 'aslp_view_app_dashboard' );
            $subscriber_role->remove_cap( 'aslp_register_as_affiliate' );
            $subscriber_role->remove_cap( 'aslp_use_ai_agent' );
        }
    }
}
