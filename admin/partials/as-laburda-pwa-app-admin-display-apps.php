<?php
/**
 * The admin Dashboard page for the AS Laburda PWA App plugin.
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

    <p><?php _e( 'Welcome to the AS Laburda PWA App Creator Dashboard! Here you can get an overview of your PWA apps, business listings, and affiliate program performance.', 'as-laburda-pwa-app' ); ?></p>

    <div id="aslp-admin-dashboard-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading dashboard data...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <div class="aslp-dashboard-overview">
            <h2><?php _e( 'Quick Overview', 'as-laburda-pwa-app' ); ?></h2>
            <div class="aslp-dashboard-stats">
                <div class="aslp-stat-card">
                    <h3><?php _e( 'Total Apps', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="dashboard-total-apps">0</p>
                </div>
                <div class="aslp-stat-card">
                    <h3><?php _e( 'Total Business Listings', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="dashboard-total-listings">0</p>
                </div>
                <div class="aslp-stat-card">
                    <h3><?php _e( 'Active Affiliates', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="dashboard-active-affiliates">0</p>
                </div>
                <div class="aslp-stat-card">
                    <h3><?php _e( 'Pending Listings', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="dashboard-pending-listings">0</p>
                </div>
                <div class="aslp-stat-card">
                    <h3><?php _e( 'Pending Affiliates', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="dashboard-pending-affiliates">0</p>
                </div>
                <div class="aslp-stat-card">
                    <h3><?php _e( 'Total Commissions Earned', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="dashboard-total-commissions">0.00</p>
                </div>
            </div>
        </div>

        <div class="aslp-recent-data-section">
            <div class="aslp-half-width">
                <h2><?php _e( 'Recent Apps', 'as-laburda-pwa-app' ); ?></h2>
                <ul id="dashboard-recent-apps" class="aslp-list">
                    <li><?php _e( 'No recent apps found.', 'as-laburda-pwa-app' ); ?></li>
                </ul>
            </div>
            <div class="aslp-half-width">
                <h2><?php _e( 'Recent Business Listings', 'as-laburda-pwa-app' ); ?></h2>
                <ul id="dashboard-recent-listings" class="aslp-list">
                    <li><?php _e( 'No recent listings found.', 'as-laburda-pwa-app' ); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var dashboard = {
        init: function() {
            this.loadDashboardData();
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

        loadDashboardData: function() {
            this.showLoading();
            this.clearMessages();

            // Fetch apps data
            var appsPromise = $.post(aslp_ajax_object.ajax_url, {
                action: 'aslp_get_all_apps',
                nonce: aslp_ajax_object.nonce
            });

            // Fetch listings data
            var listingsPromise = $.post(aslp_ajax_object.ajax_url, {
                action: 'aslp_get_all_business_listings',
                nonce: aslp_ajax_object.nonce
            });

            // Fetch affiliates data
            var affiliatesPromise = $.post(aslp_ajax_object.ajax_url, {
                action: 'aslp_admin_get_affiliates',
                nonce: aslp_ajax_object.nonce
            });

            // Fetch commissions data
            var commissionsPromise = $.post(aslp_ajax_object.ajax_url, {
                action: 'aslp_admin_manage_commissions',
                nonce: aslp_ajax_object.nonce,
                sub_action: 'get_all'
            });

            $.when(appsPromise, listingsPromise, affiliatesPromise, commissionsPromise)
                .done(function(appsResponse, listingsResponse, affiliatesResponse, commissionsResponse) {
                    dashboard.hideLoading();

                    // Process Apps
                    if (appsResponse[0].success) {
                        var apps = appsResponse[0].data.apps;
                        $('#dashboard-total-apps').text(apps.length);
                        var recentAppsList = $('#dashboard-recent-apps');
                        recentAppsList.empty();
                        if (apps.length > 0) {
                            // Sort by date_created descending and take top 5
                            apps.sort((a, b) => new Date(b.date_created) - new Date(a.date_created));
                            $.each(apps.slice(0, 5), function(index, app) {
                                recentAppsList.append(`<li><strong>${app.app_name}</strong> - ${app.description.substring(0, 70)}${app.description.length > 70 ? '...' : ''} <br> <small><em>(UUID: ${app.app_uuid})</em></small></li>`);
                            });
                        } else {
                            recentAppsList.append('<li><?php _e( 'No recent apps found.', 'as-laburda-pwa-app' ); ?></li>');
                        }
                    } else {
                        dashboard.showMessage(appsResponse[0].data.message, 'error');
                    }

                    // Process Listings
                    if (listingsResponse[0].success) {
                        var listings = listingsResponse[0].data.listings;
                        $('#dashboard-total-listings').text(listings.length);
                        var pendingListings = listings.filter(listing => listing.status === 'pending').length;
                        $('#dashboard-pending-listings').text(pendingListings);
                        var recentListingsList = $('#dashboard-recent-listings');
                        recentListingsList.empty();
                        if (listings.length > 0) {
                            // Sort by date_created descending and take top 5
                            listings.sort((a, b) => new Date(b.date_created) - new Date(a.date_created));
                            $.each(listings.slice(0, 5), function(index, listing) {
                                recentListingsList.append(`<li><strong>${listing.listing_name}</strong> - Status: ${listing.status} <br> <small><em>(Owner: ${listing.user_id})</em></small></li>`);
                            });
                        } else {
                            recentListingsList.append('<li><?php _e( 'No recent listings found.', 'as-laburda-pwa-app' ); ?></li>');
                        }
                    } else {
                        dashboard.showMessage(listingsResponse[0].data.message, 'error');
                    }

                    // Process Affiliates
                    if (affiliatesResponse[0].success) {
                        var affiliates = affiliatesResponse[0].data.affiliates;
                        var activeAffiliates = affiliates.filter(affiliate => affiliate.affiliate_status === 'active').length;
                        var pendingAffiliates = affiliates.filter(affiliate => affiliate.affiliate_status === 'pending').length;
                        $('#dashboard-active-affiliates').text(activeAffiliates);
                        $('#dashboard-pending-affiliates').text(pendingAffiliates);
                    } else {
                        dashboard.showMessage(affiliatesResponse[0].data.message, 'error');
                    }

                    // Process Commissions
                    if (commissionsResponse[0].success) {
                        var commissions = commissionsResponse[0].data.commissions;
                        var totalCommissions = commissions.reduce((sum, comm) => sum + parseFloat(comm.commission_amount), 0);
                        $('#dashboard-total-commissions').text(totalCommissions.toFixed(2) + ' <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?>');
                    } else {
                        dashboard.showMessage(commissionsResponse[0].data.message, 'error');
                    }

                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    dashboard.hideLoading();
                    dashboard.showMessage('<?php _e( 'An error occurred while loading dashboard data: ', 'as-laburda-pwa-app' ); ?>' + textStatus, 'error');
                    console.error('Dashboard AJAX error:', textStatus, errorThrown);
                });
        }
    };

    dashboard.init();
});
</script>
