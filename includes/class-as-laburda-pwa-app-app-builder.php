<?php
/**
 * Functionality related to PWA App Builder management.
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The PWA App Builder management functionality of the plugin.
 *
 * This class handles the creation, updating, retrieval, and deletion of PWA apps.
 *
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_App_Builder {

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
     * Create or update a PWA app.
     *
     * @since 2.0.0
     * @param int $user_id The ID of the user creating/updating the app.
     * @param array $app_data Associative array of app properties.
     * @param string $app_uuid Optional. The UUID of the app to update. If empty, a new app is created.
     * @return string|false The UUID of the created/updated app on success, false on failure.
     */
    public function create_update_app( $user_id, $app_data, $app_uuid = '' ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            error_log( 'ASLP: App Builder feature is disabled.' );
            return false; // Feature not enabled by admin
        }

        // Sanitize and validate data
        $sanitized_data = array(
            'user_id'                   => absint( $user_id ),
            'app_name'                  => sanitize_text_field( $app_data['app_name'] ?? '' ),
            'short_name'                => sanitize_text_field( $app_data['short_name'] ?? '' ),
            'description'               => sanitize_textarea_field( $app_data['description'] ?? '' ),
            'start_url'                 => esc_url_raw( $app_data['start_url'] ?? '/' ),
            'theme_color'               => sanitize_hex_color( $app_data['theme_color'] ?? '#2196f3' ),
            'background_color'          => sanitize_hex_color( $app_data['background_color'] ?? '#ffffff' ),
            'display_mode'              => sanitize_text_field( $app_data['display_mode'] ?? 'standalone' ),
            'orientation'               => sanitize_text_field( $app_data['orientation'] ?? 'any' ),
            'icon_192'                  => esc_url_raw( $app_data['icon_192'] ?? '' ),
            'icon_512'                  => esc_url_raw( $app_data['icon_512'] ?? '' ),
            'splash_screen'             => esc_url_raw( $app_data['splash_screen'] ?? '' ),
            'offline_page_id'           => absint( $app_data['offline_page_id'] ?? 0 ),
            'dashboard_page_id'         => absint( $app_data['dashboard_page_id'] ?? 0 ),
            'login_page_id'             => absint( $app_data['login_page_id'] ?? 0 ),
            'enable_push_notifications' => isset( $app_data['enable_push_notifications'] ) ? (bool) $app_data['enable_push_notifications'] : false,
            'enable_persistent_storage' => isset( $app_data['enable_persistent_storage'] ) ? (bool) $app_data['enable_persistent_storage'] : false,
            'desktop_template_option'   => sanitize_text_field( $app_data['desktop_template_option'] ?? 'default' ),
            'mobile_template_option'    => sanitize_text_field( $app_data['mobile_template_option'] ?? 'default' ),
            'app_status'                => sanitize_text_field( $app_data['app_status'] ?? 'draft' ),
            'current_app_plan_id'       => absint( $app_data['current_app_plan_id'] ?? 0 ),
            'seo_title'                 => sanitize_text_field( $app_data['seo_title'] ?? '' ),
            'seo_description'           => sanitize_textarea_field( $app_data['seo_description'] ?? '' ),
            'seo_keywords'              => sanitize_text_field( $app_data['seo_keywords'] ?? '' ),
        );

        if ( empty( $sanitized_data['app_name'] ) || empty( $sanitized_data['start_url'] ) ) {
            error_log( 'ASLP: App name or start URL is missing.' );
            return false;
        }

        if ( ! empty( $app_uuid ) ) {
            // Update existing app
            $existing_app = $this->database->get_app( $app_uuid );
            if ( ! $existing_app ) {
                error_log( 'ASLP: App with UUID ' . $app_uuid . ' not found for update.' );
                return false;
            }
            // Ensure user has permission to update this app
            if ( $existing_app->user_id !== $user_id && ! current_user_can( 'aslp_manage_apps' ) ) {
                error_log( 'ASLP: User ' . $user_id . ' does not have permission to update app ' . $app_uuid . '.' );
                return false;
            }

            $updated = $this->database->update_app( $app_uuid, $sanitized_data );
            if ( $updated !== false ) {
                return $app_uuid;
            } else {
                error_log( 'ASLP: Failed to update app ' . $app_uuid . ' in database.' );
                return false;
            }
        } else {
            // Create new app
            $sanitized_data['app_uuid'] = AS_Laburda_PWA_App_Utils::generate_uuid(); // Generate a new UUID
            $inserted = $this->database->add_app( $sanitized_data );
            if ( $inserted ) {
                return $sanitized_data['app_uuid'];
            } else {
                error_log( 'ASLP: Failed to add new app to database.' );
                return false;
            }
        }
    }

    /**
     * Get a PWA app by its UUID.
     *
     * @since 2.0.0
     * @param string $app_uuid The UUID of the app.
     * @return object|null App object on success, null if not found.
     */
    public function get_app( $app_uuid ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            return null; // Feature not enabled by admin
        }
        return $this->database->get_app( $app_uuid );
    }

    /**
     * Get all PWA apps associated with a specific user.
     *
     * @since 2.0.0
     * @param int $user_id The ID of the user.
     * @return array Array of app objects.
     */
    public function get_user_apps( $user_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }
        return $this->database->get_apps_by_user_id( $user_id );
    }

    /**
     * Get all PWA apps (for admin).
     *
     * @since 2.0.0
     * @param array $args Optional. Query arguments (e.g., 'app_status').
     * @return array Array of app objects.
     */
    public function get_all_apps( $args = array() ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }
        return $this->database->get_all_apps( $args );
    }

    /**
     * Delete a PWA app.
     *
     * @since 2.0.0
     * @param string $app_uuid The UUID of the app to delete.
     * @param int $user_id Optional. User ID for permission check (if not admin).
     * @return bool True on success, false on failure.
     */
    public function delete_app( $app_uuid, $user_id = 0 ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            return false; // Feature not enabled by admin
        }

        $app = $this->database->get_app( $app_uuid );
        if ( ! $app ) {
            error_log( 'ASLP: App with UUID ' . $app_uuid . ' not found for deletion.' );
            return false;
        }

        // Permission check: only owner or admin can delete
        if ( $user_id && $app->user_id !== $user_id && ! current_user_can( 'aslp_manage_apps' ) ) {
            error_log( 'ASLP: User ' . $user_id . ' does not have permission to delete app ' . $app_uuid . '.' );
            return false;
        }

        return $this->database->delete_app( $app_uuid );
    }

    /**
     * Get app templates.
     *
     * @since 2.0.0
     * @param bool $active_only Optional. If true, only return active templates.
     * @return array Array of app template objects.
     */
    public function get_app_templates( $active_only = true ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }
        return $this->database->get_all_app_templates( $active_only );
    }

    /**
     * Apply an app template to an existing app.
     *
     * @since 2.0.0
     * @param string $app_uuid The UUID of the app to apply the template to.
     * @param int $template_id The ID of the template to apply.
     * @param int $user_id The ID of the user applying the template.
     * @return bool True on success, false on failure.
     */
    public function apply_app_template( $app_uuid, $template_id, $user_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            return false; // Feature not enabled by admin
        }

        $app = $this->database->get_app( $app_uuid );
        $template = $this->database->get_app_template( $template_id );

        if ( ! $app || ! $template ) {
            error_log( 'ASLP: App or template not found for applying template.' );
            return false;
        }

        // Ensure user has permission to modify this app
        if ( $app->user_id !== $user_id && ! current_user_can( 'aslp_manage_apps' ) ) {
            error_log( 'ASLP: User ' . $user_id . ' does not have permission to modify app ' . $app_uuid . '.' );
            return false;
        }

        $template_data = AS_Laburda_PWA_App_Utils::safe_json_decode( $template->template_data, true );

        if ( ! is_array( $template_data ) ) {
            error_log( 'ASLP: Invalid template data format.' );
            return false;
        }

        // Merge template data into app data, overwriting existing fields
        $updated_app_data = array_merge( (array) $app, $template_data );

        // Remove app_uuid and user_id from updated data to prevent accidental overwrite during update
        unset( $updated_app_data['app_uuid'] );
        unset( $updated_app_data['user_id'] );
        unset( $updated_app_data['date_created'] ); // Don't update creation date
        unset( $updated_app_data['id'] ); // Remove primary key if it exists from casting to array

        // Only update fields that are part of the app configuration
        $fields_to_update = array(
            'app_name', 'short_name', 'description', 'start_url', 'theme_color',
            'background_color', 'display_mode', 'orientation', 'icon_192', 'icon_512',
            'splash_screen', 'offline_page_id', 'dashboard_page_id', 'login_page_id',
            'enable_push_notifications', 'enable_persistent_storage',
            'desktop_template_option', 'mobile_template_option', 'app_status',
            'current_app_plan_id', 'seo_title', 'seo_description', 'seo_keywords'
        );

        $data_for_db = array();
        foreach ( $fields_to_update as $field ) {
            if ( isset( $updated_app_data[ $field ] ) ) {
                $data_for_db[ $field ] = $updated_app_data[ $field ];
            }
        }

        $result = $this->database->update_app( $app_uuid, $data_for_db );

        if ( $result !== false ) {
            return true;
        } else {
            error_log( 'ASLP: Failed to apply template to app ' . $app_uuid . '.' );
            return false;
        }
    }
}
