<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';
$user_id = $_SESSION['user_id'];

// Fetch cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_cost = 0;

// Place Order
if (isset($_POST['place_order'])) {
    // For now, just clear the cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $success = "✅ Your order has been placed successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout</title>
<style>
body { font-family: Arial, sans-serif; background-color: #f8f9fa; margin:0; padding:0; color:#333; }
.container { width: 90%; max-width: 1200px; margin: 40px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
h2 { text-align:center; font-size:2em; margin-bottom:20px; }
.checkout-item { display:flex; justify-content:space-between; align-items:center; padding:15px; margin-bottom:15px; background:#fff; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
.checkout-item img { max-width:80px; border-radius:5px; margin-right:15px; }
.item-details { flex-grow:1; }
.item-name { font-weight:bold; font-size:1.1em; }
.item-price { color:#495057; margin-top:5px; }
.total { text-align:right; font-size:1.4em; font-weight:bold; margin-top:20px; }
button { background-color:#28a745; color:white; border:none; padding:12px 20px; border-radius:5px; cursor:pointer; font-size:1em; margin-top:15px; }
button:hover { background-color:#218838; }
.success { background-color:#d4edda; color:#155724; padding:10px; margin-bottom:15px; border-radius:5px; }
.back-link { display:inline-block; margin-top:20px; text-decoration:none; color:#007bff; }
.back-link:hover { text-decoration:underline; }
</style>
</head>
<body>
<div class="container">
<h2>Checkout</h2>

<?php if (isset($success)) echo "<div class='success'>$success</div>"; ?>

<?php
if (empty($cart_items)) {
    echo "<p>Your cart is empty.</p>";
} else {
    $product_ids = array_column($cart_items, 'product_id');
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $quantity = 0;
        foreach ($cart_items as $cart_item) {
            if ($cart_item['product_id'] == $product['id']) {
                $quantity = $cart_item['quantity'];
                break;
            }
        }
        $total_cost += $product['price'] * $quantity;
        echo "<div class='checkout-item'>
                <img src='../images/{$product['image']}' alt='{$product['name']}'>
                <div class='item-details'>
                    <div class='item-name'>{$product['name']}</div>
                    <div class='item-price'>₹" . number_format($product['price'] * $quantity, 2) . " x $quantity</div>
                </div>
              </div>";
    }
}
?>

<?php if (!empty($cart_items)) : ?>
<div class="total">Total: ₹<?= number_format($total_cost, 2); ?></div>
<form method="POST">
    <button type="submit" name="place_order">Place Order</button>
</form>
<a href="cart.php" class="back-link">Back to Cart</a>
<?php endif; ?>

</div>
</body>
</html>
