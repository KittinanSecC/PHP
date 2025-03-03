<?php
session_start();
include("include.php"); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบค่า product_id ที่รับมา
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    die("<h2>❌ ไม่พบสินค้า</h2>");
}
$id = intval($_GET['product_id']);

// อัปเดตจำนวนการเข้าชม (view_count) +1
$update_view_sql = "UPDATE product SET view_count = view_count + 1 WHERE product_id = ?";
$stmt_update = $conn->prepare($update_view_sql);
$stmt_update->bind_param("i", $id);
$stmt_update->execute();
$stmt_update->close();

$view_count = isset($product['view_count']) ? $product['view_count'] : 0;


include("structure.php");
// ตรวจสอบค่า ID ที่รับมา
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    die("<h2>❌ ไม่พบสินค้า</h2>");
}
$id = intval($_GET['product_id']);

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$sql = "SELECT Name, Price, Gender, FilesName, FilesName2, FilesName3, FilesName4, Description, view_count FROM product WHERE product_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("เกิดข้อผิดพลาดใน SQL: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
if (!$product) {
    die("<h2>❌ ไม่พบสินค้า</h2>");
}

// ดึงข้อมูลไซส์จากฐานข้อมูล
$sql_sizes = "SELECT Size, Stock FROM product_sizes WHERE ProductID = ? ORDER BY Size ASC";
$stmt_sizes = $conn->prepare($sql_sizes);
$stmt_sizes->bind_param("i", $id);
$stmt_sizes->execute();
$result_sizes = $stmt_sizes->get_result();
$sizes = [];

while ($row = $result_sizes->fetch_assoc()) {
    $sizes[] = $row;
}


// จัดการรูปภาพ
$images = array_filter([$product['FilesName'], $product['FilesName2'], $product['FilesName3'], $product['FilesName4']]);

// แปลงค่าเพศเป็นข้อความภาษาไทย
$gender_text = ($product['Gender'] === 'Men') ? "รองเท้าผู้ชาย" : "รองเท้าผู้หญิง";

