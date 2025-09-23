<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "⚠️ Invalid product ID.";
    $_SESSION['msg_type'] = "error";
    header("Location: manage_products.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch existing product data
$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['message'] = "❌ Product not found.";
    $_SESSION['msg_type'] = "error";
    header("Location: manage_products.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE products SET name=:name, price=:price, description=:description WHERE id=:id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ Product '{$name}' updated successfully at ₹" . number_format($price, 2);
        $_SESSION['msg_type'] = "success";
        header("Location: manage_products.php");
        exit();
    } else {
        $_SESSION['message'] = "❌ Failed to update product.";
        $_SESSION['msg_type'] = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background: #fff; padding: 20px; border-radius: 8px; width: 50%; margin: auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        input, textarea { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #ccc; }
        button { padding: 12px 20px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #45a049; }
        .back { margin-top: 15px; display: inline-block; text-decoration: none; color: #4CAF50; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Product</h2>
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>

            <label>Price (₹):</label>
            <input type="number" step="0.01" name="price" value="<?= $product['price']; ?>" required>

            <label>Description:</label>
            <textarea name="description" required><?= htmlspecialchars($product['description']); ?></textarea>

            <button type="submit">Update Product</button>
        </form>
        <a href="manage_products.php" class="back">Back to Manage Products</a>
    </div>
</body>
</html>
