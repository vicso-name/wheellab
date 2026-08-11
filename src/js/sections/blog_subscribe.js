/**
 * Blog Subscribe — Mailchimp newsletter sign-up (AJAX, no page reload).
 * Requires `wheellabMailchimp` (ajaxUrl + nonce), localized in
 * inc/enqueue.php only when template-blog.php is active and the Subscribe
 * banner is actually enabled (see wheellab_enqueue_scripts()).
 */
/* global wheellabMailchimp */

document.addEventListener("DOMContentLoaded", () => {
  initBlogSubscribe();
});

function initBlogSubscribe() {
  const form = document.querySelector(".blog-subscribe__form");
  if (!form || typeof wheellabMailchimp === "undefined") return;

  const input = form.querySelector(".blog-subscribe__input");
  const button = form.querySelector(".blog-subscribe__submit");
  const status = document.querySelector(".blog-subscribe__status");

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    if (!input || !input.checkValidity()) {
      if (input) input.reportValidity();
      return;
    }

    setStatus(status, "", null);
    if (button) button.disabled = true;

    const body = new URLSearchParams({
      action: "wheellab_mailchimp_subscribe",
      nonce: wheellabMailchimp.nonce,
      email: input.value.trim(),
    });

    fetch(wheellabMailchimp.ajaxUrl, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body,
    })
      .then((response) => response.json())
      .then((data) => {
        const message = data && data.data && data.data.message ? data.data.message : "";

        if (data && data.success) {
          setStatus(status, message, "is-success");
          form.reset();
        } else {
          setStatus(status, message || wheellabMailchimp.genericError, "is-error");
        }
      })
      .catch((error) => {
        console.error("Mailchimp subscribe failed:", error);
        setStatus(status, wheellabMailchimp.genericError, "is-error");
      })
      .finally(() => {
        if (button) button.disabled = false;
      });
  });
}

function setStatus(status, message, variant) {
  if (!status) return;

  status.textContent = message;
  status.classList.remove("is-success", "is-error");
  if (variant) status.classList.add(variant);
  status.hidden = !message;
}
