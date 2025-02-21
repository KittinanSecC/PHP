<?php
session_start();
include("include.php"); // เชื่อมต่อฐานข้อมูล
include("structure.php");
// ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    // Redirect to login.php with a return URL
    $currentURL = urlencode($_SERVER['REQUEST_URI']); // URL-encode the current URL
    header("Location: login.php?return_url=$currentURL"); // Redirect with return URL
    exit(); // Stop further execution
}

// ใช้ user_id จาก session โดยตรง
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลสินค้าจากตะกร้า
$sql = "SELECT c.cart_id, p.Name, c.size, c.quantity, c.price, c.image, 
               (c.quantity * c.price) AS total_price 
        FROM cart c
        JOIN product p ON c.product_id = p.ID 
        WHERE c.user_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("❌ SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total = 0;
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้า Prime Store TH</title>
    <link href="assets/logo/Prime2.png" rel="icon">
    <link rel="stylesheet" href="styles6.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<style>
    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .qty-btn {
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: white;
        border: 1px solid #ccc;

        width: 30px;
        height: 30px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
    }

    .qty-btn:hover {


        border-color: black;
    }

    .quantity-value {


        min-width: 24px;
        text-align: center;
    }

    .success_container {
        flex: 1;
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
    }

    .success_container h1 {
        color: #2c3e50;
        text-align: center;
        margin-bottom: 20px;
    }
</style>

<body>
    <?php renderHeader($conn) ?>
    <div class="container success_container">
        <h1>ตะกร้าสินค้า</h1>

        <div class="cart-container">
            <?php if ($result->num_rows > 0): ?>
                <div class="cart-items">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="cart-item" data-cart-id="<?= $row['cart_id'] ?>">
                            <img src="http://localhost/PHP/<?= htmlspecialchars($row['image']) ?>" width="100">
                            <div class="product-details">
                                <h3><?= htmlspecialchars($row['Name']) ?></h3>
                                <p>ไซส์: <?= htmlspecialchars($row['size']) ?></p>
                                <p>฿<?= number_format($row['price']) ?></p>
                                <div class="quantity-controls">
                                    <button class="qty-btn" data-cart-id="<?= $row['cart_id'] ?>">-</button>
                                    <span class="quantity-value"><?= $row['quantity'] ?></span>
                                    <button class="qty-btn" data-cart-id="<?= $row['cart_id'] ?>">+</button>
                                </div>
                            </div>
                            <div class="cart-actions">
                                <button class="delete-btn" data-cart-id="<?= $row['cart_id'] ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php $total += $row['total_price']; ?>
                    <?php endwhile; ?>
                </div>

                <div class="cart-summary">
                    <h3>สรุป</h3>
                    <p>ยอดรวมย่อย: ฿<span class="subtotal"><?= number_format($total, 2) ?></span></p>
                    <p>ค่าธรรมเนียมและค่าจัดส่ง: ฿150.00</p>
                    <hr>
                    <p><strong>ยอดรวม: ฿<span class="total"><?= number_format($total + 150, 2) ?></span></strong></p>
                    <form action="checkout.php" method="POST">
                        <button type="submit" class="btn cart-btn w-100 my-2 btn-dark" style="height:40px">สั่งซื้อเลย</button>
                    </form>
                    <a href=upload3.php><button class="btn wishlist-btn w-100 btn-light" style="border: 1px solid;">
                            <a>กลับหน้าสินค้า</a></button></a>
                </div>
                <script src="cart.js"></script>
            <?php else: ?>
                <p class="empty-cart">ตะกร้าของคุณว่างเปล่า 🛒</p>
            <?php endif; ?>
        </div>
    </div>
    <?php renderFooter(); ?>
</body>

</html>