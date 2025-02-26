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

$database = new Database();
$db = $database->getConnection();

try {
    $db->beginTransaction();

    // Получаем товары из корзины
    $stmt = $db->prepare("
        SELECT c.*, b.price, b.stock 
        FROM cart c 
        JOIN books b ON c.book_id = b.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();

    if (empty($cart_items)) {
        throw new Exception("Cart is empty");
    }

    // Подсчитываем общую сумму
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];

        // Проверяем наличие
        if ($item['quantity'] > $item['stock']) {
            throw new Exception("Not enough stock for some items");
        }
    }

    // Создаем заказ
    $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $total_amount]);
    $order_id = $db->lastInsertId();

    // Добавляем товары в заказ
    $stmt = $db->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $stmt->execute([$order_id, $item['book_id'], $item['quantity'], $item['price']]);

        // Обновляем количество книг
        $new_stock = $item['stock'] - $item['quantity'];
        $stmt2 = $db->prepare("UPDATE books SET stock = ? WHERE id = ?");
        $stmt2->execute([$new_stock, $item['book_id']]);
    }

    // Очищаем корзину
    $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    $db->commit();

    $_SESSION['success_message'] = "Order placed successfully!";
?>
    <script>
        window.location.href = '/orders';
    </script>
<?php

    exit;
} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error_message'] = $e->getMessage();
?>
    <script>
        window.location.href = '/cart';
    </script>
<?php

    exit;
}
