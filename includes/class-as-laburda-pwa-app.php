<?php
/**
 * The file that defines the core plugin class.
 *
 * A class definition that holds all of the stylesheet and script registration
 * and enqueuing for the plugin.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      AS_Laburda_PWA_App_Loader    $loader    Orchestrates the hooks of the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * The database manager instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_Database    $database_manager    Manages database interactions.
     */
    private $database_manager;

    /**
     * The app builder manager instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_App_Builder    $app_builder_manager    Manages PWA app creation and settings.
     */
    private $app_builder_manager;

    /**
     * The listing plans manager instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_Listing_Plans    $listing_plans_manager    Manages listing plans.
     */
    private $listing_plans_manager;

    /**
     * The memberships manager instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_Memberships    $memberships_manager    Manages user memberships and claims.
     */
    private $memberships_manager;

    /**
     * The notifications manager instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_Notifications    $notifications_manager    Manages notifications.
     */
    private $notifications_manager;

    /**
     * The products manager instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_Products    $products_manager    Manages products for business listings.
     */
    private $products_manager;

    /**
     * The events manager instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_Events    $events_manager    Manages events for business listings.
     */
    private $events_manager;

    /**
     * The menus manager instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_Menus    $menus_manager    Manages app menus.
     */
    private $menus_manager;

    /**
     * The affiliates manager instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_Affiliates    $affiliates_manager    Manages the affiliate program.
     */
    private $affiliates_manager;

    /**
     * The analytics manager instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_Analytics    $analytics_manager    Manages analytics tracking.
     */
    private $analytics_manager;

    /**
     * The AI Agent instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_AI_Agent    $ai_agent    Provides AI functionalities.
     */
    private $ai_agent;

    /**
     * The SEO manager instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_SEO    $seo_manager    Manages SEO.
     */
    private $seo_manager;

    /**
     * The App Plans manager instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_App_Plans    $app_plans_manager    Manages App Plans.
     */
    private $app_plans_manager;


    /**
     * Global plugin feature settings.
     *
     * @since    2.0.0
     * @access   private
     * @var      array    $global_feature_settings    Array of global feature settings.
     */
    private $global_feature_settings;

    /**
     * The single instance of the class.
     *
     * @since 2.0.0
     * @static
     * @var AS_Laburda_PWA_App $instance The single instance of the class.
     */
    private static $instance;

    /**
     * Returns the single instance of the class.
     *
     * @since 2.0.0
     * @static
     * @return AS_Laburda_PWA_App The single instance of the class.
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     * @since    2.0.0 Added new managers and global settings.
     */
    private function __construct() {
        if ( defined( 'AS_LABURDA_PWA_APP_VERSION' ) ) {
            $this->version = AS_LABURDA_PWA_APP_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'as-laburda-pwa-app';

        // Initialize loader first, as it's a dependency for other methods like set_locale
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-loader.php';
        $this->loader = new AS_Laburda_PWA_App_Loader();

        // Load other dependencies
        $this->load_dependencies();

        // Now that loader and dependencies are loaded, proceed with initialization
        $this->set_locale();        // Set locale using the now-initialized loader
        $this->load_managers();     // Initialize manager objects
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->add_roles_and_capabilities();

        // Load global feature settings after all managers are initialized
        // This method will also initialize default settings if they don't exist
        $this->global_feature_settings = $this->get_global_feature_settings();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include all necessary class files.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        // Core framework classes (already loaded globally in main plugin file as-laburda-pwa-app.php)
        // require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-database.php';
        // require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-utils.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-i18n.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-as-laburda-pwa-app-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-as-laburda-pwa-app-public.php';

        // All manager classes
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-app-builder.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-affiliates.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-analytics.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-ai-agent.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-events.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-listing-plans.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-memberships.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-menus.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-notifications.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-products.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-custom-fields.php'; // Ensure custom fields is loaded
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-seo.php'; // Assuming this is also a manager class
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-app-plans.php'; // New App Plans manager
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new AS_Laburda_PWA_App_i18n();
        // The load_plugin_textdomain function should be hooked to 'plugins_loaded' or 'init'
        // to ensure text domain is loaded at the correct time.
        // We'll add it via the loader, which runs during 'plugins_loaded'.
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Load and initialize all manager classes.
     *
     * @since 1.0.0
     * @since 2.0.0 Added new managers for App Builder, Affiliates, Analytics, and AI.
     */
    private function load_managers() {
        // Managers are instantiated here, relying on their respective files being loaded in load_dependencies().
        $this->database_manager    = new AS_Laburda_PWA_App_Database();
        $this->app_builder_manager = new AS_Laburda_PWA_App_App_Builder( $this );
        $this->listing_plans_manager = new AS_Laburda_PWA_App_Listing_Plans( $this );
        $this->memberships_manager = new AS_Laburda_PWA_App_Memberships( $this );
        $this->notifications_manager = new AS_Laburda_PWA_App_Notifications( $this );
        $this->products_manager    = new AS_Laburda_PWA_App_Products( $this );
        $this->events_manager      = new AS_Laburda_PWA_App_Events( $this );
        $this->menus_manager       = new AS_Laburda_PWA_App_Menus( $this );
        $this->affiliates_manager  = new AS_Laburda_PWA_App_Affiliates( $this );
        $this->analytics_manager   = new AS_Laburda_PWA_App_Analytics( $this );
        $this->ai_agent            = new AS_Laburda_PWA_App_AI_Agent( $this );
        $this->seo_manager         = new AS_Laburda_PWA_App_SEO( $this ); // Instantiate SEO Manager
        $this->app_plans_manager   = new AS_Laburda_PWA_App_App_Plans( $this ); // Instantiate App Plans Manager
    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new AS_Laburda_PWA_App_Admin( $this->get_plugin_name(), $this->get_version(), $this );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
        $this->loader->add_action( 'init', $plugin_admin, 'add_custom_roles_and_capabilities' ); // Ensure roles are added on init

        /* --- Admin AJAX Hooks --- */
        // App Builder
        $this->loader->add_action( 'wp_ajax_aslp_get_all_apps', $plugin_admin, 'handle_get_all_apps' );
        $this->loader->add_action( 'wp_ajax_aslp_update_app_settings', $plugin_admin, 'handle_update_app_settings' );
        $this->loader->add_action( 'wp_ajax_aslp_delete_app', $plugin_admin, 'handle_delete_app' );

        // Business Listings
        $this->loader->add_action( 'wp_ajax_aslp_get_all_business_listings', $plugin_admin, 'handle_get_all_business_listings' );
        $this->loader->add_action( 'wp_ajax_aslp_update_business_listing_status', $plugin_admin, 'handle_update_business_listing_status' );
        $this->loader->add_action( 'wp_ajax_aslp_delete_business_listing', $plugin_admin, 'handle_delete_business_listing' );

        // Listing Plans
        $this->loader->add_action( 'wp_ajax_aslp_get_all_listing_plans', $plugin_admin, 'handle_get_all_listing_plans' );
        $this->loader->add_action( 'wp_ajax_aslp_add_update_listing_plan', $plugin_admin, 'handle_add_update_listing_plan' );
        $this->loader->add_action( 'wp_ajax_aslp_delete_listing_plan', $plugin_admin, 'handle_delete_listing_plan' );

        // App Plans (NEW)
        $this->loader->add_action( 'wp_ajax_aslp_get_all_app_plans', $plugin_admin, 'handle_get_all_app_plans' );
        $this->loader->add_action( 'wp_ajax_aslp_add_update_app_plan', $plugin_admin, 'handle_add_update_app_plan' );
        $this->loader->add_action( 'wp_ajax_aslp_delete_app_plan', $plugin_admin, 'handle_delete_app_plan' );


        // App Templates
        $this->loader->add_action( 'wp_ajax_aslp_get_all_app_templates', $plugin_admin, 'handle_get_all_app_templates' );
        $this->loader->add_action( 'wp_ajax_aslp_add_update_app_template', $plugin_admin, 'handle_add_update_app_template' );
        $this->loader->add_action( 'wp_ajax_aslp_delete_app_template', $plugin_admin, 'handle_delete_app_template' );

        // App Menus
        $this->loader->add_action( 'wp_ajax_aslp_get_all_app_menus', $plugin_admin, 'handle_get_all_app_menus' );
        $this->loader->add_action( 'wp_ajax_aslp_add_update_app_menu', $plugin_admin, 'handle_add_update_app_menu' );
        $this->loader->add_action( 'wp_ajax_aslp_delete_app_menu', $plugin_admin, 'handle_delete_app_menu' );

        // Custom Fields
        $this->loader->add_action( 'wp_ajax_aslp_get_all_custom_fields', $plugin_admin, 'handle_get_all_custom_fields' );
        $this->loader->add_action( 'wp_ajax_aslp_add_update_custom_field', $plugin_admin, 'handle_add_update_custom_field' );
        $this->loader->add_action( 'wp_ajax_aslp_delete_custom_field', $plugin_admin, 'handle_delete_custom_field' );

        // Global Settings
        $this->loader->add_action( 'wp_ajax_aslp_get_global_settings', $plugin_admin, 'handle_get_global_settings' );
        $this->loader->add_action( 'wp_ajax_aslp_update_global_settings', $plugin_admin, 'handle_update_global_settings' );

        // Affiliates (Admin side)
        $this->loader->add_action( 'wp_ajax_aslp_admin_get_affiliates', $plugin_admin, 'handle_admin_get_affiliates' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_update_affiliate_status', $plugin_admin, 'handle_admin_update_affiliate_status' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_manage_commissions', $plugin_admin, 'handle_admin_manage_commissions' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_manage_payouts', $plugin_admin, 'handle_admin_manage_payouts' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_get_affiliate_tiers', $plugin_admin, 'handle_admin_get_affiliate_tiers' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_add_update_affiliate_tier', $plugin_admin, 'handle_admin_add_update_affiliate_tier' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_delete_affiliate_tier', $plugin_admin, 'handle_admin_delete_affiliate_tier' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_get_affiliate_creatives', $plugin_admin, 'handle_admin_get_affiliate_creatives' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_create_update_affiliate_creative', $plugin_admin, 'handle_admin_create_update_affiliate_creative' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_delete_affiliate_creative', $plugin_admin, 'handle_admin_delete_affiliate_creative' );


        // Analytics (Admin side)
        $this->loader->add_action( 'wp_ajax_aslp_get_analytics_data', $plugin_admin, 'handle_get_analytics_data' );

        // AI Agent (Admin side)
        $this->loader->add_action( 'wp_ajax_aslp_admin_ai_chat', $plugin_admin, 'handle_admin_ai_chat' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_ai_generate_seo', $plugin_admin, 'handle_admin_ai_generate_seo' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_ai_create_content', $plugin_admin, 'handle_admin_ai_create_content' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_ai_debug_site', $plugin_admin, 'handle_admin_ai_debug_site' );

        // Tools (Admin side)
        $this->loader->add_action( 'wp_ajax_aslp_admin_create_missing_pages', $plugin_admin, 'handle_admin_create_missing_pages' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_get_duplicated_pages', $plugin_admin, 'handle_admin_get_duplicated_pages' );
        $this->loader->add_action( 'wp_ajax_aslp_admin_fix_duplicated_pages', $plugin_admin, 'handle_admin_fix_duplicated_pages' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new AS_Laburda_PWA_App_Public( $this->get_plugin_name(), $this->get_version(), $this );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        // PWA Manifest and Service Worker
        $this->loader->add_action( 'wp_head', $plugin_public, 'add_pwa_manifest_link' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'register_service_worker' );
        $this->loader->add_action( 'init', $plugin_public, 'add_rewrite_rules' );
        $this->loader->add_filter( 'query_vars', $plugin_public, 'add_manifest_query_var' );
        $this->loader->add_action( 'template_redirect', $plugin_public, 'serve_pwa_manifest' );
        $this->loader->add_action( 'template_redirect', $plugin_public, 'serve_service_worker' );

        // Shortcodes (registered directly using add_shortcode)
        add_shortcode( 'aslp_business_listing', array( $plugin_public, 'display_business_listing_shortcode' ) );
        add_shortcode( 'aslp_app_preview', array( $plugin_public, 'display_app_preview_shortcode' ) );
        add_shortcode( 'aslp_affiliate_dashboard', array( $plugin_public, 'display_affiliate_dashboard_shortcode' ) );
        add_shortcode( 'aslp_app_builder', array( $plugin_public, 'display_app_builder_shortcode' ) );
        add_shortcode( 'aslp_user_business_listings', array( $plugin_public, 'display_user_business_listings_shortcode' ) ); // New shortcode for user's listings
        add_shortcode( 'aslp_user_dashboard', array( $plugin_public, 'display_user_dashboard_shortcode' ) ); // New shortcode for public dashboard

        // Public AJAX handlers (for logged-in users)
        $this->loader->add_action( 'wp_ajax_aslp_get_user_apps', $plugin_public, 'handle_get_user_apps' );
        $this->loader->add_action( 'wp_ajax_aslp_get_app_by_id', $plugin_public, 'handle_get_app_by_id' );
        $this->loader->add_action( 'wp_ajax_aslp_create_update_app', $plugin_public, 'handle_create_update_app' );
        $this->loader->add_action( 'wp_ajax_aslp_delete_app_frontend', $plugin_public, 'handle_delete_app_frontend' );

        $this->loader->add_action( 'wp_ajax_aslp_get_user_business_listings', $plugin_public, 'handle_get_user_business_listings' );
        $this->loader->add_action( 'wp_ajax_aslp_create_update_business_listing', $plugin_public, 'handle_create_update_business_listing' );
        $this->loader->add_action( 'wp_ajax_aslp_delete_business_listing_frontend', $plugin_public, 'handle_delete_business_listing_frontend' );
        $this->loader->add_action( 'wp_ajax_aslp_claim_business_listing', $plugin_public, 'handle_claim_business_listing' );

        $this->loader->add_action( 'wp_ajax_aslp_get_products_by_listing', $plugin_public, 'handle_get_products_by_listing' );
        $this->loader->add_action( 'wp_ajax_aslp_get_product', $plugin_public, 'handle_get_product' );
        $this->loader->add_action( 'wp_ajax_aslp_create_update_product', $plugin_public, 'handle_create_update_product' );
        $this->loader->add_action( 'wp_ajax_aslp_delete_product', $plugin_public, 'handle_delete_product' );

        $this->loader->add_action( 'wp_ajax_aslp_get_events_by_listing', $plugin_public, 'handle_get_events_by_listing' );
        $this->loader->add_action( 'wp_ajax_aslp_get_event', $plugin_public, 'handle_get_event' );
        $this->loader->add_action( 'wp_ajax_aslp_create_update_event', $plugin_public, 'handle_create_update_event' );
        $this->loader->add_action( 'wp_ajax_aslp_delete_event', $plugin_public, 'handle_delete_event' );

        $this->loader->add_action( 'wp_ajax_aslp_get_affiliate_data', $plugin_public, 'handle_get_affiliate_data' );
        $this->loader->add_action( 'wp_ajax_aslp_affiliate_registration', $plugin_public, 'handle_affiliate_registration' );
        $this->loader->add_action( 'wp_ajax_aslp_affiliate_request_payout', $plugin_public, 'handle_affiliate_request_payout' );
        $this->loader->add_action( 'wp_ajax_aslp_track_click', $plugin_public, 'handle_track_click' );
        $this->loader->add_action( 'wp_ajax_aslp_get_app_templates', $plugin_public, 'handle_get_app_templates' ); // Public templates access

        // Public AJAX handlers (for non-logged-in users) - if applicable
        $this->loader->add_action( 'wp_ajax_nopriv_aslp_track_click', $plugin_public, 'handle_track_click' );
        $this->loader->add_action( 'wp_ajax_nopriv_aslp_get_app_templates', $plugin_public, 'handle_get_app_templates' ); // Public templates access
        $this->loader->add_action( 'wp_ajax_nopriv_aslp_get_single_business_listing', $plugin_public, 'handle_get_single_business_listing' ); // Public access to view listing
        $this->loader->add_action( 'wp_ajax_nopriv_aslp_get_app_by_id', $plugin_public, 'handle_get_app_by_id' ); // Public access to view app preview
    }

    /**
     * Add custom roles and capabilities upon plugin initialization.
     *
     * This method ensures that the necessary custom roles and capabilities
     * are registered with WordPress when the plugin starts. This is a safer
     * place than activation hook for ensuring they are always present,
     * especially after theme/plugin switches.
     *
     * @since 2.0.0
     * @access private
     */
    private function add_roles_and_capabilities() {
        // Get the administrator role
        $admin_role = get_role( 'administrator' );

        // Add capabilities to Administrator role if they don't exist
        if ( ! empty( $admin_role ) ) {
            $admin_role->add_cap( 'aslp_manage_options' );
            $admin_role->add_cap( 'aslp_create_apps' );
            $admin_role->add_cap( 'aslp_manage_apps' );
            $admin_role->add_cap( 'aslp_manage_own_apps' ); // Admins can manage their own too
            $admin_role->add_cap( 'aslp_submit_business_listing' );
            $admin_role->add_cap( 'aslp_manage_all_business_listings' );
            $admin_role->add_cap( 'aslp_manage_own_business_listings' ); // Admins can manage their own too
            $admin_role->add_cap( 'aslp_manage_listing_plans' );
            $admin_role->add_cap( 'aslp_manage_app_plans' );
            $admin_role->add_cap( 'aslp_manage_affiliates' );
            $admin_role->add_cap( 'aslp_view_affiliate_dashboard' ); // Admins can view their own dashboard
            $admin_role->add_cap( 'aslp_manage_notifications' );
            $admin_role->add_cap( 'aslp_manage_custom_fields' );
            $admin_role->add_cap( 'aslp_manage_menus' );
            $admin_role->add_cap( 'aslp_view_analytics' );
            $admin_role->add_cap( 'aslp_use_ai_assistant' );
            $admin_role->add_cap( 'aslp_use_seo_tools' );
            $admin_role->add_cap( 'aslp_use_admin_tools' );
        }

        // Add 'App Creator' role
        add_role(
            'aslp_app_creator',
            __( 'PWA App Creator', 'as-laburda-pwa-app' ),
            array(
                'read'                   => true,
                'edit_posts'             => true, // Can edit their own posts
                'upload_files'           => true,
                'aslp_create_apps'       => true,
                'aslp_manage_own_apps'   => true,
                'aslp_view_affiliate_dashboard' => true, // Can potentially be an affiliate
                'aslp_use_ai_assistant'  => true,
                'aslp_use_seo_tools'     => true,
            )
        );

        // Add 'Business Owner' role
        add_role(
            'aslp_business_owner',
            __( 'Business Owner', 'as-laburda-pwa-app' ),
            array(
                'read'                           => true,
                'edit_posts'                     => true, // Can edit their own posts
                'upload_files'                   => true,
                'aslp_submit_business_listing'   => true,
                'aslp_manage_own_business_listings' => true,
                'aslp_view_affiliate_dashboard'  => true, // Can potentially be an affiliate
                'aslp_use_ai_assistant'          => true,
                'aslp_use_seo_tools'             => true,
            )
        );

        // Add 'Affiliate' role
        add_role(
            'aslp_affiliate',
            __( 'Affiliate', 'as-laburda-pwa-app' ),
            array(
                'read'                          => true,
                'aslp_view_affiliate_dashboard' => true,
            )
        );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    AS_Laburda_PWA_App_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Retrieve the database manager instance.
     *
     * @since     2.0.0
     * @return    AS_Laburda_PWA_App_Database    The database manager instance.
     */
    public function get_database_manager() {
        return $this->database_manager;
    }

    /**
     * Retrieve the App Builder manager instance.
     *
     * @since     2.0.0
     * @return    AS_Laburda_PWA_App_App_Builder    The App Builder manager instance.
     */
    public function get_app_builder_manager() {
        return $this->app_builder_manager;
    }

    /**
     * Retrieve the Products manager instance.
     *
     * @since     2.0.0
     * @return    AS_Laburda_PWA_App_Products    The Products manager instance.
     */
    public function get_products_manager() {
        return $this->products_manager;
    }

    /**
     * Retrieve the Events manager instance.
     *
     * @since     2.0.0
     * @return    AS_Laburda_PWA_App_Events    The Events manager instance.
     */
    public function get_events_manager() {
        return $this->events_manager;
    }

    /**
     * Retrieve the Listing Plans manager instance.
     *
     * @since     2.0.0
     * @return    AS_Laburda_PWA_App_Listing_Plans    The Listing Plans manager instance.
     */
    public function get_listing_plans_manager() {
        return $this->listing_plans_manager;
    }

    /**
     * Retrieve the App Plans manager instance.
     *
     * @since     2.0.0
     * @return    AS_Laburda_PWA_App_App_Plans    The App Plans manager instance.
     */
    public function get_app_plans_manager() {
        return $this->app_plans_manager;
    }

    /**
     * Retrieve the Affiliates manager instance.
     *
     * @since     2.0.0
     * @return    AS_Laburda_PWA_App_Affiliates    The Affiliates manager instance.
     */
    public function get_affiliates_manager() {
        return $this->affiliates_manager;
    }

    /**
     * Retrieve the Analytics manager instance.
     *
     * @since     2.0.0
     * @return    AS_Laburda_PWA_App_Analytics    The Analytics manager instance.
     */
    public function get_analytics_manager() {
        return $this->analytics_manager;
    }

    /**
     * Retrieve the AI Assistant manager instance.
     *
     * @since     2.0.0
     * @return    AS_Laburda_PWA_App_AI_Agent    The AI Assistant manager instance.
     */
    public function get_ai_agent() {
        return $this->ai_agent;
    }

    /**
     * Retrieve the SEO manager instance.
     *
     * @since     2.0.0
     * @return    AS_Laburda_PWA_App_SEO    The SEO manager instance.
     */
    public function get_seo_manager() {
        return $this->seo_manager;
    }

    /**
     * Get global plugin feature settings.
     *
     * @since 2.0.0
     * @return array An associative array of feature settings.
     */
    public function get_global_feature_settings() {
        $default_settings = array(
            'enable_app_builder'       => false,
            'enable_business_listings' => false,
            'enable_listing_plans'     => false,
            'enable_products'          => false,
            'enable_events'            => false,
            'enable_notifications'     => false,
            'enable_custom_fields'     => false,
            'enable_menus'             => false,
            'enable_affiliates'        => false,
            'enable_analytics'         => false,
            'enable_ai_agent'          => false,
            'enable_seo_tools'         => false,
            'enable_tools_menu'        => false,
            'ai_api_key'               => '',
            'ai_api_endpoint'          => '',
        );
        $settings = get_option( 'aslp_global_settings', array() );
        return array_merge( $default_settings, $settings );
    }

    /**
     * Initializes default global feature settings.
     * This method is called in the constructor to ensure settings are present.
     *
     * @since 2.0.0
     */
    private function initialize_default_global_settings() {
        // This method's logic is now primarily handled by the get_global_feature_settings()
        // method's default merge and the activator's initial option setting.
        // It remains for consistency but its core function is now simpler.
        // The get_option('aslp_global_settings') will handle loading, and if it returns false (not set),
        // the defaults are merged.
    }
}
