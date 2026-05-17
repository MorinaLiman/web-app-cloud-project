const searchInput = document.getElementById('catalogSearch');
const cards = document.querySelectorAll('[data-product-card]');

if (searchInput) {
    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        cards.forEach((card) => {
            const title = card.getAttribute('data-title');
            const author = card.getAttribute('data-author');
            const match = title.includes(query) || author.includes(query);
            card.style.display = match ? 'block' : 'none';
        });
    });
}
