/**
 * Service Reel Section — chapter thumbnail strip seeks video playback to
 * a timestamp on click, uniformly across all three supported sources:
 * a plain <video> (data-video-type="file"), a YouTube iframe (via the
 * YouTube IFrame API's seekTo()), or a Vimeo iframe (via the Vimeo
 * Player SDK's setCurrentTime()). Both third-party SDKs are lazy-loaded
 * on demand — only when a player of that type actually exists on the
 * page — rather than enqueued sitewide.
 */
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("[data-reel-player]").forEach(initReelPlayer);
});

function initReelPlayer(root) {
  const videoType = root.dataset.videoType;
  const mediaEl = root.querySelector("[data-reel-video]");
  const wrap = root.closest(".service-reel-section__player-wrap");
  const chapterButtons = wrap ? Array.from(wrap.querySelectorAll("[data-reel-chapter]")) : [];
  if (!mediaEl || !chapterButtons.length) return;

  const setActiveChapter = (button) => {
    chapterButtons.forEach((btn) => btn.classList.toggle("is-active", btn === button));
  };

  const bindChapters = (seekAndPlay) => {
    chapterButtons.forEach((button) => {
      button.addEventListener("click", () => {
        const seconds = parseFloat(button.dataset.timestamp) || 0;
        seekAndPlay(seconds);
        setActiveChapter(button);
      });
    });
  };

  if (videoType === "file") {
    bindChapters((seconds) => {
      mediaEl.currentTime = seconds;
      mediaEl.play();
    });
    return;
  }

  if (videoType === "youtube") {
    loadYouTubeApi().then((YT) => {
      const player = new YT.Player(mediaEl, {
        events: {
          onReady: () => {
            bindChapters((seconds) => {
              player.seekTo(seconds, true);
              player.playVideo();
            });
          },
        },
      });
    });
    return;
  }

  if (videoType === "vimeo") {
    loadVimeoApi().then((Vimeo) => {
      const player = new Vimeo.Player(mediaEl);
      bindChapters((seconds) => {
        player.setCurrentTime(seconds).then(() => player.play());
      });
    });
  }
}

function loadYouTubeApi() {
  if (window.YT && window.YT.Player) return Promise.resolve(window.YT);
  if (!window.__wheellabYouTubeApiPromise) {
    window.__wheellabYouTubeApiPromise = new Promise((resolve) => {
      const previousCallback = window.onYouTubeIframeAPIReady;
      window.onYouTubeIframeAPIReady = () => {
        if (typeof previousCallback === "function") previousCallback();
        resolve(window.YT);
      };
      const script = document.createElement("script");
      script.src = "https://www.youtube.com/iframe_api";
      document.head.appendChild(script);
    });
  }
  return window.__wheellabYouTubeApiPromise;
}

function loadVimeoApi() {
  if (window.Vimeo && window.Vimeo.Player) return Promise.resolve(window.Vimeo);
  if (!window.__wheellabVimeoApiPromise) {
    window.__wheellabVimeoApiPromise = new Promise((resolve) => {
      const script = document.createElement("script");
      script.src = "https://player.vimeo.com/api/player.js";
      script.addEventListener("load", () => resolve(window.Vimeo));
      document.head.appendChild(script);
    });
  }
  return window.__wheellabVimeoApiPromise;
}
