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
        form input[type="number"],
        form select,
        form input[type="file"] {
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
    renderHeader($conn)
    ?>
    <form name="form1" method="post" action="upload2.php" enctype="multipart/form-data">
        <h2>เพิ่มสินค้า</h2>

        <label for="txtName">ชื่อ :</label>
        <input type="text" name="txtName" id="txtName" required><br>

        <label for="Price">ราคา :</label>
        <input type="number" name="Price" id="Price" required><br>

        <label for="Category">แบรนด์ :</label>
        <select name="Category" id="Category" required>
            <option value="" disabled selected></option>
            <option value="Nike">Nike</option>
            <option value="Adidas">Adidas</option>
            <option value="Puma">Puma</option>
            <option value="New Balance">New Balance</option>
        </select><br>

        <label for="Gender">เพศ :</label>
        <select name="Gender" id="Gender" required>
            <option value="" disabled selected></option>
            <option value="Men">ชาย</option>
            <option value="Women">หญิง</option>
        </select><br>
        <label for="txtDescription">รายละเอียดสินค้า:</label>
        <textarea name="txtDescription" id="txtDescription" rows="4"></textarea><br>

        <label for="filUpload1">รูปภาพ 1 :</label>
        <input type="file" name="filUpload1" id="filUpload1" required><br>

        <label for="filUpload2">รูปภาพ 2 :</label>
        <input type="file" name="filUpload2" id="filUpload2"><br>

        <label for="filUpload3">รูปภาพ 3 :</label>
        <input type="file" name="filUpload3" id="filUpload3"><br>

        <label for="filUpload4">รูปภาพ 4 :</label>
        <input type="file" name="filUpload4" id="filUpload4"><br>

        <input name="btnSubmit" type="submit" value="Submit">
    </form>

    <!-- Footer -->
    <?php
    renderFooter();
    ?>a
</body>

</html>