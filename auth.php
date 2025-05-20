<?php
session_start();

// Настройки подключения к базе данных
$servername = "localhost";
$username = "root"; // По умолчанию в OpenServer
$password = ""; // Пароль по умолчанию в OpenServer
$dbname = "registration_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

$error = '';
$login = isset($_POST['login']) ? trim($_POST['login']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Серверная валидация
    if (empty($login)) {
        $error = 'login_empty';
    } elseif (empty($password)) {
        $error = 'password_empty';
    } else {
        // Проверка логина и пароля
        $stmt = $conn->prepare("SELECT password FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $login;
            header("Location: login.php?success=1");
            exit;
        } else {
            $error = 'invalid_credentials';
        }
    }

    // Перенаправление с ошибкой
    $query = http_build_query(['login' => $login, 'error' => $error]);
    header("Location: login.php?$query");
    exit;
}
?>