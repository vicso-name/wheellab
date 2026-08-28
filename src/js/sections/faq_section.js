/**
 * FAQ Section accordion. Multiple items can be open at once (not a
 * strict single-open accordion) — each question toggles independently.
 * The open/closed height animation itself is pure CSS (grid-template-rows
 * 0fr/1fr off the .is-open class, see faq_section.scss); this only flips
 * that class and aria-expanded.
 *
 * Load more: items past the first batch (BATCH_SIZE, from the button's
 * own data-batch-size — set server-side in faq_section.php, same value
 * used there to decide which items start hidden) render in the page
 * already, just hidden behind .faq-section__item--more (display: none
 * in faq_section.scss). Each click reveals one more batch; the button
 * itself is removed once no hidden items remain, same in both width
 * modes.
 */
document.addEventListener('DOMContentLoaded', () => {
    initFaqSections();
});

function initFaqSections() {
    document.querySelectorAll('.faq-section').forEach((section) => {
        section.querySelectorAll('.faq-section__question').forEach((button) => {
            button.addEventListener('click', () => {
                const item = button.closest('.faq-section__item');
                if (!item) return;

                const isOpen = item.classList.toggle('is-open');
                button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });

        const loadMoreButton = section.querySelector('.faq-section__load-more-btn');
        if (!loadMoreButton) return;

        loadMoreButton.addEventListener('click', () => {
            const batchSize = parseInt(loadMoreButton.dataset.batchSize, 10) || 5;
            const hiddenItems = section.querySelectorAll('.faq-section__item--more');

            hiddenItems.forEach((item, index) => {
                if (index < batchSize) {
                    item.classList.remove('faq-section__item--more');
                }
            });

            if (hiddenItems.length <= batchSize) {
                loadMoreButton.closest('.faq-section__load-more').remove();
            }
        });
    });
}
