<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>главная - plufue</title>
    <link rel="stylesheet" href="style.css">
    <script src="libs/gsap-public/minified/gsap.min.js"></script>
    <script src="libs/gsap-public/minified/ScrollTrigger.min.js"></script>
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
        <li class="linked"><a href="index.php" style="color: rgba(0, 0, 0, 0.264);">Главная</a></li>
        <li class="linked"><a href="projects.php">Проекты</a></li>
        <li class="linked"><a href="bids.php">Обсудить проект</a></li>
        <li class="linked"><a href="#contacts-section">Контакты</a></li>
      </ul>
      <img src="images/x.svg" class="x-close" id="x-close">
    </nav>
  </header>

<?php
require 'db.php';

// Достаем любимые проекты для слайдера на 1-м экране
$hero_slider_sql = "SELECT image_path FROM projects WHERE is_slider = 1";
$hero_slider_result = $conn->query($hero_slider_sql);
$hero_slides =[];
if ($hero_slider_result) {
    while($row = $hero_slider_result->fetch_assoc()) {
        $hero_slides[] = $row;
    }
}
// Если таких проектов нет, ставим заглушку
if (empty($hero_slides)) {
    $hero_slides[] =['image_path' => 'renaissance_full.png'];
}
?>
<div class="container-all" id="container-all">
 <section class="hero-section" style="margin-top: 0vw">
    <div class="left-half">
      <p class="top-descriptor">создаем цифровой опыт, который <br>  <u>продает.</u></p>
      <div class="brand-block">
        <span class="agency-label">веб-агенство</span>
        <h1 class="agency-name">plufue</h1> 
      </div>
      <button class="btn-primary" id="btn-primary" onclick="bidsGo()">
        <span class="btn-text">обсудить проект</span>
      </button>
    </div>
    <script>
        function bidsGo() {
            window.location.href = 'bids.php';
        }
    </script>

    <div class="right-half">
      
      <!-- Картинки-слайды (выводятся из БД) -->
      <?php foreach($hero_slides as $index => $slide): ?>
        <div class="hero-slide-bg <?= $index === 0 ? 'active' : '' ?>" style="background-image: url('images/<?= htmlspecialchars($slide['image_path']) ?>');"></div>
      <?php endforeach; ?>

      <!-- Полупрозрачное затемнение -->
      <div class="hero-overlay"></div>
      
      <div class="services-box">
        
        <ul class="services-list">
          <li><a href="#main-slider">БРЕНДИНГ</a></li>
          <li><a href="#main-slider">САЙТЫ</a></li>
          <li><a href="#main-slider">КОММЕРЦИЯ</a></li>
          <li><a href="#main-slider">СЕРВИСЫ</a></li>
        </ul>
        <a href="#main-slider" style="align-self: flex-end;"><img src="images/arrow-horizontal.svg" class="arrow-icon"></a>
      </div>
    </div>
  </section>

<!-- =============================== -->
  <!-- ЭКРАН 2: ПРОЕКТ -->
  <!-- =============================== -->
<?php
// 1. Достаем проекты для слайдера
$slider_sql = "SELECT id, title, image_path, duration, category, tags, benefits FROM projects WHERE is_slider = 1";
$slider_result = $conn->query($slider_sql);

// Карта перевода категорий из БД (английский) в интерфейс (русский)
$cat_map =[
    'sites' => 'сайты',
    'branding' => 'брендинг',
    'ecommerce' => 'коммерция', 
    'services' => 'сервисы'
];

$slider_projects =[];
while($row = $slider_result->fetch_assoc()) {
    // Переводим категорию на русский прямо здесь
    $raw_cat = mb_strtolower(trim($row['category']), 'UTF-8');
    // Создаем новое поле category_ru
    $row['category_ru'] = array_key_exists($raw_cat, $cat_map) ? $cat_map[$raw_cat] : $row['category'];
    
    $slider_projects[] = $row;
}
// 2. Превращаем массив PHP в JSON, чтобы передать в JavaScript
$slider_json = json_encode($slider_projects);
?>

<!-- ЭКРАН 2: СЛАЙДЕР -->
<section class="project-section" id="main-slider">

    <a href="#" id="slider-bg-link" class="slider-bg-link"></a>
    
    <!-- Пагинация -->
    <div class="slider-pagination" id="slider-dots">
        <!-- JS сам добавит сюда квадратики -->
    </div>

