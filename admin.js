const adminDeleteButtons = document.querySelectorAll('[data-confirm]');

adminDeleteButtons.forEach((button) => {
    button.addEventListener('click', (event) => {
        const message = button.getAttribute('data-confirm');
        if (!confirm(message)) {
            event.preventDefault();
        }
    });
});
