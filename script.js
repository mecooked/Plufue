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

// ==========================================
  // АНИМАЦИЯ ФОНА НА ГЛАВНОМ ЭКРАНЕ (HERO)
  // ==========================================
  document.addEventListener('DOMContentLoaded', () => {
    const heroBgSlides = document.querySelectorAll('.hero-slide-bg');
    
    // Запускаем переключение, только если картинок больше одной
    if (heroBgSlides.length > 1) {
      let currentHeroIndex = 0;
      
      setInterval(() => {
        // Прячем текущий фон
        heroBgSlides[currentHeroIndex].classList.remove('active');
        
        // Берем следующий (возвращаемся к нулю, если дошли до конца)
        currentHeroIndex = (currentHeroIndex + 1) % heroBgSlides.length;
        
        // Показываем новый фон
        heroBgSlides[currentHeroIndex].classList.add('active');
      }, 4000); // 4000 = 4 секунды (можешь поменять скорость)
    }
  });





// Находим все пункты меню справа
const menuItems = document.querySelectorAll('.menu-item');

// Находим все группы картинок и все группы статистики
const galleries = document.querySelectorAll('.gallery-group');
const stats = document.querySelectorAll('.stats-overlay');

menuItems.forEach(item => {
  item.addEventListener('mouseenter', () => {
    // 1. Узнаем, на какую категорию навели (например 'design')
    const targetKey = item.getAttribute('data-target');
    
    // 2. Прячем ВСЕ галереи и ВСЮ статистику (убираем класс active)
    galleries.forEach(g => g.classList.remove('active'));
    stats.forEach(s => s.classList.remove('active'));

    // 3. Показываем только нужную галерею и нужную статистику
    document.getElementById('gallery-' + targetKey).classList.add('active');
    document.getElementById('stats-' + targetKey).classList.add('active');
  });
});



window.addEventListener('load', () => {
    // 1. Создаем анимацию бесконечного движения вверх для левой колонки
    // yPercent: -50 сдвигает блок на 50% вверх (там где дубликат)
    gsap.set(".col-up", { yPercent: -5 });
    let tlUp = gsap.to(".col-up", {
      yPercent: -30,
      duration: 15,     // Время одного круга (скорость)
      ease: "none",     // Линейное движение без ускорений
      repeat: -1        // Бесконечный повтор
    });

    // 2. Делаем то же самое для правой колонки (движение вниз)
    // Сначала ставим её на -50%, а потом анимируем к 0%
    gsap.set(".col-down", { yPercent: -30 });
    let tlDown = gsap.to(".col-down", {
      yPercent: -5,
      duration: 15,
      ease: "none",
      repeat: -1
    });

    // 3. Плавное замедление при наведении на всю галерею
    const gallery = document.querySelector('.left-gallery');

    gallery.addEventListener('mouseenter', () => {
      // Плавно замедляем скорость до 20% за полсекунды
      gsap.to([tlUp, tlDown], { timeScale: 0.2, duration: 0.5 });
    });

    gallery.addEventListener('mouseleave', () => {
      // Возвращаем нормальную скорость (1) когда мышка уходит
      gsap.to([tlUp, tlDown], { timeScale: 1, duration: 0.5 });
    });

});

  document.addEventListener('DOMContentLoaded', () => {
    // Находим все картинки внутри слайд-шоу
    const slides = document.querySelectorAll('.slide-img');
    
    // Если картинок 1 или меньше, слайд-шоу не нужно
    if (slides.length <= 1) return;
    
    let currentSlideIndex = 0;

    // Запускаем бесконечный таймер (3000 мс = 3 секунды)
    setInterval(() => {
      // 1. Прячем текущую картинку
      slides[currentSlideIndex].classList.remove('active');
      
      // 2. Вычисляем следующую (если дошли до конца - возвращаемся к 0)
      currentSlideIndex = (currentSlideIndex + 1) % slides.length;
      
      // 3. Показываем новую картинку
      slides[currentSlideIndex].classList.add('active');
    }, 3000); 
  });
  
// 2. МИКРО-АНИМАЦИИ (ПОЯВЛЕНИЕ ПРИ ЗАГРУЗКЕ И СКРОЛЛЕ)
window.addEventListener('load', () => {
    
    // Обязательно регистрируем плагин скролла
    if (typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
    }

    // Всплытие текста и кнопки слева (Главный экран)
    gsap.fromTo(".top-descriptor, .agency-label, .btn-primary, .agency-name", 
        { y: 40, opacity: 0 }, 
        { y: 0, opacity: 1, duration: 1.2, stagger: 0.2, ease: "power3.out" }
    );

    // Всплытие всей коробки с категориями на главном экране
    gsap.fromTo(".services-box, .right-half", 
        { y: 50, opacity: 0 }, 
        { y: 0, opacity: 1, duration: 0.8, ease: "power2.out", delay: 0.4 }
    );

    // Анимация контактов при скролле (ScrollTrigger)
    if (typeof ScrollTrigger !== 'undefined') {
        gsap.fromTo(".contacts-title, .contacts-subtitle, .btn-discuss, .alt-services", 
            { y: 50, opacity: 0 }, 
            { 
                scrollTrigger: {
                    trigger: ".contacts-section",
                    start: "top 80%", // Запустится, когда блок появится на 80%
                },
                y: 0,
                opacity: 1,
                duration: 1,
                stagger: 0.15,
                ease: "back.out(1.2)"
            }
        );
    }
});
  

  