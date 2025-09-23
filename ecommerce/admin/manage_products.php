<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        td img {
            width: 50px;
            height: auto;
        }
        .actions a {
            display: inline-block;
            margin: 5px 5px 5px 0;
            padding: 5px 12px;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .actions a.edit {
            background-color: #007bff;
        }
        .actions a.delete {
            background-color: #dc3545;
        }
        .actions a.edit:hover {
            background-color: #0056b3;
        }
        .actions a.delete:hover {
            background-color: #c82333;
        }
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 15px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Manage Products</h2>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert <?= $_SESSION['msg_type']; ?>">
            <?= $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($products as $product) : ?>
            <tr>
                <td><?= $product['id']; ?></td>
                <td><?= htmlspecialchars($product['name']); ?></td>
                <td>₹<?= number_format($product['price'], 2, '.', ','); ?></td>
                <td><?= htmlspecialchars($product['description']); ?></td>
                <td><img src="../images/<?= htmlspecialchars($product['image']); ?>" alt="Product Image"></td>
                <td class="actions">
                    <a href="edit_product.php?id=<?= $product['id']; ?>" class="edit">Edit</a>
                    <a href="delete_product.php?id=<?= $product['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
</div>

</body>
</html>
