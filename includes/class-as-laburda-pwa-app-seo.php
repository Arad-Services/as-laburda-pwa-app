<?php
/**
 * Functionality related to SEO management.
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The SEO management functionality of the plugin.
 *
 * This class handles SEO optimization for business listings and PWA apps.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_SEO {

    private $database;
    private $main_plugin;

    public function __construct( $main_plugin ) {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-database.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-utils.php';
        $this->database = new AS_Laburda_PWA_App_Database();
        $this->main_plugin = $main_plugin;
    }

    /**
     * Update SEO data for a business listing.
     *
     * @since 2.0.0
     * @param int $listing_id The ID of the business listing.
     * @param array $seo_data Associative array with 'seo_title', 'seo_description', 'seo_keywords'.
     * @return bool True on success, false on failure.
     */
    public function update_listing_seo( $listing_id, $seo_data ) {
        // Check if global feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_seo_features'] ?? false ) ) {
            return false; // Feature not enabled by admin
        }

        $listing = $this->database->get_business_listing( $listing_id );
        if ( ! $listing ) {
            return false;
        }

        // Check if the listing's plan allows SEO features
        $effective_features = $this->main_plugin->get_listing_plans_manager()->get_listing_effective_features( $listing_id );
        if ( ! ( $effective_features['enable_seo_basic'] ?? false ) ) {
            return false; // Plan does not allow SEO features
        }

        // Sanitize data
        $data = array(
            'seo_title'       => sanitize_text_field( $seo_data['seo_title'] ?? '' ),
            'seo_description' => sanitize_textarea_field( $seo_data['seo_description'] ?? '' ),
            'seo_keywords'    => sanitize_text_field( $seo_data['seo_keywords'] ?? '' ),
        );

        return $this->database->update_business_listing( $listing_id, $data );
    }

    /**
     * Update SEO data for a PWA app.
     *
     * @since 2.0.0
     * @param string $app_id The ID of the PWA app.
     * @param array $seo_data Associative array with 'seo_title', 'seo_description', 'seo_keywords'.
     * @return bool True on success, false on failure.
     */
    public function update_app_seo( $app_id, $seo_data ) {
        // Check if global feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_seo_features'] ?? false ) ) {
            return false; // Feature not enabled by admin
        }

        $app = $this->database->get_app( $app_id );
        if ( ! $app ) {
            return false;
        }

        // Check if the app's plan allows SEO features
        $effective_features = $this->main_plugin->get_listing_plans_manager()->get_app_effective_features( $app_id );
        if ( ! ( $effective_features['enable_app_seo'] ?? false ) ) {
            return false; // Plan does not allow app SEO features
        }

        // Sanitize data
        $data = array(
            'seo_title'       => sanitize_text_field( $seo_data['seo_title'] ?? '' ),
            'seo_description' => sanitize_textarea_field( $seo_data['seo_description'] ?? '' ),
            'seo_keywords'    => sanitize_text_field( $seo_data['seo_keywords'] ?? '' ),
        );

        return $this->database->update_app( $app_id, $data );
    }

    /**
     * Get SEO data for a business listing.
     *
     * @since 2.0.0
     * @param int $listing_id The ID of the business listing.
     * @return object|null SEO data object (containing seo_title, seo_description, seo_keywords).
     */
    public function get_listing_seo( $listing_id ) {
        $listing = $this->database->get_business_listing( $listing_id );
        if ( $listing ) {
            return (object) array(
                'seo_title'       => $listing->seo_title,
                'seo_description' => $listing->seo_description,
                'seo_keywords'    => $listing->seo_keywords,
            );
        }
        return null;
    }

    /**
     * Get SEO data for a PWA app.
     *
     * @since 2.0.0
     * @param string $app_id The ID of the PWA app.
     * @return object|null SEO data object (containing seo_title, seo_description, seo_keywords).
     */
    public function get_app_seo( $app_id ) {
        $app = $this->database->get_app( $app_id );
        if ( $app ) {
            return (object) array(
                'seo_title'       => $app->seo_title,
                'seo_description' => $app->seo_description,
                'seo_keywords'    => $app->seo_keywords,
            );
        }
        return null;
    }

    // You might add AJAX handlers here for front-end SEO updates if business owners
    // are allowed to directly edit their SEO from a dashboard.
    // For now, the AI agent will handle generation, and admin will manage.
}
