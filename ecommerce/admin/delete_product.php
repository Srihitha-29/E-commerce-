<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['message'] = "✅ Product deleted successfully!";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['message'] = "❌ Failed to delete product.";
            $_SESSION['msg_type'] = "error";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "⚠️ Database error: " . $e->getMessage();
        $_SESSION['msg_type'] = "error";
    }
} else {
    $_SESSION['message'] = "⚠️ Invalid request.";
    $_SESSION['msg_type'] = "error";
}

header("Location: manage_products.php");
exit();
?>
