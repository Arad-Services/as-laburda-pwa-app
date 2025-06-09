<?php
/**
 * Funtionality related to the plugin's shortcodes.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The shortcode-specific functionality of the plugin.
 *
 * Defines the shortcode callbacks for business listing forms and dashboards.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Shortcodes {

    private $database;
    private $main_plugin;
    private $business_listings_manager;
    private $listing_plans_manager;
    private $notifications_manager;
    private $coupons_manager;
    private $products_manager; // NEW
    private $events_manager;   // NEW

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    AS_Laburda_PWA_App $main_plugin A reference to the main plugin class.
     */
    public function __construct( $main_plugin ) {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-database.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-utils.php';
        $this->database = new AS_Laburda_PWA_App_Database();
        $this->main_plugin = $main_plugin;
        $this->business_listings_manager = $main_plugin->get_business_listings_manager();
        $this->listing_plans_manager = $main_plugin->get_listing_plans_manager();
        $this->notifications_manager = $main_plugin->get_notifications_manager();
        $this->coupons_manager = $main_plugin->get_coupons_manager();
        $this->products_manager = $main_plugin->get_products_manager(); // NEW
        $this->events_manager = $main_plugin->get_events_manager();     // NEW
    }

    /**
     * Register all shortcodes.
     *
     * @since 1.0.0
     */
    public function register_shortcodes() {
        add_shortcode( 'aslp_business_listing_form', array( $this, 'render_business_listing_form' ) );
        add_shortcode( 'aslp_business_owner_dashboard', array( $this, 'render_business_owner_dashboard' ) ); // NEW
        add_shortcode( 'aslp_user_dashboard', array( $this, 'render_user_dashboard' ) ); // NEW
        add_shortcode( 'aslp_app_creator_dashboard', array( $this, 'render_app_creator_dashboard' ) ); // NEW
        add_shortcode( 'aslp_display_listing', array( $this, 'render_display_listing' ) );
    }

    /**
     * Render the business listing submission/edit form.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render_business_listing_form( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . esc_html__( 'You must be logged in to submit or edit business listings.', 'as-laburda-pwa-app' ) . '</p>';
        }

        $current_user_id = get_current_user_id();
        $atts = shortcode_atts( array(
            'listing_id' => 0,
        ), $atts, 'aslp_business_listing_form' );

        $listing_id = absint( $atts['listing_id'] );
        $listing_data = null;
        $is_editing = false;

        if ( $listing_id ) {
            $listing_data = $this->database->get_business_listing( $listing_id );
            if ( $listing_data && ( $listing_data->user_id == $current_user_id || current_user_can( 'aslp_manage_all_business_listings' ) ) ) {
                $is_editing = true;
            } else {
                return '<p>' . esc_html__( 'You do not have permission to edit this listing or it does not exist.', 'as-laburda-pwa-app' ) . '</p>';
            }
        }

        ob_start();
        ?>
        <div class="aslp-form-wrap">
            <h2><?php echo $is_editing ? esc_html__( 'Edit Business Listing', 'as-laburda-pwa-app' ) : esc_html__( 'Submit New Business Listing', 'as-laburda-pwa-app' ); ?></h2>
            <div id="aslp-form-message" class="aslp-message"></div>

            <form id="aslp-business-listing-form" enctype="multipart/form-data">
                <input type="hidden" name="action" value="aslp_submit_business_listing">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'aslp_submit_business_listing_nonce' ); ?>">
                <?php if ( $is_editing ) : ?>
                    <input type="hidden" name="listing_id" value="<?php echo esc_attr( $listing_id ); ?>">
                <?php endif; ?>

                <table class="form-table">
                    <tr>
                        <th><label for="listing_title"><?php esc_html_e( 'Business Name', 'as-laburda-pwa-app' ); ?> <span class="required">*</span></label></th>
                        <td><input type="text" id="listing_title" name="listing_title" value="<?php echo esc_attr( $listing_data->listing_title ?? '' ); ?>" required></td>
                    </tr>
                    <tr>
                        <th><label for="short_description"><?php esc_html_e( 'Short Description', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><input type="text" id="short_description" name="short_description" value="<?php echo esc_attr( $listing_data->short_description ?? '' ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="description"><?php esc_html_e( 'Full Description', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><textarea id="description" name="description" rows="5"><?php echo esc_textarea( $listing_data->description ?? '' ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="logo_url"><?php esc_html_e( 'Business Logo', 'as-laburda-pwa-app' ); ?></label></th>
                        <td>
                            <input type="url" id="logo_url" name="logo_url" value="<?php echo esc_attr( $listing_data->logo_url ?? '' ); ?>" class="regular-text">
                            <button type="button" class="button aslp-upload-button" data-target="logo_url"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                            <div class="aslp-image-preview">
                                <?php if ( ! empty( $listing_data->logo_url ) ) : ?>
                                    <img src="<?php echo esc_url( $listing_data->logo_url ); ?>" style="max-width: 100px; height: auto;">
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="featured_image_url"><?php esc_html_e( 'Featured Image', 'as-laburda-pwa-app' ); ?></label></th>
                        <td>
                            <input type="url" id="featured_image_url" name="featured_image_url" value="<?php echo esc_attr( $listing_data->featured_image_url ?? '' ); ?>" class="regular-text">
                            <button type="button" class="button aslp-upload-button" data-target="featured_image_url"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                            <div class="aslp-image-preview">
                                <?php if ( ! empty( $listing_data->featured_image_url ) ) : ?>
                                    <img src="<?php echo esc_url( $listing_data->featured_image_url ); ?>" style="max-width: 100px; height: auto;">
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="gallery_images_urls"><?php esc_html_e( 'Gallery Images', 'as-laburda-pwa-app' ); ?></label></th>
                        <td>
                            <input type="hidden" id="gallery_images_urls" name="gallery_images_urls" value="<?php echo esc_attr( $listing_data->gallery_images_urls ?? '' ); ?>">
                            <button type="button" class="button aslp-add-gallery-images"><?php esc_html_e( 'Add/Edit Gallery Images', 'as-laburda-pwa-app' ); ?></button>
                            <div class="aslp-gallery-preview">
                                <?php
                                $gallery_images = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing_data->gallery_images_urls ?? '[]', true );
                                if ( is_array( $gallery_images ) && ! empty( $gallery_images ) ) {
                                    foreach ( $gallery_images as $img_url ) {
                                        echo '<img src="' . esc_url( $img_url ) . '" style="max-width: 80px; height: auto; margin: 5px; border: 1px solid #ddd;">';
                                    }
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="youtube_video_id"><?php esc_html_e( 'YouTube Video ID', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><input type="text" id="youtube_video_id" name="youtube_video_id" value="<?php echo esc_attr( $listing_data->youtube_video_id ?? '' ); ?>" placeholder="<?php esc_attr_e( 'e.g., dQw4w9WgXcQ', 'as-laburda-pwa-app' ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="city"><?php esc_html_e( 'City', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><input type="text" id="city" name="city" value="<?php echo esc_attr( $listing_data->city ?? '' ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="address"><?php esc_html_e( 'Address', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><textarea id="address" name="address" rows="3"><?php echo esc_textarea( $listing_data->address ?? '' ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="phone_number"><?php esc_html_e( 'Phone Number', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><input type="tel" id="phone_number" name="phone_number" value="<?php echo esc_attr( $listing_data->phone_number ?? '' ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="whatsapp_number"><?php esc_html_e( 'WhatsApp Number', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><input type="tel" id="whatsapp_number" name="whatsapp_number" value="<?php echo esc_attr( $listing_data->whatsapp_number ?? '' ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="website_url"><?php esc_html_e( 'Website URL', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><input type="url" id="website_url" name="website_url" value="<?php echo esc_attr( $listing_data->website_url ?? '' ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="business_hours"><?php esc_html_e( 'Business Hours (JSON)', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><textarea id="business_hours" name="business_hours" rows="5" placeholder='<?php esc_attr_e( '{"Monday": "9:00-17:00", "Tuesday": "9:00-17:00"}', 'as-laburda-pwa-app' ); ?>'><?php echo esc_textarea( $listing_data->business_hours ?? '' ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="categories"><?php esc_html_e( 'Categories (comma-separated)', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><input type="text" id="categories" name="categories" value="<?php echo esc_attr( $listing_data->categories ?? '' ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="more_info_under_map"><?php esc_html_e( 'Additional Info (under map)', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><textarea id="more_info_under_map" name="more_info_under_map" rows="3"><?php echo esc_textarea( $listing_data->more_info_under_map ?? '' ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="features"><?php esc_html_e( 'Features (comma-separated)', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><input type="text" id="features" name="features" value="<?php echo esc_attr( $listing_data->features ?? '' ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="price_range"><?php esc_html_e( 'Price Range', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><input type="text" id="price_range" name="price_range" value="<?php echo esc_attr( $listing_data->price_range ?? '' ); ?>" placeholder="<?php esc_attr_e( 'e.g., $ - $$$', 'as-laburda-pwa-app' ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="faq"><?php esc_html_e( 'FAQ (JSON)', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><textarea id="faq" name="faq" rows="5" placeholder='<?php esc_attr_e( '[{"question": "Q1", "answer": "A1"}, {"question": "Q2", "answer": "A2"}]', 'as-laburda-pwa-app' ); ?>'><?php echo esc_textarea( $listing_data->faq ?? '' ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="social_links"><?php esc_html_e( 'Social Links (JSON)', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><textarea id="social_links" name="social_links" rows="5" placeholder='<?php esc_attr_e( '{"facebook": "url", "twitter": "url"}', 'as-laburda-pwa-app' ); ?>'><?php echo esc_textarea( $listing_data->social_links ?? '' ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="tags"><?php esc_html_e( 'Tags (comma-separated)', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><input type="text" id="tags" name="tags" value="<?php echo esc_attr( $listing_data->tags ?? '' ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="keywords"><?php esc_html_e( 'Keywords (comma-separated)', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><input type="text" id="keywords" name="keywords" value="<?php echo esc_attr( $listing_data->keywords ?? '' ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="booking_options"><?php esc_html_e( 'Booking Options (JSON)', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><textarea id="booking_options" name="booking_options" rows="5" placeholder='<?php esc_attr_e( '{"type": "external_link", "url": "https://example.com/book"}', 'as-laburda-pwa-app' ); ?>'><?php echo esc_textarea( $listing_data->booking_options ?? '' ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="menu_option"><?php esc_html_e( 'Menu Option (JSON)', 'as-laburda-pwa-app' ); ?></label></th>
                        <td><textarea id="menu_option" name="menu_option" rows="5" placeholder='<?php esc_attr_e( '{"type": "link", "url": "https://example.com/menu.pdf"}', 'as-laburda-pwa-app' ); ?>'><?php echo esc_textarea( $listing_data->menu_option ?? '' ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="current_plan_id"><?php esc_html_e( 'Current Plan', 'as-laburda-pwa-app' ); ?></label></th>
                        <td>
                            <select id="current_plan_id" name="current_plan_id">
                                <option value="0"><?php esc_html_e( 'Select a Plan', 'as-laburda-pwa-app' ); ?></option>
                                <?php
                                $plans = $this->listing_plans_manager->get_all_active_plans();
                                foreach ( $plans as $plan ) {
                                    $selected = ( $listing_data->current_plan_id ?? 0 ) == $plan->id ? 'selected' : '';
                                    echo '<option value="' . esc_attr( $plan->id ) . '" ' . $selected . '>' . esc_html( $plan->plan_name ) . ' (' . AS_Laburda_PWA_App_Utils::format_price( $plan->price ) . ' / ' . esc_html( $plan->billing_period ) . ')</option>';
                                }
                                ?>
                            </select>
                            <p class="description"><?php esc_html_e( 'Assign a membership plan to this business listing.', 'as-laburda-pwa-app' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="status"><?php esc_html_e( 'Status', 'as-laburda-pwa-app' ); ?></label></th>
                        <td>
                            <select id="status" name="status">
                                <option value="pending" <?php selected( $listing_data->status ?? '', 'pending' ); ?>><?php esc_html_e( 'Pending Review', 'as-laburda-pwa-app' ); ?></option>
                                <option value="active" <?php selected( $listing_data->status ?? '', 'active' ); ?>><?php esc_html_e( 'Active', 'as-laburda-pwa-app' ); ?></option>
                                <option value="inactive" <?php selected( $listing_data->status ?? '', 'inactive' ); ?>><?php selected( $listing_data->status ?? '', 'inactive' ); ?>><?php esc_html_e( 'Inactive', 'as-laburda-pwa-app' ); ?></option>
                                <option value="rejected" <?php selected( $listing_data->status ?? '', 'rejected' ); ?>><?php esc_html_e( 'Rejected', 'as-laburda-pwa-app' ); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

                <?php
                // Render custom fields if enabled and configured for business listings
                $global_features = $this->main_plugin->get_global_feature_settings();
                if ( ( $global_features['enable_custom_fields'] ?? false ) ) {
                    $custom_fields = $this->database->get_all_custom_fields( 'business_listing', true );
                    if ( ! empty( $custom_fields ) ) {
                        echo '<h3>' . esc_html__( 'Custom Fields', 'as-laburda-pwa-app' ) . '</h3>';
                        echo '<table class="form-table">';
                        foreach ( $custom_fields as $field ) {
                            $field_value = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing_data->{$field->field_name} ?? '""' ); // Assuming custom fields stored by their name
                            echo '<tr>';
                            echo '<th><label for="custom_field_' . esc_attr( $field->field_name ) . '">' . esc_html( $field->field_label );
                            echo ( $field->is_required ) ? ' <span class="required">*</span>' : '';
                            echo '</label></th>';
                            echo '<td>';
                            switch ( $field->field_type ) {
                                case 'text':
                                case 'email':
                                case 'url':
                                case 'number':
                                    echo '<input type="' . esc_attr( $field->field_type ) . '" id="custom_field_' . esc_attr( $field->field_name ) . '" name="custom_fields[' . esc_attr( $field->field_name ) . ']" value="' . esc_attr( $field_value ) . '" ' . ( $field->is_required ? 'required' : '' ) . ' class="regular-text">';
                                    break;
                                case 'textarea':
                                    echo '<textarea id="custom_field_' . esc_attr( $field->field_name ) . '" name="custom_fields[' . esc_attr( $field->field_name ) . ']" rows="5" ' . ( $field->is_required ? 'required' : '' ) . '>' . esc_textarea( $field_value ) . '</textarea>';
                                    break;
                                case 'select':
                                    $options = AS_Laburda_PWA_App_Utils::safe_json_decode( $field->field_options, true );
                                    echo '<select id="custom_field_' . esc_attr( $field->field_name ) . '" name="custom_fields[' . esc_attr( $field->field_name ) . ']" ' . ( $field->is_required ? 'required' : '' ) . '>';
                                    foreach ( $options as $option ) {
                                        echo '<option value="' . esc_attr( $option['value'] ) . '" ' . selected( $field_value, $option['value'], false ) . '>' . esc_html( $option['label'] ) . '</option>';
                                    }
                                    echo '</select>';
                                    break;
                                case 'checkbox':
                                    echo '<label><input type="checkbox" id="custom_field_' . esc_attr( $field->field_name ) . '" name="custom_fields[' . esc_attr( $field->field_name ) . ']" value="1" ' . checked( $field_value, true, false ) . '> ' . esc_html__( 'Yes', 'as-laburda-pwa-app' ) . '</label>';
                                    break;
                                case 'radio':
                                    $options = AS_Laburda_PWA_App_Utils::safe_json_decode( $field->field_options, true );
                                    foreach ( $options as $option ) {
                                        echo '<label><input type="radio" name="custom_fields[' . esc_attr( $field->field_name ) . ']" value="' . esc_attr( $option['value'] ) . '" ' . checked( $field_value, $option['value'], false ) . ' ' . ( $field->is_required ? 'required' : '' ) . '> ' . esc_html( $option['label'] ) . '</label><br>';
                                    }
                                    break;
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                }
                ?>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo $is_editing ? esc_attr__( 'Update Listing', 'as-laburda-pwa-app' ) : esc_attr__( 'Submit Listing', 'as-laburda-pwa-app' ); ?>">
                </p>
            </form>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Media Uploader for single images
                $('.aslp-upload-button').on('click', function(e) {
                    e.preventDefault();
                    const targetField = $(this).data('target');
                    const $targetInput = $('#' + targetField);
                    const $previewDiv = $targetInput.siblings('.aslp-image-preview');

                    let mediaUploader;
                    if (mediaUploader) {
                        mediaUploader.open();
                        return;
                    }

                    mediaUploader = wp.media({
                        title: '<?php esc_html_e( 'Choose Image', 'as-laburda-pwa-app' ); ?>',
                        button: {
                            text: '<?php esc_html_e( 'Choose Image', 'as-laburda-pwa-app' ); ?>'
                        },
                        multiple: false
                    });

                    mediaUploader.on('select', function() {
                        const attachment = mediaUploader.state().get('selection').first().toJSON();
                        $targetInput.val(attachment.url);
                        $previewDiv.html('<img src="' + attachment.url + '" style="max-width: 100px; height: auto;">');
                    });

                    mediaUploader.open();
                });

                // Media Uploader for gallery images
                $('.aslp-add-gallery-images').on('click', function(e) {
                    e.preventDefault();
                    const $galleryInput = $('#gallery_images_urls');
                    const $galleryPreview = $('.aslp-gallery-preview');
                    let currentImages = $galleryInput.val() ? JSON.parse($galleryInput.val()) : [];

                    let mediaUploader;
                    if (mediaUploader) {
                        mediaUploader.open();
                        return;
                    }

                    mediaUploader = wp.media({
                        title: '<?php esc_html_e( 'Choose Gallery Images', 'as-laburda-pwa-app' ); ?>',
                        button: {
                            text: '<?php esc_html_e( 'Add Images', 'as-laburda-pwa-app' ); ?>'
                        },
                        multiple: true // Allow multiple image selection
                    });

                    mediaUploader.on('select', function() {
                        const attachments = mediaUploader.state().get('selection').toJSON();
                        attachments.forEach(function(attachment) {
                            if (currentImages.indexOf(attachment.url) === -1) { // Avoid duplicates
                                currentImages.push(attachment.url);
                            }
                        });
                        $galleryInput.val(JSON.stringify(currentImages));

                        // Update gallery preview
                        $galleryPreview.empty();
                        currentImages.forEach(function(url) {
                            $galleryPreview.append('<img src="' + url + '" style="max-width: 80px; height: auto; margin: 5px; border: 1px solid #ddd;">');
                        });
                    });

                    mediaUploader.open();
                });


                // Handle form submission
                $('#aslp-business-listing-form').on('submit', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const $messageDiv = $('#aslp-form-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);

                    // Handle JSON fields
                    const jsonFields = ['business_hours', 'faq', 'social_links', 'booking_options', 'menu_option'];
                    jsonFields.forEach(function(field) {
                        const value = $form.find('[name="' + field + '"]').val();
                        try {
                            // Attempt to parse and re-stringify to ensure valid JSON
                            const parsed = JSON.parse(value);
                            formData.set(field, JSON.stringify(parsed));
                        } catch (e) {
                            // If invalid JSON, send as plain text or handle error
                            console.warn('Invalid JSON for ' + field + ' field. Sending as plain text.', value);
                            formData.set(field, value);
                        }
                    });

                    // Handle custom fields
                    const customFieldsData = {};
                    $form.find('[name^="custom_fields["]').each(function() {
                        const nameMatch = $(this).attr('name').match(/custom_fields\[(.*?)\]/);
                        if (nameMatch && nameMatch[1]) {
                            const fieldName = nameMatch[1];
                            let fieldValue;
                            if ($(this).is(':checkbox')) {
                                fieldValue = $(this).is(':checked') ? 1 : 0;
                            } else if ($(this).is(':radio')) {
                                if ($(this).is(':checked')) {
                                    fieldValue = $(this).val();
                                } else {
                                    return; // Skip unchecked radio buttons
                                }
                            } else {
                                fieldValue = $(this).val();
                            }
                            customFieldsData[fieldName] = fieldValue;
                        }
                    });
                    formData.set('custom_fields', JSON.stringify(customFieldsData));


                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl, // Localized ajaxurl from public.php
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                if (!<?php echo json_encode( $is_editing ); ?>) { // If it was a new submission
                                    $form[0].reset(); // Clear form
                                    $('.aslp-image-preview').empty();
                                    $('.aslp-gallery-preview').empty();
                                }
                                // Optionally, redirect or update UI to show the new/updated listing
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while submitting the listing.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Render the business owner dashboard. (NEW)
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render_business_owner_dashboard( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . esc_html__( 'You must be logged in to access the business owner dashboard.', 'as-laburda-pwa-app' ) . '</p>';
        }

        $current_user_id = get_current_user_id();
        $user_listings = $this->database->get_business_listings_by_user( $current_user_id );

        if ( empty( $user_listings ) ) {
            return '<p>' . esc_html__( 'You do not have any business listings yet. Please create one first.', 'as-laburda-pwa-app' ) . '</p>' .
                   do_shortcode( '[aslp_business_listing_form]' ); // Offer to create one
        }

        ob_start();
        ?>
        <div class="aslp-dashboard-wrap">
            <h2><?php esc_html_e( 'Your Business Owner Dashboard', 'as-laburda-pwa-app' ); ?></h2>

            <h3 class="nav-tab-wrapper">
                <a href="#my-listings" class="nav-tab nav-tab-active"><?php esc_html_e( 'My Listings', 'as-laburda-pwa-app' ); ?></a>
                <?php
                $global_features = $this->main_plugin->get_global_feature_settings();
                if ( ( $global_features['enable_products'] ?? false ) ) : ?>
                    <a href="#products" class="nav-tab"><?php esc_html_e( 'Products', 'as-laburda-pwa-app' ); ?></a>
                <?php endif; ?>
                <?php if ( ( $global_features['enable_events'] ?? false ) ) : ?>
                    <a href="#events" class="nav-tab"><?php esc_html_e( 'Events', 'as-laburda-pwa-app' ); ?></a>
                <?php endif; ?>
                <?php if ( ( $global_features['enable_coupons'] ?? false ) ) : ?>
                    <a href="#coupons" class="nav-tab"><?php esc_html_e( 'Coupons', 'as-laburda-pwa-app' ); ?></a>
                <?php endif; ?>
                <?php if ( ( $global_features['enable_notifications'] ?? false ) ) : ?>
                    <a href="#notifications" class="nav-tab"><?php esc_html_e( 'Notifications', 'as-laburda-pwa-app' ); ?></a>
                <?php endif; ?>
            </h3>

            <div id="my-listings" class="tab-content active">
                <h3><?php esc_html_e( 'Manage Your Business Listings', 'as-laburda-pwa-app' ); ?></h3>
                <div id="aslp-listing-message" class="aslp-message"></div>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Business Name', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Status', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Current Plan', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $user_listings ) ) : ?>
                            <?php foreach ( $user_listings as $listing ) :
                                $plan = $listing->current_plan_id ? $this->listing_plans_manager->get_plan_by_id( $listing->current_plan_id ) : null;
                                ?>
                                <tr>
                                    <td><?php echo esc_html( $listing->listing_title ); ?></td>
                                    <td><?php echo esc_html( ucfirst( $listing->status ) ); ?></td>
                                    <td><?php echo esc_html( $plan ? $plan->plan_name : __( 'No Plan', 'as-laburda-pwa-app' ) ); ?></td>
                                    <td>
                                        <a href="<?php echo esc_url( add_query_arg( array( 'action' => 'edit', 'listing_id' => $listing->id ), get_permalink( get_the_ID() ) ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Edit', 'as-laburda-pwa-app' ); ?></a>
                                        <button class="button button-danger aslp-delete-listing" data-id="<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                        <a href="#" class="button button-primary aslp-view-listing-details" data-id="<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'View Details', 'as-laburda-pwa-app' ); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="4"><?php esc_html_e( 'No business listings found.', 'as-laburda-pwa-app' ); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <p><a href="<?php echo esc_url( add_query_arg( 'action', 'new', get_permalink( get_the_ID() ) ) ); ?>" class="button button-primary"><?php esc_html_e( 'Add New Listing', 'as-laburda-pwa-app' ); ?></a></p>
            </div>

            <?php if ( ( $global_features['enable_products'] ?? false ) ) : ?>
            <div id="products" class="tab-content" style="display:none;">
                <h3><?php esc_html_e( 'Manage Your Products', 'as-laburda-pwa-app' ); ?></h3>
                <div id="aslp-product-message" class="aslp-message"></div>
                <?php $this->render_product_management_section( $user_listings ); ?>
            </div>
            <?php endif; ?>

            <?php if ( ( $global_features['enable_events'] ?? false ) ) : ?>
            <div id="events" class="tab-content" style="display:none;">
                <h3><?php esc_html_e( 'Manage Your Events', 'as-laburda-pwa-app' ); ?></h3>
                <div id="aslp-event-message" class="aslp-message"></div>
                <?php $this->render_event_management_section( $user_listings ); ?>
            </div>
            <?php endif; ?>

            <?php if ( ( $global_features['enable_coupons'] ?? false ) ) : ?>
            <div id="coupons" class="tab-content" style="display:none;">
                <h3><?php esc_html_e( 'Manage Your Coupons', 'as-laburda-pwa-app' ); ?></h3>
                <div id="aslp-coupon-message" class="aslp-message"></div>
                <?php $this->render_coupon_management_section( $user_listings ); ?>
            </div>
            <?php endif; ?>

            <?php if ( ( $global_features['enable_notifications'] ?? false ) ) : ?>
            <div id="notifications" class="tab-content" style="display:none;">
                <h3><?php esc_html_e( 'Send Notifications', 'as-laburda-pwa-app' ); ?></h3>
                <div id="aslp-notification-message" class="aslp-message"></div>
                <?php $this->render_notification_management_section( $user_listings ); ?>
            </div>
            <?php endif; ?>

        </div><!-- .aslp-dashboard-wrap -->

        <!-- Modals for Product, Event, Coupon Editing -->
        <?php $this->render_product_edit_modal(); ?>
        <?php $this->render_event_edit_modal(); ?>
        <?php $this->render_coupon_edit_modal(); ?>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Tab switching logic
                $('.nav-tab-wrapper a').on('click', function(e) {
                    e.preventDefault();
                    $('.nav-tab').removeClass('nav-tab-active');
                    $('.tab-content').hide();
                    $(this).addClass('nav-tab-active');
                    $($(this).attr('href')).show();
                });

                // Activate first tab by default
                if (window.location.hash) {
                    $('.nav-tab-wrapper a[href="' + window.location.hash + '"]').click();
                } else {
                    $('.nav-tab-wrapper a:first').click();
                }

                // Handle delete listing
                $(document).on('click', '.aslp-delete-listing', function() {
                    if (confirm('<?php esc_html_e( 'Are you sure you want to delete this business listing? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                        const listingId = $(this).data('id');
                        const $messageDiv = $('#aslp-listing-message');
                        $messageDiv.empty();

                        $.ajax({
                            url: asLaburdaFeatures.ajaxUrl,
                            type: 'POST',
                            data: {
                                action: 'aslp_submit_business_listing', // Re-use submit for delete by sending status 'deleted'
                                nonce: asLaburdaFeatures.nonces.submit_business_listing,
                                listing_id: listingId,
                                status: 'deleted' // Custom status for deletion
                            },
                            success: function(response) {
                                if (response.success) {
                                    $messageDiv.text(response.data.message).css('color', 'green');
                                    location.reload(); // Reload to update list
                                } else {
                                    $messageDiv.text(response.data.message).css('color', 'red');
                                }
                            },
                            error: function() {
                                $messageDiv.text('<?php esc_html_e( 'An error occurred while deleting the listing.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                            }
                        });
                    }
                });

                // Handle View Listing Details (opens in new tab/window)
                $(document).on('click', '.aslp-view-listing-details', function(e) {
                    e.preventDefault();
                    const listingId = $(this).data('id');
                    // Assuming you have a frontend page to display listings, e.g., using a shortcode like [aslp_display_listing listing_id="X"]
                    // You would replace '/your-listing-display-page/' with the actual URL of that page.
                    const viewUrl = '<?php echo esc_url( home_url( '/your-listing-display-page/' ) ); ?>?listing_id=' + listingId;
                    window.open(viewUrl, '_blank');
                });

                // Global modal close logic
                $('.aslp-close-button').on('click', function() {
                    $(this).closest('.aslp-modal').hide();
                });

                $(window).on('click', function(event) {
                    if ($(event.target).is('.aslp-modal')) {
                        $(event.target).hide();
                    }
                });

                // --- Product Management JS (within dashboard) ---
                function fetchProducts(listingId) {
                    const $productsTableBody = $('#aslp-products-table-' + listingId + ' tbody');
                    $productsTableBody.empty().append('<tr><td colspan="6"><?php esc_html_e('Loading products...', 'as-laburda-pwa-app'); ?></td></tr>');

                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'aslp_get_products_for_business',
                            nonce: asLaburdaFeatures.nonces.get_products_for_business,
                            business_listing_id: listingId
                        },
                        success: function(response) {
                            $productsTableBody.empty();
                            if (response.success && response.data.products.length > 0) {
                                response.data.products.forEach(function(product) {
                                    const row = `
                                        <tr data-product-id="${product.id}">
                                            <td>${product.product_name}</td>
                                            <td>${product.product_type.charAt(0).toUpperCase() + product.product_type.slice(1)}</td>
                                            <td>${asLaburdaFeatures.currency_symbol}${parseFloat(product.price).toFixed(2)}</td>
                                            <td>${product.is_active == 1 ? 'Yes' : 'No'}</td>
                                            <td>
                                                <button class="button button-secondary aslp-edit-product" data-id="${product.id}" data-listing-id="${listingId}"><?php esc_html_e('Edit', 'as-laburda-pwa-app'); ?></button>
                                                <button class="button button-danger aslp-delete-product" data-id="${product.id}"><?php esc_html_e('Delete', 'as-laburda-pwa-app'); ?></button>
                                            </td>
                                        </tr>
                                    `;
                                    $productsTableBody.append(row);
                                });
                            } else {
                                $productsTableBody.append('<tr><td colspan="6"><?php esc_html_e('No products found for this listing.', 'as-laburda-pwa-app'); ?></td></tr>');
                            }
                        },
                        error: function() {
                            $productsTableBody.empty().append('<tr><td colspan="6"><?php esc_html_e('Error loading products.', 'as-laburda-pwa-app'); ?></td></tr>');
                        }
                    });
                }

                // Initial load for first listing's products (if products tab is active)
                $('.aslp-products-container').each(function() {
                    const listingId = $(this).data('listing-id');
                    fetchProducts(listingId);
                });

                // Handle Add Product form submission
                $(document).on('submit', '.aslp-add-product-form', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const listingId = $form.find('[name="business_listing_id"]').val();
                    const $messageDiv = $form.closest('.aslp-products-container').find('.aslp-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);

                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                $form[0].reset();
                                $form.find('.aslp-image-preview').empty(); // Clear image preview
                                fetchProducts(listingId); // Refresh list
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while adding the product.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Edit Product button click (open modal and populate form)
                $(document).on('click', '.aslp-edit-product', function() {
                    const productId = $(this).data('id');
                    const listingId = $(this).data('listing-id');
                    const $modal = $('#aslp-edit-product-modal');
                    const $form = $modal.find('#aslp-edit-product-form');
                    const $messageDiv = $modal.find('.aslp-message');
                    $messageDiv.empty();

                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'aslp_get_product',
                            nonce: asLaburdaFeatures.nonces.get_product,
                            product_id: productId
                        },
                        success: function(response) {
                            if (response.success && response.data.product) {
                                const product = response.data.product;
                                $form.find('#edit_product_id').val(product.id);
                                $form.find('#edit_product_listing_id').val(product.business_listing_id);
                                $form.find('#edit_product_name').val(product.product_name);
                                $form.find('#edit_product_description').val(product.product_description);
                                $form.find('#edit_product_type').val(product.product_type);
                                $form.find('#edit_product_price').val(parseFloat(product.price));
                                $form.find('#edit_product_stock_quantity').val(product.stock_quantity);
                                $form.find('#edit_product_sku').val(product.sku);
                                $form.find('#edit_product_is_active').prop('checked', product.is_active == 1);

                                // Set image preview
                                const $previewDiv = $form.find('.aslp-image-preview');
                                $previewDiv.empty();
                                if (product.image_url) {
                                    $form.find('#edit_product_image_url').val(product.image_url);
                                    $previewDiv.html('<img src="' + product.image_url + '" style="max-width: 100px; height: auto;">');
                                } else {
                                    $form.find('#edit_product_image_url').val('');
                                }

                                $modal.show();
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'Error fetching product for editing.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Edit Product form submission
                $(document).on('submit', '#aslp-edit-product-form', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const productId = $form.find('#edit_product_id').val();
                    const listingId = $form.find('#edit_product_listing_id').val();
                    const $messageDiv = $form.closest('.aslp-modal').find('.aslp-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);
                    formData.set('action', 'aslp_submit_product'); // Use the same submit action
                    formData.set('nonce', asLaburdaFeatures.nonces.submit_product); // Use the same nonce
                    formData.set('product_id', productId); // Ensure product_id is sent for update

                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                $('#aslp-edit-product-modal').hide();
                                fetchProducts(listingId); // Refresh list
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while saving the product.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Delete Product button click
                $(document).on('click', '.aslp-delete-product', function() {
                    if (confirm('<?php esc_html_e( 'Are you sure you want to delete this product? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                        const productId = $(this).data('id');
                        const $productsContainer = $(this).closest('.aslp-products-container');
                        const listingId = $productsContainer.data('listing-id');
                        const $messageDiv = $productsContainer.find('.aslp-message');
                        $messageDiv.empty();

                        $.ajax({
                            url: asLaburdaFeatures.ajaxUrl,
                            type: 'POST',
                            data: {
                                action: 'aslp_delete_product',
                                nonce: asLaburdaFeatures.nonces.delete_product,
                                product_id: productId
                            },
                            success: function(response) {
                                if (response.success) {
                                    $messageDiv.text(response.data.message).css('color', 'green');
                                    fetchProducts(listingId); // Refresh list
                                } else {
                                    $messageDiv.text(response.data.message).css('color', 'red');
                                }
                            },
                            error: function() {
                                $messageDiv.text('<?php esc_html_e( 'An error occurred while deleting the product.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                            }
                        });
                    }
                });

                // --- Event Management JS (within dashboard) ---
                function fetchEvents(listingId) {
                    const $eventsTableBody = $('#aslp-events-table-' + listingId + ' tbody');
                    $eventsTableBody.empty().append('<tr><td colspan="6"><?php esc_html_e('Loading events...', 'as-laburda-pwa-app'); ?></td></tr>');

                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'aslp_get_events_for_business',
                            nonce: asLaburdaFeatures.nonces.get_events_for_business,
                            business_listing_id: listingId
                        },
                        success: function(response) {
                            $eventsTableBody.empty();
                            if (response.success && response.data.events.length > 0) {
                                response.data.events.forEach(function(event) {
                                    const row = `
                                        <tr data-event-id="${event.id}">
                                            <td>${event.event_title}</td>
                                            <td>${event.event_date} ${event.event_time || ''}</td>
                                            <td>${event.location || 'N/A'}</td>
                                            <td>${event.is_active == 1 ? 'Yes' : 'No'}</td>
                                            <td>
                                                <button class="button button-secondary aslp-edit-event" data-id="${event.id}" data-listing-id="${listingId}"><?php esc_html_e('Edit', 'as-laburda-pwa-app'); ?></button>
                                                <button class="button button-danger aslp-delete-event" data-id="${event.id}"><?php esc_html_e('Delete', 'as-laburda-pwa-app'); ?></button>
                                            </td>
                                        </tr>
                                    `;
                                    $eventsTableBody.append(row);
                                });
                            } else {
                                $eventsTableBody.append('<tr><td colspan="6"><?php esc_html_e('No events found for this listing.', 'as-laburda-pwa-app'); ?></td></tr>');
                            }
                        },
                        error: function() {
                            $eventsTableBody.empty().append('<tr><td colspan="6"><?php esc_html_e('Error loading events.', 'as-laburda-pwa-app'); ?></td></tr>');
                        }
                    });
                }

                // Initial load for first listing's events (if events tab is active)
                $('.aslp-events-container').each(function() {
                    const listingId = $(this).data('listing-id');
                    fetchEvents(listingId);
                });

                // Handle Add Event form submission
                $(document).on('submit', '.aslp-add-event-form', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const listingId = $form.find('[name="business_listing_id"]').val();
                    const $messageDiv = $form.closest('.aslp-events-container').find('.aslp-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);

                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                $form[0].reset();
                                $form.find('.aslp-image-preview').empty(); // Clear image preview
                                fetchEvents(listingId); // Refresh list
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while adding the event.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Edit Event button click (open modal and populate form)
                $(document).on('click', '.aslp-edit-event', function() {
                    const eventId = $(this).data('id');
                    const listingId = $(this).data('listing-id');
                    const $modal = $('#aslp-edit-event-modal');
                    const $form = $modal.find('#aslp-edit-event-form');
                    const $messageDiv = $modal.find('.aslp-message');
                    $messageDiv.empty();

                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'aslp_get_event',
                            nonce: asLaburdaFeatures.nonces.get_event,
                            event_id: eventId
                        },
                        success: function(response) {
                            if (response.success && response.data.event) {
                                const event = response.data.event;
                                $form.find('#edit_event_id').val(event.id);
                                $form.find('#edit_event_listing_id').val(event.business_listing_id);
                                $form.find('#edit_event_title').val(event.event_title);
                                $form.find('#edit_event_description').val(event.event_description);
                                $form.find('#edit_event_date').val(event.event_date);
                                $form.find('#edit_event_time').val(event.event_time);
                                $form.find('#edit_event_location').val(event.location);
                                $form.find('#edit_event_ticket_price').val(parseFloat(event.ticket_price));
                                $form.find('#edit_event_external_url').val(event.external_url);
                                $form.find('#edit_event_is_active').prop('checked', event.is_active == 1);

                                // Set image preview
                                const $previewDiv = $form.find('.aslp-image-preview');
                                $previewDiv.empty();
                                if (event.image_url) {
                                    $form.find('#edit_event_image_url').val(event.image_url);
                                    $previewDiv.html('<img src="' + event.image_url + '" style="max-width: 100px; height: auto;">');
                                } else {
                                    $form.find('#edit_event_image_url').val('');
                                }

                                $modal.show();
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'Error fetching event for editing.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Edit Event form submission
                $(document).on('submit', '#aslp-edit-event-form', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const eventId = $form.find('#edit_event_id').val();
                    const listingId = $form.find('#edit_event_listing_id').val();
                    const $messageDiv = $form.closest('.aslp-modal').find('.aslp-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);
                    formData.set('action', 'aslp_submit_event'); // Use the same submit action
                    formData.set('nonce', asLaburdaFeatures.nonces.submit_event); // Use the same nonce
                    formData.set('event_id', eventId); // Ensure event_id is sent for update

                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                $('#aslp-edit-event-modal').hide();
                                fetchEvents(listingId); // Refresh list
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while saving the event.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Delete Event button click
                $(document).on('click', '.aslp-delete-event', function() {
                    if (confirm('<?php esc_html_e( 'Are you sure you want to delete this event? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                        const eventId = $(this).data('id');
                        const $eventsContainer = $(this).closest('.aslp-events-container');
                        const listingId = $eventsContainer.data('listing-id');
                        const $messageDiv = $eventsContainer.find('.aslp-message');
                        $messageDiv.empty();

                        $.ajax({
                            url: asLaburdaFeatures.ajaxUrl,
                            type: 'POST',
                            data: {
                                action: 'aslp_delete_event',
                                nonce: asLaburdaFeatures.nonces.delete_event,
                                event_id: eventId
                            },
                            success: function(response) {
                                if (response.success) {
                                    $messageDiv.text(response.data.message).css('color', 'green');
                                    fetchEvents(listingId); // Refresh list
                                } else {
                                    $messageDiv.text(response.data.message).css('color', 'red');
                                }
                            },
                            error: function() {
                                $messageDiv.text('<?php esc_html_e( 'An error occurred while deleting the event.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                            }
                        });
                    }
                });

                // --- Coupon Management JS (within dashboard) ---
                function fetchCoupons(listingId) {
                    const $couponsTableBody = $('#aslp-coupons-table-' + listingId + ' tbody');
                    $couponsTableBody.empty().append('<tr><td colspan="6"><?php esc_html_e('Loading coupons...', 'as-laburda-pwa-app'); ?></td></tr>');

                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'aslp_get_coupons_for_business',
                            nonce: asLaburdaFeatures.nonces.get_coupons_for_business,
                            business_listing_id: listingId
                        },
                        success: function(response) {
                            $couponsTableBody.empty();
                            if (response.success && response.data.coupons.length > 0) {
                                response.data.coupons.forEach(function(coupon) {
                                    const row = `
                                        <tr data-coupon-id="${coupon.id}">
                                            <td>${coupon.coupon_code}</td>
                                            <td>${coupon.coupon_title}</td>
                                            <td>${coupon.discount_type.charAt(0).toUpperCase() + coupon.discount_type.slice(1)}</td>
                                            <td>${asLaburdaFeatures.currency_symbol}${parseFloat(coupon.coupon_amount).toFixed(2)}</td>
                                            <td>${coupon.expiry_date || 'N/A'}</td>
                                            <td>${coupon.is_active == 1 ? 'Yes' : 'No'}</td>
                                            <td>
                                                <button class="button button-secondary aslp-edit-coupon" data-id="${coupon.id}" data-listing-id="${listingId}"><?php esc_html_e('Edit', 'as-laburda-pwa-app'); ?></button>
                                                <button class="button button-danger aslp-delete-coupon" data-id="${coupon.id}"><?php esc_html_e('Delete', 'as-laburda-pwa-app'); ?></button>
                                            </td>
                                        </tr>
                                    `;
                                    $couponsTableBody.append(row);
                                });
                            } else {
                                $couponsTableBody.append('<tr><td colspan="6"><?php esc_html_e('No coupons found for this listing.', 'as-laburda-pwa-app'); ?></td></tr>');
                            }
                        },
                        error: function() {
                            $couponsTableBody.empty().append('<tr><td colspan="6"><?php esc_html_e('Error loading coupons.', 'as-laburda-pwa-app'); ?></td></tr>');
                        }
                    });
                }

                // Initial load for first listing's coupons (if coupons tab is active)
                $('.aslp-coupons-container').each(function() {
                    const listingId = $(this).data('listing-id');
                    fetchCoupons(listingId);
                });

                // Handle Add Coupon form submission
                $(document).on('submit', '.aslp-add-coupon-form', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const listingId = $form.find('[name="business_listing_id"]').val();
                    const $messageDiv = $form.closest('.aslp-coupons-container').find('.aslp-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);

                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                $form[0].reset();
                                fetchCoupons(listingId); // Refresh list
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while adding the coupon.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Edit Coupon button click (open modal and populate form)
                $(document).on('click', '.aslp-edit-coupon', function() {
                    const couponId = $(this).data('id');
                    const listingId = $(this).data('listing-id');
                    const $modal = $('#aslp-edit-coupon-modal');
                    const $form = $modal.find('#aslp-edit-coupon-form');
                    const $messageDiv = $modal.find('.aslp-message');
                    $messageDiv.empty();

                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'aslp_get_coupon',
                            nonce: asLaburdaFeatures.nonces.get_coupon,
                            coupon_id: couponId
                        },
                        success: function(response) {
                            if (response.success && response.data.coupon) {
                                const coupon = response.data.coupon;
                                $form.find('#edit_coupon_id').val(coupon.id);
                                $form.find('#edit_coupon_listing_id').val(coupon.business_listing_id);
                                $form.find('#edit_coupon_code').val(coupon.coupon_code);
                                $form.find('#edit_coupon_title').val(coupon.coupon_title);
                                $form.find('#edit_coupon_description').val(coupon.coupon_description);
                                $form.find('#edit_discount_type').val(coupon.discount_type);
                                $form.find('#edit_coupon_amount').val(parseFloat(coupon.coupon_amount));
                                $form.find('#edit_expiry_date').val(coupon.expiry_date);
                                $form.find('#edit_usage_limit').val(coupon.usage_limit);
                                $form.find('#edit_coupon_is_active').prop('checked', coupon.is_active == 1);

                                $modal.show();
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'Error fetching coupon for editing.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Edit Coupon form submission
                $(document).on('submit', '#aslp-edit-coupon-form', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const couponId = $form.find('#edit_coupon_id').val();
                    const listingId = $form.find('#edit_coupon_listing_id').val();
                    const $messageDiv = $form.closest('.aslp-modal').find('.aslp-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);
                    formData.set('action', 'aslp_submit_coupon'); // Use the same submit action
                    formData.set('nonce', asLaburdaFeatures.nonces.submit_coupon); // Use the same nonce
                    formData.set('coupon_id', couponId); // Ensure coupon_id is sent for update

                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                $('#aslp-edit-coupon-modal').hide();
                                fetchCoupons(listingId); // Refresh list
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while saving the coupon.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Delete Coupon button click
                $(document).on('click', '.aslp-delete-coupon', function() {
                    if (confirm('<?php esc_html_e( 'Are you sure you want to delete this coupon? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                        const couponId = $(this).data('id');
                        const $couponsContainer = $(this).closest('.aslp-coupons-container');
                        const listingId = $couponsContainer.data('listing-id');
                        const $messageDiv = $couponsContainer.find('.aslp-message');
                        $messageDiv.empty();

                        $.ajax({
                            url: asLaburdaFeatures.ajaxUrl,
                            type: 'POST',
                            data: {
                                action: 'aslp_delete_coupon',
                                nonce: asLaburdaFeatures.nonces.delete_coupon,
                                coupon_id: couponId
                            },
                            success: function(response) {
                                if (response.success) {
                                    $messageDiv.text(response.data.message).css('color', 'green');
                                    fetchCoupons(listingId); // Refresh list
                                } else {
                                    $messageDiv.text(response.data.message).css('color', 'red');
                                }
                            },
                            error: function() {
                                $messageDiv.text('<?php esc_html_e( 'An error occurred while deleting the coupon.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                            }
                        });
                    }
                });

                // --- Notification Management JS (within dashboard) ---
                $(document).on('submit', '.aslp-send-notification-form', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const listingId = $form.find('[name="business_listing_id"]').val();
                    const $messageDiv = $form.closest('.aslp-notifications-container').find('.aslp-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);
                    formData.set('action', 'aslp_send_business_notification');
                    formData.set('nonce', asLaburdaFeatures.nonces.send_business_notification); // Ensure this nonce is added to asLaburdaFeatures

                    $.ajax({
                        url: asLaburdaFeatures.ajaxUrl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                $form[0].reset();
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while sending the notification.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

            }); // End jQuery(document).ready

            // Function to handle media uploader for products/events (reusable)
            function aslpHandleMediaUpload(targetInputId) {
                const $targetInput = jQuery('#' + targetInputId);
                const $previewDiv = $targetInput.siblings('.aslp-image-preview');

                let mediaUploader;
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media({
                    title: '<?php esc_html_e( 'Choose Image', 'as-laburda-pwa-app' ); ?>',
                    button: {
                        text: '<?php esc_html_e( 'Choose Image', 'as-laburda-pwa-app' ); ?>'
                    },
                    multiple: false
                });

                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    $targetInput.val(attachment.url);
                    $previewDiv.html('<img src="' + attachment.url + '" style="max-width: 100px; height: auto;">');
                });

                mediaUploader.open();
            }
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders the product management section for a business listing.
     *
     * @since 1.0.0
     * @param array $user_listings List of business listings for the current user.
     */
    private function render_product_management_section( $user_listings ) {
        if ( empty( $user_listings ) ) {
            echo '<p>' . esc_html__( 'Please create a business listing first to manage products.', 'as-laburda-pwa-app' ) . '</p>';
            return;
        }
        ?>
        <?php foreach ( $user_listings as $listing ) :
            $effective_features = $this->main_plugin->get_listing_plans_manager()->get_listing_effective_features( $listing->id );
            if ( ! ( $effective_features['enable_products'] ?? false ) ) {
                echo '<div class="aslp-feature-disabled-message">';
                echo '<p><strong>' . esc_html( $listing->listing_title ) . ':</strong> ' . esc_html__( 'Product management is not available with your current plan.', 'as-laburda-pwa-app' ) . '</p>';
                echo '</div>';
                continue;
            }
            ?>
            <div class="aslp-products-container" data-listing-id="<?php echo esc_attr( $listing->id ); ?>">
                <h4><?php printf( esc_html__( 'Products for: %s', 'as-laburda-pwa-app' ), esc_html( $listing->listing_title ) ); ?></h4>
                <div class="aslp-message"></div>

                <h5><?php esc_html_e( 'Add New Product', 'as-laburda-pwa-app' ); ?></h5>
                <form class="aslp-add-product-form">
                    <input type="hidden" name="action" value="aslp_submit_product">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'aslp_submit_product_nonce' ); ?>">
                    <input type="hidden" name="business_listing_id" value="<?php echo esc_attr( $listing->id ); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="product_name_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Product Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="product_name_<?php echo esc_attr( $listing->id ); ?>" name="product_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="product_description_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="product_description_<?php echo esc_attr( $listing->id ); ?>" name="product_description" rows="3" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="product_type_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Product Type', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="product_type_<?php echo esc_attr( $listing->id ); ?>" name="product_type">
                                    <option value="physical"><?php esc_html_e( 'Physical', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="digital"><?php esc_html_e( 'Digital', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="service"><?php esc_html_e( 'Service', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="product_price_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Price', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" step="0.01" id="product_price_<?php echo esc_attr( $listing->id ); ?>" name="price" value="0.00" class="small-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="product_image_url_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Image URL', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="url" id="product_image_url_<?php echo esc_attr( $listing->id ); ?>" name="image_url" class="regular-text">
                                <button type="button" class="button" onclick="aslpHandleMediaUpload('product_image_url_<?php echo esc_attr( $listing->id ); ?>')"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="product_stock_quantity_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Stock Quantity', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" id="product_stock_quantity_<?php echo esc_attr( $listing->id ); ?>" name="stock_quantity" value="0" min="0" class="small-text"></td>
                        </tr>
                        <tr>
                            <th><label for="product_sku_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'SKU', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="product_sku_<?php echo esc_attr( $listing->id ); ?>" name="sku" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="product_is_active_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Is Active', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="checkbox" id="product_is_active_<?php echo esc_attr( $listing->id ); ?>" name="is_active" value="1" checked></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Add Product', 'as-laburda-pwa-app' ); ?>">
                    </p>
                </form>

                <hr>

                <h5><?php esc_html_e( 'Existing Products', 'as-laburda-pwa-app' ); ?></h5>
                <table class="wp-list-table widefat fixed striped" id="aslp-products-table-<?php echo esc_attr( $listing->id ); ?>">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Name', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Type', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Price', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Active', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Products will be loaded here via AJAX -->
                        <tr><td colspan="5"><?php esc_html_e('Loading products...', 'as-laburda-pwa-app'); ?></td></tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
        <?php
    }

    /**
     * Renders the event management section for a business listing.
     *
     * @since 1.0.0
     * @param array $user_listings List of business listings for the current user.
     */
    private function render_event_management_section( $user_listings ) {
        if ( empty( $user_listings ) ) {
            echo '<p>' . esc_html__( 'Please create a business listing first to manage events.', 'as-laburda-pwa-app' ) . '</p>';
            return;
        }
        ?>
        <?php foreach ( $user_listings as $listing ) :
            $effective_features = $this->main_plugin->get_listing_plans_manager()->get_listing_effective_features( $listing->id );
            if ( ! ( $effective_features['enable_events'] ?? false ) ) {
                echo '<div class="aslp-feature-disabled-message">';
                echo '<p><strong>' . esc_html( $listing->listing_title ) . ':</strong> ' . esc_html__( 'Event management is not available with your current plan.', 'as-laburda-pwa-app' ) . '</p>';
                echo '</div>';
                continue;
            }
            ?>
            <div class="aslp-events-container" data-listing-id="<?php echo esc_attr( $listing->id ); ?>">
                <h4><?php printf( esc_html__( 'Events for: %s', 'as-laburda-pwa-app' ), esc_html( $listing->listing_title ) ); ?></h4>
                <div class="aslp-message"></div>

                <h5><?php esc_html_e( 'Add New Event', 'as-laburda-pwa-app' ); ?></h5>
                <form class="aslp-add-event-form">
                    <input type="hidden" name="action" value="aslp_submit_event">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'aslp_submit_event_nonce' ); ?>">
                    <input type="hidden" name="business_listing_id" value="<?php echo esc_attr( $listing->id ); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="event_title_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Event Title', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="event_title_<?php echo esc_attr( $listing->id ); ?>" name="event_title" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="event_description_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="event_description_<?php echo esc_attr( $listing->id ); ?>" name="event_description" rows="3" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="event_date_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Event Date', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="date" id="event_date_<?php echo esc_attr( $listing->id ); ?>" name="event_date" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="event_time_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Event Time', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="time" id="event_time_<?php echo esc_attr( $listing->id ); ?>" name="event_time" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="event_location_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Location', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="event_location_<?php echo esc_attr( $listing->id ); ?>" name="location" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="event_image_url_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Image URL', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="url" id="event_image_url_<?php echo esc_attr( $listing->id ); ?>" name="image_url" class="regular-text">
                                <button type="button" class="button" onclick="aslpHandleMediaUpload('event_image_url_<?php echo esc_attr( $listing->id ); ?>')"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="event_ticket_price_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Ticket Price', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" step="0.01" id="event_ticket_price_<?php echo esc_attr( $listing->id ); ?>" name="ticket_price" value="0.00" min="0" class="small-text"></td>
                        </tr>
                        <tr>
                            <th><label for="event_external_url_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'External URL (e.g., ticketing)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="url" id="event_external_url_<?php echo esc_attr( $listing->id ); ?>" name="external_url" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="event_is_active_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Is Active', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="checkbox" id="event_is_active_<?php echo esc_attr( $listing->id ); ?>" name="is_active" value="1" checked></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Add Event', 'as-laburda-pwa-app' ); ?>">
                    </p>
                </form>

                <hr>

                <h5><?php esc_html_e( 'Existing Events', 'as-laburda-pwa-app' ); ?></h5>
                <table class="wp-list-table widefat fixed striped" id="aslp-events-table-<?php echo esc_attr( $listing->id ); ?>">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Title', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Date & Time', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Location', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Active', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Events will be loaded here via AJAX -->
                        <tr><td colspan="5"><?php esc_html_e('Loading events...', 'as-laburda-pwa-app'); ?></td></tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
        <?php
    }

    /**
     * Renders the coupon management section for a business listing.
     *
     * @since 1.0.0
     * @param array $user_listings List of business listings for the current user.
     */
    private function render_coupon_management_section( $user_listings ) {
        if ( empty( $user_listings ) ) {
            echo '<p>' . esc_html__( 'Please create a business listing first to manage coupons.', 'as-laburda-pwa-app' ) . '</p>';
            return;
        }
        ?>
        <?php foreach ( $user_listings as $listing ) :
            $effective_features = $this->main_plugin->get_listing_plans_manager()->get_listing_effective_features( $listing->id );
            if ( ! ( $effective_features['enable_coupons'] ?? false ) ) {
                echo '<div class="aslp-feature-disabled-message">';
                echo '<p><strong>' . esc_html( $listing->listing_title ) . ':</strong> ' . esc_html__( 'Coupon management is not available with your current plan.', 'as-laburda-pwa-app' ) . '</p>';
                echo '</div>';
                continue;
            }
            ?>
            <div class="aslp-coupons-container" data-listing-id="<?php echo esc_attr( $listing->id ); ?>">
                <h4><?php printf( esc_html__( 'Coupons for: %s', 'as-laburda-pwa-app' ), esc_html( $listing->listing_title ) ); ?></h4>
                <div class="aslp-message"></div>

                <h5><?php esc_html_e( 'Add New Coupon', 'as-laburda-pwa-app' ); ?></h5>
                <form class="aslp-add-coupon-form">
                    <input type="hidden" name="action" value="aslp_submit_coupon">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'aslp_submit_coupon_nonce' ); ?>">
                    <input type="hidden" name="business_listing_id" value="<?php echo esc_attr( $listing->id ); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="coupon_code_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Coupon Code', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="coupon_code_<?php echo esc_attr( $listing->id ); ?>" name="coupon_code" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="coupon_title_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Coupon Title', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="coupon_title_<?php echo esc_attr( $listing->id ); ?>" name="coupon_title" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="coupon_description_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="coupon_description_<?php echo esc_attr( $listing->id ); ?>" name="coupon_description" rows="3" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="discount_type_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Discount Type', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="discount_type_<?php echo esc_attr( $listing->id ); ?>" name="discount_type">
                                    <option value="fixed_cart"><?php esc_html_e( 'Fixed Cart Discount', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="percentage"><?php esc_html_e( 'Percentage Discount', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="fixed_product"><?php esc_html_e( 'Fixed Product Discount', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="coupon_amount_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Coupon Amount', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" step="0.01" id="coupon_amount_<?php echo esc_attr( $listing->id ); ?>" name="coupon_amount" value="0.00" class="small-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="expiry_date_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Expiry Date', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="date" id="expiry_date_<?php echo esc_attr( $listing->id ); ?>" name="expiry_date" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="usage_limit_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Usage Limit (0 for unlimited)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" id="usage_limit_<?php echo esc_attr( $listing->id ); ?>" name="usage_limit" value="0" min="0" class="small-text"></td>
                        </tr>
                        <tr>
                            <th><label for="coupon_is_active_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Is Active', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="checkbox" id="coupon_is_active_<?php echo esc_attr( $listing->id ); ?>" name="is_active" value="1" checked></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Add Coupon', 'as-laburda-pwa-app' ); ?>">
                    </p>
                </form>

                <hr>

                <h5><?php esc_html_e( 'Existing Coupons', 'as-laburda-pwa-app' ); ?></h5>
                <table class="wp-list-table widefat fixed striped" id="aslp-coupons-table-<?php echo esc_attr( $listing->id ); ?>">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Code', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Title', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Type', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Amount', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Expiry', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Active', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Coupons will be loaded here via AJAX -->
                        <tr><td colspan="7"><?php esc_html_e('Loading coupons...', 'as-laburda-pwa-app'); ?></td></tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
        <?php
    }

    /**
     * Renders the notification management section for a business listing.
     *
     * @since 1.0.0
     * @param array $user_listings List of business listings for the current user.
     */
    private function render_notification_management_section( $user_listings ) {
        if ( empty( $user_listings ) ) {
            echo '<p>' . esc_html__( 'Please create a business listing first to send notifications.', 'as-laburda-pwa-app' ) . '</p>';
            return;
        }
        ?>
        <?php foreach ( $user_listings as $listing ) :
            $effective_features = $this->main_plugin->get_listing_plans_manager()->get_listing_effective_features( $listing->id );
            if ( ! ( $effective_features['can_send_notifications'] ?? false ) ) {
                echo '<div class="aslp-feature-disabled-message">';
                echo '<p><strong>' . esc_html( $listing->listing_title ) . ':</strong> ' . esc_html__( 'Notification sending is not available with your current plan.', 'as-laburda-pwa-app' ) . '</p>';
                echo '</div>';
                continue;
            }
            ?>
            <div class="aslp-notifications-container" data-listing-id="<?php echo esc_attr( $listing->id ); ?>">
                <h4><?php printf( esc_html__( 'Send Notifications for: %s', 'as-laburda-pwa-app' ), esc_html( $listing->listing_title ) ); ?></h4>
                <div class="aslp-message"></div>

                <form class="aslp-send-notification-form">
                    <input type="hidden" name="business_listing_id" value="<?php echo esc_attr( $listing->id ); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="notification_title_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Notification Title', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="notification_title_<?php echo esc_attr( $listing->id ); ?>" name="notification_title" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="notification_content_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Notification Content', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="notification_content_<?php echo esc_attr( $listing->id ); ?>" name="notification_content" rows="5" class="large-text" required></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="notification_type_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Notification Type', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="notification_type_<?php echo esc_attr( $listing->id ); ?>" name="notification_type">
                                    <option value="general"><?php esc_html_e( 'General Announcement', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="promotion"><?php esc_html_e( 'Promotion/Offer', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="event"><?php esc_html_e( 'Event Update', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="target_users_<?php echo esc_attr( $listing->id ); ?>"><?php esc_html_e( 'Target Audience', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="target_users_<?php echo esc_attr( $listing->id ); ?>" name="target_users">
                                    <option value="all"><?php esc_html_e( 'All Subscribed Users', 'as-laburda-pwa-app' ); ?></option>
                                    <!-- Potentially add options for specific user groups later -->
                                </select>
                                <p class="description"><?php esc_html_e( 'Currently, notifications are sent to all users subscribed to this business.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Send Notification', 'as-laburda-pwa-app' ); ?>">
                    </p>
                </form>
                <hr>
                <h5><?php esc_html_e( 'Sent Notifications History', 'as-laburda-pwa-app' ); ?></h5>
                <p><?php esc_html_e( 'History of sent notifications will be displayed here.', 'as-laburda-pwa-app' ); ?></p>
                <!-- You would fetch and display sent notifications here via AJAX -->
            </div>
        <?php endforeach; ?>
        <?php
    }

    /**
     * Renders the product edit modal.
     *
     * @since 1.0.0
     */
    private function render_product_edit_modal() {
        ?>
        <div id="aslp-edit-product-modal" class="aslp-modal" style="display:none;">
            <div class="aslp-modal-content">
                <span class="aslp-close-button">&times;</span>
                <h3><?php esc_html_e( 'Edit Product', 'as-laburda-pwa-app' ); ?></h3>
                <div class="aslp-message"></div>
                <form id="aslp-edit-product-form">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <input type="hidden" name="business_listing_id" id="edit_product_listing_id">
                    <table class="form-table">
                        <tr>
                            <th><label for="edit_product_name"><?php esc_html_e( 'Product Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="edit_product_name" name="product_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="edit_product_description"><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="edit_product_description" name="product_description" rows="3" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="edit_product_type"><?php esc_html_e( 'Product Type', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="edit_product_type" name="product_type">
                                    <option value="physical"><?php esc_html_e( 'Physical', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="digital"><?php esc_html_e( 'Digital', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="service"><?php esc_html_e( 'Service', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_product_price"><?php esc_html_e( 'Price', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" step="0.01" id="edit_product_price" name="price" class="small-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="edit_product_image_url"><?php esc_html_e( 'Image URL', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="url" id="edit_product_image_url" name="image_url" class="regular-text">
                                <button type="button" class="button" onclick="aslpHandleMediaUpload('edit_product_image_url')"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_product_stock_quantity"><?php esc_html_e( 'Stock Quantity', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" id="edit_product_stock_quantity" name="stock_quantity" value="0" min="0" class="small-text"></td>
                        </tr>
                        <tr>
                            <th><label for="edit_product_sku"><?php esc_html_e( 'SKU', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="edit_product_sku" name="sku" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="edit_product_is_active"><?php esc_html_e( 'Is Active', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="checkbox" id="edit_product_is_active" name="is_active" value="1"></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'as-laburda-pwa-app' ); ?>">
                    </p>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Renders the event edit modal.
     *
     * @since 1.0.0
     */
    private function render_event_edit_modal() {
        ?>
        <div id="aslp-edit-event-modal" class="aslp-modal" style="display:none;">
            <div class="aslp-modal-content">
                <span class="aslp-close-button">&times;</span>
                <h3><?php esc_html_e( 'Edit Event', 'as-laburda-pwa-app' ); ?></h3>
                <div class="aslp-message"></div>
                <form id="aslp-edit-event-form">
                    <input type="hidden" name="event_id" id="edit_event_id">
                    <input type="hidden" name="business_listing_id" id="edit_event_listing_id">
                    <table class="form-table">
                        <tr>
                            <th><label for="edit_event_title"><?php esc_html_e( 'Event Title', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="edit_event_title" name="event_title" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="edit_event_description"><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="edit_event_description" name="event_description" rows="3" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="edit_event_date"><?php esc_html_e( 'Event Date', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="date" id="edit_event_date" name="event_date" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="edit_event_time"><?php esc_html_e( 'Event Time', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="time" id="edit_event_time" name="event_time" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="edit_event_location"><?php esc_html_e( 'Location', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="edit_event_location" name="location" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="edit_event_image_url"><?php esc_html_e( 'Image URL', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="url" id="edit_event_image_url" name="image_url" class="regular-text">
                                <button type="button" class="button" onclick="aslpHandleMediaUpload('edit_event_image_url')"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_event_ticket_price"><?php esc_html_e( 'Ticket Price', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" step="0.01" id="edit_event_ticket_price" name="ticket_price" value="0.00" min="0" class="small-text"></td>
                        </tr>
                        <tr>
                            <th><label for="edit_event_external_url"><?php esc_html_e( 'External URL (e.g., ticketing)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="url" id="edit_event_external_url" name="external_url" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="edit_event_is_active"><?php esc_html_e( 'Is Active', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="checkbox" id="edit_event_is_active" name="is_active" value="1"></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'as-laburda-pwa-app' ); ?>">
                    </p>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Renders the coupon edit modal.
     *
     * @since 1.0.0
     */
    private function render_coupon_edit_modal() {
        ?>
        <div id="aslp-edit-coupon-modal" class="aslp-modal" style="display:none;">
            <div class="aslp-modal-content">
                <span class="aslp-close-button">&times;</span>
                <h3><?php esc_html_e( 'Edit Coupon', 'as-laburda-pwa-app' ); ?></h3>
                <div class="aslp-message"></div>
                <form id="aslp-edit-coupon-form">
                    <input type="hidden" name="coupon_id" id="edit_coupon_id">
                    <input type="hidden" name="business_listing_id" id="edit_coupon_listing_id">
                    <table class="form-table">
                        <tr>
                            <th><label for="edit_coupon_code"><?php esc_html_e( 'Coupon Code', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="edit_coupon_code" name="coupon_code" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="edit_coupon_title"><?php esc_html_e( 'Coupon Title', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="edit_coupon_title" name="coupon_title" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="edit_coupon_description"><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="edit_coupon_description" name="coupon_description" rows="3" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="edit_discount_type"><?php esc_html_e( 'Discount Type', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="edit_discount_type" name="discount_type">
                                    <option value="fixed_cart"><?php esc_html_e( 'Fixed Cart Discount', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="percentage"><?php esc_html_e( 'Percentage Discount', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="fixed_product"><?php esc_html_e( 'Fixed Product Discount', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_coupon_amount"><?php esc_html_e( 'Coupon Amount', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" step="0.01" id="edit_coupon_amount" name="coupon_amount" class="small-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="edit_expiry_date"><?php esc_html_e( 'Expiry Date', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="date" id="edit_expiry_date" name="expiry_date" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="edit_usage_limit"><?php esc_html_e( 'Usage Limit (0 for unlimited)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="number" id="edit_usage_limit" name="usage_limit" value="0" min="0" class="small-text"></td>
                        </tr>
                        <tr>
                            <th><label for="edit_coupon_is_active"><?php esc_html_e( 'Is Active', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="checkbox" id="edit_coupon_is_active" name="is_active" value="1"></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'as-laburda-pwa-app' ); ?>">
                    </p>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Render the user dashboard. (NEW)
     * This dashboard would show user's subscribed businesses, notifications, etc.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render_user_dashboard( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . esc_html__( 'You must be logged in to access your user dashboard.', 'as-laburda-pwa-app' ) . '</p>';
        }

        $current_user_id = get_current_user_id();
        $subscribed_businesses = $this->main_plugin->get_notifications_manager()->get_user_subscribed_businesses( $current_user_id );

        ob_start();
        ?>
        <div class="aslp-user-dashboard-wrap">
            <h2><?php esc_html_e( 'Your User Dashboard', 'as-laburda-pwa-app' ); ?></h2>

            <h3 class="nav-tab-wrapper">
                <a href="#my-subscriptions" class="nav-tab nav-tab-active"><?php esc_html_e( 'My Subscriptions', 'as-laburda-pwa-app' ); ?></a>
                <a href="#my-notifications" class="nav-tab"><?php esc_html_e( 'My Notifications', 'as-laburda-pwa-app' ); ?></a>
                <a href="#my-apps" class="nav-tab"><?php esc_html_e( 'My Apps', 'as-laburda-pwa-app' ); ?></a>
            </h3>

            <div id="my-subscriptions" class="tab-content active">
                <h3><?php esc_html_e( 'Businesses You Follow', 'as-laburda-pwa-app' ); ?></h3>
                <?php if ( ! empty( $subscribed_businesses ) ) : ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Business Name', 'as-laburda-pwa-app' ); ?></th>
                                <th><?php esc_html_e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $subscribed_businesses as $business ) : ?>
                                <tr>
                                    <td><?php echo esc_html( $business->listing_title ); ?></td>
                                    <td>
                                        <button class="button button-secondary aslp-toggle-subscription" data-business-id="<?php echo esc_attr( $business->id ); ?>" data-subscribed="1"><?php esc_html_e( 'Unsubscribe', 'as-laburda-pwa-app' ); ?></button>
                                        <a href="<?php echo esc_url( get_permalink( $business->id ) ); ?>" class="button button-primary"><?php esc_html_e( 'View Listing', 'as-laburda-pwa-app' ); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p><?php esc_html_e( 'You are not subscribed to notifications from any businesses yet.', 'as-laburda-pwa-app' ); ?></p>
                <?php endif; ?>
            </div>

            <div id="my-notifications" class="tab-content" style="display:none;">
                <h3><?php esc_html_e( 'Your Notifications', 'as-laburda-pwa-app' ); ?></h3>
                <p><?php esc_html_e( 'Notifications from businesses you follow will appear here.', 'as-laburda-pwa-app' ); ?></p>
                <!-- You would fetch and display user-specific notifications here -->
            </div>

            <div id="my-apps" class="tab-content" style="display:none;">
                <h3><?php esc_html_e( 'Your Apps', 'as-laburda-pwa-app' ); ?></h3>
                <p><?php esc_html_e( 'If you have created any PWA apps, they will be listed here.', 'as-laburda-pwa-app' ); ?></p>
                <?php
                // Display apps created by this user (if applicable)
                $user_apps = $this->database->wpdb->get_results( $this->database->wpdb->prepare( "SELECT * FROM {$this->database->wpdb->prefix}aslp_apps WHERE user_id = %d ORDER BY date_created DESC", $current_user_id ) );
                if ( ! empty( $user_apps ) ) {
                    echo '<table class="wp-list-table widefat fixed striped">';
                    echo '<thead><tr><th>' . esc_html__('App Name', 'as-laburda-pwa-app') . '</th><th>' . esc_html__('Actions', 'as-laburda-pwa-app') . '</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($user_apps as $app) {
                        echo '<tr>';
                        echo '<td>' . esc_html($app->app_name) . '</td>';
                        echo '<td>';
                        echo '<a href="' . esc_url( admin_url( 'admin.php?page=as-laburda-pwa-app&app_id=' . $app->id ) ) . '" class="button button-secondary">' . esc_html__('Edit App (Admin)', 'as-laburda-pwa-app') . '</a> ';
                        echo '<a href="' . esc_url( home_url( '/?app_id=' . $app->id ) ) . '" target="_blank" class="button button-primary">' . esc_html__('View App', 'as-laburda-pwa-app') . '</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                } else {
                    echo '<p>' . esc_html__('You have not created any PWA apps yet.', 'as-laburda-pwa-app') . '</p>';
                }
                ?>
            </div>

        </div><!-- .aslp-user-dashboard-wrap -->

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Tab switching logic for user dashboard
                $('.aslp-user-dashboard-wrap .nav-tab-wrapper a').on('click', function(e) {
                    e.preventDefault();
                    $('.aslp-user-dashboard-wrap .nav-tab').removeClass('nav-tab-active');
                    $('.aslp-user-dashboard-wrap .tab-content').hide();
                    $(this).addClass('nav-tab-active');
                    $($(this).attr('href')).show();
                });

                // Activate first tab by default
                if (window.location.hash) {
                    $('.aslp-user-dashboard-wrap .nav-tab-wrapper a[href="' + window.location.hash + '"]').click();
                } else {
                    $('.aslp-user-dashboard-wrap .nav-tab-wrapper a:first').click();
                }

                // Handle toggle subscription
                $(document).on('click', '.aslp-toggle-subscription', function() {
                    const $button = $(this);
                    const businessId = $button.data('business-id');
                    const isSubscribed = $button.data('subscribed');
                    const newStatus = isSubscribed ? 0 : 1;
                    const actionText = isSubscribed ? 'Unsubscribing...' : 'Subscribing...';
                    const confirmMessage = isSubscribed ? '<?php esc_html_e( 'Are you sure you want to unsubscribe from this business?', 'as-laburda-pwa-app' ); ?>' : '<?php esc_html_e( 'Are you sure you want to subscribe to this business?', 'as-laburda-pwa-app' ); ?>';

                    if (confirm(confirmMessage)) {
                        $button.text(actionText).prop('disabled', true);
                        $.ajax({
                            url: asLaburdaFeatures.ajaxUrl,
                            type: 'POST',
                            data: {
                                action: 'aslp_toggle_business_notification',
                                nonce: asLaburdaFeatures.nonces.toggle_business_notification, // Ensure this nonce is added to asLaburdaFeatures
                                business_listing_id: businessId,
                                is_subscribed: newStatus
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert(response.data.message); // Use alert for simplicity in this example
                                    $button.data('subscribed', newStatus);
                                    $button.text(newStatus ? '<?php esc_html_e( 'Unsubscribe', 'as-laburda-pwa-app' ); ?>' : '<?php esc_html_e( 'Subscribe', 'as-laburda-pwa-app' ); ?>');
                                } else {
                                    alert(response.data.message);
                                }
                                $button.prop('disabled', false);
                            },
                            error: function() {
                                alert('<?php esc_html_e( 'An error occurred.', 'as-laburda-pwa-app' ); ?>');
                                $button.text(isSubscribed ? '<?php esc_html_e( 'Unsubscribe', 'as-laburda-pwa-app' ); ?>' : '<?php esc_html_e( 'Subscribe', 'as-laburda-pwa-app' ); ?>').prop('disabled', false);
                            }
                        });
                    }
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Render the app creator dashboard. (NEW)
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render_app_creator_dashboard( $atts ) {
        if ( ! current_user_can( 'aslp_create_apps' ) ) {
            return '<p>' . esc_html__( 'You do not have permission to access the app creator dashboard.', 'as-laburda-pwa-app' ) . '</p>';
        }

        ob_start();
        ?>
        <div class="aslp-app-creator-dashboard-wrap">
            <h2><?php esc_html_e( 'App Creator Dashboard', 'as-laburda-pwa-app' ); ?></h2>

            <h3 class="nav-tab-wrapper">
                <a href="#manage-apps" class="nav-tab nav-tab-active"><?php esc_html_e( 'Manage My Apps', 'as-laburda-pwa-app' ); ?></a>
                <a href="#create-new-app" class="nav-tab"><?php esc_html_e( 'Create New App', 'as-laburda-pwa-app' ); ?></a>
                <a href="#app-templates" class="nav-tab"><?php esc_html_e( 'App Templates', 'as-laburda-pwa-app' ); ?></a>
                <a href="#app-menus" class="nav-tab"><?php esc_html_e( 'App Menus', 'as-laburda-pwa-app' ); ?></a>
            </h3>

            <div id="manage-apps" class="tab-content active">
                <h3><?php esc_html_e( 'Your Created PWA Apps', 'as-laburda-pwa-app' ); ?></h3>
                <div id="aslp-app-message" class="aslp-message"></div>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'App Name', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'App ID', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $user_apps = $this->main_plugin->get_database_manager()->wpdb->get_results(
                            $this->main_plugin->get_database_manager()->wpdb->prepare(
                                "SELECT * FROM {$this->main_plugin->get_database_manager()->wpdb->prefix}aslp_apps WHERE user_id = %d ORDER BY date_created DESC",
                                get_current_user_id()
                            )
                        );
                        if ( ! empty( $user_apps ) ) : ?>
                            <?php foreach ( $user_apps as $app ) : ?>
                                <tr>
                                    <td><?php echo esc_html( $app->app_name ); ?></td>
                                    <td><?php echo esc_html( $app->id ); ?></td>
                                    <td>
                                        <button class="button button-secondary aslp-edit-app-settings" data-app-id="<?php echo esc_attr( $app->id ); ?>"><?php esc_html_e( 'Edit Settings', 'as-laburda-pwa-app' ); ?></button>
                                        <a href="<?php echo esc_url( home_url( '/as-laburda-manifest-' . $app->id . '.json' ) ); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'View Manifest', 'as-laburda-pwa-app' ); ?></a>
                                        <a href="<?php echo esc_url( home_url( '/as-laburda-sw-' . $app->id . '.js' ) ); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'View Service Worker', 'as-laburda-pwa-app' ); ?></a>
                                        <a href="<?php echo esc_url( home_url( '/?app_id=' . $app->id ) ); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'View App', 'as-laburda-pwa-app' ); ?></a>
                                        <button class="button button-danger aslp-delete-app" data-app-id="<?php echo esc_attr( $app->id ); ?>"><?php esc_html_e( 'Delete', 'as-laburda-pwa-app' ); ?></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="3"><?php esc_html_e( 'No apps created yet. Use the "Create New App" tab to get started!', 'as-laburda-pwa-app' ); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div id="create-new-app" class="tab-content" style="display:none;">
                <h3><?php esc_html_e( 'Create a New PWA App', 'as-laburda-pwa-app' ); ?></h3>
                <div id="aslp-create-app-message" class="aslp-message"></div>
                <form id="aslp-add-app-form">
                    <input type="hidden" name="action" value="as_laburda_add_app">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'as_laburda_add_app_nonce' ); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="app_name"><?php esc_html_e( 'App Name', 'as-laburda-pwa-app' ); ?> <span class="required">*</span></label></th>
                            <td><input type="text" id="app_name" name="app_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="short_name"><?php esc_html_e( 'Short Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="short_name" name="short_name" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="description"><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="description" name="description" rows="3" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="start_url"><?php esc_html_e( 'Start URL', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="url" id="start_url" name="start_url" class="regular-text" value="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <p class="description"><?php esc_html_e( 'The URL that is loaded when the app is launched.', 'as-laburda-pwa-app' ); ?></p></td>
                        </tr>
                        <tr>
                            <th><label for="theme_color"><?php esc_html_e( 'Theme Color', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="color" id="theme_color" name="theme_color" value="#ffffff">
                            <p class="description"><?php esc_html_e( 'The default theme color for the application.', 'as-laburda-pwa-app' ); ?></p></td>
                        </tr>
                        <tr>
                            <th><label for="background_color"><?php esc_html_e( 'Background Color', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="color" id="background_color" name="background_color" value="#ffffff">
                            <p class="description"><?php esc_html_e( 'The background color of the splash screen when the app is launched.', 'as-laburda-pwa-app' ); ?></p></td>
                        </tr>
                        <tr>
                            <th><label for="display_mode"><?php esc_html_e( 'Display Mode', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="display_mode" name="display_mode">
                                    <option value="standalone"><?php esc_html_e( 'Standalone', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="fullscreen"><?php esc_html_e( 'Fullscreen', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="minimal-ui"><?php esc_html_e( 'Minimal UI', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="browser"><?php esc_html_e( 'Browser', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="orientation"><?php esc_html_e( 'Orientation', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="orientation" name="orientation">
                                    <option value="portrait"><?php esc_html_e( 'Portrait', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="landscape"><?php esc_html_e( 'Landscape', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="any"><?php esc_html_e( 'Any', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="icon_192"><?php esc_html_e( 'Icon (192x192px)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="url" id="icon_192" name="icon_192" class="regular-text">
                                <button type="button" class="button aslp-upload-button" data-target="icon_192"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="icon_512"><?php esc_html_e( 'Icon (512x512px)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="url" id="icon_512" name="icon_512" class="regular-text">
                                <button type="button" class="button aslp-upload-button" data-target="icon_512"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="splash_screen"><?php esc_html_e( 'Splash Screen Image', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="url" id="splash_screen" name="splash_screen" class="regular-text">
                                <button type="button" class="button aslp-upload-button" data-target="splash_screen"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="offline_page_id"><?php esc_html_e( 'Offline Page', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <?php
                                wp_dropdown_pages( array(
                                    'name'              => 'offline_page_id',
                                    'id'                => 'offline_page_id',
                                    'show_option_none'  => __( '— Select —', 'as-laburda-pwa-app' ),
                                    'option_none_value' => '0',
                                    'selected'          => 0, // No default selection for new app
                                ) );
                                ?>
                                <button type="button" class="button aslp-create-page-button" data-page-type="offline"><?php esc_html_e( 'Create New Offline Page', 'as-laburda-pwa-app' ); ?></button>
                                <p class="description"><?php esc_html_e( 'Page to display when offline.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="dashboard_page_id"><?php esc_html_e( 'Dashboard Page', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <?php
                                wp_dropdown_pages( array(
                                    'name'              => 'dashboard_page_id',
                                    'id'                => 'dashboard_page_id',
                                    'show_option_none'  => __( '— Select —', 'as-laburda-pwa-app' ),
                                    'option_none_value' => '0',
                                    'selected'          => 0, // No default selection for new app
                                ) );
                                ?>
                                <button type="button" class="button aslp-create-page-button" data-page-type="dashboard"><?php esc_html_e( 'Create New Dashboard Page', 'as-laburda-pwa-app' ); ?></button>
                                <p class="description"><?php esc_html_e( 'Page for app users/business owners to access their content.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="login_page_id"><?php esc_html_e( 'Login Page', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <?php
                                wp_dropdown_pages( array(
                                    'name'              => 'login_page_id',
                                    'id'                => 'login_page_id',
                                    'show_option_none'  => __( '— Select —', 'as-laburda-pwa-app' ),
                                    'option_none_value' => '0',
                                    'selected'          => 0, // No default selection for new app
                                ) );
                                ?>
                                <button type="button" class="button aslp-create-page-button" data-page-type="login"><?php esc_html_e( 'Create New Login Page', 'as-laburda-pwa-app' ); ?></button>
                                <p class="description"><?php esc_html_e( 'Page for app users to log in.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Features', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <label for="enable_push_notifications">
                                    <input type="checkbox" id="enable_push_notifications" name="enable_push_notifications" value="1">
                                    <?php esc_html_e( 'Enable Push Notifications', 'as-laburda-pwa-app' ); ?>
                                </label><br>
                                <label for="enable_persistent_storage">
                                    <input type="checkbox" id="enable_persistent_storage" name="enable_persistent_storage" value="1">
                                    <?php esc_html_e( 'Enable Persistent Storage (for offline data)', 'as-laburda-pwa-app' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="desktop_template_option"><?php esc_html_e( 'Desktop App Template', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="desktop_template_option" name="desktop_template_option">
                                    <option value="default"><?php esc_html_e( 'Default', 'as-laburda-pwa-app' ); ?></option>
                                    <?php
                                    $templates = $this->main_plugin->get_templates_manager()->get_app_templates( true );
                                    foreach ( $templates as $template ) {
                                        echo '<option value="' . esc_attr( $template->id ) . '">' . esc_html( $template->template_name ) . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="mobile_template_option"><?php esc_html_e( 'Mobile App Template', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="mobile_template_option" name="mobile_template_option">
                                    <option value="default"><?php esc_html_e( 'Default', 'as-laburda-pwa-app' ); ?></option>
                                    <?php
                                    foreach ( $templates as $template ) {
                                        echo '<option value="' . esc_attr( $template->id ) . '">' . esc_html( $template->template_name ) . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="app_menu_option"><?php esc_html_e( 'App Menu', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="app_menu_option" name="app_menu_option">
                                    <option value="default"><?php esc_html_e( 'Default WordPress Menu', 'as-laburda-pwa-app' ); ?></option>
                                    <?php
                                    $menus = $this->main_plugin->get_menus_manager()->get_app_menus( true );
                                    foreach ( $menus as $menu ) {
                                        echo '<option value="' . esc_attr( $menu->id ) . '">' . esc_html( $menu->menu_name ) . '</option>';
                                    }
                                    ?>
                                </select>
                                <p class="description"><?php esc_html_e( 'Choose a custom menu for your PWA app or use the default WordPress menu.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" name="submit_new_app" id="submit_new_app" class="button button-primary" value="<?php esc_html_e( 'Create App', 'as-laburda-pwa-app' ); ?>">
                    </p>
                </form>
            </div>

            <div id="app-templates" class="tab-content" style="display:none;">
                <h3><?php esc_html_e( 'Manage App Templates', 'as-laburda-pwa-app' ); ?></h3>
                <p><?php esc_html_e( 'Create and manage reusable templates for your PWA apps.', 'as-laburda-pwa-app' ); ?></p>
                <div id="aslp-template-message" class="aslp-message"></div>

                <h5><?php esc_html_e( 'Add New Template', 'as-laburda-pwa-app' ); ?></h5>
                <form id="aslp-add-template-form">
                    <input type="hidden" name="action" value="aslp_save_app_template">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'aslp_save_app_template_nonce' ); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="template_name"><?php esc_html_e( 'Template Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="template_name" name="template_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="template_description"><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="template_description" name="description" rows="3" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="template_preview_image"><?php esc_html_e( 'Preview Image URL', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="url" id="template_preview_image" name="preview_image" class="regular-text">
                                <button type="button" class="button aslp-upload-button" data-target="template_preview_image"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="template_data"><?php esc_html_e( 'Template Data (JSON)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <textarea id="template_data" name="template_data" rows="10" cols="70" class="large-text code" placeholder='<?php esc_attr_e( '{"css": ".my-class { color: red; }", "html_structure": "<div class=\"my-template\">...</div>"}', 'as-laburda-pwa-app' ); ?>'></textarea>
                                <p class="description"><?php esc_html_e( 'Define the structure, styles, and default settings for this template in JSON format.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="template_is_active"><?php esc_html_e( 'Is Active', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="checkbox" id="template_is_active" name="is_active" value="1" checked></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Add Template', 'as-laburda-pwa-app' ); ?>">
                    </p>
                </form>

                <hr>

                <h5><?php esc_html_e( 'Existing App Templates', 'as-laburda-pwa-app' ); ?></h5>
                <table class="wp-list-table widefat fixed striped" id="aslp-app-templates-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Name', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Active', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Templates will be loaded here via AJAX -->
                        <tr><td colspan="4"><?php esc_html_e('Loading templates...', 'as-laburda-pwa-app'); ?></td></tr>
                    </tbody>
                </table>
            </div>

            <div id="app-menus" class="tab-content" style="display:none;">
                <h3><?php esc_html_e( 'Manage App Menus', 'as-laburda-pwa-app' ); ?></h3>
                <p><?php esc_html_e( 'Create and manage custom navigation menus for your PWA apps.', 'as-laburda-pwa-app' ); ?></p>
                <div id="aslp-menu-message" class="aslp-message"></div>

                <h5><?php esc_html_e( 'Add New Menu', 'as-laburda-pwa-app' ); ?></h5>
                <form id="aslp-add-menu-form">
                    <input type="hidden" name="action" value="aslp_save_app_menu">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'aslp_save_app_menu_nonce' ); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="menu_name"><?php esc_html_e( 'Menu Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="menu_name" name="menu_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="menu_description"><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="menu_description" name="description" rows="3" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="menu_items"><?php esc_html_e( 'Menu Items (JSON)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <textarea id="menu_items" name="menu_items" rows="10" cols="70" class="large-text code" placeholder='<?php esc_attr_e( '[{"label": "Home", "url": "/", "icon": "home"}, {"label": "About", "url": "/about", "icon": "info"}]', 'as-laburda-pwa-app' ); ?>'></textarea>
                                <p class="description"><?php esc_html_e( 'Define menu items as a JSON array. Each item should have a label, URL, and optional icon.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="menu_is_active"><?php esc_html_e( 'Is Active', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="checkbox" id="menu_is_active" name="is_active" value="1" checked></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Add Menu', 'as-laburda-pwa-app' ); ?>">
                    </p>
                </form>

                <hr>

                <h5><?php esc_html_e( 'Existing App Menus', 'as-laburda-pwa-app' ); ?></h5>
                <table class="wp-list-table widefat fixed striped" id="aslp-app-menus-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Name', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Active', 'as-laburda-pwa-app' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'as-laburda-pwa-app' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Menus will be loaded here via AJAX -->
                        <tr><td colspan="4"><?php esc_html_e('Loading menus...', 'as-laburda-pwa-app'); ?></td></tr>
                    </tbody>
                </table>
            </div>

        </div><!-- .aslp-app-creator-dashboard-wrap -->

        <!-- Modals for App Settings, Template, Menu Editing -->
        <div id="aslp-edit-app-settings-modal" class="aslp-modal" style="display:none;">
            <div class="aslp-modal-content">
                <span class="aslp-close-button">&times;</span>
                <h3><?php esc_html_e( 'Edit App Settings', 'as-laburda-pwa-app' ); ?></h3>
                <div class="aslp-message"></div>
                <form id="aslp-edit-app-settings-form">
                    <input type="hidden" name="app_id" id="edit_app_id">
                    <input type="hidden" name="action" value="as_laburda_save_app_settings">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'as_laburda_save_app_settings_nonce' ); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="edit_app_name"><?php esc_html_e( 'App Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="edit_app_name" name="app_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="edit_short_name"><?php esc_html_e( 'Short Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="edit_short_name" name="short_name" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="edit_description"><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="edit_description" name="description" rows="3" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="edit_start_url"><?php esc_html_e( 'Start URL', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="url" id="edit_start_url" name="start_url" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="edit_theme_color"><?php esc_html_e( 'Theme Color', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="color" id="edit_theme_color" name="theme_color"></td>
                        </tr>
                        <tr>
                            <th><label for="edit_background_color"><?php esc_html_e( 'Background Color', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="color" id="edit_background_color" name="background_color"></td>
                        </tr>
                        <tr>
                            <th><label for="edit_display_mode"><?php esc_html_e( 'Display Mode', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="edit_display_mode" name="display_mode">
                                    <option value="standalone"><?php esc_html_e( 'Standalone', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="fullscreen"><?php esc_html_e( 'Fullscreen', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="minimal-ui"><?php esc_html_e( 'Minimal UI', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="browser"><?php esc_html_e( 'Browser', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_orientation"><?php esc_html_e( 'Orientation', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="edit_orientation" name="orientation">
                                    <option value="portrait"><?php esc_html_e( 'Portrait', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="landscape"><?php esc_html_e( 'Landscape', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="any"><?php esc_html_e( 'Any', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_icon_192"><?php esc_html_e( 'Icon (192x192px)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="url" id="edit_icon_192" name="icon_192" class="regular-text">
                                <button type="button" class="button aslp-upload-button" data-target="edit_icon_192"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_icon_512"><?php esc_html_e( 'Icon (512x512px)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="url" id="edit_icon_512" name="icon_512" class="regular-text">
                                <button type="button" class="button aslp-upload-button" data-target="edit_icon_512"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_splash_screen"><?php esc_html_e( 'Splash Screen Image', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="url" id="edit_splash_screen" name="splash_screen" class="regular-text">
                                <button type="button" class="button aslp-upload-button" data-target="edit_splash_screen"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_offline_page_id"><?php esc_html_e( 'Offline Page', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <?php
                                wp_dropdown_pages( array(
                                    'name'              => 'offline_page_id',
                                    'id'                => 'edit_offline_page_id',
                                    'show_option_none'  => __( '— Select —', 'as-laburda-pwa-app' ),
                                    'option_none_value' => '0',
                                    'selected'          => 0, // Will be set by JS
                                ) );
                                ?>
                                <button type="button" class="button aslp-create-page-button" data-page-type="offline"><?php esc_html_e( 'Create New Offline Page', 'as-laburda-pwa-app' ); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_dashboard_page_id"><?php esc_html_e( 'Dashboard Page', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <?php
                                wp_dropdown_pages( array(
                                    'name'              => 'dashboard_page_id',
                                    'id'                => 'edit_dashboard_page_id',
                                    'show_option_none'  => __( '— Select —', 'as-laburda-pwa-app' ),
                                    'option_none_value' => '0',
                                    'selected'          => 0, // Will be set by JS
                                ) );
                                ?>
                                <button type="button" class="button aslp-create-page-button" data-page-type="dashboard"><?php esc_html_e( 'Create New Dashboard Page', 'as-laburda-pwa-app' ); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_login_page_id"><?php esc_html_e( 'Login Page', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <?php
                                wp_dropdown_pages( array(
                                    'name'              => 'login_page_id',
                                    'id'                => 'edit_login_page_id',
                                    'show_option_none'  => __( '— Select —', 'as-laburda-pwa-app' ),
                                    'option_none_value' => '0',
                                    'selected'          => 0, // Will be set by JS
                                ) );
                                ?>
                                <button type="button" class="button aslp-create-page-button" data-page-type="login"><?php esc_html_e( 'Create New Login Page', 'as-laburda-pwa-app' ); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Features', 'as-laburda-pwa-app' ); ?></th>
                            <td>
                                <label for="edit_enable_push_notifications">
                                    <input type="checkbox" id="edit_enable_push_notifications" name="enable_push_notifications" value="1">
                                    <?php esc_html_e( 'Enable Push Notifications', 'as-laburda-pwa-app' ); ?>
                                </label><br>
                                <label for="edit_enable_persistent_storage">
                                    <input type="checkbox" id="edit_enable_persistent_storage" name="enable_persistent_storage" value="1">
                                    <?php esc_html_e( 'Enable Persistent Storage (for offline data)', 'as-laburda-pwa-app' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_desktop_template_option"><?php esc_html_e( 'Desktop App Template', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="edit_desktop_template_option" name="desktop_template_option">
                                    <option value="default"><?php esc_html_e( 'Default', 'as-laburda-pwa-app' ); ?></option>
                                    <?php
                                    $templates = $this->main_plugin->get_templates_manager()->get_app_templates( true );
                                    foreach ( $templates as $template ) {
                                        echo '<option value="' . esc_attr( $template->id ) . '">' . esc_html( $template->template_name ) . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_mobile_template_option"><?php esc_html_e( 'Mobile App Template', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="edit_mobile_template_option" name="mobile_template_option">
                                    <option value="default"><?php esc_html_e( 'Default', 'as-laburda-pwa-app' ); ?></option>
                                    <?php
                                    foreach ( $templates as $template ) {
                                        echo '<option value="' . esc_attr( $template->id ) . '">' . esc_html( $template->template_name ) . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_app_menu_option"><?php esc_html_e( 'App Menu', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="edit_app_menu_option" name="app_menu_option">
                                    <option value="default"><?php esc_html_e( 'Default WordPress Menu', 'as-laburda-pwa-app' ); ?></option>
                                    <?php
                                    $menus = $this->main_plugin->get_menus_manager()->get_app_menus( true );
                                    foreach ( $menus as $menu ) {
                                        echo '<option value="' . esc_attr( $menu->id ) . '">' . esc_html( $menu->menu_name ) . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'as-laburda-pwa-app' ); ?>">
                    </p>
                </form>
            </div>
        </div>

        <div id="aslp-edit-template-modal" class="aslp-modal" style="display:none;">
            <div class="aslp-modal-content">
                <span class="aslp-close-button">&times;</span>
                <h3><?php esc_html_e( 'Edit App Template', 'as-laburda-pwa-app' ); ?></h3>
                <div class="aslp-message"></div>
                <form id="aslp-edit-template-form">
                    <input type="hidden" name="template_id" id="edit_template_id">
                    <input type="hidden" name="action" value="aslp_save_app_template">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'aslp_save_app_template_nonce' ); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="edit_template_name"><?php esc_html_e( 'Template Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="edit_template_name" name="template_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="edit_template_description"><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="edit_template_description" name="description" rows="3" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="edit_template_preview_image"><?php esc_html_e( 'Preview Image URL', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="url" id="edit_template_preview_image" name="preview_image" class="regular-text">
                                <button type="button" class="button aslp-upload-button" data-target="edit_template_preview_image"><?php esc_html_e( 'Upload Image', 'as-laburda-pwa-app' ); ?></button>
                                <div class="aslp-image-preview"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="edit_template_data"><?php esc_html_e( 'Template Data (JSON)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="edit_template_data" name="template_data" rows="10" cols="70" class="large-text code"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="edit_template_is_active"><?php esc_html_e( 'Is Active', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="checkbox" id="edit_template_is_active" name="is_active" value="1"></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'as-laburda-pwa-app' ); ?>">
                    </p>
                </form>
            </div>
        </div>

        <div id="aslp-edit-menu-modal" class="aslp-modal" style="display:none;">
            <div class="aslp-modal-content">
                <span class="aslp-close-button">&times;</span>
                <h3><?php esc_html_e( 'Edit App Menu', 'as-laburda-pwa-app' ); ?></h3>
                <div class="aslp-message"></div>
                <form id="aslp-edit-menu-form">
                    <input type="hidden" name="menu_id" id="edit_menu_id">
                    <input type="hidden" name="action" value="aslp_save_app_menu">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'aslp_save_app_menu_nonce' ); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="edit_menu_name"><?php esc_html_e( 'Menu Name', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="text" id="edit_menu_name" name="menu_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="edit_menu_description"><?php esc_html_e( 'Description', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="edit_menu_description" name="description" rows="3" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="edit_menu_items"><?php esc_html_e( 'Menu Items (JSON)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><textarea id="edit_menu_items" name="menu_items" rows="10" cols="70" class="large-text code"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="edit_menu_is_active"><?php esc_html_e( 'Is Active', 'as-laburda-pwa-app' ); ?></label></th>
                            <td><input type="checkbox" id="edit_menu_is_active" name="is_active" value="1"></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'as-laburda-pwa-app' ); ?>">
                    </p>
                </form>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Tab switching logic for app creator dashboard
                $('.aslp-app-creator-dashboard-wrap .nav-tab-wrapper a').on('click', function(e) {
                    e.preventDefault();
                    $('.aslp-app-creator-dashboard-wrap .nav-tab').removeClass('nav-tab-active');
                    $('.aslp-app-creator-dashboard-wrap .tab-content').hide();
                    $(this).addClass('nav-tab-active');
                    $($(this).attr('href')).show();
                });

                // Activate first tab by default
                if (window.location.hash) {
                    $('.aslp-app-creator-dashboard-wrap .nav-tab-wrapper a[href="' + window.location.hash + '"]').click();
                } else {
                    $('.aslp-app-creator-dashboard-wrap .nav-tab-wrapper a:first').click();
                }

                // Handle Add App form submission
                $('#aslp-add-app-form').on('submit', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const $messageDiv = $('#aslp-create-app-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);

                    $.ajax({
                        url: asLaburdaAdmin.ajax_url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                $form[0].reset();
                                $form.find('.aslp-image-preview').empty();
                                // Optionally, refresh the "Manage My Apps" tab
                                location.reload(); // Simple reload for now
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while creating the app.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Delete App button click
                $(document).on('click', '.aslp-delete-app', function() {
                    if (confirm('<?php esc_html_e( 'Are you sure you want to delete this app? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                        const appId = $(this).data('app-id');
                        const $messageDiv = $('#aslp-app-message');
                        $messageDiv.empty();

                        $.ajax({
                            url: asLaburdaAdmin.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'as_laburda_delete_app',
                                nonce: asLaburdaAdmin.nonces.delete_app,
                                app_id: appId
                            },
                            success: function(response) {
                                if (response.success) {
                                    $messageDiv.text(response.data.message).css('color', 'green');
                                    location.reload(); // Reload to update list
                                } else {
                                    $messageDiv.text(response.data.message).css('color', 'red');
                                }
                            },
                            error: function() {
                                $messageDiv.text('<?php esc_html_e( 'An error occurred while deleting the app.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                            }
                        });
                    }
                });

                // Handle Edit App Settings button click (open modal and populate form)
                $(document).on('click', '.aslp-edit-app-settings', function() {
                    const appId = $(this).data('app-id');
                    const $modal = $('#aslp-edit-app-settings-modal');
                    const $form = $modal.find('#aslp-edit-app-settings-form');
                    const $messageDiv = $modal.find('.aslp-message');
                    $messageDiv.empty();

                    // Fetch app data (this would need an AJAX endpoint in admin.php)
                    // For now, let's simulate or assume data is available
                    const allApps = <?php echo json_encode( $this->main_plugin->get_all_apps() ); ?>;
                    const appData = allApps.find(app => app.id === appId);

                    if (appData) {
                        $form.find('#edit_app_id').val(appData.id);
                        $form.find('#edit_app_name').val(appData.app_name);
                        $form.find('#edit_short_name').val(appData.short_name);
                        $form.find('#edit_description').val(appData.description);
                        $form.find('#edit_start_url').val(appData.start_url);
                        $form.find('#edit_theme_color').val(appData.theme_color);
                        $form.find('#edit_background_color').val(appData.background_color);
                        $form.find('#edit_display_mode').val(appData.display_mode);
                        $form.find('#edit_orientation').val(appData.orientation);
                        $form.find('#edit_offline_page_id').val(appData.offline_page_id);
                        $form.find('#edit_dashboard_page_id').val(appData.dashboard_page_id);
                        $form.find('#edit_login_page_id').val(appData.login_page_id);
                        $form.find('#edit_enable_push_notifications').prop('checked', appData.enable_push_notifications == 1);
                        $form.find('#edit_enable_persistent_storage').prop('checked', appData.enable_persistent_storage == 1);
                        $form.find('#edit_desktop_template_option').val(appData.desktop_template_option);
                        $form.find('#edit_mobile_template_option').val(appData.mobile_template_option);
                        $form.find('#edit_app_menu_option').val(appData.app_menu_option);


                        // Set image previews
                        const icon192Preview = $form.find('#edit_icon_192').siblings('.aslp-image-preview');
                        icon192Preview.empty();
                        if (appData.icon_192) {
                            $form.find('#edit_icon_192').val(appData.icon_192);
                            icon192Preview.html('<img src="' + appData.icon_192 + '" style="max-width: 100px; height: auto;">');
                        } else {
                            $form.find('#edit_icon_192').val('');
                        }

                        const icon512Preview = $form.find('#edit_icon_512').siblings('.aslp-image-preview');
                        icon512Preview.empty();
                        if (appData.icon_512) {
                            $form.find('#edit_icon_512').val(appData.icon_512);
                            icon512Preview.html('<img src="' + appData.icon_512 + '" style="max-width: 100px; height: auto;">');
                        } else {
                            $form.find('#edit_icon_512').val('');
                        }

                        const splashScreenPreview = $form.find('#edit_splash_screen').siblings('.aslp-image-preview');
                        splashScreenPreview.empty();
                        if (appData.splash_screen) {
                            $form.find('#edit_splash_screen').val(appData.splash_screen);
                            splashScreenPreview.html('<img src="' + appData.splash_screen + '" style="max-width: 100px; height: auto;">');
                        } else {
                            $form.find('#edit_splash_screen').val('');
                        }

                        $modal.show();
                    } else {
                        $messageDiv.text('<?php esc_html_e( 'App data not found.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                    }
                });

                // Handle Edit App Settings form submission
                $('#aslp-edit-app-settings-form').on('submit', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const appId = $form.find('#edit_app_id').val();
                    const $messageDiv = $form.closest('.aslp-modal').find('.aslp-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);

                    $.ajax({
                        url: asLaburdaAdmin.ajax_url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                $('#aslp-edit-app-settings-modal').hide();
                                location.reload(); // Reload to update list
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while saving app settings.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Create Page button click
                $(document).on('click', '.aslp-create-page-button', function() {
                    const pageType = $(this).data('page-type');
                    const pageTitle = prompt('<?php esc_html_e( 'Enter a title for the new ', 'as-laburda-pwa-app' ); ?>' + pageType + ' <?php esc_html_e( 'page:', 'as-laburda-pwa-app' ); ?>');

                    if (pageTitle) {
                        $.ajax({
                            url: asLaburdaAdmin.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'as_laburda_create_page',
                                nonce: asLaburdaAdmin.nonces.create_page,
                                page_title: pageTitle,
                                page_content: 'This is the ' + pageType + ' page for your PWA.',
                                page_template: '' // Can specify a template if needed
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert('<?php esc_html_e( 'Page created successfully! ID: ', 'as-laburda-pwa-app' ); ?>' + response.data.page_id);
                                    // Update the dropdown with the new page
                                    const $dropdown = $('#' + (pageType === 'offline' ? 'offline_page_id' : (pageType === 'dashboard' ? 'dashboard_page_id' : 'login_page_id')));
                                    $dropdown.append('<option value="' + response.data.page_id + '" selected>' + pageTitle + '</option>');
                                } else {
                                    alert('<?php esc_html_e( 'Failed to create page: ', 'as-laburda-pwa-app' ); ?>' + response.data.message);
                                }
                            },
                            error: function() {
                                alert('<?php esc_html_e( 'An error occurred while creating the page.', 'as-laburda-pwa-app' ); ?>');
                            }
                        });
                    }
                });

                // --- App Templates Management JS ---
                function fetchAppTemplates() {
                    const $templatesTableBody = $('#aslp-app-templates-table tbody');
                    $templatesTableBody.empty().append('<tr><td colspan="4"><?php esc_html_e('Loading templates...', 'as-laburda-pwa-app'); ?></td></tr>');

                    $.ajax({
                        url: asLaburdaAdmin.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'aslp_get_all_app_templates', // Need to implement this AJAX action
                            nonce: asLaburdaAdmin.nonces.get_all_app_templates // Need to add this nonce
                        },
                        success: function(response) {
                            $templatesTableBody.empty();
                            if (response.success && response.data.templates.length > 0) {
                                response.data.templates.forEach(function(template) {
                                    const row = `
                                        <tr data-template-id="${template.id}">
                                            <td>${template.template_name}</td>
                                            <td>${template.description || 'N/A'}</td>
                                            <td>${template.is_active == 1 ? 'Yes' : 'No'}</td>
                                            <td>
                                                <button class="button button-secondary aslp-edit-template" data-id="${template.id}"><?php esc_html_e('Edit', 'as-laburda-pwa-app'); ?></button>
                                                <button class="button button-danger aslp-delete-template" data-id="${template.id}"><?php esc_html_e('Delete', 'as-laburda-pwa-app'); ?></button>
                                            </td>
                                        </tr>
                                    `;
                                    $templatesTableBody.append(row);
                                });
                            } else {
                                $templatesTableBody.append('<tr><td colspan="4"><?php esc_html_e('No app templates found.', 'as-laburda-pwa-app'); ?></td></tr>');
                            }
                        },
                        error: function() {
                            $templatesTableBody.empty().append('<tr><td colspan="4"><?php esc_html_e('Error loading templates.', 'as-laburda-pwa-app'); ?></td></tr>');
                        }
                    });
                }
                fetchAppTemplates(); // Initial load

                // Handle Add Template form submission
                $('#aslp-add-template-form').on('submit', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const $messageDiv = $form.closest('.tab-content').find('.aslp-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);
                    // Ensure template_data is valid JSON
                    const templateDataVal = $form.find('#template_data').val();
                    try {
                        JSON.parse(templateDataVal);
                        formData.set('template_data', templateDataVal);
                    } catch (e) {
                        $messageDiv.text('<?php esc_html_e( 'Template Data must be valid JSON.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        return;
                    }

                    $.ajax({
                        url: asLaburdaAdmin.ajax_url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                $form[0].reset();
                                $form.find('.aslp-image-preview').empty();
                                fetchAppTemplates(); // Refresh list
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while adding the template.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Edit Template button click
                $(document).on('click', '.aslp-edit-template', function() {
                    const templateId = $(this).data('id');
                    const $modal = $('#aslp-edit-template-modal');
                    const $form = $modal.find('#aslp-edit-template-form');
                    const $messageDiv = $modal.find('.aslp-message');
                    $messageDiv.empty();

                    // Fetch template data (need an AJAX endpoint for this)
                    $.ajax({
                        url: asLaburdaAdmin.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'aslp_get_app_template', // Need to implement this AJAX action
                            nonce: asLaburdaAdmin.nonces.get_app_template, // Need to add this nonce
                            template_id: templateId
                        },
                        success: function(response) {
                            if (response.success && response.data.template) {
                                const template = response.data.template;
                                $form.find('#edit_template_id').val(template.id);
                                $form.find('#edit_template_name').val(template.template_name);
                                $form.find('#edit_template_description').val(template.description);
                                $form.find('#edit_template_preview_image').val(template.preview_image);
                                $form.find('#edit_template_data').val(JSON.stringify(template.template_data, null, 2)); // Pretty print JSON
                                $form.find('#edit_template_is_active').prop('checked', template.is_active == 1);

                                const previewImgDiv = $form.find('#edit_template_preview_image').siblings('.aslp-image-preview');
                                previewImgDiv.empty();
                                if (template.preview_image) {
                                    previewImgDiv.html('<img src="' + template.preview_image + '" style="max-width: 100px; height: auto;">');
                                }

                                $modal.show();
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'Error fetching template for editing.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Edit Template form submission
                $('#aslp-edit-template-form').on('submit', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const $messageDiv = $form.closest('.aslp-modal').find('.aslp-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);
                    const templateDataVal = $form.find('#edit_template_data').val();
                    try {
                        JSON.parse(templateDataVal);
                        formData.set('template_data', templateDataVal);
                    } catch (e) {
                        $messageDiv.text('<?php esc_html_e( 'Template Data must be valid JSON.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        return;
                    }

                    $.ajax({
                        url: asLaburdaAdmin.ajax_url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                $('#aslp-edit-template-modal').hide();
                                fetchAppTemplates(); // Refresh list
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while saving the template.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Delete Template button click
                $(document).on('click', '.aslp-delete-template', function() {
                    if (confirm('<?php esc_html_e( 'Are you sure you want to delete this template? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                        const templateId = $(this).data('id');
                        const $messageDiv = $('#aslp-template-message');
                        $messageDiv.empty();

                        $.ajax({
                            url: asLaburdaAdmin.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'aslp_delete_app_template',
                                nonce: asLaburdaAdmin.nonces.delete_app_template,
                                template_id: templateId
                            },
                            success: function(response) {
                                if (response.success) {
                                    $messageDiv.text(response.data.message).css('color', 'green');
                                    fetchAppTemplates(); // Refresh list
                                } else {
                                    $messageDiv.text(response.data.message).css('color', 'red');
                                }
                            },
                            error: function() {
                                $messageDiv.text('<?php esc_html_e( 'An error occurred while deleting the template.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                            }
                        });
                    }
                });

                // --- App Menus Management JS ---
                function fetchAppMenus() {
                    const $menusTableBody = $('#aslp-app-menus-table tbody');
                    $menusTableBody.empty().append('<tr><td colspan="4"><?php esc_html_e('Loading menus...', 'as-laburda-pwa-app'); ?></td></tr>');

                    $.ajax({
                        url: asLaburdaAdmin.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'aslp_get_all_app_menus', // Need to implement this AJAX action
                            nonce: asLaburdaAdmin.nonces.get_all_app_menus // Need to add this nonce
                        },
                        success: function(response) {
                            $menusTableBody.empty();
                            if (response.success && response.data.menus.length > 0) {
                                response.data.menus.forEach(function(menu) {
                                    const row = `
                                        <tr data-menu-id="${menu.id}">
                                            <td>${menu.menu_name}</td>
                                            <td>${menu.description || 'N/A'}</td>
                                            <td>${menu.is_active == 1 ? 'Yes' : 'No'}</td>
                                            <td>
                                                <button class="button button-secondary aslp-edit-menu" data-id="${menu.id}"><?php esc_html_e('Edit', 'as-laburda-pwa-app'); ?></button>
                                                <button class="button button-danger aslp-delete-menu" data-id="${menu.id}"><?php esc_html_e('Delete', 'as-laburda-pwa-app'); ?></button>
                                            </td>
                                        </tr>
                                    `;
                                    $menusTableBody.append(row);
                                });
                            } else {
                                $menusTableBody.append('<tr><td colspan="4"><?php esc_html_e('No app menus found.', 'as-laburda-pwa-app'); ?></td></tr>');
                            }
                        },
                        error: function() {
                            $menusTableBody.empty().append('<tr><td colspan="4"><?php esc_html_e('Error loading menus.', 'as-laburda-pwa-app'); ?></td></tr>');
                        }
                    });
                }
                fetchAppMenus(); // Initial load

                // Handle Add Menu form submission
                $('#aslp-add-menu-form').on('submit', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const $messageDiv = $form.closest('.tab-content').find('.aslp-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);
                    // Ensure menu_items is valid JSON
                    const menuItemsVal = $form.find('#menu_items').val();
                    try {
                        JSON.parse(menuItemsVal);
                        formData.set('menu_items', menuItemsVal);
                    } catch (e) {
                        $messageDiv.text('<?php esc_html_e( 'Menu Items must be valid JSON.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        return;
                    }

                    $.ajax({
                        url: asLaburdaAdmin.ajax_url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                $form[0].reset();
                                fetchAppMenus(); // Refresh list
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while adding the menu.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Edit Menu button click
                $(document).on('click', '.aslp-edit-menu', function() {
                    const menuId = $(this).data('id');
                    const $modal = $('#aslp-edit-menu-modal');
                    const $form = $modal.find('#aslp-edit-menu-form');
                    const $messageDiv = $modal.find('.aslp-message');
                    $messageDiv.empty();

                    // Fetch menu data (need an AJAX endpoint for this)
                    $.ajax({
                        url: asLaburdaAdmin.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'aslp_get_app_menu', // Need to implement this AJAX action
                            nonce: asLaburdaAdmin.nonces.get_app_menu, // Need to add this nonce
                            menu_id: menuId
                        },
                        success: function(response) {
                            if (response.success && response.data.menu) {
                                const menu = response.data.menu;
                                $form.find('#edit_menu_id').val(menu.id);
                                $form.find('#edit_menu_name').val(menu.menu_name);
                                $form.find('#edit_menu_description').val(menu.description);
                                $form.find('#edit_menu_items').val(JSON.stringify(menu.menu_items, null, 2)); // Pretty print JSON
                                $form.find('#edit_menu_is_active').prop('checked', menu.is_active == 1);

                                $modal.show();
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'Error fetching menu for editing.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Edit Menu form submission
                $('#aslp-edit-menu-form').on('submit', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const $messageDiv = $form.closest('.aslp-modal').find('.aslp-message');
                    $messageDiv.empty();

                    const formData = new FormData(this);
                    const menuItemsVal = $form.find('#edit_menu_items').val();
                    try {
                        JSON.parse(menuItemsVal);
                        formData.set('menu_items', menuItemsVal);
                    } catch (e) {
                        $messageDiv.text('<?php esc_html_e( 'Menu Items must be valid JSON.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        return;
                    }

                    $.ajax({
                        url: asLaburdaAdmin.ajax_url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $messageDiv.text(response.data.message).css('color', 'green');
                                $('#aslp-edit-menu-modal').hide();
                                fetchAppMenus(); // Refresh list
                            } else {
                                $messageDiv.text(response.data.message).css('color', 'red');
                            }
                        },
                        error: function() {
                            $messageDiv.text('<?php esc_html_e( 'An error occurred while saving the menu.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                        }
                    });
                });

                // Handle Delete Menu button click
                $(document).on('click', '.aslp-delete-menu', function() {
                    if (confirm('<?php esc_html_e( 'Are you sure you want to delete this menu? This action cannot be undone.', 'as-laburda-pwa-app' ); ?>')) {
                        const menuId = $(this).data('id');
                        const $messageDiv = $('#aslp-menu-message');
                        $messageDiv.empty();

                        $.ajax({
                            url: asLaburdaAdmin.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'aslp_delete_app_menu',
                                nonce: asLaburdaAdmin.nonces.delete_app_menu,
                                menu_id: menuId
                            },
                            success: function(response) {
                                if (response.success) {
                                    $messageDiv.text(response.data.message).css('color', 'green');
                                    fetchAppMenus(); // Refresh list
                                } else {
                                    $messageDiv.text(response.data.message).css('color', 'red');
                                }
                            },
                            error: function() {
                                $messageDiv.text('<?php esc_html_e( 'An error occurred while deleting the menu.', 'as-laburda-pwa-app' ); ?>').css('color', 'red');
                            }
                        });
                    }
                });


            }); // End jQuery(document).ready
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Render the display listing shortcode.
     * This shortcode will display a single business listing.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render_display_listing( $atts ) {
        $atts = shortcode_atts( array(
            'listing_id' => 0,
        ), $atts, 'aslp_display_listing' );

        $listing_id = absint( $atts['listing_id'] );
        if ( empty( $listing_id ) && isset( $_GET['listing_id'] ) ) {
            $listing_id = absint( $_GET['listing_id'] );
        }

        if ( empty( $listing_id ) ) {
            return '<p>' . esc_html__( 'No listing ID provided.', 'as-laburda-pwa-app' ) . '</p>';
        }

        $listing = $this->database->get_business_listing( $listing_id );

        if ( ! $listing || $listing->status !== 'active' ) {
            return '<p>' . esc_html__( 'Business listing not found or is not active.', 'as-laburda-pwa-app' ) . '</p>';
        }

        // Decode JSON fields for display
        $listing->business_hours = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing->business_hours, true );
        $listing->faq = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing->faq, true );
        $listing->social_links = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing->social_links, true );
        $listing->booking_options = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing->booking_options, true );
        $listing->menu_option = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing->menu_option, true );
        $listing->gallery_images_urls = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing->gallery_images_urls, true );

        ob_start();
        ?>
        <div class="aslp-single-listing-wrap">
            <h1><?php echo esc_html( $listing->listing_title ); ?></h1>
            <?php if ( ! empty( $listing->logo_url ) ) : ?>
                <img src="<?php echo esc_url( $listing->logo_url ); ?>" alt="<?php echo esc_attr( $listing->listing_title ); ?> Logo" class="aslp-listing-logo">
            <?php endif; ?>

            <?php if ( ! empty( $listing->featured_image_url ) ) : ?>
                <img src="<?php echo esc_url( $listing->featured_image_url ); ?>" alt="<?php echo esc_attr( $listing->listing_title ); ?>" class="aslp-listing-featured-image">
            <?php endif; ?>

            <p class="aslp-short-description"><?php echo esc_html( $listing->short_description ); ?></p>
            <div class="aslp-description"><?php echo wp_kses_post( $listing->description ); ?></div>

            <div class="aslp-contact-info">
                <?php if ( ! empty( $listing->address ) ) : ?>
                    <p><strong><?php esc_html_e( 'Address:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( $listing->address ); ?>, <?php echo esc_html( $listing->city ); ?></p>
                <?php endif; ?>
                <?php if ( ! empty( $listing->phone_number ) ) : ?>
                    <p><strong><?php esc_html_e( 'Phone:', 'as-laburda-pwa-app' ); ?></strong> <a href="tel:<?php echo esc_attr( $listing->phone_number ); ?>"><?php echo esc_html( $listing->phone_number ); ?></a></p>
                <?php endif; ?>
                <?php if ( ! empty( $listing->whatsapp_number ) ) : ?>
                    <p><strong><?php esc_html_e( 'WhatsApp:', 'as-laburda-pwa-app' ); ?></strong> <a href="https://wa.me/<?php echo esc_attr( AS_Laburda_PWA_App_Utils::format_whatsapp_number( $listing->whatsapp_number ) ); ?>" target="_blank"><?php echo esc_html( $listing->whatsapp_number ); ?></a></p>
                <?php endif; ?>
                <?php if ( ! empty( $listing->website_url ) ) : ?>
                    <p><strong><?php esc_html_e( 'Website:', 'as-laburda-pwa-app' ); ?></strong> <a href="<?php echo esc_url( $listing->website_url ); ?>" target="_blank"><?php echo esc_url( $listing->website_url ); ?></a></p>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $listing->business_hours ) && is_array( $listing->business_hours ) ) : ?>
                <h3><?php esc_html_e( 'Business Hours', 'as-laburda-pwa-app' ); ?></h3>
                <ul>
                    <?php foreach ( $listing->business_hours as $day => $hours ) : ?>
                        <li><strong><?php echo esc_html( $day ); ?>:</strong> <?php echo esc_html( $hours ); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if ( ! empty( $listing->gallery_images_urls ) && is_array( $listing->gallery_images_urls ) ) : ?>
                <h3><?php esc_html_e( 'Gallery', 'as-laburda-pwa-app' ); ?></h3>
                <div class="aslp-gallery">
                    <?php foreach ( $listing->gallery_images_urls as $img_url ) : ?>
                        <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $listing->listing_title ); ?> Gallery Image">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $listing->youtube_video_id ) ) : ?>
                <h3><?php esc_html_e( 'Video', 'as-laburda-pwa-app' ); ?></h3>
                <div class="aslp-video-embed">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo esc_attr( $listing->youtube_video_id ); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $listing->faq ) && is_array( $listing->faq ) ) : ?>
                <h3><?php esc_html_e( 'Frequently Asked Questions', 'as-laburda-pwa-app' ); ?></h3>
                <div class="aslp-faq">
                    <?php foreach ( $listing->faq as $qa ) : ?>
                        <div class="aslp-faq-item">
                            <h4><?php echo esc_html( $qa['question'] ); ?></h4>
                            <p><?php echo esc_html( $qa['answer'] ); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php
            // Display Products if enabled for this listing's plan
            $effective_features = $this->main_plugin->get_listing_plans_manager()->get_listing_effective_features( $listing->id );
            if ( ( $effective_features['enable_products'] ?? false ) ) :
                $products = $this->products_manager->get_business_products( $listing->id, true ); // Only active products
                if ( ! empty( $products ) ) : ?>
                    <h3><?php esc_html_e( 'Our Products', 'as-laburda-pwa-app' ); ?></h3>
                    <div class="aslp-products-list">
                        <?php foreach ( $products as $product ) : ?>
                            <div class="aslp-product-item">
                                <?php if ( ! empty( $product->image_url ) ) : ?>
                                    <img src="<?php echo esc_url( $product->image_url ); ?>" alt="<?php echo esc_attr( $product->product_name ); ?>">
                                <?php endif; ?>
                                <h4><?php echo esc_html( $product->product_name ); ?></h4>
                                <p class="aslp-product-price"><?php echo AS_Laburda_PWA_App_Utils::format_price( $product->price ); ?></p>
                                <p><?php echo esc_html( $product->product_description ); ?></p>
                                <!-- Add more product details/buttons as needed -->
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif;
            endif; ?>

            <?php
            // Display Events if enabled for this listing's plan
            if ( ( $effective_features['enable_events'] ?? false ) ) :
                $events = $this->events_manager->get_business_events( $listing->id, true ); // Only active and future events
                if ( ! empty( $events ) ) : ?>
                    <h3><?php esc_html_e( 'Upcoming Events', 'as-laburda-pwa-app' ); ?></h3>
                    <div class="aslp-events-list">
                        <?php foreach ( $events as $event ) : ?>
                            <div class="aslp-event-item">
                                <?php if ( ! empty( $event->image_url ) ) : ?>
                                    <img src="<?php echo esc_url( $event->image_url ); ?>" alt="<?php echo esc_attr( $event->event_title ); ?>">
                                <?php endif; ?>
                                <h4><?php echo esc_html( $event->event_title ); ?></h4>
                                <p><strong><?php esc_html_e( 'Date:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $event->event_date ) ) ); ?></p>
                                <?php if ( ! empty( $event->event_time ) ) : ?>
                                    <p><strong><?php esc_html_e( 'Time:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( $event->event_time ); ?></p>
                                <?php endif; ?>
                                <?php if ( ! empty( $event->location ) ) : ?>
                                    <p><strong><?php esc_html_e( 'Location:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( $event->location ); ?></p>
                                <?php endif; ?>
                                <p><?php echo esc_html( $event->event_description ); ?></p>
                                <?php if ( $event->ticket_price > 0 ) : ?>
                                    <p><strong><?php esc_html_e( 'Ticket Price:', 'as-laburda-pwa-app' ); ?></strong> <?php echo AS_Laburda_PWA_App_Utils::format_price( $event->ticket_price ); ?></p>
                                <?php endif; ?>
                                <?php if ( ! empty( $event->external_url ) ) : ?>
                                    <p><a href="<?php echo esc_url( $event->external_url ); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Get Tickets / More Info', 'as-laburda-pwa-app' ); ?></a></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif;
            endif; ?>

            <?php
            // Display Coupons if enabled for this listing's plan
            if ( ( $effective_features['enable_coupons'] ?? false ) ) :
                $coupons = $this->coupons_manager->get_business_coupons( $listing->id, true ); // Only active coupons
                if ( ! empty( $coupons ) ) : ?>
                    <h3><?php esc_html_e( 'Available Coupons', 'as-laburda-pwa-app' ); ?></h3>
                    <div class="aslp-coupons-list">
                        <?php foreach ( $coupons as $coupon ) : ?>
                            <div class="aslp-coupon-item">
                                <h4><?php echo esc_html( $coupon->coupon_title ); ?></h4>
                                <p><?php echo esc_html( $coupon->coupon_description ); ?></p>
                                <p><strong><?php esc_html_e( 'Code:', 'as-laburda-pwa-app' ); ?></strong> <span class="aslp-coupon-code"><?php echo esc_html( $coupon->coupon_code ); ?></span></p>
                                <p><strong><?php esc_html_e( 'Discount:', 'as-laburda-pwa-app' ); ?></strong>
                                    <?php
                                    if ( $coupon->discount_type === 'percentage' ) {
                                        echo esc_html( $coupon->coupon_amount ) . '% ' . esc_html__( 'off', 'as-laburda-pwa-app' );
                                    } else {
                                        echo AS_Laburda_PWA_App_Utils::format_price( $coupon->coupon_amount ) . ' ' . esc_html__( 'discount', 'as-laburda-pwa-app' );
                                    }
                                    ?>
                                </p>
                                <?php if ( ! empty( $coupon->expiry_date ) ) : ?>
                                    <p><strong><?php esc_html_e( 'Expires:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $coupon->expiry_date ) ) ); ?></p>
                                <?php endif; ?>
                                <button class="button button-secondary aslp-copy-coupon" data-code="<?php echo esc_attr( $coupon->coupon_code ); ?>"><?php esc_html_e( 'Copy Code', 'as-laburda-pwa-app' ); ?></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif;
            endif; ?>

            <?php if ( ( $effective_features['can_send_notifications'] ?? false ) && is_user_logged_in() ) :
                $is_subscribed = $this->main_plugin->get_notifications_manager()->is_user_subscribed_to_business( get_current_user_id(), $listing->id );
                ?>
                <h3><?php esc_html_e( 'Notifications', 'as-laburda-pwa-app' ); ?></h3>
                <button class="button button-primary aslp-toggle-subscription" data-business-id="<?php echo esc_attr( $listing->id ); ?>" data-subscribed="<?php echo esc_attr( $is_subscribed ? '1' : '0' ); ?>">
                    <?php echo $is_subscribed ? esc_html__( 'Unsubscribe from Notifications', 'as-laburda-pwa-app' ) : esc_html__( 'Subscribe to Notifications', 'as-laburda-pwa-app' ); ?>
                </button>
            <?php endif; ?>

            <p class="aslp-listing-tags">
                <strong><?php esc_html_e( 'Categories:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( $listing->categories ); ?><br>
                <strong><?php esc_html_e( 'Tags:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( $listing->tags ); ?><br>
                <strong><?php esc_html_e( 'Keywords:', 'as-laburda-pwa-app' ); ?></strong> <?php echo esc_html( $listing->keywords ); ?>
            </p>

            <?php
            // Render custom fields if enabled and configured for business listings
            $global_features = $this->main_plugin->get_global_feature_settings();
            if ( ( $global_features['enable_custom_fields'] ?? false ) ) {
                $custom_fields = $this->database->get_all_custom_fields( 'business_listing', true );
                if ( ! empty( $custom_fields ) ) {
                    echo '<h3>' . esc_html__( 'Additional Details', 'as-laburda-pwa-app' ) . '</h3>';
                    echo '<table class="form-table">';
                    foreach ( $custom_fields as $field ) {
                        $field_value = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing->{$field->field_name} ?? '""' );
                        if ( ! empty( $field_value ) ) {
                            echo '<tr>';
                            echo '<th>' . esc_html( $field->field_label ) . ':</th>';
                            echo '<td>' . esc_html( $field_value ) . '</td>';
                            echo '</tr>';
                        }
                    }
                    echo '</table>';
                }
            }
            ?>

        </div><!-- .aslp-single-listing-wrap -->

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Copy coupon code to clipboard
                $(document).on('click', '.aslp-copy-coupon', function() {
                    const couponCode = $(this).data('code');
                    const $tempInput = $('<input>');
                    $('body').append($tempInput);
                    $tempInput.val(couponCode).select();
                    document.execCommand('copy');
                    $tempInput.remove();
                    alert('<?php esc_html_e( 'Coupon code copied: ', 'as-laburda-pwa-app' ); ?>' + couponCode);
                });

                // Handle toggle subscription (for public listing view)
                $(document).on('click', '.aslp-toggle-subscription', function() {
                    const $button = $(this);
                    const businessId = $button.data('business-id');
                    const isSubscribed = $button.data('subscribed');
                    const newStatus = isSubscribed ? 0 : 1;
                    const actionText = isSubscribed ? '<?php esc_html_e( 'Unsubscribing...', 'as-laburda-pwa-app' ); ?>' : '<?php esc_html_e( 'Subscribing...', 'as-laburda-pwa-app' ); ?>';
                    const confirmMessage = isSubscribed ? '<?php esc_html_e( 'Are you sure you want to unsubscribe from this business?', 'as-laburda-pwa-app' ); ?>' : '<?php esc_html_e( 'Are you sure you want to subscribe to this business?', 'as-laburda-pwa-app' ); ?>';

                    if (confirm(confirmMessage)) {
                        $button.text(actionText).prop('disabled', true);
                        $.ajax({
                            url: asLaburdaFeatures.ajaxUrl,
                            type: 'POST',
                            data: {
                                action: 'aslp_toggle_business_notification',
                                nonce: asLaburdaFeatures.nonces.toggle_business_notification,
                                business_listing_id: businessId,
                                is_subscribed: newStatus
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert(response.data.message);
                                    $button.data('subscribed', newStatus);
                                    $button.text(newStatus ? '<?php esc_html_e( 'Unsubscribe from Notifications', 'as-laburda-pwa-app' ); ?>' : '<?php esc_html_e( 'Subscribe to Notifications', 'as-laburda-pwa-app' ); ?>');
                                } else {
                                    alert(response.data.message);
                                }
                                $button.prop('disabled', false);
                            },
                            error: function() {
                                alert('<?php esc_html_e( 'An error occurred.', 'as-laburda-pwa-app' ); ?>');
                                $button.text(isSubscribed ? '<?php esc_html_e( 'Unsubscribe from Notifications', 'as-laburda-pwa-app' ); ?>' : '<?php esc_html_e( 'Subscribe to Notifications', 'as-laburda-pwa-app' ); ?>').prop('disabled', false);
                            }
                        });
                    }
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }
}