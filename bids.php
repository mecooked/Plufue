<?php
require 'db.php';

$js_script = ""; // Создаем переменную для хранения JS-кода

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service = htmlspecialchars(trim($_POST['selected_option'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));

    // Оставляем только цифры для жесткой проверки длины
    $phone_digits = preg_replace('/\D/', '', $phone);

    if ($service && $name && $phone) {
        
        // ПРОВЕРКА: Если цифр меньше 11
        if (strlen($phone_digits) < 11) {
            $js_script = "alert('Номер телефона должен состоять из 11 цифр!');";
        } else {
            // Если всё ок - отправляем в базу
            $sql = "INSERT INTO bids (service_type, description, phone, name, email) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            $stmt->bind_param("sssss", $service, $description, $phone, $name, $email);

            if ($stmt->execute()) {
                $js_script = "alert('Спасибо! Ваша заявка успешно отправлена.'); window.location.href = window.location.pathname;";
            } else {
                $js_script = "alert('Ошибка базы данных. Попробуйте позже.');";
            }
            $stmt->close();
        }
    } else {
        $js_script = "alert('Пожалуйста, заполните имя и телефон!');";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>заявки - plufue</title>
    <link rel="stylesheet" href="bids.css">
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
        <li class="linked"><a href="index.php">Главная</a></li>
        <li class="linked"><a href="projects.php">Проекты</a></li>
        <li class="linked"><a href="bids.php" style="color: rgba(0, 0, 0, 0.264)">Обсудить проект</a></li>
        <li class="linked"><a href="index.php #contacts-section">Контакты</a></li>
      </ul>
      <img src="images/x.svg" class="x-close" id="x-close">
    </nav>
  </header>

    <div class="title">
        <p style="margin-bottom: 1vw;">создание заявки</p>
        <img src="images/ears.png" class="ears">
    </div>
    <form method="post">
        <div class="in-form-box">
            <div class="in-form">
                <div class="serv-name-title">тип услуги:</div>
                <div class="dropdown" onclick="toggleDropdown()">
                    <div class="arrow-and-name">
                        <div class="selected" id="selectedText">брендинг</div>
                        <img src="images/arrow.png" class="arrow">
                    </div>
                    
                    <ul class="options" id="options">
                        <li onclick="selectOption('1', 'брендинг')">брендинг</li>
                        <li onclick="selectOption('2' ,'коммерция')">коммерция</li>
                        <li onclick="selectOption('3' ,'сервисы')">сервисы</li>
                        <li onclick="selectOption('4' ,'сайты')">сайты</li>
                    </ul>
                    <input type="hidden" name="selected_option" id="hiddenInput" value="брендинг">
                </div>
                <div class="desc-and-title">
                    <div class="title2">расскажите нам о своем проекте:</div>
                    <textarea name="description" class="desc"></textarea>
                    <div class="title2">ваш номер телефона?</div>
                    <input type="text" class="btn1" name="phone" id="phone" minlength="18" placeholder="+7 (___) ___-__-__">
                    <div class="title2">как вас зовут?</div>
                    <input type="text" class="btn1" name="name">
                    <div class="title2">ваша почта</div>
                    <input type="email" class="btn1" name="email" required>
                </div>

                <button class="btn2" type="submit">отправить заявку</button>
                
            </div>
        </div>

        
      
    </form>
    
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

    <?php if (!empty($js_script)): ?>
    <script>
        <?php echo $js_script; ?>
    </script>
    <?php endif; ?>
    
</body>
<script>
    document.getElementById('phone').addEventListener('input', function(e) {
    let x = e.target.value.replace(/\D/g, '').substring(0, 11);
    let formatted = '';

    if (x.length > 0) formatted = '+7';
    if (x.length >= 2) formatted += ' (' + x.substring(1, 4);
    if (x.length >= 5) formatted += ') ' + x.substring(4, 7);
    if (x.length >= 8) formatted += '-' + x.substring(7, 9);
    if (x.length >= 10) formatted += '-' + x.substring(9, 11);

    e.target.value = formatted;
});

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
<script src="bids.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/smoothscroll/1.4.10/SmoothScroll.min.js" integrity="sha256-huW7yWl7tNfP7lGk46XE+Sp0nCotjzYodhVKlwaNeco=" crossorigin="anonymous"></script>
</html>