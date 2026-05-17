const contactForm = document.getElementById('contactForm');
const statusEl = document.getElementById('contactStatus');

if (contactForm) {
    contactForm.addEventListener('submit', () => {
        if (statusEl) {
            statusEl.textContent = 'Duke derguar mesazhin...';
        }
    });
}
