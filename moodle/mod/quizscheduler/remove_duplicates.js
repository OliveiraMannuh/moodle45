(function() {
    'use strict';
    
    // Executar IMEDIATAMENTE quando o script carregar
    function removeDuplicates() {
        var alerts = document.querySelectorAll('.alert-danger, .alert-error, [role="alert"]');
        var seen = new Map();
        
        alerts.forEach(function(alert) {
            var text = alert.textContent.trim();
            
            if (text.indexOf('Acesso negado') !== -1) {
                if (seen.has(text)) {
                    // Remover duplicata IMEDIATAMENTE
                    alert.parentNode.removeChild(alert);
                    console.log('Duplicata removida');
                } else {
                    seen.set(text, true);
                }
            }
        });
    }
    
    // Executar múltiplas vezes
    removeDuplicates();
    setTimeout(removeDuplicates, 1);
    setTimeout(removeDuplicates, 10);
    setTimeout(removeDuplicates, 50);
    setTimeout(removeDuplicates, 100);
    
    // Quando DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', removeDuplicates);
    }
    
    // Observar mudanças
    var observer = new MutationObserver(removeDuplicates);
    observer.observe(document.documentElement, {
        childList: true,
        subtree: true
    });
})();