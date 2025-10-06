document.addEventListener('DOMContentLoaded', function() {
    // Check if we are on the dashboard page before running dashboard-specific code
    if (document.querySelector('.dashboard-layout')) {
        // The festivals data is passed from the dashboard.php file via a data attribute on the body
        const festivals = JSON.parse(document.body.dataset.festivals || '{}');

        // --- Live Clock ---
        const clockElement = document.getElementById('live-clock');
        function updateClock() {
            if (clockElement) {
                const now = new Date();
                clockElement.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // --- Festival Calendar ---
        const monthYearElement = document.getElementById('month-year');
        const calendarDaysElement = document.getElementById('calendar-days');
        const prevMonthBtn = document.getElementById('prev-month');
        const nextMonthBtn = document.getElementById('next-month');
        let currentDate = new Date();

        const modal = document.getElementById('festival-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalDesc = document.getElementById('modal-description');
        const modalDate = document.getElementById('modal-date');
        const closeModalBtn = document.getElementById('close-modal-btn');

        function showFestivalModal(dateKey) {
            const event = festivals[dateKey];
            if (!event || !modal) return;
            
            modalTitle.textContent = event.name;
            modalDesc.textContent = event.description;
            modalDate.textContent = new Date(dateKey + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            modal.classList.add('active');
        }

        function hideFestivalModal() {
            if(modal) modal.classList.remove('active');
        }
        
        if(closeModalBtn) closeModalBtn.addEventListener('click', hideFestivalModal);
        if(modal) modal.addEventListener('click', (e) => { if (e.target === modal) hideFestivalModal(); });

        function renderCalendar() {
            if (!calendarDaysElement) return;
            const month = currentDate.getMonth();
            const year = currentDate.getFullYear();
            monthYearElement.textContent = new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'long' }).format(currentDate);
            calendarDaysElement.innerHTML = '';
            const firstDayOfMonth = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            for (let i = 0; i < firstDayOfMonth; i++) { calendarDaysElement.innerHTML += `<div></div>`; }

            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                const span = document.createElement('span');
                span.textContent = day;
                dayElement.appendChild(span);

                const today = new Date();
                if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    dayElement.classList.add('today');
                }
                const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                if (festivals[dateString]) {
                    dayElement.classList.add('festival');
                    dayElement.addEventListener('click', () => showFestivalModal(dateString));
                }
                calendarDaysElement.appendChild(dayElement);
            }
        }
        
        if(prevMonthBtn) prevMonthBtn.addEventListener('click', () => { currentDate.setMonth(currentDate.getMonth() - 1); renderCalendar(); });
        if(nextMonthBtn) nextMonthBtn.addEventListener('click', () => { currentDate.setMonth(currentDate.getMonth() + 1); renderCalendar(); });
        
        renderCalendar();
    }
});

