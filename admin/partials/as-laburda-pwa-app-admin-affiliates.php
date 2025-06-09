<?php
/**
 * The admin Affiliate Program page for the AS Laburda PWA App plugin.
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

    <p><?php _e( 'Manage your affiliate program. Review affiliate registrations, manage tiers, track commissions, and process payouts.', 'as-laburda-pwa-app' ); ?></p>

    <h2 class="nav-tab-wrapper">
        <a href="#affiliates-overview" data-tab="overview" class="nav-tab nav-tab-active"><?php _e( 'Overview', 'as-laburda-pwa-app' ); ?></a>
        <a href="#affiliates-list" data-tab="list" class="nav-tab"><?php _e( 'Affiliates', 'as-laburda-pwa-app' ); ?></a>
        <a href="#affiliate-tiers" data-tab="tiers" class="nav-tab"><?php _e( 'Tiers', 'as-laburda-pwa-app' ); ?></a>
        <a href="#affiliate-commissions" data-tab="commissions" class="nav-tab"><?php _e( 'Commissions', 'as-laburda-pwa-app' ); ?></a>
        <a href="#affiliate-payouts" data-tab="payouts" class="nav-tab"><?php _e( 'Payouts', 'as-laburda-pwa-app' ); ?></a>
        <a href="#affiliate-creatives" data-tab="creatives" class="nav-tab"><?php _e( 'Creatives', 'as-laburda-pwa-app' ); ?></a>
    </h2>

    <div id="aslp-affiliates-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Loading...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <!-- Overview Tab -->
        <div id="affiliates-overview" class="aslp-tab-content active">
            <h2><?php _e( 'Affiliate Program Overview', 'as-laburda-pwa-app' ); ?></h2>
            <div class="aslp-dashboard-cards">
                <div class="aslp-card">
                    <h3><?php _e( 'Total Affiliates', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="overview-total-affiliates" class="aslp-card-value">0</p>
                </div>
                <div class="aslp-card">
                    <h3><?php _e( 'Pending Affiliates', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="overview-pending-affiliates" class="aslp-card-value">0</p>
                </div>
                <div class="aslp-card">
                    <h3><?php _e( 'Total Commissions', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="overview-total-commissions" class="aslp-card-value">0.00</p>
                </div>
                <div class="aslp-card">
                    <h3><?php _e( 'Unpaid Commissions', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="overview-unpaid-commissions" class="aslp-card-value">0.00</p>
                </div>
                <div class="aslp-card">
                    <h3><?php _e( 'Total Payouts', 'as-laburda-pwa-app' ); ?></h3>
                    <p id="overview-total-payouts" class="aslp-card-value">0.00</p>
                </div>
            </div>
        </div>

        <!-- Affiliates List Tab -->
        <div id="affiliates-list" class="aslp-tab-content" style="display:none;">
            <h2><?php _e( 'Registered Affiliates', 'as-laburda-pwa-app' ); ?></h2>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'User ID', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'User Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Affiliate Code', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Status', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Current Tier', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Wallet Balance', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Registered', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-affiliate-list">
                    <tr>
                        <td colspan="8"><?php _e( 'No affiliates found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Affiliate Tiers Tab -->
        <div id="affiliate-tiers" class="aslp-tab-content" style="display:none;">
            <h2><?php _e( 'Affiliate Tiers', 'as-laburda-pwa-app' ); ?></h2>
            <button id="aslp-add-new-tier" class="button button-primary"><i class="fas fa-plus"></i> <?php _e( 'Add New Tier', 'as-laburda-pwa-app' ); ?></button>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Tier Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Base Commission Rate (%)', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'MLM Commission Rate (%)', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Active', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Created', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-tier-list">
                    <tr>
                        <td colspan="7"><?php _e( 'No affiliate tiers found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="aslp-tier-form-section" style="display: none;">
                <h3><?php _e( 'Tier Details', 'as-laburda-pwa-app' ); ?></h3>
                <form id="aslp-tier-form">
                    <input type="hidden" id="aslp-tier-id" name="tier_id" value="">

                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="tier_name"><?php _e( 'Tier Name', 'as-laburda-pwa-app' ); ?></label></th>
                                <td><input type="text" id="tier_name" name="tier_name" class="regular-text" required></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="tier_description"><?php _e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                                <td><textarea id="tier_description" name="description" rows="3" cols="50" class="large-text"></textarea></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="base_commission_rate"><?php _e( 'Base Commission Rate (%)', 'as-laburda-pwa-app' ); ?></label></th>
                                <td><input type="number" step="0.01" min="0" max="100" id="base_commission_rate" name="base_commission_rate" class="regular-text" value="0.00" required></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mlm_commission_rate"><?php _e( 'MLM Commission Rate (%)', 'as-laburda-pwa-app' ); ?></label></th>
                                <td><input type="number" step="0.01" min="0" max="100" id="mlm_commission_rate" name="mlm_commission_rate" class="regular-text" value="0.00">
                                <p class="description"><?php _e( 'Commission rate for multi-level marketing (MLM) referrals.', 'as-laburda-pwa-app' ); ?></p></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e( 'Is Active', 'as-laburda-pwa-app' ); ?></th>
                                <td>
                                    <label for="tier_is_active">
                                        <input type="checkbox" id="tier_is_active" name="is_active" value="1">
                                        <?php _e( 'Enable this tier', 'as-laburda-pwa-app' ); ?>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="submit">
                        <button type="submit" id="aslp-save-tier" class="button button-primary"><i class="fas fa-save"></i> <?php _e( 'Save Tier', 'as-laburda-pwa-app' ); ?></button>
                        <button type="button" id="aslp-cancel-tier-edit" class="button button-secondary"><?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                    </p>
                </form>
            </div>
        </div>

        <!-- Affiliate Commissions Tab -->
        <div id="affiliate-commissions" class="aslp-tab-content" style="display:none;">
            <h2><?php _e( 'Affiliate Commissions', 'as-laburda-pwa-app' ); ?></h2>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Commission ID', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Affiliate', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Amount', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Source', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Status', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Earned', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-commission-list">
                    <tr>
                        <td colspan="7"><?php _e( 'No commissions found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Affiliate Payouts Tab -->
        <div id="affiliate-payouts" class="aslp-tab-content" style="display:none;">
            <h2><?php _e( 'Affiliate Payouts', 'as-laburda-pwa-app' ); ?></h2>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Payout ID', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Affiliate', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Amount', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Method', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Status', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Requested', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Completed', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-payout-list">
                    <tr>
                        <td colspan="8"><?php _e( 'No payouts found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Affiliate Creatives Tab -->
        <div id="affiliate-creatives" class="aslp-tab-content" style="display:none;">
            <h2><?php _e( 'Affiliate Creatives', 'as-laburda-pwa-app' ); ?></h2>
            <button id="aslp-add-new-creative" class="button button-primary"><i class="fas fa-plus"></i> <?php _e( 'Add New Creative', 'as-laburda-pwa-app' ); ?></button>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <th><?php _e( 'Creative Name', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Type', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Content/URL', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Active', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Date Created', 'as-laburda-pwa-app' ); ?></th>
                        <th><?php _e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aslp-creative-list">
                    <tr>
                        <td colspan="6"><?php _e( 'No creatives found.', 'as-laburda-pwa-app' ); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="aslp-creative-form-section" style="display: none;">
                <h3><?php _e( 'Creative Details', 'as-laburda-pwa-app' ); ?></h3>
                <form id="aslp-creative-form">
                    <input type="hidden" id="aslp-creative-id" name="creative_id" value="">

                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="creative_name"><?php _e( 'Creative Name', 'as-laburda-pwa-app' ); ?></label></th>
                                <td><input type="text" id="creative_name" name="creative_name" class="regular-text" required></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="creative_type"><?php _e( 'Creative Type', 'as-laburda-pwa-app' ); ?></label></th>
                                <td>
                                    <select id="creative_type" name="creative_type" required>
                                        <option value="text_link"><?php _e( 'Text Link', 'as-laburda-pwa-app' ); ?></option>
                                        <option value="image_banner"><?php _e( 'Image Banner', 'as-laburda-pwa-app' ); ?></option>
                                        <option value="html_code"><?php _e( 'HTML Code', 'as-laburda-pwa-app' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="creative-content-row">
                                <th scope="row"><label for="creative_content"><?php _e( 'Content / URL', 'as-laburda-pwa-app' ); ?></label></th>
                                <td>
                                    <textarea id="creative_content" name="creative_content" rows="5" cols="50" class="large-text" required></textarea>
                                    <p class="description"><?php _e( 'For Text Link: The anchor text. For Image Banner: The image URL. For HTML Code: The full HTML snippet.', 'as-laburda-pwa-app' ); ?></p>
                                    <div class="aslp-image-preview" id="creative_image_preview" style="display: none;">
                                        <img src="" style="max-width: 200px; height: auto;">
                                    </div>
                                    <button type="button" class="button aslp-media-upload-button" id="creative_image_upload_button" style="display: none;"><?php _e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e( 'Is Active', 'as-laburda-pwa-app' ); ?></th>
                                <td>
                                    <label for="creative_is_active">
                                        <input type="checkbox" id="creative_is_active" name="is_active" value="1">
                                        <?php _e( 'Enable this creative', 'as-laburda-pwa-app' ); ?>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="submit">
                        <button type="submit" id="aslp-save-creative" class="button button-primary"><i class="fas fa-save"></i> <?php _e( 'Save Creative', 'as-laburda-pwa-app' ); ?></button>
                        <button type="button" id="aslp-cancel-creative-edit" class="button button-secondary"><?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var affiliatesAdmin = {
        init: function() {
            this.bindTabEvents();
            this.loadOverviewData();
            this.loadAffiliates();
            this.loadTiers();
            this.loadCommissions();
            this.loadPayouts();
            this.loadCreatives();
            this.bindFormEvents();
        },

        bindTabEvents: function() {
            $('.nav-tab-wrapper a').on('click', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.aslp-tab-content').hide();
                $('#affiliates-' + tab).show();
            });
        },

        bindFormEvents: function() {
            // Tier Form
            $('#aslp-add-new-tier').on('click', this.showAddTierForm.bind(this));
            $('#aslp-tier-form').on('submit', this.saveTier.bind(this));
            $('#aslp-cancel-tier-edit').on('click', this.cancelTierEdit.bind(this));
            $('#aslp-tier-list').on('click', '.aslp-edit-tier', this.editTier.bind(this));
            $('#aslp-tier-list').on('click', '.aslp-delete-tier', this.deleteTier.bind(this));
            $('#aslp-affiliate-list').on('change', '.aslp-affiliate-status-select', this.updateAffiliateStatus.bind(this));
            $('#aslp-commission-list').on('change', '.aslp-commission-status-select', this.updateCommissionStatus.bind(this));
            $('#aslp-payout-list').on('click', '.aslp-complete-payout', this.completePayout.bind(this));
            $('#aslp-payout-list').on('click', '.aslp-cancel-payout', this.cancelPayout.bind(this));

            // Creative Form
            $('#aslp-add-new-creative').on('click', this.showAddCreativeForm.bind(this));
            $('#aslp-creative-form').on('submit', this.saveCreative.bind(this));
            $('#aslp-cancel-creative-edit').on('click', this.cancelCreativeEdit.bind(this));
            $('#aslp-creative-list').on('click', '.aslp-edit-creative', this.editCreative.bind(this));
            $('#aslp-creative-list').on('click', '.aslp-delete-creative', this.deleteCreative.bind(this));
            $('#creative_type').on('change', this.toggleCreativeContentInput.bind(this));

            // Media Uploader for creatives
            $('#creative_image_upload_button').on('click', this.openMediaUploader.bind(this));
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

        // --- Overview Functions ---
        loadOverviewData: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_admin_get_affiliates', // Reusing this for overview counts
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                if (response.success) {
                    var affiliates = response.data.affiliates || [];
                    var totalAffiliates = affiliates.length;
                    var pendingAffiliates = affiliates.filter(a => a.affiliate_status === 'pending').length;

                    $('#overview-total-affiliates').text(totalAffiliates);
                    $('#overview-pending-affiliates').text(pendingAffiliates);

                    // Fetch commissions and payouts for totals
                    $.when(
                        $.post(aslp_ajax_object.ajax_url, { action: 'aslp_admin_manage_commissions', nonce: aslp_ajax_object.nonce, sub_action: 'get_all' }),
                        $.post(aslp_ajax_object.ajax_url, { action: 'aslp_admin_manage_payouts', nonce: aslp_ajax_object.nonce, sub_action: 'get_all' })
                    ).done(function(commissionsResponse, payoutsResponse) {
                        var totalCommissions = 0;
                        var unpaidCommissions = 0;
                        if (commissionsResponse[0].success) {
                            $.each(commissionsResponse[0].data.commissions, function(i, comm) {
                                totalCommissions += parseFloat(comm.commission_amount);
                                if (comm.commission_status === 'approved' || comm.commission_status === 'pending') { // Assuming pending also counts towards unpaid until paid out
                                    unpaidCommissions += parseFloat(comm.commission_amount);
                                }
                            });
                        }

                        var totalPayouts = 0;
                        if (payoutsResponse[0].success) {
                            $.each(payoutsResponse[0].data.payouts, function(i, payout) {
                                if (payout.payout_status === 'completed') {
                                    totalPayouts += parseFloat(payout.payout_amount);
                                }
                            });
                        }

                        $('#overview-total-commissions').text(totalCommissions.toFixed(2));
                        $('#overview-unpaid-commissions').text(unpaidCommissions.toFixed(2));
                        $('#overview-total-payouts').text(totalPayouts.toFixed(2));

                        affiliatesAdmin.hideLoading();
                    }).fail(function() {
                        affiliatesAdmin.hideLoading();
                        affiliatesAdmin.showMessage('<?php _e( 'Error loading commission/payout overview data.', 'as-laburda-pwa-app' ); ?>', 'error');
                    });

                } else {
                    affiliatesAdmin.hideLoading();
                    affiliatesAdmin.showMessage('<?php _e( 'Error loading affiliate overview data.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error loading affiliate overview data.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        // --- Affiliates List Functions ---
        loadAffiliates: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_admin_get_affiliates',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                var affiliateList = $('#aslp-affiliate-list');
                affiliateList.empty();

                if (response.success && response.data.affiliates.length > 0) {
                    // Fetch tiers to populate the dropdown
                    $.post(aslp_ajax_object.ajax_url, { action: 'aslp_admin_get_affiliate_tiers', nonce: aslp_ajax_object.nonce }, function(tierResponse) {
                        var tiers = tierResponse.success ? tierResponse.data.tiers : [];
                        var tierOptionsHtml = '<option value="0"><?php _e( 'No Tier', 'as-laburda-pwa-app' ); ?></option>';
                        $.each(tiers, function(i, tier) {
                            tierOptionsHtml += `<option value="${tier.id}">${tier.tier_name}</option>`;
                        });

                        $.each(response.data.affiliates, function(index, affiliate) {
                            var statusOptions = ['pending', 'active', 'rejected'];
                            var statusSelect = `<select class="aslp-affiliate-status-select" data-id="${affiliate.id}">`;
                            $.each(statusOptions, function(i, status) {
                                var selected = status === affiliate.affiliate_status ? 'selected' : '';
                                statusSelect += `<option value="${status}" ${selected}>${status.charAt(0).toUpperCase() + status.slice(1)}</option>`;
                            });
                            statusSelect += `</select>`;

                            var currentTierName = 'N/A';
                            var currentTierSelect = `<select class="aslp-affiliate-tier-select" data-id="${affiliate.id}">`;
                            currentTierSelect += tierOptionsHtml; // Add all tier options
                            if (affiliate.current_tier_id > 0) {
                                var foundTier = tiers.find(t => t.id == affiliate.current_tier_id);
                                if (foundTier) {
                                    currentTierName = foundTier.tier_name;
                                    currentTierSelect = currentTierSelect.replace(`value="${affiliate.current_tier_id}"`, `value="${affiliate.current_tier_id}" selected`);
                                }
                            }
                            currentTierSelect += `</select>`;

                            var row = `
                                <tr>
                                    <td>${affiliate.user_id}</td>
                                    <td><strong>${affiliate.user_display_name || 'N/A'}</strong></td>
                                    <td>${affiliate.affiliate_code}</td>
                                    <td>${statusSelect}</td>
                                    <td>${currentTierSelect}</td>
                                    <td>${affiliate.wallet_balance ? parseFloat(affiliate.wallet_balance).toFixed(2) : '0.00'} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></td>
                                    <td>${affiliate.date_registered}</td>
                                    <td>
                                        <button class="button button-small aslp-view-affiliate-details" data-id="${affiliate.id}"><i class="fas fa-eye"></i> <?php _e( 'View', 'as-laburda-pwa-app' ); ?></button>
                                    </td>
                                </tr>
                            `;
                            affiliateList.append(row);
                        });
                        // Attach change event for tier selection after populating
                        $('#aslp-affiliate-list').on('change', '.aslp-affiliate-tier-select', affiliatesAdmin.updateAffiliateTier.bind(affiliatesAdmin));
                    });
                } else {
                    affiliateList.append('<tr><td colspan="8"><?php _e( 'No affiliates found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error loading affiliates.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        updateAffiliateStatus: function(e) {
            this.clearMessages();
            this.showLoading();
            var affiliate_id = $(e.target).data('id');
            var new_status = $(e.target).val();
            var tier_id = $(e.target).closest('tr').find('.aslp-affiliate-tier-select').val(); // Get selected tier

            var data = {
                'action': 'aslp_admin_update_affiliate_status',
                'nonce': aslp_ajax_object.nonce,
                'affiliate_id': affiliate_id,
                'status': new_status,
                'tier_id': new_status === 'active' ? tier_id : 0 // Only send tier if activating
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                if (response.success) {
                    affiliatesAdmin.showMessage(response.data.message, 'success');
                    affiliatesAdmin.loadOverviewData(); // Update overview counts
                    // No need to reload all affiliates, status is already updated in dropdown
                } else {
                    affiliatesAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error updating affiliate status.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        updateAffiliateTier: function(e) {
            this.clearMessages();
            this.showLoading();
            var affiliate_id = $(e.target).data('id');
            var new_tier_id = $(e.target).val();

            var data = {
                'action': 'aslp_admin_update_affiliate_status', // Reusing the status update endpoint
                'nonce': aslp_ajax_object.nonce,
                'affiliate_id': affiliate_id,
                'status': 'active', // Assume changing tier implies active status
                'tier_id': new_tier_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                if (response.success) {
                    affiliatesAdmin.showMessage(response.data.message, 'success');
                } else {
                    affiliatesAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error updating affiliate tier.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        // --- Tier Functions ---
        loadTiers: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_admin_get_affiliate_tiers',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                var tierList = $('#aslp-tier-list');
                tierList.empty();

                if (response.success && response.data.tiers.length > 0) {
                    $.each(response.data.tiers, function(index, tier) {
                        var isActive = tier.is_active == 1 ? '<?php _e( 'Yes', 'as-laburda-pwa-app' ); ?>' : '<?php _e( 'No', 'as-laburda-pwa-app' ); ?>';
                        var row = `
                            <tr>
                                <td><strong>${tier.tier_name}</strong></td>
                                <td>${tier.description}</td>
                                <td>${parseFloat(tier.base_commission_rate).toFixed(2)}%</td>
                                <td>${parseFloat(tier.mlm_commission_rate).toFixed(2)}%</td>
                                <td>${isActive}</td>
                                <td>${tier.date_created}</td>
                                <td>
                                    <button class="button button-small aslp-edit-tier" data-id="${tier.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-tier" data-id="${tier.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        tierList.append(row);
                    });
                } else {
                    tierList.append('<tr><td colspan="7"><?php _e( 'No affiliate tiers found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error loading affiliate tiers.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        showAddTierForm: function() {
            this.clearMessages();
            $('#aslp-tier-form')[0].reset();
            $('#aslp-tier-id').val('');
            $('.aslp-tier-form-section').show();
            $('html, body').animate({ scrollTop: $('#affiliate-tiers').offset().top }, 'slow');
        },

        editTier: function(e) {
            this.clearMessages();
            var tier_id = $(e.target).data('id');
            this.showLoading();

            var data = {
                'action': 'aslp_admin_get_affiliate_tiers',
                'nonce': aslp_ajax_object.nonce
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                if (response.success && response.data.tiers.length > 0) {
                    var tier = response.data.tiers.find(t => t.id == tier_id);
                    if (tier) {
                        $('#aslp-tier-id').val(tier.id);
                        $('#tier_name').val(tier.tier_name);
                        $('#tier_description').val(tier.description);
                        $('#base_commission_rate').val(parseFloat(tier.base_commission_rate).toFixed(2));
                        $('#mlm_commission_rate').val(parseFloat(tier.mlm_commission_rate).toFixed(2));
                        $('#tier_is_active').prop('checked', tier.is_active == 1);

                        $('.aslp-tier-form-section').show();
                        $('html, body').animate({ scrollTop: $('#affiliate-tiers').offset().top }, 'slow');
                    } else {
                        affiliatesAdmin.showMessage('<?php _e( 'Affiliate tier not found.', 'as-laburda-pwa-app' ); ?>', 'error');
                    }
                } else {
                    affiliatesAdmin.showMessage('<?php _e( 'Error fetching affiliate tier details.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error fetching affiliate tier details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        saveTier: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var tier_id = $('#aslp-tier-id').val();
            var tier_data_to_save = {
                tier_name: $('#tier_name').val(),
                description: $('#tier_description').val(),
                base_commission_rate: parseFloat($('#base_commission_rate').val()),
                mlm_commission_rate: parseFloat($('#mlm_commission_rate').val()),
                is_active: $('#tier_is_active').is(':checked') ? 1 : 0,
            };

            var data = {
                'action': 'aslp_admin_add_update_affiliate_tier',
                'nonce': aslp_ajax_object.nonce,
                'tier_id': tier_id,
                'tier_data': JSON.stringify(tier_data_to_save)
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                if (response.success) {
                    affiliatesAdmin.showMessage(response.data.message, 'success');
                    affiliatesAdmin.loadTiers();
                    affiliatesAdmin.cancelTierEdit();
                } else {
                    affiliatesAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error saving affiliate tier.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deleteTier: function(e) {
            if (!confirm('<?php _e( 'Are you sure you want to delete this affiliate tier? This action cannot be undone and will fail if affiliates are currently assigned to it.', 'as-laburda-pwa-app' ); ?>')) {
                return;
            }

            this.clearMessages();
            this.showLoading();
            var tier_id = $(e.target).data('id');

            var data = {
                'action': 'aslp_admin_delete_affiliate_tier',
                'nonce': aslp_ajax_object.nonce,
                'tier_id': tier_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                if (response.success) {
                    affiliatesAdmin.showMessage(response.data.message, 'success');
                    affiliatesAdmin.loadTiers();
                } else {
                    affiliatesAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error deleting affiliate tier.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        cancelTierEdit: function() {
            this.clearMessages();
            $('.aslp-tier-form-section').hide();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        // --- Commissions Functions ---
        loadCommissions: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_admin_manage_commissions',
                'nonce': aslp_ajax_object.nonce,
                'sub_action': 'get_all'
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                var commissionList = $('#aslp-commission-list');
                commissionList.empty();

                if (response.success && response.data.commissions.length > 0) {
                    $.each(response.data.commissions, function(index, commission) {
                        var statusOptions = ['pending', 'approved', 'rejected'];
                        var statusSelect = `<select class="aslp-commission-status-select" data-id="${commission.id}">`;
                        $.each(statusOptions, function(i, status) {
                            var selected = status === commission.commission_status ? 'selected' : '';
                            statusSelect += `<option value="${status}" ${selected}>${status.charAt(0).toUpperCase() + status.slice(1)}</option>`;
                        });
                        statusSelect += `</select>`;

                        var row = `
                            <tr>
                                <td>${commission.id}</td>
                                <td>User ID: ${commission.affiliate_id}</td>
                                <td>${parseFloat(commission.commission_amount).toFixed(2)} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></td>
                                <td>${commission.source_type} (ID: ${commission.source_id})</td>
                                <td>${statusSelect}</td>
                                <td>${commission.date_earned}</td>
                                <td>
                                    <button class="button button-small aslp-view-commission-details" data-id="${commission.id}"><i class="fas fa-eye"></i> <?php _e( 'View', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        commissionList.append(row);
                    });
                } else {
                    commissionList.append('<tr><td colspan="7"><?php _e( 'No commissions found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error loading commissions.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        updateCommissionStatus: function(e) {
            this.clearMessages();
            this.showLoading();
            var commission_id = $(e.target).data('id');
            var new_status = $(e.target).val();

            var data = {
                'action': 'aslp_admin_manage_commissions',
                'nonce': aslp_ajax_object.nonce,
                'sub_action': 'update_status',
                'commission_id': commission_id,
                'status': new_status
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                if (response.success) {
                    affiliatesAdmin.showMessage(response.data.message, 'success');
                    affiliatesAdmin.loadCommissions(); // Reload to reflect wallet balance changes
                    affiliatesAdmin.loadOverviewData(); // Update overview counts
                } else {
                    affiliatesAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error updating commission status.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        // --- Payouts Functions ---
        loadPayouts: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_admin_manage_payouts',
                'nonce': aslp_ajax_object.nonce,
                'sub_action': 'get_all'
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                var payoutList = $('#aslp-payout-list');
                payoutList.empty();

                if (response.success && response.data.payouts.length > 0) {
                    $.each(response.data.payouts, function(index, payout) {
                        var dateCompleted = payout.date_completed !== '0000-00-00 00:00:00' ? payout.date_completed : 'N/A';
                        var actionsHtml = '';
                        if (payout.payout_status === 'pending') {
                            actionsHtml = `
                                <button class="button button-small aslp-complete-payout" data-id="${payout.id}"><i class="fas fa-check"></i> <?php _e( 'Complete', 'as-laburda-pwa-app' ); ?></button>
                                <button class="button button-small aslp-cancel-payout" data-id="${payout.id}"><i class="fas fa-times"></i> <?php _e( 'Cancel', 'as-laburda-pwa-app' ); ?></button>
                            `;
                        } else {
                            actionsHtml = `<span><?php _e( 'No actions', 'as-laburda-pwa-app' ); ?></span>`;
                        }

                        var row = `
                            <tr>
                                <td>${payout.id}</td>
                                <td>User ID: ${payout.affiliate_id}</td>
                                <td>${parseFloat(payout.payout_amount).toFixed(2)} <?php echo AS_Laburda_PWA_App_Utils::get_currency_symbol('USD'); ?></td>
                                <td>${payout.payout_method}</td>
                                <td>${payout.payout_status}</td>
                                <td>${payout.date_requested}</td>
                                <td>${dateCompleted}</td>
                                <td>${actionsHtml}</td>
                            </tr>
                        `;
                        payoutList.append(row);
                    });
                } else {
                    payoutList.append('<tr><td colspan="8"><?php _e( 'No payouts found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error loading payouts.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        completePayout: function(e) {
            var payout_id = $(e.target).data('id');
            var transaction_id = prompt('<?php _e( 'Enter transaction ID (optional):', 'as-laburda-pwa-app' ); ?>');

            if (transaction_id === null) { // User clicked cancel
                return;
            }

            this.clearMessages();
            this.showLoading();

            var data = {
                'action': 'aslp_admin_manage_payouts',
                'nonce': aslp_ajax_object.nonce,
                'sub_action': 'update_status',
                'payout_id': payout_id,
                'status': 'completed',
                'transaction_id': transaction_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                if (response.success) {
                    affiliatesAdmin.showMessage(response.data.message, 'success');
                    affiliatesAdmin.loadPayouts();
                    affiliatesAdmin.loadOverviewData(); // Update overview counts
                } else {
                    affiliatesAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error completing payout.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        cancelPayout: function(e) {
            if (!confirm('<?php _e( 'Are you sure you want to cancel this payout? Funds will be returned to the affiliate\'s wallet.', 'as-laburda-pwa-app' ); ?>')) {
                return;
            }

            this.clearMessages();
            this.showLoading();
            var payout_id = $(e.target).data('id');

            var data = {
                'action': 'aslp_admin_manage_payouts',
                'nonce': aslp_ajax_object.nonce,
                'sub_action': 'update_status',
                'payout_id': payout_id,
                'status': 'cancelled'
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                if (response.success) {
                    affiliatesAdmin.showMessage(response.data.message, 'success');
                    affiliatesAdmin.loadPayouts();
                    affiliatesAdmin.loadOverviewData(); // Update overview counts
                } else {
                    affiliatesAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error cancelling payout.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        // --- Creatives Functions ---
        loadCreatives: function() {
            this.showLoading();
            var data = {
                'action': 'aslp_affiliate_get_creatives', // Reusing public endpoint for admin view
                'nonce': aslp_ajax_object.nonce,
                'admin_view': true // Indicate admin view to get all creatives
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                var creativeList = $('#aslp-creative-list');
                creativeList.empty();

                if (response.success && response.data.creatives.length > 0) {
                    $.each(response.data.creatives, function(index, creative) {
                        var isActive = creative.is_active == 1 ? '<?php _e( 'Yes', 'as-laburda-pwa-app' ); ?>' : '<?php _e( 'No', 'as-laburda-pwa-app' ); ?>';
                        var contentDisplay = creative.creative_content;
                        if (creative.creative_type === 'image_banner' && creative.creative_content) {
                            contentDisplay = `<img src="${creative.creative_content}" style="max-width: 100px; height: auto;">`;
                        } else if (creative.creative_content.length > 50) {
                            contentDisplay = creative.creative_content.substring(0, 50) + '...';
                        }

                        var row = `
                            <tr>
                                <td><strong>${creative.creative_name}</strong></td>
                                <td>${creative.creative_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
                                <td>${contentDisplay}</td>
                                <td>${isActive}</td>
                                <td>${creative.date_created}</td>
                                <td>
                                    <button class="button button-small aslp-edit-creative" data-id="${creative.id}"><i class="fas fa-edit"></i> <?php _e( 'Edit', 'as-laburda-pwa-app' ); ?></button>
                                    <button class="button button-small aslp-delete-creative" data-id="${creative.id}"><i class="fas fa-trash"></i> <?php _e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                </td>
                            </tr>
                        `;
                        creativeList.append(row);
                    });
                } else {
                    creativeList.append('<tr><td colspan="6"><?php _e( 'No creatives found.', 'as-laburda-pwa-app' ); ?></td></tr>');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error loading creatives.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        showAddCreativeForm: function() {
            this.clearMessages();
            $('#aslp-creative-form')[0].reset();
            $('#aslp-creative-id').val('');
            $('#creative_image_preview img').attr('src', '').hide();
            $('#creative_image_upload_button').hide();
            $('.aslp-creative-form-section').show();
            $('html, body').animate({ scrollTop: $('#affiliate-creatives').offset().top }, 'slow');
        },

        editCreative: function(e) {
            this.clearMessages();
            var creative_id = $(e.target).data('id');
            this.showLoading();

            var data = {
                'action': 'aslp_affiliate_get_creatives',
                'nonce': aslp_ajax_object.nonce,
                'admin_view': true // Indicate admin view to get all creatives
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                if (response.success && response.data.creatives.length > 0) {
                    var creative = response.data.creatives.find(c => c.id == creative_id);
                    if (creative) {
                        $('#aslp-creative-id').val(creative.id);
                        $('#creative_name').val(creative.creative_name);
                        $('#creative_type').val(creative.creative_type);
                        $('#creative_content').val(creative.creative_content);
                        $('#creative_is_active').prop('checked', creative.is_active == 1);

                        affiliatesAdmin.toggleCreativeContentInput(); // Adjust input based on type
                        if (creative.creative_type === 'image_banner' && creative.creative_content) {
                            $('#creative_image_preview img').attr('src', creative.creative_content).show();
                        }

                        $('.aslp-creative-form-section').show();
                        $('html, body').animate({ scrollTop: $('#affiliate-creatives').offset().top }, 'slow');
                    } else {
                        affiliatesAdmin.showMessage('<?php _e( 'Creative not found.', 'as-laburda-pwa-app' ); ?>', 'error');
                    }
                } else {
                    affiliatesAdmin.showMessage('<?php _e( 'Error fetching creative details.', 'as-laburda-pwa-app' ); ?>', 'error');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error fetching creative details.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        saveCreative: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();

            var creative_id = $('#aslp-creative-id').val();
            var creative_data_to_save = {
                creative_name: $('#creative_name').val(),
                creative_type: $('#creative_type').val(),
                creative_content: $('#creative_content').val(),
                is_active: $('#creative_is_active').is(':checked') ? 1 : 0,
            };

            var data = {
                'action': 'aslp_affiliate_add_update_creative', // New AJAX action for creatives
                'nonce': aslp_ajax_object.nonce,
                'creative_id': creative_id,
                'creative_data': JSON.stringify(creative_data_to_save)
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                if (response.success) {
                    affiliatesAdmin.showMessage(response.data.message, 'success');
                    affiliatesAdmin.loadCreatives();
                    affiliatesAdmin.cancelCreativeEdit();
                } else {
                    affiliatesAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error saving creative.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        deleteCreative: function(e) {
            if (!confirm('<?php _e( 'Are you sure you want to delete this creative? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                return;
            }

            this.clearMessages();
            this.showLoading();
            var creative_id = $(e.target).data('id');

            var data = {
                'action': 'aslp_affiliate_delete_creative', // New AJAX action for creatives
                'nonce': aslp_ajax_object.nonce,
                'creative_id': creative_id
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                affiliatesAdmin.hideLoading();
                if (response.success) {
                    affiliatesAdmin.showMessage(response.data.message, 'success');
                    affiliatesAdmin.loadCreatives();
                } else {
                    affiliatesAdmin.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                affiliatesAdmin.hideLoading();
                affiliatesAdmin.showMessage('<?php _e( 'Error deleting creative.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        cancelCreativeEdit: function() {
            this.clearMessages();
            $('.aslp-creative-form-section').hide();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        },

        toggleCreativeContentInput: function() {
            var creativeType = $('#creative_type').val();
            var contentInput = $('#creative_content');
            var imagePreview = $('#creative_image_preview');
            var uploadButton = $('#creative_image_upload_button');

            imagePreview.hide();
            uploadButton.hide();
            contentInput.attr('type', 'text').attr('rows', '5'); // Reset to textarea default

            if (creativeType === 'image_banner') {
                contentInput.attr('type', 'url');
                uploadButton.show();
                if (contentInput.val()) {
                    imagePreview.find('img').attr('src', contentInput.val()).show();
                }
            } else if (creativeType === 'text_link') {
                contentInput.attr('type', 'text');
            } else if (creativeType === 'html_code') {
                contentInput.attr('type', 'text'); // Still a textarea
            }
        },

        openMediaUploader: function(e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            var input = $('#creative_content'); // Always target the creative_content input
            var preview = $('#creative_image_preview img');

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

    affiliatesAdmin.init();
});
</script>
