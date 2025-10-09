/**
 * Quiz timer with automatic submission.
 *
 * @module     mod_quizscheduler/quiz_timer
 * @package    mod_quizscheduler
 * @copyright  2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification', 'core/ajax'], function($, notification, ajax) {
    
    return {
        init: function(timeleft, warningtime, quizid) {
            this.createTimerDisplay();
            this.startCountdown(timeleft, warningtime, quizid);
        },
        
        createTimerDisplay: function() {
            if ($('#quiz-scheduler-timer').length > 0) {
                return; // Timer already exists
            }
            
            var timerHtml = '<div id="quiz-scheduler-timer" class="alert alert-info" ' +
                'style="position: fixed; top: 10px; right: 10px; z-index: 9999; min-width: 250px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">' +
                '<strong><i class="fa fa-clock-o"></i> Tempo do agendamento: <span id="timer-display"></span></strong>' +
                '</div>';
            $('body').prepend(timerHtml);
        },
        
        startCountdown: function(timeleft, warningtime, quizid) {
            var self = this;
            var warningShown = false;
            var finalWarningShown = false;
            
            var countdown = setInterval(function() {
                timeleft--;
                
                var hours = Math.floor(timeleft / 3600);
                var minutes = Math.floor((timeleft % 3600) / 60);
                var seconds = timeleft % 60;
                
                var display = '';
                if (hours > 0) {
                    display += hours + 'h ';
                }
                display += minutes.toString().padStart(2, '0') + 'm ' + 
                          seconds.toString().padStart(2, '0') + 's';
                
                $('#timer-display').text(display);
                
                // Change styling based on time left
                if (timeleft <= warningtime && timeleft > 60) {
                    $('#quiz-scheduler-timer').removeClass('alert-info alert-success').addClass('alert-warning');
                    
                    if (!warningShown) {
                        notification.alert(
                            'Atenção - Tempo Esgotando', 
                            'Seu tempo de agendamento está se esgotando. Você tem ' + 
                            Math.ceil(timeleft / 60) + ' minutos restantes. Finalize o questionário o quanto antes.',
                            'Entendi'
                        );
                        warningShown = true;
                    }
                } else if (timeleft <= 60 && timeleft > 0) {
                    $('#quiz-scheduler-timer').removeClass('alert-info alert-warning').addClass('alert-danger');
                    
                    if (!finalWarningShown) {
                        notification.alert(
                            'URGENTE - Último Minuto!', 
                            'Você tem apenas 1 minuto restante! O questionário será finalizado automaticamente quando o tempo expirar.',
                            'Entendi'
                        );
                        finalWarningShown = true;
                    }
                }
                
                if (timeleft <= 0) {
                    clearInterval(countdown);
                    self.handleTimeExpired(quizid);
                }
            }, 1000);
        },
        
        handleTimeExpired: function(quizid) {
            $('#timer-display').text('EXPIRADO');
            $('#quiz-scheduler-timer').removeClass('alert-danger').addClass('alert-dark');
            
            // Try to auto-submit quiz if on attempt page
            if (window.location.href.indexOf('/mod/quiz/attempt.php') !== -1) {
                notification.confirm(
                    'Tempo Esgotado',
                    'Seu tempo de agendamento expirou. O questionário deve ser finalizado agora. Suas respostas atuais serão salvas.',
                    'Finalizar Agora',
                    'Aguardar (não recomendado)',
                    function() {
                        this.forceSubmitQuiz();
                    }.bind(this),
                    function() {
                        // If user chooses to wait, warn about access loss
                        notification.alert(
                            'Aviso Important',
                            'Você pode perder acesso ao questionário a qualquer momento. Finalize assim que possível.',
                            'OK'
                        );
                    }
                );
            } else {
                notification.alert(
                    'Tempo Esgotado',
                    'Seu tempo de agendamento expirou. Você não pode mais acessar este questionário.',
                    'OK',
                    function() {
                        // Redirect to course page or scheduler
                        if (document.referrer) {
                            window.location.href = document.referrer;
                        } else {
                            window.location.reload();
                        }
                    }
                );
            }
        },
        
        forceSubmitQuiz: function() {
            // Try different methods to submit the quiz
            if ($('#responseform').length) {
                // Add finishattempt parameter and submit
                $('<input>').attr({
                    type: 'hidden',
                    name: 'finishattempt',
                    value: '1'
                }).appendTo('#responseform');
                
                // Show loading indicator
                $('body').append('<div id="submitting-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 99999; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;"><div><i class="fa fa-spinner fa-spin"></i> Finalizando questionário...</div></div>');
                
                $('#responseform').submit();
            } else if ($('input[name="finishattempt"]').length) {
                // Click finish attempt button if exists
                $('input[name="finishattempt"]').click();
            } else if ($('.submitbtns input[type="submit"]').length) {
                // Try to click any submit button
                $('.submitbtns input[type="submit"]').first().click();
            } else {
                // Fallback: try to navigate away (will trigger Moodle's unsaved changes warning)
                if (confirm('Não foi possível finalizar automaticamente. Deseja sair do questionário? (Suas respostas podem não ser salvas)')) {
                    window.location.reload();
                }
            }
        }
    };
});