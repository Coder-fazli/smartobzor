<?php
/**
 * Plugin Name: Calorie Calculator
 * Description: A comprehensive calorie calculator with multiple formulas and goals for weight management.
 * Version: 5.1.2
 * Author: Your Name
 * Text Domain: calorie-calculator
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CALORIE_CALC_VERSION', '5.1.2');
define('CALORIE_CALC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CALORIE_CALC_PLUGIN_PATH', plugin_dir_path(__FILE__));

class CalorieCalculator {

    public function __construct() {
        add_action('init', array($this, 'init'));
    }

    public function init() {
        // Register shortcode
        add_shortcode('calorie_calculator', array($this, 'display_calculator'));

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Handle AJAX requests
        add_action('wp_ajax_calculate_calories', array($this, 'handle_calculation'));
        add_action('wp_ajax_nopriv_calculate_calories', array($this, 'handle_calculation'));
    }

    public function enqueue_scripts() {
        // Enqueue Bootstrap CSS from CDN for reliable grid system
        wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', array(), '5.3.0');
        wp_enqueue_style('calorie-calculator-style', CALORIE_CALC_PLUGIN_URL . 'assets/style.css', array('bootstrap-css'), CALORIE_CALC_VERSION);
        wp_enqueue_script('calorie-calculator-script', CALORIE_CALC_PLUGIN_URL . 'assets/script.js', array('jquery'), CALORIE_CALC_VERSION, true);

        // Localize script for AJAX
        wp_localize_script('calorie-calculator-script', 'calorie_calc_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('calorie_calc_nonce')
        ));
    }

    public function display_calculator($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Калькулятор калорий'
        ), $atts);

        ob_start();
        ?>
        <div id="calorie-calculator" class="calorie-calculator">
            <!-- Header -->
            <div class="calc-header">
                <h1 class="calc-title"><?php echo esc_html($atts['title']); ?></h1>
                <p class="calc-description">
                    Рассчитайте сколько калорий, белков, жиров и углеводов вам нужно потреблять ежедневно для поддержания веса, похудения или набора массы.
                </p>
            </div>

            <form id="calorie-calc-form" class="calc-form">
                <!-- General Information -->
                <div class="calc-section">
                    <h3 class="section-title">
                        <span class="section-bullet">●</span>
                        Общая информация:
                    </h3>

                    <div class="gender-selection">
                        <label class="gender-option selected" data-gender="female">
                            <input type="radio" name="gender" value="female" checked>
                            Женщина
                        </label>
                        <label class="gender-option" data-gender="male">
                            <input type="radio" name="gender" value="male">
                            Мужчина
                        </label>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label>Возраст, лет</label>
                            <input type="number" name="age" value="0" required>
                        </div>
                        <div class="input-group">
                            <label>Рост, см</label>
                            <input type="number" name="height" value="0" required>
                        </div>
                        <div class="input-group">
                            <label>Вес, кг</label>
                            <input type="number" name="weight" value="0" required>
                        </div>
                    </div>
                </div>

                <!-- Daily Activity -->
                <div class="calc-section">
                    <h3 class="section-title">
                        <span class="section-bullet">●</span>
                        Дневная активность:
                    </h3>

                    <div class="activity-slider">
                        <input type="range" name="activity" min="1" max="5" value="1" class="slider">
                        <div class="activity-current">Низкая</div>
                        <p class="activity-description">Хожу в магазин или недолго прогуливаюсь</p>
                    </div>
                </div>

                <!-- Goal -->
                <div class="calc-section">
                    <h3 class="section-title">
                        <span class="section-bullet">●</span>
                        Ваша цель:
                    </h3>

                    <div class="goal-selection">
                        <label class="goal-option selected" data-goal="lose">
                            <input type="radio" name="goal" value="lose" checked>
                            Сбросить вес
                        </label>
                        <label class="goal-option" data-goal="maintain">
                            <input type="radio" name="goal" value="maintain">
                            Поддерживать вес
                        </label>
                        <label class="goal-option" data-goal="gain">
                            <input type="radio" name="goal" value="gain">
                            Набрать вес
                        </label>
                    </div>
                </div>

                <!-- Formula -->
                <div class="calc-section">
                    <h3 class="section-title">
                        <span class="section-bullet">●</span>
                        Формула расчета:
                        <span class="info-icon" title="Информация о формулах расчета">ⓘ</span>
                    </h3>

                    <div class="formula-selection">
                        <label class="formula-option selected" data-formula="harris">
                            <input type="radio" name="formula" value="harris" checked>
                            Харрис-Бенедикта
                        </label>
                        <label class="formula-option" data-formula="mifflin">
                            <input type="radio" name="formula" value="mifflin">
                            Миффлина-Сан Жеора
                        </label>
                    </div>
                </div>

                <button type="submit" class="calc-button">
                    <span class="calc-icon">📊</span>
                    РАССЧИТАТЬ
                </button>
            </form>

                <div id="calc-results" class="calc-results" style="display: none;">
                    <div class="row">
                        <div class="col-12">
                            <div class="results-content">
                                <!-- Results will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function handle_calculation() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'calorie_calc_nonce')) {
            wp_die('Security check failed');
        }

        // Get form data
        $gender = sanitize_text_field($_POST['gender']);
        $age = intval($_POST['age']);
        $height = intval($_POST['height']);
        $weight = intval($_POST['weight']);
        $activity = intval($_POST['activity']);
        $goal = sanitize_text_field($_POST['goal']);
        $formula = sanitize_text_field($_POST['formula']);

        // Calculate BMR
        if ($formula === 'harris') {
            $bmr = $this->calculate_harris_benedict($gender, $weight, $height, $age);
        } else {
            $bmr = $this->calculate_mifflin_st_jeor($gender, $weight, $height, $age);
        }

        // Activity multipliers
        $activity_multipliers = array(
            1 => 1.2,   // Sedentary
            2 => 1.375, // Light activity
            3 => 1.55,  // Moderate activity
            4 => 1.725, // High activity
            5 => 1.9    // Very high activity
        );

        // Calculate TDEE
        $tdee = $bmr * $activity_multipliers[$activity];

        // Adjust for goal
        $calories = $tdee;
        switch ($goal) {
            case 'lose':
                $calories = $tdee - 500; // 500 calorie deficit
                break;
            case 'gain':
                $calories = $tdee + 500; // 500 calorie surplus
                break;
        }

        // Calculate BMI
        $height_meters = $height / 100;
        $bmi = $weight / ($height_meters * $height_meters);

        // Determine BMI category and color
        $bmi_category = '';
        $bmi_color = '';
        if ($bmi < 18.5) {
            $bmi_category = 'Дефицит веса';
            $bmi_color = '#3498db';
        } elseif ($bmi < 25) {
            $bmi_category = 'Норма';
            $bmi_color = '#2ecc71';
        } elseif ($bmi < 30) {
            $bmi_category = 'Избыток веса';
            $bmi_color = '#f39c12';
        } else {
            $bmi_category = 'Ожирение';
            $bmi_color = '#e74c3c';
        }

        // Calculate macronutrients (example ratios)
        $protein = ($calories * 0.25) / 4; // 25% protein, 4 cal/g
        $fat = ($calories * 0.30) / 9;     // 30% fat, 9 cal/g
        $carbs = ($calories * 0.45) / 4;   // 45% carbs, 4 cal/g

        $results = array(
            'success' => true,
            'bmr' => round($bmr),
            'tdee' => round($tdee),
            'calories' => round($calories),
            'protein' => round($protein),
            'fat' => round($fat),
            'carbs' => round($carbs),
            'bmi' => round($bmi, 1),
            'bmi_category' => $bmi_category,
            'bmi_color' => $bmi_color
        );

        wp_send_json($results);
    }

    private function calculate_harris_benedict($gender, $weight, $height, $age) {
        if ($gender === 'male') {
            return 88.362 + (13.397 * $weight) + (4.799 * $height) - (5.677 * $age);
        } else {
            return 447.593 + (9.247 * $weight) + (3.098 * $height) - (4.330 * $age);
        }
    }

    private function calculate_mifflin_st_jeor($gender, $weight, $height, $age) {
        $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age);
        if ($gender === 'male') {
            return $bmr + 5;
        } else {
            return $bmr - 161;
        }
    }
}

// Initialize the plugin
new CalorieCalculator();

// Activation hook
register_activation_hook(__FILE__, function() {
    // Plugin activation code if needed
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Plugin deactivation code if needed
});