<?php
session_start();
include("include.php"); // เชื่อมต่อฐานข้อมูล

if (!isset($_SESSION['user_id'])) {
    exit("❌ คุณต้องเข้าสู่ระบบก่อน");
}

$user_id = $_SESSION['user_id'];
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

if (!$product_id) {
    exit("❌ รหัสสินค้าไม่ถูกต้อง");
}

// ✅ ตรวจสอบว่า product_id มีอยู่จริง
$check_sql = "SELECT COUNT(*) FROM product WHERE ID = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $product_id);
$check_stmt->execute();
$check_stmt->bind_result($count);
$check_stmt->fetch();
$check_stmt->close();

if ($count == 0) {
    exit("❌ ไม่พบสินค้าในฐานข้อมูล");
}

// ✅ ตรวจสอบว่า user_id กับ product_id ซ้ำหรือไม่
$check_fav_sql = "SELECT COUNT(*) FROM favorites WHERE user_id = ? AND product_id = ?";
$check_fav_stmt = $conn->prepare($check_fav_sql);
$check_fav_stmt->bind_param("ii", $user_id, $product_id);
$check_fav_stmt->execute();
$check_fav_stmt->bind_result($fav_count);
$check_fav_stmt->fetch();
$check_fav_stmt->close();

if ($fav_count > 0) {
    exit("❌ สินค้านี้อยู่ในรายการโปรดแล้ว");
}

// ✅ ถ้าผ่านการตรวจสอบทั้งหมด ให้เพิ่มลง favorites
$sql = "INSERT INTO favorites (user_id, product_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ii", $user_id, $product_id);
    if ($stmt->execute()) {
        echo "✅ เพิ่มสินค้าในรายการโปรดสำเร็จ!";
    } else {
        echo "❌ เกิดข้อผิดพลาด: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "❌ การเตรียมคำสั่งล้มเหลว: " . $conn->error;
}

$conn->close();
?>
 