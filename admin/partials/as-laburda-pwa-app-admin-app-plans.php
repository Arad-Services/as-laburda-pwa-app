<?php
/**
 * The admin App Plans page for the AS Laburda PWA App plugin.
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

    <p><?php _e( 'Manage the subscription plans for PWA app creation. Define features, pricing, and duration for each plan.', 'as-laburda-pwa-app' ); ?></p>

    <div id="aslp-app-plans-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <div class="aslp-plan-list-section">
            <h2><?php _e( 'Available App Plans', 'as-laburda-pwa-app' ); ?></h2>
            <button id="aslp-add-new-app-plan" class="button button-primary"><i class="fas fa-plus"></i> <?php _e( 'Add New App Plan', 'as-laburda-pwa-app' ); ?></button>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Plan Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Price', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Duration (Days)', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Features', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Active', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Created', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-app-plan-list">
                    <tr>
                        <td colspan="7"><?php _e( 'No app plans found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="aslp-plan-form-section" style="display: none;">
            <h2><?php _e( 'App Plan Details', 'as-laburda-pwa-app' ); ?></h2>
            <form id="aslp-app-plan-form">
                <input type="hidden" id="aslp-app-plan-id" name="plan_id" value="">

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="app_plan_name"><?php _e( 'Plan Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="app_plan_name" name="plan_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="app_plan_description"><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="app_plan_description" name="description" rows="5" cols="50" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="app_plan_price"><?php _e( 'Price', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" step="0.01" min="0" id="app_plan_price" name="price" class="regular-text" value="0.00" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="app_plan_duration"><?php _e( 'Duration (Days)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" min="0" id="app_plan_duration" name="duration" class="regular-text" value="0">
                            <p class="description"><?php _e( 'Number of days the plan is active. Set to 0 for lifetime/unlimited.', 'as-laburda-pwa-app' ); ?></p></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Features', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <div id="aslp-app-plan-features-container">
                                    <label><input type="checkbox" class="app-plan-feature-checkbox" value="max_apps"> <?php _e( 'Max Apps (Specify limit below)', 'as-laburda-pwa-app' ); ?></label><br>
                                    <input type="number" id="feature_max_apps_value" class="small-text" style="margin-left: 20px; width: 60px;" placeholder="Limit"><br>
                                    <label><input type="checkbox" class="app-plan-feature-checkbox" value="custom_domain"> <?php _e( 'Custom Domain Support', 'as-laburda-pwa-app' ); ?></label><br>
                                    <label><input type="checkbox" class="app-plan-feature-checkbox" value="push_notifications"> <?php _e( 'Push Notifications', 'as-laburda-pwa-app' ); ?></label><br>
                                    <label><input type="checkbox" class="app-plan-feature-checkbox" value="premium_templates"> <?php _e( 'Access Premium Templates', 'as-laburda-pwa-app' ); ?></label><br>
                                    <label><input type="checkbox" class="app-plan-feature-checkbox" value="ai_app_builder"> <?php _e( 'AI App Builder Assistance', 'as-laburda-pwa-app' ); ?></label><br>
                                    <label><input type="checkbox" class="app-plan-feature-checkbox" value="app_seo"> <?php _e( 'App SEO Tools', 'as-laburda-pwa-app' ); ?></label><br>
                                    <!-- Add more app-specific features as needed -->
                                </div>
                                <p class="description"><?php _e( 'Select the features included in this app plan.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Is Active', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <label for="app_plan_is_active">
                                    <input type="checkbox" id="app_plan_is_active" name="is_active" value="1">
                                    <?php _e( 'Enable this plan for use', 'as-laburda-pwa-app' ); ?>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <button type="submit" id="aslp-save-app-plan" class="button button-primary"><i class="fas fa-save"></i> <?php _e( 'Save App Plan', 'as-laburda-pwa-app' ); ?></button>
                    <button type="button" id="aslp-cancel-app-plan-edit" class="button button-secondary"><?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var appPlans = {
        init: function() {
            this.loadAppPlans();
            this.bindEvents();
        },

        bindEvents: function() {
            $('#aslp-add-new-app-plan').on('click', this.showAddAppPlanForm.bind(this));
            $('#aslp-app-plan-form').on('submit', this.saveAppPlan.bind(this));
            $('#aslp-cancel-app-plan-edit').on('click', this.cancelEdit.bind(this));
            $('#aslp-app-plan-list').on('click', '.aslp-edit-app-plan', this.editAppPlan.bind(this));
            $('#aslp-app-plan-list').on('click', '.aslp-delete-app-plan', this.deleteAppPlan.bind(this));

            // Feature checkbox specific handling
            $('#aslp-app-plan-features-container').on('change', '.app-plan-feature-checkbox[value="max_apps"]', this.toggleMaxAppsInput.bind(this));
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

        toggleMaxAppsInput: function() {
            if ($('.app-plan-feature-checkbox[value="max_apps"]').is(':checked')) {
                $('#feature_max_apps_value').show();
            } else {
                $('#feature_max_apps_value').hide().val(''); // Hide and clear value
            }
        },

        loadAppPlans: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_get_all_app_plans',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appPlans.hideLoading();
                var planList = $('#aslp-app-plan-list');
                planList.empty();

                if (response.success && response.data.plans.length > 0) {
                    $.each(response.data.plans, function(index, plan) {
                        var isActive = plan.is_active == 1 ? '<?php _e( 'Yes', 'as-laburda-pwa-app' ); ?>' : '<?php _e( 'No', 'as-laburda-pwa-app' ); ?>';
                        var features = JSON.parse(plan.features || '[]');
                        var featuresDisplay = [];
                        if (features.max_apps) {
                            featuresDisplay.push('Max Apps: ' + features.max_apps);
                        }
                        if (features.custom_domain) {
                            featuresDisplay.push('Custom Domain');
                        }
                        if (features.push_notifications) {
                            featuresDisplay.push('Push Notifications');
                        }
                        if (features.premium_templates) {
                            featuresDisplay.push('Premium Templates');
                        }
                        if (features.ai_app_builder) {
                            featuresDisplay.push('AI App Builder');
                        }
                        if (features.app_seo) {
                            featuresDisplay.push('App SEO');
                        }
                        
                        var displayFeatures = featuresDisplay.length > 0 ? featuresDisplay.join(', ') : '<?php _e( 'None', 'as-laburda-pwa-app' ); ?>';

                        var row = `
                            <tr>
                                <td><strong>${plan.plan_name}</strong></td>
                                <td>${parseFloat(plan.price).toFixed(2)} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></td>
                                <td>${plan.duration == 0 ? '<?php _e( 'Lifetime', 'as-laburda-pwa-app' ); ?>' : plan.duration + ' <?php _e( 'Days', 'as-laburda-pwa-app' ); ?>'}</td>
                                <td>${displayFeatures}</td>
                                <td>${isActive}</td>
                                <td>${plan.date_created}</td>
                                <td>
                                    <button class="button button-small aslp-edit-app-plan" data-id="${plan.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-app-plan" data-id="${plan.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        planList.append(row);
                    });
                } else {
                    planList.append('<tr><td colspan="7"><?php _e( 'No app plans found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                appPlans.hideLoading();
                appPlans.showMessage('<?php _e( 'Error loading app plans.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        showAddAppPlanForm: function() {
            this.clearMessages();
            $('#aslp-app-plan-form')[0].reset();
            $('#aslp-app-plan-id').val('');
            $('.app-plan-feature-checkbox').prop('checked', false); // Uncheck all features
            $('#feature_max_apps_value').hide().val(''); // Hide and clear max apps input
            $('.aslp-plan-list-section').hide();
            $('.aslp-plan-form-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        editAppPlan: function(e) {
            this.clearMessages();
            var plan_id = $(e.target).data('id');
            this.showLoading();

            var data = {
                'action': 'aslp_get_all_app_plans', // Re-fetch all and filter, or add a specific get_app_plan endpoint
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appPlans.hideLoading();
                if (response.success && response.data.plans.length > 0) {
                    var plan = response.data.plans.find(p => p.id == plan_id);
                    if (plan) {
                        $('#aslp-app-plan-id').val(plan.id);
                        $('#app_plan_name').val(plan.plan_name);
                        $('#app_plan_description').val(plan.description);
                        $('#app_plan_price').val(parseFloat(plan.price).toFixed(2));
                        $('#app_plan_duration').val(plan.duration);
                        $('#app_plan_is_active').prop('checked', plan.is_active == 1);

                        // Set features checkboxes
                        $('.app-plan-feature-checkbox').prop('checked', false); // Uncheck all first
                        var features = JSON.parse(plan.features || '{}'); // Parse as object
                        if (features.max_apps) {
                            $('.app-plan-feature-checkbox[value="max_apps"]').prop('checked', true);
                            $('#feature_max_apps_value').val(features.max_apps).show();
                        } else {
                            $('#feature_max_apps_value').hide().val('');
                        }
                        if (features.custom_domain) {
                            $('.app-plan-feature-checkbox[value="custom_domain"]').prop('checked', true);
                        }
                        if (features.push_notifications) {
                            $('.app-plan-feature-checkbox[value="push_notifications"]').prop('checked', true);
                        }
                        if (features.premium_templates) {
                            $('.app-plan-feature-checkbox[value="premium_templates"]').prop('checked', true);
                        }
                        if (features.ai_app_builder) {
                            $('.app-plan-feature-checkbox[value="ai_app_builder"]').prop('checked', true);
                        }
                        if (features.app_seo) {
                            $('.app-plan-feature-checkbox[value="app_seo"]').prop('checked', true);
                        }

                        $('.aslp-plan-list-section').hide();
                        $('.aslp-plan-form-section').show();
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    } else {
                        appPlans.showMessage('<?php _e( 'App plan not found.', 'as-laburda-pwa-app' ); ?>', 'error');
                    }
                } else {
                    appPlans.showMessage('<?php _e( 'Error fetching app plan details.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                appPlans.hideLoading();
                appPlans.showMessage('<?php _e( 'Error fetching app plan details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        saveAppPlan: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var plan_id = $('#aslp-app-plan-id').val();
            
            var features_data = {};
            if ($('.app-plan-feature-checkbox[value="max_apps"]').is(':checked')) {
                features_data.max_apps = parseInt($('#feature_max_apps_value').val());
            }
            if ($('.app-plan-feature-checkbox[value="custom_domain"]').is(':checked')) {
                features_data.custom_domain = true;
            }
            if ($('.app-plan-feature-checkbox[value="push_notifications"]').is(':checked')) {
                features_data.push_notifications = true;
            }
            if ($('.app-plan-feature-checkbox[value="premium_templates"]').is(':checked')) {
                features_data.premium_templates = true;
            }
            if ($('.app-plan-feature-checkbox[value="ai_app_builder"]').is(':checked')) {
                features_data.ai_app_builder = true;
            }
            if ($('.app-plan-feature-checkbox[value="app_seo"]').is(':checked')) {
                features_data.app_seo = true;
            }

            var plan_data_to_save = {
                plan_name: $('#app_plan_name').val(),
                description: $('#app_plan_description').val(),
                price: parseFloat($('#app_plan_price').val()),
                duration: parseInt($('#app_plan_duration').val()),
                features: features_data, // Send as object
                is_active: $('#app_plan_is_active').is(':checked') ? 1 : 0,
            };

            var data = {
                'action': 'aslp_add_update_app_plan',
                'nonce': aslp_ajax_object.nonce,
                'plan_id': plan_id,
                'plan_data': JSON.stringify(plan_data_to_save) // Stringify for AJAX post
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appPlans.hideLoading();
                if (response.success) {
                    appPlans.showMessage(response.data.message, 'success');
                    appPlans.loadAppPlans();
                    appPlans.cancelEdit();
                } else {
                    appPlans.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                appPlans.hideLoading();
                appPlans.showMessage('<?php _e( 'Error saving app plan.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deleteAppPlan: function(e) {
            if (!confirm('<?php _e( 'Are you sure you want to delete this app plan? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                return;
            }

            this.clearMessages();
            this.showLoading();
            var plan_id = $(e.target).data('id');

            var data = {
                'action': 'aslp_delete_app_plan',
                'nonce': aslp_ajax_object.nonce,
                'plan_id': plan_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appPlans.hideLoading();
                if (response.success) {
                    appPlans.showMessage(response.data.message, 'success');
                    appPlans.loadAppPlans();
                } else {
                    appPlans.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                appPlans.hideLoading();
                appPlans.showMessage('<?php _e( 'Error deleting app plan.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        cancelEdit: function() {
            this.clearMessages();
            $('.aslp-plan-form-section').hide();
            $('.aslp-plan-list-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        }
    };

    appPlans.init();
});
</script>
