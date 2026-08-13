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

        if (widget.dataset.rated === '1') {
            return;
        }

        const highlightUpTo = (rating) => {
            stars.forEach((star) => {
                star.classList.toggle('is-active', Number(star.dataset.rating) <= rating);
            });
        };

        const lock = (rating) => {
            widget.classList.add('is-rated');
            widget.dataset.rated = '1';
            stars.forEach((star) => {
                star.disabled = true;
            });
            highlightUpTo(rating);
        };

        stars.forEach((star) => {
            star.addEventListener('mouseenter', () => {
                if (widget.dataset.rated !== '1') {
                    highlightUpTo(Number(star.dataset.rating));
                }
            });

            star.addEventListener('mouseleave', () => {
                if (widget.dataset.rated !== '1') {
                    highlightUpTo(0);
                }
            });

            star.addEventListener('click', () => {
                if (widget.dataset.rated === '1' || widget.classList.contains('is-submitting')) {
                    return;
                }

                const rating = Number(star.dataset.rating);
                widget.classList.add('is-submitting');

                const body = new URLSearchParams({
                    action: 'wheellab_rate_post',
                    nonce: wheellabRating.nonce,
                    post_id: postId,
                    rating: String(rating),
                });

                fetch(wheellabRating.ajaxUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body,
                })
                    .then((response) => response.json())
                    .then((json) => {
                        const data = json.data || {};

                        if (json.success) {
                            if (averageEl) {
                                averageEl.textContent = Number(data.average).toFixed(1);
                            }
                            if (countEl) {
                                const template = data.count === 1 ? wheellabRating.personSingular : wheellabRating.personPlural;
                                countEl.textContent = template.replace('%s', data.count);
                            }
                            lock(rating);
                        } else if (data.already_rated) {
                            if (averageEl && typeof data.average !== 'undefined') {
                                averageEl.textContent = Number(data.average).toFixed(1);
                            }
                            if (countEl && typeof data.count !== 'undefined') {
                                const template = data.count === 1 ? wheellabRating.personSingular : wheellabRating.personPlural;
                                countEl.textContent = template.replace('%s', data.count);
                            }
                            lock(data.rating || rating);
                        }
                    })
                    .catch(() => {
                        // Network/server error: leave the widget interactive so the
                        // visitor can simply try again, rather than getting stuck.
                    })
                    .finally(() => {
                        widget.classList.remove('is-submitting');
                    });
            });
        });
    });
});
