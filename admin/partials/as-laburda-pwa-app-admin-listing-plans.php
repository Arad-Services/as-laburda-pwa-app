<?php
/**
 * The admin Listing Plans page for the AS Laburda PWA App plugin.
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

    <p><?php _e( 'Manage the subscription plans for business listings. Define features, pricing, and duration for each plan.', 'as-laburda-pwa-app' ); ?></p>

    <div id="aslp-listing-plans-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <div class="aslp-plan-list-section">
            <h2><?php _e( 'Available Listing Plans', 'as-laburda-pwa-app' ); ?></h2>
            <button id="aslp-add-new-plan" class="button button-primary"><i class="fas fa-plus"></i> <?php _e( 'Add New Plan', 'as-laburda-pwa-app' ); ?></button>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Plan Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Price', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Duration (Days)', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Features', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Active', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Claim Plan', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Created', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-plan-list">
                    <tr>
                        <td colspan="8"><?php _e( 'No listing plans found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="aslp-plan-form-section" style="display: none;">
            <h2><?php _e( 'Plan Details', 'as-laburda-pwa-app' ); ?></h2>
            <form id="aslp-plan-form">
                <input type="hidden" id="aslp-plan-id" name="plan_id" value="">

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="plan_name"><?php _e( 'Plan Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="plan_name" name="plan_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="plan_description"><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="plan_description" name="description" rows="5" cols="50" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="plan_price"><?php _e( 'Price', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" step="0.01" min="0" id="plan_price" name="price" class="regular-text" value="0.00" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="plan_duration"><?php _e( 'Duration (Days)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" min="0" id="plan_duration" name="duration" class="regular-text" value="0">
                            <p class="description"><?php _e( 'Number of days the plan is active. Set to 0 for lifetime/unlimited.', 'as-laburda-pwa-app' ); ?></p></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Features', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <div id="aslp-plan-features-container">
                                    <!-- Features will be dynamically added here -->
                                    <label><input type="checkbox" class="plan-feature-checkbox" value="enable_products"> <?php _e( 'Enable Products', 'as-laburda-pwa-app' ); ?></label><br>
                                    <label><input type="checkbox" class="plan-feature-checkbox" value="enable_events"> <?php _e( 'Enable Events', 'as-laburda-pwa-app' ); ?></label><br>
                                    <label><input type="checkbox" class="plan-feature-checkbox" value="enable_notifications"> <?php _e( 'Enable Notifications', 'as-laburda-pwa-app' ); ?></label><br>
                                    <label><input type="checkbox" class="plan-feature-checkbox" value="enable_custom_fields"> <?php _e( 'Enable Custom Fields', 'as-laburda-pwa-app' ); ?></label><br>
                                    <label><input type="checkbox" class="plan-feature-checkbox" value="enable_seo_tools"> <?php _e( 'Enable SEO Tools', 'as-laburda-pwa-app' ); ?></label><br>
                                    <!-- Add more features as needed -->
                                </div>
                                <p class="description"><?php _e( 'Select the features included in this plan.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Is Active', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <label for="is_active">
                                    <input type="checkbox" id="is_active" name="is_active" value="1">
                                    <?php _e( 'Enable this plan for use', 'as-laburda-pwa-app' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Is Claim Plan', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <label for="is_claim_plan">
                                    <input type="checkbox" id="is_claim_plan" name="is_claim_plan" value="1">
                                    <?php _e( 'Mark as the free claim listing plan', 'as-laburda-pwa-app' ); ?>
                                </label>
                                <p class="description"><?php _e( 'Only one plan should be marked as the "Claim Plan". This plan is automatically assigned when a user claims a listing.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <button type="submit" id="aslp-save-plan" class="button button-primary"><i class="fas fa-save"></i> <?php _e( 'Save Plan', 'as-laburda-pwa-app' ); ?></button>
                    <button type="button" id="aslp-cancel-plan-edit" class="button button-secondary"><?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var listingPlans = {
        init: function() {
            this.loadPlans();
            this.bindEvents();
        },

        bindEvents: function() {
            $('#aslp-add-new-plan').on('click', this.showAddPlanForm.bind(this));
            $('#aslp-plan-form').on('submit', this.savePlan.bind(this));
            $('#aslp-cancel-plan-edit').on('click', this.cancelEdit.bind(this));
            $('#aslp-plan-list').on('click', '.aslp-edit-plan', this.editPlan.bind(this));
            $('#aslp-plan-list').on('click', '.aslp-delete-plan', this.deletePlan.bind(this));
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

        loadPlans: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_get_all_listing_plans',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                listingPlans.hideLoading();
                var planList = $('#aslp-plan-list');
                planList.empty();

                if (response.success && response.data.plans.length > 0) {
                    $.each(response.data.plans, function(index, plan) {
                        var isActive = plan.is_active == 1 ? '<?php _e( 'Yes', 'as-laburda-pwa-app' ); ?>' : '<?php _e( 'No', 'as-laburda-pwa-app' ); ?>';
                        var isClaimPlan = plan.is_claim_plan == 1 ? '<?php _e( 'Yes', 'as-laburda-pwa-app' ); ?>' : '<?php _e( 'No', 'as-laburda-pwa-app' ); ?>';
                        var features = JSON.parse(plan.features || '[]');
                        var featuresDisplay = features.length > 0 ? features.join(', ') : '<?php _e( 'None', 'as-laburda-pwa-app' ); ?>';

                        var row = `
                            <tr>
                                <td><strong>${plan.plan_name}</strong></td>
                                <td>${plan.price} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></td>
                                <td>${plan.duration == 0 ? '<?php _e( 'Lifetime', 'as-laburda-pwa-app' ); ?>' : plan.duration}</td>
                                <td>${featuresDisplay}</td>
                                <td>${isActive}</td>
                                <td>${isClaimPlan}</td>
                                <td>${plan.date_created}</td>
                                <td>
                                    <button class="button button-small aslp-edit-plan" data-id="${plan.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-plan" data-id="${plan.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        planList.append(row);
                    });
                } else {
                    planList.append('<tr><td colspan="8"><?php _e( 'No listing plans found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                listingPlans.hideLoading();
                listingPlans.showMessage('<?php _e( 'Error loading listing plans.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        showAddPlanForm: function() {
            this.clearMessages();
            $('#aslp-plan-form')[0].reset();
            $('#aslp-plan-id').val('');
            $('.plan-feature-checkbox').prop('checked', false); // Uncheck all features
            $('.aslp-plan-list-section').hide();
            $('.aslp-plan-form-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        editPlan: function(e) {
            this.clearMessages();
            var plan_id = $(e.target).data('id');
            this.showLoading();

            var data = {
                'action': 'aslp_get_all_listing_plans', // Re-fetch all and filter, or add a specific get_listing_plan endpoint
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                listingPlans.hideLoading();
                if (response.success && response.data.plans.length > 0) {
                    var plan = response.data.plans.find(p => p.id == plan_id);
                    if (plan) {
                        $('#aslp-plan-id').val(plan.id);
                        $('#plan_name').val(plan.plan_name);
                        $('#plan_description').val(plan.description);
                        $('#plan_price').val(plan.price);
                        $('#plan_duration').val(plan.duration);
                        $('#is_active').prop('checked', plan.is_active == 1);
                        $('#is_claim_plan').prop('checked', plan.is_claim_plan == 1);

                        // Set features checkboxes
                        $('.plan-feature-checkbox').prop('checked', false); // Uncheck all first
                        var features = JSON.parse(plan.features || '[]');
                        $.each(features, function(index, feature) {
                            $(`.plan-feature-checkbox[value="${feature}"]`).prop('checked', true);
                        });

                        $('.aslp-plan-list-section').hide();
                        $('.aslp-plan-form-section').show();
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    } else {
                        listingPlans.showMessage('<?php _e( 'Listing plan not found.', 'as-laburda-pwa-app' ); ?>', 'error');
                    }
                } else {
                    listingPlans.showMessage('<?php _e( 'Error fetching listing plan details.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                listingPlans.hideLoading();
                listingPlans.showMessage('<?php _e( 'Error fetching listing plan details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        savePlan: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var plan_id = $('#aslp-plan-id').val();
            var selectedFeatures = [];
            $('.plan-feature-checkbox:checked').each(function() {
                selectedFeatures.push($(this).val());
            });

            var plan_data_to_save = {
                plan_name: $('#plan_name').val(),
                description: $('#plan_description').val(),
                price: parseFloat($('#plan_price').val()),
                duration: parseInt($('#plan_duration').val()),
                features: selectedFeatures,
                is_active: $('#is_active').is(':checked') ? 1 : 0,
                is_claim_plan: $('#is_claim_plan').is(':checked') ? 1 : 0,
            };

            var data = {
                'action': 'aslp_add_update_listing_plan',
                'nonce': aslp_ajax_object.nonce,
                'plan_id': plan_id,
                'plan_data': JSON.stringify(plan_data_to_save)
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                listingPlans.hideLoading();
                if (response.success) {
                    listingPlans.showMessage(response.data.message, 'success');
                    listingPlans.loadPlans();
                    listingPlans.cancelEdit();
                } else {
                    listingPlans.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                listingPlans.hideLoading();
                listingPlans.showMessage('<?php _e( 'Error saving listing plan.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deletePlan: function(e) {
            if (!confirm('<?php _e( 'Are you sure you want to delete this listing plan? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                return;
            }

            this.clearMessages();
            this.showLoading();
            var plan_id = $(e.target).data('id');

            var data = {
                'action': 'aslp_delete_listing_plan',
                'nonce': aslp_ajax_object.nonce,
                'plan_id': plan_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                listingPlans.hideLoading();
                if (response.success) {
                    listingPlans.showMessage(response.data.message, 'success');
                    listingPlans.loadPlans();
                } else {
                    listingPlans.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                listingPlans.hideLoading();
                listingPlans.showMessage('<?php _e( 'Error deleting listing plan.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        cancelEdit: function() {
            this.clearMessages();
            $('.aslp-plan-form-section').hide();
            $('.aslp-plan-list-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        }
    };

    listingPlans.init();
});
</script>