// เพิ่มฟังก์ชันการจัดการรายการโปรด
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['favorite'])) {
    if (!isset($_SESSION['favorites'])) {
        $_SESSION['favorites'] = [];
    }

    // ตรวจสอบว่าสินค้าอยู่ในรายการโปรดแล้วหรือไม่
    if (in_array($id, $_SESSION['favorites'])) {
        // หากมีอยู่แล้ว ให้ลบออก
        $_SESSION['favorites'] = array_diff($_SESSION['favorites'], [$id]);
        $message = "\u274c ลบสินค้าจากรายการโปรดเรียบร้อย!";
    } else {
        // หากยังไม่มี ให้เพิ่มเข้าไป
        $_SESSION['favorites'][] = $id;
        $message = "\u2714\ufe0f เพิ่มสินค้าในรายการโปรดเรียบร้อย!";
    }

    echo "<script>
    alert('$message');
    window.location.href = 'product.php?product_id=$id';
</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['Name']) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/logo/Prime2.png" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .main-image-container {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 1000px;
        }

        .main-image {
            max-width: 100%;
            height: auto;
        }

        .thumb-gallery img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            background-color: #FAFAFA;
            padding: 5px;
            border-radius: 8px;
            transition: 0.3s;
            cursor: pointer;
        }

        .thumb-gallery img:hover,
        .thumb-gallery img.active {
            filter: brightness(0.8);
        }

        .description {
            border-radius: 10px;
            margin-top: 20px;
            font-size: 1rem;
            color: black;
        }

        /* Center buttons vertically and wrap them */
        .sizes .d-flex {
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .size-btn {
            border-color: black !important;
            background-color: white;
            color: black !important;
            transition: 0.3s;
            display: inline-block;
            padding: 10px 20px;
            text-align: center;
            font-size: 14px;
            border: 2px solid #000 !important;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: calc(25% - 10px);
            /* Ensures 4 buttons per row */
            max-width: 120px;
            /* Maximum width to prevent stretching */
            margin-bottom: 10px;
            /* Space between rows */
        }

        .size-btn:hover {
            background-color: white !important;
        }

        .size-btn.active {
            background-color: black !important;
            color: white !important;
            border: 2px solid #fff;
            /* Change border when active */
        }


        .size-btn.disabled {
            pointer-events: none;
            border: 1px solid #ccc;
            background-color: rgb(226, 226, 226) !important;
            /* Light gray background for disabled */
            color: #a9a9a9;
            /* Darker gray text to make it look disabled */
        }

        .size-btn:not(.disabled):hover {
            background-color: #0056b3;
            color: white;
        }

        .favorite {
            background-color: transparent;
            border: none;
            color: gray;
            font-size: 1.2em;
            cursor: pointer;
        }

        .favorite .fa-heart {
            transition: color 0.3s ease;
        }

        .favorite.active .fa-heart {
            color: red;
        }

        /* Style for the disabled size button */
        .size-btn.disabled {
            background-color: grey;
            cursor: not-allowed;
            opacity: 0.6;
        }
    </style>
    <?php
    // ตรวจสอบว่าสินค้านี้อยู่ในรายการโปรดของผู้ใช้หรือไม่
    $is_favorite = false;
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $sql_check_fav = "SELECT * FROM favorites WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql_check_fav);
        $stmt->bind_param("ii", $user_id, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $is_favorite = $result->num_rows > 0;
    }
    ?>
</head>
<?php
renderHeader($conn);
?>
<script>
    // Function to change the large image
    function changeMainImage(thumbnail) {
        // Get the source of the clicked thumbnail
        var newSrc = thumbnail.src;

        // Change the source of the large image
        document.getElementById('mainImage').src = newSrc;

        // Optionally, you can update the alt text of the large image
        var altText = thumbnail.alt || thumbnail.title || thumbnail.getAttribute('data-title') || 'Product Image';
        document.getElementById('mainImage').alt = altText;
    }
</script>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="gallery">
                    <?php if (!empty($images)) : ?>
                        <div class="main-image-container">
                            <img id="mainImage" src="myfile/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($product['Name']) ?>" class="img-fluid main-image">
                        </div>
                        <div class="thumb-gallery mt-2 d-flex">
                            <?php foreach ($images as $img) : ?>
                                <img src="myfile/<?= htmlspecialchars($img) ?>" class="img-thumbnail mx-1 thumb-img" width="80" onclick="changeMainImage(this)">
                            <?php endforeach; ?>
                        </div>

                    <?php else : ?>
                        <p>ไม่มีภาพสินค้า</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-6">
                <h1><?= htmlspecialchars($product['Name']) ?></h1>
                <p class="text-muted"> <?= $gender_text ?> </p>
                <h3 class="text-danger">฿<?= number_format($product['Price']) ?></h3>

                <?php
                // จัดเรียงไซส์จากค่าน้อยไปมาก (รองรับไซส์ที่เป็นตัวเลขและตัวอักษร)
                usort($sizes, function ($a, $b) {
                    return strnatcmp($a['Size'], $b['Size']);
                });
                ?>
                <div class="sizes my-3">
                    <label>เลือกขนาด:</label>
                    <div class="d-flex flex-wrap justify-content-start">
                        <?php foreach ($sizes as $size) : ?>
                            <button class="btn size-btn m-1 <?= ($size['Stock'] == 0) ? 'disabled' : '' ?>"
                                data-size="<?= htmlspecialchars($size['Size']) ?>"
                                data-stock="<?= $size['Stock'] ?>">
                                <?= htmlspecialchars($size['Size']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <!-- เพิ่มแสดงจำนวนสินค้าคงเหลือ -->
                    <div id="stock-info" style="margin-top: 10px; font-weight: bold;"></div>
                </div>

                <script>
                    // เมื่อเลือกขนาด จะแสดงจำนวนสินค้าคงเหลือ
                    document.querySelectorAll('.size-btn:not(.disabled)').forEach(button => {
                        button.addEventListener('click', function() {
                            // ลบ class active จากทุกปุ่ม
                            document.querySelectorAll('.size-btn').forEach(btn => btn.classList.remove('active'));
                            // เพิ่ม class active ให้ปุ่มที่เลือก
                            this.classList.add('active');
                            // อัปเดตข้อมูลขนาดที่เลือก
                            document.getElementById('selected_size').value = this.getAttribute('data-size');

                            // แสดงข้อมูลจำนวนสินค้าคงเหลือ
                            const stock = this.getAttribute('data-stock');
                            const stockInfo = document.getElementById('stock-info');
                            if (stock > 0) {
                                stockInfo.textContent = "สินค้าคงเหลือ: " + stock + " ชิ้น";
                                stockInfo.style.color = "grey";
                            } else {
                                stockInfo.textContent = "สินค้าหมด";
                                stockInfo.style.color = "red";
                            }
                        });
                    });
                </script>

                <?php

                if (isset($_SESSION['error_message'])) {
                    echo "<p style='color: red;'>" . $_SESSION['error_message'] . "</p>";
                    unset($_SESSION['error_message']);  // Clear the message after displaying
                }
                ?>

                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $id ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['Name']) ?>">
                    <input type="hidden" name="product_price" value="<?= $product['Price'] ?>">
                    <input type="hidden" id="selected_size" name="product_size" value="">
                    <input type="hidden" name="product_image" value="myfile/<?= htmlspecialchars($product['FilesName']) ?>">

                    <button type="submit" class="btn cart-btn w-100 my-2 btn-dark p-2">เพิ่มในตะกร้า</button>
                </form>

                <script>
                    document.querySelectorAll('.size-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            document.querySelectorAll('.size-btn').forEach(btn => btn.classList.remove('active'));
                            this.classList.add('active');
                            document.getElementById('selected_size').value = this.getAttribute('data-size');
                        });
                    });
                </script>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="favorite-button">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button id="favorite-btn" data-product-id="<?= $id ?>" class="favorite <?= $is_favorite ? 'active' : '' ?>">
                                <i id="heart-icon" class="<?= $is_favorite ? 'fa fa-heart' : 'fa-regular fa-heart' ?>"
                                    style="color: <?= $is_favorite ? 'red' : 'gray' ?>;">
                                </i>
                                <span id="favorite-text"><?= $is_favorite ? 'ลบจากรายการโปรด' : 'เพิ่มในรายการโปรด' ?></span>
                            </button>
                        <?php else: ?>

                        <?php endif; ?>
                    </div>


                    <p class="mb-0"><i class="fa-regular fa-eye"></i> จำนวนการเข้าชม: <?= number_format($product['view_count']) ?> ครั้ง</p>
                </div>


                <div class="description">
                    <h4>รายละเอียดสินค้า</h4>
                    <p><?= nl2br(htmlspecialchars($product['Description'])) ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php renderFooter() ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const favoriteBtn = document.getElementById("favorite-btn");
            const heartIcon = document.getElementById("heart-icon");
            const favoriteText = document.getElementById("favorite-text");

            favoriteBtn.addEventListener("click", async function() {
                const productId = this.getAttribute("data-product-id");

                try {
                    const response = await fetch("toggle_favorite.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: `product_id=${encodeURIComponent(productId)}`,
                    });

                    const data = await response.text();
                    console.log(data);

                    if (data.includes("✅")) {
                        heartIcon.classList.remove("fa-regular", "fa-heart");
                        heartIcon.classList.add("fa", "fa-heart");
                        heartIcon.style.color = "red";
                        favoriteText.textContent = "ลบจากรายการโปรด";
                        favoriteBtn.classList.add("active");
                    } else if (data.includes("❌")) {
                        heartIcon.classList.remove("fa", "fa-heart");
                        heartIcon.classList.add("fa-regular", "fa-heart");
                        heartIcon.style.color = "gray";
                        favoriteText.textContent = "เพิ่มในรายการโปรด";
                        favoriteBtn.classList.remove("active");
                    }

                } catch (error) {
                    console.error("Error:", error);
                    alert("❌ ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้");
                }
            });
        });
    </script>
</body>

</html>