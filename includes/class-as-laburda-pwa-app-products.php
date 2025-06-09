<?php
/**
 * Functionality related to product management.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The product management functionality of the plugin.
 *
 * This class handles the creation, display, and management of products for business listings.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Products {

    /**
     * The database object.
     *
     * @since    1.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_Database    $database    The database manager instance.
     */
    private $database;

    /**
     * The main plugin instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App    $main_plugin    The main plugin instance.
     */
    private $main_plugin;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    AS_Laburda_PWA_App $main_plugin The main plugin instance.
     */
    public function __construct( $main_plugin ) {
        $this->main_plugin = $main_plugin;
        $this->database = $main_plugin->get_database_manager();
    }

    /**
     * Get products for a specific business listing.
     *
     * @since 1.0.0
     * @param int $business_listing_id Business Listing ID.
     * @param bool $active_only Optional. If true, only return active products.
     * @return array Array of product objects.
     */
    public function get_products( $business_listing_id, $active_only = false ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_products'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }
        return $this->database->get_products_for_business( $business_listing_id, $active_only );
    }

    /**
     * Get a single product by ID.
     *
     * @since 1.0.0
     * @param int $product_id Product ID.
     * @return object|null Product object on success, null if not found.
     */
    public function get_single_product( $product_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_products'] ?? false ) ) {
            return null; // Feature not enabled by admin
        }
        return $this->database->get_product( $product_id );
    }

    /**
     * Add a new product.
     *
     * @since 1.0.0
     * @param array $data Associative array of product properties.
     * @return int|false The ID of the new product on success, false on failure.
     */
    public function add_product( $data ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_products'] ?? false ) ) {
            error_log( 'ASLP: Products feature is disabled. Product not added.' );
            return false; // Feature not enabled by admin
        }
        return $this->database->add_product( $data );
    }

    /**
     * Update an existing product.
     *
     * @since 1.0.0
     * @param int $product_id The ID of the product to update.
     * @param array $data Associative array of product properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_product( $product_id, $data ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_products'] ?? false ) ) {
            error_log( 'ASLP: Products feature is disabled. Product not updated.' );
            return false; // Feature not enabled by admin
        }
        return $this->database->update_product( $product_id, $data );
    }

    /**
     * Delete a product.
     *
     * @since 1.0.0
     * @param int $product_id The ID of the product to delete.
     * @param int $user_id Optional. User ID for permission check (if not admin).
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_product( $product_id, $user_id = 0 ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_products'] ?? false ) ) {
            error_log( 'ASLP: Products feature is disabled. Product not deleted.' );
            return false; // Feature not enabled by admin
        }

        $product = $this->database->get_product( $product_id );
        if ( ! $product ) {
            error_log( 'ASLP: Product ' . $product_id . ' not found for deletion.' );
            return false;
        }

        // Verify ownership of the associated business listing
        $listing = $this->main_plugin->get_database_manager()->get_business_listing( $product->business_listing_id );
        if ( ! $listing || ( $user_id && $listing->user_id != $user_id && ! current_user_can( 'aslp_manage_all_business_listings' ) ) ) {
            error_log( 'ASLP: User ' . $user_id . ' does not have permission to delete product ' . $product_id . '.' );
            return false;
        }

        return $this->database->delete_product( $product_id );
    }
}
