/**
 * FAQ Section accordion. Multiple items can be open at once (not a
 * strict single-open accordion) — each question toggles independently.
 * The open/closed height animation itself is pure CSS (grid-template-rows
 * 0fr/1fr off the .is-open class, see faq_section.scss); this only flips
 * that class and aria-expanded.
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
    });
}
