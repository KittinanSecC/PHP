<?php
session_start();
include("include.php"); // เชื่อมต่อฐานข้อมูล
include("structure.php"); // โครงสร้างหน้าเว็บ

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view this page.");
}

$user_id = $_SESSION['user_id']; // ใช้ user_id จาก session

// ตรวจสอบการเชื่อมต่อกับฐานข้อมูล
if ($conn->connect_error) {
    die("Failed to connect to DB: " . $conn->connect_error);
}

// ดึงข้อมูลของผู้ใช้ที่เข้าสู่ระบบ
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$user = $result->fetch_assoc()) {
    die("User not found.");
}

// ดึงข้อมูลสินค้าจากตะกร้า
$sql = "SELECT cart.*, product.Name AS pro_name, product.FilesName AS pro_image
        FROM cart
        JOIN product ON cart.product_id = product.ID
        WHERE cart.user_id = ?";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result = $stmt2->get_result();
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['name']) && !empty($_POST['address']) && !empty($_POST['payment_method']) && isset($_POST['total_price'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $address = $conn->real_escape_string($_POST['address']);
        $payment_method = $conn->real_escape_string($_POST['payment_method']);
        $total_price = floatval($_POST['total_price']);

        $payment_proof_path = NULL;
        if (!empty($_FILES['payment_proof']['name']) && ($payment_method === "พร้อมเพย์" || $payment_method === "โอนผ่านธนาคาร")) {
            $targetDir = "uploads/payment_proofs/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
            }
            $fileName = basename($_FILES["payment_proof"]["name"]);
            $uniqueFileName = uniqid() . "_" . $fileName;
            $targetFilePath = $targetDir . $uniqueFileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            $allowedTypes = ["jpg", "jpeg", "png", "pdf"];
            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES["payment_proof"]["tmp_name"], $targetFilePath)) {
                    $payment_proof_path = $targetFilePath;
                } else {
                    die("Failed to upload payment proof.");
                }
            } else {
                die("Invalid file type. Allowed types: JPG, JPEG, PNG, PDF");
            }
        }

        // บันทึกคำสั่งซื้อ
        $order_sql = "INSERT INTO orders (user_id, total_price, shipping_address, payment_method, name, payment_proof)
                      VALUES (?, ?, ?, ?, ?, ?)";
        $order_stmt = $conn->prepare($order_sql);
        $order_stmt->bind_param("sdssss", $user_id, $total_price, $address, $payment_method, $name, $payment_proof_path);
        if ($order_stmt->execute()) {
            $order_id = $conn->insert_id;

            // ย้ายข้อมูลจาก cart ไป order_items
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price, size)
                         VALUES (?, ?, ?, ?, ?)";
            $item_stmt = $conn->prepare($item_sql);
            foreach ($cart_items as $item) {
                $size = $item['size'] ?? '';
                $item_stmt->bind_param("iiids", $order_id, $item['product_id'], $item['quantity'], $item['price'], $size);
                $item_stmt->execute();
            }

            // ล้างข้อมูลใน cart
            $delete_cart_sql = "DELETE FROM cart WHERE user_id = ?";
            $delete_cart_stmt = $conn->prepare($delete_cart_sql);
            $delete_cart_stmt->bind_param("i", $user_id);
            $delete_cart_stmt->execute();

            // Redirect ไปหน้าสำเร็จ
            header("Location: success.php?order_id=$order_id");
            exit;
        } else {
            die("Failed to place order.");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คำสั่งซื้อ</title>
    <link href="assets/logo/Prime2.png" rel="icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .checkout-container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .product-table img {
            border-radius: 5px;
        }

        .form-control,
        .btn {
            border-radius: 8px;
        }

        .qr-code {
            width: 450px;
            /* ขนาดที่ใหญ่ขึ้น */
            height: auto;
            display: block;
            margin: auto;
        }
    </style>
</head>

