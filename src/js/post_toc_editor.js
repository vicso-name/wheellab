(function (wp) {
  const META_KEY = "_wheellab_toc_labels";
  const { createElement: el, useMemo } = wp.element;
  const { useSelect, useDispatch } = wp.data;
  const { PluginDocumentSettingPanel } = wp.editor;
  const { TextControl } = wp.components;
  const { __ } = wp.i18n;

  function normalizeHeadingText(text) {
    return String(text || "").replace(/\s+/g, " ").trim();
  }

  function collectHeadings(content) {
    if (!content || typeof window.DOMParser === "undefined") {
      return [];
    }

    const doc = new window.DOMParser().parseFromString(
      `<div id="wheellab-toc-editor-root">${content}</div>`,
      "text/html"
    );
    const root = doc.getElementById("wheellab-toc-editor-root");

    if (!root) {
      return [];
    }

    const occurrences = new Map();

    return Array.from(root.querySelectorAll("h2")).flatMap((heading) => {
      // Matches wheellab_add_heading_anchors_and_toc(): H2s rendered inside
      // standalone <section> blocks are not part of Article content.
      if (heading.closest("section")) {
        return [];
      }

      const text = normalizeHeadingText(heading.textContent);
      if (!text) {
        return [];
      }

      const occurrence = (occurrences.get(text) || 0) + 1;
      occurrences.set(text, occurrence);

      return [{
        key: `${occurrence}|${text}`,
        text,
      }];
    });
  }

  function parseLabels(raw) {
    if (!raw || typeof raw !== "string") {
      return {};
    }

    try {
      const labels = JSON.parse(raw);
      return labels && typeof labels === "object" && !Array.isArray(labels)
        ? labels
        : {};
    } catch {
      return {};
    }
  }

  function ArticleContentPanel() {
    const editorState = useSelect((select) => {
      const editor = select("core/editor");
      const meta = editor.getEditedPostAttribute("meta") || {};

      return {
        content: editor.getEditedPostAttribute("content") || "",
        meta,
        labelsRaw: meta[META_KEY] || "",
      };
    }, []);

    const headings = useMemo(
      () => collectHeadings(editorState.content),
      [editorState.content]
    );
    const labels = useMemo(
      () => parseLabels(editorState.labelsRaw),
      [editorState.labelsRaw]
    );
    const { editPost } = useDispatch("core/editor");

    const updateLabel = (headingKey, value) => {
      const nextLabels = { ...labels };
      const cleanValue = String(value || "").trim();

      if (cleanValue) {
        nextLabels[headingKey] = cleanValue;
      } else {
        delete nextLabels[headingKey];
      }

      editPost({
        meta: {
          ...editorState.meta,
          [META_KEY]: Object.keys(nextLabels).length
            ? JSON.stringify(nextLabels)
            : "",
        },
      });
    };

    return el(
      PluginDocumentSettingPanel,
      {
        name: "wheellab-article-content",
        title: __("Article content", "wheellab"),
      },
      el(
        "p",
        null,
        __(
          "Set a shorter table-of-contents title without changing the heading in the article. Leave a field empty to use the original H2.",
          "wheellab"
        )
      ),
      headings.length
        ? headings.map((heading) => el(TextControl, {
          key: heading.key,
          label: heading.text,
          placeholder: heading.text,
          value: labels[heading.key] || "",
          onChange: (value) => updateLabel(heading.key, value),
          __next40pxDefaultSize: true,
          __nextHasNoMarginBottom: false,
        }))
        : el(
          "p",
          null,
          __("No H2 headings were found in the article content.", "wheellab")
        )
    );
  }

  wp.plugins.registerPlugin("wheellab-article-content", {
    render: ArticleContentPanel,
  });
})(window.wp);
