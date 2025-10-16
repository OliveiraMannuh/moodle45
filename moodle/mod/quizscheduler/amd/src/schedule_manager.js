import $ from 'jquery';
import Notification from 'core/notification';

export const init = () => {
    window.console.log('Schedule Manager inicializado');
    
    const form = $('#schedule-form');
    const previewButton = $('#preview-button');
    const previewContainer = $('#schedule-preview');
    const startDateInput = $('#start-date');
    const endDateInput = $('#end-date');

    // Validar data final quando data inicial mudar
    startDateInput.on('change', function() {
        const startDate = $(this).val();
        if (startDate) {
            endDateInput.attr('min', startDate);
            
            const endDate = endDateInput.val();
            if (endDate && endDate < startDate) {
                endDateInput.val(startDate);
            }
        }
        updatePreviewIfVisible();
    });

    endDateInput.on('change', updatePreviewIfVisible);

    function updatePreviewIfVisible() {
        if (previewContainer.children().length > 0) {
            const slots = calculateSlots();
            previewContainer.html(formatPreview(slots));
        }
    }

    // Função para calcular slots
    const calculateSlots = () => {
        const startDate = startDateInput.val();
        const endDate = endDateInput.val();
        const startTime = $('#start-time').val();
        const endTime = $('#end-time').val();
        const slotDuration = parseInt($('#slot-duration').val());
        const selectedWeekdays = $('.weekday-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();

        if (!startDate || !endDate || !startTime || !endTime || selectedWeekdays.length === 0) {
            return null;
        }

        if (endDate < startDate) {
            return null;
        }

        const start = new Date(startDate + 'T00:00:00');
        const end = new Date(endDate + 'T23:59:59');
        const slots = [];

        const currentDate = new Date(start);
        while (currentDate <= end) {
            const dayOfWeek = currentDate.getDay();
            
            if (selectedWeekdays.includes(dayOfWeek)) {
                const [startHour, startMin] = startTime.split(':').map(Number);
                const [endHour, endMin] = endTime.split(':').map(Number);
                
                let currentTime = startHour * 60 + startMin;
                const endTimeMin = endHour * 60 + endMin;
                
                while (currentTime + slotDuration <= endTimeMin) {
                    const slotStart = new Date(currentDate);
                    slotStart.setHours(Math.floor(currentTime / 60), currentTime % 60, 0);
                    
                    const slotEnd = new Date(slotStart);
                    slotEnd.setMinutes(slotEnd.getMinutes() + slotDuration);
                    
                    slots.push({
                        start: slotStart,
                        end: slotEnd
                    });
                    
                    currentTime += slotDuration;
                }
            }
            
            currentDate.setDate(currentDate.getDate() + 1);
        }

        return slots;
    };

    const formatPreview = (slots) => {
        if (!slots || slots.length === 0) {
            return '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Nenhum horário será gerado. Verifique os campos.</div>';
        }

        const maxUsers = $('#max-users').val();
        let html = `<div class="alert alert-success">
            <strong><i class="fa fa-check-circle"></i> ${slots.length} horário${slots.length > 1 ? 's' : ''} será${slots.length > 1 ? 'ão' : ''} criado${slots.length > 1 ? 's' : ''}</strong>
            <br><small>${maxUsers} usuário(s) por horário</small>
        </div>`;
        
        html += '<div class="schedule-preview-list" style="max-height: 400px; overflow-y: auto;">';
        html += '<table class="table table-sm table-striped table-hover">';
        html += '<thead class="thead-light"><tr><th>Data</th><th>Dia</th><th>Horário</th></tr></thead><tbody>';
        
        const groupedByDate = {};
        const weekdayNames = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        
        slots.forEach(slot => {
            const dateKey = slot.start.toLocaleDateString('pt-BR');
            if (!groupedByDate[dateKey]) {
                groupedByDate[dateKey] = {
                    slots: [],
                    weekday: weekdayNames[slot.start.getDay()],
                    date: slot.start
                };
            }
            groupedByDate[dateKey].slots.push(slot);
        });

        Object.keys(groupedByDate).sort((a, b) => {
            return groupedByDate[a].date - groupedByDate[b].date;
        }).forEach(date => {
            const dayData = groupedByDate[date];
            const daySlots = dayData.slots;
            
            html += `<tr><td rowspan="${daySlots.length}" class="align-middle"><strong>${date}</strong></td>`;
            html += `<td rowspan="${daySlots.length}" class="align-middle">${dayData.weekday}</td>`;
            
            daySlots.forEach((slot, index) => {
                if (index > 0) html += '<tr>';
                html += `<td>${slot.start.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})} - `;
                html += `${slot.end.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})}</td></tr>`;
            });
        });

        html += '</tbody></table></div>';
        return html;
    };

    previewButton.on('click', function(e) {
        e.preventDefault();
        const slots = calculateSlots();
        previewContainer.html(formatPreview(slots));
    });

    form.find('input, select').on('change', updatePreviewIfVisible);

    form.on('submit', function(e) {
        const startDate = startDateInput.val();
        const endDate = endDateInput.val();
        const startTime = $('#start-time').val();
        const endTime = $('#end-time').val();
        
        if (!startDate || !endDate) {
            e.preventDefault();
            Notification.addNotification({
                message: 'Por favor, selecione as datas de início e término.',
                type: 'error'
            });
            return false;
        }
        
        if (!startTime || !endTime) {
            e.preventDefault();
            Notification.addNotification({
                message: 'Por favor, selecione os horários.',
                type: 'error'
            });
            return false;
        }
        
        if (endTime <= startTime) {
            e.preventDefault();
            Notification.addNotification({
                message: 'O horário de término deve ser posterior ao de início.',
                type: 'error'
            });
            return false;
        }
        
        return true;
    });
};