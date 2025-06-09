/**
 * Public-facing JavaScript for AS Laburda PWA App.
 *
 * This file handles front-end interactions, AJAX calls for shortcodes,
 * and analytics tracking.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/public/js
 */

jQuery(document).ready(function($) {

    // --- Global Utility Functions for Public Side ---
    /**
     * Shows a custom alert modal.
     * @param {string} title The title for the alert.
     * @param {string} message The message to display.
     */
    window.aslpShowAlert = function(title, message) {
        var $modal = $('<div class="aslp-custom-modal-overlay"><div class="aslp-custom-modal aslp-alert-modal">' +
            '<h3>' + title + '</h3><p>' + message + '</p>' +
            '<button class="aslp-modal-ok-button button button-primary">OK</button>' +
            '</div></div>');
        $('body').append($modal);

        $modal.find('.aslp-modal-ok-button').on('click', function() {
            $modal.remove();
        });
    };

    /**
     * Shows a custom confirmation modal.
     * @param {string} title The title for the confirmation.
     * @param {string} message The message to display.
     * @param {function} onConfirm Callback function on confirm.
     * @param {function} onCancel Callback function on cancel.
     */
    window.aslpShowConfirm = function(title, message, onConfirm, onCancel) {
        var $modal = $('<div class="aslp-custom-modal-overlay"><div class="aslp-custom-modal aslp-confirm-modal">' +
            '<h3>' + title + '</h3><p>' + message + '</p>' +
            '<button class="aslp-modal-confirm-button button button-primary">Confirm</button>' +
            '<button class="aslp-modal-cancel-button button button-secondary">Cancel</button>' +
            '</div></div>');
        $('body').append($modal);

        $modal.find('.aslp-modal-confirm-button').on('click', function() {
            $modal.remove();
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        });

        $modal.find('.aslp-modal-cancel-button').on('click', function() {
            $modal.remove();
            if (typeof onCancel === 'function') {
                onCancel();
            }
        });
    };

    /**
     * Tracks a click event for analytics.
     * @param {string} itemId The ID of the item being clicked (e.g., listing ID, app UUID, affiliate ID).
     * @param {string} itemType The type of item ('listing', 'app', 'affiliate').
     * @param {string} clickTarget A specific description of what was clicked (e.g., 'phone_number', 'website_link', 'product_view', 'affiliate_link').
     */
    window.aslpTrackClick = function(itemId, itemType, clickTarget) {
        if (typeof aslp_public_ajax_object === 'undefined' || !aslp_public_ajax_object.ajax_url) {
            console.error('ASLP Analytics: aslp_public_ajax_object is not defined.');
            return;
        }

        $.post(aslp_public_ajax_object.ajax_url, {
            action: 'aslp_track_click',
            nonce: aslp_public_ajax_object.nonce,
            item_id: itemId,
            item_type: itemType,
            click_target: clickTarget
        }, function(response) {
            if (response.success) {
                console.log('ASLP Analytics: Click tracked successfully for ' + itemType + ' ' + itemId + ' - ' + clickTarget);
            } else {
                console.error('ASLP Analytics: Failed to track click. ' + (response.data.message || ''));
            }
        }).fail(function() {
            console.error('ASLP Analytics: AJAX error during click tracking.');
        });
    };

    // --- Dashboard Tabs Functionality ---
    $('.aslp-public-dashboard-container .nav-tab-wrapper a').on('click', function(e) {
        e.preventDefault();
        var tabId = $(this).attr('href'); // e.g., #dashboard-apps
        var tabName = $(this).data('tab'); // e.g., 'apps'

        // Update active tab styling
        $('.aslp-public-dashboard-container .nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        // Show/hide tab content
        $('.aslp-public-dashboard-container .aslp-tab-content').hide();
        $(tabId).show();

        // Optionally load content for specific tabs if not already loaded by PHP do_shortcode
        if (tabName === 'overview') {
            // Already loaded by PHP, but can refresh if needed
            // publicDashboard.loadOverviewData(); // Call this if it's a dynamic overview
        } else if (tabName === 'apps') {
            if (typeof appBuilderFrontend !== 'undefined' && typeof appBuilderFrontend.loadUserApps === 'function') {
                appBuilderFrontend.loadUserApps();
            }
        } else if (tabName === 'listings') {
            if (typeof userBusinessListings !== 'undefined' && typeof userBusinessListings.loadUserListings === 'function') {
                userBusinessListings.loadUserListings();
            }
        } else if (tabName === 'affiliate') {
            if (typeof affiliatesPublic !== 'undefined' && typeof affiliatesPublic.loadDashboardData === 'function') {
                affiliatesPublic.loadDashboardData();
            }
        } else if (tabName === 'notifications') {
            if (typeof userNotifications !== 'undefined' && typeof userNotifications.loadSubscribedBusinesses === 'function') {
                userNotifications.loadSubscribedBusinesses();
            }
        }
    });

    // Handle overview cards linking to specific tabs
    $('.aslp-public-dashboard-container').on('click', '.aslp-switch-tab', function(e) {
        e.preventDefault();
        var targetTab = $(this).data('tab'); // e.g., 'apps'
        // Simulate a click on the corresponding nav tab
        $('.aslp-public-dashboard-container .nav-tab[data-tab="' + targetTab + '"]').click();
    });

    // --- Common Form/List Handlers (used across different sections) ---
    var commonHandlers = {
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
        }
    };


    // --- Public Dashboard Specific Functions (e.g., overview counts) ---
    var publicDashboard = {
        init: function() {
            this.loadOverviewData();
        },

        loadOverviewData: function() {
            commonHandlers.showLoading();
            commonHandlers.clearMessages();

            var appsPromise = $.post(aslp_public_ajax_object.ajax_url, {
                action: 'aslp_get_user_apps',
                nonce: aslp_public_ajax_object.nonce
            });

            var listingsPromise = $.post(aslp_public_ajax_object.ajax_url, {
                action: 'aslp_get_user_business_listings',
                nonce: aslp_public_ajax_object.nonce
            });

            var affiliatePromise = $.post(aslp_public_ajax_object.ajax_url, {
                action: 'aslp_get_affiliate_data',
                nonce: aslp_public_ajax_object.nonce
            });

            $.when(appsPromise, listingsPromise, affiliatePromise)
                .done(function(appsResponse, listingsResponse, affiliateResponse) {
                    commonHandlers.hideLoading();

                    if (appsResponse[0].success) {
                        $('#overview-my-apps').text(appsResponse[0].data.apps.length);
                    } else {
                        console.error('Error loading user apps for overview:', appsResponse[0].data.message);
                        $('#overview-my-apps').text('N/A');
                    }

                    if (listingsResponse[0].success) {
                        $('#overview-my-listings').text(listingsResponse[0].data.listings.length);
                    } else {
                        console.error('Error loading user listings for overview:', listingsResponse[0].data.message);
                        $('#overview-my-listings').text('N/A');
                    }

                    if (affiliateResponse[0].success && affiliateResponse[0].data.affiliate_data) {
                        var walletBalance = parseFloat(affiliateResponse[0].data.affiliate_data.wallet_balance || 0).toFixed(2);
                        $('#overview-affiliate-wallet').text(walletBalance + ' <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?>');
                    } else {
                        console.error('Error loading affiliate data for overview:', affiliateResponse[0].data.message);
                        $('#overview-affiliate-wallet').text('N/A');
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    commonHandlers.hideLoading();
                    commonHandlers.showMessage('<?php _e( 'An error occurred while loading dashboard overview data: ', 'as-laburda-pwa-app' ); ?>' + textStatus, 'error');
                    console.error('Public Dashboard Overview AJAX error:', textStatus, errorThrown);
                });
        }
    };
    // Initialize public dashboard overview if the container is present
    if ($('#aslp-public-dashboard-app').length) {
        publicDashboard.init();
    }


    // --- App Builder Frontend Functions (from partials/as-laburda-pwa-app-public-app-builder.php script block) ---
    var appBuilderFrontend = {
        init: function() {
            this.loadUserApps();
            this.loadAppTemplates(); // Load templates for the dropdown
            this.bindEvents();
        },
        bindEvents: function() {
            $('#aslp-create-new-app').on('click', this.showAddAppForm.bind(this));
            $('#aslp-app-form').on('submit', this.saveApp.bind(this));
            $('#aslp-cancel-app-edit').on('click', this.cancelEdit.bind(this));
            $('#aslp-user-app-list').on('click', '.aslp-edit-app', this.editApp.bind(this));
            $('#aslp-user-app-list').on('click', '.aslp-delete-app', this.deleteApp.bind(this));
            $('#aslp-load-template').on('click', this.toggleTemplateSelect.bind(this));
            $('#aslp-template-select').on('change', this.applyTemplate.bind(this));
        },
        showLoading: commonHandlers.showLoading,
        hideLoading: commonHandlers.hideLoading,
        showMessage: commonHandlers.showMessage,
        clearMessages: commonHandlers.clearMessages,
        loadUserApps: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_get_user_apps',
                'nonce': aslp_public_ajax_object.nonce
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                appBuilderFrontend.hideLoading();
                var userAppList = $('#aslp-user-app-list');
                userAppList.empty();

                if (response.success && response.data.apps.length > 0) {
                    $.each(response.data.apps, function(index, app) {
                        var status = app.status.charAt(0).toUpperCase() + app.status.slice(1);
                        var row = `
                            <tr>
                                <td><strong>${app.app_name}</strong></td>
                                <td>${app.description}</td>
                                <td>${status}</td>
                                <td>${app.date_created}</td>
                                <td>
                                    <button class="button button-small aslp-edit-app" data-uuid="${app.app_uuid}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-app" data-uuid="${app.app_uuid}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        userAppList.append(row);
                    });
                } else {
                    userAppList.append('<tr><td colspan="5"><?php _e( 'No apps found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                appBuilderFrontend.hideLoading();
                appBuilderFrontend.showMessage('<?php _e( 'Error loading your apps.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },
        loadAppTemplates: function() {
            var data = {
                'action': 'aslp_get_app_templates',
                'nonce': aslp_public_ajax_object.nonce
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                if (response.success && response.data.templates.length > 0) {
                    var templateSelect = $('#aslp-template-select');
                    templateSelect.empty().append('<option value=""><?php _e( 'Select a template', 'as-laburda-pwa-app' ); ?></option>');
                    $.each(response.data.templates, function(index, template) {
                        templateSelect.append(`<option value="${template.id}" data-config='${template.template_data}'>${template.template_name}</option>`);
                    });
                }
            });
        },
        toggleTemplateSelect: function() {
            $('#aslp-template-select').toggle();
        },
        applyTemplate: function() {
            var selectedOption = $('#aslp-template-select option:selected');
            if (selectedOption.val()) {
                var templateConfig = selectedOption.data('config');
                $('#app_config_json').val(JSON.stringify(templateConfig, null, 2));
                $('#aslp-template-select').hide(); // Hide after selection
            }
        },
        showAddAppForm: function() {
            this.clearMessages();
            $('#aslp-app-form')[0].reset();
            $('#aslp-app-uuid').val('');
            $('#aslp-template-select').hide(); // Hide template select initially
            $('.aslp-app-list-section').hide();
            $('.aslp-app-form-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },
        editApp: function(e) {
            this.clearMessages();
            var app_uuid = $(e.target).data('uuid');
            this.showLoading();

            var data = {
                'action': 'aslp_get_app_by_id',
                'nonce': aslp_public_ajax_object.nonce,
                'app_uuid': app_uuid
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                appBuilderFrontend.hideLoading();
                if (response.success && response.data.app) {
                    var app = response.data.app;
                    $('#aslp-app-uuid').val(app.app_uuid);
                    $('#app_name').val(app.app_name);
                    $('#app_description').val(app.description);
                    $('#app_config_json').val(JSON.stringify(JSON.parse(app.app_config), null, 2));
                    $('#app_status').val(app.status);
                    $('#aslp-template-select').hide();
                    $('.aslp-app-list-section').hide();
                    $('.aslp-app-form-section').show();
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                } else {
                    appBuilderFrontend.showMessage('<?php _e( 'App not found or you do not have permission to edit it.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                appBuilderFrontend.hideLoading();
                appBuilderFrontend.showMessage('<?php _e( 'Error fetching app details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },
        saveApp: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var app_uuid = $('#aslp-app-uuid').val();
            var app_config_raw = $('#app_config_json').val();
            var app_config_json;

            try {
                app_config_json = JSON.parse(app_config_raw);
            } catch (e) {
                this.hideLoading();
                this.showMessage('<?php _e( 'Invalid JSON in App Configuration. Please correct it.', 'as-laburda-pwa-app' ); ?>', 'error');
                return;
            }

            var app_data_to_save = {
                app_name: $('#app_name').val(),
                description: $('#app_description').val(),
                app_config: app_config_json,
                status: $('#app_status').val(),
            };

            var data = {
                'action': 'aslp_create_update_app',
                'nonce': aslp_public_ajax_object.nonce,
                'app_uuid': app_uuid,
                'app_data': JSON.stringify(app_data_to_save)
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                appBuilderFrontend.hideLoading();
                if (response.success) {
                    appBuilderFrontend.showMessage(response.data.message, 'success');
                    appBuilderFrontend.loadUserApps();
                    appBuilderFrontend.cancelEdit();
                } else {
                    appBuilderFrontend.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                appBuilderFrontend.hideLoading();
                appBuilderFrontend.showMessage('<?php _e( 'Error saving app.', 'as-laburda-pwa-app' ); ?>', 'error');
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
                'action': 'aslp_delete_app_frontend',
                'nonce': aslp_public_ajax_object.nonce,
                'app_uuid': app_uuid
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                appBuilderFrontend.hideLoading();
                if (response.success) {
                    appBuilderFrontend.showMessage(response.data.message, 'success');
                    appBuilderFrontend.loadUserApps();
                } else {
                    appBuilderFrontend.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                appBuilderFrontend.hideLoading();
                appBuilderFrontend.showMessage('<?php _e( 'Error deleting app.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },
        cancelEdit: function() {
            this.clearMessages();
            $('.aslp-app-form-section').hide();
            $('.aslp-app-list-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        }
    };
    // Conditionally initialize based on presence of the app builder container
    if ($('#aslp-app-builder-frontend-app').length) {
        appBuilderFrontend.init();
    }


    // --- User Business Listings Functions (from partials/as-laburda-pwa-app-public-business-listings.php script block) ---
    var userBusinessListings = {
        currentListingId: null,

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
            if (<?php echo json_encode(AS_Laburda_PWA_App::get_instance()->get_global_feature_settings()['enable_products'] ?? false); ?>) {
                $('#aslp-products-events-management').on('click', '#aslp-add-new-product', this.showAddProductForm.bind(this));
                $('#aslp-product-form').on('submit', this.saveProduct.bind(this));
                $('#aslp-product-list-container').on('click', '.aslp-edit-product', this.editProduct.bind(this));
                $('#aslp-product-list-container').on('click', '.aslp-delete-product', this.deleteProduct.bind(this));
                $('#aslp-product-form-modal .aslp-close-modal').on('click', this.closeProductModal.bind(this));
            }

            // Event management (if enabled)
            if (<?php echo json_encode(AS_Laburda_PWA_App::get_instance()->get_global_feature_settings()['enable_events'] ?? false); ?>) {
                $('#aslp-products-events-management').on('click', '#aslp-add-new-event', this.showAddEventForm.bind(this));
                $('#aslp-event-form').on('submit', this.saveEvent.bind(this));
                $('#aslp-event-list-container').on('click', '.aslp-edit-event', this.editEvent.bind(this));
                $('#aslp-event-list-container').on('click', '.aslp-delete-event', this.deleteEvent.bind(this));
                $('#aslp-event-form-modal .aslp-close-modal').on('click', this.closeEventModal.bind(this));
            }

            // Media Uploader for images (global listener)
            $(document).on('click', '.aslp-media-upload-button', commonHandlers.openMediaUploader.bind(commonHandlers));
        },

        showLoading: commonHandlers.showLoading,
        hideLoading: commonHandlers.hideLoading,
        showMessage: commonHandlers.showMessage,
        clearMessages: commonHandlers.clearMessages,
        updateImagePreview: commonHandlers.updateImagePreview,

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
            this.currentListingId = listing_id;
            this.showLoading();

            var data = {
                'action': 'aslp_get_single_business_listing', // Use dedicated endpoint for single listing
                'nonce': aslp_public_ajax_object.nonce,
                'listing_id': listing_id
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                userBusinessListings.hideLoading();
                if (response.success && response.data.listing) {
                    var listing = response.data.listing;
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
                    $('#aslp-products-events-management').show();
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                } else {
                    userBusinessListings.showMessage('<?php _e( 'Business listing not found or you do not have permission to edit it.', 'as-laburda-pwa-app' ); ?>', 'error');
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
                    var slug = match[1].replace(/\[\]$/, '');
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
                custom_fields: custom_fields_data
            };

            var data = {
                'action': 'aslp_create_update_business_listing',
                'nonce': aslp_public_ajax_object.nonce,
                'listing_id': listing_id,
                'listing_data': JSON.stringify(listing_data_to_save)
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
            window.aslpShowConfirm(
                '<?php _e( 'Delete Listing', 'as-laburda-pwa-app' ); ?>',
                '<?php _e( 'Are you sure you want to delete this business listing? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>',
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
                function() { /* On cancel - do nothing */ }
            );
        },

        cancelListingEdit: function() {
            this.clearMessages();
            this.currentListingId = null;
            $('.aslp-listing-form-section').hide();
            $('#aslp-products-events-management').hide();
            $('.aslp-listing-list-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        // --- Product Management Functions (if enabled) ---
        loadProducts: function(listing_id) {
            var productListContainer = $('#aslp-product-list-container');
            productListContainer.empty();

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
            if (!userBusinessListings.currentListingId) {
                userBusinessListings.showMessage('<?php _e( 'Please save the listing first before adding products.', 'as-laburda-pwa-app' ); ?>', 'warning');
                return;
            }
            userBusinessListings.clearMessages();
            $('#aslp-product-form')[0].reset();
            $('#product_id').val('');
            $('#product_listing_id').val(userBusinessListings.currentListingId);
            $('#product_image_url_preview').attr('src', '').hide();
            $('#aslp-product-form-modal').fadeIn();
        },

        editProduct: function(e) {
            userBusinessListings.clearMessages();
            var product_id = $(e.target).data('id');
            userBusinessListings.showLoading();

            var data = {
                'action': 'aslp_get_product',
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
            userBusinessListings.clearMessages();
            userBusinessListings.showLoading();

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
            window.aslpShowConfirm(
                '<?php _e( 'Delete Product', 'as-laburda-pwa-app' ); ?>',
                '<?php _e( 'Are you sure you want to delete this product? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>',
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
                function() { /* On cancel - do nothing */ }
            );
        },

        closeProductModal: function() {
            $('#aslp-product-form-modal').fadeOut();
        },

        // --- Event Management Functions (if enabled) ---
        loadEvents: function(listing_id) {
            var eventListContainer = $('#aslp-event-list-container');
            eventListContainer.empty();

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
            if (!userBusinessListings.currentListingId) {
                userBusinessListings.showMessage('<?php _e( 'Please save the listing first before adding events.', 'as-laburda-pwa-app' ); ?>', 'warning');
                return;
            }
            userBusinessListings.clearMessages();
            $('#aslp-event-form')[0].reset();
            $('#event_id').val('');
            $('#event_listing_id').val(userBusinessListings.currentListingId);
            $('#aslp-event-form-modal').fadeIn();
        },

        editEvent: function(e) {
            userBusinessListings.clearMessages();
            var event_id = $(e.target).data('id');
            userBusinessListings.showLoading();

            var data = {
                'action': 'aslp_get_event',
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
                    $('#event_date').val(event.event_date);
                    $('#event_time').val(event.event_time);
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
            userBusinessListings.clearMessages();
            userBusinessListings.showLoading();

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
                    userBusinessListings.showMessage('<?php _e( 'Error saving event.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                userBusinessListings.hideLoading();
                userBusinessListings.showMessage('<?php _e( 'Error saving event.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deleteEvent: function(e) {
            window.aslpShowConfirm(
                '<?php _e( 'Delete Event', 'as-laburda-pwa-app' ); ?>',
                '<?php _e( 'Are you sure you want to delete this event? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>',
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
                            userBusinessListings.showMessage('<?php _e( 'Error deleting event.', 'as-laburda-pwa-app' ); ?>', 'error');
                        }
                    }).fail(function() {
                        userBusinessListings.hideLoading();
                        userBusinessListings.showMessage('<?php _e( 'Error deleting event.', 'as-laburda-pwa-app' ); ?>', 'error');
                    });
                },
                function() { /* On cancel - do nothing */ }
            );
        },

        closeEventModal: function() {
            $('#aslp-event-form-modal').fadeOut();
        }
    };
    // Conditionally initialize based on presence of the listing management container
    if ($('.aslp-business-listings-frontend-container').length) {
        userBusinessListings.init();
    }


    // --- Affiliate Dashboard Frontend Functions (from partials/as-laburda-pwa-app-public-affiliate-dashboard.php script block) ---
    var affiliatesPublic = {
        init: function() {
            // Check if affiliate registration form is present, if so, just bind events for registration
            if ($('#aslp-affiliate-register-form').length) {
                 this.bindEvents(); // Only bind events for registration form if it's there
            } else {
                // Otherwise, load dashboard data and bind events for dashboard features
                this.loadDashboardData();
                this.bindEvents();
            }
        },

        bindEvents: function() {
            $('#aslp-affiliate-register-form').on('submit', this.registerAffiliate.bind(this));
            $('#aslp-request-payout-button').on('click', this.requestPayout.bind(this));
            // Event listener for copying creative code/link
            $(document).on('click', '.aslp-copy-creative-code', this.copyCreativeCode.bind(this));
        },

        showLoading: commonHandlers.showLoading,
        hideLoading: commonHandlers.hideLoading,
        showMessage: commonHandlers.showMessage,
        clearMessages: commonHandlers.clearMessages,

        loadDashboardData: function() {
            this.showLoading();
            this.clearMessages();

            var data = {
                'action': 'aslp_get_affiliate_data',
                'nonce': aslp_public_ajax_object.nonce
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                affiliatesPublic.hideLoading();
                if (response.success && response.data.affiliate_data) {
                    var affiliateData = response.data.affiliate_data;
                    $('#affiliate-wallet-balance').text(parseFloat(affiliateData.wallet_balance).toFixed(2) + ' <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?>');
                    if (affiliateData.wallet_balance <= 0) {
                        $('#aslp-request-payout-button').prop('disabled', true);
                    } else {
                        $('#aslp-request-payout-button').prop('disabled', false);
                    }

                    // Populate commission history
                    var commissionHtml = '<tr><td colspan="5"><?php _e( 'No recent commissions.', 'as-laburda-pwa-app' ); ?></td></tr>';
                    if (affiliateData.recent_commissions && affiliateData.recent_commissions.length > 0) {
                        commissionHtml = '';
                        $.each(affiliateData.recent_commissions, function(i, comm) {
                            commissionHtml += `
                                <tr>
                                    <td>${comm.id}</td>
                                    <td>${parseFloat(comm.commission_amount).toFixed(2)} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></td>
                                    <td>${comm.referral_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
                                    <td>${comm.commission_status.charAt(0).toUpperCase() + comm.commission_status.slice(1)}</td>
                                    <td>${comm.date_created}</td>
                                </tr>
                            `;
                        });
                    }
                    $('#aslp-commission-history-list').html(commissionHtml);


                    // Populate payout history
                    var payoutHtml = '<tr><td colspan="5"><?php _e( 'No recent payouts.', 'as-laburda-pwa-app' ); ?></td></tr>';
                    if (affiliateData.recent_payouts && affiliateData.recent_payouts.length > 0) {
                        payoutHtml = '';
                        $.each(affiliateData.recent_payouts, function(i, payout) {
                            var completedDate = (payout.date_completed && payout.date_completed !== '0000-00-00 00:00:00') ? payout.date_completed : 'N/A';
                            payoutHtml += `
                                <tr>
                                    <td>${payout.id}</td>
                                    <td>${parseFloat(payout.payout_amount).toFixed(2)} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></td>
                                    <td>${payout.payout_method.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
                                    <td>${payout.payout_status.charAt(0).toUpperCase() + payout.payout_status.slice(1)}</td>
                                    <td>${payout.date_requested}</td>
                                    <td>${completedDate}</td>
                                </tr>
                            `;
                        });
                    }
                    $('#aslp-payout-history-list').html(payoutHtml);

                    // Populate creatives
                    var creativesHtml = '<tr><td colspan="2"><?php _e( 'No creatives available for your tier.', 'as-laburda-pwa-app' ); ?></td></tr>';
                    if (affiliateData.creatives && affiliateData.creatives.length > 0) {
                        creativesHtml = '';
                        $.each(affiliateData.creatives, function(i, creative) {
                            var creativeContentDisplay = creative.content;
                            var copyButtonText = '<?php _e( 'Copy Content', 'as-laburda-pwa-app' ); ?>';

                            if (creative.creative_type === 'image_banner') {
                                creativeContentDisplay = `<img src="${creative.content}" style="max-width: 150px; height: auto;">`;
                                copyButtonText = '<?php _e( 'Copy Image URL', 'as-laburda-pwa-app' ); ?>';
                            } else if (creative.creative_type === 'text_link') {
                                // Add affiliate code to the link
                                creativeContentDisplay = `<a href="${creative.content}?ref=${affiliateData.affiliate_code}" target="_blank">${creative.content}</a>`;
                                copyButtonText = '<?php _e( 'Copy Link', 'as-laburda-pwa-app' ); ?>';
                            } else if (creative.creative_type === 'html_code') {
                                // Replace placeholder in HTML code
                                creativeContentDisplay = creative.content.replace(/{{affiliate_code}}/g, affiliateData.affiliate_code);
                                copyButtonText = '<?php _e( 'Copy HTML', 'as-laburda-pwa-app' ); ?>';
                            }
                            
                            creativesHtml += `
                                <tr>
                                    <td><strong>${creative.creative_name}</strong><br><small>${creative.creative_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</small></td>
                                    <td>
                                        <div>${creativeContentDisplay}</div>
                                        <textarea class="aslp-creative-code-snippet" style="width:100%; height:80px; margin-top:5px;" readonly>${creative.content.replace(/{{affiliate_code}}/g, affiliateData.affiliate_code)}</textarea>
                                        <button class="button button-small aslp-copy-creative-code" data-target-text=".aslp-creative-code-snippet">${copyButtonText}</button>
                                    </td>
                                </tr>
                            `;
                        });
                    }
                    $('#aslp-creative-list').html(creativesHtml);

                } else {
                    // This handles cases where user is logged in but not an affiliate yet
                    $('#aslp-affiliate-dashboard-container').html('<p><?php _e( 'You are not registered as an affiliate or your application is pending review.', 'as-laburda-pwa-app' ); ?></p>');
                    // If the registration form is not loaded by PHP directly, uncomment the following line
                    // affiliatesPublic.showRegistrationForm();
                }
            }).fail(function() {
                affiliatesPublic.hideLoading();
                affiliatesPublic.showMessage('<?php _e( 'Error loading affiliate dashboard data.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        registerAffiliate: function(e) {
            e.preventDefault();
            this.showLoading();
            this.clearMessages();

            var formData = $(e.target).serializeArray();
            var data = {
                action: 'aslp_affiliate_registration',
                nonce: aslp_public_ajax_object.nonce,
                payment_email: $('#affiliate_payment_email').val(),
                affiliate_email: $('#affiliate_email').val(), // Ensure these match expected backend keys
                affiliate_website: $('#affiliate_website').val()
            };

            $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                affiliatesPublic.hideLoading();
                if (response.success) {
                    affiliatesPublic.showMessage(response.data.message, 'success');
                    affiliatesPublic.loadDashboardData(); // Refresh dashboard to show pending/active status
                } else {
                    affiliatesPublic.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                affiliatesPublic.hideLoading();
                affiliatesPublic.showMessage('<?php _e( 'An AJAX error occurred during registration.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        requestPayout: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var amount = parseFloat($('#affiliate-wallet-balance').text().replace(/[^0-9.]/g, '')); // Extract numeric value
            var paymentMethod = $('#payout-method').val();

            if (isNaN(amount) || amount <= 0) {
                this.hideLoading();
                this.showMessage('<?php _e( 'Please enter a valid amount greater than zero.', 'as-laburda-pwa-app' ); ?>', 'error');
                return;
            }

            window.aslpShowConfirm(
                '<?php _e( 'Confirm Payout Request', 'as-laburda-pwa-app' ); ?>',
                '<?php _e( 'Are you sure you want to request a payout of', 'as-laburda-pwa-app' ); ?> ' + amount.toFixed(2) + ' <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?> via ' + paymentMethod + '?',
                function() { // On confirm
                    var data = {
                        action: 'aslp_affiliate_request_payout',
                        nonce: aslp_public_ajax_object.nonce,
                        amount: amount,
                        payment_method: paymentMethod
                    };

                    $.post(aslp_public_ajax_object.ajax_url, data, function(response) {
                        affiliatesPublic.hideLoading();
                        if (response.success) {
                            affiliatesPublic.showMessage(response.data.message, 'success');
                            affiliatesPublic.loadDashboardData(); // Refresh data
                        } else {
                            affiliatesPublic.showMessage(response.data.message, 'error');
                        }
                    }).fail(function() {
                        affiliatesPublic.hideLoading();
                        affiliatesPublic.showMessage('<?php _e( 'An AJAX error occurred during payout request.', 'as-laburda-pwa-app' ); ?>', 'error');
                    });
                },
                function() { // On cancel
                    affiliatesPublic.hideLoading();
                    // Do nothing
                }
            );
        },

        copyCreativeCode: function(e) {
            var button = $(e.currentTarget);
            var targetTextarea = button.prev('textarea.aslp-creative-code-snippet');
            if (targetTextarea.length) {
                targetTextarea.select();
                try {
                    document.execCommand('copy');
                    button.text('<?php _e( 'Copied!', 'as-laburda-pwa-app' ); ?>').delay(1000).queue(function(next){
                        $(this).text(button.data('original-text') || '<?php _e( 'Copy Code', 'as-laburda-pwa-app' ); ?>');
                        next();
                    });
                } catch (err) {
                    console.error('Failed to copy text: ', err);
                }
            }
        }
    };
    // Conditionally initialize based on presence of the affiliate container
    if ($('.aslp-affiliate-dashboard-container').length || $('.aslp-affiliate-registration-form').length) {
        affiliatesPublic.init();
    }
});
