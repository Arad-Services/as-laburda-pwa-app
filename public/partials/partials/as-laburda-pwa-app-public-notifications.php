<?php
/**
 * The public-facing notifications management for the AS Laburda PWA App plugin.
 *
 * This file is used to markup the public-facing aspects of the plugin,
 * specifically the user's notification subscriptions.
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/public/partials
 */

// Ensure the user is logged in to view this page
if ( ! is_user_logged_in() ) {
    echo '<p>' . __( 'Please log in to manage your notifications.', 'as-laburda-pwa-app' ) . '</p>';
    return;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Get global feature settings to conditionally display sections
$global_features = AS_Laburda_PWA_App::get_instance()->get_global_feature_settings();

// Check if notifications feature is enabled globally
if ( ! ( $global_features['enable_notifications'] ?? false ) ) {
    echo '<p>' . __( 'Notifications feature is currently disabled.', 'as-laburda-pwa-app' ) . '</p>';
    return;
}

?>

<div class="aslp-notifications-frontend-container">
    <h3><?php echo sprintf( __( 'Notifications for %s', 'as-laburda-pwa-app' ), esc_html( $current_user->display_name ) ); ?></h3>

    <div id="aslp-user-notifications-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading notifications...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <div class="aslp-subscribed-businesses-list-section">
            <h4><?php _e( 'My Subscribed Businesses', 'as-laburda-pwa-app' ); ?></h4>
            <p class="description"><?php _e( 'Manage your subscriptions to receive notifications from businesses.', 'as-laburda-pwa-app' ); ?></p>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Business Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Subscribed', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-subscribed-businesses-list">
                    <tr>
                        <td colspan="3"><?php _e( 'You are not subscribed to any businesses for notifications.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="aslp-find-businesses-for-notifications-section" style="margin-top: 30px;">
            <h4><?php _e( 'Find Businesses to Subscribe To', 'as-laburda-pwa-app' ); ?></h4>
            <p class="description"><?php _e( 'Search for businesses to subscribe to their notifications.', 'as-laburda-pwa-app' ); ?></p>
            <input type="text" id="aslp-business-search-input" placeholder="<?php _e( 'Search business by name...', 'as-laburda-pwa-app' ); ?>" class="regular-text">
            <button id="aslp-search-businesses-button" class="button button-secondary"><i class="fas fa-search"></i> <?php _e( 'Search', 'as-laburda-pwa-app' ); ?></button>

            <table class="wp-list-table widefat fixed striped pages" style="margin-top: 15px;">
                <thead>
                    <tr>
                        <th><?php _e( 'Business Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Status', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-search-results-list">
                    <tr>
                        <td colspan="3"><?php _e( 'Search results will appear here.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var userNotifications = {
        init: function() {
            this.loadSubscribedBusinesses();
            this.bindEvents();
        },

        bindEvents: function() {
            // Subscription management
            $('#aslp-subscribed-businesses-list').on('click', '.aslp-unsubscribe-button', this.toggleSubscription.bind(this, false));
            $('#aslp-search-results-list').on('click', '.aslp-subscribe-button', this.toggleSubscription.bind(this, true));
            
            // Search functionality
            $('#aslp-search-businesses-button').on('click', this.searchBusinesses.bind(this));
            $('#aslp-business-search-input').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    userNotifications.searchBusinesses();
                }
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

        loadSubscribedBusinesses: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_get_user_subscribed_businesses',
                'nonce': aslp_public_ajax_object.nonce
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                userNotifications.hideLoading();
                var subscribedList = $('#aslp-subscribed-businesses-list');
                subscribedList.empty();

                if (response.success && response.data.businesses.length > 0) {
                    $.each(response.data.businesses, function(index, business) {
                        var isSubscribed = userNotifications.isUserCurrentlySubscribed(business.id); // Check current state
                        var buttonText = isSubscribed ? '<?php _e( 'Unsubscribe', 'as-laburda-pwa-app' ); ?>' : '<?php _e( 'Subscribe', 'as-laburda-pwa-app' ); ?>';
                        var buttonClass = isSubscribed ? 'aslp-unsubscribe-button' : 'aslp-subscribe-button';
                        var subscribedStatusText = isSubscribed ? '<?php _e( 'Yes', 'as-laburda-pwa-app' ); ?>' : '<?php _e( 'No', 'as-laburda-pwa-app' ); ?>';

                        var row = `
                            <tr>
                                <td><strong>${business.listing_name}</strong></td>
                                <td>${subscribedStatusText}</td>
                                <td>
                                    <button class="button button-small ${buttonClass}" data-id="${business.id}">${buttonText}</button>
                                </td>
                            </tr>
                        `;
                        subscribedList.append(row);
                    });
                } else {
                    subscribedList.append('<tr><td colspan="3"><?php _e( 'You are not subscribed to any businesses for notifications.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                userNotifications.hideLoading();
                userNotifications.showMessage('<?php _e( 'Error loading subscribed businesses.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        searchBusinesses: function() {
            this.showLoading();
            this.clearMessages();
            var searchTerm = $('#aslp-business-search-input').val().trim();
            var searchResultsList = $('#aslp-search-results-list');
            searchResultsList.empty();

            if (searchTerm === '') {
                this.hideLoading();
                searchResultsList.html('<tr><td colspan="3"><?php _e( 'Please enter a business name to search.', 'as-laburda-pwa-app' ); ?></td></tr>');
                return;
            }

            var data = {
                'action': 'aslp_search_businesses_for_notifications', // New AJAX action for searching all active businesses
                'nonce': aslp_public_ajax_object.nonce,
                'search_term': searchTerm
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                userNotifications.hideLoading();
                if (response.success && response.data.businesses.length > 0) {
                    $.each(response.data.businesses, function(index, business) {
                        var isSubscribed = userNotifications.isUserCurrentlySubscribed(business.id);
                        var buttonText = isSubscribed ? '<?php _e( 'Unsubscribe', 'as-laburda-pwa-app' ); ?>' : '<?php _e( 'Subscribe', 'as-laburda-pwa-app' ); ?>';
                        var buttonClass = isSubscribed ? 'aslp-unsubscribe-button' : 'aslp-subscribe-button';

                        var row = `
                            <tr>
                                <td><strong>${business.listing_name}</strong></td>
                                <td>${business.status.charAt(0).toUpperCase() + business.status.slice(1)}</td>
                                <td>
                                    <button class="button button-small ${buttonClass}" data-id="${business.id}">${buttonText}</button>
                                </td>
                            </tr>
                        `;
                        searchResultsList.append(row);
                    });
                } else {
                    searchResultsList.html('<tr><td colspan="3"><?php _e( 'No businesses found matching your search.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                userNotifications.hideLoading();
                userNotifications.showMessage('<?php _e( 'Error searching for businesses.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        toggleSubscription: function(subscribeStatus, e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var business_id = $(e.target).data('id');
            var actionType = subscribeStatus ? 'subscribe' : 'unsubscribe';

            var data = {
                'action': 'aslp_toggle_business_notification',
                'nonce': aslp_public_ajax_object.nonce,
                'business_listing_id': business_id,
                'is_subscribed': subscribeStatus ? 1 : 0
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                userNotifications.hideLoading();
                if (response.success) {
                    userNotifications.showMessage(response.data.message, 'success');
                    userNotifications.loadSubscribedBusinesses(); // Reload subscribed list
                    userNotifications.searchBusinesses(); // Refresh search results to update buttons
                } else {
                    userNotifications.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                userNotifications.hideLoading();
                userNotifications.showMessage('<?php _e( 'Error updating subscription status.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        // Helper to check if a business is in the current subscribed list (imperfect, better with backend state)
        isUserCurrentlySubscribed: function(business_id) {
            // This is a client-side check based on the displayed list,
            // for a perfect real-time sync, you might need to fetch
            // the user's subscription status from backend more directly.
            var subscribedBusinesses = $('#aslp-subscribed-businesses-list').find('.aslp-unsubscribe-button').map(function() {
                return $(this).data('id');
            }).get();
            return subscribedBusinesses.includes(business_id);
        }
    };

    // Initialize userNotifications if the container is present
    if ($('.aslp-notifications-frontend-container').length) {
        userNotifications.init();
    }
});
</script>
