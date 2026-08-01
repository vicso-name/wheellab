/**
 * Contact Section — Contact Form 7 behaviour that CSS alone can't do:
 * 1. Segmented "Preferred messenger" radio group — toggles .is-active on
 *    the checked option's wpcf7-list-item (CF7 doesn't add a class itself).
 * 2. Submit button label swaps to "Sending..." while the AJAX request is
 *    in flight (input[type=submit] can't take a CSS ::after spinner — it's
 *    a replaced element, browsers just ignore generated content on it).
 * 3. Post-submit "Request sent" state (node 743:8062) — CF7 dispatches
 *    wpcf7mailsent on its form element after a successful AJAX submit; we
 *    catch it, clear the form, and swap the form card into its "sent" look
 *    for 4 seconds before reverting to the (now empty) form.
 */

const SENT_OVERLAY_DURATION = 4000;
const SENDING_LABEL = "Sending...";

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".contact-section__form-card").forEach((card) => {
    syncMessengerToggle(card);
    initSubmitButton(card);
  });
});

document.addEventListener("wpcf7mailsent", (event) => {
  const card = event.target.closest(".contact-section__form-card");
  if (!card) return;

  const form = event.target.matches("form") ? event.target : event.target.querySelector("form");
  if (form) form.reset();

  // form.reset() doesn't fire "change" events, so the segmented messenger
  // toggle's .is-active class needs a manual re-sync back to its default.
  syncMessengerToggle(card);

  card.classList.add("is-sent");
  setTimeout(() => {
    card.classList.remove("is-sent");
  }, SENT_OVERLAY_DURATION);
});

function syncMessengerToggle(card) {
  const items = card.querySelectorAll(".wpcf7-radio .wpcf7-list-item");
  if (!items.length) return;

  const sync = () => {
    items.forEach((item) => {
      const input = item.querySelector('input[type="radio"]');
      item.classList.toggle("is-active", Boolean(input && input.checked));
    });
  };

  // Re-running this on an already-initialized card (after a form reset)
  // would otherwise stack duplicate listeners — guard with a data flag.
  if (!card.dataset.messengerToggleBound) {
    items.forEach((item) => {
      const input = item.querySelector('input[type="radio"]');
      if (input) input.addEventListener("change", sync);
    });
    card.dataset.messengerToggleBound = "true";
  }

  sync();
}

function initSubmitButton(card) {
  const form = card.querySelector("form");
  const submitBtn = card.querySelector(".contact-section__submit .wpcf7-submit");
  if (!form || !submitBtn) return;

  const defaultLabel = submitBtn.value;

  form.addEventListener("submit", () => {
    submitBtn.value = SENDING_LABEL;
    submitBtn.disabled = true;
  });

  // Fires once CF7's AJAX request resolves, whatever the outcome (sent,
  // invalid, spam, mail failed) — the one place to always restore the button.
  form.addEventListener("wpcf7submit", () => {
    submitBtn.value = defaultLabel;
    submitBtn.disabled = false;
  });
}
