<?php
/**
 * The admin Global Settings page for the AS Laburda PWA App plugin.
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

    <p><?php _e( 'Configure global settings for the AS Laburda PWA App plugin. Enable or disable features and set up API keys.', 'as-laburda-pwa-app' ); ?></p>

    <div id="aslp-global-settings-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Saving settings...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <form id="aslp-global-settings-form">
            <h2><?php _e( 'Feature Management', 'as-laburda-pwa-app' ); ?></h2>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><?php _e( 'Enable App Builder', 'as-laburda-pwa-app' ); ?></th>
                        <td>
                            <label for="enable_app_builder">
                                <input type="checkbox" id="enable_app_builder" name="enable_app_builder" value="1">
                                <?php _e( 'Allow users to create and manage PWA applications.', 'as-laburda-pwa-app' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Enable Business Listings', 'as-laburda-pwa-app' ); ?></th>
                        <td>
                            <label for="enable_business_listings">
                                <input type="checkbox" id="enable_business_listings" name="enable_business_listings" value="1">
                                <?php _e( 'Allow users to submit and manage business listings.', 'as-laburda-pwa-app' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Enable Listing Plans', 'as-laburda-pwa-app' ); ?></th>
                        <td>
                            <label for="enable_listing_plans">
                                <input type="checkbox" id="enable_listing_plans" name="enable_listing_plans" value="1">
                                <?php _e( 'Enable subscription plans for business listings.', 'as-laburda-pwa-app' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Enable Products', 'as-laburda-pwa-app' ); ?></th>
                        <td>
                            <label for="enable_products">
                                <input type="checkbox" id="enable_products" name="enable_products" value="1">
                                <?php _e( 'Allow businesses to add products to their listings.', 'as-laburda-pwa-app' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Enable Events', 'as-laburda-pwa-app' ); ?></th>
                        <td>
                            <label for="enable_events">
                                <input type="checkbox" id="enable_events" name="enable_events" value="1">
                                <?php _e( 'Allow businesses to add events to their listings.', 'as-laburda-pwa-app' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Enable Notifications', 'as-laburda-pwa-app' ); ?></th>
                        <td>
                            <label for="enable_notifications">
                                <input type="checkbox" id="enable_notifications" name="enable_notifications" value="1">
                                <?php _e( 'Enable push notifications and in-app notifications.', 'as-laburda-pwa-app' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Enable Custom Fields', 'as-laburda-pwa-app' ); ?></th>
                        <td>
                            <label for="enable_custom_fields">
                                <input type="checkbox" id="enable_custom_fields" name="enable_custom_fields" value="1">
                                <?php _e( 'Allow creation of custom fields for various entities.', 'as-laburda-pwa-app' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Enable App Menus', 'as-laburda-pwa-app' ); ?></th>
                        <td>
                            <label for="enable_menus">
                                <input type="checkbox" id="enable_menus" name="enable_menus" value="1">
                                <?php _e( 'Allow creation of custom menus for PWA apps.', 'as-laburda-pwa-app' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Enable Affiliate Program', 'as-laburda-pwa-app' ); ?></th>
                        <td>
                            <label for="enable_affiliates">
                                <input type="checkbox" id="enable_affiliates" name="enable_affiliates" value="1">
                                <?php _e( 'Enable the affiliate marketing program.', 'as-laburda-pwa-app' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Enable Analytics', 'as-laburda-pwa-app' ); ?></th>
                        <td>
                            <label for="enable_analytics">
                                <input type="checkbox" id="enable_analytics" name="enable_analytics" value="1">
                                <?php _e( 'Enable tracking of app views, listing views, and affiliate clicks.', 'as-laburda-pwa-app' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Enable AI Assistant', 'as-laburda-pwa-app' ); ?></th>
                        <td>
                            <label for="enable_ai_agent">
                                <input type="checkbox" id="enable_ai_agent" name="enable_ai_agent" value="1">
                                <?php _e( 'Enable AI-powered features like chat, SEO, content creation, and debugging.', 'as-laburda-pwa-app' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Enable SEO Tools', 'as-laburda-pwa-app' ); ?></th>
                        <td>
                            <label for="enable_seo_tools">
                                <input type="checkbox" id="enable_seo_tools" name="enable_seo_tools" value="1">
                                <?php _e( 'Enable SEO optimization tools (can be AI-powered).', 'as-laburda-pwa-app' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Enable Admin Tools Menu', 'as-laburda-pwa-app' ); ?></th>
                        <td>
                            <label for="enable_tools_menu">
                                <input type="checkbox" id="enable_tools_menu" name="enable_tools_menu" value="1">
                                <?php _e( 'Enable the "Tools" submenu in the admin area for utilities like page management.', 'as-laburda-pwa-app' ); ?>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h2><?php _e( 'AI Settings', 'as-laburda-pwa-app' ); ?></h2>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="ai_api_key"><?php _e( 'AI API Key', 'as-laburda-pwa-app' ); ?></label></th>
                        <td>
                            <input type="text" id="ai_api_key" name="ai_api_key" class="regular-text" value="">
                            <p class="description"><?php _e( 'Enter your API key for the AI service (e.g., Google Gemini, OpenAI).', 'as-laburda-pwa-app' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ai_api_endpoint"><?php _e( 'AI API Endpoint', 'as-laburda-pwa-app' ); ?></label></th>
                        <td>
                            <input type="url" id="ai_api_endpoint" name="ai_api_endpoint" class="regular-text" value="">
                            <p class="description"><?php _e( 'The URL for the AI API endpoint (e.g., https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent).', 'as-laburda-pwa-app' ); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="submit">
                <button type="submit" id="aslp-save-global-settings" class="button button-primary"><i class="fas fa-save"></i> <?php _e( 'Save Changes', 'as-laburda-pwa-app' ); ?></button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var globalSettings = {
        init: function() {
            this.loadSettings();
            this.bindEvents();
        },

        bindEvents: function() {
            $('#aslp-global-settings-form').on('submit', this.saveSettings.bind(this));
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

        loadSettings: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_get_global_settings',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                globalSettings.hideLoading();
                if (response.success) {
                    var settings = response.data.settings;
                    // Set checkbox states
                    $('#enable_app_builder').prop('checked', settings.enable_app_builder);
                    $('#enable_business_listings').prop('checked', settings.enable_business_listings);
                    $('#enable_listing_plans').prop('checked', settings.enable_listing_plans);
                    $('#enable_products').prop('checked', settings.enable_products);
                    $('#enable_events').prop('checked', settings.enable_events);
                    $('#enable_notifications').prop('checked', settings.enable_notifications);
                    $('#enable_custom_fields').prop('checked', settings.enable_custom_fields);
                    $('#enable_menus').prop('checked', settings.enable_menus);
                    $('#enable_affiliates').prop('checked', settings.enable_affiliates);
                    $('#enable_analytics').prop('checked', settings.enable_analytics);
                    $('#enable_ai_agent').prop('checked', settings.enable_ai_agent);
                    $('#enable_seo_tools').prop('checked', settings.enable_seo_tools);
                    $('#enable_tools_menu').prop('checked', settings.enable_tools_menu);

                    // Set AI API fields
                    $('#ai_api_key').val(settings.ai_api_key || '');
                    $('#ai_api_endpoint').val(settings.ai_api_endpoint || '');

                } else {
                    globalSettings.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                globalSettings.hideLoading();
                globalSettings.showMessage('<?php _e( 'Error loading global settings.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        saveSettings: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var settings_data = {
                enable_app_builder: $('#enable_app_builder').is(':checked'),
                enable_business_listings: $('#enable_business_listings').is(':checked'),
                enable_listing_plans: $('#enable_listing_plans').is(':checked'),
                enable_products: $('#enable_products').is(':checked'),
                enable_events: $('#enable_events').is(':checked'),
                enable_notifications: $('#enable_notifications').is(':checked'),
                enable_custom_fields: $('#enable_custom_fields').is(':checked'),
                enable_menus: $('#enable_menus').is(':checked'),
                enable_affiliates: $('#enable_affiliates').is(':checked'),
                enable_analytics: $('#enable_analytics').is(':checked'),
                enable_ai_agent: $('#enable_ai_agent').is(':checked'),
                enable_seo_tools: $('#enable_seo_tools').is(':checked'),
                enable_tools_menu: $('#enable_tools_menu').is(':checked'),
                ai_api_key: $('#ai_api_key').val(),
                ai_api_endpoint: $('#ai_api_endpoint').val(),
            };

            var data = {
                'action': 'aslp_update_global_settings',
                'nonce': aslp_ajax_object.nonce,
                'settings_data': JSON.stringify(settings_data)
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                globalSettings.hideLoading();
                if (response.success) {
                    globalSettings.showMessage(response.data.message, 'success');
                } else {
                    globalSettings.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                globalSettings.hideLoading();
                globalSettings.showMessage('<?php _e( 'An AJAX error occurred while saving settings.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        }
    };

    globalSettings.init();
});
</script>
