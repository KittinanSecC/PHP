<?php
session_start();
include("include.php"); // เชื่อมต่อฐานข้อมูล
include("structure.php");

// ตรวจสอบค่า ID ที่รับมา
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<h2>❌ ไม่พบสินค้า</h2>");
}
$id = intval($_GET['id']);

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$sql = "SELECT Name, Price, Gender, FilesName, FilesName2, FilesName3, FilesName4, Description FROM product WHERE ID = ?";
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

    echo "<script>alert('$message'); window.location.href='product.php?id=$id';</script>";
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

        .cart-btn {
            background-color: black;
            color: white;
            font-size: 1.1rem;
            border-radius: 8px;
            padding: 10px;
            border: none;
        }

        .cart-btn:hover {
            background-color: #000000;
        }

        .wishlist-btn {
            background-color: white;
            color: black;
            font-size: 1.1rem;
            border-radius: 8px;
            padding: 10px;
            border: 2px solid #ddd;
        }

        .wishlist-btn:hover {
            border-color: black;
        }

        .cart-btn i,
        .wishlist-btn i {
            margin-right: 5px;
        }

        .size-btn {
            border-color: black !important;
            /* กรอบดำ */
            background-color: white !important;
            /* พื้นหลังขาว */
            color: black !important;
            /* ตัวหนังสือดำ */

            transition: 0.3s;
        }

        .size-btn:hover {
            background-color: white !important;
            /* ไม่มีสีเทาเมื่อ hover */

        }

        .size-btn.active {
            background-color: black !important;
            /* เมื่อกด เปลี่ยนเป็นดำ */
            color: white !important;
            /* ตัวหนังสือขาว */
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
                                <img src="myfile/<?= htmlspecialchars($img) ?>" class="img-thumbnail mx-1 thumb-img" width="80">
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

                <div class="sizes my-3">
                    <label>เลือกไซส์:</label>
                    <div class="d-flex flex-wrap">
                        <?php foreach (["US 6", "US 6.5", "US 7", "US 7.5", "US 8", "US 8.5", "US 9", "US 9.5", "US 10", "US 10.5", "US 11", "US 12"] as $size) : ?>
                            <button class="btn size-btn m-1" data-size="<?= $size ?>"> <?= $size ?> </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $id ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['Name']) ?>">
                    <input type="hidden" name="product_price" value="<?= $product['Price'] ?>">
                    <input type="hidden" id="selected_size" name="product_size" value="">
                    <input type="hidden" name="product_image" value="myfile/<?= htmlspecialchars($product['FilesName']) ?>">

                    <button type="submit" class="btn cart-btn w-100 my-2 btn-dark">เพิ่มในตะกร้า</button>
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
                <div class="favorite-button">
                    <button id="favorite-btn" data-product-id="<?= $id ?>" class="favorite <?= $is_favorite ? 'active' : '' ?>">
                        <i id="heart-icon" class="<?= $is_favorite ? 'fa fa-heart' : 'fa-regular fa-heart' ?>"
                            style="color: <?= $is_favorite ? 'red' : 'gray' ?>;">
                        </i>
                        <span id="favorite-text"><?= $is_favorite ? 'ลบจากรายการโปรด' : 'เพิ่มในรายการโปรด' ?></span>
                    </button>

                </div>

                <div class="description">
                    <h4>รายละเอียดสินค้า</h4>
                    <p><?= nl2br(htmlspecialchars($product['Description'])) ?></p>
                </div>
            </div>


            <?php renderFooter() ?>

            <script>
                document.querySelectorAll('.thumb-img').forEach(img => {
                    img.addEventListener('click', function() {
                        document.getElementById('mainImage').src = this.src;
                        document.querySelectorAll('.thumb-img').forEach(img => img.classList.remove('active'));
                        this.classList.add('active');
                    });
                });
            </script>
            <script>
                document.querySelectorAll('.size-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        // ลบ active class จากปุ่มอื่น ๆ
                        document.querySelectorAll('.size-btn').forEach(btn => btn.classList.remove('active'));

                        // เพิ่ม active class ให้ปุ่มที่เลือก
                        this.classList.add('active');

                        // อัปเดตค่า input hidden
                        document.getElementById('selected_size').value = this.getAttribute('data-size');
                    });
                });

                // ตรวจสอบก่อนส่งฟอร์ม ว่าได้เลือกไซส์แล้วหรือไม่
                document.querySelector("form").addEventListener("submit", function(event) {
                    let selectedSize = document.getElementById("selected_size").value;
                    if (!selectedSize) {
                        alert("❌ โปรดเลือกไซส์ก่อนเพิ่มลงตะกร้า!");
                        event.preventDefault(); // ป้องกันการ submit
                    }
                });
            </script>
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
                                // เพิ่มในรายการโปรด -> ไอคอนสีแดง
                                heartIcon.classList.remove("fa-regular", "fa-heart");
                                heartIcon.classList.add("fa", "fa-heart");
                                heartIcon.style.color = "red";
                                favoriteText.textContent = "ลบจากรายการโปรด";
                                favoriteBtn.classList.add("active");
                            } else if (data.includes("❌")) {
                                // ลบออกจากรายการโปรด -> ไอคอนเส้นขอบสีเทา
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