<?php
/**
 * Funtionality related to database interactions.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The database interaction functionality of the plugin.
 *
 * This class defines all code necessary to interact with the WordPress database.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Database {

    /**
     * The database object.
     *
     * @since    1.0.0
     * @access   protected
     * @var      wpdb    $wpdb    The WordPress database object.
     */
    protected $wpdb;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Create custom database tables for the plugin.
     * This method should be called upon plugin activation.
     *
     * @since    1.0.0
     * @since    2.0.0 Refined SQL for dbDelta compatibility, added new tables for app builder, affiliates, analytics, and AI interactions.
     */
    public function create_custom_tables() {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $this->wpdb->get_charset_collate();

        // Table for Business Listings
        $table_name = $this->wpdb->prefix . 'aslp_business_listings';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            listing_name varchar(255) NOT NULL,
            description longtext,
            address varchar(255),
            phone_number varchar(50),
            website_url varchar(255),
            email varchar(100),
            logo_url varchar(255),
            cover_image_url varchar(255),
            category varchar(100),
            tags varchar(255),
            status varchar(20) DEFAULT 'pending' NOT NULL,
            is_claimed tinyint(1) DEFAULT 0 NOT NULL,
            current_plan_id mediumint(9) DEFAULT 0 NOT NULL,
            seo_title varchar(255),
            seo_description text,
            seo_keywords varchar(255),
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY status (status)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for Listing Plans
        $table_name = $this->wpdb->prefix . 'aslp_listing_plans';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            plan_name varchar(255) NOT NULL,
            description longtext,
            price decimal(10,2) DEFAULT '0.00' NOT NULL,
            duration int(11) DEFAULT 0 NOT NULL,
            features longtext,
            is_active tinyint(1) DEFAULT 1 NOT NULL,
            is_claim_plan tinyint(1) DEFAULT 0 NOT NULL,
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY plan_name (plan_name)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for User Subscriptions (to listing plans)
        $table_name = $this->wpdb->prefix . 'aslp_user_subscriptions';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            business_listing_id mediumint(9) NOT NULL,
            plan_id mediumint(9) NOT NULL,
            start_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            end_date datetime,
            status varchar(20) DEFAULT 'active' NOT NULL,
            payment_status varchar(20) DEFAULT 'completed' NOT NULL,
            transaction_id varchar(255),
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY business_listing_id (business_listing_id),
            KEY plan_id (plan_id),
            KEY status (status)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for Notifications
        $table_name = $this->wpdb->prefix . 'aslp_notifications';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            business_listing_id mediumint(9) NOT NULL,
            notification_title varchar(255) NOT NULL,
            notification_content longtext NOT NULL,
            target_audience varchar(50) DEFAULT 'all' NOT NULL,
            notification_type varchar(50) DEFAULT 'general' NOT NULL,
            date_sent datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY business_listing_id (business_listing_id)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for User Notification Subscriptions
        $table_name = $this->wpdb->prefix . 'aslp_user_notification_subscriptions';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            business_listing_id mediumint(9) NOT NULL,
            is_subscribed tinyint(1) DEFAULT 1 NOT NULL,
            date_subscribed datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_unsubscribed datetime,
            PRIMARY KEY  (id),
            UNIQUE KEY user_business (user_id, business_listing_id)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for Products
        $table_name = $this->wpdb->prefix . 'aslp_products';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            business_listing_id mediumint(9) NOT NULL,
            product_name varchar(255) NOT NULL,
            description longtext,
            price decimal(10,2) DEFAULT '0.00' NOT NULL,
            image_url varchar(255),
            status varchar(20) DEFAULT 'active' NOT NULL,
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY business_listing_id (business_listing_id)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for Events
        $table_name = $this->wpdb->prefix . 'aslp_events';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            business_listing_id mediumint(9) NOT NULL,
            event_name varchar(255) NOT NULL,
            description longtext,
            event_date date NOT NULL,
            event_time varchar(50),
            location varchar(255),
            image_url varchar(255),
            status varchar(20) DEFAULT 'active' NOT NULL,
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY business_listing_id (business_listing_id),
            KEY event_date (event_date)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for App Menus
        $table_name = $this->wpdb->prefix . 'aslp_app_menus';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            menu_name varchar(255) NOT NULL,
            description text,
            menu_items longtext,
            is_active tinyint(1) DEFAULT 1 NOT NULL,
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY menu_name (menu_name)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for Custom Fields
        $table_name = $this->wpdb->prefix . 'aslp_custom_fields';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            field_name varchar(255) NOT NULL,
            field_slug varchar(255) NOT NULL UNIQUE,
            field_type varchar(50) NOT NULL,
            field_options longtext,
            applies_to varchar(50) NOT NULL,
            is_required tinyint(1) DEFAULT 0 NOT NULL,
            is_active tinyint(1) DEFAULT 1 NOT NULL,
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY field_slug (field_slug)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for PWA Apps
        $table_name = $this->wpdb->prefix . 'aslp_pwa_apps';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            app_uuid varchar(36) NOT NULL UNIQUE,
            user_id bigint(20) unsigned NOT NULL,
            app_name varchar(255) NOT NULL,
            short_name varchar(12),
            description text,
            start_url varchar(255) NOT NULL,
            theme_color varchar(7),
            background_color varchar(7),
            display_mode varchar(50) DEFAULT 'standalone' NOT NULL,
            orientation varchar(50) DEFAULT 'any' NOT NULL,
            icon_192 varchar(255),
            icon_512 varchar(255),
            splash_screen varchar(255),
            offline_page_id bigint(20) unsigned DEFAULT 0 NOT NULL,
            dashboard_page_id bigint(20) unsigned DEFAULT 0 NOT NULL,
            login_page_id bigint(20) unsigned DEFAULT 0 NOT NULL,
            enable_push_notifications tinyint(1) DEFAULT 0 NOT NULL,
            enable_persistent_storage tinyint(1) DEFAULT 0 NOT NULL,
            desktop_template_option varchar(100) DEFAULT 'default' NOT NULL,
            mobile_template_option varchar(100) DEFAULT 'default' NOT NULL,
            app_status varchar(20) DEFAULT 'draft' NOT NULL,
            current_app_plan_id mediumint(9) DEFAULT 0 NOT NULL,
            seo_title varchar(255),
            seo_description text,
            seo_keywords varchar(255),
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY app_uuid (app_uuid),
            KEY user_id (user_id),
            KEY app_status (app_status)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for App Templates
        $table_name = $this->wpdb->prefix . 'aslp_app_templates';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            template_name varchar(255) NOT NULL UNIQUE,
            description text,
            template_data longtext NOT NULL,
            preview_image_url varchar(255),
            is_active tinyint(1) DEFAULT 1 NOT NULL,
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY template_name (template_name)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for Affiliates
        $table_name = $this->wpdb->prefix . 'aslp_affiliates';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL UNIQUE,
            affiliate_code varchar(50) NOT NULL UNIQUE,
            affiliate_status varchar(20) DEFAULT 'pending' NOT NULL,
            payment_email varchar(100) NOT NULL,
            wallet_balance decimal(10,2) DEFAULT '0.00' NOT NULL,
            current_tier_id mediumint(9) DEFAULT 0 NOT NULL,
            referred_by_affiliate_id mediumint(9) DEFAULT 0 NOT NULL,
            date_registered datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            KEY affiliate_status (affiliate_status)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for Affiliate Tiers
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_tiers';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            tier_name varchar(100) NOT NULL UNIQUE,
            description text,
            base_commission_rate decimal(5,2) DEFAULT '0.00' NOT NULL,
            mlm_commission_rate decimal(5,2) DEFAULT '0.00' NOT NULL,
            is_active tinyint(1) DEFAULT 1 NOT NULL,
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY tier_name (tier_name)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for Affiliate Commissions
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_commissions';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            affiliate_id mediumint(9) NOT NULL,
            referred_user_id bigint(20) unsigned,
            referral_type varchar(50) NOT NULL,
            referral_amount decimal(10,2) DEFAULT '0.00' NOT NULL,
            commission_amount decimal(10,2) DEFAULT '0.00' NOT NULL,
            commission_status varchar(20) DEFAULT 'pending' NOT NULL,
            notes text,
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            KEY affiliate_id (affiliate_id),
            KEY commission_status (commission_status)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for Affiliate Payouts
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_payouts';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            affiliate_id mediumint(9) NOT NULL,
            payout_amount decimal(10,2) NOT NULL,
            payout_method varchar(50) NOT NULL,
            payout_status varchar(20) DEFAULT 'pending' NOT NULL,
            transaction_id varchar(255),
            notes text,
            date_requested datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_completed datetime,
            PRIMARY KEY (id),
            KEY affiliate_id (affiliate_id),
            KEY payout_status (payout_status)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for Affiliate Creatives
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_creatives';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            tier_id mediumint(9) DEFAULT 0 NOT NULL,
            creative_name varchar(255) NOT NULL,
            description text,
            creative_type varchar(50) NOT NULL,
            content longtext NOT NULL,
            preview_url varchar(255),
            is_active tinyint(1) DEFAULT 1 NOT NULL,
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            KEY tier_id (tier_id)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for Analytics (Views)
        $table_name = $this->wpdb->prefix . 'aslp_analytics_views';
        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            item_id varchar(36) NOT NULL,
            item_type varchar(50) NOT NULL,
            ip_address varchar(45),
            user_id bigint(20) unsigned,
            view_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            KEY item_id (item_id),
            KEY item_type (item_type),
            KEY view_date (view_date)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for Analytics (Clicks)
        $table_name = $this->wpdb->prefix . 'aslp_analytics_clicks';
        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            item_id varchar(36) NOT NULL,
            item_type varchar(50) NOT NULL,
            click_target varchar(100),
            ip_address varchar(45),
            user_id bigint(20) unsigned,
            notes text,
            date_clicked datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            KEY item_id (item_id),
            KEY item_type (item_type),
            KEY click_target (click_target),
            KEY date_clicked (date_clicked)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for AI Agent Interactions
        $table_name = $this->wpdb->prefix . 'aslp_ai_interactions';
        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned,
            interaction_type varchar(50) NOT NULL,
            prompt longtext NOT NULL,
            response longtext NOT NULL,
            date_interacted datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY interaction_type (interaction_type)
        ) {$charset_collate};";
        dbDelta( $sql );

        // Table for App Plans
        $table_name = $this->wpdb->prefix . 'aslp_app_plans';
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            plan_name varchar(255) NOT NULL,
            description longtext,
            price decimal(10,2) DEFAULT '0.00' NOT NULL,
            duration int(11) DEFAULT 0 NOT NULL,
            features longtext,
            is_active tinyint(1) DEFAULT 1 NOT NULL,
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY plan_name (plan_name)
        ) {$charset_collate};";
        dbDelta( $sql );
    }

    /**
     * Drop custom database tables for the plugin.
     * This method should be called upon plugin deactivation/uninstall if full data removal is desired.
     *
     * @since 2.0.0
     */
    public function drop_custom_tables() {
        $table_names = array(
            $this->wpdb->prefix . 'aslp_business_listings',
            $this->wpdb->prefix . 'aslp_listing_plans',
            $this->wpdb->prefix . 'aslp_user_subscriptions',
            $this->wpdb->prefix . 'aslp_notifications',
            $this->wpdb->prefix . 'aslp_user_notification_subscriptions',
            $this->wpdb->prefix . 'aslp_products',
            $this->wpdb->prefix . 'aslp_events',
            $this->wpdb->prefix . 'aslp_app_menus',
            $this->wpdb->prefix . 'aslp_custom_fields',
            $this->wpdb->prefix . 'aslp_pwa_apps',
            $this->wpdb->prefix . 'aslp_app_templates',
            $this->wpdb->prefix . 'aslp_affiliates',
            $this->wpdb->prefix . 'aslp_affiliate_tiers',
            $this->wpdb->prefix . 'aslp_affiliate_commissions',
            $this->wpdb->prefix . 'aslp_affiliate_payouts',
            $this->wpdb->prefix . 'aslp_affiliate_creatives',
            $this->wpdb->prefix . 'aslp_analytics_views',
            $this->wpdb->prefix . 'aslp_analytics_clicks',
            $this->wpdb->prefix . 'aslp_ai_interactions',
            $this->wpdb->prefix . 'aslp_app_plans',
        );

        foreach ( $table_names as $table_name ) {
            $sql = "DROP TABLE IF EXISTS {$table_name};";
            $this->wpdb->query( $sql );
        }
    }

    /* --- Business Listings --- */

    /**
     * Add a new business listing.
     *
     * @since 1.0.0
     * @param array $data Associative array of listing properties.
     * @return int|false The ID of the new listing on success, false on failure.
     */
    public function add_business_listing( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_business_listings';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing business listing.
     *
     * @since 1.0.0
     * @param int $listing_id The ID of the listing to update.
     * @param array $data Associative array of listing properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_business_listing( $listing_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_business_listings';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $listing_id ) );
    }

    /**
     * Get a business listing by ID.
     *
     * @since 1.0.0
     * @param int $listing_id Listing ID.
     * @return object|null Listing object on success, null if not found.
     */
    public function get_business_listing( $listing_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_business_listings';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $listing_id ) );
    }

    /**
     * Get all business listings.
     *
     * @since 1.0.0
     * @param array $args Optional. Query arguments (e.g., 'user_id', 'status').
     * @return array Array of listing objects.
     */
    public function get_all_business_listings( $args = array() ) {
        $table_name = $this->wpdb->prefix . 'aslp_business_listings';
        $sql = "SELECT * FROM {$table_name} WHERE 1=1";
        $params = array();

        if ( isset( $args['user_id'] ) ) {
            $sql .= " AND user_id = %d";
            $params[] = $args['user_id'];
        }
        if ( isset( $args['status'] ) ) {
            $sql .= " AND status = %s";
            $params[] = $args['status'];
        }

        $sql .= " ORDER BY date_created DESC";

        return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) );
    }

    /**
     * Get business listings by user ID.
     *
     * @since 2.0.0
     * @param int $user_id User ID.
     * @return array Array of listing objects.
     */
    public function get_business_listings_by_user_id( $user_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_business_listings';
        return $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE user_id = %d ORDER BY date_created DESC", $user_id ) );
    }

    /**
     * Delete a business listing.
     *
     * @since 1.0.0
     * @param int $listing_id The ID of the listing to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_business_listing( $listing_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_business_listings';
        return $this->wpdb->delete( $table_name, array( 'id' => $listing_id ) );
    }

    /* --- Listing Plans --- */

    /**
     * Add a new listing plan.
     *
     * @since 1.0.0
     * @param array $data Associative array of plan properties.
     * @return int|false The ID of the new plan on success, false on failure.
     */
    public function add_listing_plan( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_listing_plans';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing listing plan.
     *
     * @since 1.0.0
     * @param int $plan_id The ID of the plan to update.
     * @param array $data Associative array of plan properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_listing_plan( $plan_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_listing_plans';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $plan_id ) );
    }

    /**
     * Get a listing plan by ID.
     *
     * @since 1.0.0
     * @param int $plan_id Plan ID.
     * @return object|null Plan object on success, null if not found.
     */
    public function get_listing_plan( $plan_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_listing_plans';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $plan_id ) );
    }

    /**
     * Get all listing plans.
     *
     * @since 1.0.0
     * @param bool $active_only Optional. If true, only return active plans.
     * @return array Array of plan objects.
     */
    public function get_all_listing_plans( $active_only = false ) {
        $table_name = $this->wpdb->prefix . 'aslp_listing_plans';
        $sql = "SELECT * FROM {$table_name}";
        if ( $active_only ) {
            $sql .= " WHERE is_active = TRUE";
        }
        $sql .= " ORDER BY plan_name ASC";
        return $this->wpdb->get_results( $sql );
    }

    /**
     * Delete a listing plan.
     *
     * @since 1.0.0
     * @param int $plan_id The ID of the plan to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_listing_plan( $plan_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_listing_plans';
        return $this->wpdb->delete( $table_name, array( 'id' => $plan_id ) );
    }

    /* --- User Subscriptions (to listing plans) --- */

    /**
     * Add a new user subscription.
     *
     * @since 1.0.0
     * @param array $data Associative array of subscription properties.
     * @return int|false The ID of the new subscription on success, false on failure.
     */
    public function add_user_subscription( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_user_subscriptions';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing user subscription.
     *
     * @since 1.0.0
     * @param int $subscription_id The ID of the subscription to update.
     * @param array $data Associative array of subscription properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_user_subscription( $subscription_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_user_subscriptions';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $subscription_id ) );
    }

    /**
     * Get user subscriptions based on arguments.
     *
     * @since 1.0.0
     * @param array $args Optional. Query arguments (e.g., 'user_id', 'business_listing_id', 'status').
     * @return array Array of subscription objects.
     */
    public function get_user_subscriptions( $args = array() ) {
        $table_name = $this->wpdb->prefix . 'aslp_user_subscriptions';
        $sql = "SELECT * FROM {$table_name} WHERE 1=1";
        $params = array();

        if ( isset( $args['user_id'] ) ) {
            $sql .= " AND user_id = %d";
            $params[] = $args['user_id'];
        }
        if ( isset( $args['business_listing_id'] ) ) {
            $sql .= " AND business_listing_id = %d";
            $params[] = $args['business_listing_id'];
        }
        if ( isset( $args['status'] ) ) {
            $sql .= " AND status = %s";
            $params[] = $args['status'];
        }

        $sql .= " ORDER BY date_created DESC";

        return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) );
    }

    /* --- Notifications --- */

    /**
     * Add a new notification.
     *
     * @since 1.0.0
     * @param array $data Associative array of notification properties.
     * @return int|false The ID of the new notification on success, false on failure.
     */
    public function add_notification( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_notifications';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Get notifications for a specific business.
     *
     * @since 1.0.0
     * @param int $business_listing_id Business Listing ID.
     * @return array Array of notification objects.
     */
    public function get_notifications_for_business( $business_listing_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_notifications';
        return $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE business_listing_id = %d ORDER BY date_sent DESC", $business_listing_id ) );
    }

    /**
     * Update a user's notification subscription status for a business.
     *
     * @since 1.0.0
     * @param int $user_id The ID of the user.
     * @param int $business_listing_id The ID of the business listing.
     * @param bool $is_subscribed Whether the user is subscribed (true) or unsubscribed (false).
     * @return int|bool The ID of the inserted/updated row, or false on failure.
     */
    public function update_user_notification_subscription( $user_id, $business_listing_id, $is_subscribed ) {
        $table_name = $this->wpdb->prefix . 'aslp_user_notification_subscriptions';
        $existing_subscription = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE user_id = %d AND business_listing_id = %d", $user_id, $business_listing_id ) );

        $data = array( 'is_subscribed' => (int) $is_subscribed );
        if ( $is_subscribed ) {
            $data['date_unsubscribed'] = null;
        } else {
            $data['date_unsubscribed'] = current_time( 'mysql' );
        }

        if ( $existing_subscription ) {
            $updated = $this->wpdb->update( $table_name, $data, array( 'id' => $existing_subscription->id ) );
            return $updated !== false ? $existing_subscription->id : false;
        } else {
            $data['user_id'] = $user_id;
            $data['business_listing_id'] = $business_listing_id;
            $data['date_subscribed'] = current_time( 'mysql' );
            $inserted = $this->wpdb->insert( $table_name, $data );
            return $inserted ? $this->wpdb->insert_id : false;
        }
    }

    /**
     * Get all businesses a user is subscribed to for notifications.
     *
     * @since 1.0.0
     * @param int $user_id The ID of the user.
     * @return array Array of subscription setting objects.
     */
    public function get_user_subscribed_businesses( $user_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_user_notification_subscriptions';
        return $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE user_id = %d AND is_subscribed = 1", $user_id ) );
    }

    /**
     * Get a single user notification subscription.
     *
     * @since 1.0.0
     * @param int $user_id The ID of the user.
     * @param int $business_listing_id The ID of the business listing.
     * @return object|null Subscription object on success, null if not found.
     */
    public function get_user_notification_subscription( $user_id, $business_listing_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_user_notification_subscriptions';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE user_id = %d AND business_listing_id = %d", $user_id, $business_listing_id ) );
    }

    /**
     * Search active business listings.
     *
     * @since 2.0.0
     * @param string $search_term The term to search for in listing names.
     * @param int $limit Optional. Max number of results.
     * @return array Array of business listing objects.
     */
    public function search_active_business_listings( $search_term, $limit = 10 ) {
        $table_name = $this->wpdb->prefix . 'aslp_business_listings';
        $sql = $this->wpdb->prepare(
            "SELECT id, listing_name, status FROM {$table_name} WHERE status = 'active' AND listing_name LIKE %s LIMIT %d",
            '%' . $this->wpdb->esc_like( $search_term ) . '%',
            $limit
        );
        return $this->wpdb->get_results( $sql );
    }

    /* --- Products --- */

    /**
     * Add a new product.
     *
     * @since 1.0.0
     * @param array $data Associative array of product properties.
     * @return int|false The ID of the new product on success, false on failure.
     */
    public function add_product( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_products';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing product.
     *
     * @since 1.0.0
     * @param int $product_id The ID of the product to update.
     * @param array $data Associative array of product properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_product( $product_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_products';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $product_id ) );
    }

    /**
     * Get a product by ID.
     *
     * @since 1.0.0
     * @param int $product_id Product ID.
     * @return object|null Product object on success, null if not found.
     */
    public function get_product( $product_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_products';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $product_id ) );
    }

    /**
     * Get all products for a specific business.
     *
     * @since 1.0.0
     * @param int $business_listing_id Business Listing ID.
     * @param bool $active_only Optional. If true, only return active products.
     * @return array Array of product objects.
     */
    public function get_products_for_business( $business_listing_id, $active_only = false ) {
        $table_name = $this->wpdb->prefix . 'aslp_products';
        $sql = "SELECT * FROM {$table_name} WHERE business_listing_id = %d";
        if ( $active_only ) {
            $sql .= " AND status = 'active'";
        }
        $sql .= " ORDER BY date_created DESC";
        return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $business_listing_id ) );
    }

    /**
     * Get all products (for admin).
     *
     * @since 2.0.0
     * @param bool $active_only Optional. If true, only return active products.
     * @return array Array of product objects including listing name.
     */
    public function get_all_products( $active_only = false ) {
        $products_table = $this->wpdb->prefix . 'aslp_products';
        $listings_table = $this->wpdb->prefix . 'aslp_business_listings';
        
        $sql = "SELECT p.*, l.listing_name FROM {$products_table} p LEFT JOIN {$listings_table} l ON p.business_listing_id = l.id WHERE 1=1";
        $params = array();

        if ( $active_only ) {
            $sql .= " AND p.status = 'active'";
        }
        $sql .= " ORDER BY p.date_created DESC";
        
        return $this->wpdb->get_results( $sql );
    }

    /**
     * Delete a product.
     *
     * @since 1.0.0
     * @param int $product_id The ID of the product to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_product( $product_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_products';
        return $this->wpdb->delete( $table_name, array( 'id' => $product_id ) );
    }

    /* --- Events --- */

    /**
     * Add a new event.
     *
     * @since 1.0.0
     * @param array $data Associative array of event properties.
     * @return int|false The ID of the new event on success, false on failure.
     */
    public function add_event( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_events';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing event.
     *
     * @since 1.0.0
     * @param int $event_id The ID of the event to update.
     * @param array $data Associative array of event properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_event( $event_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_events';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $event_id ) );
    }

    /**
     * Get an event by ID.
     *
     * @since 1.0.0
     * @param int $event_id Event ID.
     * @return object|null Event object on success, null if not found.
     */
    public function get_event( $event_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_events';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $event_id ) );
    }

    /**
     * Get all events for a specific business.
     *
     * @since 1.0.0
     * @param int $business_listing_id Business Listing ID.
     * @param bool $active_only Optional. If true, only return active/future events.
     * @return array Array of event objects.
     */
    public function get_events_for_business( $business_listing_id, $active_only = false ) {
        $table_name = $this->wpdb->prefix . 'aslp_events';
        $sql = "SELECT * FROM {$table_name} WHERE business_listing_id = %d";
        if ( $active_only ) {
            $sql .= " AND status = 'active' AND event_date >= CURDATE()"; // Only active and future events
        }
        $sql .= " ORDER BY event_date ASC, event_time ASC";
        return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $business_listing_id ) );
    }

    /**
     * Get all events (for admin).
     *
     * @since 2.0.0
     * @param bool $active_only Optional. If true, only return active/future events.
     * @return array Array of event objects including listing name.
     */
    public function get_all_events( $active_only = false ) {
        $events_table = $this->wpdb->prefix . 'aslp_events';
        $listings_table = $this->wpdb->prefix . 'aslp_business_listings';

        $sql = "SELECT e.*, l.listing_name FROM {$events_table} e LEFT JOIN {$listings_table} l ON e.business_listing_id = l.id WHERE 1=1";
        $params = array();

        if ( $active_only ) {
            $sql .= " AND e.status = 'active' AND e.event_date >= CURDATE()";
        }
        $sql .= " ORDER BY e.event_date ASC, e.event_time ASC";

        return $this->wpdb->get_results( $sql );
    }

    /**
     * Delete an event.
     *
     * @since 1.0.0
     * @param int $event_id The ID of the event to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_event( $event_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_events';
        return $this->wpdb->delete( $table_name, array( 'id' => $event_id ) );
    }

    /* --- App Menus --- */

    /**
     * Add a new app menu.
     *
     * @since 1.0.0
     * @param array $data Associative array of menu properties.
     * @return int|false The ID of the new menu on success, false on failure.
     */
    public function add_app_menu( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_menus';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing app menu.
     *
     * @since 1.0.0
     * @param int $menu_id The ID of the menu to update.
     * @param array $data Associative array of menu properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_app_menu( $menu_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_menus';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $menu_id ) );
    }

    /**
     * Get an app menu by ID.
     *
     * @since 1.0.0
     * @param int $menu_id Menu ID.
     * @return object|null Menu object on success, null if not found.
     */
    public function get_app_menu( $menu_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_menus';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $menu_id ) );
    }

    /**
     * Get all app menus.
     *
     * @since 1.0.0
     * @param bool $active_only Optional. If true, only return active menus.
     * @return array Array of menu objects.
     */
    public function get_all_app_menus( $active_only = false ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_menus';
        $sql = "SELECT * FROM {$table_name}";
        if ( $active_only ) {
            $sql .= " WHERE is_active = TRUE";
        }
        $sql .= " ORDER BY menu_name ASC";
        return $this->wpdb->get_results( $sql );
    }

    /**
     * Delete an app menu.
     *
     * @since 1.0.0
     * @param int $menu_id The ID of the menu to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_app_menu( $menu_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_menus';
        return $this->wpdb->delete( $table_name, array( 'id' => $menu_id ) );
    }

    /* --- Custom Fields --- */

    /**
     * Add a new custom field.
     *
     * @since 1.0.0
     * @param array $data Associative array of field properties.
     * @return int|false The ID of the new field on success, false on failure.
     */
    public function add_custom_field( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_custom_fields';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing custom field.
     *
     * @since 1.0.0
     * @param int $field_id The ID of the field to update.
     * @param array $data Associative array of field properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_custom_field( $field_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_custom_fields';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $field_id ) );
    }

    /**
     * Get a custom field by ID.
     *
     * @since 1.0.0
     * @param int $field_id Field ID.
     * @return object|null Field object on success, null if not found.
     */
    public function get_custom_field( $field_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_custom_fields';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $field_id ) );
    }

    /**
     * Get all custom fields.
     *
     * @since 1.0.0
     * @param array $args Optional. Query arguments (e.g., 'applies_to', 'is_active').
     * @return array Array of field objects.
     */
    public function get_all_custom_fields( $args = array() ) {
        $table_name = $this->wpdb->prefix . 'aslp_custom_fields';
        $sql = "SELECT * FROM {$table_name} WHERE 1=1";
        $params = array();

        if ( isset( $args['applies_to'] ) ) {
            $sql .= " AND applies_to = %s";
            $params[] = $args['applies_to'];
        }
        if ( isset( $args['is_active'] ) ) {
            $sql .= " AND is_active = %d";
            $params[] = (int) $args['is_active'];
        }

        $sql .= " ORDER BY field_name ASC";
        return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) );
    }

    /**
     * Delete a custom field.
     *
     * @since 1.0.0
     * @param int $field_id The ID of the field to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_custom_field( $field_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_custom_fields';
        return $this->wpdb->delete( $table_name, array( 'id' => $field_id ) );
    }

    /* --- PWA Apps --- */

    /**
     * Add a new PWA app.
     *
     * @since 2.0.0
     * @param array $data Associative array of app properties.
     * @return int|false The ID of the new app on success, false on failure.
     */
    public function add_app( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_pwa_apps';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing PWA app.
     *
     * @since 2.0.0
     * @param string $app_uuid The UUID of the app to update.
     * @param array $data Associative array of app properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_app( $app_uuid, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_pwa_apps';
        return $this->wpdb->update( $table_name, $data, array( 'app_uuid' => $app_uuid ) );
    }

    /**
     * Get a PWA app by its UUID.
     *
     * @since 2.0.0
     * @param string $app_uuid The UUID of the app.
     * @return object|null App object on success, null if not found.
     */
    public function get_app( $app_uuid ) {
        $table_name = $this->wpdb->prefix . 'aslp_pwa_apps';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE app_uuid = %s", $app_uuid ) );
    }

    /**
     * Get all PWA apps associated with a specific user.
     *
     * @since 2.0.0
     * @param int $user_id The ID of the user.
     * @return array Array of app objects.
     */
    public function get_apps_by_user_id( $user_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_pwa_apps';
        return $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE user_id = %d ORDER BY date_created DESC", $user_id ) );
    }

    /**
     * Get all PWA apps (for admin).
     *
     * @since 2.0.0
     * @param array $args Optional. Query arguments (e.g., 'app_status').
     * @return array Array of app objects.
     */
    public function get_all_apps( $args = array() ) {
        $table_name = $this->wpdb->prefix . 'aslp_pwa_apps';
        $sql = "SELECT * FROM {$table_name} WHERE 1=1";
        $params = array();

        if ( isset( $args['app_status'] ) ) {
            $sql .= " AND app_status = %s";
            $params[] = $args['app_status'];
        }

        $sql .= " ORDER BY date_created DESC";
        return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) );
    }

    /**
     * Delete a PWA app.
     *
     * @since 2.0.0
     * @param string $app_uuid The UUID of the app to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_app( $app_uuid ) {
        $table_name = $this->wpdb->prefix . 'aslp_pwa_apps';
        return $this->wpdb->delete( $table_name, array( 'app_uuid' => $app_uuid ) );
    }

    /* --- App Templates --- */

    /**
     * Add a new app template.
     *
     * @since 2.0.0
     * @param array $data Associative array of template properties.
     * @return int|false The ID of the new template on success, false on failure.
     */
    public function add_app_template( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_templates';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing app template.
     *
     * @since 2.0.0
     * @param int $template_id The ID of the template to update.
     * @param array $data Associative array of template properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_app_template( $template_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_templates';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $template_id ) );
    }

    /**
     * Get an app template by ID.
     *
     * @since 2.0.0
     * @param int $template_id Template ID.
     * @return object|null Template object on success, null if not found.
     */
    public function get_app_template( $template_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_templates';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $template_id ) );
    }

    /**
     * Get all app templates.
     *
     * @since 2.0.0
     * @param bool $active_only Optional. If true, only return active templates.
     * @return array Array of template objects.
     */
    public function get_all_app_templates( $active_only = false ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_templates';
        $sql = "SELECT * FROM {$table_name}";
        if ( $active_only ) {
            $sql .= " WHERE is_active = TRUE";
        }
        $sql .= " ORDER BY template_name ASC";
        return $this->wpdb->get_results( $sql );
    }

    /**
     * Delete an app template.
     *
     * @since 2.0.0
     * @param int $template_id The ID of the template to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_app_template( $template_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_templates';
        return $this->wpdb->delete( $table_name, array( 'id' => $template_id ) );
    }

    /* --- App Plans --- */

    /**
     * Add a new app plan.
     *
     * @since 2.0.0
     * @param array $data Associative array of plan properties.
     * @return int|false The ID of the new plan on success, false on failure.
     */
    public function add_app_plan( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_plans';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing app plan.
     *
     * @since 2.0.0
     * @param int $plan_id The ID of the plan to update.
     * @param array $data Associative array of plan properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_app_plan( $plan_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_plans';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $plan_id ) );
    }

    /**
     * Get an app plan by ID.
     *
     * @since 2.0.0
     * @param int $plan_id Plan ID.
     * @return object|null Plan object on success, null if not found.
     */
    public function get_app_plan( $plan_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_plans';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $plan_id ) );
    }

    /**
     * Get all app plans.
     *
     * @since 2.0.0
     * @param bool $active_only Optional. If true, only return active plans.
     * @return array Array of plan objects.
     */
    public function get_all_app_plans( $active_only = false ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_plans';
        $sql = "SELECT * FROM {$table_name}";
        if ( $active_only ) {
            $sql .= " WHERE is_active = TRUE";
        }
        $sql .= " ORDER BY plan_name ASC";
        return $this->wpdb->get_results( $sql );
    }

    /**
     * Delete an app plan.
     *
     * @since 2.0.0
     * @param int $plan_id The ID of the plan to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_app_plan( $plan_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_app_plans';
        return $this->wpdb->delete( $table_name, array( 'id' => $plan_id ) );
    }

    /* --- Affiliates --- */

    /**
     * Add a new affiliate.
     *
     * @since 2.0.0
     * @param array $data Associative array of affiliate properties.
     * @return int|false The ID of the new affiliate on success, false on failure.
     */
    public function add_affiliate( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliates';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing affiliate.
     *
     * @since 2.0.0
     * @param int $affiliate_id The ID of the affiliate to update.
     * @param array $data Associative array of affiliate properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_affiliate( $affiliate_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliates';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $affiliate_id ) );
    }

    /**
     * Get an affiliate by ID.
     *
     * @since 2.0.0
     * @param int $affiliate_id Affiliate ID.
     * @return object|null Affiliate object on success, null if not found.
     */
    public function get_affiliate( $affiliate_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliates';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $affiliate_id ) );
    }

    /**
     * Get an affiliate by user ID.
     *
     * @since 2.0.0
     * @param int $user_id User ID.
     * @return object|null Affiliate object on success, null if not found.
     */
    public function get_affiliate_by_user_id( $user_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliates';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE user_id = %d", $user_id ) );
    }

    /**
     * Get an affiliate by affiliate code.
     *
     * @since 2.0.0
     * @param string $affiliate_code Affiliate Code.
     * @return object|null Affiliate object on success, null if not found.
     */
    public function get_affiliate_by_code( $affiliate_code ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliates';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE affiliate_code = %s", $affiliate_code ) );
    }

    /**
     * Get all affiliates.
     *
     * @since 2.0.0
     * @param array $args Optional. Query arguments (e.g., 'affiliate_status').
     * @return array Array of affiliate objects.
     */
    public function get_all_affiliates( $args = array() ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliates';
        $sql = "SELECT a.*, u.display_name AS user_display_name FROM {$table_name} a JOIN {$this->wpdb->users} u ON a.user_id = u.ID WHERE 1=1";
        $params = array();

        if ( isset( $args['affiliate_status'] ) ) {
            $sql .= " AND a.affiliate_status = %s";
            $params[] = $args['affiliate_status'];
        }

        $sql .= " ORDER BY a.date_registered DESC";
        return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) );
    }

    /**
     * Update an affiliate's wallet balance.
     *
     * @since 2.0.0
     * @param int $affiliate_id The ID of the affiliate.
     * @param float $amount The amount to add or subtract.
     * @param string $operation 'add' or 'subtract'.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_affiliate_wallet_balance( $affiliate_id, $amount, $operation = 'add' ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliates';
        $affiliate = $this->get_affiliate( $affiliate_id );
        if ( ! $affiliate ) {
            return false;
        }

        $current_balance = floatval( $affiliate->wallet_balance );
        $new_balance = $current_balance;

        if ( 'add' === $operation ) {
            $new_balance += floatval( $amount );
        } elseif ( 'subtract' === $operation ) {
            $new_balance -= floatval( $amount );
        } else {
            return false; // Invalid operation
        }

        // Ensure balance doesn't go below zero
        $new_balance = max( 0, $new_balance );

        return $this->wpdb->update(
            $table_name,
            array( 'wallet_balance' => $new_balance ),
            array( 'id' => $affiliate_id ),
            array( '%f' ),
            array( '%d' )
        );
    }

    /**
     * Get an affiliate's wallet balance.
     *
     * @since 2.0.0
     * @param int $affiliate_id The ID of the affiliate.
     * @return float The current wallet balance, or 0.00 if not found.
     */
    public function get_affiliate_wallet_balance( $affiliate_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliates';
        $balance = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT wallet_balance FROM {$table_name} WHERE id = %d", $affiliate_id ) );
        return floatval( $balance );
    }

    /* --- Affiliate Tiers --- */

    /**
     * Add a new affiliate tier.
     *
     * @since 2.0.0
     * @param array $data Associative array of tier properties.
     * @return int|false The ID of the new tier on success, false on failure.
     */
    public function add_affiliate_tier( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_tiers';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing affiliate tier.
     *
     * @since 2.0.0
     * @param int $tier_id The ID of the tier to update.
     * @param array $data Associative array of tier properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_affiliate_tier( $tier_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_tiers';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $tier_id ) );
    }

    /**
     * Get an affiliate tier by ID.
     *
     * @since 2.0.0
     * @param int $tier_id Tier ID.
     * @return object|null Tier object on success, null if not found.
     */
    public function get_affiliate_tier( $tier_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_tiers';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $tier_id ) );
    }

    /**
     * Get an affiliate tier by name.
     *
     * @since 2.0.0
     * @param string $tier_name Tier Name.
     * @return object|null Tier object on success, null if not found.
     */
    public function get_affiliate_tier_by_name( $tier_name ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_tiers';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE tier_name = %s", $tier_name ) );
    }

    /**
     * Get all affiliate tiers.
     *
     * @since 2.0.0
     * @param bool $active_only Optional. If true, only return active tiers.
     * @return array Array of tier objects.
     */
    public function get_all_affiliate_tiers( $active_only = false ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_tiers';
        $sql = "SELECT * FROM {$table_name}";
        if ( $active_only ) {
            $sql .= " WHERE is_active = TRUE";
        }
        $sql .= " ORDER BY base_commission_rate ASC";
        return $this->wpdb->get_results( $sql );
    }

    /**
     * Delete an affiliate tier.
     *
     * @since 2.0.0
     * @param int $tier_id The ID of the tier to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_affiliate_tier( $tier_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_tiers';
        return $this->wpdb->delete( $table_name, array( 'id' => $tier_id ) );
    }

    /* --- Affiliate Commissions --- */

    /**
     * Add a new affiliate commission.
     *
     * @since 2.0.0
     * @param array $data Associative array of commission properties.
     * @return int|false The ID of the new commission on success, false on failure.
     */
    public function add_affiliate_commission( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_commissions';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing affiliate commission.
     *
     * @since 2.0.0
     * @param int $commission_id The ID of the commission to update.
     * @param array $data Associative array of commission properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_affiliate_commission( $commission_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_commissions';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $commission_id ) );
    }

    /**
     * Get an affiliate commission by ID.
     *
     * @since 2.0.0
     * @param int $commission_id Commission ID.
     * @return object|null Commission object on success, null if not found.
     */
    public function get_affiliate_commission( $commission_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_commissions';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $commission_id ) );
    }

    /**
     * Get all affiliate commissions.
     *
     * @since 2.0.0
     * @param array $args Optional. Query arguments (e.g., 'affiliate_id', 'status', 'limit').
     * @return array Array of commission objects.
     */
    public function get_all_affiliate_commissions( $args = array() ) {
        $commissions_table = $this->wpdb->prefix . 'aslp_affiliate_commissions';
        $affiliates_table = $this->wpdb->prefix . 'aslp_affiliates';
        $users_table = $this->wpdb->users;

        $sql = "SELECT c.*, a.user_id, u.display_name AS affiliate_user_display_name FROM {$commissions_table} c JOIN {$affiliates_table} a ON c.affiliate_id = a.id JOIN {$users_table} u ON a.user_id = u.ID WHERE 1=1";
        $params = array();

        if ( isset( $args['affiliate_id'] ) ) {
            $sql .= " AND c.affiliate_id = %d";
            $params[] = $args['affiliate_id'];
        }
        if ( isset( $args['status'] ) ) {
            $sql .= " AND c.commission_status = %s";
            $params[] = $args['status'];
        }

        $sql .= " ORDER BY c.date_created DESC";
        if ( isset( $args['limit'] ) && $args['limit'] > 0 ) {
            $sql .= " LIMIT %d";
            $params[] = absint( $args['limit'] );
        }

        return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) );
    }

    /**
     * Get an affiliate's total earnings (approved commissions).
     *
     * @since 2.0.0
     * @param int $affiliate_id The ID of the affiliate.
     * @param string $status Optional. Filter by status ('pending', 'approved', 'rejected'). Default 'approved'.
     * @return float Total earnings.
     */
    public function get_affiliate_total_earnings( $affiliate_id, $status = 'approved' ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_commissions';
        $sql = "SELECT SUM(commission_amount) FROM {$table_name} WHERE affiliate_id = %d AND commission_status = %s";
        $total = $this->wpdb->get_var( $this->wpdb->prepare( $sql, $affiliate_id, $status ) );
        return floatval( $total );
    }

    /**
     * Get an affiliate's total referral count.
     *
     * @since 2.0.0
     * @param int $affiliate_id The ID of the affiliate.
     * @return int Total referral count.
     */
    public function get_affiliate_referral_count( $affiliate_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_commissions';
        $count = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT COUNT(DISTINCT referred_user_id) FROM {$table_name} WHERE affiliate_id = %d", $affiliate_id ) );
        return absint( $count );
    }

    /* --- Affiliate Payouts --- */

    /**
     * Add a new affiliate payout request.
     *
     * @since 2.0.0
     * @param array $data Associative array of payout properties.
     * @return int|false The ID of the new payout on success, false on failure.
     */
    public function add_affiliate_payout( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_payouts';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing affiliate payout.
     *
     * @since 2.0.0
     * @param int $payout_id The ID of the payout to update.
     * @param array $data Associative array of payout properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_affiliate_payout( $payout_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_payouts';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $payout_id ) );
    }

    /**
     * Get an affiliate payout by ID.
     *
     * @since 2.0.0
     * @param int $payout_id Payout ID.
     * @return object|null Payout object on success, null if not found.
     */
    public function get_affiliate_payout( $payout_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_payouts';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $payout_id ) );
    }

    /**
     * Get all affiliate payouts.
     *
     * @since 2.0.0
     * @param array $args Optional. Query arguments (e.g., 'affiliate_id', 'status', 'limit').
     * @return array Array of payout objects.
     */
    public function get_all_affiliate_payouts( $args = array() ) {
        $payouts_table = $this->wpdb->prefix . 'aslp_affiliate_payouts';
        $affiliates_table = $this->wpdb->prefix . 'aslp_affiliates';
        $users_table = $this->wpdb->users;

        $sql = "SELECT p.*, a.user_id, u.display_name AS affiliate_user_display_name FROM {$payouts_table} p JOIN {$affiliates_table} a ON p.affiliate_id = a.id JOIN {$users_table} u ON a.user_id = u.ID WHERE 1=1";
        $params = array();

        if ( isset( $args['affiliate_id'] ) ) {
            $sql .= " AND p.affiliate_id = %d";
            $params[] = $args['affiliate_id'];
        }
        if ( isset( $args['payout_status'] ) ) {
            $sql .= " AND p.payout_status = %s";
            $params[] = $args['payout_status'];
        }

        $sql .= " ORDER BY p.date_requested DESC";
        if ( isset( $args['limit'] ) && $args['limit'] > 0 ) {
            $sql .= " LIMIT %d";
            $params[] = absint( $args['limit'] );
        }

        return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) );
    }

    /**
     * Get an affiliate's total paid payouts.
     *
     * @since 2.0.0
     * @param int $affiliate_id The ID of the affiliate.
     * @return float Total paid amount.
     */
    public function get_affiliate_total_paid_payouts( $affiliate_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_payouts';
        $sql = "SELECT SUM(payout_amount) FROM {$table_name} WHERE affiliate_id = %d AND payout_status = 'completed'";
        $total = $this->wpdb->get_var( $this->wpdb->prepare( $sql, $affiliate_id ) );
        return floatval( $total );
    }

    /**
     * Get an affiliate's total pending payouts.
     *
     * @since 2.0.0
     * @param int $affiliate_id The ID of the affiliate.
     * @return float Total pending amount.
     */
    public function get_affiliate_total_pending_payouts( $affiliate_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_payouts';
        $sql = "SELECT SUM(payout_amount) FROM {$table_name} WHERE affiliate_id = %d AND payout_status = 'pending'";
        $total = $this->wpdb->get_var( $this->wpdb->prepare( $sql, $affiliate_id ) );
        return floatval( $total );
    }

    /* --- Affiliate Creatives --- */

    /**
     * Add a new affiliate creative.
     *
     * @since 2.0.0
     * @param array $data Associative array of creative properties.
     * @return int|false The ID of the new creative on success, false on failure.
     */
    public function add_affiliate_creative( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_creatives';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update an existing affiliate creative.
     *
     * @since 2.0.0
     * @param int $creative_id The ID of the creative to update.
     * @param array $data Associative array of creative properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_affiliate_creative( $creative_id, $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_creatives';
        return $this->wpdb->update( $table_name, $data, array( 'id' => $creative_id ) );
    }

    /**
     * Get an affiliate creative by ID.
     *
     * @since 2.0.0
     * @param int $creative_id Creative ID.
     * @return object|null Creative object on success, null if not found.
     */
    public function get_affiliate_creative( $creative_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_creatives';
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $creative_id ) );
    }

    /**
     * Get all affiliate creatives.
     *
     * @since 2.0.0
     * @param bool $active_only Optional. If true, only return active creatives.
     * @param int $tier_id Optional. Filter by specific tier_id.
     * @return array Array of creative objects.
     */
    public function get_all_affiliate_creatives( $active_only = false, $tier_id = 0 ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_creatives';
        $sql = "SELECT * FROM {$table_name} WHERE 1=1";
        $params = array();

        if ( $active_only ) {
            $sql .= " AND is_active = TRUE";
        }
        if ( $tier_id > 0 ) {
            $sql .= " AND tier_id = %d";
            $params[] = $tier_id;
        }

        $sql .= " ORDER BY creative_name ASC";
        return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) );
    }

    /**
     * Delete an affiliate creative.
     *
     * @since 2.0.0
     * @param int $creative_id The ID of the creative to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_affiliate_creative( $creative_id ) {
        $table_name = $this->wpdb->prefix . 'aslp_affiliate_creatives';
        return $this->wpdb->delete( $table_name, array( 'id' => $creative_id ) );
    }

    /* --- Analytics (Views) --- */

    /**
     * Add a new analytics view entry.
     *
     * @since 2.0.0
     * @param array $data Associative array of view properties.
     * @return int|false The ID of the new view entry on success, false on failure.
     */
    public function add_analytics_view( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_analytics_views';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Get analytics view count for a specific item or overall.
     *
     * @since 2.0.0
     * @param string $item_type Item type ('app', 'listing', 'affiliate').
     * @param string $item_id Optional. The ID (UUID for app, numeric ID for listing/affiliate) of the item.
     * @param string $period Optional. Filter by period ('day', 'week', 'month', 'year', 'total').
     * @return int Total view count.
     */
    public function get_analytics_view_count( $item_type, $item_id = '', $period = 'total' ) {
        $table_name = $this->wpdb->prefix . 'aslp_analytics_views';
        $sql = "SELECT COUNT(*) FROM {$table_name} WHERE item_type = %s";
        $params = array( $item_type );

        if ( ! empty( $item_id ) ) {
            $sql .= " AND item_id = %s";
            $params[] = $item_id;
        }

        $sql .= $this->get_date_filter_sql( $period, $params, 'view_date' );

        $count = $this->wpdb->get_var( $this->wpdb->prepare( $sql, $params ) );
        return absint( $count );
    }

    /* --- Analytics (Clicks) --- */

    /**
     * Add a new analytics click entry.
     *
     * @since 2.0.0
     * @param array $data Associative array of click properties.
     * @return int|false The ID of the new click entry on success, false on failure.
     */
    public function add_analytics_click( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_analytics_clicks';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Get analytics click count for a specific item and/or target.
     *
     * @since 2.0.0
     * @param string $item_type Item type ('app', 'listing', 'affiliate').
     * @param string $item_id Optional. The ID (UUID for app, numeric ID for listing/affiliate) of the item.
     * @param string $period Optional. Filter by period ('day', 'week', 'month', 'year', 'total').
     * @param string $click_target Optional. Filter by specific click target.
     * @return int Total click count.
     */
    public function get_analytics_click_count( $item_type, $item_id = '', $period = 'total', $click_target = '' ) {
        $table_name = $this->wpdb->prefix . 'aslp_analytics_clicks';
        $sql = "SELECT COUNT(*) FROM {$table_name} WHERE item_type = %s";
        $params = array( $item_type );

        if ( ! empty( $item_id ) ) {
            $sql .= " AND item_id = %s";
            $params[] = $item_id;
        }
        if ( ! empty( $click_target ) ) {
            $sql .= " AND click_target = %s";
            $params[] = $click_target;
        }

        $sql .= $this->get_date_filter_sql( $period, $params, 'date_clicked' );

        $count = $this->wpdb->get_var( $this->wpdb->prepare( $sql, $params ) );
        return absint( $count );
    }

    /**
     * Get aggregated analytics data for views over time.
     *
     * @since 2.0.0
     * @param string $item_type Item type ('app', 'listing', 'affiliate').
     * @param string $period Time period ('day', 'week', 'month', 'year').
     * @return array Array of objects with 'date' and 'views' count.
     */
    public function get_aggregated_views( $item_type, $period ) {
        global $wpdb;
        $table_name = $this->wpdb->prefix . 'aslp_analytics_views';
        $sql = "SELECT DATE(view_date) as date, COUNT(*) as views FROM {$table_name} WHERE item_type = %s";
        $params = array( $item_type );

        $sql .= $this->get_date_filter_sql( $period, $params, 'view_date' );
        $sql .= " GROUP BY DATE(view_date) ORDER BY date ASC";

        return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
    }

    /**
     * Get aggregated analytics data for clicks over time.
     *
     * @since 2.0.0
     * @param string $item_type Item type ('app', 'listing', 'affiliate').
     * @param string $period Time period ('day', 'week', 'month', 'year').
     * @return array Array of objects with 'date' and 'clicks' count.
     */
    public function get_aggregated_clicks( $item_type, $period ) {
        global $wpdb;
        $table_name = $this->wpdb->prefix . 'aslp_analytics_clicks';
        $sql = "SELECT DATE(date_clicked) as date, COUNT(*) as clicks FROM {$table_name} WHERE item_type = %s";
        $params = array( $item_type );

        $sql .= $this->get_date_filter_sql( $period, $params, 'date_clicked' );
        $sql .= " GROUP BY DATE(date_clicked) ORDER BY date ASC";

        return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
    }


    /**
     * Helper function to generate SQL date filter.
     *
     * @since 2.0.0
     * @param string $period The period ('daily', 'weekly', 'monthly', 'yearly', 'total').
     * @param array $params Reference to the parameters array to add date values.
     * @param string $date_column The name of the date column in the table.
     * @return string The SQL WHERE clause for date filtering.
     */
    private function get_date_filter_sql( $period, &$params, $date_column = 'date_created' ) {
        $sql_filter = '';
        switch ( $period ) {
            case 'daily':
                $sql_filter .= " AND DATE({$date_column}) = CURDATE()";
                break;
            case 'weekly':
                $sql_filter .= " AND YEARWEEK({$date_column}, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'monthly':
                $sql_filter .= " AND MONTH({$date_column}) = MONTH(CURDATE()) AND YEAR({$date_column}) = YEAR(CURDATE())";
                break;
            case 'yearly':
                $sql_filter .= " AND YEAR({$date_column}) = YEAR(CURDATE())";
                break;
            case 'total':
            default:
                // No date filter needed
                break;
        }
        return $sql_filter;
    }


    /* --- AI Agent Interactions --- */

    /**
     * Add a new AI interaction log entry.
     *
     * @since 2.0.0
     * @param array $data Associative array of interaction properties.
     * @return int|false The ID of the new interaction entry on success, false on failure.
     */
    public function add_ai_interaction( $data ) {
        $table_name = $this->wpdb->prefix . 'aslp_ai_interactions';
        $result = $this->wpdb->insert( $table_name, $data );
        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Get AI interaction logs.
     *
     * @since 2.0.0
     * @param array $args Optional. Query arguments (e.g., 'user_id', 'interaction_type', 'limit').
     * @return array Array of AI interaction objects.
     */
    public function get_ai_interactions( $args = array() ) {
        $table_name = $this->wpdb->prefix . 'aslp_ai_interactions';
        $sql = "SELECT * FROM {$table_name} WHERE 1=1";
        $params = array();

        if ( isset( $args['user_id'] ) ) {
            $sql .= " AND user_id = %d";
            $params[] = $args['user_id'];
        }
        if ( isset( $args['interaction_type'] ) ) {
            $sql .= " AND interaction_type = %s";
            $params[] = $args['interaction_type'];
        }

        $sql .= " ORDER BY date_interacted DESC";
        if ( isset( $args['limit'] ) && $args['limit'] > 0 ) {
            $sql .= " LIMIT %d";
            $params[] = absint( $args['limit'] );
        }

        return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) );
    }
}
