/**
 * Single post sidebar — Table of Contents scroll-spy. Highlights
 * whichever <h2> section is currently in view as the reader scrolls;
 * clicking a TOC link still works as a plain anchor link even if this
 * script never runs.
 */
document.addEventListener("DOMContentLoaded", () => {
  initSinglePostToc();
});

function initSinglePostToc() {
  const toc = document.querySelector(".single-post-toc");
  if (!toc || typeof IntersectionObserver === "undefined") return;

  const links = toc.querySelectorAll(".single-post-toc__link");
  if (!links.length) return;

  const linkByAnchor = new Map();
  const headings = [];

  links.forEach((link) => {
    const anchor = link.dataset.tocAnchor;
    const heading = anchor ? document.getElementById(anchor) : null;
    if (!heading) return;
    linkByAnchor.set(heading, link);
    headings.push(heading);
  });

  if (!headings.length) return;

  const setActive = (activeHeading) => {
    links.forEach((link) => link.classList.remove("is-active"));
    const activeLink = linkByAnchor.get(activeHeading);
    if (activeLink) activeLink.classList.add("is-active");
  };

  // A heading counts as "current" once it crosses a line near the top
  // of the viewport, rather than only while fully visible — matches
  // how reading position naturally tracks past each section.
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) setActive(entry.target);
      });
    },
    { rootMargin: "-15% 0px -70% 0px", threshold: 0 }
  );

  headings.forEach((heading) => observer.observe(heading));
  setActive(headings[0]);
}