<body>
    <?php renderHeader($conn); ?>
    <div class="container mt-5 checkout-container">
        <h2 class="text-center">สรุปคำสั่งซื้อ</h2>
        <form method="post" enctype="multipart/form-data">
            <table class="table product-table mt-3">
                <thead>
                    <tr class="table-dark text-center">
                        <th>สินค้า</th>
                        <th>รูป</th>
                        <th>ไซส์</th>
                        <th>จำนวน</th>
                        <th>ราคา</th>
                    </tr>
                </thead>
                <?php
                $total = 0;
                $shipping_fee = 100; // ค่าจัดส่ง
                $service_fee = 50; // ค่าธรรมเนียม

                // ✅ คำนวณรวมราคาสินค้าก่อนแสดงผล
                foreach ($cart_items as $item) {
                    $total += ($item['price'] * $item['quantity']);
                }

                $total_before_fees = $total; // เก็บค่าราคารวมของสินค้าไว้ก่อนบวกค่าธรรมเนียม
                $total += $shipping_fee + $service_fee; // บวกค่าธรรมเนียมและค่าจัดส่ง

                ?>

                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr class="text-center">
                            <td><?php echo htmlspecialchars($item['pro_name']); ?></td>
                            <td><img src="myfile/<?php echo htmlspecialchars($item['pro_image']); ?>" width="80"></td>
                            <td><?php echo htmlspecialchars($item['size'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>฿<?php echo number_format($item['price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold text-end">
                        <td colspan="5">รวมค่าสินค้า: ฿<?php echo number_format($total_before_fees, 2); ?></td>
                    </tr>
                    <tr class="fw-bold text-end">
                        <td colspan="5">ค่าธรรมเนียม: ฿<?php echo number_format($service_fee, 2); ?></td>
                    </tr>
                    <tr class="fw-bold text-end">
                        <td colspan="5">ค่าจัดส่ง: ฿<?php echo number_format($shipping_fee, 2); ?></td>
                    </tr>
                    <tr class="fw-bold text-end">
                        <td colspan="5">รวมทั้งหมด: ฿<?php echo number_format($total, 2); ?></td>
                        <input type="hidden" name="total_price" value="<?php echo $total; ?>">
                    </tr>
                </tbody>

            </table>

            <h4 class="mt-4">ที่อยู่จัดส่ง</h4>
            <input type="text" class="form-control mb-2" name="name" placeholder="ชื่อ-นามสกุล" value="<?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>" required>
            <input type="text" class="form-control mb-2" name="email" placeholder="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            <textarea name="address" class="form-control" placeholder="ที่อยู่" required></textarea>

            <h4 class="mt-3">ช่องทางชำระเงิน</h4>
            <select name="payment_method" id="payment_method" class="form-control" required>
                <option value="เก็บเงินปลายทาง">เก็บเงินปลายทาง</option>
                <option value="โอนผ่านธนาคาร">พร้อมเพย์/โอนผ่านธนาคาร</option>



            </select>
            <div id="payment_proof_section" class="mt-3" style="display: none;">
                <div class="text-center">
                    <label class="form-label fw-bold fs-5">QR พร้อมเพย์ / ธนาคาร:</label>
                    <div class="qr-container mt-4 mb-4">
                        <img src="assets/bank/prom.jpg" alt="QR พร้อมเพย์ / ธนาคาร" class="qr-code">
                    </div>
                </div>
                <div>
                    <label class="form-label fw-bold">แนบสลิปการโอน:</label>
                    <input type="file" name="payment_proof" id="payment_proof" class="form-control">
                </div>
            </div>


            <script>
                document.getElementById("payment_method").addEventListener("change", function() {
                    var proofSection = document.getElementById("payment_proof_section");
                    var proofInput = document.getElementById("payment_proof");

                    if (this.value === "พร้อมเพย์" || this.value === "โอนผ่านธนาคาร") {
                        proofSection.style.display = "block";
                        proofInput.required = true; // บังคับให้แนบไฟล์
                    } else {
                        proofSection.style.display = "none";
                        proofInput.required = false; // ไม่บังคับถ้าเลือกวิธีอื่น
                    }
                });

                document.getElementById("checkoutForm").addEventListener("submit", function(event) {
                    var paymentMethod = document.getElementById("payment_method").value;
                    var proofInput = document.getElementById("payment_proof");

                    if ((paymentMethod === "พร้อมเพย์" || paymentMethod === "โอนผ่านธนาคาร") && proofInput.files.length === 0) {
                        alert("กรุณาแนบสลิปการโอนเงินก่อนกดยืนยัน!");
                        event.preventDefault(); // ป้องกันการส่งฟอร์ม
                    }
                });
            </script>
            <div class="d-flex justify-content-between mt-4">
                <a href="cart.php" class="btn btn-light border" style="border: 1px solid;">ย้อนกลับ</a>
                <button type="submit" class="btn btn-dark" >ยืนยันการชำระเงิน</button>
            </div>
        </form>
    </div>
    <?php renderFooter(); ?>
</body>

</html>