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

const filterButtons = document.querySelectorAll('[data-filter]');
const taskRows = document.querySelectorAll('.task-row');
const filterEmpty = document.querySelector('.filter-empty');

filterButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const filter = button.dataset.filter;
        let visibleCount = 0;

        filterButtons.forEach((candidate) => candidate.classList.toggle('is-active', candidate === button));

        taskRows.forEach((row) => {
            const isVisible = filter === 'all' || row.dataset.status === filter;
            row.hidden = !isVisible;
            visibleCount += Number(isVisible);
        });

        if (filterEmpty) {
            filterEmpty.hidden = visibleCount !== 0;
        }
    });
});

document.querySelector('.flash-message button')?.addEventListener('click', (event) => {
    event.currentTarget.closest('.flash-message')?.remove();
});

const descriptionInput = document.querySelector('#description');

const resizeDescription = () => {
    if (!descriptionInput) {
        return;
    }

    descriptionInput.style.height = 'auto';
    descriptionInput.style.height = `${Math.min(descriptionInput.scrollHeight, 240)}px`;
};

descriptionInput?.addEventListener('input', resizeDescription);
resizeDescription();

document.querySelectorAll('[data-dialog-open]').forEach((button) => {
    button.addEventListener('click', () => {
        document.getElementById(button.dataset.dialogOpen)?.showModal();
    });
});

document.querySelectorAll('[data-dialog-close]').forEach((button) => {
    button.addEventListener('click', () => {
        button.closest('dialog')?.close();
    });
});

const dashboardDeactivateDialog = document.querySelector('#dashboard-deactivate-member');

document.querySelectorAll('[data-deactivate-member]').forEach((button) => {
    button.addEventListener('click', () => {
        dashboardDeactivateDialog.querySelector('[data-dialog-member-name]').textContent = button.dataset.memberName;
        dashboardDeactivateDialog.querySelector('[data-deactivate-form]').action = button.dataset.memberAction;
        dashboardDeactivateDialog.showModal();
    });
});
