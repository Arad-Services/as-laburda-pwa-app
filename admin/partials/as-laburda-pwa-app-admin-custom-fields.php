<?php
/**
 * The admin Custom Fields page for the AS Laburda PWA App plugin.
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

    <p><?php _e( 'Create and manage custom fields that can be attached to various entities within your PWA apps, such as business listings, products, or events.', 'as-laburda-pwa-app' ); ?></p>

    <div id="aslp-custom-fields-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <div class="aslp-field-list-section">
            <h2><?php _e( 'Available Custom Fields', 'as-laburda-pwa-app' ); ?></h2>
            <button id="aslp-add-new-field" class="button button-primary"><i class="fas fa-plus"></i> <?php _e( 'Add New Field', 'as-laburda-pwa-app' ); ?></button>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Field Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Field Slug', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Field Type', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Applies To', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Required', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Active', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Created', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-field-list">
                    <tr>
                        <td colspan="8"><?php _e( 'No custom fields found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="aslp-field-form-section" style="display: none;">
            <h2><?php _e( 'Field Details', 'as-laburda-pwa-app' ); ?></h2>
            <form id="aslp-field-form">
                <input type="hidden" id="aslp-field-id" name="field_id" value="">

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="field_name"><?php _e( 'Field Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="field_name" name="field_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="field_slug"><?php _e( 'Field Slug', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="field_slug" name="field_slug" class="regular-text">
                            <p class="description"><?php _e( 'Unique identifier for the field (e.g., "my_custom_field"). Auto-generated if left empty.', 'as-laburda-pwa-app' ); ?></p></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="field_type"><?php _e( 'Field Type', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="field_type" name="field_type" required>
                                    <option value="text"><?php _e( 'Text', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="textarea"><?php _e( 'Textarea', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="number"><?php _e( 'Number', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="email"><?php _e( 'Email', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="url"><?php _e( 'URL', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="date"><?php _e( 'Date', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="select"><?php _e( 'Select (Dropdown)', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="checkbox"><?php _e( 'Checkbox', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="radio"><?php _e( 'Radio Buttons', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr id="field-options-row" style="display: none;">
                            <th scope="row"><label for="field_options"><?php _e( 'Field Options (Comma Separated)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="text" id="field_options" name="field_options" class="regular-text">
                                <p class="description"><?php _e( 'Enter options for Select, Checkbox, or Radio fields, separated by commas (e.g., Option 1, Option 2, Option 3).', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="applies_to"><?php _e( 'Applies To', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="applies_to" name="applies_to" required>
                                    <option value="listing"><?php _e( 'Business Listing', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="product"><?php _e( 'Product', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="event"><?php _e( 'Event', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="user"><?php _e( 'User Profile', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="app"><?php _e( 'PWA App', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Is Required', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <label for="is_required">
                                    <input type="checkbox" id="is_required" name="is_required" value="1">
                                    <?php _e( 'This field is required', 'as-laburda-pwa-app' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Is Active', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <label for="is_active">
                                    <input type="checkbox" id="is_active" name="is_active" value="1">
                                    <?php _e( 'Enable this custom field', 'as-laburda-pwa-app' ); ?>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <button type="submit" id="aslp-save-field" class="button button-primary"><i class="fas fa-save"></i> <?php _e( 'Save Field', 'as-laburda-pwa-app' ); ?></button>
                    <button type="button" id="aslp-cancel-field-edit" class="button button-secondary"><?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var customFields = {
        init: function() {
            this.loadFields();
            this.bindEvents();
            this.toggleFieldOptionsVisibility(); // Initial check on load
        },

        bindEvents: function() {
            $('#aslp-add-new-field').on('click', this.showAddFieldForm.bind(this));
            $('#aslp-field-form').on('submit', this.saveField.bind(this));
            $('#aslp-cancel-field-edit').on('click', this.cancelEdit.bind(this));
            $('#aslp-field-list').on('click', '.aslp-edit-field', this.editField.bind(this));
            $('#aslp-field-list').on('click', '.aslp-delete-field', this.deleteField.bind(this));
            $('#field_type').on('change', this.toggleFieldOptionsVisibility.bind(this));
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

        toggleFieldOptionsVisibility: function() {
            var fieldType = $('#field_type').val();
            if (fieldType === 'select' || fieldType === 'checkbox' || fieldType === 'radio') {
                $('#field-options-row').show();
            } else {
                $('#field-options-row').hide();
                $('#field_options').val(''); // Clear options if not applicable
            }
        },

        loadFields: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_get_all_custom_fields',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                customFields.hideLoading();
                var fieldList = $('#aslp-field-list');
                fieldList.empty();

                if (response.success && response.data.fields.length > 0) {
                    $.each(response.data.fields, function(index, field) {
                        var isActive = field.is_active == 1 ? '<?php _e( 'Yes', 'as-laburda-pwa-app' ); ?>' : '<?php _e( 'No', 'as-laburda-pwa-app' ); ?>';
                        var isRequired = field.is_required == 1 ? '<?php _e( 'Yes', 'as-laburda-pwa-app' ); ?>' : '<?php _e( 'No', 'as-laburda-pwa-app' ); ?>';
                        var fieldOptions = JSON.parse(field.field_options || '[]');
                        var optionsDisplay = fieldOptions.length > 0 ? fieldOptions.join(', ') : 'N/A';

                        var row = `
                            <tr>
                                <td><strong>${field.field_name}</strong></td>
                                <td>${field.field_slug}</td>
                                <td>${field.field_type}</td>
                                <td>${field.applies_to}</td>
                                <td>${isRequired}</td>
                                <td>${isActive}</td>
                                <td>${field.date_created}</td>
                                <td>
                                    <button class="button button-small aslp-edit-field" data-id="${field.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-field" data-id="${field.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        fieldList.append(row);
                    });
                } else {
                    fieldList.append('<tr><td colspan="8"><?php _e( 'No custom fields found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                customFields.hideLoading();
                customFields.showMessage('<?php _e( 'Error loading custom fields.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        showAddFieldForm: function() {
            this.clearMessages();
            $('#aslp-field-form')[0].reset();
            $('#aslp-field-id').val('');
            $('#field_slug').val(''); // Ensure slug is cleared
            $('#field_options').val(''); // Ensure options are cleared
            $('#field-options-row').hide(); // Hide options row by default
            $('.aslp-field-list-section').hide();
            $('.aslp-field-form-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        editField: function(e) {
            this.clearMessages();
            var field_id = $(e.target).data('id');
            this.showLoading();

            var data = {
                'action': 'aslp_get_all_custom_fields', // Re-fetch all and filter, or add a specific get_custom_field endpoint
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                customFields.hideLoading();
                if (response.success && response.data.fields.length > 0) {
                    var field = response.data.fields.find(f => f.id == field_id);
                    if (field) {
                        $('#aslp-field-id').val(field.id);
                        $('#field_name').val(field.field_name);
                        $('#field_slug').val(field.field_slug);
                        $('#field_type').val(field.field_type);
                        $('#applies_to').val(field.applies_to);
                        $('#is_required').prop('checked', field.is_required == 1);
                        $('#is_active').prop('checked', field.is_active == 1);

                        // Set field options
                        var fieldOptions = JSON.parse(field.field_options || '[]');
                        $('#field_options').val(fieldOptions.join(', '));
                        customFields.toggleFieldOptionsVisibility(); // Show/hide based on type

                        $('.aslp-field-list-section').hide();
                        $('.aslp-field-form-section').show();
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    } else {
                        customFields.showMessage('<?php _e( 'Custom field not found.', 'as-laburda-pwa-app' ); ?>', 'error');
                    }
                } else {
                    customFields.showMessage('<?php _e( 'Error fetching custom field details.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                customFields.hideLoading();
                customFields.showMessage('<?php _e( 'Error fetching custom field details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        saveField: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var field_id = $('#aslp-field-id').val();
            var field_name = $('#field_name').val();
            var field_slug = $('#field_slug').val();
            var field_type = $('#field_type').val();
            var applies_to = $('#applies_to').val();
            var is_required = $('#is_required').is(':checked') ? 1 : 0;
            var is_active = $('#is_active').is(':checked') ? 1 : 0;
            var field_options_raw = $('#field_options').val();
            var field_options = [];

            if (field_type === 'select' || field_type === 'checkbox' || field_type === 'radio') {
                field_options = field_options_raw.split(',').map(item => item.trim()).filter(item => item !== '');
                if (field_options.length === 0) {
                    this.hideLoading();
                    this.showMessage('<?php _e( 'For Select, Checkbox, or Radio fields, please provide at least one option.', 'as-laburda-pwa-app' ); ?>', 'error');
                    return;
                }
            }

            var field_data_to_save = {
                field_name: field_name,
                field_slug: field_slug || field_name.toLowerCase().replace(/[^a-z0-9_]/g, '_').replace(/_{2,}/g, '_').replace(/^_|_$/g, ''), // Basic slugify
                field_type: field_type,
                field_options: field_options,
                applies_to: applies_to,
                is_required: is_required,
                is_active: is_active,
            };

            var data = {
                'action': 'aslp_add_update_custom_field',
                'nonce': aslp_ajax_object.nonce,
                'field_id': field_id,
                'field_data': JSON.stringify(field_data_to_save)
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                customFields.hideLoading();
                if (response.success) {
                    customFields.showMessage(response.data.message, 'success');
                    customFields.loadFields();
                    customFields.cancelEdit();
                } else {
                    customFields.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                customFields.hideLoading();
                customFields.showMessage('<?php _e( 'Error saving custom field.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deleteField: function(e) {
            if (!confirm('<?php _e( 'Are you sure you want to delete this custom field? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                return;
            }

            this.clearMessages();
            this.showLoading();
            var field_id = $(e.target).data('id');

            var data = {
                'action': 'aslp_delete_custom_field',
                'nonce': aslp_ajax_object.nonce,
                'field_id': field_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                customFields.hideLoading();
                if (response.success) {
                    customFields.showMessage(response.data.message, 'success');
                    customFields.loadFields();
                } else {
                    customFields.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                customFields.hideLoading();
                customFields.showMessage('<?php _e( 'Error deleting custom field.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        cancelEdit: function() {
            this.clearMessages();
            $('.aslp-field-form-section').hide();
            $('.aslp-field-list-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        }
    };

    customFields.init();
});
</script>
