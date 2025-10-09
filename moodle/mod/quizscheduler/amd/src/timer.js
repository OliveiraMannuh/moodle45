/**
 * Quiz scheduler timer module.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification'], function($, notification) {
    
    var timer = {
        
        /**
         * Initialize the timer.
         * @param {number} timeleft Time left in seconds
         * @param {number} warningtime Warning time in seconds
         */
        init: function(timeleft, warningtime) {
            this.createTimerDisplay();
            this.startCountdown(timeleft, warningtime);
        },
        
        /**
         * Create timer display element.
         */
        createTimerDisplay: function() {
            var timerHtml = '<div id="quiz-scheduler-timer" class="alert alert-info fixed-top" style="z-index: 9999; margin: 10px;">' +
                '<strong>Tempo restante do agendamento: <span id="timer-display"></span></strong>' +
                '</div>';
            $('body').prepend(timerHtml);
        },
        
        /**
         * Start countdown timer.
         * @param {number} timeleft Time left in seconds
         * @param {number} warningtime Warning time in seconds
         */
        startCountdown: function(timeleft, warningtime) {
            var self = this;
            var warningShown = false;
            
            var countdown = setInterval(function() {
                timeleft--;
                
                var hours = Math.floor(timeleft / 3600);
                var minutes = Math.floor((timeleft % 3600) / 60);
                var seconds = timeleft % 60;
                
                var display = '';
                if (hours > 0) {
                    display += hours + 'h ';
                }
                display += minutes + 'm ' + seconds + 's';
                
                $('#timer-display').text(display);
                
                // Change color based on time left
                if (timeleft <= warningtime && timeleft > 60) {
                    $('#quiz-scheduler-timer').removeClass('alert-info alert-success').addClass('alert-warning');
                    
                    if (!warningShown) {
                        notification.alert('Atenção', 'Seu tempo de agendamento está se esgotando. Você tem ' + 
                            Math.ceil(timeleft / 60) + ' minutos restantes.', 'OK');
                        warningShown = true;
                    }
                } else if (timeleft <= 60) {
                    $('#quiz-scheduler-timer').removeClass('alert-info alert-warning').addClass('alert-danger');
                }
                
                if (timeleft <= 0) {
                    clearInterval(countdown);
                    self.handleTimeExpired();
                }
            }, 1000);
        },
        
        /**
         * Handle when time expires.
         */
        handleTimeExpired: function() {
            $('#timer-display').text('EXPIRADO');
            
            notification.alert(
                'Tempo Esgotado', 
                'Seu tempo de agendamento expirou. O questionário será finalizado automaticamente.',
                'OK',
                function() {
                    // Force submit the quiz
                    if ($('#responseform').length) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'finishattempt',
                            value: '1'
                        }).appendTo('#responseform');
                        $('#responseform').submit();
                    } else {
                        // Redirect to quiz view page
                        window.location.reload();
                    }
                }
            );
        }
    };
    
    return timer;
});