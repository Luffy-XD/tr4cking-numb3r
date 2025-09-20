document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('[data-toggle]');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            const target = document.querySelector(toggle.dataset.toggle);
            if (target) {
                target.classList.toggle('hidden');
            }
        });
    });

    const closeButtons = document.querySelectorAll('[data-close]');
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const target = document.querySelector(button.dataset.close);
            if (target) {
                target.classList.add('hidden');
            }
        });
    });

    const toast = document.querySelector('[data-toast]');
    if (toast) {
        setTimeout(() => {
            toast.classList.add('opacity-0');
        }, 4000);
    }
});
