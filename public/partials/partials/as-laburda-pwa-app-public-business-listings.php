<?php
/**
 * The public-facing business listings management for the AS Laburda PWA App plugin.
 *
 * This file is used to markup the public-facing aspects of the plugin,
 * specifically the user's business listings management area.
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/public/partials
 */

// Ensure the user is logged in to view this page
if ( ! is_user_logged_in() ) {
    echo '<p>' . __( 'Please log in to manage your business listings.', 'as-laburda-pwa-app' ) . '</p>';
    return;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Get global feature settings to conditionally display sections
$global_features = AS_Laburda_PWA_App::get_instance()->get_global_feature_settings();

// Check if business listings feature is enabled and user has capability
if ( ! ( $global_features['enable_business_listings'] ?? false ) || ! user_can( $user_id, 'aslp_submit_business_listing' ) ) {
    echo '<p>' . __( 'Business listings management is currently disabled or you do not have permission to access it.', 'as-laburda-pwa-app' ) . '</p>';
    return;
}

?>

<div class="aslp-business-listings-frontend-container">
    <h3><?php _e( 'Your Business Listings', 'as-laburda-pwa-app' ); ?></h3>

    <div id="aslp-user-listings-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading listings...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <div class="aslp-listing-list-section">
            <h4><?php _e( 'Your Listings', 'as-laburda-pwa-app' ); ?></h4>
            <button id="aslp-add-new-listing" class="button button-primary"><i class="fas fa-plus"></i> <?php _e( 'Add New Listing', 'as-laburda-pwa-app' ); ?></button>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Listing Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Status', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Created', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-user-listing-list">
                    <tr>
                        <td colspan="4"><?php _e( 'No business listings found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="aslp-listing-form-section" style="display: none;">
            <h4><?php _e( 'Listing Details', 'as-laburda-pwa-app' ); ?></h4>
            <form id="aslp-listing-form">
                <input type="hidden" id="aslp-listing-id" name="listing_id" value="">

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="listing_name"><?php _e( 'Listing Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="listing_name" name="listing_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="description"><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="description" name="description" rows="5" cols="50" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="address"><?php _e( 'Address', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="address" name="address" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="city"><?php _e( 'City', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="city" name="city" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="state"><?php _e( 'State / Province', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="state" name="state" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="zip_code"><?php _e( 'Zip / Postal Code', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="zip_code" name="zip_code" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="country"><?php _e( 'Country', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="country" name="country" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="phone"><?php _e( 'Phone', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="tel" id="phone" name="phone" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="email"><?php _e( 'Email', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="email" id="email" name="email" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="website"><?php _e( 'Website', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="url" id="website" name="website" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="logo_url"><?php _e( 'Logo URL', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="text" id="logo_url" name="logo_url" class="regular-text aslp-media-upload-url" value="">
                                <button type="button" class="button aslp-media-upload-button"><?php _e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview">
                                    <img src="" id="logo_url_preview" style="max-width: 150px; height: auto; display: none;">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="featured_image_url"><?php _e( 'Featured Image URL', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="text" id="featured_image_url" name="featured_image_url" class="regular-text aslp-media-upload-url" value="">
                                <button type="button" class="button aslp-media-upload-button"><?php _e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview">
                                    <img src="" id="featured_image_url_preview" style="max-width: 150px; height: auto; display: none;">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="status"><?php _e( 'Status', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="status" name="status">
                                    <option value="pending"><?php _e( 'Pending Review', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="active"><?php _e( 'Active', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="inactive"><?php _e( 'Inactive', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                                <p class="description"><?php _e( 'Listings may require admin approval to become active.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>

                        <?php
                        // Dynamically add custom fields if enabled
                        if ( ( $global_features['enable_custom_fields'] ?? false ) ) {
                            $custom_fields = AS_Laburda_PWA_App::get_instance()->get_database_manager()->get_all_custom_fields( array( 'applies_to' => 'listing', 'is_active' => true ) );
                            if ( ! empty( $custom_fields ) ) {
                                foreach ( $custom_fields as $field ) {
                                    ?>
                                    <tr>
                                        <th scope="row"><label for="custom_field_<?php echo esc_attr( $field->field_slug ); ?>"><?php echo esc_html( $field->field_name ); ?></label></th>
                                        <td>
                                            <?php
                                            $field_options = AS_Laburda_PWA_App_Utils::safe_json_decode( $field->field_options, true );
                                            $field_id_attr = 'custom_field_' . esc_attr( $field->field_slug );
                                            $field_name_attr = 'custom_fields[' . esc_attr( $field->field_slug ) . ']';
                                            $required_attr = $field->is_required ? 'required' : '';

                                            switch ( $field->field_type ) {
                                                case 'text':
                                                case 'email':
                                                case 'url':
                                                case 'number':
                                                case 'date':
                                                    echo '<input type="' . esc_attr( $field->field_type ) . '" id="' . $field_id_attr . '" name="' . $field_name_attr . '" class="regular-text" ' . $required_attr . '>';
                                                    break;
                                                case 'textarea':
                                                    echo '<textarea id="' . $field_id_attr . '" name="' . $field_name_attr . '" rows="5" cols="50" class="large-text" ' . $required_attr . '></textarea>';
                                                    break;
                                                case 'select':
                                                    echo '<select id="' . $field_id_attr . '" name="' . $field_name_attr . '" ' . $required_attr . '>';
                                                    echo '<option value="">' . __( 'Select...', 'as-laburda-pwa-app' ) . '</option>';
                                                    foreach ( $field_options as $option ) {
                                                        echo '<option value="' . esc_attr( $option ) . '">' . esc_html( $option ) . '</option>';
                                                    }
                                                    echo '</select>';
                                                    break;
                                                case 'checkbox':
                                                    foreach ( $field_options as $option ) {
                                                        echo '<label><input type="checkbox" name="' . $field_name_attr . '[]" value="' . esc_attr( $option ) . '"> ' . esc_html( $option ) . '</label><br>';
                                                    }
                                                    break;
                                                case 'radio':
                                                    foreach ( $field_options as $option ) {
                                                        echo '<label><input type="radio" name="' . $field_name_attr . '" value="' . esc_attr( $option ) . '" ' . $required_attr . '> ' . esc_html( $option ) . '</label><br>';
                                                    }
                                                    break;
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <p class="submit">
                    <button type="submit" id="aslp-save-listing" class="button button-primary"><i class="fas fa-save"></i> <?php _e( 'Save Listing', 'as-laburda-pwa-app' ); ?></button>
                    <button type="button" id="aslp-cancel-listing-edit" class="button button-secondary"><?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                </p>
            </form>

            <div id="aslp-products-events-management" style="display: none;">
                <hr>
                <?php if ( ( $global_features['enable_products'] ?? false ) ) : ?>
                    <h4><?php _e( 'Products', 'as-laburda-pwa-app' ); ?></h4>
                    <button id="aslp-add-new-product" class="button button-secondary"><i class="fas fa-plus"></i> <?php _e( 'Add New Product', 'as-laburda-pwa-app' ); ?></button>
                    <div id="aslp-product-list-container">
                        <p><?php _e( 'No products added yet.', 'as-laburda-pwa-app' ); ?></p>
                    </div>

                    <div id="aslp-product-form-modal" class="aslp-modal-overlay" style="display:none;">
                        <div class="aslp-modal-content">
                            <h3><?php _e( 'Product Details', 'as-laburda-pwa-app' ); ?></h3>
                            <form id="aslp-product-form">
                                <input type="hidden" id="product_id" name="product_id" value="">
                                <input type="hidden" id="product_listing_id" name="listing_id" value="">
                                <table class="form-table">
                                    <tbody>
                                        <tr>
                                            <th scope="row"><label for="product_name"><?php _e( 'Product Name', 'as-laburda-pwa-app' ); ?></label></th>
                                            <td><input type="text" id="product_name" name="product_name" class="regular-text" required></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="product_description"><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                                            <td><textarea id="product_description" name="description" rows="3" cols="50" class="large-text"></textarea></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="product_price"><?php _e( 'Price', 'as-laburda-pwa-app' ); ?></label></th>
                                            <td><input type="number" step="0.01" min="0" id="product_price" name="price" class="regular-text" value="0.00"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="product_link"><?php _e( 'Product Link', 'as-laburda-pwa-app' ); ?></label></th>
                                            <td><input type="url" id="product_link" name="link" class="regular-text"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="product_image_url"><?php _e( 'Image URL', 'as-laburda-pwa-app' ); ?></label></th>
                                            <td>
                                                <input type="text" id="product_image_url" name="image_url" class="regular-text aslp-media-upload-url" value="">
                                                <button type="button" class="button aslp-media-upload-button"><?php _e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                                <div class="aslp-image-preview">
                                                    <img src="" id="product_image_url_preview" style="max-width: 100px; height: auto; display: none;">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p class="submit">
                                    <button type="submit" class="button button-primary"><?php _e( 'Save Product', 'as-laburda-pwa-app' ); ?></button>
                                    <button type="button" class="button button-secondary aslp-close-modal"><?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                                </p>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ( ( $global_features['enable_events'] ?? false ) ) : ?>
                    <hr>
                    <h4><?php _e( 'Events', 'as-laburda-pwa-app' ); ?></h4>
                    <button id="aslp-add-new-event" class="button button-secondary"><i class="fas fa-plus"></i> <?php _e( 'Add New Event', 'as-laburda-pwa-app' ); ?></button>
                    <div id="aslp-event-list-container">
                        <p><?php _e( 'No events added yet.', 'as-laburda-pwa-app' ); ?></p>
                    </div>

                    <div id="aslp-event-form-modal" class="aslp-modal-overlay" style="display:none;">
                        <div class="aslp-modal-content">
                            <h3><?php _e( 'Event Details', 'as-laburda-pwa-app' ); ?></h3>
                            <form id="aslp-event-form">
                                <input type="hidden" id="event_id" name="event_id" value="">
                                <input type="hidden" id="event_listing_id" name="listing_id" value="">
                                <table class="form-table">
                                    <tbody>
                                        <tr>
                                            <th scope="row"><label for="event_name"><?php _e( 'Event Name', 'as-laburda-pwa-app' ); ?></label></th>
                                            <td><input type="text" id="event_name" name="event_name" class="regular-text" required></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="event_description"><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                                            <td><textarea id="event_description" name="description" rows="3" cols="50" class="large-text"></textarea></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="event_date"><?php _e( 'Date', 'as-laburda-pwa-app' ); ?></label></th>
                                            <td><input type="date" id="event_date" name="event_date" class="regular-text" required></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="event_time"><?php _e( 'Time', 'as-laburda-pwa-app' ); ?></label></th>
                                            <td><input type="time" id="event_time" name="event_time" class="regular-text"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="event_location"><?php _e( 'Location', 'as-laburda-pwa-app' ); ?></label></th>
                                            <td><input type="text" id="event_location" name="location" class="regular-text"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="event_link"><?php _e( 'Event Link', 'as-laburda-pwa-app' ); ?></label></th>
                                            <td><input type="url" id="event_link" name="link" class="regular-text"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p class="submit">
                                    <button type="submit" class="button button-primary"><?php _e( 'Save Event', 'as-laburda-pwa-app' ); ?></button>
                                    <button type="button" class="button button-secondary aslp-close-modal"><?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                                </p>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var userBusinessListings = {
        currentListingId: null, // To store the ID of the listing being edited

        init: function() {
            this.loadUserListings();
            this.bindEvents();
        },

        bindEvents: function() {
            // Listing management
            $('#aslp-add-new-listing').on('click', this.showAddListingForm.bind(this));
            $('#aslp-listing-form').on('submit', this.saveListing.bind(this));
            $('#aslp-cancel-listing-edit').on('click', this.cancelListingEdit.bind(this));
            $('#aslp-user-listing-list').on('click', '.aslp-edit-listing', this.editListing.bind(this));
            $('#aslp-user-listing-list').on('click', '.aslp-delete-listing', this.deleteListing.bind(this));

            // Product management (if enabled)
            <?php if ( ( $global_features['enable_products'] ?? false ) ) : ?>
            $('#aslp-add-new-product').on('click', this.showAddProductForm.bind(this));
            $('#aslp-product-form').on('submit', this.saveProduct.bind(this));
            $('#aslp-product-list-container').on('click', '.aslp-edit-product', this.editProduct.bind(this));
            $('#aslp-product-list-container').on('click', '.aslp-delete-product', this.deleteProduct.bind(this));
            $('#aslp-product-form-modal .aslp-close-modal').on('click', this.closeProductModal.bind(this));
            <?php endif; ?>

            // Event management (if enabled)
            <?php if ( ( $global_features['enable_events'] ?? false ) ) : ?>
            $('#aslp-add-new-event').on('click', this.showAddEventForm.bind(this));
            $('#aslp-event-form').on('submit', this.saveEvent.bind(this));
            $('#aslp-event-list-container').on('click', '.aslp-edit-event', this.editEvent.bind(this));
            $('#aslp-event-list-container').on('click', '.aslp-delete-event', this.deleteEvent.bind(this));
            $('#aslp-event-form-modal .aslp-close-modal').on('click', this.closeEventModal.bind(this));
            <?php endif; ?>

            // Media Uploader for images
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

        // --- Listing Management Functions ---
        loadUserListings: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_get_user_business_listings',
                'nonce': aslp_public_ajax_object.nonce
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                userBusinessListings.hideLoading();
                var userListingList = $('#aslp-user-listing-list');
                userListingList.empty();

                if (response.success && response.data.listings.length > 0) {
                    $.each(response.data.listings, function(index, listing) {
                        var status = listing.status.charAt(0).toUpperCase() + listing.status.slice(1);
                        var row = `
                            <tr>
                                <td><strong>${listing.listing_name}</strong></td>
                                <td>${status}</td>
                                <td>${listing.date_created}</td>
                                <td>
                                    <button class="button button-small aslp-edit-listing" data-id="${listing.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-listing" data-id="${listing.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        userListingList.append(row);
                    });
                } else {
                    userListingList.append('<tr><td colspan="4"><?php _e( 'No business listings found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                userBusinessListings.hideLoading();
                userBusinessListings.showMessage('<?php _e( 'Error loading your business listings.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        showAddListingForm: function() {
            this.clearMessages();
            $('#aslp-listing-form')[0].reset();
            $('#aslp-listing-id').val('');
            $('#logo_url_preview').attr('src', '').hide();
            $('#featured_image_url_preview').attr('src', '').hide();
            // Clear custom fields
            $('input[name^="custom_fields["], textarea[name^="custom_fields["], select[name^="custom_fields["]').val('');
            $('input[type="checkbox"][name^="custom_fields["], input[type="radio"][name^="custom_fields["]').prop('checked', false);

            $('#aslp-products-events-management').hide(); // Hide products/events section for new listing
            $('.aslp-listing-list-section').hide();
            $('.aslp-listing-form-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        editListing: function(e) {
            this.clearMessages();
            var listing_id = $(e.target).data('id');
            this.currentListingId = listing_id; // Set current listing ID
            this.showLoading();

            var data = {
                'action': 'aslp_get_user_business_listings', // Re-fetch all and find, or add a specific get_listing endpoint
                'nonce': aslp_public_ajax_object.nonce
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                userBusinessListings.hideLoading();
                if (response.success && response.data.listings.length > 0) {
                    var listing = response.data.listings.find(l => l.id == listing_id);
                    if (listing) {
                        $('#aslp-listing-id').val(listing.id);
                        $('#listing_name').val(listing.listing_name);
                        $('#description').val(listing.description);
                        $('#address').val(listing.address);
                        $('#city').val(listing.city);
                        $('#state').val(listing.state);
                        $('#zip_code').val(listing.zip_code);
                        $('#country').val(listing.country);
                        $('#phone').val(listing.phone);
                        $('#email').val(listing.email);
                        $('#website').val(listing.website);
                        $('#logo_url').val(listing.logo_url);
                        userBusinessListings.updateImagePreview($('#logo_url'), $('#logo_url_preview'));
                        $('#featured_image_url').val(listing.featured_image_url);
                        userBusinessListings.updateImagePreview($('#featured_image_url'), $('#featured_image_url_preview'));
                        $('#status').val(listing.status);

                        // Populate custom fields
                        var custom_fields_data = JSON.parse(listing.custom_fields || '{}');
                        for (var slug in custom_fields_data) {
                            var value = custom_fields_data[slug];
                            var inputElement = $(`#custom_field_${slug}`);
                            if (inputElement.attr('type') === 'checkbox') {
                                if (Array.isArray(value)) {
                                    value.forEach(v => $(`input[name="custom_fields[${slug}][]"][value="${v}"]`).prop('checked', true));
                                }
                            } else if (inputElement.attr('type') === 'radio') {
                                $(`input[name="custom_fields[${slug}]"][value="${value}"]`).prop('checked', true);
                            } else {
                                inputElement.val(value);
                            }
                        }

                        // Load products and events for this listing
                        userBusinessListings.loadProducts(listing_id);
                        userBusinessListings.loadEvents(listing_id);

                        $('.aslp-listing-list-section').hide();
                        $('.aslp-listing-form-section').show();
                        $('#aslp-products-events-management').show(); // Show products/events section
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    } else {
                        userBusinessListings.showMessage('<?php _e( 'Business listing not found or you do not have permission to edit it.', 'as-laburda-pwa-app' ); ?>', 'error');
                    }
                } else {
                    userBusinessListings.showMessage('<?php _e( 'Error fetching business listing details.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                userBusinessListings.hideLoading();
                userBusinessListings.showMessage('<?php _e( 'Error fetching business listing details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        saveListing: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var listing_id = $('#aslp-listing-id').val();
            var custom_fields_data = {};
            $('input[name^="custom_fields["], textarea[name^="custom_fields["], select[name^="custom_fields["]').each(function() {
                var name = $(this).attr('name');
                var match = name.match(/custom_fields\[(.*?)\]/);
                if (match && match[1]) {
                    var slug = match[1].replace(/\[\]$/, ''); // Remove [] for array fields
                    if ($(this).attr('type') === 'checkbox') {
                        if (!custom_fields_data[slug]) {
                            custom_fields_data[slug] = [];
                        }
                        if ($(this).is(':checked')) {
                            custom_fields_data[slug].push($(this).val());
                        }
                    } else if ($(this).attr('type') === 'radio') {
                        if ($(this).is(':checked')) {
                            custom_fields_data[slug] = $(this).val();
                        }
                    } else {
                        custom_fields_data[slug] = $(this).val();
                    }
                }
            });

            var listing_data_to_save = {
                listing_name: $('#listing_name').val(),
                description: $('#description').val(),
                address: $('#address').val(),
                city: $('#city').val(),
                state: $('#state').val(),
                zip_code: $('#zip_code').val(),
                country: $('#country').val(),
                phone: $('#phone').val(),
                email: $('#email').val(),
                website: $('#website').val(),
                logo_url: $('#logo_url').val(),
                featured_image_url: $('#featured_image_url').val(),
                status: $('#status').val(),
                custom_fields: custom_fields_data // Send as object
            };

            var data = {
                'action': 'aslp_create_update_business_listing',
                'nonce': aslp_public_ajax_object.nonce,
                'listing_id': listing_id,
                'listing_data': JSON.stringify(listing_data_to_save) // Stringify for AJAX post
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                userBusinessListings.hideLoading();
                if (response.success) {
                    userBusinessListings.showMessage(response.data.message, 'success');
                    userBusinessListings.loadUserListings();
                    userBusinessListings.cancelListingEdit();
                } else {
                    userBusinessListings.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                userBusinessListings.hideLoading();
                userBusinessListings.showMessage('<?php _e( 'Error saving business listing.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deleteListing: function(e) {
            showCustomConfirm(
                'Delete Listing',
                'Are you sure you want to delete this business listing? This action cannot be undone.',
                function() { // On confirm
                    userBusinessListings.clearMessages();
                    userBusinessListings.showLoading();
                    var listing_id = $(e.target).data('id');

                    var data = {
                        'action': 'aslp_delete_business_listing_frontend',
                        'nonce': aslp_public_ajax_object.nonce,
                        'listing_id': listing_id
                    };

                    $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                        userBusinessListings.hideLoading();
                        if (response.success) {
                            userBusinessListings.showMessage(response.data.message, 'success');
                            userBusinessListings.loadUserListings();
                        } else {
                            userBusinessListings.showMessage(response.data.message, 'error');
                        }
                    }).fail(function() {
                        userBusinessListings.hideLoading();
                        userBusinessListings.showMessage('<?php _e( 'Error deleting business listing.', 'as-laburda-pwa-app' ); ?>', 'error');
                    });
                },
                function() { // On cancel
                    // Do nothing
                }
            );
        },

        cancelListingEdit: function() {
            this.clearMessages();
            this.currentListingId = null; // Clear current listing ID
            $('.aslp-listing-form-section').hide();
            $('#aslp-products-events-management').hide();
            $('.aslp-listing-list-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        // --- Product Management Functions (if enabled) ---
        <?php if ( ( $global_features['enable_products'] ?? false ) ) : ?>
        loadProducts: function(listing_id) {
            var productListContainer = $('#aslp-product-list-container');
            productListContainer.empty(); // Clear existing products

            var data = {
                'action': 'aslp_get_products_by_listing',
                'nonce': aslp_public_ajax_object.nonce,
                'listing_id': listing_id
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                if (response.success && response.data.products.length > 0) {
                    var productsHtml = '<table class="wp-list-table widefat fixed striped"><thead><tr><th><?php _e( 'Product Name', 'as-laburda-pwa-app' ); ?></th><th><?php _e( 'Price', 'as-laburda-pwa-app' ); ?></th><th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th></tr></thead><tbody>';
                    $.each(response.data.products, function(index, product) {
                        productsHtml += `
                            <tr>
                                <td><strong>${product.product_name}</strong></td>
                                <td>${parseFloat(product.price).toFixed(2)} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></td>
                                <td>
                                    <button class="button button-small aslp-edit-product" data-id="${product.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-product" data-id="${product.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                    });
                    productsHtml += '</tbody></table>';
                    productListContainer.html(productsHtml);
                } else {
                    productListContainer.html('<p><?php _e( 'No products added yet.', 'as-laburda-pwa-app' ); ?></p>');
                }
            }).fail(function() {
                userBusinessListings.showMessage('<?php _e( 'Error loading products.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        showAddProductForm: function() {
            if (!this.currentListingId) {
                this.showMessage('<?php _e( 'Please save the listing first before adding products.', 'as-laburda-pwa-app' ); ?>', 'warning');
                return;
            }
            this.clearMessages();
            $('#aslp-product-form')[0].reset();
            $('#product_id').val('');
            $('#product_listing_id').val(this.currentListingId);
            $('#product_image_url_preview').attr('src', '').hide();
            $('#aslp-product-form-modal').fadeIn();
        },

        editProduct: function(e) {
            this.clearMessages();
            var product_id = $(e.target).data('id');
            this.showLoading();

            var data = {
                'action': 'aslp_get_product', // Assuming a specific endpoint for single product
                'nonce': aslp_public_ajax_object.nonce,
                'product_id': product_id
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                userBusinessListings.hideLoading();
                if (response.success && response.data.product) {
                    var product = response.data.product;
                    $('#product_id').val(product.id);
                    $('#product_listing_id').val(product.listing_id);
                    $('#product_name').val(product.product_name);
                    $('#product_description').val(product.description);
                    $('#product_price').val(parseFloat(product.price).toFixed(2));
                    $('#product_link').val(product.link);
                    $('#product_image_url').val(product.image_url);
                    userBusinessListings.updateImagePreview($('#product_image_url'), $('#product_image_url_preview'));
                    $('#aslp-product-form-modal').fadeIn();
                } else {
                    userBusinessListings.showMessage('<?php _e( 'Product not found or you do not have permission to edit it.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                userBusinessListings.hideLoading();
                userBusinessListings.showMessage('<?php _e( 'Error fetching product details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        saveProduct: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var product_id = $('#product_id').val();
            var product_data_to_save = {
                listing_id: $('#product_listing_id').val(),
                product_name: $('#product_name').val(),
                description: $('#product_description').val(),
                price: parseFloat($('#product_price').val()),
                link: $('#product_link').val(),
                image_url: $('#product_image_url').val()
            };

            var data = {
                'action': 'aslp_create_update_product',
                'nonce': aslp_public_ajax_object.nonce,
                'product_id': product_id,
                'product_data': JSON.stringify(product_data_to_save)
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                userBusinessListings.hideLoading();
                if (response.success) {
                    userBusinessListings.showMessage(response.data.message, 'success');
                    userBusinessListings.loadProducts(userBusinessListings.currentListingId);
                    userBusinessListings.closeProductModal();
                } else {
                    userBusinessListings.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                userBusinessListings.hideLoading();
                userBusinessListings.showMessage('<?php _e( 'Error saving product.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deleteProduct: function(e) {
            showCustomConfirm(
                'Delete Product',
                'Are you sure you want to delete this product? This action cannot be undone.',
                function() { // On confirm
                    userBusinessListings.clearMessages();
                    userBusinessListings.showLoading();
                    var product_id = $(e.target).data('id');

                    var data = {
                        'action': 'aslp_delete_product',
                        'nonce': aslp_public_ajax_object.nonce,
                        'product_id': product_id
                    };

                    $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                        userBusinessListings.hideLoading();
                        if (response.success) {
                            userBusinessListings.showMessage(response.data.message, 'success');
                            userBusinessListings.loadProducts(userBusinessListings.currentListingId);
                        } else {
                            userBusinessListings.showMessage(response.data.message, 'error');
                        }
                    }).fail(function() {
                        userBusinessListings.hideLoading();
                        userBusinessListings.showMessage('<?php _e( 'Error deleting product.', 'as-laburda-pwa-app' ); ?>', 'error');
                    });
                },
                function() { // On cancel
                    // Do nothing
                }
            );
        },

        closeProductModal: function() {
            $('#aslp-product-form-modal').fadeOut();
        },
        <?php endif; ?>

        // --- Event Management Functions (if enabled) ---
        <?php if ( ( $global_features['enable_events'] ?? false ) ) : ?>
        loadEvents: function(listing_id) {
            var eventListContainer = $('#aslp-event-list-container');
            eventListContainer.empty(); // Clear existing events

            var data = {
                'action': 'aslp_get_events_by_listing',
                'nonce': aslp_public_ajax_object.nonce,
                'listing_id': listing_id
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                if (response.success && response.data.events.length > 0) {
                    var eventsHtml = '<table class="wp-list-table widefat fixed striped"><thead><tr><th><?php _e( 'Event Name', 'as-laburda-pwa-app' ); ?></th><th><?php _e( 'Date', 'as-laburda-pwa-app' ); ?></th><th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th></tr></thead><tbody>';
                    $.each(response.data.events, function(index, event) {
                        eventsHtml += `
                            <tr>
                                <td><strong>${event.event_name}</strong></td>
                                <td>${event.event_date}</td>
                                <td>
                                    <button class="button button-small aslp-edit-event" data-id="${event.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-event" data-id="${event.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                    });
                    eventsHtml += '</tbody></table>';
                    eventListContainer.html(eventsHtml);
                } else {
                    eventListContainer.html('<p><?php _e( 'No events added yet.', 'as-laburda-pwa-app' ); ?></p>');
                }
            }).fail(function() {
                userBusinessListings.showMessage('<?php _e( 'Error loading events.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        showAddEventForm: function() {
            if (!this.currentListingId) {
                this.showMessage('<?php _e( 'Please save the listing first before adding events.', 'as-laburda-pwa-app' ); ?>', 'warning');
                return;
            }
            this.clearMessages();
            $('#aslp-event-form')[0].reset();
            $('#event_id').val('');
            $('#event_listing_id').val(this.currentListingId);
            $('#aslp-event-form-modal').fadeIn();
        },

        editEvent: function(e) {
            this.clearMessages();
            var event_id = $(e.target).data('id');
            this.showLoading();

            var data = {
                'action': 'aslp_get_event', // Assuming a specific endpoint for single event
                'nonce': aslp_public_ajax_object.nonce,
                'event_id': event_id
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                userBusinessListings.hideLoading();
                if (response.success && response.data.event) {
                    var event = response.data.event;
                    $('#event_id').val(event.id);
                    $('#event_listing_id').val(event.listing_id);
                    $('#event_name').val(event.event_name);
                    $('#event_description').val(event.description);
                    $('#event_date').val(event.event_date); // Date input needs YYYY-MM-DD format
                    $('#event_time').val(event.event_time); // Time input needs HH:MM format
                    $('#event_location').val(event.location);
                    $('#event_link').val(event.link);
                    $('#aslp-event-form-modal').fadeIn();
                } else {
                    userBusinessListings.showMessage('<?php _e( 'Event not found or you do not have permission to edit it.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                userBusinessListings.hideLoading();
                userBusinessListings.showMessage('<?php _e( 'Error fetching event details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        saveEvent: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var event_id = $('#event_id').val();
            var event_data_to_save = {
                listing_id: $('#event_listing_id').val(),
                event_name: $('#event_name').val(),
                description: $('#event_description').val(),
                event_date: $('#event_date').val(),
                event_time: $('#event_time').val(),
                location: $('#event_location').val(),
                link: $('#event_link').val()
            };

            var data = {
                'action': 'aslp_create_update_event',
                'nonce': aslp_public_ajax_object.nonce,
                'event_id': event_id,
                'event_data': JSON.stringify(event_data_to_save)
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                userBusinessListings.hideLoading();
                if (response.success) {
                    userBusinessListings.showMessage(response.data.message, 'success');
                    userBusinessListings.loadEvents(userBusinessListings.currentListingId);
                    userBusinessListings.closeEventModal();
                } else {
                    userBusinessListings.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                userBusinessListings.hideLoading();
                userBusinessListings.showMessage('<?php _e( 'Error saving event.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deleteEvent: function(e) {
            showCustomConfirm(
                'Delete Event',
                'Are you sure you want to delete this event? This action cannot be undone.',
                function() { // On confirm
                    userBusinessListings.clearMessages();
                    userBusinessListings.showLoading();
                    var event_id = $(e.target).data('id');

                    var data = {
                        'action': 'aslp_delete_event',
                        'nonce': aslp_public_ajax_object.nonce,
                        'event_id': event_id
                    };

                    $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                        userBusinessListings.hideLoading();
                        if (response.success) {
                            userBusinessListings.showMessage(response.data.message, 'success');
                            userBusinessListings.loadEvents(userBusinessListings.currentListingId);
                        } else {
                            userBusinessListings.showMessage(response.data.message, 'error');
                        }
                    }).fail(function() {
                        userBusinessListings.hideLoading();
                        userBusinessListings.showMessage('<?php _e( 'Error deleting event.', 'as-laburda-pwa-app' ); ?>', 'error');
                    });
                },
                function() { // On cancel
                    // Do nothing
                }
            );
        },

        closeEventModal: function() {
            $('#aslp-event-form-modal').fadeOut();
        }
        <?php endif; ?>
    };

    userBusinessListings.init();
});
</script>
