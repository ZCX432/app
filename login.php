<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error { color: red; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Авторизация</h2>
                <form action="auth.php" method="POST" id="loginForm" novalidate>
                    <div class="mb-3">
                        <label for="login" class="form-label">Логин</label>
                        <input type="text" class="form-control" id="login" name="login" value="<?php echo isset($_GET['login']) ? htmlspecialchars($_GET['login']) : ''; ?>">
                        <div class="error"><?php echo isset($_GET['error']) && strpos($_GET['error'], 'login') !== false ? 'Логин обязателен' : (isset($_GET['error']) && strpos($_GET['error'], 'invalid') !== false ? 'Неверный логин или пароль' : ''); ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <div class="error"><?php echo isset($_GET['error']) && strpos($_GET['error'], 'password') !== false ? 'Пароль обязателен' : (isset($_GET['error']) && strpos($_GET['error'], 'invalid') !== false ? 'Неверный логин или пароль' : ''); ?></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Войти</button>
                </form>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success mt-3">Авторизация успешна!</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const login = document.getElementById('login').value;
            const password = document.getElementById('password').value;
            let errors = [];

            if (!login) errors.push('Логин обязателен');
            if (!password) errors.push('Пароль обязателен');

            if (errors.length > 0) {
                e.preventDefault();
                alert('Пожалуйста, исправьте ошибки:\n' + errors.join('\n'));
            }
        });
    </script>
</body>
</html>