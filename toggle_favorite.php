<?php
session_start();
include("include.php");

if (!isset($_SESSION['user_id'])) {
    echo "❌ กรุณาเข้าสู่ระบบ";
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if ($product_id == 0) {
    echo "❌ ไม่พบสินค้า";
    exit;
}

// ตรวจสอบว่าสินค้าอยู่ในรายการโปรดหรือไม่
$sql_check = "SELECT * FROM favorites WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ลบออกจากรายการโปรด
    $sql_delete = "DELETE FROM favorites WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("ii", $user_id, $product_id);
    if ($stmt->execute()) {
        echo "❌ ลบออกจากรายการโปรด";
    } else {
        echo "❌ เกิดข้อผิดพลาด";
    }
} else {
    // เพิ่มในรายการโปรด
    $sql_insert = "INSERT INTO favorites (user_id, product_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("ii", $user_id, $product_id);
    if ($stmt->execute()) {
        echo "✅ เพิ่มสินค้าในรายการโปรด";
    } else {
        echo "❌ เกิดข้อผิดพลาด";
    }
}

$stmt->close();
$conn->close();
?>
