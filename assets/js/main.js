// CareerCompass — Main JS

// Auto-close flash messages
document.addEventListener('DOMContentLoaded', function () {
  const flash = document.querySelector('.flash');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity .5s';
      flash.style.opacity = '0';
      setTimeout(() => flash.remove(), 500);
    }, 4000);
  }

  // Confirm delete buttons
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function (e) {
      if (!confirm(this.dataset.confirm || 'Are you sure?')) e.preventDefault();
    });
  });

  // Star rating hover fix (RTL trick)
  document.querySelectorAll('.star-rating').forEach(wrap => {
    const labels = wrap.querySelectorAll('label');
    labels.forEach((lbl, i) => {
      lbl.addEventListener('mouseenter', () => {
        labels.forEach((l, j) => l.style.color = j <= i ? 'var(--gold)' : '#D1D5DB');
      });
    });
    wrap.addEventListener('mouseleave', () => {
      const checked = wrap.querySelector('input:checked');
      const idx = checked ? parseInt(checked.value) - 1 : -1;
      labels.forEach((l, j) => l.style.color = j <= idx ? 'var(--gold)' : '#D1D5DB');
    });
  });

  // Career filter tabs
  document.querySelectorAll('[data-filter-btn]').forEach(btn => {
    btn.addEventListener('click', function () {
      const group = this.dataset.filterBtn;
      const val   = this.dataset.value;
      document.querySelectorAll(`[data-filter-btn="${group}"]`).forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      document.querySelectorAll('[data-filter]').forEach(card => {
        const match = val === 'all' || card.dataset.filter === val;
        card.style.display = match ? '' : 'none';
      });
    });
  });

  // Character counter
  document.querySelectorAll('[data-maxlength]').forEach(el => {
    const max = parseInt(el.dataset.maxlength);
    const counter = document.createElement('div');
    counter.className = 'form-hint text-right';
    counter.textContent = `0 / ${max}`;
    el.parentNode.appendChild(counter);
    el.addEventListener('input', () => {
      counter.textContent = `${el.value.length} / ${max}`;
      counter.style.color = el.value.length > max * .9 ? '#DC2626' : '';
    });
  });
});
