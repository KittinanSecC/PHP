<?php
session_start();
include 'include.php'; // เชื่อมต่อฐานข้อมูล
include("structure.php");

// รับค่า order_id จาก URL และตรวจสอบว่ามีค่าหรือไม่
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id == 0) {
    die("<h2 class='text-center text-red-500 font-bold text-xl'>❌ ไม่พบคำสั่งซื้อ (Invalid Order ID)</h2>");
}

// ดึงข้อมูลคำสั่งซื้อ
$order_sql = "SELECT * FROM orders WHERE order_id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    die("<h2 class='text-center text-red-500 font-bold text-xl'>❌ ไม่พบคำสั่งซื้อในระบบ</h2>");
}

// ดึงข้อมูลรายการสินค้าในออเดอร์
$item_sql = "SELECT oi.*, p.Name AS product_name, p.FilesName AS product_image
             FROM order_items oi
             JOIN product p ON oi.product_id = p.ID
             WHERE oi.order_id = ?";
$item_stmt = $conn->prepare($item_sql);
$item_stmt->bind_param("i", $order_id);
$item_stmt->execute();
$item_result = $item_stmt->get_result();
$items = $item_result->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คำสั่งซื้อสำเร็จ</title>
    <link href="assets/logo/Prime2.png" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
            width: 500px;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon {
            font-size: 50px;
            color: #28a745;
        }

        .title {
            font-weight: 700;
            font-size: 24px;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .title img {
            width: 40px;
            margin-left: 8px;
        }

        .order-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            text-align: left;
        }

        .order-info strong {
            color: #000;
        }

        .product-list {
            margin-top: 10px;
            text-align: left;
        }

        .product-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .product-item img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
        }

        .product-item div {
            flex: 1;
        }

        .btn {
            background: black;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn:hover {
            background: #333;
        }

        .product-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .product-name {
            flex: 2;
        }

        .product-qty,
        .product-price {
            flex: 1;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">✔️</div>
        <div class="title">คำสั่งซื้อสำเร็จ!</div>
        <p>ขอบคุณที่สั่งซื้อกับ Prime</p>
        <div class="order-info">
            <p><strong>เลขคำสั่งซื้อ:</strong> #<?php echo htmlspecialchars($order['order_id']); ?></p>
            <p><strong>ชื่อผู้รับ:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
            <p><strong>ที่อยู่จัดส่ง:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
            <p><strong>ช่องทางชำระเงิน:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
            <p><strong>วันที่สั่งซื้อ:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
        </div>

        <h3 class="mt-4">รายการสินค้า</h3>
        <div class="product-list">
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <div class="product-details">
                        <span class="product-name"><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></span>
                        <span class="product-qty">จำนวน: <?php echo htmlspecialchars($item['quantity']); ?></span>
                        <span class="product-price">ราคา: ฿<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <p>❌ ไม่มีสินค้าในคำสั่งซื้อนี้</p>
            <?php endif; ?>
        </div>

        <h3 class="mt-4">ยอดรวม: ฿<?php echo number_format($order['total_price'], 2); ?></h3>

        <a href="main.php" class="btn">กลับหน้าแรก</a>
    </div>
</body>

</html>