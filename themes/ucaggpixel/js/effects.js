(function(){
  const petals = document.querySelector('.petals');
  document.addEventListener('keydown', (e) => {
    if (e.key.toLowerCase() === 'p' && petals) {
      petals.style.display = (petals.style.display === 'none') ? '' : 'none';
    }
  });
})();

// HUB hide/show toggle (checkbox) with localStorage
(function () {
  const hub = document.getElementById('hub-card');
  const toggle = document.getElementById('hubHideToggle');
  if (!hub || !toggle) return;

  const KEY = 'ucagg_hub_hidden';
  const apply = (hidden) => {
    hub.classList.toggle('hub-hidden', hidden);
    toggle.checked = hidden;
  };

  apply(localStorage.getItem(KEY) === '1');

  toggle.addEventListener('change', () => {
    const hidden = toggle.checked;
    localStorage.setItem(KEY, hidden ? '1' : '0');
    apply(hidden);
  });
})();