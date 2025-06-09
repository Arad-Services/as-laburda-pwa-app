<?php
/**
 * The admin AI Assistant page for the AS Laburda PWA App plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/admin/partials
 */
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <p><?php _e( 'Utilize the AI Assistant for various tasks including content generation, SEO optimization, and site debugging. Ensure your AI API key is configured in Global Settings.', 'as-laburda-pwa-app' ); ?></p>

    <h2 class="nav-tab-wrapper">
        <a href="#ai-chat" data-tab="chat" class="nav-tab nav-tab-active"><?php _e( 'AI Chat', 'as-laburda-pwa-app' ); ?></a>
        <a href="#ai-seo" data-tab="seo" class="nav-tab"><?php _e( 'SEO Generator', 'as-laburda-pwa-app' ); ?></a>
        <a href="#ai-content" data-tab="content" class="nav-tab"><?php _e( 'Content Creator', 'as-laburda-pwa-app' ); ?></a>
        <a href="#ai-debug" data-tab="debug" class="nav-tab"><?php _e( 'Site Debugger', 'as-laburda-pwa-app' ); ?></a>
    </h2>

    <div id="aslp-ai-assistant-app">
        <div class="aslp-loading-overlay" style="display: none;">
            <div class="aslp-loading-spinner"></div>
            <p><?php _e( 'Processing with AI...', 'as-laburda-pwa-app' ); ?></p>
        </div>

        <div class="aslp-message-area"></div>

        <!-- AI Chat Tab -->
        <div id="ai-chat" class="aslp-tab-content active">
            <h2><?php _e( 'AI Chat', 'as-laburda-pwa-app' ); ?></h2>
            <div class="aslp-ai-chat-interface">
                <div id="ai-chat-log" style="height: 300px; overflow-y: scroll; border: 1px solid #eee; padding: 10px; margin-bottom: 15px; background: #f9f9f9; border-radius: 4px;">
                    <p><strong>AI:</strong> <?php _e( 'Hello! How can I assist you today?', 'as-laburda-pwa-app' ); ?></p>
                </div>
                <textarea id="ai-chat-input" placeholder="<?php _e( 'Type your message here...', 'as-laburda-pwa-app' ); ?>"></textarea>
                <button id="ai-chat-send" class="button button-primary" style="margin-top: 10px;"><i class="fas fa-paper-plane"></i> <?php _e( 'Send Message', 'as-laburda-pwa-app' ); ?></button>
            </div>
        </div>

        <!-- SEO Generator Tab -->
        <div id="ai-seo" class="aslp-tab-content" style="display:none;">
            <h2><?php _e( 'SEO Generator', 'as-laburda-pwa-app' ); ?></h2>
            <p><?php _e( 'Generate SEO meta titles, descriptions, and keywords for your content using AI.', 'as-laburda-pwa-app' ); ?></p>
            <form id="ai-seo-form">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="seo_item_type"><?php _e( 'Content Type', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="seo_item_type" name="item_type" required>
                                    <option value="listing"><?php _e( 'Business Listing', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="product"><?php _e( 'Product', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="event"><?php _e( 'Event', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="app"><?php _e( 'PWA App', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="page"><?php _e( 'WordPress Page', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="post"><?php _e( 'WordPress Post', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="seo_item_id"><?php _e( 'Content ID (Optional)', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <input type="text" id="seo_item_id" name="item_id" class="regular-text" placeholder="<?php _e( 'Enter ID or leave empty for general content', 'as-laburda-pwa-app' ); ?>">
                                <p class="description"><?php _e( 'The ID of the specific listing, product, event, app, page, or post you want to optimize. Leave empty for general content analysis.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="seo_content_to_analyze"><?php _e( 'Content to Analyze', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <textarea id="seo_content_to_analyze" name="content_to_analyze" rows="10" cols="70" class="large-text" placeholder="<?php _e( 'Paste your content here (e.g., product description, listing details, blog post text)', 'as-laburda-pwa-app' ); ?>" required></textarea>
                                <p class="description"><?php _e( 'Provide the main text content for which you want to generate SEO elements.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <button type="submit" id="ai-generate-seo" class="button button-primary"><i class="fas fa-magic"></i> <?php _e( 'Generate SEO', 'as-laburda-pwa-app' ); ?></button>
                </p>
            </form>
            <div id="ai-seo-results" class="aslp-ai-response" style="display:none;">
                <h3><?php _e( 'Generated SEO Data:', 'as-laburda-pwa-app' ); ?></h3>
                <p><strong><?php _e( 'Meta Title:', 'as-laburda-pwa-app' ); ?></strong> <span id="seo-meta-title"></span></p>
                <p><strong><?php _e( 'Meta Description:', 'as-laburda-pwa-app' ); ?></strong> <span id="seo-meta-description"></span></p>
                <p><strong><?php _e( 'Keywords:', 'as-laburda-pwa-app' ); ?></strong> <span id="seo-keywords"></span></p>
            </div>
        </div>

        <!-- Content Creator Tab -->
        <div id="ai-content" class="aslp-tab-content" style="display:none;">
            <h2><?php _e( 'Content Creator', 'as-laburda-pwa-app' ); ?></h2>
            <p><?php _e( 'Generate various types of content using AI, such as product descriptions, blog post drafts, or marketing copy.', 'as-laburda-pwa-app' ); ?></p>
            <form id="ai-content-form">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="content_type"><?php _e( 'Content Type', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <select id="content_type" name="content_type" required>
                                    <option value="product_description"><?php _e( 'Product Description', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="blog_post_draft"><?php _e( 'Blog Post Draft', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="marketing_copy"><?php _e( 'Marketing Copy', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="email_newsletter"><?php _e( 'Email Newsletter', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="social_media_post"><?php _e( 'Social Media Post', 'as-laburda-pwa-app' ); ?></option>
                                    <option value="faq_answers"><?php _e( 'FAQ Answers', 'as-laburda-pwa-app' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="content_prompt"><?php _e( 'Prompt', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <textarea id="content_prompt" name="prompt" rows="10" cols="70" class="large-text" placeholder="<?php _e( 'Describe what kind of content you want to generate (e.g., "A catchy product description for a new vegan protein powder", "A blog post draft about the benefits of PWA apps")', 'as-laburda-pwa-app' ); ?>" required></textarea>
                                <p class="description"><?php _e( 'Be as specific as possible to get the best results.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <button type="submit" id="ai-create-content" class="button button-primary"><i class="fas fa-pen-nib"></i> <?php _e( 'Generate Content', 'as-laburda-pwa-app' ); ?></button>
                </p>
            </form>
            <div id="ai-content-results" class="aslp-ai-response" style="display:none;">
                <h3><?php _e( 'Generated Content:', 'as-laburda-pwa-app' ); ?></h3>
                <div id="generated-content-output"></div>
            </div>
        </div>

        <!-- Site Debugger Tab -->
        <div id="ai-debug" class="aslp-tab-content" style="display:none;">
            <h2><?php _e( 'Site Debugger', 'as-laburda-pwa-app' ); ?></h2>
            <p><?php _e( 'Paste error logs, code snippets, or descriptions of issues, and the AI will attempt to provide insights and solutions.', 'as-laburda-pwa-app' ); ?></p>
            <form id="ai-debug-form">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="debug_info"><?php _e( 'Debug Information', 'as-laburda-pwa-app' ); ?></label></th>
                            <td>
                                <textarea id="debug_info" name="debug_info" rows="15" cols="70" class="large-text code" placeholder="<?php _e( 'Paste error messages, debug logs, relevant code snippets, or a detailed description of the problem you are facing.', 'as-laburda-pwa-app' ); ?>" required></textarea>
                                <p class="description"><?php _e( 'The more context you provide, the better the AI can assist.', 'as-laburda-pwa-app' ); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <button type="submit" id="ai-debug-site" class="button button-primary"><i class="fas fa-bug"></i> <?php _e( 'Analyze & Debug', 'as-laburda-pwa-app' ); ?></button>
                </p>
            </form>
            <div id="ai-debug-results" class="aslp-ai-response" style="display:none;">
                <h3><?php _e( 'AI Debug Report:', 'as-laburda-pwa-app' ); ?></h3>
                <div id="debug-report-output"></div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var aiAssistant = {
        init: function() {
            this.bindTabEvents();
            this.bindFormEvents();
        },

        bindTabEvents: function() {
            $('.nav-tab-wrapper a').on('click', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.aslp-tab-content').hide();
                $('#ai-' + tab).show();
            });
        },

        bindFormEvents: function() {
            // AI Chat
            $('#ai-chat-send').on('click', this.sendChatMessage.bind(this));
            $('#ai-chat-input').on('keypress', function(e) {
                if (e.which === 13 && !e.shiftKey) { // Enter key, but not Shift+Enter
                    e.preventDefault();
                    aiAssistant.sendChatMessage();
                }
            });

            // SEO Generator
            $('#ai-seo-form').on('submit', this.generateSEO.bind(this));

            // Content Creator
            $('#ai-content-form').on('submit', this.createContent.bind(this));

            // Site Debugger
            $('#ai-debug-form').on('submit', this.debugSite.bind(this));
        },

        showLoading: function() {
            $('.aslp-loading-overlay').fadeIn();
        },

        hideLoading: function() {
            $('.aslp-loading-overlay').fadeOut();
        },

        showMessage: function(message, type = 'success') {
            var messageDiv = $('.aslp-message-area');
            messageDiv.html('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            messageDiv.find('.notice').append('<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'as-laburda-pwa-app' ); ?></span></button>');
            messageDiv.find('.notice-dismiss').on('click', function() {
                $(this).closest('.notice').remove();
            });
        },

        clearMessages: function() {
            $('.aslp-message-area').empty();
        },

        // --- AI Chat Functions ---
        sendChatMessage: function() {
            var chatInput = $('#ai-chat-input');
            var message = chatInput.val().trim();
            if (message === '') {
                this.showMessage('<?php _e( 'Please enter a message.', 'as-laburda-pwa-app' ); ?>', 'error');
                return;
            }

            var chatLog = $('#ai-chat-log');
            chatLog.append('<p><strong><?php _e( 'You:', 'as-laburda-pwa-app' ); ?></strong> ' + message + '</p>');
            chatInput.val('');
            chatLog.scrollTop(chatLog[0].scrollHeight); // Scroll to bottom

            this.showLoading();
            this.clearMessages();

            var data = {
                'action': 'aslp_admin_ai_chat',
                'nonce': aslp_ajax_object.nonce,
                'message': message
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                aiAssistant.hideLoading();
                if (response.success) {
                    chatLog.append('<p><strong>AI:</strong> ' + response.data.response + '</p>');
                } else {
                    chatLog.append('<p><strong>AI:</strong> <?php _e( 'Error: Could not get a response. ', 'as-laburda-pwa-app' ); ?>' + response.data.message + '</p>');
                    aiAssistant.showMessage(response.data.message, 'error');
                }
                chatLog.scrollTop(chatLog[0].scrollHeight); // Scroll to bottom again
            }).fail(function() {
                aiAssistant.hideLoading();
                chatLog.append('<p><strong>AI:</strong> <?php _e( 'An AJAX error occurred while communicating with the AI.', 'as-laburda-pwa-app' ); ?></p>');
                aiAssistant.showMessage('<?php _e( 'An AJAX error occurred while communicating with the AI.', 'as-laburda-pwa-app' ); ?>', 'error');
                chatLog.scrollTop(chatLog[0].scrollHeight);
            });
        },

        // --- SEO Generator Functions ---
        generateSEO: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();
            $('#ai-seo-results').hide(); // Hide previous results

            var item_type = $('#seo_item_type').val();
            var item_id = $('#seo_item_id').val();
            var content_to_analyze = $('#seo_content_to_analyze').val();

            if (content_to_analyze.trim() === '') {
                this.hideLoading();
                this.showMessage('<?php _e( 'Please provide content to analyze for SEO.', 'as-laburda-pwa-app' ); ?>', 'error');
                return;
            }

            var data = {
                'action': 'aslp_admin_ai_generate_seo',
                'nonce': aslp_ajax_object.nonce,
                'item_type': item_type,
                'item_id': item_id,
                'content_to_analyze': content_to_analyze
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                aiAssistant.hideLoading();
                if (response.success) {
                    $('#seo-meta-title').text(response.data.seo_data.meta_title || 'N/A');
                    $('#seo-meta-description').text(response.data.seo_data.meta_description || 'N/A');
                    $('#seo-keywords').text(response.data.seo_data.keywords ? response.data.seo_data.keywords.join(', ') : 'N/A');
                    $('#ai-seo-results').show();
                    aiAssistant.showMessage(response.data.message, 'success');
                } else {
                    aiAssistant.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                aiAssistant.hideLoading();
                aiAssistant.showMessage('<?php _e( 'An AJAX error occurred while generating SEO.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        // --- Content Creator Functions ---
        createContent: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();
            $('#ai-content-results').hide(); // Hide previous results
            $('#generated-content-output').empty();

            var content_type = $('#content_type').val();
            var prompt = $('#content_prompt').val();

            if (prompt.trim() === '') {
                this.hideLoading();
                this.showMessage('<?php _e( 'Please provide a prompt for content generation.', 'as-laburda-pwa-app' ); ?>', 'error');
                return;
            }

            var data = {
                'action': 'aslp_admin_ai_create_content',
                'nonce': aslp_ajax_object.nonce,
                'content_type': content_type,
                'prompt': prompt
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                aiAssistant.hideLoading();
                if (response.success) {
                    $('#generated-content-output').html(response.data.content.replace(/\n/g, '<br>')); // Display with line breaks
                    $('#ai-content-results').show();
                    aiAssistant.showMessage(response.data.message, 'success');
                } else {
                    aiAssistant.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                aiAssistant.hideLoading();
                aiAssistant.showMessage('<?php _e( 'An AJAX error occurred while creating content.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        },

        // --- Site Debugger Functions ---
        debugSite: function(e) {
            e.preventDefault();
            this.clearMessages();
            this.showLoading();
            $('#ai-debug-results').hide(); // Hide previous results
            $('#debug-report-output').empty();

            var debug_info = $('#debug_info').val();

            if (debug_info.trim() === '') {
                this.hideLoading();
                this.showMessage('<?php _e( 'Please provide debug information for analysis.', 'as-laburda-pwa-app' ); ?>', 'error');
                return;
            }

            var data = {
                'action': 'aslp_admin_ai_debug_site',
                'nonce': aslp_ajax_object.nonce,
                'debug_info': debug_info
            };

            $.post(aslp_ajax_object.ajax_url, data, function(response) {
                aiAssistant.hideLoading();
                if (response.success) {
                    var report = response.data.report;
                    var reportHtml = '<h4><?php _e( 'Summary:', 'as-laburda-pwa-app' ); ?></h4><p>' + (report.summary || 'N/A') + '</p>';
                    if (report.issues && report.issues.length > 0) {
                        reportHtml += '<h4><?php _e( 'Identified Issues:', 'as-laburda-pwa-app' ); ?></h4><ul>';
                        $.each(report.issues, function(i, issue) {
                            reportHtml += '<li><strong>' + (issue.title || 'N/A') + ':</strong> ' + (issue.description || 'N/A') + '</li>';
                        });
                        reportHtml += '</ul>';
                    }
                    if (report.solutions && report.solutions.length > 0) {
                        reportHtml += '<h4><?php _e( 'Suggested Solutions:', 'as-laburda-pwa-app' ); ?></h4><ol>';
                        $.each(report.solutions, function(i, solution) {
                            reportHtml += '<li>' + solution + '</li>';
                        });
                        reportHtml += '</ol>';
                    }
                    if (report.code_suggestions) {
                        reportHtml += '<h4><?php _e( 'Code Suggestions:', 'as-laburda-pwa-app' ); ?></h4><pre style="background-color: #eee; padding: 10px; border-radius: 4px; overflow-x: auto;"><code>' + (report.code_suggestions || 'N/A') + '</code></pre>';
                    }

                    $('#debug-report-output').html(reportHtml);
                    $('#ai-debug-results').show();
                    aiAssistant.showMessage(response.data.message, 'success');
                } else {
                    aiAssistant.showMessage(response.data.message, 'error');
                }
            }).fail(function() {
                aiAssistant.hideLoading();
                aiAssistant.showMessage('<?php _e( 'An AJAX error occurred while debugging the site.', 'as-laburda-pwa-app' ); ?>', 'error');
            });
        }
    };

    aiAssistant.init();
});
</script>
