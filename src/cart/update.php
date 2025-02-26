<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
?>
    <script>
        window.location.href = '/cart';
    </script>
<?php
    exit;
}

$database = new Database();
$db = $database->getConnection();

$cart_id = filter_input(INPUT_POST, 'cart_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if ($cart_id && $quantity > 0) {
    $stmt = $db->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$quantity, $cart_id, $_SESSION['user_id']]);
} elseif ($cart_id && $quantity === 0) {
    $stmt = $db->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $_SESSION['user_id']]);
}

?>
<script>
    window.location.href = '/cart';
</script>
<?php
exit;
