/**
 * Ultra-aggressive quiz access interceptor.
 *
 * @module     mod_quizscheduler/interceptor
 * @package    mod_quizscheduler
 * @copyright  2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
    
    var interceptor = {
        
        /**
         * Initialize ultra-aggressive interception.
         */
        init: function() {
            this.interceptAllQuizAccess();
            this.monitorPageChanges();
            this.blockDirectAccess();
        },
        
        /**
         * Intercept ALL possible quiz access methods.
         */
        interceptAllQuizAccess: function() {
            var self = this;
            
            // Intercept ALL links containing quiz
            $(document).on('click', 'a[href*="quiz"]', function(e) {
                var href = $(this).attr('href');
                if (self.isQuizUrl(href)) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    self.checkAccessBeforeNavigation(href);
                    return false;
                }
            });
            
            // Intercept ALL forms that might lead to quiz
            $(document).on('submit', 'form', function(e) {
                var action = $(this).attr('action');
                if (action && self.isQuizUrl(action)) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    self.checkAccessBeforeSubmit($(this));
                    return false;
                }
            });
            
            // Intercept ALL buttons that might start quiz
            $(document).on('click', 'input[type="submit"], button[type="submit"], button', function(e) {
                var form = $(this).closest('form');
                if (form.length && self.isQuizUrl(form.attr('action'))) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    self.checkAccessBeforeSubmit(form);
                    return false;
                }
            });
            
            // Check current page if it's a quiz page
            if (self.isQuizUrl(window.location.href)) {
                self.checkCurrentPageAccess();
            }
        },
        
        /**
         * Monitor page changes and check access.
         */
        monitorPageChanges: function() {
            var self = this;
            
            // Monitor URL changes (for AJAX navigation)
            var currentUrl = window.location.href;
            setInterval(function() {
                if (window.location.href !== currentUrl) {
                    currentUrl = window.location.href;
                    if (self.isQuizUrl(currentUrl)) {
                        self.checkCurrentPageAccess();
                    }
                }
            }, 1000);
        },
        
        /**
         * Block direct access to quiz pages.
         */
        blockDirectAccess: function() {
            var self = this;
            
            // If we're already on a quiz page, check access immediately
            if (self.isQuizUrl(window.location.href)) {
                // Give page a moment to load, then check
                setTimeout(function() {
                    self.forceAccessCheck();
                }, 500);
            }
        },
        
        /**
         * Check if URL is quiz-related.
         */
        isQuizUrl: function(url) {
            if (!url) return false;
            
            return url.indexOf('/mod/quiz/') !== -1 ||
                   url.indexOf('quiz') !== -1 && (
                       url.indexOf('view.php') !== -1 ||
                       url.indexOf('attempt.php') !== -1 ||
                       url.indexOf('startattempt.php') !== -1 ||
                       url.indexOf('review.php') !== -1
                   );
        },
        
        /**
         * Get quiz ID from URL or form.
         */
        getQuizId: function(url, form) {
            var quizId = null;
            
            // Try URL parameters
            if (url) {
                var urlObj = new URL(url, window.location.origin);
                var id = urlObj.searchParams.get('id');
                var quizid = urlObj.searchParams.get('quizid');
                var attemptid = urlObj.searchParams.get('attempt');
                
                if (quizid) {
                    return parseInt(quizid);
                }
                
                if (id || attemptid) {
                    // Use AJAX to get quiz ID
                    this.getQuizIdViaAjax(id, attemptid, function(result) {
                        quizId = result;
                    });
                }
            }
            
            // Try form inputs
            if (form && form.length) {
                var cmid = form.find('input[name="cmid"]').val();
                var quizidField = form.find('input[name="quizid"]').val();
                
                if (quizidField) {
                    return parseInt(quizidField);
                }
                
                if (cmid) {
                    this.getQuizIdViaAjax(cmid, null, function(result) {
                        quizId = result;
                    });
                }
            }
            
            return quizId;
        },
        
        /**
         * Get quiz ID via AJAX.
         */
        getQuizIdViaAjax: function(cmid, attemptid, callback) {
            var calls = [];
            
            if (cmid) {
                calls.push({
                    methodname: 'mod_quizscheduler_get_quiz_id',
                    args: { cmid: parseInt(cmid) }
                });
            }
            
            if (calls.length > 0) {
                ajax.call(calls, true, true)[0].done(function(result) {
                    callback(result.quizid || null);
                }).fail(function() {
                    callback(null);
                });
            }
        },
        
        /**
         * Check access before navigation.
         */
        checkAccessBeforeNavigation: function(url) {
            var self = this;
            var quizId = self.getQuizId(url);
            
            if (quizId) {
                self.verifyAccessAndProceed(quizId, function() {
                    window.location.href = url;
                });
            } else {
                // If we can't determine quiz ID, allow navigation
                window.location.href = url;
            }
        },
        
        /**
         * Check access before form submission.
         */
        checkAccessBeforeSubmit: function(form) {
            var self = this;
            var quizId = self.getQuizId(form.attr('action'), form);
            
            if (quizId) {
                self.verifyAccessAndProceed(quizId, function() {
                    form.off().submit();
                });
            } else {
                // If we can't determine quiz ID, allow submission
                form.off().submit();
            }
        },
        
        /**
         * Check current page access.
         */
        checkCurrentPageAccess: function() {
            var self = this;
            var quizId = self.getQuizId(window.location.href);
            
            if (quizId) {
                self.verifyAccessAndProceed(quizId, function() {
                    // Access allowed, do nothing
                }, true);
            }
        },
        
        /**
         * Force access check for current page.
         */
        forceAccessCheck: function() {
            var self = this;
            
            // Extract quiz ID from current page
            var urlParams = new URLSearchParams(window.location.search);
            var id = urlParams.get('id');
            var quizid = urlParams.get('quizid');
            var attemptid = urlParams.get('attempt');
            
            if (quizid) {
                self.verifyAccessAndProceed(parseInt(quizid), function() {}, true);
            } else if (id) {
                self.getQuizIdViaAjax(id, attemptid, function(quizId) {
                    if (quizId) {
                        self.verifyAccessAndProceed(quizId, function() {}, true);
                    }
                });
            }
        },
        
        /**
         * Verify access and proceed or block.
         */
        verifyAccessAndProceed: function(quizId, successCallback, forceCheck) {
            var self = this;
            
            ajax.call([{
                methodname: 'mod_quizscheduler_check_access',
                args: { quizid: quizId }
            }])[0].done(function(result) {
                if (result.allowed) {
                    if (successCallback) {
                        successCallback();
                    }
                    // Initialize timer if we have time left
                    if (result.timeleft > 0) {
                        self.initTimer(result.timeleft, quizId);
                    }
                } else {
                    // Access denied - show modal and redirect
                    self.showAccessDeniedAndRedirect(result, forceCheck);
                }
            }).fail(function() {
                // If AJAX fails and it's not a force check, allow access
                if (!forceCheck && successCallback) {
                    successCallback();
                }
            });
        },
        
        /**
         * Show access denied modal and force redirect.
         */
        showAccessDeniedAndRedirect: function(result, forceRedirect) {
            var message = 
                '<div class="text-center mb-3">' +
                    '<i class="fa fa-exclamation-triangle fa-3x text-warning"></i>' +
                '</div>' +
                '<div class="alert alert-danger">' +
                    '<strong>Acesso Negado!</strong><br>' +
                    result.message +
                '</div>' +
                '<p><strong>Para acessar este questionário você deve:</strong></p>' +
                '<ol>' +
                    '<li>Agendar um horário disponível</li>' +
                    '<li>Acessar o questionário apenas no horário marcado</li>' +
                '</ol>';
            
            if (forceRedirect && result.redirect_url) {
                // Force immediate redirect
                notification.alert(
                    'Acesso ao Questionário Negado',
                    message,
                    'Ir para Agendamento',
                    function() {
                        window.location.href = result.redirect_url;
                    }
                );
                
                // Also redirect after 3 seconds regardless of user action
                setTimeout(function() {
                    window.location.href = result.redirect_url;
                }, 3000);
            } else {
                notification.confirm(
                    'Acesso ao Questionário Negado',
                    message,
                    'Ir para Agendamento',
                    'Voltar',
                    function() {
                        if (result.redirect_url) {
                            window.location.href = result.redirect_url;
                        }
                    },
                    function() {
                        window.history.back();
                    }
                );
            }
        },
        
        /**
         * Initialize timer.
         */
        initTimer: function(timeLeft, quizId) {
            require(['mod_quizscheduler/quiz_timer'], function(timer) {
                timer.init(timeLeft, 300, quizId);
            });
        }
    };
    
    return {
        init: function() {
            $(document).ready(function() {
                interceptor.init();
            });
        }
    };
});