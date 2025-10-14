define([], function() {
    'use strict';

    /**
     * Inicializa controles de repetição
     */
    function initRepeatControls() {
        const repeatSelect = document.getElementById('repeat-select');
        const repeatEndContainer = document.getElementById('repeat-end-container');
        const repeatEndDate = document.getElementById('repeat-end-date');
        const startDateInput = document.querySelector('input[name="generate_startdate"]');
        const form = document.querySelector('form[method="post"]');
        
        if (!repeatSelect) {
            return;
        }
        
        repeatSelect.addEventListener('change', function() {
            if (this.value !== 'none') {
                repeatEndContainer.style.display = 'flex';
                
                // Define data mínima baseada na data de início
                if (startDateInput && startDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    startDate.setDate(startDate.getDate() + 1);
                    repeatEndDate.min = startDate.toISOString().split('T')[0];
                } else {
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    repeatEndDate.min = tomorrow.toISOString().split('T')[0];
                }
                
                // Define data padrão (30 dias a partir da data de início ou hoje)
                if (!repeatEndDate.value) {
                    const defaultEnd = startDateInput && startDateInput.value 
                        ? new Date(startDateInput.value) 
                        : new Date();
                    defaultEnd.setDate(defaultEnd.getDate() + 30);
                    repeatEndDate.value = defaultEnd.toISOString().split('T')[0];
                }
            } else {
                repeatEndContainer.style.display = 'none';
                repeatEndDate.value = ''; // Limpa o valor
            }
        });
        
        // Atualizar data mínima quando a data de início mudar
        if (startDateInput) {
            startDateInput.addEventListener('change', function() {
                if (repeatSelect.value !== 'none' && this.value) {
                    const startDate = new Date(this.value);
                    startDate.setDate(startDate.getDate() + 1);
                    repeatEndDate.min = startDate.toISOString().split('T')[0];
                    
                    // Se a data final for anterior à nova data mínima, ajustar
                    if (repeatEndDate.value && new Date(repeatEndDate.value) < startDate) {
                        repeatEndDate.value = startDate.toISOString().split('T')[0];
                    }
                }
            });
        }
        
        // Validação no submit do formulário
        if (form) {
            form.addEventListener('submit', function(e) {
                // Só validar se uma opção de repetição estiver selecionada
                if (repeatSelect.value !== 'none') {
                    if (!repeatEndDate.value) {
                        e.preventDefault();
                        alert('Por favor, selecione a data final da repetição.');
                        repeatEndDate.focus();
                        return false;
                    }
                    
                    // Validar se a data final é posterior à data inicial
                    if (startDateInput && startDateInput.value) {
                        const startDate = new Date(startDateInput.value);
                        const endDate = new Date(repeatEndDate.value);
                        
                        if (endDate <= startDate) {
                            e.preventDefault();
                            alert('A data final da repetição deve ser posterior à data inicial.');
                            repeatEndDate.focus();
                            return false;
                        }
                    }
                }
                
                return true;
            });
        }
    }

    return {
        init: function() {
            // Aguardar o DOM estar pronto
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initRepeatControls);
            } else {
                initRepeatControls();
            }
        }
    };
});