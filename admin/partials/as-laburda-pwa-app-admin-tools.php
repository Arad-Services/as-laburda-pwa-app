<?php
/**
 * The admin Tools page for the AS Laburda PWA App plugin.
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

    <p><?php _e( 'This section provides various tools to help manage your WordPress site in conjunction with the PWA App Creator plugin.', 'as-laburda-pwa-app' ); ?></p>

    <div id="aslp-tools-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Processing...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <div class="aslp-tool-section">
            <h2><?php _e( 'Essential Pages Management', 'as-laburda-pwa-app' ); ?></h2>
            <p><?php _e( 'This tool will check for and create any missing essential pages required by the plugin (e.g., App Dashboard, Affiliate Dashboard).', 'as-laburda-pwa-app' ); ?></p>
            <button id="aslp-create-missing-pages" class="button button-primary"><i class="fas fa-file-alt"></i> <?php _e( 'Create Missing Pages', 'as-laburda-pwa-app' ); ?></button>
            <div id="create-pages-results" class="aslp-tool-results" style="display:none;"></div>
        </div>

        <div class="aslp-tool-section" style="margin-top: 30px;">
            <h2><?php _e( 'Duplicated Pages Cleanup', 'as-laburda-pwa-app' ); ?></h2>
            <p><?php _e( 'Identify and fix duplicated WordPress pages (pages with the same title). This tool will keep only the oldest version of each duplicated page.', 'as-laburda-pwa-app' ); ?></p>
            <button id="aslp-check-duplicated-pages" class="button button-secondary"><i class="fas fa-search"></i> <?php _e( 'Check for Duplicated Pages', 'as-laburda-pwa-app' ); ?></button>
            <button id="aslp-fix-duplicated-pages" class="button button-danger" style="display:none;"><i class="fas fa-eraser"></i> <?php _e( 'Fix Duplicated Pages', 'as-laburda-pwa-app' ); ?></button>
            <div id="duplicated-pages-results" class="aslp-tool-results" style="margin-top: 15px; display:none;"></div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var adminTools = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            $('#aslp-create-missing-pages').on('click', this.createMissingPages.bind(this));
            $('#aslp-check-duplicated-pages').on('click', this.checkDuplicatedPages.bind(this));
            $('#aslp-fix-duplicated-pages').on('click', this.fixDuplicatedPages.bind(this));
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

        // --- Create Missing Pages ---
        createMissingPages: function() {
            this.clearMessages();
            this.showLoading();
            $('#create-pages-results').hide().empty();

            var data = {
                'action': 'aslp_admin_create_missing_pages',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                adminTools.hideLoading();
                if (response.success) {
                    adminTools.showMessage(response.data.message, 'success');
                    $('#create-pages-results').html('<p>' + response.data.message + '</p>').show();
                } else {
                    adminTools.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                adminTools.hideLoading();
                adminTools.showMessage('<?php _e( 'An AJAX error occurred while creating pages.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        // --- Check Duplicated Pages ---
        checkDuplicatedPages: function() {
            this.clearMessages();
            this.showLoading();
            $('#duplicated-pages-results').hide().empty();
            $('#aslp-fix-duplicated-pages').hide(); // Hide fix button until duplicates are found

            var data = {
                'action': 'aslp_admin_get_duplicated_pages',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                adminTools.hideLoading();
                if (response.success) {
                    if (response.data.duplicates && response.data.duplicates.length > 0) {
                        let html = '<h4><?php _e( 'Found Duplicated Pages:', 'as-laburda-pwa-app' ); ?></h4><ul>';
                        $.each(response.data.duplicates, function(i, duplicate) {
                            html += `<li><strong>${duplicate.post_title}</strong> (Count: ${duplicate.count}, IDs: ${duplicate.ids})</li>`;
                        });
                        html += '</ul><p><?php _e( 'Click "Fix Duplicated Pages" to delete all but the oldest version of these pages.', 'as-laburda-pwa-app' ); ?></p>';
                        $('#duplicated-pages-results').html(html).show();
                        $('#aslp-fix-duplicated-pages').show(); // Show fix button
                        adminTools.showMessage(response.data.message, 'warning');
                    } else {
                        $('#duplicated-pages-results').html('<p><?php _e( 'No duplicated pages found.', 'as-laburda-pwa-app' ); ?></p>').show();
                        adminTools.showMessage(response.data.message, 'success');
                    }
                } else {
                    adminTools.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                adminTools.hideLoading();
                adminTools.showMessage('<?php _e( 'An AJAX error occurred while checking for duplicated pages.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        // --- Fix Duplicated Pages ---
        fixDuplicatedPages: function() {
            if (!confirm('<?php _e( 'Are you absolutely sure you want to fix duplicated pages? This will permanently delete all but the oldest version of each duplicated page. This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                return;
            }

            this.clearMessages();
            this.showLoading();
            $('#duplicated-pages-results').hide().empty();
            $('#aslp-fix-duplicated-pages').hide();

            var data = {
                'action': 'aslp_admin_fix_duplicated_pages',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                adminTools.hideLoading();
                if (response.success) {
                    adminTools.showMessage(response.data.message, 'success');
                    $('#duplicated-pages-results').html('<p>' + response.data.message + '</p>').show();
                } else {
                    adminTools.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                adminTools.hideLoading();
                adminTools.showMessage('<?php _e( 'An AJAX error occurred while fixing duplicated pages.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        }
    };

    adminTools.init();
});
</script>
