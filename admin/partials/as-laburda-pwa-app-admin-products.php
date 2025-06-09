<?php
/**
 * The admin Products page for the AS Laburda PWA App plugin.
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

    <p><?php _e( 'Manage all products associated with business listings on your platform. You can add, edit, or delete products.', 'as-laburda-pwa-app' ); ?></p>

    <div id="aslp-products-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <div class="aslp-product-list-section">
            <h2><?php _e( 'All Products', 'as-laburda-pwa-app' ); ?></h2>
            <button id="aslp-add-new-product" class="button button-primary"><i class="fas fa-plus"></i> <?php _e( 'Add New Product', 'as-laburda-pwa-app' ); ?></button>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Product Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Listing Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Price', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Status', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Created', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-product-list">
                    <tr>
                        <td colspan="6"><?php _e( 'No products found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="aslp-product-form-section" style="display: none;">
            <h2><?php _e( 'Product Details', 'as-laburda-pwa-app' ); ?></h2>
            <form id="aslp-product-form">
                <input type="hidden" id="aslp-product-id" name="product_id" value="">

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="product_name"><?php _e( 'Product Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="product_name" name="product_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="description"><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="description" name="description" rows="5" cols="50" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="price"><?php _e( 'Price', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" step="0.01" min="0" id="price" name="price" class="regular-text" value="0.00" required></td>
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
                                <p class="description"><?php _e( 'Select the business listing this product belongs to.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="status"><?php _e( 'Status', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="status" name="status">
                                    <option value="active"><?php _e( 'Active', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="inactive"><?php _e( 'Inactive', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <button type="submit" id="aslp-save-product" class="button button-primary"><i class="fas fa-save"></i> <?php _e( 'Save Product', 'as-laburda-pwa-app' ); ?></button>
                    <button type="button" id="aslp-cancel-product-edit" class="button button-secondary"><?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var productsAdmin = {
        init: function() {
            this.loadProducts();
            this.loadBusinessListings(); // Load listings for dropdown
            this.bindEvents();
        },

        bindEvents: function() {
            $('#aslp-add-new-product').on('click', this.showAddProductForm.bind(this));
            $('#aslp-product-form').on('submit', this.saveProduct.bind(this));
            $('#aslp-cancel-product-edit').on('click', this.cancelEdit.bind(this));
            $('#aslp-product-list').on('click', '.aslp-edit-product', this.editProduct.bind(this));
            $('#aslp-product-list').on('click', '.aslp-delete-product', this.deleteProduct.bind(this));

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

        loadProducts: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_get_products_for_admin', // Assuming a new AJAX action for admin to get all products
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                productsAdmin.hideLoading();
                var productList = $('#aslp-product-list');
                productList.empty();

                if (response.success && response.data.products.length > 0) {
                    $.each(response.data.products, function(index, product) {
                        var status = product.status.charAt(0).toUpperCase() + product.status.slice(1);
                        var listingName = product.listing_name || 'N/A'; // Get listing name from response if available

                        var row = `
                            <tr>
                                <td><strong>${product.product_name}</strong></td>
                                <td>${listingName}</td>
                                <td>${parseFloat(product.price).toFixed(2)} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></td>
                                <td>${status}</td>
                                <td>${product.date_created}</td>
                                <td>
                                    <button class="button button-small aslp-edit-product" data-id="${product.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-product" data-id="${product.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        productList.append(row);
                    });
                } else {
                    productList.append('<tr><td colspan="6"><?php _e( 'No products found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                productsAdmin.hideLoading();
                productsAdmin.showMessage('<?php _e( 'Error loading products.', 'as-laburda-pwa-app' ); ?>', 'error');
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

        showAddProductForm: function() {
            this.clearMessages();
            $('#aslp-product-form')[0].reset();
            $('#aslp-product-id').val('');
            $('#image_url_preview').attr('src', '').hide();
            $('.aslp-product-list-section').hide();
            $('.aslp-product-form-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        editProduct: function(e) {
            this.clearMessages();
            var product_id = $(e.target).data('id');
            this.showLoading();

            var data = {
                'action': 'aslp_get_product_admin', // Assuming a specific endpoint for single product in admin
                'nonce': aslp_ajax_object.nonce,
                'product_id': product_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                productsAdmin.hideLoading();
                if (response.success && response.data.product) {
                    var product = response.data.product;
                    $('#aslp-product-id').val(product.id);
                    $('#product_name').val(product.product_name);
                    $('#description').val(product.description);
                    $('#price').val(parseFloat(product.price).toFixed(2));
                    $('#image_url').val(product.image_url);
                    productsAdmin.updateImagePreview($('#image_url'), $('#image_url_preview'));
                    $('#listing_id').val(product.business_listing_id);
                    $('#status').val(product.status);

                    $('.aslp-product-list-section').hide();
                    $('.aslp-product-form-section').show();
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                } else {
                    productsAdmin.showMessage('<?php _e( 'Product not found.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                productsAdmin.hideLoading();
                productsAdmin.showMessage('<?php _e( 'Error fetching product details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        saveProduct: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var product_id = $('#aslp-product-id').val();
            var product_data_to_save = {
                product_name: $('#product_name').val(),
                description: $('#description').val(),
                price: parseFloat($('#price').val()),
                image_url: $('#image_url').val(),
                business_listing_id: $('#listing_id').val(),
                status: $('#status').val(),
            };

            if (!product_data_to_save.business_listing_id || product_data_to_save.business_listing_id === '0') {
                this.hideLoading();
                this.showMessage('<?php _e( 'Please select an associated business listing.', 'as-laburda-pwa-app' ); ?>', 'error');
                return;
            }

            var data = {
                'action': 'aslp_create_update_product_admin', // New AJAX action for admin side products
                'nonce': aslp_ajax_object.nonce,
                'product_id': product_id,
                'product_data': JSON.stringify(product_data_to_save)
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                productsAdmin.hideLoading();
                if (response.success) {
                    productsAdmin.showMessage(response.data.message, 'success');
                    productsAdmin.loadProducts();
                    productsAdmin.cancelEdit();
                } else {
                    productsAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                productsAdmin.hideLoading();
                productsAdmin.showMessage('<?php _e( 'Error saving product.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deleteProduct: function(e) {
            if (!confirm('<?php _e( 'Are you sure you want to delete this product? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                return;
            }

            this.clearMessages();
            this.showLoading();
            var product_id = $(e.target).data('id');

            var data = {
                'action': 'aslp_delete_product_admin', // New AJAX action for admin side products
                'nonce': aslp_ajax_object.nonce,
                'product_id': product_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                productsAdmin.hideLoading();
                if (response.success) {
                    productsAdmin.showMessage(response.data.message, 'success');
                    productsAdmin.loadProducts();
                } else {
                    productsAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                productsAdmin.hideLoading();
                productsAdmin.showMessage('<?php _e( 'Error deleting product.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        cancelEdit: function() {
            this.clearMessages();
            $('.aslp-product-form-section').hide();
            $('.aslp-product-list-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        }
    };

    productsAdmin.init();
});
</script>
