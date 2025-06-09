<?php
/**
 * The admin Analytics page for the AS Laburda PWA App plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/admin/partials
 */
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <p><?php _e( 'View analytics data for your Progressive Web Apps, business listings, and affiliate program. Track views, clicks, and user engagement.', 'as-laburda-pwa-app' ); ?></p>

    <h2 class="nav-tab-wrapper">
        <a href="#analytics-overview" data-tab="overview" class="nav-tab nav-tab-active"><?php _e( 'Overview', 'as-laburda-pwa-app' ); ?></a>
        <a href="#analytics-apps" data-tab="apps" class="nav-tab"><?php _e( 'Apps', 'as-laburda-pwa-app' ); ?></a>
        <a href="#analytics-listings" data-tab="listings" class="nav-tab"><?php _e( 'Listings', 'as-laburda-pwa-app' ); ?></a>
        <a href="#analytics-affiliates" data-tab="affiliates" class="nav-tab"><?php _e( 'Affiliates', 'as-laburda-pwa-app' ); ?></a>
    </h2>

    <div id="aslp-analytics-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <!-- Overview Tab -->
        <div id="analytics-overview" class="aslp-tab-content active">
            <h2><?php _e( 'Overall Analytics Overview', 'as-laburda-pwa-app' ); ?></h2>
            <div class="aslp-dashboard-cards">
                <div class="aslp-card">
                    <h3><?php _e( 'Total App Views', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="overview-total-app-views" class="aslp-card-value">0</p>
                </div>
                <div class="aslp-card">
                    <h3><?php _e( 'Total Listing Views', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="overview-total-listing-views" class="aslp-card-value">0</p>
                </div>
                <div class="aslp-card">
                    <h3><?php _e( 'Total Affiliate Clicks', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="overview-total-affiliate-clicks" class="aslp-card-value">0</p>
                </div>
                <div class="aslp-card">
                    <h3><?php _e( 'Total Signups (via Affiliate)', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="overview-total-affiliate-signups" class="aslp-card-value">0</p>
                </div>
            </div>

            <div class="aslp-chart-container" style="margin-top: 30px;">
                <h3><?php _e( 'Views Over Time', 'as-laburda-pwa-app' ); ?></h3>
                <canvas id="viewsChart" width="400" height="150"></canvas>
            </div>
        </div>

        <!-- Apps Analytics Tab -->
        <div id="analytics-apps" class="aslp-tab-content" style="display:none;">
            <h2><?php _e( 'PWA App Analytics', 'as-laburda-pwa-app' ); ?></h2>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'App Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Total Views', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Unique Views', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Last Viewed', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-app-analytics-list">
                    <tr>
                        <td colspan="4"><?php _e( 'No app analytics data found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Listings Analytics Tab -->
        <div id="analytics-listings" class="aslp-tab-content" style="display:none;">
            <h2><?php _e( 'Business Listing Analytics', 'as-laburda-pwa-app' ); ?></h2>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Listing Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Total Views', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Unique Views', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Last Viewed', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-listing-analytics-list">
                    <tr>
                        <td colspan="4"><?php _e( 'No listing analytics data found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Affiliates Analytics Tab -->
        <div id="analytics-affiliates" class="aslp-tab-content" style="display:none;">
            <h2><?php _e( 'Affiliate Analytics', 'as-laburda-pwa-app' ); ?></h2>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Affiliate Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Affiliate Code', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Total Clicks', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Total Signups', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Total Commissions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-affiliate-analytics-list">
                    <tr>
                        <td colspan="5"><?php _e( 'No affiliate analytics data found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
jQuery(document).ready(function($) {
    var analyticsAdmin = {
        viewsChartInstance: null,

        init: function() {
            this.bindTabEvents();
            this.loadAnalyticsData();
        },

        bindTabEvents: function() {
            $('.nav-tab-wrapper a').on('click', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.aslp-tab-content').hide();
                $('#analytics-' + tab).show();
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

        loadAnalyticsData: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_admin_get_analytics_data',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                analyticsAdmin.hideLoading();
                if (response.success) {
                    var analyticsData = response.data;

                    // Overview Tab
                    $('#overview-total-app-views').text(analyticsData.overview.total_app_views);
                    $('#overview-total-listing-views').text(analyticsData.overview.total_listing_views);
                    $('#overview-total-affiliate-clicks').text(analyticsData.overview.total_affiliate_clicks);
                    $('#overview-total-affiliate-signups').text(analyticsData.overview.total_affiliate_signups);

                    analyticsAdmin.renderViewsChart(analyticsData.overview.views_over_time);

                    // Apps Analytics Tab
                    var appAnalyticsList = $('#aslp-app-analytics-list');
                    appAnalyticsList.empty();
                    if (analyticsData.apps.length > 0) {
                        $.each(analyticsData.apps, function(index, app) {
                            var row = `
                                <tr>
                                    <td><strong>${app.app_name}</strong></td>
                                    <td>${app.total_views}</td>
                                    <td>${app.unique_views}</td>
                                    <td>${app.last_viewed || 'N/A'}</td>
                                </tr>
                            `;
                            appAnalyticsList.append(row);
                        });
                    } else {
                        appAnalyticsList.append('<tr><td colspan="4"><?php _e( 'No app analytics data found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                    }

                    // Listings Analytics Tab
                    var listingAnalyticsList = $('#aslp-listing-analytics-list');
                    listingAnalyticsList.empty();
                    if (analyticsData.listings.length > 0) {
                        $.each(analyticsData.listings, function(index, listing) {
                            var row = `
                                <tr>
                                    <td><strong>${listing.listing_name}</strong></td>
                                    <td>${listing.total_views}</td>
                                    <td>${listing.unique_views}</td>
                                    <td>${listing.last_viewed || 'N/A'}</td>
                                </tr>
                            `;
                            listingAnalyticsList.append(row);
                        });
                    } else {
                        listingAnalyticsList.append('<tr><td colspan="4"><?php _e( 'No listing analytics data found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                    }

                    // Affiliates Analytics Tab
                    var affiliateAnalyticsList = $('#aslp-affiliate-analytics-list');
                    affiliateAnalyticsList.empty();
                    if (analyticsData.affiliates.length > 0) {
                        $.each(analyticsData.affiliates, function(index, affiliate) {
                            var row = `
                                <tr>
                                    <td><strong>${affiliate.user_display_name || 'N/A'}</strong></td>
                                    <td>${affiliate.affiliate_code}</td>
                                    <td>${affiliate.total_clicks}</td>
                                    <td>${affiliate.total_signups}</td>
                                    <td>${parseFloat(affiliate.total_commissions).toFixed(2)} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></td>
                                </tr>
                            `;
                            affiliateAnalyticsList.append(row);
                        });
                    } else {
                        affiliateAnalyticsList.append('<tr><td colspan="5"><?php _e( 'No affiliate analytics data found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                    }

                } else {
                    analyticsAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                analyticsAdmin.hideLoading();
                analyticsAdmin.showMessage('<?php _e( 'Error loading analytics data.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        renderViewsChart: function(data) {
            if (this.viewsChartInstance) {
                this.viewsChartInstance.destroy();
            }

            var labels = data.map(item => item.date);
            var views = data.map(item => item.views);

            var ctx = document.getElementById('viewsChart').getContext('2d');
            this.viewsChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Views',
                        data: views,
                        backgroundColor: 'rgba(0, 115, 170, 0.2)', // WordPress primary blue with alpha
                        borderColor: 'rgba(0, 115, 170, 1)',
                        borderWidth: 1,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Views'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    }
                }
            });
        }
    };

    analyticsAdmin.init();
});
</script>
