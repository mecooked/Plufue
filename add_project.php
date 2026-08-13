<?php
session_start();
require 'db.php';

if (!isset($_SESSION['is_admin'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Собираем тексты
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

    // Функция для загрузки картинок
    function uploadImage($input_name) {
        if (!empty($_FILES[$input_name]['name'])) {
            $filename = time() . '_' . $_FILES[$input_name]['name']; // Уникальное имя
            move_uploaded_file($_FILES[$input_name]['tmp_name'], 'images/' . $filename);
            return $filename;
        }
        return null;
    }

    $image_path = uploadImage('image_path');
    $image1 = uploadImage('image1');
    $image2 = uploadImage('image2');
    $image3 = uploadImage('image3');

    // запрос на вставку
    $sql = "INSERT INTO projects (title, category, tags, duration, is_featured, is_slider, image_path, benefits, hook, image1, image2, image3, about_project, insight) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiissssssss", $title, $category, $tags, $duration, $is_featured, $is_slider, $image_path, $benefits, $hook, $image1, $image2, $image3, $about_project, $insight);
    $stmt->execute();

    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить проект</title>
    <style>
        body { background-color: #050505; color: white; font-family: seen; padding: 40px; }
        @font-face { font-family: seen; src: url(fonts/seen.ttf); }
        .form-container { max-width: 800px; margin: 0 auto; background: #111; padding: 30px; border-radius: 10px; border: 1px solid #333;}
        .form-group { margin-bottom: 20px; display: flex; flex-direction: column;}
        label { margin-bottom: 5px; color: #aaa; }
        input[type="text"], textarea, select { padding: 10px; background: #222; border: 1px solid #444; color: white; border-radius: 5px; width: 100%; box-sizing: border-box;}
        textarea { height: 100px; resize: vertical; }
        .checkbox-group { flex-direction: row; align-items: center; gap: 10px; }
        .btn { padding: 15px; background: white; color: black; border: none; border-radius: 50px; cursor: pointer; font-weight: bold; margin-top: 20px;}
        .btn-cancel { background: transparent; color: white; border: 1px solid white; text-align: center; text-decoration: none; display: inline-block; box-sizing: border-box;}
        .btn:hover { background-color: gray; color: white; }
        button { transition: 0.2s }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 style="margin-top:0;">ДОБАВИТЬ ПРОЕКТ</h2>
        
        <form method="POST" enctype="multipart/form-data">
            <!-- Основное -->
            <div class="form-group"><label>Название проекта *</label><input type="text" name="title" required></div>
            <div class="form-group">
                <label>Категория (на англ, например: sites, branding, ecommerce) *</label>
                <input type="text" name="category" required>
            </div>
            <div class="form-group"><label>Теги (через пробел или запятую с решеткой)</label><input type="text" name="tags"></div>
            <div class="form-group"><label>Длительность (например: 1 месяц)</label><input type="text" name="duration"></div>
            
            <div class="form-group checkbox-group"><input type="checkbox" name="is_featured" checked><label>Показывать в Галерее (Экран 3)</label></div>
            <div class="form-group checkbox-group"><input type="checkbox" name="is_slider"><label>Показывать в Слайдере (Экран 2)</label></div>

            <!-- Главная картинка -->
            <div class="form-group"><label>Главная обложка (обязательно) *</label><input type="file" name="image_path" required accept="image/*"></div>

            <!-- Тексты -->
            <div class="form-group"><label>Бенефиты (для слайдера)</label><textarea name="benefits"></textarea></div>
            <div class="form-group"><label>Хук (Крупный текст для детальной страницы)</label><textarea name="hook"></textarea></div>
            
            <!-- Картинки для детальной страницы -->
            <div class="form-group"><label>Картинка 1 (широкая)</label><input type="file" name="image1" accept="image/*"></div>
            <div class="form-group"><label>Картинка 2 (половинка)</label><input type="file" name="image2" accept="image/*"></div>
            <div class="form-group"><label>Картинка 3 (половинка)</label><input type="file" name="image3" accept="image/*"></div>

            <!-- Подробное описание -->
            <div class="form-group"><label>О проекте</label><textarea name="about_project"></textarea></div>
            <div class="form-group"><label>Инсайт</label><textarea name="insight"></textarea></div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn" style="flex:1;">СОХРАНИТЬ ПРОЕКТ</button>
                <a href="admin.php" class="btn btn-cancel" style="flex:1;">ОТМЕНА</a>
            </div>
        </form>
    </div>
</body>
</html>