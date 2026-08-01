/**
 * Blog page — AJAX category filter + "load more" pagination.
 * Requires `wheellabBlog` (ajaxUrl + nonce), localized in inc/enqueue.php
 * only when template-blog.php is active. The first page of results is
 * already rendered server-side (see template-parts/sections/blog_filter.php)
 * — this script only handles subsequent interactions.
 */
/* global wheellabBlog */

document.addEventListener("DOMContentLoaded", () => {
  initBlogFilter();
});

function initBlogFilter() {
  const section = document.querySelector(".blog-filter");
  if (!section || typeof wheellabBlog === "undefined") return;

  const grid = section.querySelector(".blog-filter__grid");
  const countNumber = section.querySelector(".blog-filter__count-number");
  const loadMoreWrap = section.querySelector(".blog-filter__load-more");
  const loadMoreBtn = loadMoreWrap ? loadMoreWrap.querySelector("button") : null;
  const chips = section.querySelectorAll(".blog-filter__chip");
  if (!grid) return;

  // Guards against out-of-order responses: if the user clicks two chips in
  // quick succession, only the reply matching the most recent request is
  // applied — an earlier, slower response can no longer clobber it.
  let requestToken = 0;

  chips.forEach((chip) => {
    chip.addEventListener("click", () => {
      const alreadyActive = chip.classList.contains("is-active");

      chips.forEach((c) => {
        c.classList.remove("is-active");
        c.setAttribute("aria-selected", "false");
      });

      // Clicking the active chip again clears the filter — there's no
      // separate "All" chip in the design (node 527:28120).
      const category = alreadyActive ? "" : chip.dataset.category;

      if (!alreadyActive) {
        chip.classList.add("is-active");
        chip.setAttribute("aria-selected", "true");
      }

      requestToken += 1;
      fetchBlogPage({ grid, countNumber, loadMoreWrap, loadMoreBtn, category, page: 1, append: false, token: requestToken, getToken: () => requestToken });
    });
  });

  if (loadMoreBtn) {
    loadMoreBtn.addEventListener("click", () => {
      const category = grid.dataset.category || "";
      const nextPage = (parseInt(grid.dataset.page, 10) || 1) + 1;

      requestToken += 1;
      fetchBlogPage({ grid, countNumber, loadMoreWrap, loadMoreBtn, category, page: nextPage, append: true, token: requestToken, getToken: () => requestToken });
    });
  }
}

function fetchBlogPage({ grid, countNumber, loadMoreWrap, loadMoreBtn, category, page, append, token, getToken }) {
  grid.classList.add("is-loading");
  grid.setAttribute("aria-busy", "true");
  if (loadMoreBtn) loadMoreBtn.disabled = true;

  const body = new URLSearchParams({
    action: "wheellab_blog_query",
    nonce: wheellabBlog.nonce,
    category: category || "",
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
      if (!data || !data.success) throw new Error("Blog query returned an error");

      const { html, foundPosts, maxPages, page: currentPage } = data.data;

      if (append) {
        grid.insertAdjacentHTML("beforeend", html);
      } else {
        grid.innerHTML = html;
      }

      grid.dataset.page = String(currentPage);
      grid.dataset.maxPages = String(maxPages);
      grid.dataset.category = category || "";

      if (countNumber) countNumber.textContent = String(foundPosts);
      if (loadMoreWrap) loadMoreWrap.hidden = currentPage >= maxPages;
    })
    .catch((error) => {
      console.error("Blog query failed:", error);
    })
    .finally(() => {
      if (token !== getToken()) return;
      grid.classList.remove("is-loading");
      grid.removeAttribute("aria-busy");
      if (loadMoreBtn) loadMoreBtn.disabled = false;
    });
}
