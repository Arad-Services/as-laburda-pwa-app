<?php
/**
 * Functionality related to Affiliate Program management.
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The Affiliate Program management functionality of the plugin.
 *
 * This class handles affiliate registration, commission tracking, payouts,
 * and management of affiliate creatives.
 *
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Affiliates {

    /**
     * The database object.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_Database    $database    The database manager instance.
     */
    private $database;

    /**
     * The main plugin instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App    $main_plugin    The main plugin instance.
     */
    private $main_plugin;

    /**
     * Initialize the class and set its properties.
     *
     * @since    2.0.0
     * @param    AS_Laburda_PWA_App $main_plugin The main plugin instance.
     */
    public function __construct( $main_plugin ) {
        $this->main_plugin = $main_plugin;
        $this->database = $main_plugin->get_database_manager();
    }

    /**
     * Register a user as an affiliate.
     *
     * @since 2.0.0
     * @param int $user_id The ID of the user to register.
     * @param string $payment_email The PayPal email or other payment identifier.
     * @return bool True on success, false on failure.
     */
    public function register_affiliate( $user_id, $payment_email ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_affiliates'] ?? false ) ) {
            error_log( 'ASLP: Affiliate program feature is disabled.' );
            return false; // Feature not enabled by admin
        }

        // Check if user is already an affiliate
        if ( $this->database->get_affiliate_by_user_id( $user_id ) ) {
            error_log( 'ASLP: User ' . $user_id . ' is already an affiliate.' );
            return false;
        }

        // Generate a unique affiliate code
        $affiliate_code = AS_Laburda_PWA_App_Utils::generate_unique_code( 'aslp_affiliates', 'affiliate_code', 10 );

        // Get the default tier (Tier 1)
        $default_tier = $this->database->get_affiliate_tier_by_name( 'Tier 1' );
        $tier_id = $default_tier ? $default_tier->id : 0;

        $data = array(
            'user_id'          => absint( $user_id ),
            'affiliate_code'   => sanitize_text_field( $affiliate_code ),
            'affiliate_status' => 'pending', // Requires admin approval
            'payment_email'    => sanitize_email( $payment_email ),
            'current_tier_id'  => absint( $tier_id ),
        );

        $inserted_id = $this->database->add_affiliate( $data );

        if ( $inserted_id ) {
            // Add 'app_user' role if not already present, or 'affiliate' role if you define one.
            $user = new WP_User( $user_id );
            if ( ! $user->has_cap( 'aslp_view_affiliate_dashboard' ) ) {
                $user->add_cap( 'aslp_view_affiliate_dashboard' );
                $user->add_cap( 'aslp_request_payout' );
            }
            return true;
        }
        return false;
    }

    /**
     * Get affiliate details by user ID.
     *
     * @since 2.0.0
     * @param int $user_id The ID of the user.
     * @return object|null Affiliate object on success, null if not found.
     */
    public function get_affiliate_by_user_id( $user_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_affiliates'] ?? false ) ) {
            return null; // Feature not enabled by admin
        }
        return $this->database->get_affiliate_by_user_id( $user_id );
    }

    /**
     * Track an affiliate click.
     *
     * @since 2.0.0
     * @param int $affiliate_id The ID of the affiliate.
     * @param string $referral_url The URL that was clicked.
     * @param string $ip_address The IP address of the user who clicked.
     * @param string $user_agent The user agent string of the user who clicked.
     * @return bool True on success, false on failure.
     */
    public function track_affiliate_click( $affiliate_id, $referral_url, $ip_address, $user_agent ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_affiliates'] ?? false ) ) {
            return false; // Feature not enabled by admin
        }
        if ( ! ( $global_features['enable_analytics'] ?? false ) ) {
            return false; // Analytics must be enabled for click tracking
        }

        $data = array(
            'item_id'      => absint( $affiliate_id ), // Store affiliate_id here
            'item_type'    => 'affiliate',
            'click_target' => 'referral_link',
            'ip_address'   => sanitize_text_field( $ip_address ),
            'user_id'      => get_current_user_id() ? get_current_user_id() : null, // Log user if logged in
            'notes'        => sprintf( 'Referral URL: %s | User Agent: %s', esc_url_raw( $referral_url ), sanitize_text_field( $user_agent ) ),
        );

        return $this->database->add_analytics_click( $data );
    }

    /**
     * Record a commission for an affiliate.
     * This would typically be called when a referred user signs up, makes a purchase, etc.
     *
     * @since 2.0.0
     * @param int $affiliate_id The ID of the affiliate earning the commission.
     * @param int $referred_user_id The ID of the user who was referred.
     * @param string $referral_type Type of referral (e.g., 'new_signup', 'listing_purchase', 'app_plan_purchase').
     * @param float $referral_amount The base amount on which commission is calculated.
     * @param int $parent_affiliate_id Optional. For MLM, the ID of the parent affiliate.
     * @return bool True on success, false on failure.
     */
    public function record_commission( $affiliate_id, $referred_user_id, $referral_type, $referral_amount, $parent_affiliate_id = 0 ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_affiliates'] ?? false ) ) {
            return false; // Feature not enabled by admin
        }

        $affiliate = $this->database->get_affiliate( $affiliate_id );
        if ( ! $affiliate || $affiliate->affiliate_status !== 'active' ) {
            error_log( 'ASLP: Affiliate ' . $affiliate_id . ' not found or not active for commission recording.' );
            return false;
        }

        $tier = $this->database->get_affiliate_tier( $affiliate->current_tier_id );
        $base_commission_rate = $tier ? $tier->base_commission_rate : 0;
        $mlm_commission_rate = $tier ? $tier->mlm_commission_rate : 0;

        $commission_amount = ( $referral_amount * $base_commission_rate ) / 100;

        $data = array(
            'affiliate_id'     => absint( $affiliate_id ),
            'referred_user_id' => absint( $referred_user_id ),
            'referral_type'    => sanitize_text_field( $referral_type ),
            'referral_amount'  => floatval( $referral_amount ),
            'commission_amount' => floatval( $commission_amount ),
            'commission_status' => 'pending', // Awaiting admin approval
        );

        $inserted = $this->database->add_affiliate_commission( $data );

        // Handle MLM commission if applicable
        if ( $inserted && $parent_affiliate_id > 0 && $mlm_commission_rate > 0 ) {
            $parent_affiliate = $this->database->get_affiliate( $parent_affiliate_id );
            if ( $parent_affiliate && $parent_affiliate->affiliate_status === 'active' ) {
                $mlm_commission_amount = ( $referral_amount * $mlm_commission_rate ) / 100;
                $mlm_data = array(
                    'affiliate_id'     => absint( $parent_affiliate_id ),
                    'referred_user_id' => absint( $referred_user_id ),
                    'referral_type'    => 'mlm_commission',
                    'referral_amount'  => floatval( $referral_amount ),
                    'commission_amount' => floatval( $mlm_commission_amount ),
                    'commission_status' => 'pending',
                    'notes'            => sprintf( 'MLM commission from affiliate %d', $affiliate_id ),
                );
                $this->database->add_affiliate_commission( $mlm_data );
            }
        }

        return $inserted;
    }

    /**
     * Request a payout for an affiliate.
     *
     * @since 2.0.0
     * @param int $affiliate_id The ID of the affiliate requesting payout.
     * @param float $amount The amount to request.
     * @param string $payout_method The preferred payout method.
     * @return bool True on success, false on failure.
     */
    public function request_payout( $affiliate_id, $amount, $payout_method ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_affiliates'] ?? false ) ) {
            return false; // Feature not enabled by admin
        }

        $affiliate = $this->database->get_affiliate( $affiliate_id );
        if ( ! $affiliate || $affiliate->affiliate_status !== 'active' ) {
            error_log( 'ASLP: Affiliate ' . $affiliate_id . ' not found or not active for payout request.' );
            return false;
        }

        $current_balance = $this->database->get_affiliate_wallet_balance( $affiliate_id );
        if ( $current_balance < $amount ) {
            error_log( 'ASLP: Affiliate ' . $affiliate_id . ' has insufficient balance for payout request.' );
            return false;
        }

        $data = array(
            'affiliate_id'   => absint( $affiliate_id ),
            'payout_amount'  => floatval( $amount ),
            'payout_method'  => sanitize_text_field( $payout_method ),
            'payout_status'  => 'pending',
        );

        $inserted = $this->database->add_affiliate_payout( $data );

        if ( $inserted ) {
            // Deduct from wallet balance immediately upon request
            $this->database->update_affiliate_wallet_balance( $affiliate_id, $amount, 'subtract' );
            return true;
        }
        return false;
    }

    /**
     * Get affiliate dashboard data.
     *
     * @since 2.0.0
     * @param int $affiliate_id The ID of the affiliate.
     * @return array Associative array of dashboard data.
     */
    public function get_affiliate_dashboard_data( $affiliate_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_affiliates'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }

        $affiliate = $this->database->get_affiliate( $affiliate_id );
        if ( ! $affiliate ) {
            return array();
        }

        $tier = $this->database->get_affiliate_tier( $affiliate->current_tier_id );

        $total_earnings = $this->database->get_affiliate_total_earnings( $affiliate_id, 'approved' );
        $unpaid_earnings = $this->database->get_affiliate_wallet_balance( $affiliate_id );
        $total_referrals = $this->database->get_affiliate_referral_count( $affiliate_id );
        $recent_commissions = $this->database->get_affiliate_commissions( $affiliate_id, 5 ); // Last 5 commissions
        $recent_payouts = $this->database->get_affiliate_payouts( $affiliate_id, 5 ); // Last 5 payouts
        $paid_payouts = $this->database->get_affiliate_total_paid_payouts( $affiliate_id );
        $pending_payouts = $this->database->get_affiliate_total_pending_payouts( $affiliate_id );
        $creatives = $this->database->get_affiliate_creatives( $affiliate->current_tier_id, true ); // Active creatives for their tier

        return array(
            'affiliate_info'     => $affiliate,
            'tier_info'          => $tier,
            'total_earnings'     => $total_earnings,
            'unpaid_earnings'    => $unpaid_earnings,
            'total_referrals'    => $total_referrals,
            'recent_commissions' => $recent_commissions,
            'recent_payouts'     => $recent_payouts,
            'paid_payouts'       => $paid_payouts,
            'pending_payouts'    => $pending_payouts,
            'creatives'          => $creatives,
        );
    }

    /**
     * Get affiliate wallet balance.
     *
     * @since 2.0.0
     * @param int $affiliate_id The ID of the affiliate.
     * @return float The current wallet balance.
     */
    public function get_affiliate_wallet_balance( $affiliate_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_affiliates'] ?? false ) ) {
            return 0.00; // Feature not enabled by admin
        }
        return $this->database->get_affiliate_wallet_balance( $affiliate_id );
    }

    /**
     * Get affiliate creatives for a specific tier.
     *
     * @since 2.0.0
     * @param int $tier_id The ID of the affiliate tier.
     * @param bool $active_only Optional. If true, only return active creatives.
     * @return array Array of creative objects.
     */
    public function get_affiliate_creatives( $tier_id, $active_only = true ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_affiliates'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }
        return $this->database->get_affiliate_creatives( $tier_id, $active_only );
    }

    /**
     * Manually assign an affiliate to a tier.
     *
     * @since 2.0.0
     * @param int $affiliate_id The ID of the affiliate.
     * @param int $tier_id The ID of the tier to assign.
     * @return bool True on success, false on failure.
     */
    public function assign_affiliate_to_tier( $affiliate_id, $tier_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_affiliates'] ?? false ) ) {
            return false; // Feature not enabled by admin
        }

        $affiliate = $this->database->get_affiliate( $affiliate_id );
        $tier = $this->database->get_affiliate_tier( $tier_id );

        if ( ! $affiliate || ! $tier ) {
            error_log( 'ASLP: Affiliate or tier not found for assignment.' );
            return false;
        }

        $data = array( 'current_tier_id' => absint( $tier_id ) );
        return $this->database->update_affiliate( $affiliate_id, $data );
    }

    /**
     * Get a list of all affiliate tiers.
     *
     * @since 2.0.0
     * @param bool $active_only Optional. If true, only return active tiers.
     * @return array Array of tier objects.
     */
    public function get_all_affiliate_tiers( $active_only = false ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_affiliates'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }
        return $this->database->get_all_affiliate_tiers( $active_only );
    }
}
