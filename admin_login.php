<?php
session_start();
require 'db.php';

$error = '';

// Если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Ищем пользователя в базе
    $sql = "SELECT * FROM users WHERE username = ? AND role = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Проверяем пароль (сравниваем с зашифрованным в базе)
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['is_admin'] = true;
        $_SESSION['username'] = $user['username'];
        header("Location: admin.php");
        exit;
    } else {
        $error = "Неверный логин или пароль, либо нет прав администратора.";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в панель</title>
    <style>
        body { background-color: #050505; color: white; font-family: 'Montserrat', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { border: 1px solid rgba(255,255,255,0.2); padding: 40px; border-radius: 10px; width: 100%; max-width: 400px; text-align: center; box-sizing: border-box;}
        input { width: 100%; padding: 15px; margin-bottom: 20px; background: transparent; border: 1px solid rgba(255,255,255,0.5); color: white; border-radius: 50px; box-sizing: border-box; outline: none; }
        button { width: 100%; padding: 15px; background: white; color: black; border: none; border-radius: 50px; cursor: pointer; font-weight: bold; text-transform: uppercase;}
        .error { color: #ff9d9d; margin-bottom: 20px; font-size: 14px; }
    </style>
</head>
<body>
    <form class="login-box" method="POST">
        <h2 style="margin-top:0;">ВХОД В АДМИН-ПАНЕЛЬ</h2>
        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <input type="text" name="username" placeholder="логин" required>
        <input type="password" name="password" placeholder="пароль" required>
        <button type="submit">войти</button>
    </form>
</body>
</html>