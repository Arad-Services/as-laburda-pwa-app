<?php
/**
 * The admin App Templates page for the AS Laburda PWA App plugin.
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

    <p><?php _e( 'Manage predefined templates for your Progressive Web Apps. These templates can be used as starting points when creating new apps.', 'as-laburda-pwa-app' ); ?></p>

    <div id="aslp-app-templates-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <div class="aslp-template-list-section">
            <h2><?php _e( 'Available App Templates', 'as-laburda-pwa-app' ); ?></h2>
            <button id="aslp-add-new-template" class="button button-primary"><i class="fas fa-plus"></i> <?php _e( 'Add New Template', 'as-laburda-pwa-app' ); ?></button>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Template Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Active', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Created', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-template-list">
                    <tr>
                        <td colspan="5"><?php _e( 'No app templates found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="aslp-template-form-section" style="display: none;">
            <h2><?php _e( 'Template Details', 'as-laburda-pwa-app' ); ?></h2>
            <form id="aslp-template-form">
                <input type="hidden" id="aslp-template-id" name="template_id" value="">

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="template_name"><?php _e( 'Template Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="template_name" name="template_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="template_description"><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="template_description" name="description" rows="5" cols="50" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="template_config"><?php _e( 'Template Configuration (JSON)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <textarea id="template_config" name="template_config" rows="10" cols="70" class="large-text code" required></textarea>
                                <p class="description"><?php _e( 'Enter the JSON configuration for this app template. This should define default values for app properties (e.g., start_url, theme_color, default pages, etc.).', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="preview_image_url"><?php _e( 'Preview Image URL', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="text" id="preview_image_url" name="preview_image_url" class="regular-text aslp-media-upload-url" value="">
                                <button type="button" class="button aslp-media-upload-button"><?php _e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview">
                                    <img src="" id="preview_image_url_preview" style="max-width: 150px; height: auto; display: none;">
                                </div>
                                <p class="description"><?php _e( 'An image to preview what this template looks like.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Is Active', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <label for="is_active">
                                    <input type="checkbox" id="is_active" name="is_active" value="1">
                                    <?php _e( 'Enable this template for use', 'as-laburda-pwa-app' ); ?>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <button type="submit" id="aslp-save-template" class="button button-primary"><i class="fas fa-save"></i> <?php _e( 'Save Template', 'as-laburda-pwa-app' ); ?></button>
                    <button type="button" id="aslp-cancel-template-edit" class="button button-secondary"><?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var appTemplates = {
        init: function() {
            this.loadTemplates();
            this.bindEvents();
        },

        bindEvents: function() {
            $('#aslp-add-new-template').on('click', this.showAddTemplateForm.bind(this));
            $('#aslp-template-form').on('submit', this.saveTemplate.bind(this));
            $('#aslp-cancel-template-edit').on('click', this.cancelEdit.bind(this));
            $('#aslp-template-list').on('click', '.aslp-edit-template', this.editTemplate.bind(this));
            $('#aslp-template-list').on('click', '.aslp-delete-template', this.deleteTemplate.bind(this));

            // Media Uploader for preview image
            $('.aslp-media-upload-button').on('click', this.openMediaUploader.bind(this));
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

        loadTemplates: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_get_all_app_templates',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appTemplates.hideLoading();
                var templateList = $('#aslp-template-list');
                templateList.empty();

                if (response.success && response.data.templates.length > 0) {
                    $.each(response.data.templates, function(index, template) {
                        var status = template.is_active == 1 ? '<?php _e( 'Active', 'as-laburda-pwa-app' ); ?>' : '<?php _e( 'Inactive', 'as-laburda-pwa-app' ); ?>';
                        var row = `
                            <tr>
                                <td><strong>${template.template_name}</strong></td>
                                <td>${template.description}</td>
                                <td>${status}</td>
                                <td>${template.date_created}</td>
                                <td>
                                    <button class="button button-small aslp-edit-template" data-id="${template.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-template" data-id="${template.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        templateList.append(row);
                    });
                } else {
                    templateList.append('<tr><td colspan="5"><?php _e( 'No app templates found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                appTemplates.hideLoading();
                appTemplates.showMessage('<?php _e( 'Error loading app templates.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        showAddTemplateForm: function() {
            this.clearMessages();
            $('#aslp-template-form')[0].reset();
            $('#aslp-template-id').val('');
            $('#preview_image_url_preview').attr('src', '').hide(); // Clear preview image
            $('.aslp-template-list-section').hide();
            $('.aslp-template-form-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        editTemplate: function(e) {
            this.clearMessages();
            var template_id = $(e.target).data('id');
            this.showLoading();

            var data = {
                'action': 'aslp_get_all_app_templates', // Re-fetch all and filter, or add a specific get_app_template endpoint
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appTemplates.hideLoading();
                if (response.success && response.data.templates.length > 0) {
                    var template = response.data.templates.find(t => t.id == template_id);
                    if (template) {
                        $('#aslp-template-id').val(template.id);
                        $('#template_name').val(template.template_name);
                        $('#template_description').val(template.description);
                        $('#template_config').val(JSON.stringify(JSON.parse(template.template_data), null, 2)); // Pretty print JSON
                        $('#preview_image_url').val(template.preview_image_url);
                        $('#is_active').prop('checked', template.is_active == 1);

                        appTemplates.updateImagePreview($('#preview_image_url'), $('#preview_image_url_preview'));

                        $('.aslp-template-list-section').hide();
                        $('.aslp-template-form-section').show();
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    } else {
                        appTemplates.showMessage('<?php _e( 'App template not found.', 'as-laburda-pwa-app' ); ?>', 'error');
                    }
                } else {
                    appTemplates.showMessage('<?php _e( 'Error fetching app template details.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                appTemplates.hideLoading();
                appTemplates.showMessage('<?php _e( 'Error fetching app template details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        saveTemplate: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var template_id = $('#aslp-template-id').val();
            var template_config_raw = $('#template_config').val();
            var template_config_json;

            try {
                template_config_json = JSON.parse(template_config_raw);
            } catch (e) {
                this.hideLoading();
                this.showMessage('<?php _e( 'Invalid JSON in Template Configuration. Please correct it.', 'as-laburda-pwa-app' ); ?>', 'error');
                return;
            }

            var template_data_to_save = {
                template_name: $('#template_name').val(),
                description: $('#template_description').val(),
                template_config: template_config_json, // Send as parsed object
                preview_image_url: $('#preview_image_url').val(),
                is_active: $('#is_active').is(':checked') ? 1 : 0,
            };

            var data = {
                'action': 'aslp_add_update_app_template',
                'nonce': aslp_ajax_object.nonce,
                'template_id': template_id,
                'template_data': JSON.stringify(template_data_to_save) // Stringify for AJAX post
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appTemplates.hideLoading();
                if (response.success) {
                    appTemplates.showMessage(response.data.message, 'success');
                    appTemplates.loadTemplates();
                    appTemplates.cancelEdit();
                } else {
                    appTemplates.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                appTemplates.hideLoading();
                appTemplates.showMessage('<?php _e( 'Error saving app template.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deleteTemplate: function(e) {
            if (!confirm('<?php _e( 'Are you sure you want to delete this app template? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                return;
            }

            this.clearMessages();
            this.showLoading();
            var template_id = $(e.target).data('id');

            var data = {
                'action': 'aslp_delete_app_template',
                'nonce': aslp_ajax_object.nonce,
                'template_id': template_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appTemplates.hideLoading();
                if (response.success) {
                    appTemplates.showMessage(response.data.message, 'success');
                    appTemplates.loadTemplates();
                } else {
                    appTemplates.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                appTemplates.hideLoading();
                appTemplates.showMessage('<?php _e( 'Error deleting app template.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        cancelEdit: function() {
            this.clearMessages();
            $('.aslp-template-form-section').hide();
            $('.aslp-template-list-section').show();
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
        }
    };

    appTemplates.init();
});
</script>
