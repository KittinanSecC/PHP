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
    $product_id = intval($_POST['product_id']);
    $size = trim($_POST['product_size']);
    $price = floatval($_POST['product_price']);
    $image = $_POST['product_image'];
    $quantity = 1; // ตั้งค่าเริ่มต้นให้เพิ่มทีละ 1 ชิ้น

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($size)) {
        die("❌ Error: โปรดเลือกไซส์ก่อนเพิ่มลงตะกร้า");
    }
    if (!is_numeric($price)) {
        die("❌ Error: ไม่พบราคาสินค้า");
    }
    if (empty($image)) {
        die("❌ Error: ไม่พบรูปภาพสินค้า");
    }

    // ตรวจสอบว่าสินค้านี้ + ไซส์นี้ มีอยู่ในตะกร้าหรือยัง
    $sql = "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND size = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $user_id, $product_id, $size);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartItem = $result->fetch_assoc();

    if ($cartItem) {
        // ถ้ามีอยู่แล้ว ให้อัปเดตจำนวนสินค้า
        $new_quantity = $cartItem['quantity'] + 1;
        $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_quantity, $cartItem['cart_id']);
    } else {
        // ถ้ายังไม่มี ให้เพิ่มเข้าไปใหม่
        $sql = "INSERT INTO cart (user_id, product_id, size, quantity, price, image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisids", $user_id, $product_id, $size, $quantity, $price, $image);
    }

    if ($stmt->execute()) {
        echo "<script>alert('✅ เพิ่มสินค้าในตะกร้าสำเร็จ!'); window.location='cart.php';</script>";
    } else {
        die("❌ Error: ไม่สามารถเพิ่มสินค้าได้");
    }

    $stmt->close();
    $conn->close();
}
?>
