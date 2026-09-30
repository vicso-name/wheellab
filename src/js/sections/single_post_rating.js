/* global wheellabRating */

document.addEventListener('DOMContentLoaded', () => {
    const widgets = document.querySelectorAll('.single-post-rating');

    if (!widgets.length || typeof wheellabRating === 'undefined') {
        return;
    }

    widgets.forEach((widget) => {
        const postId = widget.dataset.postId;
        const stars = Array.from(widget.querySelectorAll('.single-post-rating__star'));
        const averageEl = widget.querySelector('[data-role="average"]');
        const countEl = widget.querySelector('[data-role="count-text"]');
        const feedbackEl = widget.querySelector('.single-post-rating__feedback');
        let currentRating = Number(widget.dataset.userRating) || 0;
        let feedbackTimer;

        // Fills every star up to `rating` while leaving `aria-pressed` and the
        // labels tied to the rating that is actually saved, so hovering never
        // tells assistive tech that a different value is selected.
        const highlightUpTo = (rating) => {
            stars.forEach((star) => {
                const value = Number(star.dataset.rating);
                const isCurrent = value === currentRating;
                const setLabel = star.dataset.labelSet || '';
                const clearLabel = star.dataset.labelClear || '';

                star.classList.toggle('is-active', value <= rating);
                star.setAttribute('aria-pressed', String(isCurrent));
                star.title = isCurrent ? clearLabel : setLabel;
                star.setAttribute('aria-label', isCurrent ? `${setLabel}. ${clearLabel}` : setLabel);
            });
        };

        const showFeedback = (message, isError = false) => {
            if (!feedbackEl) return;

            window.clearTimeout(feedbackTimer);
            feedbackEl.textContent = message;
            feedbackEl.classList.toggle('is-error', isError);
            feedbackEl.classList.add('is-visible');
            feedbackTimer = window.setTimeout(() => {
                feedbackEl.classList.remove('is-visible');
            }, 2600);
        };

        const updateSummary = (data) => {
            if (averageEl) {
                averageEl.textContent = Number(data.average).toFixed(1);
            }
            if (countEl) {
                const count = Number(data.count);
                const template = count === 1 ? wheellabRating.personSingular : wheellabRating.personPlural;
                countEl.textContent = template.replace('%s', String(count));
            }
        };

        const submitRating = async (nextRating) => {
            widget.classList.add('is-submitting');
            stars.forEach((button) => { button.disabled = true; });

            const body = new URLSearchParams({
                action: 'wheellab_rate_post',
                nonce: wheellabRating.nonce,
                post_id: postId,
                rating: String(nextRating),
            });

            try {
                const response = await fetch(wheellabRating.ajaxUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body,
                });
                const json = await response.json();
                const data = json.data || {};
                if (!response.ok || !json.success) throw new Error(data.message || wheellabRating.feedbackError);

                currentRating = Number(data.rating) || 0;
                widget.dataset.userRating = String(currentRating);
                widget.classList.toggle('is-rated', currentRating > 0);
                highlightUpTo(currentRating);
                updateSummary(data);

                const messages = {
                    added: wheellabRating.feedbackAdded,
                    changed: wheellabRating.feedbackChanged,
                    cancelled: wheellabRating.feedbackCancelled,
                };
                showFeedback(messages[data.action] || wheellabRating.feedbackAdded);
            } catch (error) {
                highlightUpTo(currentRating);
                showFeedback(error.message || wheellabRating.feedbackError, true);
            } finally {
                widget.classList.remove('is-submitting');
                stars.forEach((button) => { button.disabled = false; });
            }
        };

        stars.forEach((star) => {
            const value = Number(star.dataset.rating);

            star.addEventListener('mouseenter', () => highlightUpTo(value));
            star.addEventListener('mouseleave', () => highlightUpTo(currentRating));
            star.addEventListener('focus', () => highlightUpTo(value));
            star.addEventListener('blur', () => highlightUpTo(currentRating));

            star.addEventListener('click', () => {
                if (widget.classList.contains('is-submitting')) return;

                // Clicking the star that is already selected clears the rating;
                // any other star changes it.
                submitRating(value === currentRating ? 0 : value);
            });
        });

        highlightUpTo(currentRating);
    });
});
