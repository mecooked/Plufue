<?php
require_once 'db.php'; 

$sql = "SELECT * FROM projects ORDER BY id DESC";
$result = $conn->query($sql);
$projects =[];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}
$cat_map =[
    'ecommerce' => 'коммерция',
    'services' => 'сервисы',
    'branding' => 'брендинг',
    'sites' => 'сайты'
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>все проекты - Plufue</title>
<link rel="stylesheet" href="projects.css">
</head>
<body>

  <header class="top-header">
    
    <div class="button-wrapper">
        <button class="menu-toggle-btn" id="menuToggleBtn">
            <a href="index.php" class="logo-link">
                <img src="images/logo.svg" alt="Логотип" class="logo-img">
            </a>
            <span></span>
            <img src="images/burger.svg" alt="">
        </button>
    </div>

    <!-- ВЫПАДАЮЩЕЕ МЕНЮ -->
    <nav class="dropdown-overlay" id="dropdownMenu">
      <ul class="dropdown-links">
        <li><a href="index.php">Главная</a></li>
        <li><a href="projects.php" style="color: rgba(0, 0, 0, 0.264);;">Проекты</a></li>
        <li><a href="bids.php">Обсудить проект</a></li>
        <li><a href="index.php #contacts-section">Контакты</a></li>
      </ul>
      <img src="images/x.svg" class="x-close" id="x-close">
    </nav>
  </header>

<main>
  
<div class="container-all" id="container-all">
  <div class="global-vertical-line"></div>
  
  <section class="hero-section">
    <div class="hero-left">
      <h1 class="page-title">ВСЕ<br>ПРОЕКТЫ</h1>
      
      <div class="search-wrapper">
        <input type="text" id="searchInput" class="search-input" placeholder="ПОИСК">
        <button class="search-icon-btn" id="search-icon-btn" name="search-icon-btn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </button>
      </div>
      
      <div class="filters">
        <button class="filter-btn active" data-filter="все">все</button>
        <button class="filter-btn" data-filter="брендинг">брендинг</button>
        <button class="filter-btn" data-filter="коммерция">коммерция</button>
        <button class="filter-btn" data-filter="сервисы">сервисы</button>
        <button class="filter-btn" data-filter="сайты">сайты</button>
      </div>
    </div>
    
    <div class="hero-right"></div>
  </section>

  <section class="projects-grid" id="projectsGrid">
    
    <?php foreach($projects as $p): ?>
    
    <?php 
      $raw_cat = mb_strtolower(trim($p['category']), 'UTF-8');
      $display_category = array_key_exists($raw_cat, $cat_map) ? $cat_map[$raw_cat] : $raw_cat;
      $clean_title = mb_strtolower(trim($p['title']), 'UTF-8');
    ?>

    <div class="project-wrapper" data-title="<?= htmlspecialchars($clean_title) ?>" data-category="<?= htmlspecialchars($display_category) ?>">
      
      <a href="view_project.php?id=<?= $p['id'] ?>" class="project-card">
        
        <div class="card-bg" style="background-image: url('images/<?= htmlspecialchars($p['image_path']) ?>');"></div>
        <div class="card-overlay"></div>

        <div class="card-top">
          <ul class="card-tags">
            <?php 
              $tags_string = $p['tags'] ?? ''; 
              if(!empty($tags_string)) {
                  $normalized_tags = str_replace([",", "\n", "\r", "\t"], " ", $tags_string);
                  $tags_array = preg_split('/\s+/', $normalized_tags, -1, PREG_SPLIT_NO_EMPTY);
                  
                  foreach($tags_array as $tag) {
                      $clean_tag = trim(str_replace('#', '', $tag));
                      if(!empty($clean_tag)) {
                          echo "<li>#" . htmlspecialchars($clean_tag) . "</li>";
                      }
                  }
              }
            ?>
          </ul>
          
          <div class="card-duration">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <?= htmlspecialchars($p['duration'] ?? '1 месяц') ?>
          </div>
        </div>

        <div class="card-bottom">
          <h2 class="card-title"><?= htmlspecialchars($p['title']) ?></h2>
          <span class="card-category">#<?= htmlspecialchars($display_category) ?></span>
        </div>
      </a>

    </div> 
    
    <?php endforeach; ?>

  </section>

</main>

<footer class="main-footer">
    <div class="footer-relative-box">
      
      <img src="images/flower2.png" alt="" class="flower flower-top">
      <img src="images/flower1.png" alt="" class="flower flower-bottom">

      <h2 class="footer-logo">plufue</h2>

      <div class="footer-socials">
        <a href="https://vk.com" class="social-link"><img src="images/vk.svg" alt="VK"></a>
        <a href="https://pinterest.com" class="social-link"><img src="images/pinterest.png" alt="Pinterest"></a>
        <a href="https://behance.com" class="social-link"><img src="images/behance.png" alt="Behance"></a>
        <a href="https://dribble.com" class="social-link"><img src="images/dribble.png" alt="Dribbble"></a>
      </div>

      <div class="footer-copyright">
        все права защищены plufue &copy;
      </div>
      
    </div>
  </footer>

<script>
  const searchInput = document.getElementById('searchInput');
  const filterBtns = document.querySelectorAll('.filter-btn');
  const wrappers = document.querySelectorAll('.project-wrapper');
  let currentFilter = 'все';

  function updateProjects() {
    const searchText = searchInput.value.trim().toLowerCase();
    
    // Создаем пустой массив, чтобы сложить туда только те карточки, которые сейчас покажем
    let visibleCards = []; 

    wrappers.forEach(wrapper => {
      const title = wrapper.getAttribute('data-title');
      const category = wrapper.getAttribute('data-category');
      
      const matchFilter = (currentFilter === 'все' || category === currentFilter);
      const matchSearch = title.includes(searchText);

      if (matchFilter && matchSearch) {
        wrapper.style.display = 'block'; 
        visibleCards.push(wrapper); // Закидываем карточку в список видимых
      } else {
        wrapper.style.display = 'none';
      }
    });

    // гсап: Анимируем появление только видимых карточек
    if (typeof gsap !== 'undefined' && visibleCards.length > 0) {
      gsap.fromTo(visibleCards, 
        { y: 50, opacity: 0 }, // Откуда начинаем (чуть ниже и прозрачные)
        { 
          y: 0, 
          opacity: 1, 
          duration: 0.6, 
          stagger: 0.05, // Задержка между появлением каждой карточки (каскад)
          ease: "back.out(1.5)", // Пружинящий эффект как на слайдере
          overwrite: "auto" // Сбрасываем старую анимацию, если кликают быстро
        }
      );
    }
  }

  // Поиск по кнопке-лупе
  const searchBtn = document.getElementById('search-icon-btn'); 
  searchBtn.addEventListener('click', updateProjects);

  // Поиск по нажатию клавиши Enter (чтобы было удобнее)
  searchInput.addEventListener('keyup', (e) => {
    if (e.key === 'Enter') updateProjects();
  });
  
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      
      currentFilter = btn.getAttribute('data-filter').trim().toLowerCase();
      updateProjects(); // Запускает фильтрацию и анимацию
    });
  });

  // ЗАПУСК АНИМАЦИИ ПРИ САМОЙ ПЕРВОЙ ЗАГРУЗКЕ СТРАНИЦЫ
  window.addEventListener('load', () => {
    updateProjects();
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.getElementById('menuToggleBtn');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const conAll = document.getElementById('container-all');
    const xClose = document.getElementById('x-close');
    // const links = document.querySelector('linked');

    menuBtn.addEventListener('click', () => {
      dropdownMenu.classList.toggle('active');
      conAll.classList.toggle('active');
    });

    xClose.addEventListener('click', () => {
      dropdownMenu.classList.toggle('active');
      conAll.classList.toggle('active');
    });

    // links.addEventListener('click', () => {
    //   dropdownMenu.classList.toggle('active');
    //   conAll.classList.toggle('active');
    // });
    
    

    // Опционально: закрывать меню при клике вне его области
    document.addEventListener('click', (event) => {
      if (!menuBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.classList.remove('active');
        conAll.classList.remove('active');
      }
    });
  });
</script>
</div>
<script src="libs/gsap-public/minified/gsap.min.js"></script>
<script src="libs/gsap-public/minified/ScrollTrigger.min.js"></script>
<script src="projects.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/smoothscroll/1.4.10/SmoothScroll.min.js" integrity="sha256-huW7yWl7tNfP7lGk46XE+Sp0nCotjzYodhVKlwaNeco=" crossorigin="anonymous"></script>
</body>
</html>