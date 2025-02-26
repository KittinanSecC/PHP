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

// ดึงข้อมูลสินค้า
$sql = "SELECT c.price, c.user_id, p.Price AS latest_price 
        FROM cart c 
        JOIN product p ON c.product_id = p.ID 
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
$new_price_per_item = floatval($cartItem['latest_price']); // ราคาล่าสุดจากตารางสินค้า

// อัปเดตราคาล่าสุดและจำนวนสินค้าในตะกร้า
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

// คำนวณยอดรวมสินค้าในตะกร้าใหม่
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

// ส่งข้อมูลกลับ
echo json_encode([
    "success" => true,
    "new_price_per_item" => $new_price_per_item, // ✅ ราคาต่อชิ้นที่อัปเดต
    "new_subtotal" => floatval($newTotal)
]);
?>
