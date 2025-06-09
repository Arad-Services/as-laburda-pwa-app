<?php
/**
 * The admin App Menus page for the AS Laburda PWA App plugin.
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

    <p><?php _e( 'Create and manage custom navigation menus for your Progressive Web Apps. These menus can be assigned to different apps or used as global navigation.', 'as-laburda-pwa-app' ); ?></p>

    <div id="aslp-app-menus-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <div class="aslp-menu-list-section">
            <h2><?php _e( 'Available App Menus', 'as-laburda-pwa-app' ); ?></h2>
            <button id="aslp-add-new-menu" class="button button-primary"><i class="fas fa-plus"></i> <?php _e( 'Add New Menu', 'as-laburda-pwa-app' ); ?></button>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Menu Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Active', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Created', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-menu-list">
                    <tr>
                        <td colspan="5"><?php _e( 'No app menus found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="aslp-menu-form-section" style="display: none;">
            <h2><?php _e( 'Menu Details', 'as-laburda-pwa-app' ); ?></h2>
            <form id="aslp-menu-form">
                <input type="hidden" id="aslp-menu-id" name="menu_id" value="">

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="menu_name"><?php _e( 'Menu Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="menu_name" name="menu_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="menu_description"><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="menu_description" name="description" rows="5" cols="50" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Menu Items', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <div id="aslp-menu-items-container">
                                    <!-- Menu items will be dynamically added here -->
                                    <p><?php _e( 'No menu items added yet.', 'as-laburda-pwa-app' ); ?></p>
                                </div>
                                <button type="button" id="aslp-add-menu-item" class="button button-secondary"><i class="fas fa-plus"></i> <?php _e( 'Add Menu Item', 'as-laburda-pwa-app' ); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Is Active', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <label for="is_active">
                                    <input type="checkbox" id="is_active" name="is_active" value="1">
                                    <?php _e( 'Enable this menu for use', 'as-laburda-pwa-app' ); ?>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <button type="submit" id="aslp-save-menu" class="button button-primary"><i class="fas fa-save"></i> <?php _e( 'Save Menu', 'as-laburda-pwa-app' ); ?></button>
                    <button type="button" id="aslp-cancel-menu-edit" class="button button-secondary"><?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var appMenus = {
        menuItemCounter: 0,

        init: function() {
            this.loadMenus();
            this.bindEvents();
        },

        bindEvents: function() {
            $('#aslp-add-new-menu').on('click', this.showAddMenuForm.bind(this));
            $('#aslp-menu-form').on('submit', this.saveMenu.bind(this));
            $('#aslp-cancel-menu-edit').on('click', this.cancelEdit.bind(this));
            $('#aslp-menu-list').on('click', '.aslp-edit-menu', this.editMenu.bind(this));
            $('#aslp-menu-list').on('click', '.aslp-delete-menu', this.deleteMenu.bind(this));
            $('#aslp-add-menu-item').on('click', this.addMenuItem.bind(this));
            $('#aslp-menu-items-container').on('click', '.aslp-remove-menu-item', this.removeMenuItem.bind(this));
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

        loadMenus: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_get_all_app_menus',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appMenus.hideLoading();
                var menuList = $('#aslp-menu-list');
                menuList.empty();

                if (response.success && response.data.menus.length > 0) {
                    $.each(response.data.menus, function(index, menu) {
                        var status = menu.is_active == 1 ? '<?php _e( 'Active', 'as-laburda-pwa-app' ); ?>' : '<?php _e( 'Inactive', 'as-laburda-pwa-app' ); ?>';
                        var row = `
                            <tr>
                                <td><strong>${menu.menu_name}</strong></td>
                                <td>${menu.description}</td>
                                <td>${status}</td>
                                <td>${menu.date_created}</td>
                                <td>
                                    <button class="button button-small aslp-edit-menu" data-id="${menu.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-menu" data-id="${menu.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        menuList.append(row);
                    });
                } else {
                    menuList.append('<tr><td colspan="5"><?php _e( 'No app menus found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                appMenus.hideLoading();
                appMenus.showMessage('<?php _e( 'Error loading app menus.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        showAddMenuForm: function() {
            this.clearMessages();
            $('#aslp-menu-form')[0].reset();
            $('#aslp-menu-id').val('');
            $('#aslp-menu-items-container').empty(); // Clear existing menu items
            this.menuItemCounter = 0; // Reset counter for new form
            $('#aslp-menu-items-container').append('<p><?php _e( 'No menu items added yet.', 'as-laburda-pwa-app' ); ?></p>');
            $('.aslp-menu-list-section').hide();
            $('.aslp-menu-form-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        editMenu: function(e) {
            this.clearMessages();
            var menu_id = $(e.target).data('id');
            this.showLoading();

            var data = {
                'action': 'aslp_get_all_app_menus', // Re-fetch all and filter, or add a specific get_app_menu endpoint
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appMenus.hideLoading();
                if (response.success && response.data.menus.length > 0) {
                    var menu = response.data.menus.find(m => m.id == menu_id);
                    if (menu) {
                        $('#aslp-menu-id').val(menu.id);
                        $('#menu_name').val(menu.menu_name);
                        $('#menu_description').val(menu.description);
                        $('#is_active').prop('checked', menu.is_active == 1);

                        // Populate menu items
                        $('#aslp-menu-items-container').empty();
                        appMenus.menuItemCounter = 0;
                        var menu_items = JSON.parse(menu.menu_items);
                        if (menu_items && menu_items.length > 0) {
                            $.each(menu_items, function(index, item) {
                                appMenus.addMenuItem(item);
                            });
                        } else {
                            $('#aslp-menu-items-container').append('<p><?php _e( 'No menu items added yet.', 'as-laburda-pwa-app' ); ?></p>');
                        }

                        $('.aslp-menu-list-section').hide();
                        $('.aslp-menu-form-section').show();
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    } else {
                        appMenus.showMessage('<?php _e( 'App menu not found.', 'as-laburda-pwa-app' ); ?>', 'error');
                    }
                } else {
                    appMenus.showMessage('<?php _e( 'Error fetching app menu details.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                appMenus.hideLoading();
                appMenus.showMessage('<?php _e( 'Error fetching app menu details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        addMenuItem: function(item = {}) {
            $('#aslp-menu-items-container p').remove(); // Remove "No menu items" message
            var index = appMenus.menuItemCounter++;
            var menuItemHtml = `
                <div class="aslp-menu-item-row" data-index="${index}" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background: #f9f9f9;">
                    <h4 style="margin-top: 0;"><?php _e( 'Menu Item', 'as-laburda-pwa-app' ); ?> #${index + 1} <button type="button" class="button button-small aslp-remove-menu-item" style="float: right;"><i class="fas fa-times"></i> <?php _e( 'Remove', 'as-laburda-pwa-app' ); ?></button></h4>
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="menu_item_label_${index}"><?php _e( 'Label', 'as-laburda-pwa-app' ); ?></label></th>
                                <td><input type="text" id="menu_item_label_${index}" class="regular-text menu-item-label" value="${item.label || ''}" required></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="menu_item_url_${index}"><?php _e( 'URL', 'as-laburda-pwa-app' ); ?></label></th>
                                <td><input type="url" id="menu_item_url_${index}" class="regular-text menu-item-url" value="${item.url || ''}" required></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="menu_item_icon_${index}"><?php _e( 'Icon (Font Awesome class)', 'as-laburda-pwa-app' ); ?></label></th>
                                <td><input type="text" id="menu_item_icon_${index}" class="regular-text menu-item-icon" value="${item.icon || ''}">
                                <p class="description"><?php _e( 'e.g., fas fa-home, fab fa-facebook. See Font Awesome for classes.', 'as-laburda-pwa-app' ); ?></p></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="menu_item_type_${index}"><?php _e( 'Type', 'as-laburda-pwa-app' ); ?></label></th>
                                <td>
                                    <select id="menu_item_type_${index}" class="menu-item-type">
                                        <option value="internal" ${item.type === 'internal' ? 'selected' : ''}><?php _e( 'Internal Page', 'as-laburda-pwa-app' ); ?></option>
                                        <option value="external" ${item.type === 'external' ? 'selected' : ''}><?php _e( 'External Link', 'as-laburda-pwa-app' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;
            $('#aslp-menu-items-container').append(menuItemHtml);
        },

        removeMenuItem: function(e) {
            if (!confirm('<?php _e( 'Are you sure you want to remove this menu item?', 'as-laburda-pwa-app' ); ?>')) {
                return;
            }
            $(e.target).closest('.aslp-menu-item-row').remove();
            if ($('#aslp-menu-items-container').children().length === 0) {
                $('#aslp-menu-items-container').append('<p><?php _e( 'No menu items added yet.', 'as-laburda-pwa-app' ); ?></p>');
            }
        },

        saveMenu: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var menu_id = $('#aslp-menu-id').val();
            var menu_items = [];
            $('.aslp-menu-item-row').each(function() {
                var item = {
                    label: $(this).find('.menu-item-label').val(),
                    url: $(this).find('.menu-item-url').val(),
                    icon: $(this).find('.menu-item-icon').val(),
                    type: $(this).find('.menu-item-type').val()
                };
                menu_items.push(item);
            });

            var menu_data_to_save = {
                menu_name: $('#menu_name').val(),
                description: $('#menu_description').val(),
                menu_items: menu_items,
                is_active: $('#is_active').is(':checked') ? 1 : 0,
            };

            var data = {
                'action': 'aslp_add_update_app_menu',
                'nonce': aslp_ajax_object.nonce,
                'menu_id': menu_id,
                'menu_data': JSON.stringify(menu_data_to_save)
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appMenus.hideLoading();
                if (response.success) {
                    appMenus.showMessage(response.data.message, 'success');
                    appMenus.loadMenus();
                    appMenus.cancelEdit();
                } else {
                    appMenus.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                appMenus.hideLoading();
                appMenus.showMessage('<?php _e( 'Error saving app menu.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deleteMenu: function(e) {
            if (!confirm('<?php _e( 'Are you sure you want to delete this app menu? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                return;
            }

            this.clearMessages();
            this.showLoading();
            var menu_id = $(e.target).data('id');

            var data = {
                'action': 'aslp_delete_app_menu',
                'nonce': aslp_ajax_object.nonce,
                'menu_id': menu_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                appMenus.hideLoading();
                if (response.success) {
                    appMenus.showMessage(response.data.message, 'success');
                    appMenus.loadMenus();
                } else {
                    appMenus.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                appMenus.hideLoading();
                appMenus.showMessage('<?php _e( 'Error deleting app menu.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        cancelEdit: function() {
            this.clearMessages();
            $('.aslp-menu-form-section').hide();
            $('.aslp-menu-list-section').show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        }
    };

    appMenus.init();
});
</script>
