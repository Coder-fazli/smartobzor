jQuery(document).ready(function($) {

    // Activity level descriptions
    const activityDescriptions = {
        1: 'Хожу в магазин или недолго прогуливаюсь',
        2: 'Легкие упражнения 1-3 раза в неделю',
        3: 'Умеренные упражнения 3-5 раз в неделю',
        4: 'Интенсивные тренировки 6-7 раз в неделю',
        5: 'Очень интенсивные тренировки, физическая работа'
    };

    // Activity level labels
    const activityLabels = [
        'Низкая',
        'Легкая',
        'Умеренная',
        'Высокая',
        'Очень высокая'
    ];

    // Initialize UI interactions
    initializeGenderSelection();
    initializeGoalSelection();
    initializeFormulaSelection();
    initializeActivitySlider();
    initializeFormSubmission();

    function initializeGenderSelection() {
        $('.gender-option').click(function() {
            $('.gender-option').removeClass('selected');
            $(this).addClass('selected');
            $(this).find('input[type="radio"]').prop('checked', true);
        });
    }

    function initializeGoalSelection() {
        $('.goal-option').click(function() {
            $('.goal-option').removeClass('selected');
            $(this).addClass('selected');
            $(this).find('input[type="radio"]').prop('checked', true);
        });
    }

    function initializeFormulaSelection() {
        $('.formula-option').click(function() {
            $('.formula-option').removeClass('selected');
            $(this).addClass('selected');
            $(this).find('input[type="radio"]').prop('checked', true);
        });
    }

    function initializeActivitySlider() {
        const slider = $('input[name="activity"]');
        const labels = $('.activity-label');
        const description = $('.activity-description');

        function updateSlider() {
            const value = parseInt(slider.val());
            const percentage = ((value - 1) / 4) * 100;

            // Update visual progress
            slider.css({
                'background': `linear-gradient(to right, #ff6b35 0%, #ff6b35 ${percentage}%, #e0e0e0 ${percentage}%, #e0e0e0 100%)`,
                'transition': 'background 0.3s ease'
            });

            // Update current activity level
            $('.activity-current').text(activityLabels[value - 1]);

            // Update description
            $('.activity-description').text(activityDescriptions[value]);
        }

        // Handle different events for smooth interaction
        slider.on('input', function() {
            updateSlider();
        });

        slider.on('change', function() {
            updateSlider();
        });

        updateSlider(); // Initialize
    }

    function initializeFormSubmission() {
        $('#calorie-calc-form').on('submit', function(e) {
            e.preventDefault();

            const button = $('.calc-button');
            const results = $('#calc-results');

            // Add loading state
            button.addClass('loading');
            button.find('.calc-icon').text('⏳');
            button.find('span:last-child').text('РАССЧИТЫВАЕМ ВАШУ НОРМУ...');

            // Collect form data
            const formData = {
                action: 'calculate_calories',
                nonce: calorie_calc_ajax.nonce,
                gender: $('input[name="gender"]:checked').val(),
                age: parseInt($('input[name="age"]').val()),
                height: parseInt($('input[name="height"]').val()),
                weight: parseInt($('input[name="weight"]').val()),
                activity: parseInt($('input[name="activity"]').val()),
                goal: $('input[name="goal"]:checked').val(),
                formula: $('input[name="formula"]:checked').val()
            };

            // Validate form data
            if (!validateFormData(formData)) {
                resetButton();
                return;
            }

            // Make AJAX request
            $.ajax({
                url: calorie_calc_ajax.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        displayResults(response, formData);
                        results.slideDown();
                    } else {
                        alert('Произошла ошибка при расчете. Попробуйте еще раз.');
                    }
                    resetButton();
                },
                error: function() {
                    alert('Произошла ошибка при отправке данных. Проверьте подключение к интернету.');
                    resetButton();
                }
            });
        });
    }

    function validateFormData(data) {
        if (!data.gender) {
            alert('Пожалуйста, выберите пол');
            return false;
        }

        if (!data.age || data.age < 1) {
            alert('Пожалуйста, введите корректный возраст');
            return false;
        }

        if (!data.height || data.height < 1) {
            alert('Пожалуйста, введите корректный рост');
            return false;
        }

        if (!data.weight || data.weight < 1) {
            alert('Пожалуйста, введите корректный вес');
            return false;
        }

        return true;
    }

    function resetButton() {
        const button = $('.calc-button');
        button.removeClass('loading');
        button.find('.calc-icon').text('🧮');
        button.find('span:last-child').text('РАССЧИТАТЬ МОЮ НОРМУ');
    }

    function displayResults(response, formData) {
        const goalTexts = {
            'lose': 'похудения',
            'maintain': 'поддержания веса',
            'gain': 'набора веса'
        };

        // Calculate BMI position for the progress bar (0-100%)
        const bmiPosition = Math.min(Math.max((response.bmi - 15) / 25 * 100, 0), 100);

        const resultsHtml = `
            <div class="results-title">Ваш результат</div>

            <!-- BMI Section -->
            <div class="result-card bmi-card">
                <div class="card-header">
                    <h3>Ваш индекс массы тела</h3>
                    <span class="info-icon" title="ИМТ рассчитывается как вес в кг делённый на квадрат роста в метрах">ⓘ</span>
                </div>
                <div class="bmi-display">
                    <div class="bmi-value">${response.bmi}</div>
                    <div class="bmi-indicator">
                        <div class="bmi-bar">
                            <div class="bmi-gradient"></div>
                            <div class="bmi-pointer" style="left: ${bmiPosition}%"></div>
                        </div>
                        <div class="bmi-labels">
                            <span>Дефицит</span>
                            <span>Норма</span>
                            <span>Избыток</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Calories Section -->
            <div class="result-card calories-card">
                <div class="card-header">
                    <h3>Ваша суточная норма калорий</h3>
                    <span class="info-icon" title="Рекомендуемое количество калорий для достижения вашей цели">ⓘ</span>
                </div>
                <div class="calories-display">
                    <div class="circular-progress" data-calories="${response.calories}">
                        <svg class="progress-ring" width="120" height="120">
                            <defs>
                                <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#667eea"/>
                                    <stop offset="100%" style="stop-color:#764ba2"/>
                                </linearGradient>
                            </defs>
                            <circle class="progress-ring-background" cx="60" cy="60" r="54"></circle>
                            <circle class="progress-ring-progress" cx="60" cy="60" r="54" stroke="url(#progressGradient)"></circle>
                        </svg>
                        <div class="progress-text">
                            <div class="calories-number">${response.calories}</div>
                            <div class="calories-label">ккал*</div>
                        </div>
                    </div>
                    <div class="calories-breakdown">
                        <div class="breakdown-title">Из которых</div>
                        <div class="macro-items">
                            <div class="macro-item">
                                <div class="macro-dot protein-dot"></div>
                                <span class="macro-value">${response.protein} г</span>
                                <span class="macro-label">Белки</span>
                            </div>
                            <div class="macro-item">
                                <div class="macro-dot fat-dot"></div>
                                <span class="macro-value">${response.fat} г</span>
                                <span class="macro-label">Жиры</span>
                            </div>
                            <div class="macro-item">
                                <div class="macro-dot carb-dot"></div>
                                <span class="macro-value">${response.carbs} г</span>
                                <span class="macro-label">Углеводы</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="result-note">
                * Данные результаты являются рекомендацией, для более точных результатов обратитесь к специалисту.
            </div>
        `;

        $('#calc-results .results-content').html(resultsHtml);

        // Animate circular progress
        setTimeout(() => {
            animateCircularProgress();
        }, 300);
    }

    function animateCircularProgress() {
        const circle = $('.progress-ring-progress');
        const radius = 54;
        const circumference = 2 * Math.PI * radius;

        circle.css({
            'stroke-dasharray': circumference,
            'stroke-dashoffset': circumference
        });

        // Animate to 75% fill (visual effect)
        setTimeout(() => {
            circle.css({
                'stroke-dashoffset': circumference * 0.25,
                'transition': 'stroke-dashoffset 1s ease-in-out'
            });
        }, 100);
    }

    // Remove all input restrictions - let users type freely
    // Only basic validation will happen during form submission

    // Smooth scrolling to results
    function scrollToResults() {
        $('html, body').animate({
            scrollTop: $('#calc-results').offset().top - 20
        }, 800);
    }

    // Add scroll to results when displayed
    const originalSlideDown = $.fn.slideDown;
    $('#calc-results').slideDown = function() {
        const result = originalSlideDown.apply(this, arguments);
        setTimeout(scrollToResults, 300);
        return result;
    };

    // Tooltip functionality for info icon
    $('.info-icon').on('mouseenter', function() {
        const tooltip = $(`
            <div class="formula-tooltip">
                <strong>Харрис-Бенедикта:</strong> Классическая формула, разработанная в 1919 году<br>
                <strong>Миффлина-Сан Жеора:</strong> Более современная формула, считается более точной
            </div>
        `);

        $('body').append(tooltip);

        const icon = $(this);
        const iconOffset = icon.offset();

        tooltip.css({
            position: 'absolute',
            top: iconOffset.top - tooltip.outerHeight() - 10,
            left: iconOffset.left - tooltip.outerWidth() / 2 + icon.outerWidth() / 2,
            background: '#333',
            color: 'white',
            padding: '10px',
            borderRadius: '6px',
            fontSize: '12px',
            lineHeight: '1.4',
            zIndex: 1000,
            boxShadow: '0 2px 10px rgba(0,0,0,0.2)',
            maxWidth: '250px'
        });
    }).on('mouseleave', function() {
        $('.formula-tooltip').remove();
    });

    // Keyboard navigation
    $(document).on('keydown', function(e) {
        if (e.key === 'Enter' && !$(e.target).is('button')) {
            e.preventDefault();
            $('#calorie-calc-form').submit();
        }
    });

    // Form reset functionality (if needed)
    function resetForm() {
        $('#calorie-calc-form')[0].reset();
        $('.gender-option, .goal-option, .formula-option').removeClass('selected');
        $('.gender-option:first, .goal-option:first, .formula-option:first').addClass('selected');
        $('#calc-results').slideUp();
        initializeActivitySlider();
    }

    // Expose reset function globally if needed
    window.resetCalorieCalculator = resetForm;
});