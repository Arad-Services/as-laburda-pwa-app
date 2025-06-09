<?php
/**
 * The admin dashboard page for the AS Laburda PWA App plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/admin/partials
 */

// Get global feature settings
$global_features = $this->main_plugin->get_global_feature_settings();
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <p class="about-text">
        <?php _e( 'Welcome to the AS Laburda PWA App Creator! Manage your Progressive Web Apps, business listings, affiliate program, and more from one central dashboard.', 'as-laburda-pwa-app' ); ?>
    </p>

    <div id="dashboard-widgets-wrap">
        <div id="dashboard-widgets" class="metabox-holder">

            <div id="postbox-container-1" class="postbox-container">
                <div class="meta-box-sortables ui-sortable">

                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e( 'Quick Links', 'as-laburda-pwa-app' ); ?></span></h2>
                        <div class="inside">
                            <ul>
                                <?php if ( current_user_can( 'aslp_manage_apps' ) && ( $global_features['enable_app_builder'] ?? false ) ) : ?>
                                    <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-app-builder' ) ); ?>"><i class="fas fa-mobile-alt"></i> <?php _e( 'App Builder', 'as-laburda-pwa-app' ); ?></a></li>
                                <?php endif; ?>

                                <?php if ( current_user_can( 'aslp_manage_all_business_listings' ) && ( $global_features['enable_business_listings'] ?? false ) ) : ?>
                                    <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-listings' ) ); ?>"><i class="fas fa-store"></i> <?php _e( 'Business Listings', 'as-laburda-pwa-app' ); ?></a></li>
                                <?php endif; ?>

                                <?php if ( current_user_can( 'aslp_manage_listing_plans' ) && ( $global_features['enable_listing_plans'] ?? false ) ) : ?>
                                    <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-plans' ) ); ?>"><i class="fas fa-file-invoice-dollar"></i> <?php _e( 'Listing Plans', 'as-laburda-pwa-app' ); ?></a></li>
                                <?php endif; ?>

                                <?php if ( current_user_can( 'aslp_manage_app_templates' ) && ( $global_features['enable_app_builder'] ?? false ) ) : ?>
                                    <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-app-templates' ) ); ?>"><i class="fas fa-layer-group"></i> <?php _e( 'App Templates', 'as-laburda-pwa-app' ); ?></a></li>
                                <?php endif; ?>

                                <?php if ( current_user_can( 'aslp_manage_app_menus' ) && ( $global_features['enable_menus'] ?? false ) ) : ?>
                                    <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-menus' ) ); ?>"><i class="fas fa-bars"></i> <?php _e( 'App Menus', 'as-laburda-pwa-app' ); ?></a></li>
                                <?php endif; ?>

                                <?php if ( current_user_can( 'aslp_manage_custom_fields' ) && ( $global_features['enable_custom_fields'] ?? false ) ) : ?>
                                    <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-custom-fields' ) ); ?>"><i class="fas fa-cogs"></i> <?php _e( 'Custom Fields', 'as-laburda-pwa-app' ); ?></a></li>
                                <?php endif; ?>

                                <?php if ( current_user_can( 'aslp_manage_affiliates' ) && ( $global_features['enable_affiliates'] ?? false ) ) : ?>
                                    <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-affiliates' ) ); ?>"><i class="fas fa-handshake"></i> <?php _e( 'Affiliate Program', 'as-laburda-pwa-app' ); ?></a></li>
                                <?php endif; ?>

                                <?php if ( current_user_can( 'aslp_view_analytics' ) && ( $global_features['enable_analytics'] ?? false ) ) : ?>
                                    <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-analytics' ) ); ?>"><i class="fas fa-chart-bar"></i> <?php _e( 'Analytics', 'as-laburda-pwa-app' ); ?></a></li>
                                <?php endif; ?>

                                <?php if ( current_user_can( 'aslp_manage_ai_settings' ) && ( $global_features['enable_ai_agent'] ?? false ) ) : ?>
                                    <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-ai-assistant' ) ); ?>"><i class="fas fa-robot"></i> <?php _e( 'AI Assistant', 'as-laburda-pwa-app' ); ?></a></li>
                                <?php endif; ?>

                                <?php if ( current_user_can( 'manage_options' ) && ( $global_features['enable_tools_menu'] ?? false ) ) : ?>
                                    <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-tools' ) ); ?>"><i class="fas fa-tools"></i> <?php _e( 'Tools', 'as-laburda-pwa-app' ); ?></a></li>
                                <?php endif; ?>

                                <?php if ( current_user_can( 'aslp_manage_global_settings' ) ) : ?>
                                    <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-settings' ) ); ?>"><i class="fas fa-sliders-h"></i> <?php _e( 'Global Settings', 'as-laburda-pwa-app' ); ?></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e( 'Plugin Status', 'as-laburda-pwa-app' ); ?></span></h2>
                        <div class="inside">
                            <p><strong><?php _e( 'Version:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( $this->version ); ?></p>
                            <p><strong><?php _e( 'Database Tables:', 'as-laburda-pwa-app' ); ?></strong>
                                <?php
                                global $wpdb;
                                $required_tables = array(
                                    'aslp_business_listings',
                                    'aslp_listing_plans',
                                    'aslp_user_subscriptions',
                                    'aslp_notifications',
                                    'aslp_user_notification_subscriptions',
                                    'aslp_products',
                                    'aslp_events',
                                    'aslp_app_menus',
                                    'aslp_custom_fields',
                                    'aslp_pwa_apps',
                                    'aslp_app_templates',
                                    'aslp_affiliates',
                                    'aslp_affiliate_tiers',
                                    'aslp_affiliate_commissions',
                                    'aslp_affiliate_payouts',
                                    'aslp_affiliate_creatives',
                                    'aslp_analytics_views',
                                    'aslp_analytics_clicks',
                                    'aslp_ai_interactions',
                                );
                                $missing_tables = array();
                                foreach ( $required_tables as $table ) {
                                    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}{$table}'" ) != $wpdb->prefix . $table ) {
                                        $missing_tables[] = $table;
                                    }
                                }

                                if ( empty( $missing_tables ) ) {
                                    echo '<span style="color: green;"><i class="fas fa-check-circle"></i> ' . __( 'All tables are present.', 'as-laburda-pwa-app' ) . '</span>';
                                } else {
                                    echo '<span style="color: red;"><i class="fas fa-exclamation-triangle"></i> ' . __( 'Missing tables:', 'as-laburda-pwa-app' ) . ' ' . esc_html( implode( ', ', $missing_tables ) ) . '</span>';
                                    echo '<p><button id="aslp-recreate-tables" class="button button-secondary">' . __( 'Recreate Missing Tables', 'as-laburda-pwa-app' ) . '</button></p>';
                                    echo '<div id="aslp-recreate-tables-message"></div>';
                                }
                                ?>
                            </p>
                            <p><strong><?php _e( 'Active Features:', 'as-laburda-pwa-app' ); ?></strong>
                                <ul>
                                    <?php
                                    $active_features_count = 0;
                                    foreach ( $global_features as $feature_name => $is_enabled ) {
                                        if ( $is_enabled ) {
                                            echo '<li><i class="fas fa-check-circle" style="color: green;"></i> ' . esc_html( ucwords( str_replace( '_', ' ', $feature_name ) ) ) . '</li>';
                                            $active_features_count++;
                                        }
                                    }
                                    if ( $active_features_count === 0 ) {
                                        echo '<li><i class="fas fa-exclamation-circle" style="color: orange;"></i> ' . __( 'No features are currently enabled. Go to Global Settings to enable them.', 'as-laburda-pwa-app' ) . '</li>';
                                    }
                                    ?>
                                </ul>
                            </p>
                        </div>
                    </div>

                </div>
            </div><!-- /postbox-container-1 -->

            <div id="postbox-container-2" class="postbox-container">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e( 'Usage & Support', 'as-laburda-pwa-app' ); ?></span></h2>
                        <div class="inside">
                            <p><?php _e( 'Need help getting started or have questions about specific features?', 'as-laburda-pwa-app' ); ?></p>
                            <ul>
                                <li><i class="fas fa-book"></i> <a href="#" target="_blank"><?php _e( 'Documentation', 'as-laburda-pwa-app' ); ?></a></li>
                                <li><i class="fas fa-life-ring"></i> <a href="#" target="_blank"><?php _e( 'Support Forum', 'as-laburda-pwa-app' ); ?></a></li>
                                <li><i class="fas fa-bug"></i> <a href="#" target="_blank"><?php _e( 'Report a Bug', 'as-laburda-pwa-app' ); ?></a></li>
                            </ul>
                            <p><?php _e( 'Consider leaving a 5-star review if you find this plugin useful!', 'as-laburda-pwa-app' ); ?></p>
                            <p><a href="https://wordpress.org/support/plugin/as-laburda-pwa-app/reviews/" target="_blank" class="button button-secondary"><?php _e( 'Leave a Review', 'as-laburda-pwa-app' ); ?></a></p>
                        </div>
                    </div>
                </div>
            </div><!-- /postbox-container-2 -->

        </div><!-- /dashboard-widgets -->
    </div><!-- /dashboard-widgets-wrap -->
</div><!-- /wrap -->

<script>
jQuery(document).ready(function($) {
    // Handle recreate tables button click
    $('#aslp-recreate-tables').on('click', function() {
        if (confirm('<?php _e( 'Are you sure you want to recreate missing database tables? This action cannot be undone and may affect existing data if tables are dropped and recreated.', 'as-laburda-pwa-app' ); ?>')) {
            var data = {
                'action': 'aslp_admin_create_missing_pages', // Reusing this action for now, it calls activator's table creation
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                var messageDiv = $('#aslp-recreate-tables-message');
                if (response.success) {
                    messageDiv.html('<span style="color: green;"><i class="fas fa-check-circle"></i> ' + response.data.message + '</span>');
                    // Optionally, reload the page to reflect changes
                    setTimeout(function() { location.reload(); }, 2000);
                } else {
                    messageDiv.html('<span style="color: red;"><i class="fas fa-times-circle"></i> ' + response.data.message + '</span>');
                }
            });
        }
    });
});
</script>
