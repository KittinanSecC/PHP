<?php
session_start();
include("include.php");

// ตรวจสอบข้อมูลที่รับมา
if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
    echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบถ้วน"]);
    exit;
}

$cart_id = intval($_POST['cart_id']);
$quantity = intval($_POST['quantity']);

if ($quantity < 1) {
    echo json_encode(["success" => false, "message" => "จำนวนสินค้าต้องมากกว่า 0"]);
    exit;
}

// 🔹 ดึงข้อมูลสินค้า รวมถึง stock คงเหลือ
$sql = "SELECT c.price, c.user_id, c.size, c.product_id, p.Price AS latest_price, 
               ps.Stock 
        FROM cart c 
        JOIN product p ON c.product_id = p.product_id 
        JOIN product_sizes ps ON ps.ProductID = p.product_id AND ps.Size = c.size
        WHERE c.cart_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL Error"]);
    exit;
}
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$result = $stmt->get_result();
$cartItem = $result->fetch_assoc();

if (!$cartItem) {
    echo json_encode(["success" => false, "message" => "ไม่พบสินค้า"]);
    exit;
}

$user_id = intval($cartItem['user_id']);
$new_price_per_item = floatval($cartItem['latest_price']); // ราคาล่าสุดจากสินค้า
$stock_available = intval($cartItem['Stock']); // สต็อกคงเหลือของสินค้า

// 🔴 ตรวจสอบว่าจำนวนสินค้าที่อัปเดตมากกว่าสต็อกหรือไม่
if ($quantity > $stock_available) {
    echo json_encode([
        "success" => false,
        "message" => "❌ จำนวนสินค้าเกินสต็อก! (เหลือ: $stock_available ชิ้น)"
    ]);
    exit;
}

// 🔴 ตรวจสอบจำนวนสินค้าที่สามารถเพิ่มได้ตามจำนวนสต็อก
$sql_check_stock = "SELECT Stock FROM product_sizes WHERE ProductID = ? AND Size = ?";
$stmt_check_stock = $conn->prepare($sql_check_stock);
$stmt_check_stock->bind_param("is", $cartItem['product_id'], $cartItem['size']);
$stmt_check_stock->execute();
$result_check_stock = $stmt_check_stock->get_result();
$product_size = $result_check_stock->fetch_assoc();

if (!$product_size) {
    echo json_encode(["success" => false, "message" => "ข้อมูลสต็อกไม่ถูกต้อง"]);
    exit;
}

$stock_available = $product_size['Stock']; // สต็อกจาก product_sizes

// 🔴 ตรวจสอบว่าเพิ่มจำนวนสินค้าเกินสต็อกหรือไม่
if ($quantity > $stock_available) {
    echo json_encode([
        "success" => false,
        "message" => "❌ จำนวนสินค้าเกินสต็อก! (เหลือ: $stock_available ชิ้น)"
    ]);
    exit;
}



// 🔹 อัปเดตจำนวนสินค้าและราคาล่าสุดในตะกร้า
$sql = "UPDATE cart SET quantity = ?, price = ? WHERE cart_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL Error"]);
    exit;
}
$stmt->bind_param("idi", $quantity, $new_price_per_item, $cart_id);
$success = $stmt->execute();

if (!$success) {
    echo json_encode(["success" => false, "message" => "อัปเดตสินค้าไม่สำเร็จ"]);
    exit;
}

// 🔹 คำนวณยอดรวมสินค้าในตะกร้าใหม่
$sql = "SELECT SUM(quantity * price) AS new_subtotal FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL Error"]);
    exit;
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$newTotal = $result->fetch_assoc()["new_subtotal"] ?? 0;

// 🔹 Retrieve the product price for the response (price1)
$sql_price = "SELECT price AS price1 FROM product WHERE product_id = ?";
$stmt_price = $conn->prepare($sql_price);
$stmt_price->bind_param("i", $cartItem['product_id']);
$stmt_price->execute();
$result_price = $stmt_price->get_result();
$price1 = $result_price->fetch_assoc()['price1'] ?? 0;

// 🔹 ส่งข้อมูลกลับไปอัปเดตหน้าเว็บแบบเรียลไทม์
echo json_encode([
    "success" => true,
    "new_price_per_item" => $new_price_per_item, // ✅ ราคาต่อชิ้นที่อัปเดต
    "new_subtotal" => floatval($newTotal),
    "stock_available" => $stock_available, // ✅ สต็อกล่าสุด
    "price1" => floatval($price1) // ✅ ราคาจาก `product` table
]);
