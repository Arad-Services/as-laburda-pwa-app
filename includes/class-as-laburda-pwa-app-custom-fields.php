<?php
/**
 * Functionality related to Custom Fields management.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The Custom Fields management functionality of the plugin.
 *
 * This class handles the creation, display, and management of custom fields
 * for various plugin entities (e.g., business listings, products, events, apps).
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Custom_Fields {

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
     * Get a custom field by its ID.
     *
     * @since 1.0.0
     * @param int $field_id Field ID.
     * @return object|null Field object on success, null if not found.
     */
    public function get_custom_field( $field_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_custom_fields'] ?? false ) ) {
            return null; // Feature not enabled by admin
        }
        return $this->database->get_custom_field( $field_id );
    }

    /**
     * Get all custom fields.
     *
     * @since 1.0.0
     * @param array $args Optional. Query arguments (e.g., 'applies_to', 'is_active').
     * @return array Array of field objects.
     */
    public function get_all_custom_fields( $args = array() ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_custom_fields'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }
        return $this->database->get_all_custom_fields( $args );
    }

    /**
     * Add a new custom field.
     *
     * @since 1.0.0
     * @param array $data Associative array of field properties.
     * @return int|false The ID of the new field on success, false on failure.
     */
    public function add_custom_field( $data ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_custom_fields'] ?? false ) ) {
            error_log( 'ASLP: Custom Fields feature is disabled. Field not added.' );
            return false; // Feature not enabled by admin
        }

        $sanitized_data = array(
            'field_name'    => sanitize_text_field( $data['field_name'] ?? '' ),
            'field_slug'    => sanitize_title( $data['field_slug'] ?? $data['field_name'] ?? '' ),
            'field_type'    => sanitize_text_field( $data['field_type'] ?? '' ),
            'field_options' => AS_Laburda_PWA_App_Utils::safe_json_encode( $data['field_options'] ?? array() ),
            'applies_to'    => sanitize_text_field( $data['applies_to'] ?? '' ),
            'is_required'   => isset( $data['is_required'] ) ? (bool) $data['is_required'] : false,
            'is_active'     => isset( $data['is_active'] ) ? (bool) $data['is_active'] : false,
        );

        if ( empty( $sanitized_data['field_name'] ) || empty( $sanitized_data['field_type'] ) || empty( $sanitized_data['applies_to'] ) ) {
            error_log( 'ASLP: Missing required custom field data (name, type, or applies_to).' );
            return false;
        }

        return $this->database->add_custom_field( $sanitized_data );
    }

    /**
     * Update an existing custom field.
     *
     * @since 1.0.0
     * @param int $field_id The ID of the field to update.
     * @param array $data Associative array of field properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_custom_field( $field_id, $data ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_custom_fields'] ?? false ) ) {
            error_log( 'ASLP: Custom Fields feature is disabled. Field not updated.' );
            return false; // Feature not enabled by admin
        }

        $field = $this->database->get_custom_field( $field_id );
        if ( ! $field ) {
            error_log( 'ASLP: Custom field ' . $field_id . ' not found for update.' );
            return false;
        }

        $sanitized_data = array(
            'field_name'    => sanitize_text_field( $data['field_name'] ?? $field->field_name ),
            'field_slug'    => sanitize_title( $data['field_slug'] ?? $field->field_slug ),
            'field_type'    => sanitize_text_field( $data['field_type'] ?? $field->field_type ),
            'field_options' => AS_Laburda_PWA_App_Utils::safe_json_encode( $data['field_options'] ?? AS_Laburda_PWA_App_Utils::safe_json_decode($field->field_options, true) ),
            'applies_to'    => sanitize_text_field( $data['applies_to'] ?? $field->applies_to ),
            'is_required'   => isset( $data['is_required'] ) ? (bool) $data['is_required'] : (bool) $field->is_required,
            'is_active'     => isset( $data['is_active'] ) ? (bool) $data['is_active'] : (bool) $field->is_active,
        );

        return $this->database->update_custom_field( $field_id, $sanitized_data );
    }

    /**
     * Delete a custom field.
     *
     * @since 1.0.0
     * @param int $field_id The ID of the field to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_custom_field( $field_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_custom_fields'] ?? false ) ) {
            error_log( 'ASLP: Custom Fields feature is disabled. Field not deleted.' );
            return false; // Feature not enabled by admin
        }

        $field = $this->database->get_custom_field( $field_id );
        if ( ! $field ) {
            error_log( 'ASLP: Custom field ' . $field_id . ' not found for deletion.' );
            return false;
        }

        return $this->database->delete_custom_field( $field_id );
    }
}
