<?php
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

$errors = [];
$formData = [
    'login' => isset($_POST['login']) ? trim($_POST['login']) : '',
    'password' => isset($_POST['password']) ? trim($_POST['password']) : '',
    'full_name' => isset($_POST['full_name']) ? trim($_POST['full_name']) : '',
    'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
    'email' => isset($_POST['email']) ? trim($_POST['email']) : ''
];

// Серверная валидация
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($formData['login'])) {
        $errors['login'] = 'Логин обязателен';
    } else if (!preg_match('/^[a-zA-Z0-9]{3,}$/', $formData['login'])) {
        $errors['login'] = 'Логин должен содержать минимум 3 символа (буквы и цифры)';
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE login = ?");
        $stmt->execute([$formData['login']]);
        if ($stmt->fetchColumn() > 0) {
            $errors['login'] = 'Логин уже занят';
        }
    }

    if (empty($formData['password'])) {
        $errors['password'] = 'Пароль обязателен';
    } else if (strlen($formData['password']) < 6) {
        $errors['password'] = 'Пароль должен содержать минимум 6 символов';
    }

    if (empty($formData['full_name'])) {
        $errors['full_name'] = 'ФИО обязательно';
    } else if (!preg_match('/^[А-Яа-я\s]+$/', $formData['full_name'])) {
        $errors['full_name'] = 'ФИО должно содержать только кириллицу и пробелы';
    }

    if (empty($formData['phone'])) {
        $errors['phone'] = 'Телефон обязателен';
    } else if (!preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $formData['phone'])) {
        $errors['phone'] = 'Формат телефона: +7(XXX)-XXX-XX-XX';
    }

    if (empty($formData['email'])) {
        $errors['email'] = 'Email обязателен';
    } else if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный формат email';
    }

    if (empty($errors)) {
        // Хэширование пароля
        $hashedPassword = password_hash($formData['password'], PASSWORD_DEFAULT);
        try {
            $stmt = $conn->prepare("INSERT INTO users (login, password, full_name, phone, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$formData['login'], $hashedPassword, $formData['full_name'], $formData['phone'], $formData['email']]);
            header("Location: index.php?success=1");
            exit;
        } catch (PDOException $e) {
            $errors['db'] = "Ошибка базы данных: " . $e->getMessage();
        }
    }

    // Перенаправление с ошибками
    $query = http_build_query(array_merge($formData, ['errors' => json_encode($errors)]));
    header("Location: index.php?$query");
    exit;
}
?>