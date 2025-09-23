<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';
$user_id = $_SESSION['user_id'];

// ====================== HANDLE CART ACTIONS ====================== //

// Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = 1;

    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
    header("Location: cart.php");
    exit();
}

// Increase Quantity
if (isset($_POST['increase_quantity'])) {
    $product_id = intval($_POST['product_id']);
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    header("Location: cart.php");
    exit();
}

// Decrease Quantity
if (isset($_POST['decrease_quantity'])) {
    $product_id = intval($_POST['product_id']);
    $stmt = $conn->prepare("UPDATE cart SET quantity = GREATEST(quantity - 1, 1) WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    header("Location: cart.php");
    exit();
}

// Remove from Cart
if (isset($_POST['remove_from_cart'])) {
    $product_id = intval($_POST['product_id']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    header("Location: cart.php");
    exit();
}

// ====================== FETCH CART ITEMS ====================== //
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===== Minimal Fix for empty cart =====
$cart_is_empty = false;
if (empty($cart_items)) {
    $cart_is_empty = true;
}

$total_cost = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Cart</title>
<style>
body {
    font-family: 'Arial', sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
    color: #333;
}
.container {
    width: 90%;
    max-width: 1200px;
    margin: 40px auto;
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}
h2 {
    text-align: center;
    font-size: 2em;
    margin-bottom: 20px;
}
.cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px;
    margin-bottom: 15px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}
.cart-item img {
    max-width: 100px;
    border-radius: 8px;
    margin-right: 20px;
}
.item-details {
    flex-grow: 1;
}
.item-name {
    font-size: 1.2em;
    font-weight: bold;
    color: #343a40;
}
.item-price {
    font-size: 1.1em;
    color: #495057;
}
.item-actions {
    display: flex;
    align-items: center;
    gap: 5px;
}
.item-actions form button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    line-height: 1;
}
.item-actions form button:hover {
    background-color: #0056b3;
}
.quantity-display {
    width: 40px;
    text-align: center;
    border: none;
    font-size: 16px;
}
.cart-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
}
.cart-actions a {
    background-color: #28a745;
    color: white;
    padding: 12px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 1.1em;
    transition: background-color 0.3s;
}
.cart-actions a:hover {
    background-color: #218838;
}
.total-cost {
    font-size: 1.6em;
    font-weight: bold;
    color: #343a40;
    text-align: center;
    margin-top: 20px;
}
</style>
</head>
<body>
<div class="container">
<h2>Your Cart</h2>

<?php
if ($cart_is_empty) {
    echo "<p>Your cart is empty. <a href='../index.php'>Go back to shop</a></p>";
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

        echo "<div class='cart-item'>
            <img src='../images/{$product['image']}' alt='{$product['name']}'>
            <div class='item-details'>
                <div class='item-name'>{$product['name']}</div>
                <div class='item-price'>₹" . number_format($product['price'], 2) . " x $quantity</div>
            </div>
            <div class='item-actions'>
                <form method='POST'>
                    <input type='hidden' name='product_id' value='{$product['id']}'>
                    <button type='submit' name='decrease_quantity'>−</button>
                </form>
                <input type='text' class='quantity-display' value='$quantity' readonly>
                <form method='POST'>
                    <input type='hidden' name='product_id' value='{$product['id']}'>
                    <button type='submit' name='increase_quantity'>+</button>
                </form>
                <form method='POST'>
                    <input type='hidden' name='product_id' value='{$product['id']}'>
                    <button type='submit' name='remove_from_cart'>Remove</button>
                </form>
            </div>
        </div>";
    }

    echo "<div class='total-cost'>Total: ₹" . number_format($total_cost, 2) . "</div>";
    echo "<div class='cart-actions'>
            <a href='../index.php'>Back to Shop</a>
            <a href='checkout.php'>Proceed to Checkout</a>
          </div>";
}
?>

</div>
</body>
</html>
