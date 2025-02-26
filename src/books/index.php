<?php
require_once '../includes/header.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$stmt = $db->query("SELECT * FROM books");
$books = $stmt->fetchAll();
?>

<div class="main-content">
    <div class="container">
        <h2>Книги</h2>
        <div class="books-grid">
            <?php foreach ($books as $book): ?>
                <div class="book-card">
                    <img class="book-img" src="<?php echo $book['image_url']; ?>" alt="">
                    <h3><?php echo $book['title']; ?></h3>
                    <p>Автор <?php echo $book['author']; ?></p>
                    <p class="price"><?php echo number_format($book['price'], 2); ?>р</p>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="/cart/add.php" method="POST">
                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                            <button type="submit">Добавить в корзину</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>