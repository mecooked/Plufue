  function toggleDropdown() {
    document.querySelector('.dropdown').classList.toggle('open');
    document.querySelector('.arrow').classList.toggle('open');
  }

  function selectOption(value, text) {
    document.querySelector('.selected').textContent = text;
    document.getElementById('hiddenInput').value = value;
    document.querySelector('.dropdown').classList.remove('open');
    document.querySelector('.arrow').classList.remove('open');
  }

  // Закрывать при клике вне dropdown
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown')) {
      document.querySelector('.dropdown').classList.remove('open');
      document.querySelector('.arrow').classList.remove('open');
    }
  });

