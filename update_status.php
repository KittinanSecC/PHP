<?php
session_start();
include 'include.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

// ตรวจสอบว่า order_id และ order_status ถูกส่งมาหรือไม่
if (!isset($_POST['order_id']) || !isset($_POST['order_status'])) {
    die("Invalid request.");
}

$order_id = intval($_POST['order_id']);
$order_status = $_POST['order_status']; // รับค่าจาก dropdown
$user_id = $_SESSION['user_id'];

// ตรวจสอบว่าเป็นแอดมินหรือเจ้าของออเดอร์
$check_sql = "SELECT user_id, order_status FROM orders WHERE order_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $order_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    die("Order not found.");
}

$order = $check_result->fetch_assoc();

// อนุญาตให้แค่แอดมิน (user_id = 0) หรือเจ้าของออเดอร์อัปเดตสถานะ
if ($user_id != 0 && $order['user_id'] != $user_id) {
    die("You don't have permission to update this order.");
}

// อัปเดต order_status ตามที่เลือกใน dropdown
$update_sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("si", $order_status, $order_id);

if ($update_stmt->execute()) {
    echo "Order status updated successfully.";
} else {
    echo "Failed to update order status.";
}

$update_stmt->close();
$check_stmt->close();
$conn->close();

// Redirect กลับไปที่หน้า orders
header("Location: successshow.php");
exit();
