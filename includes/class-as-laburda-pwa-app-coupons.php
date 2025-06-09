<?php
/**
 * Funtionality related to coupon management.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The coupon management functionality of the plugin.
 *
 * This class handles the creation, display, and usage tracking of coupons for business listings.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Coupons {

    private $database;
    private $main_plugin;

    public function __construct( $main_plugin ) {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-database.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-utils.php';
        $this->database = new AS_Laburda_PWA_App_Database();
        $this->main_plugin = $main_plugin;
    }

    /**
     * Get coupons for a specific business listing.
     *
     * @since 1.0.0
     * @param int $business_listing_id The ID of the business listing.
     * @param bool $active_only Optional. If true, only return active and unexpired coupons.
     * @return array Array of coupon objects.
     */
    public function get_business_coupons( $business_listing_id, $active_only = true ) {
        return $this->database->get_coupons_for_business( $business_listing_id, $active_only );
    }

    /**
     * Add a new coupon.
     *
     * @since 1.0.0
     * @param int $business_listing_id The ID of the business listing.
     * @param array $data Coupon data (coupon_code, coupon_title, etc.).
     * @return int|bool Insert ID on success, false on failure.
     */
    public function add_new_coupon( $business_listing_id, $data ) {
        if ( ! current_user_can( 'aslp_manage_coupons' ) ) {
            return false; // Permission check
        }

        // Basic validation for required fields
        if ( empty( $data['coupon_code'] ) || empty( $data['coupon_title'] ) || empty( $data['discount_type'] ) || ! isset( $data['coupon_amount'] ) ) {
            return false;
        }

        $data['business_listing_id'] = $business_listing_id;
        $data['coupon_code'] = sanitize_text_field( $data['coupon_code'] );
        $data['coupon_title'] = sanitize_text_field( $data['coupon_title'] );
        $data['coupon_description'] = sanitize_textarea_field( $data['coupon_description'] ?? '' );
        $data['discount_type'] = sanitize_text_field( $data['discount_type'] );
        $data['coupon_amount'] = floatval( $data['coupon_amount'] );
        $data['expiry_date'] = ! empty( $data['expiry_date'] ) ? sanitize_text_field( $data['expiry_date'] ) : null;
        $data['usage_limit'] = absint( $data['usage_limit'] ?? 0 );
        $data['is_active'] = isset( $data['is_active'] ) ? (bool) $data['is_active'] : true;

        return $this->database->add_coupon( $data );
    }

    /**
     * Update an existing coupon.
     *
     * @since 1.0.0
     * @param int $coupon_id The ID of the coupon to update.
     * @param array $data Coupon data to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_existing_coupon( $coupon_id, $data ) {
        if ( ! current_user_can( 'aslp_manage_coupons' ) ) {
            return false; // Permission check
        }

        $coupon = $this->database->get_coupon( $coupon_id );
        if ( ! $coupon || $coupon->business_listing_id != get_current_user_id() ) { // Assuming coupon is tied to business owner's user_id
            return false; // User does not own this coupon or coupon not found
        }

        $data['coupon_code'] = sanitize_text_field( $data['coupon_code'] );
        $data['coupon_title'] = sanitize_text_field( $data['coupon_title'] );
        $data['coupon_description'] = sanitize_textarea_field( $data['coupon_description'] ?? '' );
        $data['discount_type'] = sanitize_text_field( $data['discount_type'] );
        $data['coupon_amount'] = floatval( $data['coupon_amount'] );
        $data['expiry_date'] = ! empty( $data['expiry_date'] ) ? sanitize_text_field( $data['expiry_date'] ) : null;
        $data['usage_limit'] = absint( $data['usage_limit'] ?? 0 );
        $data['is_active'] = isset( $data['is_active'] ) ? (bool) $data['is_active'] : true;

        return $this->database->update_coupon( $coupon_id, $data );
    }

    /**
     * Delete a coupon.
     *
     * @since 1.0.0
     * @param int $coupon_id The ID of the coupon to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_coupon( $coupon_id ) {
        if ( ! current_user_can( 'aslp_manage_coupons' ) ) {
            return false; // Permission check
        }

        $coupon = $this->database->get_coupon( $coupon_id );
        if ( ! $coupon || $coupon->business_listing_id != get_current_user_id() ) { // Assuming coupon is tied to business owner's user_id
            return false; // User does not own this coupon or coupon not found
        }

        return $this->database->delete_coupon( $coupon_id );
    }

    /**
     * Increment the used count for a coupon.
     * This would typically be called when a coupon is successfully applied in a transaction.
     *
     * @since 1.0.0
     * @param int $coupon_id The ID of the coupon.
     * @return bool True on success, false on failure.
     */
    public function increment_coupon_usage( $coupon_id ) {
        $coupon = $this->database->get_coupon( $coupon_id );
        if ( $coupon ) {
            $new_used_count = $coupon->used_count + 1;
            return $this->database->update_coupon( $coupon_id, array( 'used_count' => $new_used_count ) );
        }
        return false;
    }
}

