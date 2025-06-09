<?php
/**
 * The admin Events page for the AS Laburda PWA App plugin.
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

    <p><?php _e( 'Manage all events associated with business listings on your platform. You can add, edit, or delete events.', 'as-laburda-pwa-app' ); ?></p>

    <div id="aslp-events-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <div class="aslp-event-list-section">
            <h2><?php _e( 'All Events', 'as-laburda-pwa-app' ); ?></h2>
            <button id="aslp-add-new-event" class="button button-primary"><i class="fas fa-plus"></i> <?php _e( 'Add New Event', 'as-laburda-pwa-app' ); ?></button>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Event Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Listing Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Time', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Status', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-event-list">
                    <tr>
                        <td colspan="6"><?php _e( 'No events found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="aslp-event-form-section" style="display: none;">
            <h2><?php _e( 'Event Details', 'as-laburda-pwa-app' ); ?></h2>
            <form id="aslp-event-form">
                <input type="hidden" id="aslp-event-id" name="event_id" value="">

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="event_name"><?php _e( 'Event Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="event_name" name="event_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="description"><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="description" name="description" rows="5" cols="50" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="event_date"><?php _e( 'Event Date', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="date" id="event_date" name="event_date" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="event_time"><?php _e( 'Event Time', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="time" id="event_time" name="event_time" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="location"><?php _e( 'Location', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="location" name="location" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="image_url"><?php _e( 'Image URL', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="text" id="image_url" name="image_url" class="regular-text aslp-media-upload-url" value="">
                                <button type="button" class="button aslp-media-upload-button"><?php _e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview">
                                    <img src="" id="image_url_preview" style="max-width: 150px; height: auto; display: none;">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="listing_id"><?php _e( 'Associated Listing', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="listing_id" name="listing_id" required>
                                    <option value="0"><?php _e( 'Select a listing', 'as-laburda-pwa-app' ); ?></option>
                                    <!-- Business listings will be dynamically loaded here -->
                                </select>
                                <p class="description"><?php _e( 'Select the business listing this event belongs to.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="status"><?php _e( 'Status', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="status" name="status">
                                    <option value="active"><?php _e( 'Active', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="cancelled"><?php _e( 'Cancelled', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="completed"><?php _e( 'Completed', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="draft"><?php _e( 'Draft', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <button type="submit" id="aslp-save-event" class="button button-primary"><i class="fas fa-save"></i> <?php _e( 'Save Event', 'as-laburda-pwa-app' ); ?></button>
                    <button type="button" id="aslp-cancel-event-edit" class="button button-secondary"><?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var eventsAdmin = {
        init: function() {
            this.loadEvents();
            this.loadBusinessListings(); // Load listings for dropdown
            this.bindEvents();
        },

        bindEvents: function() {
            $('#aslp-add-new-event').on('click', this.showAddEventForm.bind(this));
            $('#aslp-event-form').on('submit', this.saveEvent.bind(this));
            $('#aslp-cancel-event-edit').on('click', this.cancelEdit.bind(this));
            $('#aslp-event-list').on('click', '.aslp-edit-event', this.editEvent.bind(this));
            $('#aslp-event-list').on('click', '.aslp-delete-event', this.deleteEvent.bind(this));

            // Media Uploader for image
            $(document).on('click', '.aslp-media-upload-button', this.openMediaUploader.bind(this));
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

        updateImagePreview: function(inputElement, previewElement) {
            var imageUrl = inputElement.val();
            if (imageUrl) {
                previewElement.attr('src', imageUrl).show();
            } else {
                previewElement.attr('src', '').hide();
            }
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

        loadEvents: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_get_events_for_admin', // Assuming a new AJAX action for admin to get all events
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                eventsAdmin.hideLoading();
                var eventList = $('#aslp-event-list');
                eventList.empty();

                if (response.success && response.data.events.length > 0) {
                    $.each(response.data.events, function(index, event) {
                        var status = event.status.charAt(0).toUpperCase() + event.status.slice(1);
                        var listingName = event.listing_name || 'N/A'; // Get listing name from response if available

                        var row = `
                            <tr>
                                <td><strong>${event.event_name}</strong></td>
                                <td>${listingName}</td>
                                <td>${event.event_date}</td>
                                <td>${event.event_time || 'N/A'}</td>
                                <td>${status}</td>
                                <td>
                                    <button class="button button-small aslp-edit-event" data-id="${event.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-event" data-id="${event.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        eventList.append(row);
                    });
                } else {
                    eventList.append('<tr><td colspan="6"><?php _e( 'No events found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                eventsAdmin.hideLoading();
                eventsAdmin.showMessage('<?php _e( 'Error loading events.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        loadBusinessListings: function() {
            var data = {
                'action': 'aslp_get_all_business_listings',
                'nonce': aslp_ajax_object.nonce
            };
            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                if (response.success && response.data.listings.length > 0) {
                    var listingSelect = $('#listing_id');
                    listingSelect.empty().append('<option value="0"><?php _e( 'Select a listing', 'as-laburda-pwa-app' ); ?></option>');
                    $.each(response.data.listings, function(index, listing) {
                        listingSelect.append(`<option value="${listing.id}">${listing.listing_name}</option>`);
                    });
                }
            });
        },

        showAddEventForm: function() {
            this.clearMessages();
            $('#aslp-event-form')[0].reset();
            $('#aslp-event-id').val('');
            $('#image_url_preview').attr('src', '').hide();
            $('.aslp-event-list-section').hide();
            $('.aslp-event-form-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        editEvent: function(e) {
            this.clearMessages();
            var event_id = $(e.target).data('id');
            this.showLoading();

            var data = {
                'action': 'aslp_get_event_admin', // Assuming a specific endpoint for single event in admin
                'nonce': aslp_ajax_object.nonce,
                'event_id': event_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                eventsAdmin.hideLoading();
                if (response.success && response.data.event) {
                    var event = response.data.event;
                    $('#aslp-event-id').val(event.id);
                    $('#event_name').val(event.event_name);
                    $('#description').val(event.description);
                    $('#event_date').val(event.event_date);
                    $('#event_time').val(event.event_time);
                    $('#location').val(event.location);
                    $('#image_url').val(event.image_url);
                    eventsAdmin.updateImagePreview($('#image_url'), $('#image_url_preview'));
                    $('#listing_id').val(event.business_listing_id);
                    $('#status').val(event.status);

                    $('.aslp-event-list-section').hide();
                    $('.aslp-event-form-section').show();
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                } else {
                    eventsAdmin.showMessage('<?php _e( 'Event not found.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                eventsAdmin.hideLoading();
                eventsAdmin.showMessage('<?php _e( 'Error fetching event details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        saveEvent: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var event_id = $('#aslp-event-id').val();
            var event_data_to_save = {
                event_name: $('#event_name').val(),
                description: $('#description').val(),
                event_date: $('#event_date').val(),
                event_time: $('#event_time').val(),
                location: $('#location').val(),
                image_url: $('#image_url').val(),
                business_listing_id: $('#listing_id').val(),
                status: $('#status').val(),
            };

            if (!event_data_to_save.business_listing_id || event_data_to_save.business_listing_id === '0') {
                this.hideLoading();
                this.showMessage('<?php _e( 'Please select an associated business listing.', 'as-laburda-pwa-app' ); ?>', 'error');
                return;
            }

            var data = {
                'action': 'aslp_create_update_event_admin', // New AJAX action for admin side events
                'nonce': aslp_ajax_object.nonce,
                'event_id': event_id,
                'event_data': JSON.stringify(event_data_to_save)
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                eventsAdmin.hideLoading();
                if (response.success) {
                    eventsAdmin.showMessage(response.data.message, 'success');
                    eventsAdmin.loadEvents();
                    eventsAdmin.cancelEdit();
                } else {
                    eventsAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                eventsAdmin.hideLoading();
                eventsAdmin.showMessage('<?php _e( 'Error saving event.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deleteEvent: function(e) {
            if (!confirm('<?php _e( 'Are you sure you want to delete this event? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                return;
            }

            this.clearMessages();
            this.showLoading();
            var event_id = $(e.target).data('id');

            var data = {
                'action': 'aslp_delete_event_admin', // New AJAX action for admin side events
                'nonce': aslp_ajax_object.nonce,
                'event_id': event_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                eventsAdmin.hideLoading();
                if (response.success) {
                    eventsAdmin.showMessage(response.data.message, 'success');
                    eventsAdmin.loadEvents();
                } else {
                    eventsAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                eventsAdmin.hideLoading();
                eventsAdmin.showMessage('<?php _e( 'Error deleting event.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        cancelEdit: function() {
            this.clearMessages();
            $('.aslp-event-form-section').hide();
            $('.aslp-event-list-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        }
    };

    eventsAdmin.init();
});
</script>
