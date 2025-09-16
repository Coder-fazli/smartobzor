<?php
/**
 * Quiz Shortcodes Class
 * Handles shortcodes for displaying quiz interface
 */

if (!defined('ABSPATH')) {
    exit;
}

class QuizShortcodes {
    
    public function __construct() {
        add_shortcode('quiz_app', array($this, 'quiz_app_shortcode'));
        add_shortcode('quiz_categories', array($this, 'quiz_categories_shortcode'));
    }
    
    public function quiz_app_shortcode($atts) {
        $atts = shortcode_atts(array(
            'category' => '',
            'questions' => 20,
            'timer' => 30
        ), $atts);
        
        ob_start();
        ?>
        <div id="quiz-app" class="quiz-app-container">
            <!-- Quiz Categories Screen -->
            <div id="quiz-categories" class="quiz-screen active">
                <div class="quiz-header">
                    <div class="hamburger-menu">☰</div>
                    <h1 class="quiz-title">Quiz App</h1>
                    <div class="user-profile">👤</div>
                </div>
                
                <div class="quiz-stats">
                    <div class="stat-item">
                        <span class="stat-label">Question Count</span>
                        <span class="stat-value" id="question-count">230</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Your Ranking</span>
                        <span class="stat-value" id="user-ranking">1250</span>
                        <span class="ranking-arrow">↑</span>
                    </div>
                </div>
                
                <div class="quiz-categories-list">
                    <?php
                    $categories = get_terms(array(
                        'taxonomy' => 'quiz_category',
                        'hide_empty' => false
                    ));
                    
                    if (!empty($categories) && !is_wp_error($categories)) {
                        foreach ($categories as $category) {
                            $icon = $this->get_category_icon($category->slug);
                            ?>
                            <div class="category-item" data-category="<?php echo esc_attr($category->slug); ?>">
                                <div class="category-icon"><?php echo $icon; ?></div>
                                <div class="category-name"><?php echo esc_html($category->name); ?></div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                
                <div class="upgrade-button">
                    <button class="btn-upgrade">UPGRADE</button>
                </div>
            </div>
            
            <!-- Quiz Questions Screen -->
            <div id="quiz-questions" class="quiz-screen">
                <div class="quiz-header">
                    <h2 class="quiz-category-title">
                        <span class="category-icon" id="current-category-icon"></span>
                        <span id="current-category-name"></span>
                    </h2>
                    <div class="close-quiz">✕</div>
                </div>
                
                <div class="quiz-info">
                    <span class="quiz-progress">Quiz: <span id="current-question">1</span>/20</span>
                    <span class="quiz-timer">Time: <span id="timer-display">30:00</span></span>
                </div>
                
                <div class="question-container">
                    <div class="question-text" id="question-text"></div>
                    
                    <div class="answer-options" id="answer-options">
                        <!-- Answer options will be populated by JavaScript -->
                    </div>
                </div>
                
                <div class="quiz-actions">
                    <button class="btn-submit" id="submit-answer">SUBMIT</button>
                </div>
            </div>
            
            <!-- Quiz Results Screen -->
            <div id="quiz-results" class="quiz-screen">
                <div class="quiz-header">
                    <div class="close-results">✕</div>
                </div>
                
                <div class="results-card">
                    <div class="trophy-icon">🏆</div>
                    <h2 class="congrats-text">Congrats!</h2>
                    <div class="score-display" id="final-score">90% Score</div>
                    <p class="completion-text">Quiz completed successfully.</p>
                    <p class="attempts-text">
                        You attempt <span id="total-questions">20</span> questions and from that 
                        <span id="correct-answers">18</span> answer is correct.
                    </p>
                    
                    <div class="share-section">
                        <p class="share-text">Share with us:</p>
                        <div class="social-share-buttons">
                            <button class="share-btn instagram" data-platform="instagram">📷</button>
                            <button class="share-btn facebook" data-platform="facebook">📘</button>
                            <button class="share-btn whatsapp" data-platform="whatsapp">📱</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function quiz_categories_shortcode($atts) {
        $atts = shortcode_atts(array(
            'show_count' => 'true'
        ), $atts);
        
        ob_start();
        ?>
        <div class="quiz-categories-widget">
            <h3>Quiz Categories</h3>
            <ul class="categories-list">
                <?php
                $categories = get_terms(array(
                    'taxonomy' => 'quiz_category',
                    'hide_empty' => false
                ));
                
                if (!empty($categories) && !is_wp_error($categories)) {
                    foreach ($categories as $category) {
                        $count = $atts['show_count'] === 'true' ? ' (' . $category->count . ')' : '';
                        ?>
                        <li>
                            <a href="<?php echo get_term_link($category); ?>">
                                <?php echo esc_html($category->name . $count); ?>
                            </a>
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function get_category_icon($slug) {
        $icons = array(
            'art-literature' => '✒️',
            'general-knowledge' => '📚',
            'science-nature' => '🔬',
            'technology' => '💻',
            'history' => '📜',
            'geography' => '🌍',
            'sports' => '⚽',
            'entertainment' => '🎬'
        );
        
        return isset($icons[$slug]) ? $icons[$slug] : '📝';
    }
}
