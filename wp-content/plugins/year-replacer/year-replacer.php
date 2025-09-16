<?php
/**
 * Plugin Name: Year Replacer 2025
 * Description: Replace all instances of 2023 and 2024 with 2025 in posts, titles, and meta data
 * Version: 1.0
 * Author: SmartObzor
 */

if (!defined('ABSPATH')) {
    exit;
}

class YearReplacer2025 {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_year_replacer_action', array($this, 'handle_replacement'));
    }

    public function add_admin_menu() {
        add_management_page(
            'Year Replacer 2025',
            'Year Replacer',
            'manage_options',
            'year-replacer',
            array($this, 'admin_page')
        );
    }

    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>Year Replacer 2025</h1>
            <p>This tool will replace all instances of <strong>2023</strong> and <strong>2024</strong> with <strong>2025</strong> in:</p>
            <ul>
                <li>Post titles</li>
                <li>Post content</li>
                <li>Post excerpts</li>
                <li>Post meta data (SEO titles, descriptions, etc.)</li>
            </ul>

            <div id="year-replacer-results"></div>

            <h2>Step 1: Preview Changes</h2>
            <button id="preview-btn" class="button button-secondary">Preview What Will Be Changed</button>

            <div id="preview-results" style="margin-top: 20px;"></div>

            <h2>Step 2: Execute Replacement</h2>
            <button id="replace-btn" class="button button-primary" style="display:none;">Replace 2023/2024 with 2025</button>

            <script>
            jQuery(document).ready(function($) {

                $('#preview-btn').click(function() {
                    $(this).prop('disabled', true).text('Scanning...');

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'year_replacer_action',
                            mode: 'preview',
                            nonce: '<?php echo wp_create_nonce('year_replacer_nonce'); ?>'
                        },
                        success: function(response) {
                            $('#preview-results').html(response);
                            $('#replace-btn').show();
                            $('#preview-btn').prop('disabled', false).text('Preview What Will Be Changed');
                        }
                    });
                });

                $('#replace-btn').click(function() {
                    if (!confirm('Are you sure you want to replace all 2023 and 2024 with 2025? This cannot be undone!')) {
                        return;
                    }

                    $(this).prop('disabled', true).text('Replacing...');

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'year_replacer_action',
                            mode: 'replace',
                            nonce: '<?php echo wp_create_nonce('year_replacer_nonce'); ?>'
                        },
                        success: function(response) {
                            $('#year-replacer-results').html('<div class="notice notice-success"><p>' + response + '</p></div>');
                            $('#replace-btn').prop('disabled', false).text('Replace 2023/2024 with 2025');
                            $('#preview-results').html('');
                            $('#replace-btn').hide();
                        }
                    });
                });
            });
            </script>
        </div>
        <?php
    }

    public function handle_replacement() {
        if (!wp_verify_nonce($_POST['nonce'], 'year_replacer_nonce')) {
            wp_die('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        global $wpdb;

        $mode = $_POST['mode'];

        if ($mode === 'preview') {
            $this->show_preview();
        } elseif ($mode === 'replace') {
            $this->execute_replacement();
        }

        wp_die();
    }

    private function show_preview() {
        global $wpdb;

        // Check posts
        $posts = $wpdb->get_results("
            SELECT ID, post_title, post_type, post_status, post_date
            FROM {$wpdb->posts}
            WHERE (post_title LIKE '%2023%' OR post_title LIKE '%2024%'
                   OR post_content LIKE '%2023%' OR post_content LIKE '%2024%'
                   OR post_excerpt LIKE '%2023%' OR post_excerpt LIKE '%2024%')
            ORDER BY post_date DESC
            LIMIT 20
        ");

        // Check meta
        $meta_count = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->postmeta}
            WHERE meta_value LIKE '%2023%' OR meta_value LIKE '%2024%'
        ");

        $output = '<div style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd;">';
        $output .= '<h3>Preview Results:</h3>';

        if (count($posts) > 0) {
            $output .= '<h4>Posts that will be updated (' . count($posts) . ' shown, more may exist):</h4>';
            $output .= '<ul>';
            foreach ($posts as $post) {
                $output .= '<li><strong>' . esc_html($post->post_title) . '</strong> (' . $post->post_type . ', ' . $post->post_status . ') - ' . $post->post_date . '</li>';
            }
            $output .= '</ul>';
        } else {
            $output .= '<p>No posts found containing 2023 or 2024.</p>';
        }

        if ($meta_count > 0) {
            $output .= '<h4>Post meta entries: ' . $meta_count . ' entries will be updated</h4>';
        } else {
            $output .= '<p>No post meta found containing 2023 or 2024.</p>';
        }

        $total = count($posts) + $meta_count;

        if ($total > 0) {
            $output .= '<div style="background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; margin: 10px 0;">';
            $output .= '<strong>Ready to replace:</strong> Approximately ' . $total . ' items will be updated.';
            $output .= '</div>';
        } else {
            $output .= '<div style="background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0;">';
            $output .= '<strong>No changes needed!</strong> Your site doesn\'t contain 2023 or 2024.';
            $output .= '</div>';
        }

        $output .= '</div>';

        echo $output;
    }

    private function execute_replacement() {
        global $wpdb;

        $posts_updated = 0;
        $meta_updated = 0;

        // Update posts
        $posts_result = $wpdb->query("
            UPDATE {$wpdb->posts}
            SET post_title = REPLACE(REPLACE(post_title, '2023', '2025'), '2024', '2025'),
                post_content = REPLACE(REPLACE(post_content, '2023', '2025'), '2024', '2025'),
                post_excerpt = REPLACE(REPLACE(post_excerpt, '2023', '2025'), '2024', '2025')
            WHERE (post_title LIKE '%2023%' OR post_title LIKE '%2024%'
                   OR post_content LIKE '%2023%' OR post_content LIKE '%2024%'
                   OR post_excerpt LIKE '%2023%' OR post_excerpt LIKE '%2024%')
        ");

        if ($posts_result !== false) {
            $posts_updated = $posts_result;
        }

        // Update postmeta
        $meta_result = $wpdb->query("
            UPDATE {$wpdb->postmeta}
            SET meta_value = REPLACE(REPLACE(meta_value, '2023', '2025'), '2024', '2025')
            WHERE meta_value LIKE '%2023%' OR meta_value LIKE '%2024%'
        ");

        if ($meta_result !== false) {
            $meta_updated = $meta_result;
        }

        // Clear caches
        wp_cache_flush();
        delete_transient('all_transients');
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%'");

        $message = "✅ Replacement completed successfully!<br>";
        $message .= "📄 Posts updated: " . $posts_updated . "<br>";
        $message .= "🔧 Meta entries updated: " . $meta_updated . "<br>";
        $message .= "🗑️ Caches cleared<br><br>";
        $message .= "<strong>All 2023 and 2024 references have been replaced with 2025!</strong>";

        echo $message;
    }
}

new YearReplacer2025();
?>