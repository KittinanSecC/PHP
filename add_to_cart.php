<?php
include("include.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['login_message'] = "กรุณาเข้าสู่ระบบก่อนเพิ่มสินค้าในตะกร้า";
        header("Location: login.php");
        exit();
    }

    // ดึง user_id จาก session
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = 1;

    // ตรวจสอบไซส์
    if (!isset($_POST['product_size']) || empty(trim($_POST['product_size']))) {
        $_SESSION['error_message'] = "❌ โปรดเลือกไซส์ก่อนเพิ่มลงตะกร้า";
        header("Location: product_detail.php?id=$product_id");
        exit();
    }

    $size = trim($_POST['product_size']);

    // ตรวจสอบราคาสินค้า
    if (!isset($_POST['product_price']) || !is_numeric($_POST['product_price'])) {
        $_SESSION['error_message'] = "❌ ไม่พบราคาสินค้า";
        header("Location: product_detail.php?id=$product_id");
        exit();
    }

    $price = floatval($_POST['product_price']);

    // ตรวจสอบรูปภาพสินค้า
    if (!isset($_POST['product_image']) || empty($_POST['product_image'])) {
        $_SESSION['error_message'] = "❌ ไม่พบรูปภาพสินค้า";
        header("Location: product_detail.php?id=$product_id");
        exit();
    }

    $image = $_POST['product_image'];

    // ✅ INSERT พร้อมเก็บชื่อรูป
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, size, quantity, price, image) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        $_SESSION['error_message'] = "❌ SQL Error: " . $conn->error;
        header("Location: product_detail.php?id=$product_id");
        exit();
    }

    $stmt->bind_param("iisids", $user_id, $product_id, $size, $quantity, $price, $image);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "✅ เพิ่มสินค้าในตะกร้าสำเร็จ!";
        header("Location: cart.php");
        exit();
    } else {
        $_SESSION['error_message'] = "❌ ไม่สามารถเพิ่มสินค้าได้";
        header("Location: product_detail.php?id=$product_id");
        exit();
    }

    $stmt->close();
    $conn->close();
}
