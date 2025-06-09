<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/admin
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The main plugin instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App    $main_plugin    The main plugin instance.
     */
    private $main_plugin;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version           The version of this plugin.
     * @param      AS_Laburda_PWA_App $main_plugin The main plugin instance.
     */
    public function __construct( $plugin_name, $version, $main_plugin ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->main_plugin = $main_plugin;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/as-laburda-pwa-app-admin.css', array(), $this->version, 'all' );
        // Add Font Awesome for icons
        wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css', array(), '6.0.0-beta3', 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/as-laburda-pwa-app-admin.js', array( 'jquery' ), $this->version, false );

        // Enqueue WordPress media uploader scripts
        if ( ! did_action( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        }

        // Localize script for AJAX calls
        wp_localize_script(
            $this->plugin_name,
            'aslp_ajax_object',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'aslp_admin_nonce' ),
            )
        );
    }

    /**
     * Add custom roles and capabilities upon plugin initialization.
     * This ensures roles are present even if the plugin was deactivated and reactivated.
     *
     * @since 1.0.0
     * @since 2.0.0 Added new roles and capabilities for PWA apps, affiliates, analytics, and AI.
     */
    public function add_custom_roles_and_capabilities() {
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

    /**
     * Add the top-level admin menu and submenus.
     *
     * @since 1.0.0
     * @since 2.0.0 Added new menu items for App Builder, Affiliates, Analytics, AI Assistant, Tools, and Global Settings.
     */
    public function add_plugin_admin_menu() {
        // Get global feature settings
        $global_features = $this->main_plugin->get_global_feature_settings();

        add_menu_page(
            __( 'PWA App Creator', 'as-laburda-pwa-app' ),
            __( 'PWA App Creator', 'as-laburda-pwa-app' ),
            'read', // Minimum capability to see the main menu
            $this->plugin_name,
            array( $this, 'display_dashboard_page' ),
            'dashicons-tablet', // Icon for the menu
            80 // Position in the menu order
        );

        // Dashboard Submenu
        add_submenu_page(
            $this->plugin_name,
            __( 'Dashboard', 'as-laburda-pwa-app' ),
            __( 'Dashboard', 'as-laburda-pwa-app' ),
            'read',
            $this->plugin_name,
            array( $this, 'display_dashboard_page' )
        );

        // App Builder Submenu
        if ( current_user_can( 'aslp_manage_apps' ) && ( $global_features['enable_app_builder'] ?? false ) ) {
            add_submenu_page(
                $this->plugin_name,
                __( 'App Builder', 'as-laburda-pwa-app' ),
                __( 'App Builder', 'as-laburda-pwa-app' ),
                'aslp_manage_apps',
                $this->plugin_name . '-app-builder',
                array( $this, 'display_app_builder_page' )
            );
        }

        // App Templates Submenu
        if ( current_user_can( 'aslp_manage_app_templates' ) && ( $global_features['enable_app_builder'] ?? false ) ) {
            add_submenu_page(
                $this->plugin_name,
                __( 'App Templates', 'as-laburda-pwa-app' ),
                __( 'App Templates', 'as-laburda-pwa-app' ),
                'aslp_manage_app_templates',
                $this->plugin_name . '-app-templates',
                array( $this, 'display_app_templates_page' )
            );
        }

        // App Menus Submenu
        if ( current_user_can( 'aslp_manage_app_menus' ) && ( $global_features['enable_menus'] ?? false ) ) {
            add_submenu_page(
                $this->plugin_name,
                __( 'App Menus', 'as-laburda-pwa-app' ),
                __( 'App Menus', 'as-laburda-pwa-app' ),
                'aslp_manage_app_menus',
                $this->plugin_name . '-menus',
                array( $this, 'display_app_menus_page' )
            );
        }

        // Business Listings Submenu
        if ( current_user_can( 'aslp_manage_all_business_listings' ) && ( $global_features['enable_business_listings'] ?? false ) ) {
            add_submenu_page(
                $this->plugin_name,
                __( 'Business Listings', 'as-laburda-pwa-app' ),
                __( 'Business Listings', 'as-laburda-pwa-app' ),
                'aslp_manage_all_business_listings',
                $this->plugin_name . '-listings',
                array( $this, 'display_business_listings_page' )
            );
        }

        // Listing Plans Submenu
        if ( current_user_can( 'aslp_manage_listing_plans' ) && ( $global_features['enable_listing_plans'] ?? false ) ) {
            add_submenu_page(
                $this->plugin_name,
                __( 'Listing Plans', 'as-laburda-pwa-app' ),
                __( 'Listing Plans', 'as-laburda-pwa-app' ),
                'aslp_manage_listing_plans',
                $this->plugin_name . '-plans',
                array( $this, 'display_listing_plans_page' )
            );
        }

        // Custom Fields Submenu
        if ( current_user_can( 'aslp_manage_custom_fields' ) && ( $global_features['enable_custom_fields'] ?? false ) ) {
            add_submenu_page(
                $this->plugin_name,
                __( 'Custom Fields', 'as-laburda-pwa-app' ),
                __( 'Custom Fields', 'as-laburda-pwa-app' ),
                'aslp_manage_custom_fields',
                $this->plugin_name . '-custom-fields',
                array( $this, 'display_custom_fields_page' )
            );
        }

        // Products Submenu
        if ( current_user_can( 'aslp_manage_products' ) && ( $global_features['enable_products'] ?? false ) ) {
            add_submenu_page(
                $this->plugin_name,
                __( 'Products', 'as-laburda-pwa-app' ),
                __( 'Products', 'as-laburda-pwa-app' ),
                'aslp_manage_products',
                $this->plugin_name . '-products',
                array( $this, 'display_products_page' )
            );
        }

        // Events Submenu
        if ( current_user_can( 'aslp_manage_events' ) && ( $global_features['enable_events'] ?? false ) ) {
            add_submenu_page(
                $this->plugin_name,
                __( 'Events', 'as-laburda-pwa-app' ),
                __( 'Events', 'as-laburda-pwa-app' ),
                'aslp_manage_events',
                $this->plugin_name . '-events',
                array( $this, 'display_events_page' )
            );
        }


        // Affiliate Program Submenu
        if ( current_user_can( 'aslp_manage_affiliates' ) && ( $global_features['enable_affiliates'] ?? false ) ) {
            add_submenu_page(
                $this->plugin_name,
                __( 'Affiliate Program', 'as-laburda-pwa-app' ),
                __( 'Affiliate Program', 'as-laburda-pwa-app' ),
                'aslp_manage_affiliates',
                $this->plugin_name . '-affiliates',
                array( $this, 'display_affiliate_program_page' )
            );
        }

        // Analytics Submenu
        if ( current_user_can( 'aslp_view_analytics' ) && ( $global_features['enable_analytics'] ?? false ) ) {
            add_submenu_page(
                $this->plugin_name,
                __( 'Analytics', 'as-laburda-pwa-app' ),
                __( 'Analytics', 'as-laburda-pwa-app' ),
                'aslp_view_analytics',
                $this->plugin_name . '-analytics',
                array( $this, 'display_analytics_page' )
            );
        }

        // AI Assistant Submenu
        if ( current_user_can( 'aslp_manage_ai_settings' ) && ( $global_features['enable_ai_agent'] ?? false ) ) {
            add_submenu_page(
                $this->plugin_name,
                __( 'AI Assistant', 'as-laburda-pwa-app' ),
                __( 'AI Assistant', 'as-laburda-pwa-app' ),
                'aslp_manage_ai_settings',
                $this->plugin_name . '-ai-assistant',
                array( $this, 'display_ai_assistant_page' )
            );
        }

        // Tools Submenu (for admin-only tools like page creation/fix)
        if ( current_user_can( 'manage_options' ) && ( $global_features['enable_tools_menu'] ?? false ) ) {
            add_submenu_page(
                $this->plugin_name,
                __( 'Tools', 'as-laburda-pwa-app' ),
                __( 'Tools', 'as-laburda-pwa-app' ),
                'manage_options', // Only administrators
                $this->plugin_name . '-tools',
                array( $this, 'display_tools_page' )
            );
        }

        // Global Settings Submenu
        if ( current_user_can( 'aslp_manage_global_settings' ) ) {
            add_submenu_page(
                $this->plugin_name,
                __( 'Global Settings', 'as-laburda-pwa-app' ),
                __( 'Global Settings', 'as-laburda-pwa-app' ),
                'aslp_manage_global_settings',
                $this->plugin_name . '-settings',
                array( $this, 'display_global_settings_page' )
            );
        }
    }

    /**
     * Display the plugin's main dashboard page.
     *
     * @since 1.0.0
     */
    public function display_dashboard_page() {
        include_once 'partials/as-laburda-pwa-app-admin-dashboard.php';
    }

    /**
     * Display the App Builder page.
     *
     * @since 2.0.0
     */
    public function display_app_builder_page() {
        if ( ! current_user_can( 'aslp_manage_apps' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'as-laburda-pwa-app' ) );
        }
        include_once 'partials/as-laburda-pwa-app-admin-app-builder.php';
    }

    /**
     * Display the App Templates page.
     *
     * @since 2.0.0
     */
    public function display_app_templates_page() {
        if ( ! current_user_can( 'aslp_manage_app_templates' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'as-laburda-pwa-app' ) );
        }
        include_once 'partials/as-laburda-pwa-app-admin-app-templates.php';
    }

    /**
     * Display the App Menus page.
     *
     * @since 1.0.0
     */
    public function display_app_menus_page() {
        if ( ! current_user_can( 'aslp_manage_app_menus' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'as-laburda-pwa-app' ) );
        }
        include_once 'partials/as-laburda-pwa-app-admin-app-menus.php';
    }

    /**
     * Display the Business Listings page.
     *
     * @since 1.0.0
     */
    public function display_business_listings_page() {
        if ( ! current_user_can( 'aslp_manage_all_business_listings' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'as-laburda-pwa-app' ) );
        }
        include_once 'partials/as-laburda-pwa-app-admin-business-listings.php';
    }

    /**
     * Display the Listing Plans page.
     *
     * @since 1.0.0
     */
    public function display_listing_plans_page() {
        if ( ! current_user_can( 'aslp_manage_listing_plans' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'as-laburda-pwa-app' ) );
        }
        include_once 'partials/as-laburda-pwa-app-admin-listing-plans.php';
    }

    /**
     * Display the Custom Fields page.
     *
     * @since 1.0.0
     */
    public function display_custom_fields_page() {
        if ( ! current_user_can( 'aslp_manage_custom_fields' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'as-laburda-pwa-app' ) );
        }
        include_once 'partials/as-laburda-pwa-app-admin-custom-fields.php';
    }

    /**
     * Display the Products page.
     *
     * @since 2.0.0
     */
    public function display_products_page() {
        if ( ! current_user_can( 'aslp_manage_products' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'as-laburda-pwa-app' ) );
        }
        include_once 'partials/as-laburda-pwa-app-admin-products.php';
    }

    /**
     * Display the Events page.
     *
     * @since 2.0.0
     */
    public function display_events_page() {
        if ( ! current_user_can( 'aslp_manage_events' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'as-laburda-pwa-app' ) );
        }
        include_once 'partials/as-laburda-pwa-app-admin-events.php';
    }


    /**
     * Display the Affiliate Program page.
     *
     * @since 2.0.0
     */
    public function display_affiliate_program_page() {
        if ( ! current_user_can( 'aslp_manage_affiliates' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'as-laburda-pwa-app' ) );
        }
        include_once 'partials/as-laburda-pwa-app-admin-affiliates.php';
    }

    /**
     * Display the Analytics page.
     *
     * @since 2.0.0
     */
    public function display_analytics_page() {
        if ( ! current_user_can( 'aslp_view_analytics' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'as-laburda-pwa-app' ) );
        }
        include_once 'partials/as-laburda-pwa-app-admin-analytics.php';
    }

    /**
     * Display the AI Assistant page.
     *
     * @since 2.0.0
     */
    public function display_ai_assistant_page() {
        if ( ! current_user_can( 'aslp_manage_ai_settings' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'as-laburda-pwa-app' ) );
        }
        include_once 'partials/as-laburda-pwa-app-admin-ai-assistant.php';
    }

    /**
     * Display the Tools page.
     *
     * @since 2.0.0
     */
    public function display_tools_page() {
        if ( ! current_user_can( 'manage_options' ) ) { // Only administrators
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'as-laburda-pwa-app' ) );
        }
        include_once 'partials/as-laburda-pwa-app-admin-tools.php';
    }

    /**
     * Display the Global Settings page.
     *
     * @since 2.0.0
     */
    public function display_global_settings_page() {
        if ( ! current_user_can( 'aslp_manage_global_settings' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'as-laburda-pwa-app' ) );
        }
        include_once 'partials/as-laburda-pwa-app-admin-global-settings.php';
    }

    /* --- AJAX Handlers (Admin Side) --- */

    /**
     * Handle AJAX request to get all PWA apps (for admin).
     *
     * @since 2.0.0
     */
    public function handle_get_all_apps() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_apps' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to view apps.', 'as-laburda-pwa-app' ) ) );
        }

        $apps = $this->main_plugin->get_app_builder_manager()->get_all_apps();
        wp_send_json_success( array( 'apps' => $apps ) );
    }

    /**
     * Handle AJAX request to create/update PWA app settings (for admin).
     *
     * @since 2.0.0
     */
    public function handle_create_update_app_admin() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_apps' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to manage app settings.', 'as-laburda-pwa-app' ) ) );
        }

        $app_uuid = sanitize_text_field( $_POST['app_uuid'] ?? '' );
        $app_data = json_decode( stripslashes( $_POST['app_data'] ?? '{}' ), true );

        if ( empty( $app_data ) ) {
            wp_send_json_error( array( 'message' => __( 'Missing app data.', 'as-laburda-pwa-app' ) ) );
        }

        // Admin can update any app, so pass admin's user ID for permissions
        $updated_uuid = $this->main_plugin->get_app_builder_manager()->create_update_app( get_current_user_id(), $app_data, $app_uuid );

        if ( $updated_uuid ) {
            wp_send_json_success( array( 'message' => __( 'App settings updated successfully.', 'as-laburda-pwa-app' ), 'app_uuid' => $updated_uuid ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to update app settings.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to delete a PWA app (for admin).
     *
     * @since 2.0.0
     */
    public function handle_delete_app_admin() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_apps' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to delete apps.', 'as-laburda-pwa-app' ) ) );
        }

        $app_uuid = sanitize_text_field( $_POST['app_uuid'] ?? '' );

        if ( empty( $app_uuid ) ) {
            wp_send_json_error( array( 'message' => __( 'App UUID is required.', 'as-laburda-pwa-app' ) ) );
        }

        // Admin can delete any app, pass admin's user ID for permissions
        $deleted = $this->main_plugin->get_app_builder_manager()->delete_app( $app_uuid, get_current_user_id() );

        if ( $deleted ) {
            wp_send_json_success( array( 'message' => __( 'App deleted successfully.', 'as-laburda-pwa-app' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete app.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to get all app templates (for admin).
     *
     * @since 2.0.0
     */
    public function handle_get_all_app_templates() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_app_templates' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to view app templates.', 'as-laburda-pwa-app' ) ) );
        }

        $templates = $this->main_plugin->get_app_builder_manager()->get_app_templates( false ); // Get all, including inactive
        wp_send_json_success( array( 'templates' => $templates ) );
    }

    /**
     * Handle AJAX request to add/update an app template (for admin).
     *
     * @since 2.0.0
     */
    public function handle_add_update_app_template() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_app_templates' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to manage app templates.', 'as-laburda-pwa-app' ) ) );
        }

        $template_data_raw = json_decode( stripslashes( $_POST['template_data'] ?? '{}' ), true );
        $template_id = absint( $_POST['template_id'] ?? 0 );

        if ( empty( $template_data_raw['template_name'] ) || empty( $template_data_raw['features'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Template name and features are required.', 'as-laburda-pwa-app' ) ) );
        }

        $data_to_save = array(
            'template_name'     => sanitize_text_field( $template_data_raw['template_name'] ),
            'description'       => sanitize_textarea_field( $template_data_raw['description'] ?? '' ),
            'template_data'     => AS_Laburda_PWA_App_Utils::safe_json_encode( $template_data_raw['features'] ), // Ensure features is JSON string
            'preview_image_url' => esc_url_raw( $template_data_raw['preview_image_url'] ?? '' ),
            'is_active'         => isset( $template_data_raw['is_active'] ) ? (bool) $template_data_raw['is_active'] : false,
        );

        if ( $template_id ) {
            $updated = $this->main_plugin->get_database_manager()->update_app_template( $template_id, $data_to_save );
            if ( $updated !== false ) {
                wp_send_json_success( array( 'message' => __( 'App template updated successfully.', 'as-laburda-pwa-app' ), 'template_id' => $template_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to update app template.', 'as-laburda-pwa-app' ) ) );
            }
        } else {
            $inserted_id = $this->main_plugin->get_database_manager()->add_app_template( $data_to_save );
            if ( $inserted_id ) {
                wp_send_json_success( array( 'message' => __( 'App template added successfully.', 'as-laburda-pwa-app' ), 'template_id' => $inserted_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to add app template.', 'as-laburda-pwa-app' ) ) );
            }
        }
    }

    /**
     * Handle AJAX request to delete an app template (for admin).
     *
     * @since 2.0.0
     */
    public function handle_delete_app_template() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_app_templates' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to delete app templates.', 'as-laburda-pwa-app' ) ) );
        }

        $template_id = absint( $_POST['template_id'] ?? 0 );

        if ( empty( $template_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Template ID is required.', 'as-laburda-pwa-app' ) ) );
        }

        $deleted = $this->main_plugin->get_database_manager()->delete_app_template( $template_id );

        if ( $deleted ) {
            wp_send_json_success( array( 'message' => __( 'App template deleted successfully.', 'as-laburda-pwa-app' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete app template.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to get all app menus (for admin).
     *
     * @since 1.0.0
     */
    public function handle_get_all_app_menus() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_app_menus' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to view app menus.', 'as-laburda-pwa-app' ) ) );
        }

        $menus = $this->main_plugin->get_menus_manager()->get_all_app_menus( false ); // Get all, including inactive
        wp_send_json_success( array( 'menus' => $menus ) );
    }

    /**
     * Handle AJAX request to add/update an app menu (for admin).
     *
     * @since 1.0.0
     */
    public function handle_add_update_app_menu() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_app_menus' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to manage app menus.', 'as-laburda-pwa-app' ) ) );
        }

        $menu_data = json_decode( stripslashes( $_POST['menu_data'] ?? '{}' ), true );
        $menu_id = absint( $_POST['menu_id'] ?? 0 );

        if ( empty( $menu_data['menu_name'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Menu name is required.', 'as-laburda-pwa-app' ) ) );
        }

        $data_to_save = array(
            'menu_name'   => sanitize_text_field( $menu_data['menu_name'] ),
            'description' => sanitize_textarea_field( $menu_data['description'] ?? '' ),
            'menu_items'  => AS_Laburda_PWA_App_Utils::safe_json_encode( $menu_data['menu_items'] ?? array() ),
            'is_active'   => isset( $menu_data['is_active'] ) ? (bool) $menu_data['is_active'] : false,
        );

        if ( $menu_id ) {
            $updated = $this->main_plugin->get_menus_manager()->update_menu( $menu_id, $data_to_save );
            if ( $updated !== false ) {
                wp_send_json_success( array( 'message' => __( 'App menu updated successfully.', 'as-laburda-pwa-app' ), 'menu_id' => $menu_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to update app menu.', 'as-laburda-pwa-app' ) ) );
            }
        } else {
            $inserted_id = $this->main_plugin->get_menus_manager()->add_menu( $data_to_save );
            if ( $inserted_id ) {
                wp_send_json_success( array( 'message' => __( 'App menu added successfully.', 'as-laburda-pwa-app' ), 'menu_id' => $inserted_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to add app menu.', 'as-laburda-pwa-app' ) ) );
            }
        }
    }

    /**
     * Handle AJAX request to delete an app menu (for admin).
     *
     * @since 1.0.0
     */
    public function handle_delete_app_menu() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_app_menus' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to delete app menus.', 'as-laburda-pwa-app' ) ) );
        }

        $menu_id = absint( $_POST['menu_id'] ?? 0 );

        if ( empty( $menu_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Menu ID is required.', 'as-laburda-pwa-app' ) ) );
        }

        $deleted = $this->main_plugin->get_menus_manager()->delete_menu( $menu_id );

        if ( $deleted ) {
            wp_send_json_success( array( 'message' => __( 'App menu deleted successfully.', 'as-laburda-pwa-app' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete app menu.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to get all custom fields (for admin).
     *
     * @since 1.0.0
     */
    public function handle_get_all_custom_fields() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_custom_fields' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to view custom fields.', 'as-laburda-pwa-app' ) ) );
        }

        $fields = $this->main_plugin->get_database_manager()->get_all_custom_fields( array( 'is_active' => false ) ); // Get all, including inactive
        wp_send_json_success( array( 'fields' => $fields ) );
    }

    /**
     * Handle AJAX request to add/update a custom field (for admin).
     *
     * @since 1.0.0
     */
    public function handle_add_update_custom_field() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_custom_fields' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to manage custom fields.', 'as-laburda-pwa-app' ) ) );
        }

        $field_data = json_decode( stripslashes( $_POST['field_data'] ?? '{}' ), true );
        $field_id = absint( $_POST['field_id'] ?? 0 );

        if ( empty( $field_data['field_name'] ) || empty( $field_data['field_type'] ) || empty( $field_data['applies_to'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Field name, type, and application are required.', 'as-laburda-pwa-app' ) ) );
        }

        $data_to_save = array(
            'field_name'    => sanitize_text_field( $field_data['field_name'] ),
            'field_slug'    => sanitize_title( $field_data['field_slug'] ?? $field_data['field_name'] ),
            'field_type'    => sanitize_text_field( $field_data['field_type'] ),
            'field_options' => AS_Laburda_PWA_App_Utils::safe_json_encode( $field_data['field_options'] ?? array() ),
            'applies_to'    => sanitize_text_field( $field_data['applies_to'] ),
            'is_required'   => isset( $field_data['is_required'] ) ? (bool) $field_data['is_required'] : false,
            'is_active'     => isset( $field_data['is_active'] ) ? (bool) $field_data['is_active'] : false,
        );

        if ( $field_id ) {
            $updated = $this->main_plugin->get_database_manager()->update_custom_field( $field_id, $data_to_save );
            if ( $updated !== false ) {
                wp_send_json_success( array( 'message' => __( 'Custom field updated successfully.', 'as-laburda-pwa-app' ), 'field_id' => $field_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to update custom field.', 'as-laburda-pwa-app' ) ) );
            }
        } else {
            $inserted_id = $this->main_plugin->get_database_manager()->add_custom_field( $data_to_save );
            if ( $inserted_id ) {
                wp_send_json_success( array( 'message' => __( 'Custom field added successfully.', 'as-laburda-pwa-app' ), 'field_id' => $inserted_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to add custom field.', 'as-laburda-pwa-app' ) ) );
            }
        }
    }

    /**
     * Handle AJAX request to delete a custom field (for admin).
     *
     * @since 1.0.0
     */
    public function handle_delete_custom_field() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_custom_fields' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to delete custom fields.', 'as-laburda-pwa-app' ) ) );
        }

        $field_id = absint( $_POST['field_id'] ?? 0 );

        if ( empty( $field_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Field ID is required.', 'as-laburda-pwa-app' ) ) );
        }

        $deleted = $this->main_plugin->get_database_manager()->delete_custom_field( $field_id );

        if ( $deleted ) {
            wp_send_json_success( array( 'message' => __( 'Custom field deleted successfully.', 'as-laburda-pwa-app' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete custom field.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to get global settings.
     *
     * @since 2.0.0
     */
    public function handle_get_global_settings() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_global_settings' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to view global settings.', 'as-laburda-pwa-app' ) ) );
        }

        $settings = $this->main_plugin->get_global_feature_settings();
        wp_send_json_success( array( 'settings' => $settings ) );
    }

    /**
     * Handle AJAX request to update global settings.
     *
     * @since 2.0.0
     */
    public function handle_update_global_settings() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_global_settings' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to update global settings.', 'as-laburda-pwa-app' ) ) );
        }

        $new_settings_raw = json_decode( stripslashes( $_POST['settings_data'] ?? '{}' ), true );
        $sanitized_settings = array();

        foreach ( $new_settings_raw as $key => $value ) {
            // Sanitize boolean values
            if ( in_array( $key, array(
                'enable_app_builder', 'enable_affiliates', 'enable_analytics', 'enable_ai_agent',
                'enable_business_listings', 'enable_products', 'enable_events',
                'enable_notifications', 'enable_menus', 'enable_custom_fields',
                'enable_listing_plans', 'enable_seo_tools', 'enable_tools_menu'
            ) ) ) {
                $sanitized_settings[ $key ] = (bool) $value;
            } elseif ( $key === 'ai_api_key' || $key === 'ai_api_endpoint' ) {
                $sanitized_settings[ $key ] = sanitize_text_field( $value );
            }
            // Add more specific sanitization for other settings if needed
        }

        $updated = $this->main_plugin->update_global_feature_settings( $sanitized_settings );

        if ( $updated !== false ) {
            wp_send_json_success( array( 'message' => __( 'Global settings updated successfully.', 'as-laburda-pwa-app' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to update global settings.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to get all affiliates (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_get_affiliates() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_affiliates' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to view affiliates.', 'as-laburda-pwa-app' ) ) );
        }

        $affiliates = $this->main_plugin->get_affiliates_manager()->get_all_affiliates();
        wp_send_json_success( array( 'affiliates' => $affiliates ) );
    }

    /**
     * Handle AJAX request to update affiliate status (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_update_affiliate_status() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_affiliates' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to update affiliate status.', 'as-laburda-pwa-app' ) ) );
        }

        $affiliate_id = absint( $_POST['affiliate_id'] ?? 0 );
        $status = sanitize_text_field( $_POST['status'] ?? '' );
        $tier_id = absint( $_POST['tier_id'] ?? 0 ); // Optional: for assigning tier on activation

        if ( empty( $affiliate_id ) || empty( $status ) ) {
            wp_send_json_error( array( 'message' => __( 'Missing affiliate ID or status.', 'as-laburda-pwa-app' ) ) );
        }

        $data_to_update = array( 'affiliate_status' => $status );

        // If activating, ensure a tier is assigned
        if ( 'active' === $status && $tier_id > 0 ) {
            $data_to_update['current_tier_id'] = $tier_id;
        }

        $updated = $this->main_plugin->get_database_manager()->update_affiliate( $affiliate_id, $data_to_update );

        if ( $updated !== false ) {
            // If activating, grant affiliate capabilities to the user
            if ( 'active' === $status ) {
                $affiliate_obj = $this->main_plugin->get_database_manager()->get_affiliate( $affiliate_id );
                if ( $affiliate_obj ) {
                    $user = new WP_User( $affiliate_obj->user_id );
                    if ( ! $user->has_cap( 'aslp_view_affiliate_dashboard' ) ) {
                        $user->add_cap( 'aslp_view_affiliate_dashboard' );
                        $user->add_cap( 'aslp_request_payout' );
                    }
                }
            }
            wp_send_json_success( array( 'message' => __( 'Affiliate status updated successfully.', 'as-laburda-pwa-app' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to update affiliate status.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to manage affiliate commissions (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_manage_commissions() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_affiliate_commissions' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to manage commissions.', 'as-laburda-pwa-app' ) ) );
        }

        $sub_action = sanitize_text_field( $_POST['sub_action'] ?? '' );

        if ( 'get_all' === $sub_action ) {
            $commissions = $this->main_plugin->get_database_manager()->get_all_affiliate_commissions();
            wp_send_json_success( array( 'commissions' => $commissions ) );
        } elseif ( 'update_status' === $sub_action ) {
            $commission_id = absint( $_POST['commission_id'] ?? 0 );
            $status = sanitize_text_field( $_POST['status'] ?? '' ); // 'approved', 'rejected'

            if ( empty( $commission_id ) || empty( $status ) ) {
                wp_send_json_error( array( 'message' => __( 'Missing commission ID or status.', 'as-laburda-pwa-app' ) ) );
            }

            $commission = $this->main_plugin->get_database_manager()->get_affiliate_commission( $commission_id );
            if ( ! $commission ) {
                wp_send_json_error( array( 'message' => __( 'Commission not found.', 'as-laburda-pwa-app' ) ) );
            }

            $updated = $this->main_plugin->get_database_manager()->update_affiliate_commission( $commission_id, array( 'commission_status' => $status ) );

            if ( $updated !== false ) {
                // Update affiliate's wallet balance if commission is approved
                if ( 'approved' === $status ) {
                    $this->main_plugin->get_database_manager()->update_affiliate_wallet_balance( $commission->affiliate_id, $commission->commission_amount, 'add' );
                }
                wp_send_json_success( array( 'message' => __( 'Commission status updated successfully.', 'as-laburda-pwa-app' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to update commission status.', 'as-laburda-pwa-app' ) ) );
            }
        } else {
            wp_send_json_error( array( 'message' => __( 'Invalid sub-action for commission management.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to manage affiliate payouts (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_manage_payouts() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_affiliate_payouts' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to manage payouts.', 'as-laburda-pwa-app' ) ) );
        }

        $sub_action = sanitize_text_field( $_POST['sub_action'] ?? '' );

        if ( 'get_all' === $sub_action ) {
            $payouts = $this->main_plugin->get_database_manager()->get_all_affiliate_payouts();
            wp_send_json_success( array( 'payouts' => $payouts ) );
        } elseif ( 'update_status' === $sub_action ) {
            $payout_id = absint( $_POST['payout_id'] ?? 0 );
            $status = sanitize_text_field( $_POST['status'] ?? '' ); // 'completed', 'cancelled'
            $transaction_id = sanitize_text_field( $_POST['transaction_id'] ?? '' ); // Optional transaction ID

            if ( empty( $payout_id ) || empty( $status ) ) {
                wp_send_json_error( array( 'message' => __( 'Missing payout ID or status.', 'as-laburda-pwa-app' ) ) );
            }

            $payout = $this->main_plugin->get_database_manager()->get_affiliate_payout( $payout_id );
            if ( ! $payout ) {
                wp_send_json_error( array( 'message' => __( 'Payout not found.', 'as-laburda-pwa-app' ) ) );
            }

            $data_to_update = array( 'payout_status' => $status );
            if ( 'completed' === $status ) {
                $data_to_update['date_completed'] = current_time( 'mysql' );
                $data_to_update['transaction_id'] = $transaction_id;
            } elseif ( 'cancelled' === $status ) {
                // If cancelled, return funds to wallet
                $this->main_plugin->get_database_manager()->update_affiliate_wallet_balance( $payout->affiliate_id, $payout->payout_amount, 'add' );
            }

            $updated = $this->main_plugin->get_database_manager()->update_affiliate_payout( $payout_id, $data_to_update );

            if ( $updated !== false ) {
                wp_send_json_success( array( 'message' => __( 'Payout status updated successfully.', 'as-laburda-pwa-app' ) ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to update payout status.', 'as-laburda-pwa-app' ) ) );
            }
        } else {
            wp_send_json_error( array( 'message' => __( 'Invalid sub-action for payout management.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to get all affiliate creatives (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_get_affiliate_creatives() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_affiliate_creatives' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to view affiliate creatives.', 'as-laburda-pwa-app' ) ) );
        }

        // Allow fetching all creatives regardless of tier for admin view
        $creatives = $this->main_plugin->get_database_manager()->get_all_affiliate_creatives( false );
        wp_send_json_success( array( 'creatives' => $creatives ) );
    }

    /**
     * Handle AJAX request to add/update an affiliate creative (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_create_update_affiliate_creative() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_affiliate_creatives' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to manage affiliate creatives.', 'as-laburda-pwa-app' ) ) );
        }

        $creative_data_raw = json_decode( stripslashes( $_POST['creative_data'] ?? '{}' ), true );
        $creative_id = absint( $_POST['creative_id'] ?? 0 );

        if ( empty( $creative_data_raw['creative_name'] ) || empty( $creative_data_raw['creative_type'] ) || empty( $creative_data_raw['content'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Creative name, type, and content are required.', 'as-laburda-pwa-app' ) ) );
        }

        $data_to_save = array(
            'creative_name' => sanitize_text_field( $creative_data_raw['creative_name'] ),
            'description'   => sanitize_textarea_field( $creative_data_raw['description'] ?? '' ), // Assuming description field is added
            'creative_type' => sanitize_text_field( $creative_data_raw['creative_type'] ),
            'content'       => wp_kses_post( $creative_data_raw['content'] ), // Allow HTML for banners/HTML code
            'preview_url'   => esc_url_raw( $creative_data_raw['preview_url'] ?? '' ), // Assuming preview_url field is added
            'is_active'     => isset( $creative_data_raw['is_active'] ) ? (bool) $creative_data_raw['is_active'] : false,
            'tier_id'       => absint( $creative_data_raw['tier_id'] ?? 0 ), // Associate with a tier
        );

        if ( $creative_id ) {
            $updated = $this->main_plugin->get_database_manager()->update_affiliate_creative( $creative_id, $data_to_save );
            if ( $updated !== false ) {
                wp_send_json_success( array( 'message' => __( 'Affiliate creative updated successfully.', 'as-laburda-pwa-app' ), 'creative_id' => $creative_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to update affiliate creative.', 'as-laburda-pwa-app' ) ) );
            }
        } else {
            $inserted_id = $this->main_plugin->get_database_manager()->add_affiliate_creative( $data_to_save );
            if ( $inserted_id ) {
                wp_send_json_success( array( 'message' => __( 'Affiliate creative added successfully.', 'as-laburda-pwa-app' ), 'creative_id' => $inserted_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to add affiliate creative.', 'as-laburda-pwa-app' ) ) );
            }
        }
    }

    /**
     * Handle AJAX request to delete an affiliate creative (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_delete_affiliate_creative() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_affiliate_creatives' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to delete affiliate creatives.', 'as-laburda-pwa-app' ) ) );
        }

        $creative_id = absint( $_POST['creative_id'] ?? 0 );

        if ( empty( $creative_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Creative ID is required.', 'as-laburda-pwa-app' ) ) );
        }

        $deleted = $this->main_plugin->get_database_manager()->delete_affiliate_creative( $creative_id );

        if ( $deleted ) {
            wp_send_json_success( array( 'message' => __( 'Affiliate creative deleted successfully.', 'as-laburda-pwa-app' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete affiliate creative.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to get all affiliate tiers (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_get_affiliate_tiers() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_affiliate_tiers' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to view affiliate tiers.', 'as-laburda-pwa-app' ) ) );
        }

        $tiers = $this->main_plugin->get_affiliates_manager()->get_all_affiliate_tiers( false ); // Get all, including inactive
        wp_send_json_success( array( 'tiers' => $tiers ) );
    }

    /**
     * Handle AJAX request to add/update an affiliate tier (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_add_update_affiliate_tier() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_affiliate_tiers' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to manage affiliate tiers.', 'as-laburda-pwa-app' ) ) );
        }

        $tier_data = json_decode( stripslashes( $_POST['tier_data'] ?? '{}' ), true );
        $tier_id = absint( $_POST['tier_id'] ?? 0 );

        if ( empty( $tier_data['tier_name'] ) || ! isset( $tier_data['base_commission_rate'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Tier name and base commission rate are required.', 'as-laburda-pwa-app' ) ) );
        }

        $data_to_save = array(
            'tier_name'          => sanitize_text_field( $tier_data['tier_name'] ),
            'description'        => sanitize_textarea_field( $tier_data['description'] ?? '' ),
            'base_commission_rate' => floatval( $tier_data['base_commission_rate'] ),
            'mlm_commission_rate'  => floatval( $tier_data['mlm_commission_rate'] ?? 0 ),
            'is_active'          => isset( $tier_data['is_active'] ) ? (bool) $tier_data['is_active'] : false,
        );

        if ( $tier_id ) {
            $updated = $this->main_plugin->get_database_manager()->update_affiliate_tier( $tier_id, $data_to_save );
            if ( $updated !== false ) {
                wp_send_json_success( array( 'message' => __( 'Affiliate tier updated successfully.', 'as-laburda-pwa-app' ), 'tier_id' => $tier_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to update affiliate tier.', 'as-laburda-pwa-app' ) ) );
            }
        } else {
            $inserted_id = $this->main_plugin->get_database_manager()->add_affiliate_tier( $data_to_save );
            if ( $inserted_id ) {
                wp_send_json_success( array( 'message' => __( 'Affiliate tier added successfully.', 'as-laburda-pwa-app' ), 'tier_id' => $inserted_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to add affiliate tier.', 'as-laburda-pwa-app' ) ) );
            }
        }
    }

    /**
     * Handle AJAX request to delete an affiliate tier (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_delete_affiliate_tier() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_manage_affiliate_tiers' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to delete affiliate tiers.', 'as-laburda-pwa-app' ) ) );
        }

        $tier_id = absint( $_POST['tier_id'] ?? 0 );

        if ( empty( $tier_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Tier ID is required.', 'as-laburda-pwa-app' ) ) );
        }

        // Prevent deletion of tiers that have active affiliates assigned
        $affiliates_in_tier = $this->main_plugin->get_database_manager()->get_all_affiliates( array( 'current_tier_id' => $tier_id ) );
        if ( ! empty( $affiliates_in_tier ) ) {
            wp_send_json_error( array( 'message' => __( 'Cannot delete tier: affiliates are currently assigned to this tier. Please reassign them first.', 'as-laburda-pwa-app' ) ) );
        }

        $deleted = $this->main_plugin->get_database_manager()->delete_affiliate_tier( $tier_id );

        if ( $deleted ) {
            wp_send_json_success( array( 'message' => __( 'Affiliate tier deleted successfully.', 'as-laburda-pwa-app' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete affiliate tier.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to get analytics data (for admin).
     * This function now supports fetching overall analytics or analytics for a specific item.
     *
     * @since 2.0.0
     */
    public function handle_get_analytics_data() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_view_analytics' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to view analytics.', 'as-laburda-pwa-app' ) ) );
        }

        $item_id = sanitize_text_field( $_POST['item_id'] ?? '' );
        $item_type = sanitize_text_field( $_POST['item_type'] ?? '' );
        $period = sanitize_text_field( $_POST['period'] ?? 'total' ); // 'daily', 'weekly', 'monthly', 'total'

        // If item_id and item_type are empty, fetch overall analytics.
        // Otherwise, fetch analytics for the specific item.
        $analytics_data = $this->main_plugin->get_analytics_manager()->get_analytics_data( $item_id, $item_type, $period );

        if ( $analytics_data ) {
            wp_send_json_success( $analytics_data );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to retrieve analytics data.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request for AI chat (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_ai_chat() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_use_ai_tools' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to use AI chat.', 'as-laburda-pwa-app' ) ) );
        }

        $user_message = sanitize_textarea_field( $_POST['message'] ?? '' );

        if ( empty( $user_message ) ) {
            wp_send_json_error( array( 'message' => __( 'Message cannot be empty.', 'as-laburda-pwa-app' ) ) );
        }

        $ai_response = $this->main_plugin->get_ai_agent()->chat_with_ai( $user_message );

        if ( $ai_response ) {
            wp_send_json_success( array( 'response' => $ai_response ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to get AI response. Check AI settings and API key.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to generate SEO with AI (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_ai_generate_seo() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_use_ai_tools' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to generate SEO with AI.', 'as-laburda-pwa-app' ) ) );
        }

        $item_id = sanitize_text_field( $_POST['item_id'] ?? '' );
        $item_type = sanitize_text_field( $_POST['item_type'] ?? '' );
        $content_to_analyze = sanitize_textarea_field( $_POST['content_to_analyze'] ?? '' );

        if ( empty( $item_id ) || empty( $item_type ) || empty( $content_to_analyze ) ) {
            wp_send_json_error( array( 'message' => __( 'Missing item ID, type, or content for SEO generation.', 'as-laburda-pwa-app' ) ) );
        }

        $seo_data = $this->main_plugin->get_ai_agent()->generate_seo_for_item( $item_type, $item_id, $content_to_analyze );

        if ( $seo_data ) {
            wp_send_json_success( array( 'message' => __( 'SEO data generated and saved successfully!', 'as-laburda-pwa-app' ), 'seo_data' => $seo_data ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to generate SEO data. Check AI settings and API key.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to create content with AI (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_ai_create_content() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_use_ai_tools' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to create content with AI.', 'as-laburda-pwa-app' ) ) );
        }

        $content_type = sanitize_text_field( $_POST['content_type'] ?? '' );
        $prompt = sanitize_textarea_field( $_POST['prompt'] ?? '' );

        if ( empty( $content_type ) || empty( $prompt ) ) {
            wp_send_json_error( array( 'message' => __( 'Content type and prompt are required.', 'as-laburda-pwa-app' ) ) );
        }

        $generated_content = $this->main_plugin->get_ai_agent()->generate_content( $content_type, $prompt );

        if ( $generated_content ) {
            wp_send_json_success( array( 'message' => __( 'Content generated successfully!', 'as-laburda-pwa-app' ), 'content' => $generated_content ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to generate content. Check AI settings and API key.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to debug site with AI (for admin).
     *
     * @since 2.0.0
     */
    public function handle_admin_ai_debug_site() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'aslp_use_ai_tools' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to debug site with AI.', 'as-laburda-pwa-app' ) ) );
        }

        $debug_info = sanitize_textarea_field( $_POST['debug_info'] ?? '' );

        if ( empty( $debug_info ) ) {
            wp_send_json_error( array( 'message' => __( 'Debug information is required for AI analysis.', 'as-laburda-pwa-app' ) ) );
        }

        $ai_report_json = $this->main_plugin->get_ai_agent()->debug_site_with_ai( $debug_info );

        if ( $ai_report_json ) {
            $ai_report = AS_Laburda_PWA_App_Utils::safe_json_decode( $ai_report_json, true );
            wp_send_json_success( array( 'message' => __( 'AI debug report generated successfully!', 'as-laburda-pwa-app' ), 'report' => $ai_report ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to generate AI debug report. Check AI settings and API key.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to create missing essential pages.
     * This is a tool function, typically for administrators.
     *
     * @since 2.0.0
     */
    public function handle_admin_create_missing_pages() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to create pages.', 'as-laburda-pwa-app' ) ) );
        }

        $pages_to_create = array(
            'app-dashboard' => array(
                'title'   => __( 'App Dashboard', 'as-laburda-pwa-app' ),
                'content' => '[aslp_app_builder]', // Example shortcode
                'option_key' => 'aslp_app_dashboard_page_id',
            ),
            'business-listings' => array(
                'title'   => __( 'Business Listings', 'as-laburda-pwa-app' ),
                'content' => '[aslp_business_listings_archive]', // Placeholder for a future archive shortcode
                'option_key' => 'aslp_business_listings_page_id',
            ),
            'affiliate-dashboard' => array(
                'title'   => __( 'Affiliate Dashboard', 'as-laburda-pwa-app' ),
                'content' => '[aslp_affiliate_dashboard]',
                'option_key' => 'aslp_affiliate_dashboard_page_id',
            ),
            'offline-page' => array(
                'title'   => __( 'Offline Page', 'as-laburda-pwa-app' ),
                'content' => __( 'You are currently offline. Please check your internet connection.', 'as-laburda-pwa-app' ),
                'option_key' => 'aslp_offline_page_id',
            ),
            'login-page' => array(
                'title'   => __( 'Login / Register', 'as-laburda-pwa-app' ),
                'content' => '[aslp_login_register_form]', // Placeholder for a custom login/register shortcode
                'option_key' => 'aslp_login_page_id',
            ),
        );

        $created_count = 0;
        $updated_options = array();

        foreach ( $pages_to_create as $slug => $page_info ) {
            $page_title = $page_info['title'];
            $page_content = $page_info['content'];
            $option_key = $page_info['option_key'];

            // Check if page already exists
            $existing_page = get_page_by_title( $page_title );

            if ( ! $existing_page ) {
                $page_id = wp_insert_post( array(
                    'post_title'    => $page_title,
                    'post_content'  => $page_content,
                    'post_status'   => 'publish',
                    'post_type'     => 'page',
                    'post_name'     => $slug,
                ) );

                if ( $page_id && ! is_wp_error( $page_id ) ) {
                    update_option( $option_key, $page_id );
                    $updated_options[ $option_key ] = $page_id;
                    $created_count++;
                } else {
                    error_log( 'ASLP Tools: Failed to create page ' . $page_title . ': ' . ( is_wp_error( $page_id ) ? $page_id->get_error_message() : 'Unknown error' ) );
                }
            } else {
                // If page exists, ensure option is set
                if ( get_option( $option_key ) != $existing_page->ID ) {
                    update_option( $option_key, $existing_page->ID );
                    $updated_options[ $option_key ] = $existing_page->ID;
                }
            }
        }

        if ( $created_count > 0 ) {
            wp_send_json_success( array( 'message' => sprintf( __( '%d missing essential pages created and plugin options updated.', 'as-laburda-pwa-app' ), $created_count ), 'updated_options' => $updated_options ) );
        } else {
            wp_send_json_success( array( 'message' => __( 'All essential pages already exist or could not be created. Plugin options updated if necessary.', 'as-laburda-pwa-app' ), 'updated_options' => $updated_options ) );
        }
    }

    /**
     * Handle AJAX request to get duplicated pages.
     * This is a tool function, typically for administrators.
     *
     * @since 2.0.0
     */
    public function handle_admin_get_duplicated_pages() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to check for duplicated pages.', 'as-laburda-pwa-app' ) ) );
        }

        global $wpdb;
        $table_name = $wpdb->posts;

        $sql = "SELECT post_title, COUNT(ID) as count, GROUP_CONCAT(ID) as ids
                FROM $table_name
                WHERE post_type = 'page' AND post_status = 'publish'
                GROUP BY post_title
                HAVING COUNT(ID) > 1";

        $duplicates = $wpdb->get_results( $sql );

        if ( ! empty( $duplicates ) ) {
            wp_send_json_success( array( 'message' => __( 'Duplicated pages found.', 'as-laburda-pwa-app' ), 'duplicates' => $duplicates ) );
        } else {
            wp_send_json_success( array( 'message' => __( 'No duplicated pages found.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to fix duplicated pages.
     * This is a tool function, typically for administrators.
     * It will delete all but the oldest version of duplicated pages.
     *
     * @since 2.0.0
     */
    public function handle_admin_fix_duplicated_pages() {
        check_ajax_referer( 'aslp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to fix duplicated pages.', 'as-laburda-pwa-app' ) ) );
        }

        global $wpdb;
        $table_name = $wpdb->posts;

        $sql = "SELECT post_title, GROUP_CONCAT(ID ORDER BY post_date ASC) as sorted_ids
                FROM $table_name
                WHERE post_type = 'page' AND post_status = 'publish'
                GROUP BY post_title
                HAVING COUNT(ID) > 1";

        $duplicates = $wpdb->get_results( $sql );
        $deleted_count = 0;
        $deleted_titles = array();

        foreach ( $duplicates as $duplicate ) {
            $ids = explode( ',', $duplicate->sorted_ids );
            $keep_id = array_shift( $ids ); // Keep the oldest ID

            foreach ( $ids as $delete_id ) {
                if ( wp_delete_post( $delete_id, true ) ) { // true for force delete
                    $deleted_count++;
                }
            }
            $deleted_titles[] = $duplicate->post_title;
        }

        if ( $deleted_count > 0 ) {
            wp_send_json_success( array( 'message' => sprintf( __( '%d duplicated pages deleted. Titles: %s', 'as-laburda-pwa-app' ), $deleted_count, implode( ', ', $deleted_titles ) ) ) );
        } else {
            wp_send_json_success( array( 'message' => __( 'No duplicated pages found to fix.', 'as-laburda-pwa-app' ) ) );
        }
    }
}
