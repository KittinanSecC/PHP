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
    <title>สินค้า Prime Sneakers Store</title>
    <link href="assets/logo/Prime2.png" rel="icon">
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="style4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- script ---->
    <style>
        /* Filter and Sort */
        .filter-sort {
            margin: 20px;
            text-align: center;
        }

        #sort-options {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <?php renderHeader($conn); ?>

    <div class="container mt-4 center">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="sidebar p-3 bg-light">
                    <h5>ตัวกรองสินค้า</h5>
                    <hr>
                    <form method="GET">
                        <!-- Search Box -->
                        <div class="mb-3">
                            <label class="form-label">ค้นหาสินค้า</label>
                            <input type="text" name="filter" class="form-control" placeholder="พิมพ์ชื่อสินค้า..." value="<?= isset($_GET['filter']) ? $_GET['filter'] : ''; ?>">
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label class="form-label">หมวดหมู่</label>
                            <select name="category" class="form-select">
                                <option value="">ทั้งหมด</option>
                                <option value="Men" <?= isset($_GET['category']) && $_GET['category'] == 'Men' ? 'selected' : ''; ?>>รองเท้าผู้ชาย</option>
                                <option value="Women" <?= isset($_GET['category']) && $_GET['category'] == 'Women' ? 'selected' : ''; ?>>รองเท้าผู้หญิง</option>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label">ช่วงราคา</label>
                            <input type="number" name="min_price" class="form-control mb-2" placeholder="ราคาต่ำสุด" value="<?= isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>">
                            <input type="number" name="max_price" class="form-control" placeholder="ราคาสูงสุด" value="<?= isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-dark w-100">กรองสินค้า</button>
                    </form>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-md-9">
                <div class="row">
                    <?php
                    include("include.php");
                    $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
                    $category = isset($_GET['category']) ? $_GET['category'] : '';
                    $min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
                    $max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
                    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'name-asc';

                    $strSQL = "SELECT * FROM product WHERE 1=1";
                    if ($filter) {
                        $strSQL .= " AND Name LIKE '%$filter%'";
                    }
                    if ($category) {
                        $strSQL .= " AND Gender = '$category'";
                    }
                    if ($min_price !== '' && $max_price !== '') {
                        $strSQL .= " AND Price BETWEEN $min_price AND $max_price";
                    }

                    // Sorting
                    switch ($sort) {
                        case 'name-asc':
                            $strSQL .= " ORDER BY Name ASC";
                            break;
                        case 'name-desc':
                            $strSQL .= " ORDER BY Name DESC";
                            break;
                        case 'price-asc':
                            $strSQL .= " ORDER BY Price ASC";
                            break;
                        case 'price-desc':
                            $strSQL .= " ORDER BY Price DESC";
                            break;
                        default:
                            $strSQL .= " ORDER BY Name ASC";
                    }

                    $objQuery = mysqli_query($conn, $strSQL) or die("Error Query [" . $strSQL . "]");

                    while ($objResult = mysqli_fetch_array($objQuery)) {
                    ?>
                        <div class="col-md-4 mb-3 col-sm-6">
                            <a href="product-detail.php?id=<?= $objResult['ID']; ?>" class="text-decoration-none text-dark">
                                <div class="card h-100" style="border: 0px; border-radius:0px;">
                                    <img src="myfile/<?= htmlspecialchars($objResult["FilesName"]); ?>" style="background-color:#FAFAFA;" class="card-img-top" alt="<?= htmlspecialchars($objResult["Name"]); ?>">
                                    <div class="card-body text-start">
                                        <h5 class="card-title" style="font-size:small;"><?= htmlspecialchars($objResult["Name"]); ?></h5>
                                        <p class="card-text" style="font-size:small;"><?= $objResult["Gender"] == "Men" ? "รองเท้าผู้ชาย" : "รองเท้าผู้หญิง"; ?></p>
                                        <p class="card-text" style="font-size:small;">฿<?= number_format($objResult["Price"]); ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php renderFooter(); ?>
</body>


</html>