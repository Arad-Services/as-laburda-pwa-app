<?php
/**
 * The public-facing user dashboard for the AS Laburda PWA App plugin.
 *
 * This file is used to markup the public-facing aspects of the plugin,
 * specifically the user's personal dashboard where they can manage their
 * apps, listings, etc.
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/public/partials
 */

// Ensure the user is logged in to view this dashboard
if ( ! is_user_logged_in() ) {
    echo '<p>' . __( 'Please log in to view your dashboard.', 'as-laburda-pwa-app' ) . '</p>';
    return;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Get global feature settings to conditionally display sections
$global_features = AS_Laburda_PWA_App::get_instance()->get_global_feature_settings();

?>

<div class="wrap aslp-public-dashboard-container">
    <h1><?php echo sprintf( __( 'Welcome, %s!', 'as-laburda-pwa-app' ), esc_html( $current_user->display_name ) ); ?></h1>

    <p><?php _e( 'This is your personal dashboard. Here you can manage your PWA apps, business listings, and other related features.', 'as-laburda-pwa-app' ); ?></p>

    <h2 class="nav-tab-wrapper">
        <a href="#dashboard-overview" data-tab="overview" class="nav-tab nav-tab-active"><?php _e( 'Overview', 'as-laburda-pwa-app' ); ?></a>
        <?php if ( ( $global_features['enable_app_builder'] ?? false ) && user_can( $user_id, 'aslp_create_apps' ) ) : ?>
            <a href="#dashboard-apps" data-tab="apps" class="nav-tab"><?php _e( 'My Apps', 'as-laburda-pwa-app' ); ?></a>
        <?php endif; ?>
        <?php if ( ( $global_features['enable_business_listings'] ?? false ) && user_can( $user_id, 'aslp_submit_business_listing' ) ) : ?>
            <a href="#dashboard-listings" data-tab="listings" class="nav-tab"><?php _e( 'My Listings', 'as-laburda-pwa-app' ); ?></a>
        <?php endif; ?>
        <?php if ( ( $global_features['enable_affiliates'] ?? false ) && user_can( $user_id, 'aslp_view_affiliate_dashboard' ) ) : ?>
            <a href="#dashboard-affiliate" data-tab="affiliate" class="nav-tab"><?php _e( 'Affiliate Program', 'as-laburda-pwa-app' ); ?></a>
        <?php endif; ?>
        <?php if ( ( $global_features['enable_notifications'] ?? false ) ) : ?>
            <a href="#dashboard-notifications" data-tab="notifications" class="nav-tab"><?php _e( 'Notifications', 'as-laburda-pwa-app' ); ?></a>
        <?php endif; ?>
    </h2>

    <div id="aslp-public-dashboard-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading data...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <!-- Overview Tab -->
        <div id="dashboard-overview" class="aslp-tab-content active">
            <h2><?php _e( 'Dashboard Overview', 'as-laburda-pwa-app' ); ?></h2>
            <div class="aslp-dashboard-cards">
                <?php if ( ( $global_features['enable_app_builder'] ?? false ) && user_can( $user_id, 'aslp_create_apps' ) ) : ?>
                    <div class="aslp-card">
                        <h3><?php _e( 'Your Apps', 'as-laburda-pwa-app' ); ?></h3>
                        <p id="overview-my-apps" class="aslp-card-value">0</p>
                        <a href="#dashboard-apps" data-tab="apps" class="button button-secondary aslp-switch-tab"><?php _e( 'Manage Apps', 'as-laburda-pwa-app' ); ?></a>
                    </div>
                <?php endif; ?>
                <?php if ( ( $global_features['enable_business_listings'] ?? false ) && user_can( $user_id, 'aslp_submit_business_listing' ) ) : ?>
                    <div class="aslp-card">
                        <h3><?php _e( 'Your Listings', 'as-laburda-pwa-app' ); ?></h3>
                        <p id="overview-my-listings" class="aslp-card-value">0</p>
                        <a href="#dashboard-listings" data-tab="listings" class="button button-secondary aslp-switch-tab"><?php _e( 'Manage Listings', 'as-laburda-pwa-app' ); ?></a>
                    </div>
                <?php endif; ?>
                <?php if ( ( $global_features['enable_affiliates'] ?? false ) && user_can( $user_id, 'aslp_view_affiliate_dashboard' ) ) : ?>
                    <div class="aslp-card">
                        <h3><?php _e( 'Affiliate Wallet', 'as-laburda-pwa-app' ); ?></h3>
                        <p id="overview-affiliate-wallet" class="aslp-card-value">0.00 <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></p>
                        <a href="#dashboard-affiliate" data-tab="affiliate" class="button button-secondary aslp-switch-tab"><?php _e( 'Go to Affiliate', 'as-laburda-pwa-app' ); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- My Apps Tab -->
        <?php if ( ( $global_features['enable_app_builder'] ?? false ) && user_can( $user_id, 'aslp_create_apps' ) ) : ?>
            <div id="dashboard-apps" class="aslp-tab-content" style="display:none;">
                <h2><?php _e( 'My PWA Apps', 'as-laburda-pwa-app' ); ?></h2>
                <div id="aslp-app-builder-frontend-container">
                    <!-- This content will be loaded by the shortcode or dynamically -->
                    <?php echo do_shortcode( '[aslp_app_builder]' ); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- My Listings Tab -->
        <?php if ( ( $global_features['enable_business_listings'] ?? false ) && user_can( $user_id, 'aslp_submit_business_listing' ) ) : ?>
            <div id="dashboard-listings" class="aslp-tab-content" style="display:none;">
                <h2><?php _e( 'My Business Listings', 'as-laburda-pwa-app' ); ?></h2>
                <div id="aslp-business-listings-frontend-container">
                    <!-- This content will be loaded by the shortcode or dynamically -->
                    <?php echo do_shortcode( '[aslp_user_business_listings]' ); // Assuming a shortcode for user's listings ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Affiliate Program Tab -->
        <?php if ( ( $global_features['enable_affiliates'] ?? false ) && user_can( $user_id, 'aslp_view_affiliate_dashboard' ) ) : ?>
            <div id="dashboard-affiliate" class="aslp-tab-content" style="display:none;">
                <h2><?php _e( 'Affiliate Program', 'as-laburda-pwa-app' ); ?></h2>
                <div id="aslp-affiliate-dashboard-container">
                    <!-- This content will be loaded by the shortcode or dynamically -->
                    <?php echo do_shortcode( '[aslp_affiliate_dashboard]' ); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Notifications Tab -->
        <?php if ( ( $global_features['enable_notifications'] ?? false ) ) : ?>
            <div id="dashboard-notifications" class="aslp-tab-content" style="display:none;">
                <h2><?php _e( 'My Notifications', 'as-laburda-pwa-app' ); ?></h2>
                <div id="aslp-notifications-container">
                    <p><?php _e( 'You have no new notifications.', 'as-laburda-pwa-app' ); ?></p>
                    <!-- Future: Implement notification display and management -->
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var publicDashboard = {
        init: function() {
            this.bindTabEvents();
            this.loadOverviewData();
            // Initial load of content for the active tab if it's not overview
            var initialTab = $('.nav-tab-wrapper .nav-tab-active').data('tab');
            if (initialTab !== 'overview') {
                this.loadTabContent(initialTab);
            }
        },

        bindTabEvents: function() {
            var self = this;
            $('.nav-tab-wrapper a').on('click', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.aslp-tab-content').hide();
                $('#dashboard-' + tab).show();
                self.loadTabContent(tab); // Load content when tab is clicked
            });

            // For buttons inside overview cards that switch tabs
            $(document).on('click', '.aslp-switch-tab', function(e) {
                e.preventDefault();
                var targetTab = $(this).data('tab');
                $('.nav-tab[data-tab="' + targetTab + '"]').click(); // Simulate click on the tab
            });
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

        loadOverviewData: function() {
            this.showLoading();
            this.clearMessages();

            var appsPromise = $.post(aslp_public_ajax_object.ajax_url, {
                action: 'aslp_get_user_apps',
                nonce: aslp_public_ajax_object.nonce
            });

            var listingsPromise = $.post(aslp_public_ajax_object.ajax_url, {
                action: 'aslp_get_user_business_listings', // Assuming a new AJAX action for user's listings
                nonce: aslp_public_ajax_object.nonce
            });

            var affiliatePromise = $.post(aslp_public_ajax_object.ajax_url, {
                action: 'aslp_get_affiliate_data', // Assuming a new AJAX action for user's affiliate data
                nonce: aslp_public_ajax_object.nonce
            });

            $.when(appsPromise, listingsPromise, affiliatePromise)
                .done(function(appsResponse, listingsResponse, affiliateResponse) {
                    publicDashboard.hideLoading();

                    // Update Apps Overview
                    if (appsResponse[0].success) {
                        $('#overview-my-apps').text(appsResponse[0].data.apps.length);
                    } else {
                        console.error('Error loading user apps for overview:', appsResponse[0].data.message);
                        $('#overview-my-apps').text('N/A');
                    }

                    // Update Listings Overview
                    if (listingsResponse[0].success) {
                        $('#overview-my-listings').text(listingsResponse[0].data.listings.length);
                    } else {
                        console.error('Error loading user listings for overview:', listingsResponse[0].data.message);
                        $('#overview-my-listings').text('N/A');
                    }

                    // Update Affiliate Overview
                    if (affiliateResponse[0].success && affiliateResponse[0].data.affiliate_data) {
                        var walletBalance = parseFloat(affiliateResponse[0].data.affiliate_data.wallet_balance || 0).toFixed(2);
                        $('#overview-affiliate-wallet').text(walletBalance + ' <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?>');
                    } else {
                        console.error('Error loading affiliate data for overview:', affiliateResponse[0].data.message);
                        $('#overview-affiliate-wallet').text('N/A');
                    }

                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    publicDashboard.hideLoading();
                    publicDashboard.showMessage('<?php _e( 'An error occurred while loading dashboard overview data: ', 'as-laburda-pwa-app' ); ?>' + textStatus, 'error');
                    console.error('Public Dashboard Overview AJAX error:', textStatus, errorThrown);
                });
        },

        loadTabContent: function(tab) {
            // This function is primarily for tabs that load content dynamically if they don't use shortcodes
            // For tabs using do_shortcode() directly in PHP, this might not be strictly necessary
            // but can be used for additional dynamic loading or refreshing.
            switch (tab) {
                case 'apps':
                    // The [aslp_app_builder] shortcode already renders its content.
                    // If you want to dynamically reload it, you'd need an AJAX endpoint
                    // that returns the shortcode's HTML.
                    // For now, assume it's rendered on page load by PHP.
                    break;
                case 'listings':
                    // Similar to apps, [aslp_user_business_listings] is expected to render.
                    break;
                case 'affiliate':
                    // Similar to apps, [aslp_affiliate_dashboard] is expected to render.
                    // However, the JS for affiliate dashboard is already in public.js.
                    // We can trigger a refresh of affiliate data here if needed.
                    // if (typeof loadAffiliateData === 'function') {
                    //     loadAffiliateData();
                    // }
                    break;
                case 'notifications':
                    // Example: Fetch and display notifications
                    // this.showLoading();
                    // $.post(aslp_public_ajax_object.ajax_url, {
                    //     action: 'aslp_get_user_notifications',
                    //     nonce: aslp_public_ajax_object.nonce
                    // }, function(response) {
                    //     publicDashboard.hideLoading();
                    //     if (response.success && response.data.notifications.length > 0) {
                    //         var notificationsHtml = '<ul>';
                    //         $.each(response.data.notifications, function(i, notif) {
                    //             notificationsHtml += `<li><strong>${notif.title}</strong>: ${notif.message} <small>(${notif.date})</small></li>`;
                    //         });
                    //         notificationsHtml += '</ul>';
                    //         $('#aslp-notifications-container').html(notificationsHtml);
                    //     } else {
                    //         $('#aslp-notifications-container').html('<p><?php _e( 'You have no new notifications.', 'as-laburda-pwa-app' ); ?></p>');
                    //     }
                    // }).fail(function() {
                    //     publicDashboard.hideLoading();
                    //     publicDashboard.showMessage('<?php _e( 'Error loading notifications.', 'as-laburda-pwa-app' ); ?>', 'error');
                    // });
                    break;
            }
        }
    };

    publicDashboard.init();
});
</script>
