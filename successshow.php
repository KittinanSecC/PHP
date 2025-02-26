<?php
session_start();
include 'include.php'; // เชื่อมต่อฐานข้อมูล
include("structure.php");

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    $currentURL = urlencode($_SERVER['REQUEST_URI']);
    header("Location: login.php?return_url=$currentURL");
    exit();
}

$user_id = $_SESSION['user_id']; // ใช้ user_id จาก session

// ดึงข้อมูลคำสั่งซื้อจากฐานข้อมูลสำหรับ user_id ที่ล็อกอินอยู่
$order_sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$order_stmt = $conn->prepare($order_sql);

if ($order_stmt === false) {
    die("Error preparing order SQL: " . $conn->error);
}

$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result === false) {
    die("Error fetching order data: " . $conn->error);
}

$orders = $order_result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการคำสั่งซื้อของคุณ</title>
    <link href="assets/logo/Prime2.png" rel="icon">
    <style>

        .success_container {
            flex: 1;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color:rgb(0, 0, 0);
            color: white;
        }

        table tr:hover {
            background-color: #f2f2f2;
        }

        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .no-orders {
            text-align: center;
            font-size: 18px;
            color: #e74c3c;
        }
    </style>
</head>

<body>
    <?php
    renderHeader($conn);
    ?>

    <div class="container success_container">
        <h1>รายการคำสั่งซื้อของคุณ</h1>

        <?php if (empty($orders)): ?>
            <p class="no-orders">ยังไม่มีคำสั่งซื้อของคุณในระบบ</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>หมายเลขคำสั่งซื้อ</th>
                        <th>วันที่สั่งซื้อ</th>
                        <th>ยอดรวม</th>
                        <th>สถานะคำสั่งซื้อ</th>
                        <th>ดูรายละเอียด</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['created_at']; ?></td>
                            <td><?php echo number_format($order['total_price'], 2); ?> ฿</td>
                            <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                            <td><a href="order_details.php?order_id=<?php echo $order['order_id']; ?>" class="btn">ดูรายละเอียด</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
    <?php
    renderFooter();
    ?>
</body>

</html>