<?php
session_start();
require 'db.php';

// 1. ЗАЩИТА: Если не админ — выкидываем на страницу логина
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// 2. ОБРАБОТКА УДАЛЕНИЯ ЗАЯВКИ
if (isset($_POST['delete_bid_id'])) {
    $id = intval($_POST['delete_bid_id']);
    $conn->query("DELETE FROM bids WHERE id = $id");
    header("Location: admin.php"); exit;
}

// 3. ОБРАБОТКА УДАЛЕНИЯ ПРОЕКТА
if (isset($_POST['delete_project_id'])) {
    $id = intval($_POST['delete_project_id']);
    $conn->query("DELETE FROM projects WHERE id = $id");
    header("Location: admin.php"); exit;
}

// 4. ДОСТАЕМ ДАННЫЕ ИЗ БАЗЫ
$bids = $conn->query("SELECT * FROM bids ORDER BY id DESC");
$projects = $conn->query("SELECT * FROM projects ORDER BY id DESC");

// Словарик для услуг из заявок
$services = ['1' => 'брендинг', '2' => 'коммерция', '3' => 'сервисы', '4' => 'сайты'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

    <header class="header">
        <h1 class="title">АДМИН-ПАНЕЛЬ</h1>
        <div class="controls">
            <a href="add_project.php" class="btn-outline">+ добавить проект</a>
            <a href="logout.php" class="btn-red">ВЫЙТИ</a>
        </div>
    </header>

    <!-- ПЕРЕКЛЮЧАТЕЛИ ВКЛАДОК -->
    <div class="tabs">
        <button class="tab-btn active" onclick="openTab('bids', this)">Заявки</button>
        <button class="tab-btn" onclick="openTab('projects', this)">Проекты</button>
    </div>

    <!-- ВКЛАДКА: ЗАЯВКИ -->
    <div id="bids" class="tab-content active">
        <div class="grid">
            <?php while($bid = $bids->fetch_assoc()): ?>
            <div class="card">
                <div>
                    <p>Имя: <strong><?= htmlspecialchars($bid['name']) ?></strong></p>
                    <p>Телефон: <strong><?= htmlspecialchars($bid['phone']) ?></strong></p>
                    <p>Услуга: <strong><?= $services[$bid['service_type']] ?? $bid['service_type'] ?></strong></p>
                    <p>Описание:<br><strong><?= nl2br(htmlspecialchars($bid['description'])) ?></strong></p>
                    <p style="margin-top:15px; font-size: 12px;">Создано: <?= $bid['created_at'] ?></p>
                </div>
                
                <form class="card-actions" method="POST" onsubmit="return confirm('Точно удалить заявку?');">
                    <input type="hidden" name="delete_bid_id" value="<?= $bid['id'] ?>">
                    <button type="submit" class="action-btn btn-delete">удалить</button>
                </form>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- ВКЛАДКА: ПРОЕКТЫ -->
    <div id="projects" class="tab-content">
        <div class="grid">
            <?php while($project = $projects->fetch_assoc()): ?>
            <div class="card">
                <div>
                    <div class="card-title"><?= htmlspecialchars($project['title']) ?></div>
                    <p>Категория: <strong><?= htmlspecialchars($project['category']) ?></strong></p>
                    <p>Теги: <strong><?= htmlspecialchars($project['tags']) ?></strong></p>
                </div>
                
                <div class="card-actions">
                    <a href="edit_project.php?id=<?= $project['id'] ?>" class="action-btn btn-edit">редактировать ✎</a>
                    <form method="POST" onsubmit="return confirm('Точно удалить проект?');" style="margin:0;">
                        <input type="hidden" name="delete_project_id" value="<?= $project['id'] ?>">
                        <button type="submit" class="action-btn btn-delete">удалить</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        // скрипт для переключения вкладок
        function openTab(tabId, btn) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            
            document.getElementById(tabId).classList.add('active');
            btn.classList.add('active');
        }
    </script>
</body>
</html>