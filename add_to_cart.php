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

    // ดึงข้อมูลจากฟอร์ม
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $size = trim($_POST['product_size']);
    $price = floatval($_POST['product_price']);
    $image = $_POST['product_image'];
    $quantity = 1; // ตั้งค่าเริ่มต้นให้เพิ่มทีละ 1 ชิ้น

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($size)) {
        $_SESSION['error_message'] = "โปรดเลือกขนาดก่อนเพิ่มลงตะกร้า";
        header("Location: product-detail.php?product_id=$product_id");
        exit();
    }
    if (!is_numeric($price)) {
        die("❌ Error: ไม่พบราคาสินค้า");
    }
    if (empty($image)) {
        die("❌ Error: ไม่พบรูปภาพสินค้า");
    }

    // 🔹 ดึงข้อมูลสต็อกจาก product_sizes
    $sql_stock = "SELECT Stock FROM product_sizes WHERE ProductID = ? AND Size = ?";
    $stmt_stock = $conn->prepare($sql_stock);
    $stmt_stock->bind_param("is", $product_id, $size);
    $stmt_stock->execute();
    $result_stock = $stmt_stock->get_result();
    $product_stock = $result_stock->fetch_assoc();

    if (!$product_stock) {
        die("❌ Error: ข้อมูลสต็อกไม่ถูกต้อง");
    }
    $stock_available = $product_stock['Stock']; // สต็อกสินค้าตามขนาด

    // 🔹 ตรวจสอบว่าสินค้าที่จะเพิ่มเข้าไปมีอยู่ในตะกร้าแล้วหรือไม่
    $sql = "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND size = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $user_id, $product_id, $size);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartItem = $result->fetch_assoc();

    if ($cartItem) {
        // ถ้ามีสินค้านี้อยู่ในตะกร้าแล้ว
        $new_quantity = $cartItem['quantity'] + 1;

        // 🔴 ตรวจสอบว่าไม่เกินสต็อก
        if ($new_quantity > $stock_available) {
            $_SESSION['error_message'] = "จำนวนสินค้าในตะกร้าเกินสต็อก (เหลือ: $stock_available ชิ้น)";
            header("Location: product-detail.php?product_id=$product_id");
            exit();
        }

        // อัปเดตจำนวนสินค้าในตะกร้า
        $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_quantity, $cartItem['cart_id']);
    } else {
        // ถ้ายังไม่มีสินค้าในตะกร้า ให้เพิ่มสินค้าใหม่
        if ($quantity > $stock_available) {
            $_SESSION['error_message'] = "จำนวนสินค้าในตะกร้าเกินสต็อก (เหลือ: $stock_available ชิ้น)";
            header("Location: product-detail.php?product_id=$product_id");
            exit();
        }

        // เพิ่มสินค้าใหม่ลงในตะกร้า
        $sql = "INSERT INTO cart (user_id, product_id, size, quantity, price, image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisids", $user_id, $product_id, $size, $quantity, $price, $image);
    }

    // ทำการ execute คำสั่ง
    if ($stmt->execute()) {
        echo "<script>alert('✅ เพิ่มสินค้าในตะกร้าสำเร็จ!'); window.location='cart.php';</script>";
    } else {
        die("❌ Error: ไม่สามารถเพิ่มสินค้าได้");
    }

    $stmt->close();
    $conn->close();
}
