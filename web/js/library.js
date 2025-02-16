document.addEventListener('DOMContentLoaded', () => {
    console.log('JS is connected and ready!');
});

// Обработка клика по карточке книги
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.book-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            // Если клик был не по кнопке "Подробнее" или кнопке избранного
            if (!e.target.closest('.btn') && !e.target.closest('.favorite-btn')) {
                const url = this.getAttribute('data-book-url');
                if (url) {
                    window.location.href = url;
                }
            }
        });
    });
});
