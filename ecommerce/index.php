<?php
session_start();
include 'includes/db.php';
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Store</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <div class="header-container">
        <h1>Welcome to Our Store</h1>
        <nav>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="pages/cart.php" class="cart-link">Cart</a>
                <form method="POST" action="pages/logout.php" style="display:inline;">
                    <button type="submit" class="logout-button">Logout</button>
                </form>
            <?php else: ?>
                <a href="pages/login.php">Login</a>
                <a href="pages/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<div class="main-container">
<main>
<h2>Products</h2>
<div class="product-list">
<?php if (empty($products)) : ?>
    <p>No products available.</p>
<?php else : ?>
    <?php foreach ($products as $product) : ?>
        <div class="product">
            <h3><?= htmlspecialchars($product['name']); ?></h3>
            <p>Price: ₹<?= number_format($product['price'],2); ?></p>
            <p><?= htmlspecialchars($product['description']); ?></p>
            <?php if (!empty($product['image'])) : ?>
                <img src="images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
            <?php endif; ?>

            <?php if(isset($_SESSION['user_id'])): ?>
                <form method="POST" action="pages/cart.php">
                    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                    <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
                </form>
            <?php else: ?>
                <a href="pages/login.php" class="add-to-cart-button">Login to Add</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
</main>
</div>

<footer>
<p>&copy; <?= date('Y'); ?> Online Store. All rights reserved.</p>
</footer>
</body>
</html>
