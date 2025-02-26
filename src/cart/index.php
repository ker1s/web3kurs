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
    SELECT b.*, c.quantity 
    FROM cart c 
    JOIN books b ON c.book_id = b.id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<div class="main-content">
    <div class="container">
        <h2>Корзина</h2>
        <?php if (empty($cart_items)): ?>
            <p>Пусто</p>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['title']; ?>">
                        <div class="item-details">
                            <h3><?php echo $item['title']; ?></h3>
                            <p>Количество: <?php echo $item['quantity']; ?></p>
                            <p>Цена: <?php echo number_format($item['price'], 2); ?>р</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="cart-total">
                <h3>Всего: <?php echo number_format($total, 2); ?>р</h3>
                <form action="/cart/checkout.php" method="POST">
                    <button type="submit">Заказать</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>