const menuButton = document.querySelector('.menu-button');
const sidebar = document.querySelector('.sidebar');

menuButton?.addEventListener('click', () => {
    const isOpen = sidebar.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
});

sidebar?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
        sidebar.classList.remove('is-open');
        menuButton?.setAttribute('aria-expanded', 'false');
    });
});

document.querySelector('.flash-message button')?.addEventListener('click', (event) => {
    event.currentTarget.closest('.flash-message')?.remove();
});

document.querySelector('[data-print]')?.addEventListener('click', () => window.print());

document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});


const roleSelect = document.querySelector('[data-account-role]');
const memberFields = document.querySelector('[data-member-fields]');
const showRoleFields = () => {
    if (!roleSelect || !memberFields) return;
    const isMember = roleSelect.value === 'member';
    memberFields.hidden = !isMember;
    memberFields.disabled = !isMember;
};
roleSelect?.addEventListener('change', showRoleFields);
showRoleFields();

const greeting = document.querySelector('[data-weather-url]');
if (greeting) {
    fetch(greeting.dataset.weatherUrl, { headers: { Accept: 'application/json' } })
        .then((response) => { if (!response.ok) throw new Error(); return response.json(); })
        .then((weather) => {
            greeting.textContent = weather.message;
            const details = document.querySelector('[data-weather-meta]');
            const updated = weather.updated_at ? new Date(weather.updated_at) : null;
            if (details && weather.city) {
                details.textContent = `${weather.city}${updated && !Number.isNaN(updated.valueOf()) ? ` · Updated ${updated.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}` : ''} · `;
            }
        })
        .catch(() => { greeting.textContent = 'Weather is temporarily unavailable.'; });
}

if (document.querySelector('[data-address-form]')) {
    import('./address-map.js').then(({ initializeAddress }) => initializeAddress()).catch(() => {
        const status = document.querySelector('[data-map-status]');
        if (status) status.textContent = 'The map could not load. Enter coordinates directly or leave the pin blank.';
    });
}
