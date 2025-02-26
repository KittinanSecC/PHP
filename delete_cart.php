<?php
session_start();
include("include.php");

header("Content-Type: application/json");

// ตรวจสอบ session ว่ามี user_id หรือไม่
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "⛔ Unauthorized"]);
    exit;
}

if (!isset($_POST['cart_id'])) {
    echo json_encode(["success" => false, "message" => "❌ ไม่มี cart_id"]);
    exit;
}

$cart_id = intval($_POST['cart_id']);
$user_id = $_SESSION['user_id'];

// ตรวจสอบว่าสินค้าเป็นของ user นี้จริงหรือไม่
$check_sql = "SELECT * FROM cart WHERE cart_id = ? AND user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $cart_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "❌ ไม่พบสินค้านี้ในตะกร้า"]);
    exit;
}

// ลบสินค้าโดยตรวจสอบ user_id ด้วย
$sql = "DELETE FROM cart WHERE cart_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_id, $user_id);
$delete_success = $stmt->execute();

if ($delete_success) {
    // คำนวณยอดรวมใหม่ของตะกร้า
    $total_sql = "SELECT SUM(quantity * price) AS total FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($total_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $total_result = $stmt->get_result();
    $total = $total_result->fetch_assoc()['total'] ?? 0;

    echo json_encode(["success" => true, "new_total" => number_format($total, 2)]);
} else {
    echo json_encode(["success" => false, "message" => "❌ ลบสินค้าไม่สำเร็จ"]);
}
?>
