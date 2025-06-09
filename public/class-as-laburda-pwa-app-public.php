<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/public
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Public {

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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version           The version of the plugin.
     * @param      AS_Laburda_PWA_App $main_plugin The main plugin instance.
     */
    public function __construct( $plugin_name, $version, $main_plugin ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->main_plugin = $main_plugin;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/as-laburda-pwa-app-public.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/as-laburda-pwa-app-public.js', array( 'jquery' ), $this->version, true );

        // Localize script for AJAX calls
        wp_localize_script(
            $this->plugin_name,
            'aslp_public_ajax_object',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'aslp_public_nonce' ),
            )
        );
    }

    /**
     * Add link to PWA manifest in wp_head.
     *
     * @since 2.0.0
     */
    public function add_pwa_manifest_link() {
        // Check if App Builder is enabled globally
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            return;
        }

        // Get the default app UUID from settings or a specific one if needed
        $default_app_uuid = get_option( 'aslp_default_pwa_app_uuid' ); // Assuming this option stores the default app UUID

        if ( ! empty( $default_app_uuid ) ) {
            echo '<link rel="manifest" href="' . esc_url( home_url( '/aslp-pwa-manifest.json?app_uuid=' . $default_app_uuid ) ) . '">';
            echo '<meta name="apple-mobile-web-app-capable" content="yes">';
            echo '<meta name="apple-mobile-web-app-status-bar-style" content="black">';
            echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
            echo '<link rel="apple-touch-icon" href="' . esc_url( plugin_dir_url( __FILE__ ) . 'images/icon-192x192.png' ) . '">'; // Placeholder
        }
    }

    /**
     * Register the service worker.
     *
     * @since 2.0.0
     */
    public function register_service_worker() {
        // Check if App Builder is enabled globally
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            return;
        }

        wp_enqueue_script( 'aslp-service-worker-registration', plugin_dir_url( __FILE__ ) . 'js/service-worker-registration.js', array(), $this->version, true );
    }

    /**
     * Add rewrite rules for PWA manifest and service worker.
     *
     * @since 2.0.0
     */
    public function add_rewrite_rules() {
        // Check if App Builder is enabled globally
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            return;
        }

        add_rewrite_rule( '^aslp-pwa-manifest\.json$', 'index.php?aslp_pwa_manifest=1', 'top' );
        add_rewrite_rule( '^service-worker\.js$', 'index.php?aslp_service_worker=1', 'top' );
        // Flush rewrite rules on plugin activation/deactivation, not on every page load.
        // This is handled in AS_Laburda_PWA_App_Activator/Deactivator.
    }

    /**
     * Add custom query vars for PWA manifest and service worker.
     *
     * @since 2.0.0
     * @param array $vars The array of query variables.
     * @return array
     */
    public function add_manifest_query_var( $vars ) {
        $vars[] = 'aslp_pwa_manifest';
        $vars[] = 'aslp_service_worker';
        $vars[] = 'app_uuid'; // To get specific app manifest
        return $vars;
    }

    /**
     * Serve the PWA manifest.json file.
     *
     * @since 2.0.0
     */
    public function serve_pwa_manifest() {
        if ( get_query_var( 'aslp_pwa_manifest' ) ) {
            // Check if App Builder is enabled globally
            $global_features = $this->main_plugin->get_global_feature_settings();
            if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
                status_header( 404 );
                exit;
            }

            $app_uuid = sanitize_text_field( get_query_var( 'app_uuid' ) );
            if ( empty( $app_uuid ) ) {
                // If no specific app_uuid, try to get the default one
                $app_uuid = get_option( 'aslp_default_pwa_app_uuid' );
            }

            $app_data = $this->main_plugin->get_app_builder_manager()->get_app( $app_uuid );

            if ( $app_data ) {
                header( 'Content-Type: application/manifest+json' );
                echo AS_Laburda_PWA_App_Utils::generate_pwa_manifest( $app_data );
            } else {
                status_header( 404 );
            }
            exit;
        }
    }

    /**
     * Serve the service-worker.js file.
     *
     * @since 2.0.0
     */
    public function serve_service_worker() {
        if ( get_query_var( 'aslp_service_worker' ) ) {
            // Check if App Builder is enabled globally
            $global_features = $this->main_plugin->get_global_feature_settings();
            if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
                status_header( 404 );
                exit;
            }

            header( 'Content-Type: application/javascript' );
            header( 'Service-Worker-Allowed: /' ); // Important for scope

            // Load the service worker content
            include_once plugin_dir_path( __FILE__ ) . 'js/service-worker.js';
            exit;
        }
    }

    /* --- Shortcode Callbacks --- */

    /**
     * Shortcode to display a business listing.
     * Example: [aslp_business_listing id="123"]
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function display_business_listing_shortcode( $atts ) {
        // Check if Business Listings are enabled globally
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_business_listings'] ?? false ) ) {
            return '<p>' . __( 'Business listings feature is currently disabled.', 'as-laburda-pwa-app' ) . '</p>';
        }

        $atts = shortcode_atts(
            array(
                'id' => 0,
            ),
            $atts,
            'aslp_business_listing'
        );

        $listing_id = absint( $atts['id'] );

        if ( ! $listing_id ) {
            return '<p>' . __( 'Business listing ID is missing.', 'as-laburda-pwa-app' ) . '</p>';
        }

        $listing = $this->main_plugin->get_database_manager()->get_business_listing( $listing_id );

        if ( ! $listing || $listing->status !== 'active' ) {
            return '<p>' . __( 'Business listing not found or is not active.', 'as-laburda-pwa-app' ) . '</p>';
        }

        // Track view for this listing
        $user_ip = AS_Laburda_PWA_App_Utils::get_user_ip();
        $user_id = get_current_user_id();
        $this->main_plugin->get_analytics_manager()->track_view( 'listing', $listing->id, $user_ip, $user_id );

        ob_start();
        ?>
        <div class="aslp-business-listing-container">
            <h2><?php echo esc_html( $listing->listing_name ); ?></h2>

            <?php if ( ! empty( $listing->featured_image_url ) ) : ?>
                <img src="<?php echo esc_url( $listing->featured_image_url ); ?>" alt="<?php echo esc_attr( $listing->listing_name ); ?>" class="aslp-listing-featured-image">
            <?php endif; ?>

            <?php if ( ! empty( $listing->logo_url ) ) : ?>
                <img src="<?php echo esc_url( $listing->logo_url ); ?>" alt="<?php echo esc_attr( $listing->listing_name ); ?> Logo" class="aslp-listing-logo">
            <?php endif; ?>

            <p><strong><?php _e( 'Description:', 'as-laburda-pwa-app' ); ?></strong> <?php echo nl2br( esc_html( $listing->description ) ); ?></p>
            <p><strong><?php _e( 'Address:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( $listing->address ); ?>, <?php echo esc_html( $listing->city ); ?>, <?php echo esc_html( $listing->state ); ?> <?php echo esc_html( $listing->zip_code ); ?>, <?php echo esc_html( $listing->country ); ?></p>
            <p><strong><?php _e( 'Phone:', 'as-laburda-pwa-app' ); ?></strong> <a href="tel:<?php echo esc_attr( $listing->phone ); ?>"><?php echo esc_html( $listing->phone ); ?></a></p>
            <p><strong><?php _e( 'Email:', 'as-laburda-pwa-app' ); ?></strong> <a href="mailto:<?php echo esc_attr( $listing->email ); ?>"><?php echo esc_html( $listing->email ); ?></a></p>
            <p><strong><?php _e( 'Website:', 'as-laburda-pwa-app' ); ?></strong> <a href="<?php echo esc_url( $listing->website ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $listing->website ); ?></a></p>

            <?php
            // Display custom fields if enabled and available
            if ( ( $global_features['enable_custom_fields'] ?? false ) ) {
                $custom_fields = $this->main_plugin->get_database_manager()->get_custom_fields_for_entity( 'listing', $listing->id );
                if ( ! empty( $custom_fields ) ) {
                    echo '<h3>' . __( 'Additional Details', 'as-laburda-pwa-app' ) . '</h3>';
                    foreach ( $custom_fields as $field ) {
                        echo '<p><strong>' . esc_html( $field->field_name ) . ':</strong> ' . esc_html( $field->field_value ) . '</p>';
                    }
                }
            }

            // Display products if enabled and available
            if ( ( $global_features['enable_products'] ?? false ) ) {
                $products = $this->main_plugin->get_products_manager()->get_products_by_listing_id( $listing->id );
                if ( ! empty( $products ) ) {
                    echo '<div class="aslp-products-list">';
                    echo '<h3>' . __( 'Our Products', 'as-laburda-pwa-app' ) . '</h3>';
                    foreach ( $products as $product ) {
                        ?>
                        <div class="aslp-product-item">
                            <?php if ( ! empty( $product->image_url ) ) : ?>
                                <img src="<?php echo esc_url( $product->image_url ); ?>" alt="<?php echo esc_attr( $product->product_name ); ?>" class="aslp-product-image">
                            <?php endif; ?>
                            <h4><?php echo esc_html( $product->product_name ); ?></h4>
                            <p><?php echo nl2br( esc_html( $product->description ) ); ?></p>
                            <p><strong><?php _e( 'Price:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( $product->price ); ?> <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></p>
                            <?php if ( ! empty( $product->link ) ) : ?>
                                <p><a href="<?php echo esc_url( $product->link ); ?>" target="_blank" rel="noopener noreferrer"><?php _e( 'View Product', 'as-laburda-pwa-app' ); ?></a></p>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                    echo '</div>';
                }
            }

            // Display events if enabled and available
            if ( ( $global_features['enable_events'] ?? false ) ) {
                $events = $this->main_plugin->get_events_manager()->get_events_by_listing_id( $listing->id );
                if ( ! empty( $events ) ) {
                    echo '<div class="aslp-events-list">';
                    echo '<h3>' . __( 'Upcoming Events', 'as-laburda-pwa-app' ) . '</h3>';
                    foreach ( $events as $event ) {
                        ?>
                        <div class="aslp-event-item">
                            <h4><?php echo esc_html( $event->event_name ); ?></h4>
                            <p><strong><?php _e( 'Date:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( date( get_option( 'date_format' ), strtotime( $event->event_date ) ) ); ?></p>
                            <p><strong><?php _e( 'Time:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( date( get_option( 'time_format' ), strtotime( $event->event_time ) ) ); ?></p>
                            <p><?php echo nl2br( esc_html( $event->description ) ); ?></p>
                            <?php if ( ! empty( $event->location ) ) : ?>
                                <p><strong><?php _e( 'Location:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( $event->location ); ?></p>
                            <?php endif; ?>
                            <?php if ( ! empty( $event->link ) ) : ?>
                                <a href="<?php echo esc_url( $event->link ); ?>" target="_blank" rel="noopener noreferrer" class="aslp-event-link"><?php _e( 'More Info', 'as-laburda-pwa-app' ); ?></a>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                    echo '</div>';
                }
            }

            // Claim Listing Button if not claimed
            if ( ! $listing->is_claimed ) {
                ?>
                <button class="aslp-claim-listing-button" data-listing-id="<?php echo esc_attr( $listing->id ); ?>">
                    <?php _e( 'Claim This Listing', 'as-laburda-pwa-app' ); ?>
                </button>
                <?php
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode to display a PWA app preview and launch button.
     * Example: [aslp_app_preview uuid="your-app-uuid"]
     *
     * @since 2.0.0
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function display_app_preview_shortcode( $atts ) {
        // Check if App Builder is enabled globally
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            return '<p>' . __( 'PWA App Builder feature is currently disabled.', 'as-laburda-pwa-app' ) . '</p>';
        }

        $atts = shortcode_atts(
            array(
                'uuid' => '', // App UUID
            ),
            $atts,
            'aslp_app_preview'
        );

        $app_uuid = sanitize_text_field( $atts['uuid'] );

        if ( empty( $app_uuid ) ) {
            return '<p>' . __( 'PWA App UUID is missing.', 'as-laburda-pwa-app' ) . '</p>';
        }

        $app_data = $this->main_plugin->get_app_builder_manager()->get_app( $app_uuid );

        if ( ! $app_data || $app_data->app_status !== 'published' ) {
            // Only show published apps to public, or if user is owner/admin
            if ( ! is_user_logged_in() || ( get_current_user_id() !== $app_data->user_id && ! current_user_can( 'aslp_manage_apps' ) ) ) {
                return '<p>' . __( 'PWA App not found or is not published.', 'as-laburda-pwa-app' ) . '</p>';
            }
        }

        // Decode app_config to get manifest details
        $app_config = AS_Laburda_PWA_App_Utils::safe_json_decode( $app_data->app_config, true );
        $app_name = $app_config['name'] ?? $app_data->app_name;
        $app_description = $app_config['description'] ?? $app_data->description;
        $start_url = $app_config['start_url'] ?? home_url(); // Fallback to home URL
        $icon_url = $app_config['icons'][0]['src'] ?? plugin_dir_url( __FILE__ ) . 'images/icon-192x192.png'; // Fallback icon

        ob_start();
        ?>
        <div class="aslp-app-preview-container">
            <h3><?php echo esc_html( $app_name ); ?></h3>
            <?php if ( ! empty( $icon_url ) ) : ?>
                <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $app_name ); ?> Icon" style="width: 128px; height: 128px; margin-bottom: 15px;">
            <?php endif; ?>
            <p><?php echo nl2br( esc_html( $app_description ) ); ?></p>
            <p><strong><?php _e( 'Launch URL:', 'as-laburda-pwa-app' ); ?></strong> <a href="<?php echo esc_url( $start_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_url( $start_url ); ?></a></p>

            <button class="aslp-track-app-click-button"
                    data-item-id="<?php echo esc_attr( $app_uuid ); ?>"
                    data-item-type="app_launch"
                    data-click-target="<?php echo esc_url( $start_url ); ?>"
                    onclick="window.open('<?php echo esc_url( $start_url ); ?>', '_blank');">
                <?php _e( 'Launch App (Demo)', 'as-laburda-pwa-app' ); ?>
            </button>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode to display the affiliate dashboard or registration form.
     * Example: [aslp_affiliate_dashboard]
     *
     * @since 2.0.0
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function display_affiliate_dashboard_shortcode( $atts ) {
        // Check if Affiliate Program is enabled globally
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_affiliates'] ?? false ) ) {
            return '<p>' . __( 'Affiliate program feature is currently disabled.', 'as-laburda-pwa-app' ) . '</p>';
        }

        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to access the affiliate dashboard.', 'as-laburda-pwa-app' ) . '</p>';
        }

        $user_id = get_current_user_id();
        $affiliate_manager = $this->main_plugin->get_affiliates_manager();
        $affiliate_data = $affiliate_manager->get_affiliate_by_user_id( $user_id );

        ob_start();

        if ( $affiliate_data && $affiliate_data->affiliate_status === 'active' ) {
            // Display Affiliate Dashboard
            $wallet_balance = $affiliate_data->wallet_balance ?? 0;
            $affiliate_code = $affiliate_data->affiliate_code;
            $referral_url = add_query_arg( 'ref', $affiliate_code, home_url() );
            $creatives = $affiliate_manager->get_all_affiliate_creatives( true ); // Get all active creatives (0 means all tiers)

            ?>
            <div class="aslp-affiliate-dashboard-container">
                <h3><?php _e( 'Your Affiliate Dashboard', 'as-laburda-pwa-app' ); ?></h3>
                <div id="aslp-payout-message"></div>
                <p><strong><?php _e( 'Current Wallet Balance:', 'as-laburda-pwa-app' ); ?></strong> <span id="affiliate-wallet-balance"><?php echo number_format( $wallet_balance, 2 ); ?> <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></span></p>

                <button id="aslp-request-payout-button" class="button button-primary" <?php echo ( $wallet_balance <= 0 ) ? 'disabled' : ''; ?>>
                    <?php _e( 'Request Payout', 'as-laburda-pwa-app' ); ?>
                </button>

                <h4><?php _e( 'Your Referral Link', 'as-laburda-pwa-app' ); ?></h4>
                <p>Share this link to earn commissions:</p>
                <input type="text" value="<?php echo esc_url( $referral_url ); ?>" readonly onclick="this.select(); document.execCommand('copy');" title="<?php _e( 'Click to copy', 'as-laburda-pwa-app' ); ?>">
                <p class="description"><?php _e( 'Click the link to copy it to your clipboard.', 'as-laburda-pwa-app' ); ?></p>

                <?php if ( ! empty( $creatives ) ) : ?>
                    <h4><?php _e( 'Marketing Creatives', 'as-laburda-pwa-app' ); ?></h4>
                    <p><?php _e( 'Use these creatives to promote the site:', 'as-laburda-pwa-app' ); ?></p>
                    <ul>
                        <?php foreach ( $creatives as $creative ) :
                            $final_content = '';
                            if ( $creative->creative_type === 'text_link' ) {
                                $final_content = '<a href="' . esc_url( add_query_arg( 'ref', $affiliate_code, home_url() ) ) . '" target="_blank" rel="nofollow">' . esc_html( $creative->content ) . '</a>';
                            } elseif ( $creative->creative_type === 'image_banner' ) {
                                $final_content = '<a href="' . esc_url( add_query_arg( 'ref', $affiliate_code, home_url() ) ) . '" target="_blank" rel="nofollow"><img src="' . esc_url( $creative->content ) . '" alt="' . esc_attr( $creative->creative_name ) . '" style="max-width:100%;"></a>';
                            } elseif ( $creative->creative_type === 'html_code' ) {
                                // For HTML code, we replace a placeholder for the referral URL
                                $final_content = str_replace( '{{referral_url}}', esc_url( add_query_arg( 'ref', $affiliate_code, home_url() ) ), $creative->content );
                            }
                            ?>
                            <li>
                                <strong><?php echo esc_html( $creative->creative_name ); ?> (<?php echo esc_html( ucfirst( str_replace( '_', ' ', $creative->creative_type ) ) ); ?>)</strong><br>
                                <pre style="background-color:#eee; padding:10px; border-radius:4px; overflow-x:auto;"><?php echo esc_html( $final_content ); ?></pre>
                                <button class="button button-small aslp-copy-creative-code" data-code="<?php echo esc_attr( $final_content ); ?>"><?php _e( 'Copy Code', 'as-laburda-pwa-app' ); ?></button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p><?php _e( 'No marketing creatives available yet.', 'as-laburda-pwa-app' ); ?></p>
                <?php endif; ?>

                <?php
                // Add more dashboard elements like commission history, payout history etc.
                $commission_history = $affiliate_manager->get_affiliate_commissions( $user_id );
                if ( ! empty( $commission_history ) ) {
                    echo '<h4>' . __( 'Commission History', 'as-laburda-pwa-app' ) . '</h4>';
                    echo '<table class="wp-list-table widefat fixed striped">';
                    echo '<thead><tr><th>' . __( 'Amount', 'as-laburda-pwa-app' ) . '</th><th>' . __( 'Source', 'as-laburda-pwa-app' ) . '</th><th>' . __( 'Status', 'as-laburda-pwa-app' ) . '</th><th>' . __( 'Date', 'as-laburda-pwa-app' ) . '</th></tr></thead>';
                    echo '<tbody>';
                    foreach ( $commission_history as $commission ) {
                        echo '<tr>';
                        echo '<td>' . number_format( $commission->commission_amount, 2 ) . ' ' . AS_Laburda_PWA_App_Utils::get_currency_symbol('USD') . '</td>';
                        echo '<td>' . esc_html( $commission->referral_type ) . ' (ID: ' . esc_html( $commission->referred_user_id ) . ')</td>';
                        echo '<td>' . esc_html( ucfirst( $commission->commission_status ) ) . '</td>';
                        echo '<td>' . esc_html( $commission->date_created ) . '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>' . __( 'No commission history available.', 'as-laburda-pwa-app' ) . '</p>';
                }

                $payout_history = $affiliate_manager->get_affiliate_payouts( $user_id );
                if ( ! empty( $payout_history ) ) {
                    echo '<h4>' . __( 'Payout History', 'as-laburda-pwa-app' ) . '</h4>';
                    echo '<table class="wp-list-table widefat fixed striped">';
                    echo '<thead><tr><th>' . __( 'Amount', 'as-laburda-pwa-app' ) . '</th><th>' . __( 'Method', 'as-laburda-pwa-app' ) . '</th><th>' . __( 'Status', 'as-laburda-pwa-app' ) . '</th><th>' . __( 'Requested', 'as-laburda-pwa-app' ) . '</th><th>' . __( 'Completed', 'as-laburda-pwa-app' ) . '</th></tr></thead>';
                    echo '<tbody>';
                    foreach ( $payout_history as $payout ) {
                        echo '<tr>';
                        echo '<td>' . number_format( $payout->payout_amount, 2 ) . ' ' . AS_Laburda_PWA_App_Utils::get_currency_symbol('USD') . '</td>';
                        echo '<td>' . esc_html( $payout->payout_method ) . '</td>';
                        echo '<td>' . esc_html( ucfirst( $payout->payout_status ) ) . '</td>';
                        echo '<td>' . esc_html( $payout->date_requested ) . '</td>';
                        echo '<td>' . ( ! empty( $payout->date_completed ) && $payout->date_completed !== '0000-00-00 00:00:00' ? esc_html( $payout->date_completed ) : 'N/A' ) . '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>' . __( 'No payout history available.', 'as-laburda-pwa-app' ) . '</p>';
                }
                ?>
            </div>
            <?php
        } elseif ( $affiliate_data && $affiliate_data->affiliate_status === 'pending' ) {
            // Display pending message
            ?>
            <div class="aslp-affiliate-dashboard-container">
                <h3><?php _e( 'Affiliate Application Pending', 'as-laburda-pwa-app' ); ?></h3>
                <p><?php _e( 'Your affiliate application is currently under review. We will notify you once it has been approved.', 'as-laburda-pwa-app' ); ?></p>
                <p><?php _e( 'Thank you for your patience!', 'as-laburda-pwa-app' ); ?></p>
            </div>
            <?php
        } else {
            // Display registration form
            ?>
            <div class="aslp-affiliate-registration-form">
                <h3><?php _e( 'Become an Affiliate', 'as-laburda-pwa-app' ); ?></h3>
                <p><?php _e( 'Join our affiliate program and start earning commissions by referring new users and businesses!', 'as-laburda-pwa-app' ); ?></p>
                <div id="aslp-affiliate-message"></div>
                <form id="aslp-affiliate-register-form">
                    <input type="hidden" name="action" value="aslp_affiliate_registration">
                    <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'aslp_public_nonce' ) ); ?>">

                    <label for="affiliate_email"><?php _e( 'Your Email:', 'as-laburda-pwa-app' ); ?></label>
                    <input type="email" id="affiliate_email" name="affiliate_email" value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>" required>

                    <label for="affiliate_website"><?php _e( 'Your Website (Optional):', 'as-laburda-pwa-app' ); ?></label>
                    <input type="url" id="affiliate_website" name="affiliate_website">

                    <label for="affiliate_payment_email"><?php _e( 'PayPal Email for Payouts:', 'as-laburda-pwa-app' ); ?></label>
                    <input type="email" id="affiliate_payment_email" name="affiliate_payment_email" required>

                    <button type="submit" class="button button-primary"><?php _e( 'Register as Affiliate', 'as-laburda-pwa-app' ); ?></button>
                </form>
            </div>
            <?php
        }

        return ob_get_clean();
    }

    /**
     * Shortcode to display the PWA App Builder interface for users.
     * Example: [aslp_app_builder]
     *
     * @since 2.0.0
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function display_app_builder_shortcode( $atts ) {
        // Check if App Builder is enabled globally
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            return '<p>' . __( 'PWA App Builder feature is currently disabled.', 'as-laburda-pwa-app' ) . '</p>';
        }

        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to build and manage your PWA apps.', 'as-laburda-pwa-app' ) . '</p>';
        }

        $user_id = get_current_user_id();
        if ( ! user_can( $user_id, 'aslp_create_apps' ) ) {
            return '<p>' . __( 'You do not have permission to create PWA apps. Please contact the administrator.', 'as-laburda-pwa-app' ) . '</p>';
        }

        ob_start();
        ?>
        <div class="aslp-app-builder-container">
            <h3><?php _e( 'Your PWA App Builder', 'as-laburda-pwa-app' ); ?></h3>
            <div id="aslp-app-builder-frontend-app">
                <div class="aslp-loading-overlay" style="display: none;">
                    <div class="aslp-loading-spinner"></div>
                    <p><?php _e( 'Loading app builder...', 'as-laburda-pwa-app' ); ?></p>
                </div>
                <div class="aslp-message-area"></div>

                <div class="aslp-app-list-section">
                    <h4><?php _e( 'Your Existing Apps', 'as-laburda-pwa-app' ); ?></h4>
                    <button id="aslp-create-new-app" class="button button-primary"><i class="fas fa-plus"></i> <?php _e( 'Create New App', 'as-laburda-pwa-app' ); ?></button>
                    <table class="wp-list-table widefat fixed striped pages">
                        <thead>
                            <tr>
                                <th><?php _e( 'App Name', 'as-laburda-pwa-app' ); ?></th>
                                <th><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></th>
                                <th><?php _e( 'Status', 'as-laburda-pwa-app' ); ?></th>
                                <th><?php _e( 'Date Created', 'as-laburda-pwa-app' ); ?></th>
                                <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                            </tr>
                        </thead>
                        <tbody id="aslp-user-app-list">
                            <tr>
                                <td colspan="5"><?php _e( 'No apps found.', 'as-laburda-pwa-app' ); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="aslp-app-form-section" style="display: none;">
                    <h4><?php _e( 'App Details', 'as-laburda-pwa-app' ); ?></h4>
                    <form id="aslp-app-form">
                        <input type="hidden" id="aslp-app-uuid" name="app_uuid" value="">

                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row"><label for="app_name"><?php _e( 'App Name', 'as-laburda-pwa-app' ); ?></label></th>
                                    <td><input type="text" id="app_name" name="app_name" class="regular-text" required></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="app_description"><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                                    <td><textarea id="app_description" name="description" rows="5" cols="50" class="large-text"></textarea></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="app_config_json"><?php _e( 'App Configuration (JSON)', 'as-laburda-pwa-app' ); ?></label></th>
                                    <td>
                                        <textarea id="app_config_json" name="app_config_json" rows="15" cols="70" class="large-text code" required></textarea>
                                        <p class="description"><?php _e( 'Enter the JSON configuration for your PWA manifest. This includes name, short_name, start_url, display, background_color, theme_color, and icons.', 'as-laburda-pwa-app' ); ?></p>
                                        <button type="button" id="aslp-load-template" class="button button-secondary"><?php _e( 'Load Template', 'as-laburda-pwa-app' ); ?></button>
                                        <select id="aslp-template-select" style="display:none; margin-left: 10px;">
                                            <option value=""><?php _e( 'Select a template', 'as-laburda-pwa-app' ); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e( 'App Status', 'as-laburda-pwa-app' ); ?></th>
                                    <td>
                                        <label for="app_status">
                                            <select id="app_status" name="status">
                                                <option value="pending"><?php _e( 'Pending Review', 'as-laburda-pwa-app' ); ?></option>
                                                <option value="active"><?php _e( 'Active', 'as-laburda-pwa-app' ); ?></option>
                                                <option value="inactive"><?php _e( 'Inactive', 'as-laburda-pwa-app' ); ?></option>
                                            </select>
                                            <p class="description"><?php _e( 'Apps may require admin approval to become active.', 'as-laburda-pwa-app' ); ?></p>
                                        </label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <p class="submit">
                            <button type="submit" id="aslp-save-app" class="button button-primary"><i class="fas fa-save"></i> <?php _e( 'Save App', 'as-laburda-pwa-app' ); ?></button>
                            <button type="button" id="aslp-cancel-app-edit" class="button button-secondary"><?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function($) {
                var appBuilderFrontend = {
                    init: function() {
                        this.loadUserApps();
                        this.loadAppTemplates(); // Load templates for the dropdown
                        this.bindEvents();
                    },

                    bindEvents: function() {
                        $('#aslp-create-new-app').on('click', this.showAddAppForm.bind(this));
                        $('#aslp-app-form').on('submit', this.saveApp.bind(this));
                        $('#aslp-cancel-app-edit').on('click', this.cancelEdit.bind(this));
                        $('#aslp-user-app-list').on('click', '.aslp-edit-app', this.editApp.bind(this));
                        $('#aslp-user-app-list').on('click', '.aslp-delete-app', this.deleteApp.bind(this));
                        $('#aslp-load-template').on('click', this.toggleTemplateSelect.bind(this));
                        $('#aslp-template-select').on('change', this.applyTemplate.bind(this));
                    },

                    showLoading: function() {
                        $('.aslp-loading-overlay').fadeIn();
                    },

                    hideLoading: function() {
                        $('.aslp-loading-overlay').fadeOut();
                    },

                    showMessage: function(message, type = 'success') {
                        var messageDiv = $('.aslp-message-area');
                        messageDiv.html('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
                        messageDiv.find('.notice').append('<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'as-laburda-pwa-app' ); ?></span></button>');
                        messageDiv.find('.notice-dismiss').on('click', function() {
                            $(this).closest('.notice').remove();
                        });
                    },

                    clearMessages: function() {
                        $('.aslp-message-area').empty();
                    },

                    loadUserApps: function() {
                        this.showLoading();
                        var data = {
                            'action': 'aslp_get_user_apps',
                            'nonce': aslp_public_ajax_object.nonce
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            appBuilderFrontend.hideLoading();
                            var userAppList = $('#aslp-user-app-list');
                            userAppList.empty();

                            if (response.success && response.data.apps.length > 0) {
                                $.each(response.data.apps, function(index, app) {
                                    var status = app.status.charAt(0).toUpperCase() + app.status.slice(1);
                                    var row = `
                                        <tr>
                                            <td><strong>${app.app_name}</strong></td>
                                            <td>${app.description}</td>
                                            <td>${status}</td>
                                            <td>${app.date_created}</td>
                                            <td>
                                                <button class="button button-small aslp-edit-app" data-uuid="${app.app_uuid}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                                <button class="button button-small aslp-delete-app" data-uuid="${app.app_uuid}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                            </td>
                                        </tr>
                                    `;
                                    userAppList.append(row);
                                });
                            } else {
                                userAppList.append('<tr><td colspan="5"><?php _e( 'No apps found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                            }
                        }).fail(function() {
                            appBuilderFrontend.hideLoading();
                            appBuilderFrontend.showMessage('<?php _e( 'Error loading your apps.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    loadAppTemplates: function() {
                        var data = {
                            'action': 'aslp_get_app_templates',
                            'nonce': aslp_public_ajax_object.nonce // Public nonce for templates
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            if (response.success && response.data.templates.length > 0) {
                                var templateSelect = $('#aslp-template-select');
                                templateSelect.empty().append('<option value=""><?php _e( 'Select a template', 'as-laburda-pwa-app' ); ?></option>');
                                $.each(response.data.templates, function(index, template) {
                                    templateSelect.append(`<option value="${template.id}" data-config='${template.template_data}'>${template.template_name}</option>`);
                                });
                            }
                        });
                    },

                    toggleTemplateSelect: function() {
                        $('#aslp-template-select').toggle();
                    },

                    applyTemplate: function() {
                        var selectedOption = $('#aslp-template-select option:selected');
                        if (selectedOption.val()) {
                            var templateConfig = selectedOption.data('config');
                            $('#app_config_json').val(JSON.stringify(templateConfig, null, 2)); // Pretty print JSON
                            // Optionally, pre-fill app name/description from template if available
                            // $('#app_name').val(selectedOption.text());
                            // $('#app_description').val(templateConfig.description || '');
                        }
                    },

                    showAddAppForm: function() {
                        this.clearMessages();
                        $('#aslp-app-form')[0].reset();
                        $('#aslp-app-uuid').val('');
                        $('#aslp-template-select').hide(); // Hide template select initially
                        $('.aslp-app-list-section').hide();
                        $('.aslp-app-form-section').show();
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    },

                    editApp: function(e) {
                        this.clearMessages();
                        var app_uuid = $(e.target).data('uuid');
                        this.showLoading();

                        var data = {
                            'action': 'aslp_get_app_by_id',
                            'nonce': aslp_public_ajax_object.nonce,
                            'app_uuid': app_uuid
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            appBuilderFrontend.hideLoading();
                            if (response.success && response.data.app) {
                                var app = response.data.app;
                                $('#aslp-app-uuid').val(app.app_uuid);
                                $('#app_name').val(app.app_name);
                                $('#app_description').val(app.description);
                                $('#app_config_json').val(JSON.stringify(JSON.parse(app.app_config), null, 2));
                                $('#app_status').val(app.status);
                                $('#aslp-template-select').hide();
                                $('.aslp-app-list-section').hide();
                                $('.aslp-app-form-section').show();
                                $('html, body').animate({ scrollTop: 0 }, 'slow');
                            } else {
                                appBuilderFrontend.showMessage('<?php _e( 'App not found or you do not have permission to edit it.', 'as-laburda-pwa-app' ); ?>', 'error');
                            }
                        }).fail(function() {
                            appBuilderFrontend.hideLoading();
                            appBuilderFrontend.showMessage('<?php _e( 'Error fetching app details.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    saveApp: function(e) {
                        e.preventDefault();
                        this.clearMessages();
                        this.showLoading();

                        var app_uuid = $('#aslp-app-uuid').val();
                        var app_config_raw = $('#app_config_json').val();
                        var app_config_json;

                        try {
                            app_config_json = JSON.parse(app_config_raw);
                        } catch (e) {
                            this.hideLoading();
                            this.showMessage('<?php _e( 'Invalid JSON in App Configuration. Please correct it.', 'as-laburda-pwa-app' ); ?>', 'error');
                            return;
                        }

                        var app_data_to_save = {
                            app_name: $('#app_name').val(),
                            description: $('#app_description').val(),
                            app_config: app_config_json,
                            status: $('#app_status').val(),
                        };

                        var data = {
                            'action': 'aslp_create_update_app',
                            'nonce': aslp_public_ajax_object.nonce,
                            'app_uuid': app_uuid,
                            'app_data': JSON.stringify(app_data_to_save)
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            appBuilderFrontend.hideLoading();
                            if (response.success) {
                                appBuilderFrontend.showMessage(response.data.message, 'success');
                                appBuilderFrontend.loadUserApps();
                                appBuilderFrontend.cancelEdit();
                            } else {
                                appBuilderFrontend.showMessage(response.data.message, 'error');
                            }
                        }).fail(function() {
                            appBuilderFrontend.hideLoading();
                            appBuilderFrontend.showMessage('<?php _e( 'Error saving app.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    deleteApp: function(e) {
                        if (!confirm('<?php _e( 'Are you sure you want to delete this app? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                            return;
                        }

                        this.clearMessages();
                        this.showLoading();
                        var app_uuid = $(e.target).data('uuid');

                        var data = {
                            'action': 'aslp_delete_app_frontend',
                            'nonce': aslp_public_ajax_object.nonce,
                            'app_uuid': app_uuid
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            appBuilderFrontend.hideLoading();
                            if (response.success) {
                                appBuilderFrontend.showMessage(response.data.message, 'success');
                                appBuilderFrontend.loadUserApps();
                            } else {
                                appBuilderFrontend.showMessage(response.data.message, 'error');
                            }
                        }).fail(function() {
                            appBuilderFrontend.hideLoading();
                            appBuilderFrontend.showMessage('<?php _e( 'Error deleting app.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    cancelEdit: function() {
                        this.clearMessages();
                        $('.aslp-app-form-section').hide();
                        $('.aslp-app-list-section').show();
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    }
                };
                // Conditionally initialize based on presence of the app builder container
                if ($('#aslp-app-builder-frontend-app').length) {
                    appBuilderFrontend.init();
                }


                // --- User Business Listings Functions (from partials/as-laburda-pwa-app-public-business-listings.php script block) ---
                var userBusinessListings = {
                    currentListingId: null,

                    init: function() {
                        this.loadUserListings();
                        this.bindEvents();
                    },

                    bindEvents: function() {
                        // Listing management
                        $('#aslp-add-new-listing').on('click', this.showAddListingForm.bind(this));
                        $('#aslp-listing-form').on('submit', this.saveListing.bind(this));
                        $('#aslp-cancel-listing-edit').on('click', this.cancelListingEdit.bind(this));
                        $('#aslp-user-listing-list').on('click', '.aslp-edit-listing', this.editListing.bind(this));
                        $('#aslp-user-listing-list').on('click', '.aslp-delete-listing', this.deleteListing.bind(this));

                        // Product management (if enabled)
                        if (<?php echo json_encode(AS_Laburda_PWA_App::get_instance()->get_global_feature_settings()['enable_products'] ?? false); ?>) {
                            $('#aslp-products-events-management').on('click', '#aslp-add-new-product', this.showAddProductForm.bind(this));
                            $('#aslp-product-form').on('submit', this.saveProduct.bind(this));
                            $('#aslp-product-list-container').on('click', '.aslp-edit-product', this.editProduct.bind(this));
                            $('#aslp-product-list-container').on('click', '.aslp-delete-product', this.deleteProduct.bind(this));
                            $('#aslp-product-form-modal .aslp-close-modal').on('click', this.closeProductModal.bind(this));
                        }

                        // Event management (if enabled)
                        if (<?php echo json_encode(AS_Laburda_PWA_App::get_instance()->get_global_feature_settings()['enable_events'] ?? false); ?>) {
                            $('#aslp-products-events-management').on('click', '#aslp-add-new-event', this.showAddEventForm.bind(this));
                            $('#aslp-event-form').on('submit', this.saveEvent.bind(this));
                            $('#aslp-event-list-container').on('click', '.aslp-edit-event', this.editEvent.bind(this));
                            $('#aslp-event-list-container').on('click', '.aslp-delete-event', this.deleteEvent.bind(this));
                            $('#aslp-event-form-modal .aslp-close-modal').on('click', this.closeEventModal.bind(this));
                        }

                        // Media Uploader for images (global listener)
                        $(document).on('click', '.aslp-media-upload-button', commonHandlers.openMediaUploader.bind(commonHandlers));
                    },

                    showLoading: commonHandlers.showLoading,
                    hideLoading: commonHandlers.hideLoading,
                    showMessage: commonHandlers.showMessage,
                    clearMessages: commonHandlers.clearMessages,
                    updateImagePreview: commonHandlers.updateImagePreview,

                    loadUserListings: function() {
                        this.showLoading();
                        var data = {
                            'action': 'aslp_get_user_business_listings',
                            'nonce': aslp_public_ajax_object.nonce
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            userBusinessListings.hideLoading();
                            var userListingList = $('#aslp-user-listing-list');
                            userListingList.empty();

                            if (response.success && response.data.listings.length > 0) {
                                $.each(response.data.listings, function(index, listing) {
                                    var status = listing.status.charAt(0).toUpperCase() + listing.status.slice(1);
                                    var row = `
                                        <tr>
                                            <td><strong>${listing.listing_name}</strong></td>
                                            <td>${status}</td>
                                            <td>${listing.date_created}</td>
                                            <td>
                                                <button class="button button-small aslp-edit-listing" data-id="${listing.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                                <button class="button button-small aslp-delete-listing" data-id="${listing.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                            </td>
                                        </tr>
                                    `;
                                    userListingList.append(row);
                                });
                            } else {
                                userListingList.append('<tr><td colspan="4"><?php _e( 'No business listings found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                            }
                        }).fail(function() {
                            userBusinessListings.hideLoading();
                            userBusinessListings.showMessage('<?php _e( 'Error loading your business listings.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    showAddListingForm: function() {
                        this.clearMessages();
                        $('#aslp-listing-form')[0].reset();
                        $('#aslp-listing-id').val('');
                        $('#logo_url_preview').attr('src', '').hide();
                        $('#featured_image_url_preview').attr('src', '').hide();
                        $('input[name^="custom_fields["], textarea[name^="custom_fields["], select[name^="custom_fields["]').val('');
                        $('input[type="checkbox"][name^="custom_fields["], input[type="radio"][name^="custom_fields["]').prop('checked', false);

                        $('#aslp-products-events-management').hide(); // Hide products/events section for new listing
                        $('.aslp-listing-list-section').hide();
                        $('.aslp-listing-form-section').show();
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    },

                    editListing: function(e) {
                        this.clearMessages();
                        var listing_id = $(e.target).data('id');
                        this.currentListingId = listing_id;
                        this.showLoading();

                        var data = {
                            'action': 'aslp_get_single_business_listing', // Use dedicated endpoint for single listing
                            'nonce': aslp_public_ajax_object.nonce,
                            'listing_id': listing_id
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            userBusinessListings.hideLoading();
                            if (response.success && response.data.listing) {
                                var listing = response.data.listing;
                                $('#aslp-listing-id').val(listing.id);
                                $('#listing_name').val(listing.listing_name);
                                $('#description').val(listing.description);
                                $('#address').val(listing.address);
                                $('#city').val(listing.city);
                                $('#state').val(listing.state);
                                $('#zip_code').val(listing.zip_code);
                                $('#country').val(listing.country);
                                $('#phone').val(listing.phone);
                                $('#email').val(listing.email);
                                $('#website').val(listing.website);
                                $('#logo_url').val(listing.logo_url);
                                userBusinessListings.updateImagePreview($('#logo_url'), $('#logo_url_preview'));
                                $('#featured_image_url').val(listing.featured_image_url);
                                userBusinessListings.updateImagePreview($('#featured_image_url'), $('#featured_image_url_preview'));
                                $('#status').val(listing.status);

                                // Populate custom fields
                                var custom_fields_data = JSON.parse(listing.custom_fields || '{}');
                                for (var slug in custom_fields_data) {
                                    var value = custom_fields_data[slug];
                                    var inputElement = $(`#custom_field_${slug}`);
                                    if (inputElement.attr('type') === 'checkbox') {
                                        if (Array.isArray(value)) {
                                            value.forEach(v => $(`input[name="custom_fields[${slug}][]"][value="${v}"]`).prop('checked', true));
                                        }
                                    } else if (inputElement.attr('type') === 'radio') {
                                        $(`input[name="custom_fields[${slug}]"][value="${value}"]`).prop('checked', true);
                                    } else {
                                        inputElement.val(value);
                                    }
                                }

                                // Load products and events for this listing
                                userBusinessListings.loadProducts(listing_id);
                                userBusinessListings.loadEvents(listing_id);

                                $('.aslp-listing-list-section').hide();
                                $('.aslp-listing-form-section').show();
                                $('#aslp-products-events-management').show();
                                $('html, body').animate({ scrollTop: 0 }, 'slow');
                            } else {
                                userBusinessListings.showMessage('<?php _e( 'Business listing not found or you do not have permission to edit it.', 'as-laburda-pwa-app' ); ?>', 'error');
                            }
                        }).fail(function() {
                            userBusinessListings.hideLoading();
                            userBusinessListings.showMessage('<?php _e( 'Error fetching business listing details.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    saveListing: function(e) {
                        e.preventDefault();
                        this.clearMessages();
                        this.showLoading();

                        var listing_id = $('#aslp-listing-id').val();
                        var custom_fields_data = {};
                        $('input[name^="custom_fields["], textarea[name^="custom_fields["], select[name^="custom_fields["]').each(function() {
                            var name = $(this).attr('name');
                            var match = name.match(/custom_fields\[(.*?)\]/);
                            if (match && match[1]) {
                                var slug = match[1].replace(/\[\]$/, '');
                                if ($(this).attr('type') === 'checkbox') {
                                    if (!custom_fields_data[slug]) {
                                        custom_fields_data[slug] = [];
                                    }
                                    if ($(this).is(':checked')) {
                                        custom_fields_data[slug].push($(this).val());
                                    }
                                } else if ($(this).attr('type') === 'radio') {
                                    if ($(this).is(':checked')) {
                                        custom_fields_data[slug] = $(this).val();
                                    }
                                } else {
                                    custom_fields_data[slug] = $(this).val();
                                }
                            }
                        });

                        var listing_data_to_save = {
                            listing_name: $('#listing_name').val(),
                            description: $('#description').val(),
                            address: $('#address').val(),
                            city: $('#city').val(),
                            state: $('#state').val(),
                            zip_code: $('#zip_code').val(),
                            country: $('#country').val(),
                            phone: $('#phone').val(),
                            email: $('#email').val(),
                            website: $('#website').val(),
                            logo_url: $('#logo_url').val(),
                            featured_image_url: $('#featured_image_url').val(),
                            status: $('#status').val(),
                            custom_fields: custom_fields_data
                        };

                        var data = {
                            'action': 'aslp_create_update_business_listing',
                            'nonce': aslp_public_ajax_object.nonce,
                            'listing_id': listing_id,
                            'listing_data': JSON.stringify(listing_data_to_save)
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            userBusinessListings.hideLoading();
                            if (response.success) {
                                userBusinessListings.showMessage(response.data.message, 'success');
                                userBusinessListings.loadUserListings();
                                userBusinessListings.cancelListingEdit();
                            } else {
                                userBusinessListings.showMessage(response.data.message, 'error');
                            }
                        }).fail(function() {
                            userBusinessListings.hideLoading();
                            userBusinessListings.showMessage('<?php _e( 'Error saving business listing.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    deleteListing: function(e) {
                        window.aslpShowConfirm(
                            '<?php _e( 'Delete Listing', 'as-laburda-pwa-app' ); ?>',
                            '<?php _e( 'Are you sure you want to delete this business listing? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>',
                            function() { // On confirm
                                userBusinessListings.clearMessages();
                                userBusinessListings.showLoading();
                                var listing_id = $(e.target).data('id');

                                var data = {
                                    'action': 'aslp_delete_business_listing_frontend',
                                    'nonce': aslp_public_ajax_object.nonce,
                                    'listing_id': listing_id
                                };

                                $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                                    userBusinessListings.hideLoading();
                                    if (response.success) {
                                        userBusinessListings.showMessage(response.data.message, 'success');
                                        userBusinessListings.loadUserListings();
                                    } else {
                                        userBusinessListings.showMessage(response.data.message, 'error');
                                    }
                                }).fail(function() {
                                    userBusinessListings.hideLoading();
                                    userBusinessListings.showMessage('<?php _e( 'Error deleting business listing.', 'as-laburda-pwa-app' ); ?>', 'error');
                                });
                            },
                            function() { /* On cancel - do nothing */ }
                        );
                    },

                    cancelListingEdit: function() {
                        this.clearMessages();
                        this.currentListingId = null;
                        $('.aslp-listing-form-section').hide();
                        $('#aslp-products-events-management').hide();
                        $('.aslp-listing-list-section').show();
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    },

                    // --- Product Management Functions (if enabled) ---
                    loadProducts: function(listing_id) {
                        var productListContainer = $('#aslp-product-list-container');
                        productListContainer.empty();

                        var data = {
                            'action': 'aslp_get_products_by_listing',
                            'nonce': aslp_public_ajax_object.nonce,
                            'listing_id': listing_id
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            if (response.success && response.data.products.length > 0) {
                                var productsHtml = '<table class="wp-list-table widefat fixed striped"><thead><tr><th><?php _e( 'Product Name', 'as-laburda-pwa-app' ); ?></th><th><?php _e( 'Price', 'as-laburda-pwa-app' ); ?></th><th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th></tr></thead><tbody>';
                                $.each(response.data.products, function(index, product) {
                                    productsHtml += `
                                        <tr>
                                            <td><strong>${product.product_name}</strong></td>
                                            <td>${parseFloat(product.price).toFixed(2)} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></td>
                                            <td>
                                                <button class="button button-small aslp-edit-product" data-id="${product.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                                <button class="button button-small aslp-delete-product" data-id="${product.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                            </td>
                                        </tr>
                                    `;
                                });
                                productsHtml += '</tbody></table>';
                                productListContainer.html(productsHtml);
                            } else {
                                productListContainer.html('<p><?php _e( 'No products added yet.', 'as-laburda-pwa-app' ); ?></p>');
                            }
                        }).fail(function() {
                            userBusinessListings.showMessage('<?php _e( 'Error loading products.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    showAddProductForm: function() {
                        if (!userBusinessListings.currentListingId) {
                            userBusinessListings.showMessage('<?php _e( 'Please save the listing first before adding products.', 'as-laburda-pwa-app' ); ?>', 'warning');
                            return;
                        }
                        userBusinessListings.clearMessages();
                        $('#aslp-product-form')[0].reset();
                        $('#product_id').val('');
                        $('#product_listing_id').val(userBusinessListings.currentListingId);
                        $('#product_image_url_preview').attr('src', '').hide();
                        $('#aslp-product-form-modal').fadeIn();
                    },

                    editProduct: function(e) {
                        userBusinessListings.clearMessages();
                        var product_id = $(e.target).data('id');
                        userBusinessListings.showLoading();

                        var data = {
                            'action': 'aslp_get_product',
                            'nonce': aslp_public_ajax_object.nonce,
                            'product_id': product_id
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            userBusinessListings.hideLoading();
                            if (response.success && response.data.product) {
                                var product = response.data.product;
                                $('#product_id').val(product.id);
                                $('#product_listing_id').val(product.listing_id);
                                $('#product_name').val(product.product_name);
                                $('#product_description').val(product.description);
                                $('#product_price').val(parseFloat(product.price).toFixed(2));
                                $('#product_link').val(product.link);
                                $('#product_image_url').val(product.image_url);
                                userBusinessListings.updateImagePreview($('#product_image_url'), $('#product_image_url_preview'));
                                $('#aslp-product-form-modal').fadeIn();
                            } else {
                                userBusinessListings.showMessage('<?php _e( 'Product not found or you do not have permission to edit it.', 'as-laburda-pwa-app' ); ?>', 'error');
                            }
                        }).fail(function() {
                            userBusinessListings.hideLoading();
                            userBusinessListings.showMessage('<?php _e( 'Error fetching product details.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    saveProduct: function(e) {
                        e.preventDefault();
                        userBusinessListings.clearMessages();
                        userBusinessListings.showLoading();

                        var product_id = $('#product_id').val();
                        var product_data_to_save = {
                            listing_id: $('#product_listing_id').val(),
                            product_name: $('#product_name').val(),
                            description: $('#product_description').val(),
                            price: parseFloat($('#product_price').val()),
                            link: $('#product_link').val(),
                            image_url: $('#product_image_url').val()
                        };

                        var data = {
                            'action': 'aslp_create_update_product',
                            'nonce': aslp_public_ajax_object.nonce,
                            'product_id': product_id,
                            'product_data': JSON.stringify(product_data_to_save)
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            userBusinessListings.hideLoading();
                            if (response.success) {
                                userBusinessListings.showMessage(response.data.message, 'success');
                                userBusinessListings.loadProducts(userBusinessListings.currentListingId);
                                userBusinessListings.closeProductModal();
                            } else {
                                userBusinessListings.showMessage('<?php _e( 'Error saving product.', 'as-laburda-pwa-app' ); ?>', 'error');
                            }
                        }).fail(function() {
                            userBusinessListings.hideLoading();
                            userBusinessListings.showMessage('<?php _e( 'Error saving product.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    deleteProduct: function(e) {
                        window.aslpShowConfirm(
                            '<?php _e( 'Delete Product', 'as-laburda-pwa-app' ); ?>',
                            '<?php _e( 'Are you sure you want to delete this product? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>',
                            function() { // On confirm
                                userBusinessListings.clearMessages();
                                userBusinessListings.showLoading();
                                var product_id = $(e.target).data('id');

                                var data = {
                                    'action': 'aslp_delete_product',
                                    'nonce': aslp_public_ajax_object.nonce,
                                    'product_id': product_id
                                };

                                $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                                    userBusinessListings.hideLoading();
                                    if (response.success) {
                                        userBusinessListings.showMessage(response.data.message, 'success');
                                        userBusinessListings.loadProducts(userBusinessListings.currentListingId);
                                    } else {
                                        userBusinessListings.showMessage('<?php _e( 'Error deleting product.', 'as-laburda-pwa-app' ); ?>', 'error');
                                    }
                                }).fail(function() {
                                    userBusinessListings.hideLoading();
                                    userBusinessListings.showMessage('<?php _e( 'Error deleting product.', 'as-laburda-pwa-app' ); ?>', 'error');
                                });
                            },
                            function() { /* On cancel - do nothing */ }
                        );
                    },

                    closeProductModal: function() {
                        $('#aslp-product-form-modal').fadeOut();
                    },

                    // --- Event Management Functions (if enabled) ---
                    loadEvents: function(listing_id) {
                        var eventListContainer = $('#aslp-event-list-container');
                        eventListContainer.empty();

                        var data = {
                            'action': 'aslp_get_events_by_listing',
                            'nonce': aslp_public_ajax_object.nonce,
                            'listing_id': listing_id
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            if (response.success && response.data.events.length > 0) {
                                var eventsHtml = '<table class="wp-list-table widefat fixed striped"><thead><tr><th><?php _e( 'Event Name', 'as-laburda-pwa-app' ); ?></th><th><?php _e( 'Date', 'as-laburda-pwa-app' ); ?></th><th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th></tr></thead><tbody>';
                                $.each(response.data.events, function(index, event) {
                                    eventsHtml += `
                                        <tr>
                                            <td><strong>${event.event_name}</strong></td>
                                            <td>${event.event_date}</td>
                                            <td>
                                                <button class="button button-small aslp-edit-event" data-id="${event.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                                <button class="button button-small aslp-delete-event" data-id="${event.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                            </td>
                                        </tr>
                                    `;
                                });
                                eventsHtml += '</tbody></table>';
                                eventListContainer.html(eventsHtml);
                            } else {
                                eventListContainer.html('<p><?php _e( 'No events added yet.', 'as-laburda-pwa-app' ); ?></p>');
                            }
                        }).fail(function() {
                            userBusinessListings.showMessage('<?php _e( 'Error loading events.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    showAddEventForm: function() {
                        if (!userBusinessListings.currentListingId) {
                            userBusinessListings.showMessage('<?php _e( 'Please save the listing first before adding events.', 'as-laburda-pwa-app' ); ?>', 'warning');
                            return;
                        }
                        userBusinessListings.clearMessages();
                        $('#aslp-event-form')[0].reset();
                        $('#event_id').val('');
                        $('#event_listing_id').val(userBusinessListings.currentListingId);
                        $('#aslp-event-form-modal').fadeIn();
                    },

                    editEvent: function(e) {
                        userBusinessListings.clearMessages();
                        var event_id = $(e.target).data('id');
                        userBusinessListings.showLoading();

                        var data = {
                            'action': 'aslp_get_event',
                            'nonce': aslp_public_ajax_object.nonce,
                            'event_id': event_id
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            userBusinessListings.hideLoading();
                            if (response.success && response.data.event) {
                                var event = response.data.event;
                                $('#event_id').val(event.id);
                                $('#event_listing_id').val(event.listing_id);
                                $('#event_name').val(event.event_name);
                                $('#event_description').val(event.description);
                                $('#event_date').val(event.event_date);
                                $('#event_time').val(event.event_time);
                                $('#event_location').val(event.location);
                                $('#event_link').val(event.link);
                                $('#aslp-event-form-modal').fadeIn();
                            } else {
                                userBusinessListings.showMessage('<?php _e( 'Event not found or you do not have permission to edit it.', 'as-laburda-pwa-app' ); ?>', 'error');
                            }
                        }).fail(function() {
                            userBusinessListings.hideLoading();
                            userBusinessListings.showMessage('<?php _e( 'Error fetching event details.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    saveEvent: function(e) {
                        e.preventDefault();
                        userBusinessListings.clearMessages();
                        userBusinessListings.showLoading();

                        var event_id = $('#event_id').val();
                        var event_data_to_save = {
                            listing_id: $('#event_listing_id').val(),
                            event_name: $('#event_name').val(),
                            description: $('#event_description').val(),
                            event_date: $('#event_date').val(),
                            event_time: $('#event_time').val(),
                            location: $('#event_location').val(),
                            link: $('#event_link').val()
                        };

                        var data = {
                            'action': 'aslp_create_update_event',
                            'nonce': aslp_public_ajax_object.nonce,
                            'event_id': event_id,
                            'event_data': JSON.stringify(event_data_to_save)
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            userBusinessListings.hideLoading();
                            if (response.success) {
                                userBusinessListings.showMessage(response.data.message, 'success');
                                userBusinessListings.loadEvents(userBusinessListings.currentListingId);
                                userBusinessListings.closeEventModal();
                            } else {
                                userBusinessListings.showMessage('<?php _e( 'Error saving event.', 'as-laburda-pwa-app' ); ?>', 'error');
                            }
                        }).fail(function() {
                            userBusinessListings.hideLoading();
                            userBusinessListings.showMessage('<?php _e( 'Error saving event.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    deleteEvent: function(e) {
                        window.aslpShowConfirm(
                            '<?php _e( 'Delete Event', 'as-laburda-pwa-app' ); ?>',
                            '<?php _e( 'Are you sure you want to delete this event? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>',
                            function() { // On confirm
                                userBusinessListings.clearMessages();
                                userBusinessListings.showLoading();
                                var event_id = $(e.target).data('id');

                                var data = {
                                    'action': 'aslp_delete_event',
                                    'nonce': aslp_public_ajax_object.nonce,
                                    'event_id': event_id
                                };

                                $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                                    userBusinessListings.hideLoading();
                                    if (response.success) {
                                        userBusinessListings.showMessage(response.data.message, 'success');
                                        userBusinessListings.loadEvents(userBusinessListings.currentListingId);
                                    } else {
                                        userBusinessListings.showMessage('<?php _e( 'Error deleting event.', 'as-laburda-pwa-app' ); ?>', 'error');
                                    }
                                }).fail(function() {
                                    userBusinessListings.hideLoading();
                                    userBusinessListings.showMessage('<?php _e( 'Error deleting event.', 'as-laburda-pwa-app' ); ?>', 'error');
                                });
                            },
                            function() { /* On cancel - do nothing */ }
                        );
                    },

                    closeEventModal: function() {
                        $('#aslp-event-form-modal').fadeOut();
                    }
                };
                // Conditionally initialize based on presence of the listing management container
                if ($('.aslp-business-listings-frontend-container').length) {
                    userBusinessListings.init();
                }


                // --- Affiliate Dashboard Frontend Functions (from partials/as-laburda-pwa-app-public-affiliate-dashboard.php script block) ---
                var affiliatesPublic = {
                    init: function() {
                        // Check if affiliate registration form is present, if so, just bind events for registration
                        if ($('#aslp-affiliate-register-form').length) {
                             this.bindEvents(); // Only bind events for registration form if it's there
                        } else {
                            // Otherwise, load dashboard data and bind events for dashboard features
                            this.loadDashboardData();
                            this.bindEvents();
                        }
                    },

                    bindEvents: function() {
                        $('#aslp-affiliate-register-form').on('submit', this.registerAffiliate.bind(this));
                        $('#aslp-request-payout-button').on('click', this.requestPayout.bind(this));
                        // Event listener for copying creative code/link
                        $(document).on('click', '.aslp-copy-creative-code', this.copyCreativeCode.bind(this));
                    },

                    showLoading: commonHandlers.showLoading,
                    hideLoading: commonHandlers.hideLoading,
                    showMessage: commonHandlers.showMessage,
                    clearMessages: commonHandlers.clearMessages,

                    loadDashboardData: function() {
                        this.showLoading();
                        this.clearMessages();

                        var data = {
                            'action': 'aslp_get_affiliate_data',
                            'nonce': aslp_public_ajax_object.nonce
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            affiliatesPublic.hideLoading();
                            if (response.success && response.data.affiliate_data) {
                                var affiliateData = response.data.affiliate_data;
                                $('#affiliate-wallet-balance').text(parseFloat(affiliateData.wallet_balance).toFixed(2) + ' <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?>');
                                if (affiliateData.wallet_balance <= 0) {
                                    $('#aslp-request-payout-button').prop('disabled', true);
                                } else {
                                    $('#aslp-request-payout-button').prop('disabled', false);
                                }

                                // Populate commission history
                                var commissionHtml = '<tr><td colspan="5"><?php _e( 'No recent commissions.', 'as-laburda-pwa-app' ); ?></td></tr>';
                                if (affiliateData.recent_commissions && affiliateData.recent_commissions.length > 0) {
                                    commissionHtml = '';
                                    $.each(affiliateData.recent_commissions, function(i, comm) {
                                        commissionHtml += `
                                            <tr>
                                                <td>${comm.id}</td>
                                                <td>${parseFloat(comm.commission_amount).toFixed(2)} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></td>
                                                <td>${comm.referral_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
                                                <td>${comm.commission_status.charAt(0).toUpperCase() + comm.commission_status.slice(1)}</td>
                                                <td>${comm.date_created}</td>
                                            </tr>
                                        `;
                                    });
                                }
                                $('#aslp-commission-history-list').html(commissionHtml);


                                // Populate payout history
                                var payoutHtml = '<tr><td colspan="5"><?php _e( 'No recent payouts.', 'as-laburda-pwa-app' ); ?></td></tr>';
                                if (affiliateData.recent_payouts && affiliateData.recent_payouts.length > 0) {
                                    payoutHtml = '';
                                    $.each(affiliateData.recent_payouts, function(i, payout) {
                                        var completedDate = (payout.date_completed && payout.date_completed !== '0000-00-00 00:00:00') ? payout.date_completed : 'N/A';
                                        payoutHtml += `
                                            <tr>
                                                <td>${payout.id}</td>
                                                <td>${parseFloat(payout.payout_amount).toFixed(2)} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></td>
                                                <td>${payout.payout_method.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
                                                <td>${payout.payout_status.charAt(0).toUpperCase() + payout.payout_status.slice(1)}</td>
                                                <td>${payout.date_requested}</td>
                                                <td>${completedDate}</td>
                                            </tr>
                                        `;
                                    });
                                }
                                $('#aslp-payout-history-list').html(payoutHtml);

                                // Populate creatives
                                var creativesHtml = '<tr><td colspan="2"><?php _e( 'No creatives available for your tier.', 'as-laburda-pwa-app' ); ?></td></tr>';
                                if (affiliateData.creatives && affiliateData.creatives.length > 0) {
                                    creativesHtml = '';
                                    $.each(affiliateData.creatives, function(i, creative) {
                                        var creativeContentDisplay = creative.content;
                                        var copyButtonText = '<?php _e( 'Copy Content', 'as-laburda-pwa-app' ); ?>';

                                        if (creative.creative_type === 'image_banner') {
                                            creativeContentDisplay = `<img src="${creative.content}" style="max-width: 150px; height: auto;">`;
                                            copyButtonText = '<?php _e( 'Copy Image URL', 'as-laburda-pwa-app' ); ?>';
                                        } else if (creative.creative_type === 'text_link') {
                                            // Add affiliate code to the link
                                            creativeContentDisplay = `<a href="${creative.content}?ref=${affiliateData.affiliate_code}" target="_blank">${creative.content}</a>`;
                                            copyButtonText = '<?php _e( 'Copy Link', 'as-laburda-pwa-app' ); ?>';
                                        } else if (creative.creative_type === 'html_code') {
                                            // Replace placeholder in HTML code
                                            creativeContentDisplay = creative.content.replace(/{{affiliate_code}}/g, affiliateData.affiliate_code);
                                            copyButtonText = '<?php _e( 'Copy HTML', 'as-laburda-pwa-app' ); ?>';
                                        }
                                        
                                        creativesHtml += `
                                            <tr>
                                                <td><strong>${creative.creative_name}</strong><br><small>${creative.creative_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</small></td>
                                                <td>
                                                    <div>${creativeContentDisplay}</div>
                                                    <textarea class="aslp-creative-code-snippet" style="width:100%; height:80px; margin-top:5px;" readonly>${creative.content.replace(/{{affiliate_code}}/g, affiliateData.affiliate_code)}</textarea>
                                                    <button class="button button-small aslp-copy-creative-code" data-target-text=".aslp-creative-code-snippet">${copyButtonText}</button>
                                                </td>
                                            </tr>
                                        `;
                                    });
                                }
                                $('#aslp-creative-list').html(creativesHtml);

                            } else {
                                // This handles cases where user is logged in but not an affiliate yet
                                $('#aslp-affiliate-dashboard-container').html('<p><?php _e( 'You are not registered as an affiliate or your application is pending review.', 'as-laburda-pwa-app' ); ?></p>');
                                // If the registration form is not loaded by PHP directly, uncomment the following line
                                // affiliatesPublic.showRegistrationForm();
                            }
                        }).fail(function() {
                            affiliatesPublic.hideLoading();
                            affiliatesPublic.showMessage('<?php _e( 'Error loading affiliate dashboard data.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    registerAffiliate: function(e) {
                        e.preventDefault();
                        this.showLoading();
                        this.clearMessages();

                        var formData = $(e.target).serializeArray();
                        var data = {
                            action: 'aslp_affiliate_registration',
                            nonce: aslp_public_ajax_object.nonce,
                            payment_email: $('#affiliate_payment_email').val(),
                            affiliate_email: $('#affiliate_email').val(), // Ensure these match expected backend keys
                            affiliate_website: $('#affiliate_website').val()
                        };

                        $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                            affiliatesPublic.hideLoading();
                            if (response.success) {
                                affiliatesPublic.showMessage(response.data.message, 'success');
                                affiliatesPublic.loadDashboardData(); // Refresh dashboard to show pending/active status
                            } else {
                                affiliatesPublic.showMessage(response.data.message, 'error');
                            }
                        }).fail(function() {
                            affiliatesPublic.hideLoading();
                            affiliatesPublic.showMessage('<?php _e( 'An AJAX error occurred during registration.', 'as-laburda-pwa-app' ); ?>', 'error');
                        });
                    },

                    requestPayout: function(e) {
                        e.preventDefault();
                        this.clearMessages();
                        this.showLoading();

                        var amount = parseFloat($('#affiliate-wallet-balance').text().replace(/[^0-9.]/g, '')); // Extract numeric value
                        var paymentMethod = $('#payout-method').val();

                        if (isNaN(amount) || amount <= 0) {
                            this.hideLoading();
                            this.showMessage('<?php _e( 'Please enter a valid amount greater than zero.', 'as-laburda-pwa-app' ); ?>', 'error');
                            return;
                        }

                        window.aslpShowConfirm(
                            '<?php _e( 'Confirm Payout Request', 'as-laburda-pwa-app' ); ?>',
                            '<?php _e( 'Are you sure you want to request a payout of', 'as-laburda-pwa-app' ); ?> ' + amount.toFixed(2) + ' <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?> via ' + paymentMethod + '?',
                            function() { // On confirm
                                var data = {
                                    action: 'aslp_affiliate_request_payout',
                                    nonce: aslp_public_ajax_object.nonce,
                                    amount: amount,
                                    payment_method: paymentMethod
                                };

                                $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                                    affiliatesPublic.hideLoading();
                                    if (response.success) {
                                        affiliatesPublic.showMessage(response.data.message, 'success');
                                        affiliatesPublic.loadDashboardData(); // Refresh data
                                    } else {
                                        affiliatesPublic.showMessage(response.data.message, 'error');
                                    }
                                }).fail(function() {
                                    affiliatesPublic.hideLoading();
                                    affiliatesPublic.showMessage('<?php _e( 'An AJAX error occurred during payout request.', 'as-laburda-pwa-app' ); ?>', 'error');
                                });
                            },
                            function() { // On cancel
                                affiliatesPublic.hideLoading();
                                // Do nothing
                            }
                        );
                    },

                    copyCreativeCode: function(e) {
                        var button = $(e.currentTarget);
                        var targetTextarea = button.prev('textarea.aslp-creative-code-snippet');
                        if (targetTextarea.length) {
                            targetTextarea.select();
                            try {
                                document.execCommand('copy');
                                button.text('<?php _e( 'Copied!', 'as-laburda-pwa-app' ); ?>').delay(1000).queue(function(next){
                                    $(this).text(button.data('original-text') || '<?php _e( 'Copy Code', 'as-laburda-pwa-app' ); ?>');
                                    next();
                                });
                            } catch (err) {
                                console.error('Failed to copy text: ', err);
                            }
                        }
                    }
                };
                // Conditionally initialize based on presence of the affiliate container
                if ($('.aslp-affiliate-dashboard-container').length || $('.aslp-affiliate-registration-form').length) {
                    affiliatesPublic.init();
                }
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
