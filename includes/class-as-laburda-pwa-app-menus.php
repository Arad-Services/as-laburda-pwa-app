<?php
/**
 * Functionality related to app menu management.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The app menu management functionality of the plugin.
 *
 * This class handles the creation, display, and management of PWA app menus.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Menus {

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
     * Get app menus.
     *
     * @since 1.0.0
     * @param bool $active_only Optional. If true, only return active menus.
     * @return array Array of menu objects.
     */
    public function get_all_app_menus( $active_only = false ) {
        // Check if global feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_menus'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }
        return $this->database->get_all_app_menus( $active_only );
    }

    /**
     * Get an app menu by ID.
     *
     * @since 1.0.0
     * @param int $menu_id Menu ID.
     * @return object|null Menu object on success, null if not found.
     */
    public function get_app_menu( $menu_id ) {
        // Check if global feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_menus'] ?? false ) ) {
            return null; // Feature not enabled by admin
        }
        return $this->database->get_app_menu( $menu_id );
    }

    /**
     * Add a new app menu.
     *
     * @since 1.0.0
     * @param array $data Associative array of menu properties.
     * @return int|false The ID of the new menu on success, false on failure.
     */
    public function add_menu( $data ) {
        // Check if global feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_menus'] ?? false ) ) {
            error_log( 'ASLP: App Menus feature is disabled. Menu not added.' );
            return false; // Feature not enabled by admin
        }

        $sanitized_data = array(
            'menu_name'   => sanitize_text_field( $data['menu_name'] ?? '' ),
            'description' => sanitize_textarea_field( $data['description'] ?? '' ),
            'menu_items'  => AS_Laburda_PWA_App_Utils::safe_json_encode( $data['menu_items'] ?? array() ),
            'is_active'   => isset( $data['is_active'] ) ? (bool) $data['is_active'] : false,
        );

        if ( empty( $sanitized_data['menu_name'] ) ) {
            error_log( 'ASLP: Menu name is required.' );
            return false;
        }

        return $this->database->add_app_menu( $sanitized_data );
    }

    /**
     * Update an existing app menu.
     *
     * @since 1.0.0
     * @param int $menu_id The ID of the menu to update.
     * @param array $data Associative array of menu properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_menu( $menu_id, $data ) {
        // Check if global feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_menus'] ?? false ) ) {
            error_log( 'ASLP: App Menus feature is disabled. Menu not updated.' );
            return false; // Feature not enabled by admin
        }

        $menu = $this->database->get_app_menu( $menu_id );
        if ( ! $menu ) {
            error_log( 'ASLP: Menu ' . $menu_id . ' not found for update.' );
            return false; // Menu not found
        }

        $data['menu_name'] = sanitize_text_field( $data['menu_name'] ?? $menu->menu_name );
        $data['description'] = sanitize_textarea_field( $data['description'] ?? $menu->description );
        $data['menu_items'] = AS_Laburda_PWA_App_Utils::safe_json_encode( $data['menu_items'] ?? AS_Laburda_PWA_App_Utils::safe_json_decode($menu->menu_items, true) );
        $data['is_active'] = isset( $data['is_active'] ) ? (bool) $data['is_active'] : (bool) $menu->is_active;

        return $this->database->update_app_menu( $menu_id, $data );
    }

    /**
     * Delete an app menu.
     *
     * @since 1.0.0
     * @param int $menu_id The ID of the menu to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_menu( $menu_id ) {
        // Check if global feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_menus'] ?? false ) ) {
            error_log( 'ASLP: App Menus feature is disabled. Menu not deleted.' );
            return false; // Feature not enabled by admin
        }

        $menu = $this->database->get_app_menu( $menu_id );
        if ( ! $menu ) {
            error_log( 'ASLP: Menu ' . $menu_id . ' not found for deletion.' );
            return false; // Menu not found
        }

        return $this->database->delete_app_menu( $menu_id );
    }
}
