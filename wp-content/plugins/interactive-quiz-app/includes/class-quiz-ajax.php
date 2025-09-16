<?php
/**
 * Quiz AJAX Class
 * Handles AJAX requests for quiz functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class QuizAjax {
    
    public function __construct() {
        add_action('wp_ajax_get_quiz_questions', array($this, 'get_quiz_questions'));
        add_action('wp_ajax_nopriv_get_quiz_questions', array($this, 'get_quiz_questions'));
        
        add_action('wp_ajax_submit_quiz_answer', array($this, 'submit_quiz_answer'));
        add_action('wp_ajax_nopriv_submit_quiz_answer', array($this, 'submit_quiz_answer'));
        
        add_action('wp_ajax_save_quiz_results', array($this, 'save_quiz_results'));
        add_action('wp_ajax_nopriv_save_quiz_results', array($this, 'save_quiz_results'));
    }
    
    public function get_quiz_questions() {
        check_ajax_referer('quiz_nonce', 'nonce');
        
        $category = sanitize_text_field($_POST['category']);
        $limit = intval($_POST['limit']) ?: 20;
        
        $args = array(
            'post_type' => 'quiz_question',
            'posts_per_page' => $limit,
            'orderby' => 'rand',
            'post_status' => 'publish'
        );
        
        if (!empty($category)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'quiz_category',
                    'field' => 'slug',
                    'terms' => $category
                )
            );
        }
        
        $questions = get_posts($args);
        $formatted_questions = array();
        
        foreach ($questions as $question) {
            $options = get_post_meta($question->ID, '_quiz_options', true);
            $correct_answer = get_post_meta($question->ID, '_correct_answer', true);
            
            if (is_array($options) && !empty($correct_answer)) {
                $formatted_questions[] = array(
                    'id' => $question->ID,
                    'question' => $question->post_title,
                    'options' => $options,
                    'correct_answer' => intval($correct_answer)
                );
            }
        }
        
        wp_send_json_success(array(
            'questions' => $formatted_questions,
            'total_questions' => count($formatted_questions)
        ));
    }
    
    public function submit_quiz_answer() {
        check_ajax_referer('quiz_nonce', 'nonce');
        
        $question_id = intval($_POST['question_id']);
        $selected_answer = intval($_POST['selected_answer']);
        $correct_answer = intval($_POST['correct_answer']);
        
        $is_correct = ($selected_answer === $correct_answer);
        $points = $is_correct ? 5 : 0;
        
        wp_send_json_success(array(
            'is_correct' => $is_correct,
            'points' => $points,
            'correct_answer' => $correct_answer
        ));
    }
    
    public function save_quiz_results() {
        check_ajax_referer('quiz_nonce', 'nonce');
        
        $total_questions = intval($_POST['total_questions']);
        $correct_answers = intval($_POST['correct_answers']);
        $total_points = intval($_POST['total_points']);
        $time_taken = intval($_POST['time_taken']);
        $category = sanitize_text_field($_POST['category']);
        
        $percentage = ($correct_answers / $total_questions) * 100;
        
        // Save results to database (optional)
        $result_data = array(
            'total_questions' => $total_questions,
            'correct_answers' => $correct_answers,
            'total_points' => $total_points,
            'percentage' => $percentage,
            'time_taken' => $time_taken,
            'category' => $category,
            'date' => current_time('mysql'),
            'user_id' => get_current_user_id()
        );
        
        // Store in session or database
        if (!session_id()) {
            session_start();
        }
        $_SESSION['quiz_results'] = $result_data;
        
        wp_send_json_success(array(
            'percentage' => round($percentage, 1),
            'correct_answers' => $correct_answers,
            'total_questions' => $total_questions,
            'total_points' => $total_points,
            'time_taken' => $time_taken
        ));
    }
}
