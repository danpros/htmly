/*!
 * Editorial theme — main.js
 * Small, dependency-free progressive enhancement.
 * The page is fully usable and correctly styled without this file.
 *
 * Every feature below is wrapped in its own try/catch. That's
 * deliberate: several of these blocks repair quirks in HTMLy *core's*
 * own generated markup (search widget, breadcrumb, archive toggle)
 * which this theme can't test against directly. If any one of those
 * ever hits real-world markup it doesn't expect and throws, it must
 * never be able to take down the dark-mode toggle or the back-to-top
 * button with it.
 */
(function () {
  "use strict";

  function safe(fn) {
    try { fn(); } catch (e) { /* one feature failing must never break the rest */ }
  }

  /* ---- Dark / light toggle -------------------------------------- */
  safe(function () {
    var root = document.documentElement;
    var toggle = document.getElementById("theme-toggle");
    if (!toggle) return;

    function currentTheme() {
      return root.getAttribute("data-theme") ||
        (window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light");
    }

    toggle.addEventListener("click", function () {
      var next = currentTheme() === "dark" ? "light" : "dark";
      root.setAttribute("data-theme", next);
      try { localStorage.setItem("editorial-theme", next); } catch (e) {}
      toggle.setAttribute("aria-pressed", next === "dark" ? "true" : "false");
    });
  });

  /* ---- Back to top ------------------------------------------------ */
  safe(function () {
    var toTop = document.querySelector(".to-top");
    if (!toTop) return;
    var onScroll = function () {
      if (window.scrollY > 640) {
        toTop.classList.add("visible");
      } else {
        toTop.classList.remove("visible");
      }
    };
    document.addEventListener("scroll", onScroll, { passive: true });
    onScroll();

    // Belt-and-braces: don't rely solely on the native href="#top" anchor
    // jump (which still works as a no-JS fallback). Handling the click
    // directly guarantees the scroll happens regardless of how a given
    // browser resolves an anchor jump against the sticky header.
    toTop.addEventListener("click", function (e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  });

  /* ---- Close mobile nav after choosing a link --------------------- */
  safe(function () {
    var navToggle = document.getElementById("nav-toggle");
    if (!navToggle) return;
    document.querySelectorAll(".main-nav a").forEach(function (link) {
      link.addEventListener("click", function () { navToggle.checked = false; });
    });
  });

  /* ---- Accessible name for HTMLy core's search widget ------------
   * HTMLy's built-in search() function renders its own <form>/<input>
   * markup with no theme hook to add a <label>, id, or aria-label at
   * the source. Rather than guess the exact class/id core happens to
   * output, this targets any text/search input inside any <form> that
   * has no accessible name yet, and gives it one — a safe no-op
   * wherever a label already exists. */
  safe(function () {
    document.querySelectorAll('form input[type="text"], form input[type="search"]').forEach(function (el) {
      if (el.hasAttribute("aria-label") || el.getAttribute("aria-labelledby")) return;
      var id = el.getAttribute("id");
      if (id && window.CSS && CSS.escape && document.querySelector('label[for="' + CSS.escape(id) + '"]')) return;
      if (el.closest("label")) return;
      el.setAttribute("aria-label", "Search");
    });
  });

  /* ---- Valid list structure for core-generated markup -------------
   * Some of HTMLy's own widget/breadcrumb functions place non-<li>
   * content directly inside a <ul>/<ol> — most commonly a trailing
   * plain-text page title after the breadcrumb's <li> items. That's
   * invalid HTML (a list may only directly contain <li>,
   * <script>, or <template>) and the theme has no hook into that core
   * markup to fix it at the source. This repairs the rendered DOM:
   * any direct child of a list that is neither an allowed element nor
   * a stray non-<li> element gets wrapped in an <li> — including bare
   * text nodes, which `.children` alone would miss. Whitespace-only
   * text between list items is left alone (that's valid HTML). */
  safe(function () {
    document.querySelectorAll("ul, ol").forEach(function (list) {
      Array.prototype.slice.call(list.childNodes).forEach(function (child) {
        if (child.nodeType === 3) {
          // Text node: only wrap it if it actually has visible content.
          if (child.textContent.trim() === "") return;
          var textLi = document.createElement("li");
          list.insertBefore(textLi, child);
          textLi.appendChild(child);
          return;
        }
        if (child.nodeType !== 1) return; // ignore comments etc.
        var tag = child.tagName;
        if (tag !== "LI" && tag !== "SCRIPT" && tag !== "TEMPLATE") {
          var li = document.createElement("li");
          list.insertBefore(li, child);
          li.appendChild(child);
        }
      });
    });
  });

  /* ---- Make HTMLy core's archive-widget toggle crawlable-safe ------
   * HTMLy's archive_list() widget renders its expand/collapse toggles
   * as `<a href="javascript:void(0)" onclick="...">`, which search
   * engines correctly flag as "not crawlable" since the href isn't a
   * real destination. The theme has no hook into that core markup, so
   * this converts those specific anchors into real <button> elements
   * at runtime — buttons aren't evaluated by that check at all, and
   * they're the semantically correct element for a JS-only action
   * anyway. The inline onclick logic is preserved as-is (it only
   * touches `this.parentNode`, which still resolves the same way). */
  safe(function () {
    document.querySelectorAll('a[href="javascript:void(0)"], a[href="javascript:void(0);"]').forEach(function (a) {
      var btn = document.createElement("button");
      btn.type = "button";
      Array.prototype.forEach.call(a.attributes, function (attr) {
        if (attr.name !== "href") btn.setAttribute(attr.name, attr.value);
      });
      btn.innerHTML = a.innerHTML;
      a.parentNode.replaceChild(btn, a);
    });
  });

})();
