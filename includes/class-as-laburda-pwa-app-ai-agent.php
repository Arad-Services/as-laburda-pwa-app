<?php
/**
 * Functionality related to AI Agent management.
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The AI Agent management functionality of the plugin.
 *
 * This class handles interactions with an external AI service for various tasks
 * like chat, SEO generation, content creation, and site debugging.
 *
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_AI_Agent {

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
     * The API key for the AI service.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $api_key    The API key for the AI service.
     */
    private $api_key;

    /**
     * The API endpoint for the AI service.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $api_endpoint    The API endpoint for the AI service.
     */
    private $api_endpoint;

    /**
     * Initialize the class and set its properties.
     *
     * @since    2.0.0
     * @param    AS_Laburda_PWA_App $main_plugin The main plugin instance.
     */
    public function __construct( $main_plugin ) {
        $this->main_plugin = $main_plugin;
        $this->database = $main_plugin->get_database_manager();

        // Retrieve AI settings from global options
        $global_settings = $this->main_plugin->get_global_feature_settings();
        $this->api_key = $global_settings['ai_api_key'] ?? '';
        $this->api_endpoint = $global_settings['ai_api_endpoint'] ?? 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent'; // Default Gemini endpoint
    }

    /**
     * Set the AI API key.
     *
     * @since 2.0.0
     * @param string $api_key The AI service API key.
     */
    public function set_api_key( $api_key ) {
        $this->api_key = sanitize_text_field( $api_key );
        // Optionally, save to global settings immediately or allow admin to save from settings page
    }

    /**
     * Set the AI API endpoint.
     *
     * @since 2.0.0
     * @param string $api_endpoint The AI service API endpoint.
     */
    public function set_api_endpoint( $api_endpoint ) {
        $this->api_endpoint = esc_url_raw( $api_endpoint );
        // Optionally, save to global settings immediately or allow admin to save from settings page
    }

    /**
     * Make a request to the AI service.
     *
     * @since 2.0.0
     * @param array $payload The request payload for the AI service.
     * @return string|false The AI response text on success, false on failure.
     */
    private function make_ai_request( $payload ) {
        if ( empty( $this->api_key ) || empty( $this->api_endpoint ) ) {
            error_log( 'ASLP AI Agent: API Key or Endpoint is not set.' );
            return false;
        }

        $headers = array(
            'Content-Type' => 'application/json',
        );

        $args = array(
            'body'        => AS_Laburda_PWA_App_Utils::safe_json_encode( $payload ),
            'headers'     => $headers,
            'method'      => 'POST',
            'timeout'     => 60, // seconds
            'sslverify'   => false, // Consider true in production with proper SSL setup
        );

        $url = add_query_arg( 'key', $this->api_key, $this->api_endpoint );
        $response = wp_remote_post( $url, $args );

        if ( is_wp_error( $response ) ) {
            error_log( 'ASLP AI Agent Error: ' . $response->get_error_message() );
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = AS_Laburda_PWA_App_Utils::safe_json_decode( $body, true );

        if ( isset( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        } elseif ( isset( $data['error']['message'] ) ) {
            error_log( 'ASLP AI Agent API Error: ' . $data['error']['message'] );
        } else {
            error_log( 'ASLP AI Agent: Unexpected API response structure. Response: ' . $body );
        }

        return false;
    }

    /**
     * Chat with the AI assistant.
     *
     * @since 2.0.0
     * @param string $user_message The message from the user.
     * @return string|false The AI's response, or false on failure.
     */
    public function chat_with_ai( $user_message ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_ai_agent'] ?? false ) ) {
            error_log( 'ASLP: AI Agent feature is disabled. Chat not processed.' );
            return false; // Feature not enabled by admin
        }

        $prompt = "You are a helpful assistant for a PWA App Creator plugin. Respond concisely and professionally. User: " . $user_message;

        $payload = array(
            'contents' => array(
                array(
                    'role' => 'user',
                    'parts' => array(
                        array( 'text' => $prompt )
                    )
                )
            )
        );

        $ai_response = $this->make_ai_request( $payload );

        if ( $ai_response ) {
            $user_id = get_current_user_id() ? get_current_user_id() : 0;
            $this->database->add_ai_interaction( array(
                'user_id'        => $user_id,
                'interaction_type' => 'chat',
                'prompt'         => $user_message,
                'response'       => $ai_response,
            ) );
        }

        return $ai_response;
    }

    /**
     * Generate SEO data (title, description, keywords) for a given item using AI.
     *
     * @since 2.0.0
     * @param string $item_type The type of item ('listing' or 'app').
     * @param string $item_id The ID (numeric or UUID) of the item.
     * @param string $content_to_analyze The main content/description to base SEO on.
     * @return array|false Associative array with 'seo_title', 'seo_description', 'seo_keywords' on success, false on failure.
     */
    public function generate_seo_for_item( $item_type, $item_id, $content_to_analyze ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_ai_agent'] ?? false ) || ! ( $global_features['enable_seo_tools'] ?? false ) ) {
            error_log( 'ASLP: AI Agent or SEO Tools feature is disabled. SEO not generated.' );
            return false; // Feature not enabled by admin
        }

        $prompt = "Generate SEO metadata (title, description, keywords) for the following content related to a " . $item_type . ". Provide the output in JSON format with keys: 'seo_title' (max 60 chars), 'seo_description' (max 160 chars), 'seo_keywords' (comma-separated, max 200 chars). Ensure the keywords are highly relevant.\n\nContent:\n" . $content_to_analyze;

        $payload = array(
            'contents' => array(
                array(
                    'role' => 'user',
                    'parts' => array(
                        array( 'text' => $prompt )
                    )
                )
            ),
            'generationConfig' => array(
                'responseMimeType' => 'application/json',
                'responseSchema' => array(
                    'type' => 'OBJECT',
                    'properties' => array(
                        'seo_title' => array( 'type' => 'STRING' ),
                        'seo_description' => array( 'type' => 'STRING' ),
                        'seo_keywords' => array( 'type' => 'STRING' ),
                    ),
                    'propertyOrdering' => array( 'seo_title', 'seo_description', 'seo_keywords' )
                )
            )
        );

        $json_response = $this->make_ai_request( $payload );

        if ( $json_response ) {
            $seo_data = AS_Laburda_PWA_App_Utils::safe_json_decode( $json_response, true );

            if ( is_array( $seo_data ) && isset( $seo_data['seo_title'] ) ) {
                // Update the item in the database with new SEO data
                $data_to_update = array(
                    'seo_title'       => substr( sanitize_text_field( $seo_data['seo_title'] ), 0, 60 ),
                    'seo_description' => substr( sanitize_textarea_field( $seo_data['seo_description'] ), 0, 160 ),
                    'seo_keywords'    => substr( sanitize_text_field( $seo_data['seo_keywords'] ), 0, 200 ),
                );

                $updated = false;
                if ( $item_type === 'listing' ) {
                    $updated = $this->database->update_business_listing( absint( $item_id ), $data_to_update );
                } elseif ( $item_type === 'app' ) {
                    $updated = $this->database->update_app( sanitize_text_field( $item_id ), $data_to_update );
                }

                if ( $updated !== false ) {
                    $user_id = get_current_user_id() ? get_current_user_id() : 0;
                    $this->database->add_ai_interaction( array(
                        'user_id'        => $user_id,
                        'interaction_type' => 'seo_generation',
                        'prompt'         => "Generate SEO for " . $item_type . " ID: " . $item_id . " with content: " . $content_to_analyze,
                        'response'       => $json_response,
                    ) );
                    return $data_to_update;
                } else {
                    error_log( 'ASLP AI Agent: Failed to save generated SEO data for ' . $item_type . ' ID: ' . $item_id );
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * Generate content (e.g., descriptions, blog posts) using AI.
     *
     * @since 2.0.0
     * @param string $content_type The type of content to generate (e.g., 'product_description', 'blog_post', 'ad_copy').
     * @param string $prompt The prompt for content generation.
     * @return string|false The generated content, or false on failure.
     */
    public function generate_content( $content_type, $prompt ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_ai_agent'] ?? false ) ) {
            error_log( 'ASLP: AI Agent feature is disabled. Content not generated.' );
            return false; // Feature not enabled by admin
        }

        $full_prompt = "Generate a " . sanitize_text_field( $content_type ) . " based on the following instructions: " . $prompt;

        $payload = array(
            'contents' => array(
                array(
                    'role' => 'user',
                    'parts' => array(
                        array( 'text' => $full_prompt )
                    )
                )
            )
        );

        $generated_content = $this->make_ai_request( $payload );

        if ( $generated_content ) {
            $user_id = get_current_user_id() ? get_current_user_id() : 0;
            $this->database->add_ai_interaction( array(
                'user_id'        => $user_id,
                'interaction_type' => 'content_generation',
                'prompt'         => $full_prompt,
                'response'       => $generated_content,
            ) );
        }

        return $generated_content;
    }

    /**
     * Debug site issues using AI.
     *
     * @since 2.0.0
     * @param string $debug_info The debug information (e.g., error logs, system info).
     * @return string|false A JSON string containing the AI's debug report, or false on failure.
     */
    public function debug_site_with_ai( $debug_info ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_ai_agent'] ?? false ) ) {
            error_log( 'ASLP: AI Agent feature is disabled. Debug not processed.' );
            return false; // Feature not enabled by admin
        }

        $prompt = "Analyze the following WordPress site debug information and provide a structured report in JSON format. The report should include: 'problem_summary', 'root_cause', 'suggested_fix', 'impact', and 'confidence_score' (1-5). If no clear problem, state 'No obvious issues found'.\n\nDebug Info:\n" . $debug_info;

        $payload = array(
            'contents' => array(
                array(
                    'role' => 'user',
                    'parts' => array(
                        array( 'text' => $prompt )
                    )
                )
            ),
            'generationConfig' => array(
                'responseMimeType' => 'application/json',
                'responseSchema' => array(
                    'type' => 'OBJECT',
                    'properties' => array(
                        'problem_summary' => array( 'type' => 'STRING' ),
                        'root_cause' => array( 'type' => 'STRING' ),
                        'suggested_fix' => array( 'type' => 'STRING' ),
                        'impact' => array( 'type' => 'STRING' ),
                        'confidence_score' => array( 'type' => 'INTEGER' ),
                    ),
                    'propertyOrdering' => array( 'problem_summary', 'root_cause', 'suggested_fix', 'impact', 'confidence_score' )
                )
            )
        );

        $json_response = $this->make_ai_request( $payload );

        if ( $json_response ) {
            $user_id = get_current_user_id() ? get_current_user_id() : 0;
            $this->database->add_ai_interaction( array(
                'user_id'        => $user_id,
                'interaction_type' => 'debug_report',
                'prompt'         => $prompt,
                'response'       => $json_response,
            ) );
        }

        return $json_response;
    }
}
