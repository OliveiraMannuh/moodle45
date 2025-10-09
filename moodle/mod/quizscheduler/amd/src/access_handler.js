/**
 * Access handler for quiz scheduler.
 *
 * @module     mod_quizscheduler/access_handler
 * @package    mod_quizscheduler
 * @copyright  2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/notification'], function(notification) {
    
    return {
        handleAccessDenied: function(message, redirectUrl) {
            // Show modal and redirect after user acknowledges
            notification.confirm(
                'Acesso ao Questionário Negado',
                '<div class="text-center mb-3">' +
                    '<i class="fa fa-exclamation-triangle fa-3x text-warning"></i>' +
                '</div>' +
                '<div class="alert alert-warning">' +
                    '<strong>Agendamento Obrigatório</strong><br>' +
                    message +
                '</div>' +
                '<p class="text-muted small">' +
                    'Para acessar este questionário, você precisa agendar um horário específico.' +
                '</p>',
                'Ir para Agendamento',
                'Voltar',
                function() {
                    window.location.href = redirectUrl;
                },
                function() {
                    window.history.back();
                }
            );
        }
    };
});