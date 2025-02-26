<?php
require_once '../includes/header.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    // Валидация
    if (strlen($username) < 3) {
        $errors[] = "Логин должен иметь длину больше 3 символов";
    }

    if (strlen($password) < 6) {
        $errors[] = "Пароль должен иметь длину больше 6 символов";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Пароли не сходятся";
    }

    // Проверка существующего пользователя
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $errors[] = "Этот пользователь уже существует";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        if ($stmt->execute([$username, $hashed_password])) {
            $_SESSION['user_id'] = $db->lastInsertId();
            require_once '../includes/header.php';
?>
            <script>
                window.location.href = '/';
            </script>
<?php
            exit;
        } else {
            $errors[] = "Ошибка";
        }
    }
}
?>

<div class="main-content">
    <div class="container">
        <h2>Регистрация</h2>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form">
            <div class="form-group">
                <label>Логин</label>
                <input type="text" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>

            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Подтвердите пароль</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit">Зарегистрироваться</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>