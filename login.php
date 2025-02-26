<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="login.css">
  <title>เข้าสู่ระบบของ Prime Sneakers Store TH</title>
    <link href="assets/logo/Prime2.png" rel="icon">
</head>

<body>
  <div class="container" id="signup" style="display:none">
    <h1 class="form-title">สมัครสมาชิก</h1>
    <form method="post" action="register.php" onsubmit="return validateSignup()">
      <div class="input-group">
        <input type="text" name="fName" id="fName" placeholder="ชื่อ*" required>
      </div>
      <div class="input-group">
        <input type="text" name="lName" id="lName" placeholder="นามสกุล*" required>
      </div>
      <div class="input-group">
        <input type="text" name="username" id="username" placeholder="Username*" required>
      </div>
      <div class="input-group">
        <input type="email" name="email" id="signupEmail" placeholder="อีเมล*" required>
      </div>
      <div class="input-group">
        <input type="password" name="password" id="signupPassword" placeholder="พาสเวิร์ด*" required>
        
      </div>
      <div class="input-group">
        <input type="password" id="confirmPassword" placeholder="ยืนยันพาสเวิร์ด*" required>
        
      </div>
      <p id="passwordError" style="color: red; display: none;">รหัสผ่านไม่ตรงกัน</p>

      <input type="submit" class="btn" value="สมัครสมาชิก" name="signUp">
    </form>

    <div class="validate"></div>

    <p class="or">
    </p>
    <div class="links">
      <p>มีบัญชีอยู่แล้วหรือ?</p>
      <button id="signInButton">เข้าสู่ระบบ</button>
    </div>
    <div class="links">
      <a href="main.php"><button>กลับหน้าหลัก</button></a>
    </div>
  </div>

  <?php session_start(); ?>
  <div class="container" id="signIn">
    <div class="container_img "><img src="assets\logo\Prime2.png"  alt="โลโก้เว็บไซต์" width="150px"></div>
    <h1 class="form-title">ยินดีต้อนรับ</h1>
    <h4 class="form-text" style="font-weight: 100;">เข้าสู่ระบบ Prime</h4>

    <form method="post" action="register.php">
      <div class="input-group">
        <input type="email" name="email" id="loginEmail" placeholder="อีเมล" required>
      </div>
      <div class="input-group">
        <input type="password" name="password" id="loginPassword" placeholder="พาสเวิร์ด" required>
        <i class="fa-solid fa-eye-slash togglePassword" data-target="loginPassword"></i>
      </div>
      <!-- Show error message if login fails -->
      <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red; margin-bottom: 1rem;"><?php echo $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); // Clear error after showing 
        ?>
      <?php endif; ?>
      <input type="submit" class="btn" value="เข้าสู่ระบบ" name="signIn">
    </form>

    <div class="links">
      <p>ยังไม่มีบัญชีใช่ไหม?</p>
      <button id="signUpButton">สมัครสมาชิก</button>
    </div>
    <div class="links">
      <a href="main.php"><button>กลับหน้าหลัก</button></a>
    </div>
  </div>

  <script src="login.js">
  </script>
</body>



</html>