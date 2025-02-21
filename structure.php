<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<?php
function renderHeader($conn)
{
    echo '<head>';
    echo '<link rel="stylesheet" href="header.css">';
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';
    echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">';
    echo '</head>';

    // Top Bar
    echo '<div class="top-bar">';

    // เช็คว่าผู้ใช้ล็อกอินหรือยังโดยใช้ user_id
    if (isset($_SESSION['user_id'])) {
    }
    echo '<a href="aboutus.php">เกี่ยวกับเรา</a>';
    echo '<a href="policy.php">นโยบาย</a>';

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $query = mysqli_query($conn, "SELECT firstName, lastName FROM users WHERE user_id='$user_id'");
        $row = mysqli_fetch_assoc($query);
        echo '<span>สวัสดี คุณ ' . $row['firstName'] . '</span>';
        echo '<span class="separator" style="margin: 0 8px; color: #666; font-weight: normal;">|</span>';
        echo '<a href="logout.php" class="logout-btn">ออกจากระบบ</a>';
        echo '<a href="profile.php" class="cart-icon"><i class="fa fa-user"></i></a>';
    } else {
        echo '<a href="login.php" class="signin-btn">เข้าสู่ระบบ</a>';
    }
    echo '</div>';

    // Header
    echo '<header>';
    echo '<div class="logo">';
    echo '<a href="main.php" class="logo">';
    echo '<img src="assets/logo/Prime2.png" alt="Logo">';
    echo '</a>';
    echo '</div>';
    echo '<nav>';
    echo '<ul>';
    echo '<li><a href="main.php">หน้าแรก</a></li>';
    echo '<li><a href="upload3.php">สินค้า</a></li>';
    echo '<li><a href="Men.php">ผู้ชาย</a></li>';
    echo '<li><a href="Women.php">ผู้หญิง</a></li>';
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == 0) {
        echo '<li><a href="upload1.php">เพิ่มสินค้า</a></li>';
    }

    echo '</ul>';

    echo '</nav>';
    // เช็คว่าผู้ใช้ล็อกอินหรือยังโดยใช้ user_id
    if (isset($_SESSION['user_id'])) {

        echo '</div>';
    }
    echo '<div class="cart_section">';
    echo '<a href="cart.php" class="btn1" style="margin-left:1rem;margin-right:1.4rem;"><i class="fa-solid fa-bag-shopping"></i></i></a>'; // Cart icon


    echo '</header>';
}

function renderFooter()
{
    echo '
    <head>
        <link rel="stylesheet" href="footer.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    <style>
        * {
            font-family: \'Kanit\', \'serif\';
        }
    </style>
    <!-- Footer -->
    
    <footer class="site-footer">
    
        <div class="container" style="padding-top:40px;">
        <hr>
            <br><br>
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <h6>เกี่ยวกับเรา</h6>
                    <p class="text-justify">Prime.com ชีวิตที่ใช่ รองเท้าที่ชอบ Prime เป็นเว็บไซต์ที่มุ่งเน้นการขายรองเท้า Nike แท้ทุกแบบและทุกสไตล์ เพื่อให้คุณสามารถเลือกซื้อรองเท้าคู่โปรดได้ตามความต้องการ โดยมีทั้งรองเท้าสำหรับการเล่นกีฬาและการใช้งานในชีวิตประจำวัน เว็บไซต์ของเรามีการคัดสรรรองเท้าคุณภาพสูงจาก Nike ที่ได้รับความนิยมและตอบโจทย์ไลฟ์สไตล์ของทุกคน พร้อมทั้งการให้บริการที่สะดวก รวดเร็ว และปลอดภัยในการช้อปปิ้งออนไลน์ ทำให้คุณมั่นใจได้ว่าจะได้รองเท้า Nike แท้ในราคาที่คุ้มค่าและบริการที่ยอดเยี่ยม.</p>
                </div>

                <div class="col-xs-6 col-md-3">
                    <h6>หมวดหมู่</h6>
                    <ul class="footer-links">
                        <li><a href="upload3.php">สินค้า</a></li>
                        <li><a href="Men.php">ผู้ชาย</a></li>
                        <li><a href="Women.php">ผู้หญิง</a></li>
                    </ul>
                </div>

                <div class="col-xs-6 col-md-3">
                    <h6>ลิงก์ด่วน</h6>
                    <ul class="footer-links">
                        <li><a href="aboutus.php">เกี่ยวกับเรา</a></li>
                        <li><a href="contac.php">ติดต่อเรา</a></li>
                        <li><a href="policy.php">นโยบายความเป็นส่วนตัว</a></li>
                    </ul>
                </div>
            </div>
            <br>
            <hr>
        </div>
        
        <div class="container">
            <div class="row">
                 <div class="col-md-12 text-center">
                     <p class="copyright-text">ลิขสิทธิ์ &copy; 2025 สงวนลิขสิทธิ์โดย
                    <a href="main.php">Prime</a>.
                </p>
            </div>
        </div>
    </div>
    
</footer>';
}
?>
<style>
    ::-webkit-scrollbar {
        height: 5px;
        width: 5px;
    }

    ::-webkit-scrollbar-track {
        background: #fff;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(45deg, #000, #000);
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>