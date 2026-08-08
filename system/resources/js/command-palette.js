/**
 * HTMLy Command Palette (Ctrl+K / Cmd+K)
 * Fast navigation and quick action modal for Admin Panel.
 */

(function () {
  'use strict';

  // Command Palette Items
  const COMMANDS = [
    { name: '✏️ Add New Post', url: '/admin/content', category: 'Action' },
    { name: '📄 Add New Page', url: '/add/page', category: 'Action' },
    { name: '📑 View My Drafts', url: '/admin/draft', category: 'Navigation' },
    { name: '📅 Scheduled Posts', url: '/admin/scheduled', category: 'Navigation' },
    { name: '📂 Manage Categories', url: '/admin/categories', category: 'Navigation' },
    { name: '💬 Comments', url: '/admin/comments', category: 'Navigation' },
    { name: '⚙️ Site Configuration', url: '/admin/config', category: 'Settings' },
    { name: '📖 HTMLy Documentation', url: 'https://docs.htmly.com', category: 'Help', external: true }
  ];

  let overlay = null;
  let input = null;
  let list = null;
  let selectedIndex = 0;
  let filteredCommands = [...COMMANDS];

  function createPaletteDOM() {
    overlay = document.createElement('div');
    overlay.className = 'htmly-cmd-overlay';
    overlay.innerHTML = `
      <div class="htmly-cmd-modal">
        <input type="text" class="htmly-cmd-input" placeholder="Type a command or search (e.g. Add Post)..." />
        <ul class="htmly-cmd-list"></ul>
      </div>
    `;

    document.body.appendChild(overlay);

    input = overlay.querySelector('.htmly-cmd-input');
    list = overlay.querySelector('.htmly-cmd-list');

    // Event Listeners
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) togglePalette(false);
    });

    input.addEventListener('input', onSearchInput);
    input.addEventListener('keydown', onKeyDown);

    renderList();
  }

  function renderList() {
    list.innerHTML = '';
    if (filteredCommands.length === 0) {
      list.innerHTML = `<li class="htmly-cmd-item" style="opacity:0.6; pointer-events:none;">No matching commands found</li>`;
      return;
    }

    filteredCommands.forEach((cmd, idx) => {
      const li = document.createElement('li');
      li.className = `htmly-cmd-item ${idx === selectedIndex ? 'selected' : ''}`;
      li.innerHTML = `
        <span>${cmd.name}</span>
        <span class="htmly-cmd-badge">${cmd.category}</span>
      `;
      li.addEventListener('click', () => executeCommand(cmd));
      list.appendChild(li);
    });
  }

  function onSearchInput() {
    const query = input.value.toLowerCase().trim();
    filteredCommands = COMMANDS.filter(cmd => 
      cmd.name.toLowerCase().includes(query) || 
      cmd.category.toLowerCase().includes(query)
    );
    selectedIndex = 0;
    renderList();
  }

  function onKeyDown(e) {
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      selectedIndex = (selectedIndex + 1) % filteredCommands.length;
      renderList();
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      selectedIndex = (selectedIndex - 1 + filteredCommands.length) % filteredCommands.length;
      renderList();
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (filteredCommands[selectedIndex]) {
        executeCommand(filteredCommands[selectedIndex]);
      }
    } else if (e.key === 'Escape') {
      togglePalette(false);
    }
  }

  function executeCommand(cmd) {
    togglePalette(false);
    if (cmd.external) {
      window.open(cmd.url, '_blank');
    } else {
      window.location.href = cmd.url;
    }
  }

  function togglePalette(show) {
    if (!overlay) createPaletteDOM();
    const isActive = show !== undefined ? show : !overlay.classList.contains('active');

    if (isActive) {
      overlay.classList.add('active');
      input.value = '';
      filteredCommands = [...COMMANDS];
      selectedIndex = 0;
      renderList();
      setTimeout(() => input.focus(), 50);
    } else {
      overlay.classList.remove('active');
      input.blur();
    }
  }

  // Global Keyboard Trigger (Ctrl+K or Cmd+K)
  document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      togglePalette();
    }
  });

  // Expose to window
  window.htmlyToggleCommandPalette = togglePalette;
})();
