<?php
/**
 * Functionality related to app template management.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The app template management functionality of the plugin.
 *
 * This class handles the creation, display, and management of PWA app templates.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Templates {

    private $database;
    private $main_plugin;

    public function __construct( $main_plugin ) {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-database.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-utils.php';
        $this->database = new AS_Laburda_PWA_App_Database();
        $this->main_plugin = $main_plugin;
    }

    /**
     * Get app templates.
     *
     * @since 1.0.0
     * @param bool $active_only Optional. If true, only return active templates.
     * @return array Array of template objects.
     */
    public function get_app_templates( $active_only = true ) {
        $templates = $this->database->get_all_app_templates( $active_only );
        foreach ( $templates as $template ) {
            if ( ! empty( $template->template_data ) ) {
                $template->template_data = AS_Laburda_PWA_App_Utils::safe_json_decode( $template->template_data, true );
            }
        }
        return $templates;
    }

    /**
     * Get a single app template by ID.
     *
     * @since 1.0.0
     * @param int $template_id The ID of the template.
     * @return object|null Template object on success, null if not found.
     */
    public function get_template_by_id( $template_id ) {
        $template = $this->database->get_app_template( $template_id );
        if ( $template && ! empty( $template->template_data ) ) {
            $template->template_data = AS_Laburda_PWA_App_Utils::safe_json_decode( $template->template_data, true );
        }
        return $template;
    }

    /**
     * Add a new app template.
     *
     * @since 1.0.0
     * @param array $data Template data (template_name, description, etc.).
     * @return int|bool Insert ID on success, false on failure.
     */
    public function add_new_template( $data ) {
        // Check if global feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_templates'] ?? false ) ) {
            return false; // Feature not enabled by admin
        }

        if ( ! current_user_can( 'manage_options' ) ) { // Only admin can add templates
            return false;
        }

        // Basic validation for required fields
        if ( empty( $data['template_name'] ) ) {
            return false;
        }

        $data['template_name'] = sanitize_text_field( $data['template_name'] );
        $data['description'] = sanitize_textarea_field( $data['description'] ?? '' );
        $data['preview_image'] = esc_url_raw( $data['preview_image'] ?? '' );
        $data['template_data'] = AS_Laburda_PWA_App_Utils::safe_json_encode( $data['template_data'] ?? array() );
        $data['is_active'] = isset( $data['is_active'] ) ? (bool) $data['is_active'] : true;

        return $this->database->add_app_template( $data );
    }

    /**
     * Update an existing app template.
     *
     * @since 1.0.0
     * @param int $template_id The ID of the template to update.
     * @param array $data Template data to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_existing_template( $template_id, $data ) {
        // Check if global feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_templates'] ?? false ) ) {
            return false; // Feature not enabled by admin
        }

        if ( ! current_user_can( 'manage_options' ) ) { // Only admin can update templates
            return false;
        }

        $template = $this->database->get_app_template( $template_id );
        if ( ! $template ) {
            return false; // Template not found
        }

        $data['template_name'] = sanitize_text_field( $data['template_name'] ?? $template->template_name );
        $data['description'] = sanitize_textarea_field( $data['description'] ?? $template->description );
        $data['preview_image'] = esc_url_raw( $data['preview_image'] ?? $template->preview_image );
        $data['template_data'] = AS_Laburda_PWA_App_Utils::safe_json_encode( $data['template_data'] ?? AS_Laburda_PWA_App_Utils::safe_json_decode($template->template_data, true) );
        $data['is_active'] = isset( $data['is_active'] ) ? (bool) $data['is_active'] : (bool) $template->is_active;

        return $this->database->update_app_template( $template_id, $data );
    }

    /**
     * Delete an app template.
     *
     * @since 1.0.0
     * @param int $template_id The ID of the template to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_template( $template_id ) {
        // Check if global feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_templates'] ?? false ) ) {
            return false; // Feature not enabled by admin
        }

        if ( ! current_user_can( 'manage_options' ) ) { // Only admin can delete templates
            return false;
        }

        $template = $this->database->get_app_template( $template_id );
        if ( ! $template ) {
            return false; // Template not found
        }

        return $this->database->delete_app_template( $template_id );
    }
}