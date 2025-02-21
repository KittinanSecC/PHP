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
    <title>ส่งข้อมูลสินค้าอย่างเป็นทางการของ Prime Sneakers Store</title>
    <link href="assets/logo/Prime2.png" rel="icon">
    <link rel="stylesheet" href="style2.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <style>
        .center-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* ให้เต็มหน้าจอ */
            text-align: center;
            flex-direction: column; /* จัดให้อยู่กึ่งกลางแนวตั้ง */
        }
    </style>
</head>

<body>
    <?php
    renderHeader($conn);
    
    // 1️⃣ เชื่อมต่อฐานข้อมูล
    $servername = "localhost";
    $username = "root";  // หรือที่ตั้งค่าไว้
    $password = "";       // ใส่รหัสผ่านถ้ามี
    $dbname = "prime";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // 2️⃣ รับค่าจากฟอร์ม
    $name = $_POST['txtName'];
    $price = $_POST['Price'];
    $category = $_POST['Category'];
    $gender = $_POST['Gender'];
    $description = $conn->real_escape_string($_POST['txtDescription']); // ป้องกัน SQL Injection

    // 3️⃣ กำหนดโฟลเดอร์อัปโหลด
    $uploadDir = "myfile/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
    }

    // 4️⃣ อัปโหลดไฟล์รูป
    $files = ['filUpload1', 'filUpload2', 'filUpload3', 'filUpload4'];
    $filePaths = [];

    foreach ($files as $fileKey) {
        if ($_FILES[$fileKey]['error'] === 0) {
            $fileName = basename($_FILES[$fileKey]['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath)) {
                $filePaths[$fileKey] = $fileName;
            } else {
                $filePaths[$fileKey] = NULL;
            }
        } else {
            $filePaths[$fileKey] = NULL;
        }
    }

    // 5️⃣ SQL บันทึกข้อมูลลงฐานข้อมูล
    $sql = "INSERT INTO product (Name, Price, Category, Gender, Description, FilesName, FilesName2, FilesName3, FilesName4)
            VALUES ('$name', '$price', '$category', '$gender', '$description',
                    '{$filePaths['filUpload1']}', '{$filePaths['filUpload2']}', '{$filePaths['filUpload3']}', '{$filePaths['filUpload4']}')";

    ?>

    <div class="center-container">
        <?php
        if ($conn->query($sql) === TRUE) {
            echo "<h2>เพิ่มข้อมูลสำเร็จ!</h2>";
        } else {
            echo "<h2>Error: " . $sql . "<br>" . $conn->error . "</h2>";
        }
        ?>
        <br>
        <a href="Upload3.php">View files</a>
    </div>

    <?php
    $conn->close();
    renderFooter();
    ?>
</body>

</html>
