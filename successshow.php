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

// ตรวจสอบว่า user_id เป็น 0 หรือไม่
if ($user_id == 0) {
    // ดึงข้อมูลคำสั่งซื้อทั้งหมด พร้อมกับข้อมูลผู้ใช้จาก users table
    $order_sql = "
        SELECT o.*, u.firstName, u.lastName, u.username, u.email 
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.user_id
        ORDER BY o.created_at DESC
    ";
} else {
    // ดึงข้อมูลคำสั่งซื้อสำหรับ user_id ที่ล็อกอิน
    $order_sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
}

$order_stmt = $conn->prepare($order_sql);

if ($order_stmt === false) {
    die("Error preparing order SQL: " . $conn->error);
}

// ถ้า user_id ไม่ใช่ 0, bind_param() จะใช้
if ($user_id != 0) {
    $order_stmt->bind_param("i", $user_id);
}

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
        /* Ensuring table takes full width but adjusts based on content */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            table-layout: auto;
            /* Let the table adjust column width based on content */
        }

        /* Table header and data cells */
        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }

        /* Style for table headers */
        table th {
            background-color: rgb(0, 0, 0);
            color: white;
            text-align: center;
        }

        /* Hover effect for rows */
        table tr:hover {
            background-color: #f2f2f2;
        }

        /* Style for buttons */
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .btn-dark {
            background-color: #333;
        }

        /* Hover effect for buttons */
        .btn:hover,
        .btn-dark:hover {
            background-color: #2980b9;
        }

        /* Additional styles for the container and header */
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
    <?php
    // ปรับข้อความแสดงผลตาม user_id
    if ($user_id == 0) {
        echo '<div class="container success_container">
        <h1>รายการคำสั่งซื้อทั้งหมด</h1>';
    } else {
        echo    '<div class="container success_container">
                <h1>รายการคำสั่งซื้อของคุณ</h1>';
    };
    ?>

    <?php if (empty($orders)): ?>
        <p class="no-orders">ยังไม่มีคำสั่งซื้อในระบบ</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <?php if ($user_id == 0): ?>
                        <th>หมายเลขผู้ใช้</th> <!-- Add this column if user_id is 0 -->
                        <th>ชื่อผู้ใช้</th>
                        <th>Username</th>
                        <th>อีเมล</th>
                    <?php endif; ?>
                    <th>หมายเลขคำสั่งซื้อ</th>
                    <th>วันที่สั่งซื้อ</th>
                    <th>ยอดรวม</th>
                    <th>สถานะคำสั่งซื้อ</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <?php if ($user_id == 0): ?>
                            <td><?php echo $order['user_id']; ?></td>
                            <td><?php echo $order['firstName']; ?> <?php echo $order['lastName']; ?></td>
                            <td><?php echo $order['username']; ?></td>
                            <td><?php echo $order['email']; ?></td>
                        <?php endif; ?>
                        <td>#<?php echo $order['order_id']; ?></td>
                        <td><?php echo $order['created_at']; ?></td>
                        <td><?php echo number_format($order['total_price'], 2); ?> ฿</td>
                        <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                        <td><a href="order_details.php?order_id=<?php echo $order['order_id']; ?>" class="btn">ดูรายละเอียด</a></td>
                        <td><a href="" class="btn btn-dark">update status</a></td>
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