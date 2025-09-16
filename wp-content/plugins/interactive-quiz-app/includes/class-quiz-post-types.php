<?php
/**
 * Quiz Post Types Class
 * Handles custom post types for quiz questions
 */

if (!defined('ABSPATH')) {
    exit;
}

class QuizPostTypes {
    
    public function __construct() {
        add_action('init', array($this, 'register_post_types'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
    }
    
    public function register_post_types() {
        // Register Quiz Question post type
        register_post_type('quiz_question', array(
            'labels' => array(
                'name' => 'Quiz Questions',
                'singular_name' => 'Quiz Question',
                'add_new' => 'Add New Question',
                'add_new_item' => 'Add New Quiz Question',
                'edit_item' => 'Edit Quiz Question',
                'new_item' => 'New Quiz Question',
                'view_item' => 'View Quiz Question',
                'search_items' => 'Search Quiz Questions',
                'not_found' => 'No quiz questions found',
                'not_found_in_trash' => 'No quiz questions found in trash'
            ),
            'public' => true,
            'has_archive' => false,
            'supports' => array('title', 'editor'),
            'menu_icon' => 'dashicons-forms',
            'show_in_rest' => true
        ));
        
        // Register Quiz Category taxonomy
        register_taxonomy('quiz_category', 'quiz_question', array(
            'labels' => array(
                'name' => 'Quiz Categories',
                'singular_name' => 'Quiz Category',
                'search_items' => 'Search Categories',
                'all_items' => 'All Categories',
                'parent_item' => 'Parent Category',
                'parent_item_colon' => 'Parent Category:',
                'edit_item' => 'Edit Category',
                'update_item' => 'Update Category',
                'add_new_item' => 'Add New Category',
                'new_item_name' => 'New Category Name',
                'menu_name' => 'Categories'
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'quiz-category'),
            'show_in_rest' => true
        ));
    }
    
    public function add_meta_boxes() {
        add_meta_box(
            'quiz_question_options',
            'Question Options',
            array($this, 'render_meta_box'),
            'quiz_question',
            'normal',
            'high'
        );
    }
    
    public function render_meta_box($post) {
        wp_nonce_field('quiz_question_meta_box', 'quiz_question_meta_box_nonce');
        
        $options = get_post_meta($post->ID, '_quiz_options', true);
        $correct_answer = get_post_meta($post->ID, '_correct_answer', true);
        
        if (!is_array($options)) {
            $options = array('', '', '', '');
        }
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="option_a">Option A:</label></th>
                <td><input type="text" id="option_a" name="quiz_options[]" value="<?php echo esc_attr($options[0]); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="option_b">Option B:</label></th>
                <td><input type="text" id="option_b" name="quiz_options[]" value="<?php echo esc_attr($options[1]); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="option_c">Option C:</label></th>
                <td><input type="text" id="option_c" name="quiz_options[]" value="<?php echo esc_attr($options[2]); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="option_d">Option D:</label></th>
                <td><input type="text" id="option_d" name="quiz_options[]" value="<?php echo esc_attr($options[3]); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="correct_answer">Correct Answer:</label></th>
                <td>
                    <select id="correct_answer" name="correct_answer">
                        <option value="">Select correct answer</option>
                        <option value="0" <?php selected($correct_answer, '0'); ?>>A</option>
                        <option value="1" <?php selected($correct_answer, '1'); ?>>B</option>
                        <option value="2" <?php selected($correct_answer, '2'); ?>>C</option>
                        <option value="3" <?php selected($correct_answer, '3'); ?>>D</option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }
    
    public function save_meta_boxes($post_id) {
        if (!isset($_POST['quiz_question_meta_box_nonce']) || 
            !wp_verify_nonce($_POST['quiz_question_meta_box_nonce'], 'quiz_question_meta_box')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (isset($_POST['quiz_options']) && is_array($_POST['quiz_options'])) {
            $options = array_map('sanitize_text_field', $_POST['quiz_options']);
            update_post_meta($post_id, '_quiz_options', $options);
        }
        
        if (isset($_POST['correct_answer'])) {
            update_post_meta($post_id, '_correct_answer', sanitize_text_field($_POST['correct_answer']));
        }
    }
}
