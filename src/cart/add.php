<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
?>
    <script>
        window.location.href = '/auth/login.php';
    </script>
<?php

    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $database = new Database();
    $db = $database->getConnection();

    $book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?: 1;

    // Проверяем наличие книги
    $stmt = $db->prepare("SELECT stock FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();

    if ($book && $book['stock'] >= $quantity) {
        // Проверяем, есть ли уже книга в корзине
        $stmt = $db->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND book_id = ?");
        $stmt->execute([$_SESSION['user_id'], $book_id]);
        $cart_item = $stmt->fetch();

        if ($cart_item) {
            // Обновляем количество
            $new_quantity = $cart_item['quantity'] + $quantity;
            $stmt = $db->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->execute([$new_quantity, $cart_item['id']]);
        } else {
            // Добавляем новый товар
            $stmt = $db->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $book_id, $quantity]);
        }
    }
}

?>
<script>
    window.location.href = '/cart';
</script>
<?php

exit;
