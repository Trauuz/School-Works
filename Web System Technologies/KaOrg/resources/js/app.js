const emailLinkParameters = new URLSearchParams(window.location.hash.slice(1));
if (!document.querySelector('[data-auth-callback]') && document.body.dataset.authCallbackUrl && (emailLinkParameters.has('access_token') || emailLinkParameters.has('error_code'))) {
    window.location.replace(document.body.dataset.authCallbackUrl + window.location.hash);
}

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
            greeting.textContent = weather.condition ? `${weather.condition}. ${weather.message}` : weather.message;
            const details = document.querySelector('[data-weather-meta]');
            const updated = weather.updated_at ? new Date(weather.updated_at) : null;
            if (details && weather.city) {
                details.textContent = `${weather.city}${updated && !Number.isNaN(updated.valueOf()) ? ` | Updated ${updated.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}` : ''} | `;
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


const authCallback = document.querySelector('[data-auth-callback]');
if (authCallback) {
    const parameters = new URLSearchParams(window.location.hash.slice(1));
    window.history.replaceState(null, '', window.location.pathname);
    const form = authCallback.querySelector('[data-callback-form]');
    const accessToken = parameters.get('access_token');
    const refreshToken = parameters.get('refresh_token');
    const type = parameters.get('type') || authCallback.dataset.flow;
    if (!parameters.has('error') && !parameters.has('error_code') && accessToken && refreshToken && ['email', 'signup', 'recovery'].includes(type)) {
        form.elements.access_token.value = accessToken;
        form.elements.refresh_token.value = refreshToken;
        form.elements.type.value = type;
        form.submit();
    } else {
        authCallback.querySelector('[data-callback-title]').textContent = 'This email link is unavailable';
        authCallback.querySelector('[data-callback-message]').textContent = 'The link is incomplete or has expired. Request a new confirmation email or password reset below.';
    }
}

const resend = document.querySelector('[data-resend-countdown]');
if (resend) {
 const until = Date.now() + Number(resend.dataset.resendCountdown) * 1000;
 const tick = () => { const seconds = Math.max(0, Math.ceil((until-Date.now())/1000)); resend.disabled = seconds > 0; resend.textContent = seconds ? `Resend available in ${seconds}s` : 'Resend confirmation email'; };
 tick(); const timer = setInterval(() => { tick(); if (Date.now() >= until) clearInterval(timer); }, 1000);
 resend.form.addEventListener('submit', () => { resend.disabled = true; resend.textContent = 'Requesting email...'; });
}
const verification = document.querySelector('[data-verification-status]');
if (verification && verification.querySelector('[data-check-verification]')) {
 let checking = false;
 const check = async () => { if(checking) return; checking=true; const message=verification.querySelector('[data-verification-message]'); try { const response=await fetch(verification.dataset.verificationStatus,{headers:{Accept:'application/json'},cache:'no-store'}); if(!response.ok) throw new Error(); const state=await response.json(); if(state.verified) { message.textContent='Email confirmed. Continue to your account.'; const link=document.createElement('a'); link.href=state.continue_url; link.textContent='Continue'; link.className='submit-button'; message.append(link); } else message.textContent='Not confirmed yet. Open the confirmation link in your email.'; } catch { message.textContent='Unable to check right now. Please try again shortly.'; } finally {checking=false;} };
 verification.querySelector('[data-check-verification]').addEventListener('click',check); window.addEventListener('focus',check);
}
