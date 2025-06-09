<?php
/**
 * Fired during plugin activation.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Activator {

    /**
     * Perform activation tasks.
     *
     * @since    1.0.0
     * @since    2.0.0 Added database table creation and default role/capability setup.
     */
    public static function activate() {
        // Ensure the database manager is available
        // This file is required directly by the activation hook, so its dependencies need to be loaded.
        // AS_Laburda_PWA_App_Database and AS_Laburda_PWA_App_Utils are loaded in the main plugin file early.

        $database_manager = new AS_Laburda_PWA_App_Database();
        $database_manager->create_custom_tables();

        self::add_custom_roles_and_capabilities();

        // Flush rewrite rules to ensure manifest.json and service-worker.js URLs work
        flush_rewrite_rules();

        // Initialize default global settings if not already set
        $global_settings = get_option( 'as_laburda_pwa_app_global_settings', false );
        if ( $global_settings === false ) {
            $default_settings = array(
                'enable_app_builder'    => true,
                'enable_affiliates'     => true,
                'enable_analytics'      => true,
                'enable_ai_agent'       => true,
                'enable_business_listings' => true,
                'enable_products'       => true,
                'enable_events'         => true,
                'enable_notifications'  => true,
                'enable_menus'          => true,
                'enable_custom_fields'  => true,
                'enable_listing_plans'  => true,
                'enable_seo_tools'      => true,
                'enable_tools_menu'     => true,
                'ai_api_key'            => '', // Placeholder for AI API key
                'ai_api_endpoint'       => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
            );
            update_option( 'as_laburda_pwa_app_global_settings', $default_settings );
        }

        // Create default affiliate tier if it doesn't exist
        $default_tier = $database_manager->get_affiliate_tier_by_name( 'Tier 1' );
        if ( ! $default_tier ) {
            $database_manager->add_affiliate_tier( array(
                'tier_name'          => 'Tier 1',
                'description'        => 'Default affiliate tier with standard commission rates.',
                'base_commission_rate' => 10.00, // 10%
                'mlm_commission_rate'  => 0.00,  // No MLM for default
                'is_active'          => true,
            ) );
        }
    }

    /**
     * Add custom roles and capabilities.
     * This is duplicated from AS_Laburda_PWA_App_Admin to ensure roles are added on activation,
     * even if the admin class isn't fully loaded yet during the activation hook.
     *
     * @since 2.0.0
     */
    private static function add_custom_roles_and_capabilities() {
        // Role for App Users (can create/manage own apps, view app dashboard)
        add_role(
            'app_user',
            __( 'App User', 'as-laburda-pwa-app' ),
            array(
                'read'                   => true,
                'aslp_view_app_dashboard'  => true,
                'aslp_create_apps'       => true,
                'aslp_manage_own_apps'   => true,
            )
        );

        // Role for Business Owners (can manage their own listings, products, events, notifications)
        add_role(
            'business_owner',
            __( 'Business Owner', 'as-laburda-pwa-app' ),
            array(
                'read'                          => true,
                'aslp_submit_business_listing'  => true,
                'aslp_manage_own_business_listings' => true,
                'aslp_manage_products'          => true,
                'aslp_manage_events'            => true,
                'aslp_send_business_notifications' => true,
                'aslp_manage_notifications'     => true, // For subscribing/unsubscribing
                'aslp_view_business_dashboard'  => true,
                'aslp_manage_coupons'           => true, // Placeholder for future coupon feature
            )
        );

        // Role for Affiliates
        add_role(
            'affiliate',
            __( 'Affiliate', 'as-laburda-pwa-app' ),
            array(
                'read'                          => true,
                'aslp_register_as_affiliate'    => true,
                'aslp_view_affiliate_dashboard' => true,
                'aslp_request_payout'           => true,
            )
        );

        // Get the administrator role
        $admin_role = get_role( 'administrator' );

        if ( $admin_role ) {
            // Capabilities for App Management (Admin)
            $admin_role->add_cap( 'aslp_manage_apps' ); // Manage all apps
            $admin_role->add_cap( 'aslp_manage_app_templates' ); // Manage app templates
            $admin_role->add_cap( 'aslp_manage_app_menus' ); // Manage app menus

            // Capabilities for Business Listing Management (Admin)
            $admin_role->add_cap( 'aslp_manage_all_business_listings' ); // Manage all listings
            $admin_role->add_cap( 'aslp_manage_listing_plans' ); // Manage listing plans
            $admin_role->add_cap( 'aslp_manage_custom_fields' ); // Manage custom fields

            // Capabilities for Affiliate Program Management (Admin)
            $admin_role->add_cap( 'aslp_manage_affiliates' ); // Manage all affiliates, tiers, commissions, payouts
            $admin_role->add_cap( 'aslp_manage_affiliate_tiers' );
            $admin_role->add_cap( 'aslp_manage_affiliate_creatives' );
            $admin_role->add_cap( 'aslp_manage_affiliate_commissions' );
            $admin_role->add_cap( 'aslp_manage_affiliate_payouts' );

            // Capabilities for Analytics (Admin)
            $admin_role->add_cap( 'aslp_view_analytics' ); // View all analytics data

            // Capabilities for AI Agent (Admin)
            $admin_role->add_cap( 'aslp_manage_ai_settings' ); // Manage AI API keys, endpoints
            $admin_role->add_cap( 'aslp_use_ai_tools' ); // Use AI for SEO, content, debugging

            // Global Settings Management
            $admin_role->add_cap( 'aslp_manage_global_settings' ); // Manage plugin-wide feature toggles
        }
    }
}
