<?php
require 'db.php';
$username = 'admin';
$password = password_hash('12345', PASSWORD_DEFAULT);
$role = 'admin';

$sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $password, $role);
if ($stmt->execute()) {
    echo "Администратор успешно создан! Логин: admin, Пароль: 12345.";
} else {
    echo "Ошибка: " . $conn->error;
}
?>