<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Киниги тут</title>
    <link href="/css/style.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="brand">Книги тут</a>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/cart">Корзина</a>
                    <a href="/orders">Заказы</a>
                    <a href="/auth/logout.php">Выход</a>
                <?php else: ?>
                    <a href="/auth/login.php">Вход</a>
                    <a href="/auth/register.php">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>