<div class="project-info">
  
  <!-- Ссылка-обертка для заголовка и стрелочки -->
      <a href="#" id="slider-title-link" class="project-title-link">
          <h2 class="project-title" id="slider-title">Загрузка...</h2>
          <img src="images/arrow-horizontal.svg" alt="->" class="title-arrow">
      </a>
  
  <div class="project-meta">
    <ul class="tags-list" id="slider-tags">
      <!-- JS сам создаст <li>#дизайн</li> и т.д. -->
    </ul>
    
    <div class="project-time">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
      <div id="slider-duration"></div>
    </div>
  </div>

  <div class="divider-track">
      <div class="divider-progress" id="slider-progress"></div>
  </div>

  <ul class="project-stats" id="slider-benefits">
  </ul>

</div>

    <!-- Навигация справа -->
    <div class="slider-nav">
      <!-- Назад -->
      <div class="nav-btn" onclick="prevSlide()">
        <svg class="nav-arrow" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M5 12L12 5M5 12L12 19" stroke="white" stroke-width="2" stroke-linecap="square"/></svg>
      </div>
      <!-- Вперед -->
      <div class="nav-btn" onclick="nextSlide()">
        <svg class="nav-arrow" style="transform: translateX(-150%) rotate(180deg);" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M5 12L12 5M5 12L12 19" stroke="white" stroke-width="2" stroke-linecap="square"/></svg>
      </div>
    </div>
</section>

<script>
// 1. Берем наши проекты из базы данных
let slides = <?= $slider_json ?>;

