<?php
session_start();
include 'include.php'; // Connect to the database
include("structure.php");

// Get the order_id from the URL
$order_id = $_GET['order_id'] ?? 0;

if ($order_id == 0) {
    die("<h2>ไม่พบคำสั่งซื้อ</h2>");
}

// Retrieve the order details from the database
$order_sql = "SELECT * FROM orders WHERE order_id = ?";
$order_stmt = $conn->prepare($order_sql);

if ($order_stmt === false) {
    die("Error preparing order SQL: " . $conn->error);
}

$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result === false) {
    die("Error fetching order data: " . $conn->error);
}

$order = $order_result->fetch_assoc();


if (!$order) {
    die("<h2>ไม่พบคำสั่งซื้อ</h2>");
}

// Retrieve the order items
$item_sql = "SELECT oi.*, p.Name AS product_name, p.FilesName AS product_image
             FROM order_items oi
             JOIN product p ON oi.product_id = p.ID
             WHERE oi.order_id = ?";
$item_stmt = $conn->prepare($item_sql);

if ($item_stmt === false) {
    die("Error preparing item SQL: " . $conn->error);
}

$item_stmt->bind_param("i", $order_id);
$item_stmt->execute();
$item_result = $item_stmt->get_result();

if ($item_result === false) {
    die("Error fetching item data: " . $conn->error);
}

$items = $item_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดคำสั่งซื้อ</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
    <link href="assets/logo/Prime2.png" rel="icon">
</head>
<style>
    .order_container {
        max-width: 960px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
    }

    .order_container h1 {
        color: #000000;
        margin-bottom: 20px;
    }

    .order_container p {
        margin-bottom: 10px;
    }

    .order_container table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .order_container th,
    td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .order_container th {
        background-color: #000000;
        color: #fff;
    }

    .order_container img {
        max-width: 50px;
        height: auto;
        margin-right: 10px;
        vertical-align: middle;
    }

    .order_container h2 {
        margin-top: 30px;
    }

    .order_container h3 {
        margin-top: 20px;
    }

    .order_container .btn {
        display: inline-block;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 20px;
    }
</style>

<body>
    <?php renderHeader($conn); ?>

    <div class="container order_container">
        <h1>รายละเอียดคำสั่งซื้อ #<?php echo $order_id; ?></h1>
        <p><strong>ชื่อผู้รับ:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
        <p><strong>ที่อยู่จัดส่ง:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
        <p><strong>ช่องทางชำระเงิน:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
        <p><strong>วันที่สั่งซื้อ:</strong> <?php echo $order['created_at']; ?></p>

        <h2>รายการสินค้า</h2>
        <table>
            <tr>
                <th>สินค้า</th>
                <th>จำนวน</th>
                <th>ราคา</th>
            </tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <img src="myfile/<?php echo $item['product_image']; ?>" width="50">
                        <?php echo htmlspecialchars($item['product_name']); ?>
                    </td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo number_format($item['price'] * $item['quantity'], 2); ?> ฿</td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>ยอดรวม: <?php echo number_format($order['total_price'], 2); ?> ฿</h3>
        <button onclick="history.back()" class="btn btn-dark">ย้อนกลับ</button>

        <!-- Check if there is a payment proof and display the button -->
        <?php if ($order['payment_proof']): ?>
            <h3>หลักฐานการชำระเงิน:</h3>
            <a href="<?php echo htmlspecialchars($order['payment_proof']); ?>" class="btn btn-dark">ดูสลิปการโอน</a>
        <?php endif; ?>

        
    </div>
    </div>
    

    <?php renderFooter(); ?>
</body>

</html>