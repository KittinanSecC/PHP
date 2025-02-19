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
    <title>ร้านค้าอย่างเป็นทางการของ Prime Sneakers Store TH
    </title>
    <link rel="stylesheet" href="style2.css">
    <link href="assets/logo/Prime2.png" rel="icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!--animate.css-->
    <link rel="stylesheet" href="assets/css/animate.css">

    <!--owl.carousel.css-->
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/owl.theme.default.min.css">
    <!-- React & ReactDOM CDN -->
    <script src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script>
    <script src="assets/js/jquery.js"></script>
    <!--modernizr.min.js-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
    <!--bootstrap.min.js-->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- bootsnav js -->
    <script src="assets/js/bootsnav.js"></script>
    <!--owl.carousel.js-->
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!--Custom JS-->
    <script src="assets/js/custom.js"></script>
</head>

<body>
    <?php
    renderHeader($conn)
    ?>
    <div id="promo-banner">
        <p id="promo-text"></p>
    </div>
    <script>
        const promos = [
            "ยินดีต้อนรับสู่\nPrime Sneakers Store",
            "ชีวิตที่ใช่  รองเท้าที่ชอบ\nกับ Prime",
            "ชีวิตที่ใช่  รองเท้าที่ชอบ\nค่าส่ง 150 บาท ทุกคู่",
            "เหนือระดับ ทุกเสต็ป\nBy Prime Sneakers"
        ];

        let index = 0;

        function updatePromo() {
            document.getElementById("promo-text").textContent = promos[index];
            index = (index + 1) % promos.length;
        }

        setInterval(updatePromo, 5000); // เปลี่ยนข้อความทุก 3 วินาที
        updatePromo(); // แสดงข้อความแรกทันที
    </script>
    <!-- billbord -->
    <section id="video-background" class="video-container">
        <a href="upload3.php">
            <video autoplay loop muted playsinline class="video-background">
                <source src="assets/video/nike.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </a>
    </section>
    <!--brand strat -->
    <section id="brand" class="brand">
        <div class="container">
            <div class="brand-area">
                <div class="owl-carousel owl-theme brand-item">
                    <div class="item">
                        <a href="#">
                            <img src="assets/brand/nike.png" alt="brand-image" />
                        </a>
                    </div><!--/.item-->
                    <div class="item">
                        <a href="#">
                            <img src="assets/brand/adidas.png" alt="brand-image" />
                        </a>
                    </div><!--/.item-->
                    <div class="item">
                        <a href="#">
                            <img src="assets/brand/puma.png" alt="brand-image" />
                        </a>
                    </div>
                    <div class="item">
                        <a href="#">
                            <img src="assets/brand/nbb.png" alt="brand-image" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* ตั้งค่า container ของวีดีโอ */
        .video-container {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        /* วีดีโอขยายเต็มพื้นที่ */
        .video-background {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            /* ให้ลิงก์ครอบวิดีโอทั้งหมด */
        }
    </style>

    <!-- Hero Section -->
    <section class="hero">
        <img src="assets/font/fonts.png" alt="font">
        <!-- ปุ่มดูข้อมูล -->
        <a href="upload3.php" class="btn btn-dark" style="border-radius: 25px;">เลือกซื้อ</a>
    </section>

    <!DOCTYPE html>
    <html lang="th">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>รายการโปรดของคุณ</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body>
        <?php
        include("include.php"); // เชื่อมต่อฐานข้อมูล

        $query = "SELECT ID, Name, FilesName FROM product ORDER BY ID DESC LIMIT 10";
        $result = mysqli_query($conn, $query);
        ?>

        <div class="container mt-5">
            <h2 class="text-start">สินค้าล่าสุด</h2>
            <div class="row flex-nowrap overflow-auto" style="white-space: nowrap;">
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <a href="#" onclick="sendToSearch('<?= htmlspecialchars($row['Name']); ?>'); return false;" class="text-decoration-none text-dark">
                            <div class="card h-100 border-0">
                                <img src="myfile/<?= htmlspecialchars($row['FilesName']); ?>" class="card-img-top" alt="<?= htmlspecialchars($row['Name']); ?>" style="background-color:#FAFAFA;">
                                <div class="card-body text-start">
                                    <h5 class="card-title" style="font-size:large;"><?= htmlspecialchars($row['Name']); ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>

        <script>
            function sendToSearch(productName) {
                window.location.href = `upload3.php?filter=${encodeURIComponent(productName)}`;
            }
        </script>

        <!-- Hero Section -->
        <section class="hero">
            <h5 style="font-size: 100px; font-weight:bold;">ชีวิตที่ใช่ <br> รองเท้าที่ชอบ Prime</h5> <br>
            <!-- ปุ่มดูข้อมูล -->
            <a href="upload3.php" class="btn btn-dark" style="border-radius: 25px;">เลือกซื้อ</a>
        </section>

        <div class="container mt-5">
            <div class="row text-center">

                <div class="col">
                    <section class="hero">
                        <div class="hero-wrapper" style="background-color: #000;">
                            <img src="myfile/runm.png" alt="รองเท้าผู้ชาย">
                            <div class="overlay"></div>
                            <h5>รองเท้าผู้ชาย</h5>
                            <!-- Include the category as a GET parameter -->
                            <a href="upload3.php?category=Men" class="btn btn-dark btn-lg">เลือกซื้อ</a>
                        </div>
                    </section>
                </div>


                <div class="col">
                    <section class="hero">
                        <div class="hero-wrapper" style="background-color: #000;">
                            <img src="myfile/runf.jpg" alt="รองเท้าผู้หญิง">
                            <div class="overlay"></div>
                            <h5>รองเท้าผู้หญิง</h5>
                            <!-- Include the category as a GET parameter -->
                            <a href="upload3.php?category=Women" class="btn btn-dark btn-lg">เลือกซื้อ</a>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <?php
        include("include.php"); // เชื่อมต่อฐานข้อมูล

        $query = "SELECT ID, Name, FilesName FROM product ORDER BY ID ASC LIMIT 10";
        $result2 = mysqli_query($conn, $query);
        ?>

        <div class="container mt-5">
            <h2 class="text-start">สินค้ายอดนิยม</h2>
            <div class="row flex-nowrap overflow-auto" style="white-space: nowrap;">
                <?php while ($row = mysqli_fetch_assoc($result2)) { ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <a href="#" onclick="sendToSearch('<?= htmlspecialchars($row['Name']); ?>'); return false;" class="text-decoration-none text-dark">
                            <div class="card h-100 border-0">
                                <img src="myfile/<?= htmlspecialchars($row['FilesName']); ?>" class="card-img-top" alt="<?= htmlspecialchars($row['Name']); ?>" style="background-color:#FAFAFA;">
                                <div class="card-body text-start">
                                    <h5 class="card-title" style="font-size:large;"><?= htmlspecialchars($row['Name']); ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>

        <script>
            function sendToSearch(productName) {
                window.location.href = `upload3.php?filter=${encodeURIComponent(productName)}`;
            }
        </script>

    </body>

    </html>
    <!-- Footer -->
    <?php
    renderFooter();
    ?>


</body>

</html>