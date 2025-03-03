<?php
// เชื่อมต่อฐานข้อมูล
include("include.php");

// ดึงข้อมูลชื่อสินค้าจาก product_sizes
$sql = "SELECT DISTINCT ProductID FROM product_sizes";
$product_result = $conn->query($sql);

// ดึงข้อมูลขนาดสินค้าจากฐานข้อมูล
$size_sql = "SELECT DISTINCT size FROM product_sizes";
$size_result = $conn->query($size_sql);
?>

<!DOCTYPE html>
<html lang="th">
<?php
session_start();
include("include.php");
include("structure.php");
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้าอย่างเป็นทางการของ Prime Sneakers Store</title>
    <link href="assets/logo/Prime2.png" rel="icon">
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        form {
            width: 500px;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        form h2 {
            margin-bottom: 20px;
            font-size: 26px;
            color: #333;
            text-align: center;
        }

        form label {
            display: block;
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: bold;
            color: #000000;
        }

        form input[type="text"],
        form input[type="Stock"],
        form select,
        form input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        form input[type="file"] {
            padding: 5px;
        }

        form input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: rgb(29, 27, 27);
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form input[type="submit"]:hover {
            background-color: rgb(0, 0, 0);
        }

        form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
    </style>

</head>

<body>
    <?php
    renderHeader($conn);
    ?>
    <form name="form2" method="post" action="Stockupdate.php" enctype="multipart/form-data">
        <h2>เพิ่มจำนวนสินค้า</h2>

        <label for="txtName">ชื่อสินค้า :</label>
        <select name="txtName" id="txtName" required>
            <option value="" disabled selected>เลือกชื่อสินค้า</option>
            <?php
            // ดึงชื่อสินค้าจาก product โดยใช้ productID ที่ได้จาก product_sizes
            if ($product_result->num_rows > 0) {
                while ($product_row = $product_result->fetch_assoc()) {
                    $productID = $product_row['ProductID'];

                    // ดึงชื่อสินค้าจาก product table โดยใช้ productID
                    $product_name_sql = "SELECT Name FROM product WHERE product_id = ?";
                    $product_name_stmt = $conn->prepare($product_name_sql);
                    $product_name_stmt->bind_param("i", $productID);
                    $product_name_stmt->execute();
                    $product_name_result = $product_name_stmt->get_result();

                    if ($product_name_result->num_rows > 0) {
                        while ($name_row = $product_name_result->fetch_assoc()) {
                            echo "<option value='" . $productID . "'>" . $name_row['Name'] . "</option>";
                        }
                    }
                    $product_name_stmt->close();
                }
            } else {
                echo "<option value=''>ไม่มีข้อมูลสินค้า</option>";
            }
            ?>
        </select><br>

        <label for="txtSize">ขนาด :</label>
        <select name="txtSize" id="txtSize" required>
            <option value="" disabled selected>เลือกขนาด</option>
            <?php
            // ตรวจสอบว่ามีข้อมูลขนาดสินค้า
            if ($size_result->num_rows > 0) {
                while ($size_row = $size_result->fetch_assoc()) {
                    echo "<option value='" . $size_row['size'] . "'>" . $size_row['size'] . "</option>";
                }
            } else {
                echo "<option value=''>ไม่มีขนาดสินค้าพร้อมใช้งาน</option>";
            }
            ?>
        </select><br>

        <label for="Stock">จำนวน :</label>
        <input type="number" name="Stock" id="Stock" required><br>

        <?php
         if (isset($_SESSION['success_message'])) {
            echo "<p style='color: green;'>" . $_SESSION['success_message'] . "</p>";
            unset($_SESSION['success_message']); // ลบข้อความหลังจากแสดง
        }

        if (isset($_SESSION['error_message'])) {
            echo "<p style='color: red;'>" . $_SESSION['error_message'] . "</p>";
            unset($_SESSION['error_message']); // ลบข้อความหลังจากแสดง
        } ?>

        <input name="btnSubmit" type="submit" value="Submit">
    </form>

    <!-- Footer -->
    <?php
    renderFooter();
    ?>
</body>

</html>