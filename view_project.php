<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 
    
    $sql = "SELECT * FROM projects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();
}

if (!$project) {
    header("Location: projects.php");
    exit;
}

$cat_map =[
    'design' => 'дизайн',
    'branding' => 'брендинг',
    'ui-ux' => 'ui-ux',
    'uiux' => 'ui-ux',
    'site' => 'сайт',
    'sites' => 'сайт',
    'ecommerce' => 'e-commerce',
    'creative' => 'креатив'
];

$raw_cat = mb_strtolower(trim($project['category'] ?? ''), 'UTF-8');
$cat_ru = array_key_exists($raw_cat, $cat_map) ? $cat_map[$raw_cat] : $project['category'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($project['title']) ?> - Plufue</title>
    <link rel="stylesheet" href="view_project.css">
</head>
<body>

<div class="project-container">
    
    <div class="breadcrumb">
        <a href="projects.php">↖ проекты</a>
    </div>

    <div class="divider"></div>

    <div class="title-row">
        <h1 class="project-title"><?= htmlspecialchars($project['title']) ?></h1>
        <div class="project-subtitle">подробности проекта</div>
    </div>

    <img src="images/<?= htmlspecialchars($project['image_path']) ?>" alt="<?= htmlspecialchars($project['title']) ?>" class="main-image">

    <div class="tags-row">
        <span class="tag-item">#<?= htmlspecialchars($cat_ru) ?></span>
        <?php 
        if (!empty($project['tags'])) {
            $tagsArray = preg_split('/[, \n\r\t]+/', $project['tags'], -1, PREG_SPLIT_NO_EMPTY);
            foreach($tagsArray as $tag) {
                $cleanTag = trim(str_replace('#', '', $tag));
                if(mb_strtolower($cleanTag) !== mb_strtolower($cat_ru)) {
                    echo "<span class='tag-item'>#" . htmlspecialchars($cleanTag) . "</span>";
                }
            }
        }
        ?>
    </div>

    <div class="divider"></div>

    <p class="hook-text"><?= nl2br(htmlspecialchars($project['hook'] ?? '')) ?></p>

    <div class="images-grid">
        <?php if(!empty($project['image1'])): ?>
            <img src="images/<?= htmlspecialchars($project['image1']) ?>" class="grid-img grid-full" alt="">
        <?php endif; ?>

        <div class="grid-halves">
            <?php if(!empty($project['image2'])): ?>
                <img src="images/<?= htmlspecialchars($project['image2']) ?>" class="grid-img grid-half" alt="">
            <?php endif; ?>

            <?php if(!empty($project['image3'])): ?>
                <img src="images/<?= htmlspecialchars($project['image3']) ?>" class="grid-img grid-half" alt="">
            <?php endif; ?>
        </div>
    </div>

    <div class="divider"></div>

    <div class="info-section">
        <div class="info-col">
            <h2>О ПРОЕКТЕ</h2>
            <p><?= nl2br(htmlspecialchars($project['about_project'] ?? '')) ?></p>
        </div>
        <div class="info-col">
            <h2>ИНСАЙТ</h2>
            <p><?= nl2br(htmlspecialchars($project['insight'] ?? '')) ?></p>
        </div>
    </div>

    <div class="divider"></div>

</div>

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

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/smoothscroll/1.4.10/SmoothScroll.min.js" integrity="sha256-huW7yWl7tNfP7lGk46XE+Sp0nCotjzYodhVKlwaNeco=" crossorigin="anonymous"></script>
</html>