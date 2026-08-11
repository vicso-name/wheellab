/**
 * Author page — "load more" pagination only (no category chips, unlike
 * the Blog page). Posts through the same `wheellab_blog_query` AJAX
 * action as blog_filter.js (see inc/ajax_blog.php), with an `author` id
 * instead of a `category` slug. Requires `wheellabBlog` (ajaxUrl + nonce),
 * localized in inc/enqueue.php only when is_author() is true. The first
 * page is already rendered server-side (template-parts/sections/
 * author_posts.php) — this script only handles "load more" clicks.
 */
/* global wheellabBlog */

document.addEventListener("DOMContentLoaded", () => {
  initAuthorPosts();
});

function initAuthorPosts() {
  const section = document.querySelector(".author-posts");
  if (!section || typeof wheellabBlog === "undefined") return;

  const grid = section.querySelector(".blog-filter__grid");
  const countNumber = section.querySelector(".blog-filter__count-number");
  const loadMoreWrap = section.querySelector(".blog-filter__load-more");
  const loadMoreBtn = loadMoreWrap ? loadMoreWrap.querySelector("button") : null;
  if (!grid || !loadMoreBtn) return;

  const authorId = grid.dataset.author || "";
  let requestToken = 0;

  loadMoreBtn.addEventListener("click", () => {
    const nextPage = (parseInt(grid.dataset.page, 10) || 1) + 1;
    requestToken += 1;
    fetchAuthorPage({ grid, countNumber, loadMoreWrap, loadMoreBtn, authorId, page: nextPage, token: requestToken, getToken: () => requestToken });
  });
}

function fetchAuthorPage({ grid, countNumber, loadMoreWrap, loadMoreBtn, authorId, page, token, getToken }) {
  grid.classList.add("is-loading");
  grid.setAttribute("aria-busy", "true");
  loadMoreBtn.disabled = true;

  const body = new URLSearchParams({
    action: "wheellab_blog_query",
    nonce: wheellabBlog.nonce,
    author: authorId,
    page: String(page),
  });

  fetch(wheellabBlog.ajaxUrl, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body,
  })
    .then((response) => response.json())
    .then((data) => {
      if (token !== getToken()) return; // a newer request has since started
      if (!data || !data.success) throw new Error("Author posts query returned an error");

      const { html, foundPosts, maxPages, page: currentPage } = data.data;

      grid.insertAdjacentHTML("beforeend", html);
      grid.dataset.page = String(currentPage);
      grid.dataset.maxPages = String(maxPages);

      if (countNumber) countNumber.textContent = `${foundPosts} articles`;
      if (loadMoreWrap) loadMoreWrap.hidden = currentPage >= maxPages;
    })
    .catch((error) => {
      console.error("Author posts query failed:", error);
    })
    .finally(() => {
      if (token !== getToken()) return;
      grid.classList.remove("is-loading");
      grid.removeAttribute("aria-busy");
      loadMoreBtn.disabled = false;
    });
}