if (slides.length > 0) {
    
    let currentIndex = 0; // Начинаем с нулевого (первого) слайда
    let timer;            // Здесь будет лежать таймер перелистывания

    // 2. Генерируем квадратики (пагинацию)
    let dotsContainer = document.getElementById('slider-dots');
    
    for (let i = 0; i < slides.length; i++) {
        let dot = document.createElement('div');
        dot.className = 'square-dot';
        
        // Говорим: при клике на квадратик — покажи нужный слайд
        dot.onclick = function() { 
            currentIndex = i; 
            showSlide(currentIndex); 
        };
        
        dotsContainer.appendChild(dot);
    }

    // ==========================================
    // ГЛАВНАЯ ФУНКЦИЯ: ПОКАЗАТЬ СЛАЙД
    // ==========================================
function showSlide(index) {
    let currentData = slides[index];

    // 1. Меняем картинку и заголовок
    document.getElementById('main-slider').style.backgroundImage = "url('images/" + currentData.image_path + "')";
    document.getElementById('slider-title').innerText = currentData.title;
    document.getElementById('slider-bg-link').href = "view_project.php?id=" + currentData.id;
    document.getElementById('slider-title-link').href = "view_project.php?id=" + currentData.id;

    // ==========================================
    // 2. ВЫВОДИМ БЕНЕФИТЫ
    // ==========================================
    document.getElementById('slider-benefits').innerHTML = "<li>" + currentData.benefits + "</li>";
    document.getElementById('slider-duration').innerHTML = "<li>" + currentData.duration + "</li>";

    // ==========================================
    // 3. ВЫВОДИМ ТЕГИ И ГЛАВНУЮ КАТЕГОРИЮ
    // ==========================================
    let tagsHTML = "";

    // Сначала берем главную категорию (уже переведенную на русский в PHP)
    let mainCat = currentData.category_ru ? currentData.category_ru : "проект";
    tagsHTML += "<li>#" + mainCat + "</li>";

    // Теперь парсим теги из столбца tags (если они есть)
    if (currentData.tags) {
        // Заменяем запятые и переносы строк на обычные пробелы
        let normalizedTags = currentData.tags.replace(/[, \n\r\t]+/g, " ");
        // Разбиваем строку по пробелам и откидываем пустые
        let tagsArray = normalizedTags.split(" ").filter(tag => tag.trim() !== "");
        
        tagsArray.forEach(tag => {
            let cleanTag = tag.replace(/#/g, '').trim(); // Убираем решетку, если она уже была
            
            // Если тег не пустой и не дублирует главную категорию
            if (cleanTag && cleanTag.toLowerCase() !== mainCat.toLowerCase()) {
                tagsHTML += "<li>#" + cleanTag + "</li>";
            }
        });
    }

// Вставляем все получившиеся <li> в список
    document.getElementById('slider-tags').innerHTML = tagsHTML;

    gsap.fromTo([
        "#slider-title-link", 
        "#slider-tags", 
        ".project-time", 
        "#slider-benefits"
    ], {
        y: 20,            // Начальная позиция (чуть ниже)
        opacity: 0        // Прозрачность 0
    }, {
        y: 0,             // Конечная позиция
        opacity: 1,       // Прозрачность 1
        duration: 0.6,    // Быстрая и сочная анимация
        stagger: 0.08,    // Каскадное появление друг за другом
        ease: "back.out(1.5)", // Эффект легкой пружинки в конце
        overwrite: "auto" // Очищает предыдущую анимацию, если пользователь быстро кликает
    });


    // ==========================================
    // 4. Пагинация (квадратики) и линия времени
    // ==========================================
    let dots = document.querySelectorAll('.square-dot');
    for (let i = 0; i < dots.length; i++) {
        dots[i].classList.remove('active');
    }
    dots[index].classList.add('active');

    let line = document.getElementById('slider-progress');
    line.classList.remove('progress-active');
    
    void line.offsetWidth;
    
    line.classList.add('progress-active');

    clearTimeout(timer);
    timer = setTimeout(nextSlide, 7000);
}

    // ==========================================
    // ФУНКЦИИ ДЛЯ СТРЕЛОЧЕК ВПЕРЕД И НАЗАД
    // ==========================================
    function nextSlide() {
        currentIndex = currentIndex + 1;
        // Если дошли до конца — возвращаемся в начало (к нулю)
        if (currentIndex >= slides.length) {
            currentIndex = 0;
        }
        showSlide(currentIndex);
    }

    function prevSlide() {
        currentIndex = currentIndex - 1;
        // Если ушли в минус — прыгаем на самый последний слайд
        if (currentIndex < 0) {
            currentIndex = slides.length - 1;
        }
        showSlide(currentIndex);
    }

    showSlide(0); 
}
</script>

<!-- =============================== -->
  <!-- ЭКРАН 3: ГАЛЕРЕЯ -->
  <!-- =============================== -->
  <section class="services-wrapper">
    <?php
    // 1. Берем проекты из БД
    $sql = "SELECT id, title, image_path, category, tags FROM projects WHERE is_featured = 1 ORDER BY id DESC LIMIT 30";
    $result = $conn->query($sql);

    // 2. Раскладываем их по (категориям)
    $portfolio =[
        'sites' => [],
        'branding' => [],
        'ecommerce' =>[],
        'services' =>[]
    ];
    if ($result) {
        while($row = $result->fetch_assoc()) {
            if(array_key_exists($row['category'], $portfolio)) {
                $portfolio[$row['category']][] = $row;
            }
        }
    }

    // 3. Названия для меню и тегов
    $categories =[
        'sites' => 'сайты',
        'branding' => 'брендинг',
        'ecommerce' => 'коммерция', 
        'services' => 'сервисы'
    ];

    ?>

    <div class="left-gallery">
        
        <!-- БЛОК СТАТИСТИКИ (4 группы, переключаются через opacity) -->
        <div class="stats-container">
                <!-- Плашка конкретной категории -->
                <div class="stats-overlay">
                    <div class="stat-badge">[ 20+ ] проектов</div>
                    <div class="stat-badge">[ 24/7 ] поддержка после релиза</div>
                    <div class="stat-badge">[ +60% ] средний рост конверсии</div>
                </div>
        </div>

        <!-- САМА ГАЛЕРЕЯ С КАРТИНКАМИ -->
        <?php 
        $is_first_gallery = true;
        foreach($portfolio as $cat_key => $projects_array): 
            
            // Если проектов нет, ставим заглушку
            if (empty($projects_array)) {
                $projects_array[] =['id' => 1, 'image_path' => 'renaissance_full.png', 'title' => 'Проект в разработке', 'tags' => ''];
            }

            // Бьем на 2 колонки
            $half = ceil(count($projects_array) / 2);
            $col_up = array_slice($projects_array, 0, $half);
            $col_down = array_slice($projects_array, $half);
            if (empty($col_down)) { $col_down = $col_up; }

            // Умножаем картинки, чтобы лента не обрывалась (простая магия)
            while (count($col_up) < 5) { $col_up = array_merge($col_up, $col_up); }
            while (count($col_down) < 5) { $col_down = array_merge($col_down, $col_down); }

            $activeClass = $is_first_gallery ? 'active' : '';
            $is_first_gallery = false;
            $cat_ru = $categories[$cat_key];
        ?>

        <!-- Группа картинок одной категории -->
        <div class="gallery-group <?= $activeClass ?>" id="gallery-<?= $cat_key ?>">
            
            <!-- КОЛОНКА ВВЕРХ -->
            <div class="column col-up">
                <!-- Оригинал -->
                <div class="column-inner">
                    <?php foreach($col_up as $p): ?>
                        <a href="view_project.php?id=<?= $p['id'] ?>" class="gallery-card">
                            <img src="images/<?= htmlspecialchars($p['image_path']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                            <div class="card-hover-overlay">
                                <div class="card-tags">
                                    <?php 
                                    if (!empty($p['tags'])) {
                                        $tagsArray = preg_split('/[, \n\r\t]+/', $p['tags'], -1, PREG_SPLIT_NO_EMPTY);
                                        foreach($tagsArray as $tag) {
                                            $cleanTag = trim(str_replace('#', '', $tag));
                                            if(mb_strtolower($cleanTag, 'UTF-8') !== mb_strtolower($cat_ru, 'UTF-8')) {
                                                echo "<span>#" . htmlspecialchars($cleanTag) . "</span>";
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="card-info-bottom">
                                    <h3 class="card-title-hover"><?= htmlspecialchars($p['title']) ?></h3>
                                    <span class="card-cat-hover">#<?= $cat_ru ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                <!-- Дубликат для эффекта бесконечности -->
                <div class="column-inner">
                    <?php foreach($col_up as $p): ?>
                        <a href="view_project.php?id=<?= $p['id'] ?>" class="gallery-card">
                            <img src="images/<?= htmlspecialchars($p['image_path']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                            <div class="card-hover-overlay">
                                <div class="card-tags">
                                    <?php 
                                    if (!empty($p['tags'])) {
                                        $tagsArray = preg_split('/[, \n\r\t]+/', $p['tags'], -1, PREG_SPLIT_NO_EMPTY);
                                        foreach($tagsArray as $tag) {
                                            $cleanTag = trim(str_replace('#', '', $tag));
                                            if(mb_strtolower($cleanTag, 'UTF-8') !== mb_strtolower($cat_ru, 'UTF-8')) {
                                                echo "<span>#" . htmlspecialchars($cleanTag) . "</span>";
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="card-info-bottom">
                                    <h3 class="card-title-hover"><?= htmlspecialchars($p['title']) ?></h3>
                                    <span class="card-cat-hover">#<?= $cat_ru ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- КОЛОНКА ВНИЗ -->
            <div class="column col-down">
                <!-- Оригинал -->
                <div class="column-inner">
                    <?php foreach($col_down as $p): ?>
                        <a href="view_project.php?id=<?= $p['id'] ?>" class="gallery-card">
                            <img src="images/<?= htmlspecialchars($p['image_path']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                            <div class="card-hover-overlay">
                                <div class="card-tags">
                                    <?php 
                                    if (!empty($p['tags'])) {
                                        $tagsArray = preg_split('/[, \n\r\t]+/', $p['tags'], -1, PREG_SPLIT_NO_EMPTY);
                                        foreach($tagsArray as $tag) {
                                            $cleanTag = trim(str_replace('#', '', $tag));
                                            if(mb_strtolower($cleanTag, 'UTF-8') !== mb_strtolower($cat_ru, 'UTF-8')) {
                                                echo "<span>#" . htmlspecialchars($cleanTag) . "</span>";
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="card-info-bottom">
                                    <h3 class="card-title-hover"><?= htmlspecialchars($p['title']) ?></h3>
                                    <span class="card-cat-hover">#<?= $cat_ru ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                <!-- Дубликат для эффекта бесконечности -->
                <div class="column-inner">
                    <?php foreach($col_down as $p): ?>
                        <a href="view_project.php?id=<?= $p['id'] ?>" class="gallery-card">
                            <img src="images/<?= htmlspecialchars($p['image_path']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                            <div class="card-hover-overlay">
                                <div class="card-tags">
                                    <?php 
                                    if (!empty($p['tags'])) {
                                        $tagsArray = preg_split('/[, \n\r\t]+/', $p['tags'], -1, PREG_SPLIT_NO_EMPTY);
                                        foreach($tagsArray as $tag) {
                                            $cleanTag = trim(str_replace('#', '', $tag));
                                            if(mb_strtolower($cleanTag, 'UTF-8') !== mb_strtolower($cat_ru, 'UTF-8')) {
                                                echo "<span>#" . htmlspecialchars($cleanTag) . "</span>";
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="card-info-bottom">
                                    <h3 class="card-title-hover"><?= htmlspecialchars($p['title']) ?></h3>
                                    <span class="card-cat-hover">#<?= $cat_ru ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

    <!-- МЕНЮ СПРАВА -->
    <div class="right-menu">
        <?php foreach($categories as $key => $name): ?>
            <!-- data-target хранит имя категории, чтобы JS знал, что открывать -->
            <h2 class="menu-item" data-target="<?= $key ?>">
                <!-- <div class="service-call"></div> -->
                <span class="menu-text"><?= $name ?></span>
            </h2>
        <?php endforeach; ?>
    </div>
  </section>

    <?php
  // Вытаскиваем 5 случайных картинок из базы для слайд-шоу
  $slideshow_sql = "SELECT image_path FROM projects ORDER BY RAND() LIMIT 5";
  $slideshow_result = $conn->query($slideshow_sql);
  $slides =[];
  while($row = $slideshow_result->fetch_assoc()) {
      $slides[] = "images/" . $row['image_path'];
  }

  // Заглушка, если проектов в базе пока нет
  if(empty($slides)) {
      $slides = ["images/default-bg.jpg"]; 
  }
  ?>

  <section class="all-projects-cta">

    <!-- Вся эта зона кликабельна и ведет на страницу всех проектов -->
    <a href="projects.php" class="cta-interactive-zone">

      <div class="large-image-circle">
        <?php foreach($slides as $index => $img): ?>
          <!-- Первая картинка сразу с классом active, остальные прозрачные -->
          <div class="slide-img <?= $index === 0 ? 'active' : '' ?>" style="background-image: url('<?= $img ?>');"></div>
        <?php endforeach; ?>
        <!-- Легкое затемнение, чтобы белая кнопка и текст читались лучше -->
        <div class="circle-overlay"></div>
      </div>

      <img src="images/circle-text.png" alt="Все проекты" class="rotating-text-img">

      <div class="inner-solid-circle">
        <img src="images/projects-icon.png" alt="" class="grid-icon">
      </div>

    </a>

  </section>
  
  <section class="contacts-section" id="contacts-section">
              <!-- ОСНОВНОЙ КОНТЕНТ КОНТАКТОВ -->
  <div class="contacts-content">
    <h1 class="contacts-title">
      думаете над своим<br>
      <span class="text-coral">проектом</span>
      <span>?</span>
    </h1>
    
    <h2 class="contacts-subtitle">- свяжитесь с нами!</h2>
    
    <a href="bids.php" class="btn-discuss">
      <span>обсудить проект</span>
      <img src="images/arrow-horizontal.svg" class="arrow-horizontal">
    </a>

    <div class="alt-services">
      <p>Или через другие сервисы:</p>
      <div class="social-links">
        <a href="https://vk.com" class="social-icon"><img src="images/vk.svg" alt="VK"></a>
        <a href="https://360.yandex.com/mail/" class="social-icon"><img src="images/yandex-mail.svg" alt="Email"></a>
        <a href="https://wechat.com/ru" class="social-icon"><img src="images/wechat.svg" alt="WeChat"></a>
      </div>
    </div>
  </div>
  </section>

<!-- =============================== -->
  <!-- ФУТЕР -->
  <!-- =============================== -->
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
</div>
</body>
<script src="script.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/smoothscroll/1.4.10/SmoothScroll.min.js" integrity="sha256-huW7yWl7tNfP7lGk46XE+Sp0nCotjzYodhVKlwaNeco=" crossorigin="anonymous"></script>
</html>