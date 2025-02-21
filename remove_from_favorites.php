<?php
session_start();
include("include.php"); // เชื่อมต่อฐานข้อมูล

if (!isset($_SESSION['user_id'])) {
    exit("❌ คุณต้องเข้าสู่ระบบก่อน");
}

$user_id = $_SESSION['user_id'];
$product_id = filter_input(INPUT_POST, 'ID', FILTER_VALIDATE_INT);

if (!$product_id) {
    exit("❌ รหัสสินค้าไม่ถูกต้อง");
}

$sql = "DELETE FROM favorites WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ii", $user_id, $product_id);
    if ($stmt->execute()) {
        echo "✅ ลบสินค้าออกจากรายการโปรดสำเร็จ!";
    } else {
        echo "❌ เกิดข้อผิดพลาด: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "❌ การเตรียมคำสั่งล้มเหลว: " . $conn->error;
}

$conn->close();
?>