<?php
/**
 * The admin Business Listings page for the AS Laburda PWA App plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/admin/partials
 */
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <p><?php _e( 'Manage all business listings submitted to your platform. You can approve, reject, or delete listings, and assign plans.', 'as-laburda-pwa-app' ); ?></p>

    <div id="aslp-business-listings-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <div class="aslp-listing-list-section">
            <h2><?php _e( 'All Business Listings', 'as-laburda-pwa-app' ); ?></h2>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Listing Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Owner', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Status', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Claimed', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Current Plan', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Created', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-listing-list">
                    <tr>
                        <td colspan="7"><?php _e( 'No business listings found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var businessListings = {
        init: function() {
            this.loadListings();
            this.bindEvents();
        },

        bindEvents: function() {
            $('#aslp-listing-list').on('change', '.aslp-listing-status-select', this.updateListingStatus.bind(this));
            $('#aslp-listing-list').on('click', '.aslp-delete-listing', this.deleteListing.bind(this));
            // Add event listener for plan assignment if a modal/form is implemented later
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

        loadListings: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_get_all_business_listings',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                businessListings.hideLoading();
                var listingList = $('#aslp-listing-list');
                listingList.empty();

                if (response.success && response.data.listings.length > 0) {
                    $.each(response.data.listings, function(index, listing) {
                        var isClaimed = listing.is_claimed == 1 ? '<?php _e( 'Yes', 'as-laburda-pwa-app' ); ?>' : '<?php _e( 'No', 'as-laburda-pwa-app' ); ?>';
                        var ownerName = listing.user_id ? `User ID: ${listing.user_id}` : 'N/A'; // Will fetch actual user name later

                        // Status dropdown
                        var statusOptions = ['pending', 'active', 'rejected', 'suspended'];
                        var statusSelect = `<select class="aslp-listing-status-select" data-id="${listing.id}">`;
                        $.each(statusOptions, function(i, status) {
                            var selected = status === listing.status ? 'selected' : '';
                            statusSelect += `<option value="${status}" ${selected}>${status.charAt(0).toUpperCase() + status.slice(1)}</option>`;
                        });
                        statusSelect += `</select>`;

                        var row = `
                            <tr>
                                <td><strong>${listing.listing_name}</strong></td>
                                <td>${ownerName}</td>
                                <td>${statusSelect}</td>
                                <td>${isClaimed}</td>
                                <td>Plan ID: ${listing.current_plan_id}</td>
                                <td>${listing.date_created}</td>
                                <td>
                                    <button class="button button-small aslp-delete-listing" data-id="${listing.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        listingList.append(row);
                    });
                } else {
                    listingList.append('<tr><td colspan="7"><?php _e( 'No business listings found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                businessListings.hideLoading();
                businessListings.showMessage('<?php _e( 'Error loading business listings.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        updateListingStatus: function(e) {
            this.clearMessages();
            this.showLoading();
            var listing_id = $(e.target).data('id');
            var new_status = $(e.target).val();

            var data = {
                'action': 'aslp_update_business_listing_status',
                'nonce': aslp_ajax_object.nonce,
                'listing_id': listing_id,
                'status': new_status
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                businessListings.hideLoading();
                if (response.success) {
                    businessListings.showMessage(response.data.message, 'success');
                    // No need to reload all listings, status is already updated in dropdown
                } else {
                    businessListings.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                businessListings.hideLoading();
                businessListings.showMessage('<?php _e( 'Error updating listing status.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deleteListing: function(e) {
            if (!confirm('<?php _e( 'Are you sure you want to delete this business listing? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                return;
            }

            this.clearMessages();
            this.showLoading();
            var listing_id = $(e.target).data('id');

            var data = {
                'action': 'aslp_delete_business_listing',
                'nonce': aslp_ajax_object.nonce,
                'listing_id': listing_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                businessListings.hideLoading();
                if (response.success) {
                    businessListings.showMessage(response.data.message, 'success');
                    businessListings.loadListings(); // Reload list after deletion
                } else {
                    businessListings.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                businessListings.hideLoading();
                businessListings.showMessage('<?php _e( 'Error deleting business listing.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        }
    };

    businessListings.init();
});
</script>
