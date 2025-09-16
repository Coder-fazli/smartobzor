/**
 * Quiz App JavaScript
 * Handles quiz functionality, timer, scoring, and social sharing
 */

(function($) {
    'use strict';
    
    class QuizApp {
        constructor() {
            this.currentScreen = 'categories';
            this.currentQuestion = 0;
            this.questions = [];
            this.selectedAnswers = [];
            this.correctAnswers = 0;
            this.totalPoints = 0;
            this.timer = null;
            this.timeRemaining = quiz_ajax.timer_duration;
            this.startTime = null;
            this.category = '';
            
            this.init();
        }
        
        init() {
            this.bindEvents();
            this.updateStats();
        }
        
        bindEvents() {
            // Category selection
            $(document).on('click', '.category-item', (e) => {
                this.category = $(e.currentTarget).data('category');
                this.loadQuestions();
            });
            
            // Close buttons
            $(document).on('click', '.close-quiz, .close-results', () => {
                this.showScreen('categories');
                this.resetQuiz();
            });
            
            // Answer selection
            $(document).on('click', '.answer-option', (e) => {
                if (!$(e.currentTarget).hasClass('disabled')) {
                    this.selectAnswer($(e.currentTarget));
                }
            });
            
            // Submit answer
            $(document).on('click', '#submit-answer', () => {
                this.submitAnswer();
            });
            
            // Social sharing
            $(document).on('click', '.share-btn', (e) => {
                this.shareResults($(e.currentTarget).data('platform'));
            });
            
            // Upgrade button
            $(document).on('click', '.btn-upgrade', () => {
                this.handleUpgrade();
            });
        }
        
        showScreen(screenName) {
            $('.quiz-screen').removeClass('active');
            $(`#quiz-${screenName}`).addClass('active');
            this.currentScreen = screenName;
        }
        
        loadQuestions() {
            this.showScreen('questions');
            this.startTime = Date.now();
            
            $.ajax({
                url: quiz_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_quiz_questions',
                    nonce: quiz_ajax.nonce,
                    category: this.category,
                    limit: quiz_ajax.questions_count
                },
                success: (response) => {
                    if (response.success) {
                        this.questions = response.data.questions;
                        this.selectedAnswers = new Array(this.questions.length).fill(null);
                        this.displayQuestion();
                        this.startTimer();
                    } else {
                        alert('Error loading questions. Please try again.');
                        this.showScreen('categories');
                    }
                },
                error: () => {
                    alert('Error loading questions. Please try again.');
                    this.showScreen('categories');
                }
            });
        }
        
        displayQuestion() {
            if (this.currentQuestion >= this.questions.length) {
                this.showResults();
                return;
            }
            
            const question = this.questions[this.currentQuestion];
            const questionNumber = this.currentQuestion + 1;
            
            // Update header
            $('#current-category-name').text(this.getCategoryName());
            $('#current-category-icon').text(this.getCategoryIcon());
            $('#current-question').text(questionNumber);
            
            // Display question
            $('#question-text').text(question.question);
            
            // Display answer options
            const optionsHtml = question.options.map((option, index) => {
                const letter = String.fromCharCode(65 + index); // A, B, C, D
                const isSelected = this.selectedAnswers[this.currentQuestion] === index;
                
                return `
                    <div class="answer-option ${isSelected ? 'selected' : ''}" data-index="${index}">
                        <div class="option-letter">${letter}</div>
                        <div class="option-text">${option}</div>
                    </div>
                `;
            }).join('');
            
            $('#answer-options').html(optionsHtml);
            
            // Update submit button
            $('#submit-answer').prop('disabled', this.selectedAnswers[this.currentQuestion] === null);
        }
        
        selectAnswer(optionElement) {
            $('.answer-option').removeClass('selected');
            optionElement.addClass('selected');
            
            const selectedIndex = optionElement.data('index');
            this.selectedAnswers[this.currentQuestion] = selectedIndex;
            
            $('#submit-answer').prop('disabled', false);
        }
        
        submitAnswer() {
            const selectedIndex = this.selectedAnswers[this.currentQuestion];
            if (selectedIndex === null) return;
            
            const question = this.questions[this.currentQuestion];
            const isCorrect = selectedIndex === question.correct_answer;
            
            if (isCorrect) {
                this.correctAnswers++;
                this.totalPoints += quiz_ajax.points_per_answer;
            }
            
            // Show answer feedback
            $('.answer-option').addClass('disabled');
            $('.answer-option').each((index, element) => {
                const $element = $(element);
                const optionIndex = $element.data('index');
                
                if (optionIndex === question.correct_answer) {
                    $element.addClass('correct');
                    $element.append('<div class="option-status">✓</div>');
                } else if (optionIndex === selectedIndex && !isCorrect) {
                    $element.addClass('incorrect');
                    $element.append('<div class="option-status">✗</div>');
                }
            });
            
            // Disable submit button
            $('#submit-answer').prop('disabled', true);
            
            // Move to next question after delay
            setTimeout(() => {
                this.currentQuestion++;
                this.displayQuestion();
            }, 2000);
        }
        
        startTimer() {
            this.updateTimerDisplay();
            
            this.timer = setInterval(() => {
                this.timeRemaining--;
                this.updateTimerDisplay();
                
                if (this.timeRemaining <= 0) {
                    this.endQuiz();
                }
            }, 1000);
        }
        
        updateTimerDisplay() {
            const minutes = Math.floor(this.timeRemaining / 60);
            const seconds = this.timeRemaining % 60;
            const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            $('#timer-display').text(timeString);
            
            // Add warning class when time is running low
            if (this.timeRemaining <= 300) { // 5 minutes
                $('#timer-display').addClass('timer-warning');
            }
        }
        
        endQuiz() {
            clearInterval(this.timer);
            this.showResults();
        }
        
        showResults() {
            clearInterval(this.timer);
            
            const timeTaken = Math.floor((Date.now() - this.startTime) / 1000);
            const percentage = (this.correctAnswers / this.questions.length) * 100;
            
            // Update results display
            $('#final-score').text(`${Math.round(percentage)}% Score`);
            $('#total-questions').text(this.questions.length);
            $('#correct-answers').text(this.correctAnswers);
            
            // Save results
            this.saveResults(timeTaken, percentage);
            
            this.showScreen('results');
        }
        
        saveResults(timeTaken, percentage) {
            $.ajax({
                url: quiz_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'save_quiz_results',
                    nonce: quiz_ajax.nonce,
                    total_questions: this.questions.length,
                    correct_answers: this.correctAnswers,
                    total_points: this.totalPoints,
                    time_taken: timeTaken,
                    category: this.category
                },
                success: (response) => {
                    console.log('Results saved:', response);
                },
                error: () => {
                    console.log('Error saving results');
                }
            });
        }
        
        shareResults(platform) {
            const percentage = Math.round((this.correctAnswers / this.questions.length) * 100);
            const text = `I scored ${percentage}% on the ${this.getCategoryName()} quiz! Can you beat my score?`;
            const url = window.location.href;
            
            let shareUrl = '';
            
            switch (platform) {
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}&quote=${encodeURIComponent(text)}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
                    break;
                case 'whatsapp':
                    shareUrl = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`;
                    break;
                case 'instagram':
                    // Instagram doesn't support direct sharing via URL
                    this.copyToClipboard(text + ' ' + url);
                    alert('Text copied to clipboard! You can now paste it on Instagram.');
                    return;
            }
            
            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        }
        
        copyToClipboard(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text);
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
            }
        }
        
        resetQuiz() {
            this.currentQuestion = 0;
            this.questions = [];
            this.selectedAnswers = [];
            this.correctAnswers = 0;
            this.totalPoints = 0;
            this.timeRemaining = quiz_ajax.timer_duration;
            this.category = '';
            
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
            
            $('#timer-display').removeClass('timer-warning');
        }
        
        updateStats() {
            // Update question count
            $.ajax({
                url: quiz_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_quiz_questions',
                    nonce: quiz_ajax.nonce,
                    limit: 1000
                },
                success: (response) => {
                    if (response.success) {
                        $('#question-count').text(response.data.total_questions);
                    }
                }
            });
        }
        
        getCategoryName() {
            const categoryNames = {
                'art-literature': 'Art and Literature',
                'general-knowledge': 'General Knowledge',
                'science-nature': 'Science & Nature',
                'technology': 'Technology',
                'history': 'History',
                'geography': 'Geography',
                'sports': 'Sports',
                'entertainment': 'Entertainment'
            };
            
            return categoryNames[this.category] || this.category;
        }
        
        getCategoryIcon() {
            const categoryIcons = {
                'art-literature': '✒️',
                'general-knowledge': '📚',
                'science-nature': '🔬',
                'technology': '💻',
                'history': '📜',
                'geography': '🌍',
                'sports': '⚽',
                'entertainment': '🎬'
            };
            
            return categoryIcons[this.category] || '📝';
        }
        
        handleUpgrade() {
            // Implement upgrade functionality
            alert('Upgrade feature coming soon!');
        }
    }
    
    // Initialize the quiz app when document is ready
    $(document).ready(() => {
        if ($('#quiz-app').length) {
            new QuizApp();
        }
    });
    
})(jQuery);
