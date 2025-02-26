<?php
require_once '../includes/header.php';
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

$stmt = $db->prepare("
    SELECT o.*, oi.quantity, b.title, b.price
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN books b ON oi.book_id = b.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<div class="main-content">
    <div class="container">
        <h2>Заказы</h2>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success"><?php echo $_SESSION['success_message'];
                                    unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <p>Пусто</p>
        <?php else: ?>
            <?php
            $current_order_id = null;
            $order_total = 0;
            ?>

            <?php foreach ($orders as $item): ?>
                <?php if ($current_order_id !== $item['id']): ?>
                    <?php if ($current_order_id !== null): ?>
                        <div class="order-total">
                            Всего: $<?php echo number_format($order_total, 2); ?>
                        </div>
    </div>
<?php endif; ?>
</div>

<div class="order">
    <h3>Заказ #<?php echo $item['id']; ?></h3>
    <p>Дата создания: <?php echo date('Y-m-d H:i', strtotime($item['created_at'])); ?></p>
    <p>Статус: <?php echo ucfirst($item['status']); ?></p>
<?php
                    $current_order_id = $item['id'];
                    $order_total = 0;
                endif; ?>

<div class="order-item">
    <span><?php echo $item['title']; ?></span>
    <span>Количество: <?php echo $item['quantity']; ?></span>
    <span>Цена: <?php echo number_format($item['price'], 2); ?>р</span>
</div>

<?php $order_total += $item['price'] * $item['quantity']; ?>
<?php endforeach; ?>

<div class="order-total">
    Всего: <?php echo number_format($order_total, 2); ?>р
</div>
<?php endif; ?>
</div>
</div>


<?php require_once '../includes/footer.php'; ?>