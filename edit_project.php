<?php
session_start();
require 'db.php';

if (!isset($_SESSION['is_admin'])) { header("Location: login.php"); exit; }

$id = intval($_GET['id']);
$project = $conn->query("SELECT * FROM projects WHERE id = $id")->fetch_assoc();

if (!$project) { header("Location: admin.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $tags = $_POST['tags'];
    $duration = $_POST['duration'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_slider = isset($_POST['is_slider']) ? 1 : 0;
    $benefits = $_POST['benefits'];
    $hook = $_POST['hook'];
    $about_project = $_POST['about_project'];
    $insight = $_POST['insight'];

    // Если картинка загружена - берем её, если нет - оставляем старую из БД
    function getUpdatedImage($input_name, $old_image) {
        if (!empty($_FILES[$input_name]['name'])) {
            $filename = time() . '_' . $_FILES[$input_name]['name'];
            move_uploaded_file($_FILES[$input_name]['tmp_name'], 'images/' . $filename);
            return $filename;
        }
        return $old_image;
    }

    $image_path = getUpdatedImage('image_path', $project['image_path']);
    $image1 = getUpdatedImage('image1', $project['image1']);
    $image2 = getUpdatedImage('image2', $project['image2']);
    $image3 = getUpdatedImage('image3', $project['image3']);

    $sql = "UPDATE projects SET title=?, category=?, tags=?, duration=?, is_featured=?, is_slider=?, image_path=?, benefits=?, hook=?, image1=?, image2=?, image3=?, about_project=?, insight=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiissssssssi", $title, $category, $tags, $duration, $is_featured, $is_slider, $image_path, $benefits, $hook, $image1, $image2, $image3, $about_project, $insight, $id);
    $stmt->execute();

    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать проект</title>
    <style>
        body { background-color: #050505; color: white; font-family: seen; padding: 40px; }
        @font-face { font-family: seen; src: url(fonts/seen.ttf); }
        .form-container { max-width: 800px; margin: 0 auto; background: #111; padding: 30px; border-radius: 10px; border: 1px solid #333;}
        .form-group { margin-bottom: 20px; display: flex; flex-direction: column;}
        label { margin-bottom: 5px; color: #aaa; }
        input[type="text"], textarea, select { padding: 10px; background: #222; border: 1px solid #444; color: white; border-radius: 5px;}
        textarea { height: 100px; resize: vertical; }
        .checkbox-group { flex-direction: row; align-items: center; gap: 10px; }
        .btn { padding: 15px; background: white; color: black; border: none; border-radius: 50px; cursor: pointer; font-weight: bold; margin-top: 20px;}
        .btn-cancel { background: transparent; color: white; border: 1px solid white; text-align: center; text-decoration: none; display: inline-block; box-sizing: border-box;}
        .current-img { font-size: 12px; color: #666; margin-top: 5px; }
        .btn:hover { background-color: gray; color: white; }
        button { transition: 0.2s }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 style="margin-top:0;">РЕДАКТИРОВАТЬ ПРОЕКТ</h2>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group"><label>Название</label><input type="text" name="title" value="<?= htmlspecialchars($project['title']) ?>" required></div>
            <div class="form-group"><label>Категория</label><input type="text" name="category" value="<?= htmlspecialchars($project['category']) ?>" required></div>
            <div class="form-group"><label>Теги</label><input type="text" name="tags" value="<?= htmlspecialchars($project['tags']) ?>"></div>
            <div class="form-group"><label>Длительность</label><input type="text" name="duration" value="<?= htmlspecialchars($project['duration']) ?>"></div>
            
            <div class="form-group checkbox-group"><input type="checkbox" name="is_featured" <?= $project['is_featured'] ? 'checked' : '' ?>><label>В Галерее</label></div>
            <div class="form-group checkbox-group"><input type="checkbox" name="is_slider" <?= $project['is_slider'] ? 'checked' : '' ?>><label>В Слайдере</label></div>

            <div class="form-group">
                <label>Главная обложка (оставьте пустым, чтобы не менять)</label>
                <input type="file" name="image_path" accept="image/*">
                <?php if($project['image_path']): ?><div class="current-img">Текущая: <?= $project['image_path'] ?></div><?php endif; ?>
            </div>

            <div class="form-group"><label>Бенефиты</label><textarea name="benefits"><?= htmlspecialchars($project['benefits']) ?></textarea></div>
            <div class="form-group"><label>Хук</label><textarea name="hook"><?= htmlspecialchars($project['hook']) ?></textarea></div>
            
            <div class="form-group">
                <label>Картинка 1 (широкая)</label><input type="file" name="image1" accept="image/*">
                <?php if($project['image1']): ?><div class="current-img">Текущая: <?= $project['image1'] ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Картинка 2 (половинка)</label><input type="file" name="image2" accept="image/*">
                <?php if($project['image2']): ?><div class="current-img">Текущая: <?= $project['image2'] ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Картинка 3 (половинка)</label><input type="file" name="image3" accept="image/*">
                <?php if($project['image3']): ?><div class="current-img">Текущая: <?= $project['image3'] ?></div><?php endif; ?>
            </div>

            <div class="form-group"><label>О проекте</label><textarea name="about_project"><?= htmlspecialchars($project['about_project']) ?></textarea></div>
            <div class="form-group"><label>Инсайт</label><textarea name="insight"><?= htmlspecialchars($project['insight']) ?></textarea></div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn" style="flex:1;">ОБНОВИТЬ ПРОЕКТ</button>
                <a href="admin.php" class="btn btn-cancel" style="flex:1;">ОТМЕНА</a>
            </div>
        </form>
    </div>
</body>
</html>