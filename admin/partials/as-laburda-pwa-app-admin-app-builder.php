<?php
/**
 * The admin App Builder page for the AS Laburda PWA App plugin.
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

    <p><?php _e( 'Manage your Progressive Web Applications. Create new apps, configure their settings, and publish them.', 'as-laburda-pwa-app' ); ?></p>

    <div id="aslp-app-builder-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <div class="aslp-app-list-section">
            <h2><?php _e( 'Your Apps', 'as-laburda-pwa-app' ); ?></h2>
            <button id="aslp-add-new-app" class="button button-primary"><i class="fas fa-plus"></i> <?php _e( 'Add New App', 'as-laburda-pwa-app' ); ?></button>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'App Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Short Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Status', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Created', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-app-list">
                    <tr>
                        <td colspan="5"><?php _e( 'No apps found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="aslp-app-form-section" style="display: none;">
            <h2><?php _e( 'App Details', 'as-laburda-pwa-app' ); ?></h2>
            <form id="aslp-app-form">
                <input type="hidden" id="aslp-app-uuid" name="app_uuid" value="">

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="app_name"><?php _e( 'App Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="app_name" name="app_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="short_name"><?php _e( 'Short Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="short_name" name="short_name" class="regular-text" maxlength="12">
                            <p class="description"><?php _e( 'A short version of the app\'s name, used where space is limited (e.g., home screen). Max 12 characters.', 'as-laburda-pwa-app' ); ?></p></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="description"><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="description" name="description" rows="5" cols="50" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="start_url"><?php _e( 'Start URL', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="url" id="start_url" name="start_url" class="regular-text" value="/" required>
                            <p class="description"><?php _e( 'The URL that loads when the app is launched. Relative to the site root (e.g., / or /my-app/).', 'as-laburda-pwa-app' ); ?></p></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="theme_color"><?php _e( 'Theme Color', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="color" id="theme_color" name="theme_color" value="#2196f3">
                            <p class="description"><?php _e( 'The default theme color for the application. Affects browser UI.', 'as-laburda-pwa-app' ); ?></p></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="background_color"><?php _e( 'Background Color', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="color" id="background_color" name="background_color" value="#ffffff">
                            <p class="description"><?php _e( 'The background color of the splash screen when the app is launched.', 'as-laburda-pwa-app' ); ?></p></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="display_mode"><?php _e( 'Display Mode', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="display_mode" name="display_mode">
                                    <option value="standalone"><?php _e( 'Standalone', 'as-laburda-pwa-app' ); ?> (<?php _e( 'Looks like a native app', 'as-laburda-pwa-app' ); ?>)</option>
                                    <option value="fullscreen"><?php _e( 'Fullscreen', 'as-laburda-pwa-app' ); ?> (<?php _e( 'Takes up the entire screen', 'as-laburda-pwa-app' ); ?>)</option>
                                    <option value="minimal-ui"><?php _e( 'Minimal UI', 'as-laburda-pwa-app' ); ?> (<?php _e( 'Minimal browser UI', 'as-laburda-pwa-app' ); ?>)</option>
                                    <option value="browser"><?php _e( 'Browser', 'as-laburda-pwa-app' ); ?> (<?php _e( 'Regular browser tab', 'as-laburda-pwa-app' ); ?>)</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="orientation"><?php _e( 'Orientation', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="orientation" name="orientation">
                                    <option value="any"><?php _e( 'Any', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="portrait"><?php _e( 'Portrait', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="landscape"><?php _e( 'Landscape', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="icon_192"><?php _e( 'App Icon (192x192)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="text" id="icon_192" name="icon_192" class="regular-text aslp-media-upload-url" value="">
                                <button type="button" class="button aslp-media-upload-button"><?php _e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview">
                                    <img src="" id="icon_192_preview" style="max-width: 100px; height: auto; display: none;">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="icon_512"><?php _e( 'App Icon (512x512)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="text" id="icon_512" name="icon_512" class="regular-text aslp-media-upload-url" value="">
                                <button type="button" class="button aslp-media-upload-button"><?php _e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview">
                                    <img src="" id="icon_512_preview" style="max-width: 100px; height: auto; display: none;">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="splash_screen"><?php _e( 'Splash Screen Image', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="text" id="splash_screen" name="splash_screen" class="regular-text aslp-media-upload-url" value="">
                                <button type="button" class="button aslp-media-upload-button"><?php _e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview">
                                    <img src="" id="splash_screen_preview" style="max-width: 100px; height: auto; display: none;">
                                </div>
                                <p class="description"><?php _e( 'Recommended size: 1280x768px or similar aspect ratio.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="offline_page_id"><?php _e( 'Offline Page', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <?php
                                wp_dropdown_pages( array(
                                    'name'              => 'offline_page_id',
                                    'id'                => 'offline_page_id',
                                    'selected'          => get_option( 'aslp_offline_page_id' ), // Default to the tool-created page
                                    'show_option_none'  => __( '&mdash; Select &mdash;', 'as-laburda-pwa-app' ),
                                    'option_none_value' => 0,
                                ) );
                                ?>
                                <p class="description"><?php _e( 'Page to display when the app is offline.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="dashboard_page_id"><?php _e( 'Dashboard Page', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <?php
                                wp_dropdown_pages( array(
                                    'name'              => 'dashboard_page_id',
                                    'id'                => 'dashboard_page_id',
                                    'selected'          => get_option( 'aslp_app_dashboard_page_id' ), // Default to the tool-created page
                                    'show_option_none'  => __( '&mdash; Select &mdash;', 'as-laburda-pwa-app' ),
                                    'option_none_value' => 0,
                                ) );
                                ?>
                                <p class="description"><?php _e( 'The main dashboard page for app users.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="login_page_id"><?php _e( 'Login Page', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <?php
                                wp_dropdown_pages( array(
                                    'name'              => 'login_page_id',
                                    'id'                => 'login_page_id',
                                    'selected'          => get_option( 'aslp_login_page_id' ), // Default to the tool-created page
                                    'show_option_none'  => __( '&mdash; Select &mdash;', 'as-laburda-pwa-app' ),
                                    'option_none_value' => 0,
                                ) );
                                ?>
                                <p class="description"><?php _e( 'The login/registration page for the app.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Push Notifications', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <label for="enable_push_notifications">
                                    <input type="checkbox" id="enable_push_notifications" name="enable_push_notifications" value="1">
                                    <?php _e( 'Enable Push Notifications for this app', 'as-laburda-pwa-app' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Persistent Storage', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <label for="enable_persistent_storage">
                                    <input type="checkbox" id="enable_persistent_storage" name="enable_persistent_storage" value="1">
                                    <?php _e( 'Enable Persistent Storage (e.g., for offline data)', 'as-laburda-pwa-app' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="desktop_template_option"><?php _e( 'Desktop Template', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="desktop_template_option" name="desktop_template_option">
                                    <option value="default"><?php _e( 'Default', 'as-laburda-pwa-app' ); ?></option>
                                    <!-- Templates will be loaded dynamically via JS -->
                                </select>
                                <p class="description"><?php _e( 'Choose a template for the desktop version of the PWA.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mobile_template_option"><?php _e( 'Mobile Template', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="mobile_template_option" name="mobile_template_option">
                                    <option value="default"><?php _e( 'Default', 'as-laburda-pwa-app' ); ?></option>
                                    <!-- Templates will be loaded dynamically via JS -->
                                </select>
                                <p class="description"><?php _e( 'Choose a template for the mobile version of the PWA.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="app_status"><?php _e( 'App Status', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="app_status" name="app_status">
                                    <option value="draft"><?php _e( 'Draft', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="published"><?php _e( 'Published', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="suspended"><?php _e( 'Suspended', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="current_app_plan_id"><?php _e( 'App Plan', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="current_app_plan_id" name="current_app_plan_id">
                                    <option value="0"><?php _e( 'No Plan', 'as-laburda-pwa-app' ); ?></option>
                                    <!-- Plans will be loaded dynamically via JS -->
                                </select>
                                <p class="description"><?php _e( 'Assign a plan to this app. Features may vary based on the selected plan.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h3><?php _e( 'SEO Settings', 'as-laburda-pwa-app' ); ?></h3>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="seo_title"><?php _e( 'SEO Title', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="text" id="seo_title" name="seo_title" class="regular-text" maxlength="60">
                                <p class="description"><?php _e( 'Max 60 characters. Appears in browser tab and search results.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="seo_description"><?php _e( 'SEO Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <textarea id="seo_description" name="seo_description" rows="3" cols="50" class="large-text" maxlength="160"></textarea>
                                <p class="description"><?php _e( 'Max 160 characters. Appears in search results snippet.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="seo_keywords"><?php _e( 'SEO Keywords', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="text" id="seo_keywords" name="seo_keywords" class="regular-text" maxlength="200">
                                <p class="description"><?php _e( 'Comma-separated keywords. Max 200 characters.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <?php if ( $this->main_plugin->get_global_feature_settings()['enable_ai_agent'] ?? false ) : ?>
                        <tr>
                            <th scope="row"><?php _e( 'AI SEO Generation', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <button type="button" id="aslp-generate-seo-ai" class="button button-secondary"><i class="fas fa-robot"></i> <?php _e( 'Generate SEO with AI', 'as-laburda-pwa-app' ); ?></button>
                                <p class="description"><?php _e( 'Use AI to generate SEO title, description, and keywords based on the app name and description.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <?php endif; ?>
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
    var appBuilder = {
        init: function() {
            this.loadApps();
            this.bindEvents();
            this.loadTemplatesAndPlans();
        },

        bindEvents: function() {
            $('#aslp-add-new-app').on('click', this.showAddAppForm.bind(this));
            $('#aslp-app-form').on('submit', this.saveApp.bind(this));
            $('#aslp-cancel-app-edit').on('click', this.cancelEdit.bind(this));
            $('#aslp-app-list').on('click', '.aslp-edit-app', this.editApp.bind(this));
            $('#aslp-app-list').on('click', '.aslp-delete-app', this.deleteApp.bind(this));
            $('#aslp-app-list').on('click', '.aslp-preview-app', this.previewApp.bind(this));
            $('#aslp-app-list').on('click', '.aslp-apply-template', this.applyTemplate.bind(this));

            // Media Uploader for icons and splash screen
            $('.aslp-media-upload-button').on('click', this.openMediaUploader.bind(this));

            // AI SEO Generation
            $('#aslp-generate-seo-ai').on('click', this.generateSeoWithAI.bind(this));
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

        loadApps: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_get_all_apps',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appBuilder.hideLoading();
                var appList = $('#aslp-app-list');
                appList.empty();

                if (response.success && response.data.apps.length > 0) {
                    $.each(response.data.apps, function(index, app) {
                        var row = `
                            <tr>
                                <td><strong>${app.app_name}</strong></td>
                                <td>${app.short_name}</td>
                                <td>${app.app_status}</td>
                                <td>${app.date_created}</td>
                                <td>
                                    <button class="button button-small aslp-edit-app" data-uuid="${app.app_uuid}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-app" data-uuid="${app.app_uuid}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-preview-app" data-uuid="${app.app_uuid}"><i class="fas fa-eye"></i> <?php _e( 'Preview', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-apply-template" data-uuid="${app.app_uuid}"><i class="fas fa-file-import"></i> <?php _e( 'Apply Template', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        appList.append(row);
                    });
                } else {
                    appList.append('<tr><td colspan="5"><?php _e( 'No apps found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                appBuilder.hideLoading();
                appBuilder.showMessage('<?php _e( 'Error loading apps.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        loadTemplatesAndPlans: function() {
            // Load App Templates
            var dataTemplates = {
                'action': 'aslp_get_all_app_templates',
                'nonce': aslp_ajax_object.nonce
            };
            $.post(aslp_ajax_object.ajax_url, dataTemplates, function(response) {
                if (response.success && response.data.templates.length > 0) {
                    var desktopTemplateSelect = $('#desktop_template_option');
                    var mobileTemplateSelect = $('#mobile_template_option');
                    $.each(response.data.templates, function(index, template) {
                        desktopTemplateSelect.append(`<option value="${template.id}">${template.template_name}</option>`);
                        mobileTemplateSelect.append(`<option value="${template.id}">${template.template_name}</option>`);
                    });
                }
            });

            // Load Listing Plans (for app plans)
            var dataPlans = {
                'action': 'aslp_get_all_listing_plans', // Reusing listing plans for app plans
                'nonce': aslp_ajax_object.nonce
            };
            $.post(aslp_ajax_object.ajax_url, dataPlans, function(response) {
                if (response.success && response.data.plans.length > 0) {
                    var appPlanSelect = $('#current_app_plan_id');
                    $.each(response.data.plans, function(index, plan) {
                        appPlanSelect.append(`<option value="${plan.id}">${plan.plan_name} (${plan.price} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?>)</option>`);
                    });
                }
            });
        },

        showAddAppForm: function() {
            this.clearMessages();
            $('#aslp-app-form')[0].reset();
            $('#aslp-app-uuid').val('');
            // Reset image previews
            $('#icon_192_preview').attr('src', '').hide();
            $('#icon_512_preview').attr('src', '').hide();
            $('#splash_screen_preview').attr('src', '').hide();

            $('.aslp-app-list-section').hide();
            $('.aslp-app-form-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        editApp: function(e) {
            this.clearMessages();
            var app_uuid = $(e.target).data('uuid');
            this.showLoading();

            var data = {
                'action': 'aslp_get_all_apps', // Re-fetch all and filter, or add a specific get_app_by_uuid endpoint
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appBuilder.hideLoading();
                if (response.success && response.data.apps.length > 0) {
                    var app = response.data.apps.find(a => a.app_uuid === app_uuid);
                    if (app) {
                        $('#aslp-app-uuid').val(app.app_uuid);
                        $('#app_name').val(app.app_name);
                        $('#short_name').val(app.short_name);
                        $('#description').val(app.description);
                        $('#start_url').val(app.start_url);
                        $('#theme_color').val(app.theme_color);
                        $('#background_color').val(app.background_color);
                        $('#display_mode').val(app.display_mode);
                        $('#orientation').val(app.orientation);
                        $('#icon_192').val(app.icon_192);
                        $('#icon_512').val(app.icon_512);
                        $('#splash_screen').val(app.splash_screen);
                        $('#offline_page_id').val(app.offline_page_id);
                        $('#dashboard_page_id').val(app.dashboard_page_id);
                        $('#login_page_id').val(app.login_page_id);
                        $('#enable_push_notifications').prop('checked', app.enable_push_notifications == 1);
                        $('#enable_persistent_storage').prop('checked', app.enable_persistent_storage == 1);
                        $('#desktop_template_option').val(app.desktop_template_option);
                        $('#mobile_template_option').val(app.mobile_template_option);
                        $('#app_status').val(app.app_status);
                        $('#current_app_plan_id').val(app.current_app_plan_id);
                        $('#seo_title').val(app.seo_title);
                        $('#seo_description').val(app.seo_description);
                        $('#seo_keywords').val(app.seo_keywords);

                        // Update image previews
                        appBuilder.updateImagePreview($('#icon_192'), $('#icon_192_preview'));
                        appBuilder.updateImagePreview($('#icon_512'), $('#icon_512_preview'));
                        appBuilder.updateImagePreview($('#splash_screen'), $('#splash_screen_preview'));

                        $('.aslp-app-list-section').hide();
                        $('.aslp-app-form-section').show();
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    } else {
                        appBuilder.showMessage('<?php _e( 'App not found.', 'as-laburda-pwa-app' ); ?>', 'error');
                    }
                } else {
                    appBuilder.showMessage('<?php _e( 'Error fetching app details.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                appBuilder.hideLoading();
                appBuilder.showMessage('<?php _e( 'Error fetching app details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        saveApp: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var app_uuid = $('#aslp-app-uuid').val();
            var app_data = {
                app_name: $('#app_name').val(),
                short_name: $('#short_name').val(),
                description: $('#description').val(),
                start_url: $('#start_url').val(),
                theme_color: $('#theme_color').val(),
                background_color: $('#background_color').val(),
                display_mode: $('#display_mode').val(),
                orientation: $('#orientation').val(),
                icon_192: $('#icon_192').val(),
                icon_512: $('#icon_512').val(),
                splash_screen: $('#splash_screen').val(),
                offline_page_id: $('#offline_page_id').val(),
                dashboard_page_id: $('#dashboard_page_id').val(),
                login_page_id: $('#login_page_id').val(),
                enable_push_notifications: $('#enable_push_notifications').is(':checked') ? 1 : 0,
                enable_persistent_storage: $('#enable_persistent_storage').is(':checked') ? 1 : 0,
                desktop_template_option: $('#desktop_template_option').val(),
                mobile_template_option: $('#mobile_template_option').val(),
                app_status: $('#app_status').val(),
                current_app_plan_id: $('#current_app_plan_id').val(),
                seo_title: $('#seo_title').val(),
                seo_description: $('#seo_description').val(),
                seo_keywords: $('#seo_keywords').val(),
            };

            var data = {
                'action': 'aslp_update_app_settings', // Using update for both add/edit on admin side
                'nonce': aslp_ajax_object.nonce,
                'app_uuid': app_uuid,
                'app_data': JSON.stringify(app_data)
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appBuilder.hideLoading();
                if (response.success) {
                    appBuilder.showMessage(response.data.message, 'success');
                    appBuilder.loadApps();
                    appBuilder.cancelEdit();
                } else {
                    appBuilder.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                appBuilder.hideLoading();
                appBuilder.showMessage('<?php _e( 'Error saving app.', 'as-laburda-pwa-app' ); ?>', 'error');
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
                'action': 'aslp_delete_app',
                'nonce': aslp_ajax_object.nonce,
                'app_uuid': app_uuid
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appBuilder.hideLoading();
                if (response.success) {
                    appBuilder.showMessage(response.data.message, 'success');
                    appBuilder.loadApps();
                } else {
                    appBuilder.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                appBuilder.hideLoading();
                appBuilder.showMessage('<?php _e( 'Error deleting app.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        previewApp: function(e) {
            var app_uuid = $(e.target).data('uuid');
            // This would typically open a new tab/window with the PWA preview URL
            // For now, we can log it or show a placeholder.
            // In a real scenario, you'd have a public-facing endpoint for PWA preview.
            alert('<?php _e( 'Previewing app with UUID:', 'as-laburda-pwa-app' ); ?> ' + app_uuid + '\n<?php _e( ' (Implementation for live preview URL is needed)', 'as-laburda-pwa-app' ); ?>');
            console.log('Preview App UUID:', app_uuid);
        },

        applyTemplate: function(e) {
            var app_uuid = $(e.target).data('uuid');
            // This would open a modal or redirect to a page to select a template
            alert('<?php _e( 'Applying template to app with UUID:', 'as-laburda-pwa-app' ); ?> ' + app_uuid + '\n<?php _e( ' (Template selection UI and logic are needed)', 'as-laburda-pwa-app' ); ?>');
            console.log('Apply Template to App UUID:', app_uuid);
        },

        cancelEdit: function() {
            this.clearMessages();
            $('.aslp-app-form-section').hide();
            $('.aslp-app-list-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        openMediaUploader: function(e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            var input = button.prev('.aslp-media-upload-url');
            var preview = button.next('.aslp-image-preview').find('img');

            var custom_uploader = wp.media({
                title: '<?php _e( 'Select Image', 'as-laburda-pwa-app' ); ?>',
                library: {
                    type: 'image'
                },
                button: {
                    text: '<?php _e( 'Select Image', 'as-laburda-pwa-app' ); ?>'
                },
                multiple: false
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                input.val(attachment.url);
                preview.attr('src', attachment.url).show();
            }).open();
        },

        updateImagePreview: function(inputElement, previewElement) {
            var imageUrl = inputElement.val();
            if (imageUrl) {
                previewElement.attr('src', imageUrl).show();
            } else {
                previewElement.attr('src', '').hide();
            }
        },

        generateSeoWithAI: function() {
            this.clearMessages();
            this.showLoading();

            var app_uuid = $('#aslp-app-uuid').val();
            var app_name = $('#app_name').val();
            var description = $('#description').val();

            if (!app_name || !description) {
                this.hideLoading();
                this.showMessage('<?php _e( 'Please provide an App Name and Description before generating SEO.', 'as-laburda-pwa-app' ); ?>', 'warning');
                return;
            }

            var content_to_analyze = `App Name: ${app_name}\nDescription: ${description}`;

            var data = {
                'action': 'aslp_admin_ai_generate_seo',
                'nonce': aslp_ajax_object.nonce,
                'item_id': app_uuid, // Pass app_uuid for saving
                'item_type': 'app',
                'content_to_analyze': content_to_analyze
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appBuilder.hideLoading();
                if (response.success) {
                    appBuilder.showMessage(response.data.message, 'success');
                    // Populate the SEO fields with AI generated data
                    $('#seo_title').val(response.data.seo_data.seo_title);
                    $('#seo_description').val(response.data.seo_data.seo_description);
                    $('#seo_keywords').val(response.data.seo_data.seo_keywords);
                } else {
                    appBuilder.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                appBuilder.hideLoading();
                appBuilder.showMessage('<?php _e( 'Error generating SEO with AI.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        }
    };

    appBuilder.init();
});
</script>
