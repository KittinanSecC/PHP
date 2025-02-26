<?php
session_start();
include 'include.php';

if (isset($_POST['signUp'])) {
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password = md5($password);

    // ตรวจสอบว่าอีเมลนี้มีอยู่แล้วหรือไม่
    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "อีเมลนี้ถูกใช้งานแล้ว";
        header("Location: login.php");
        exit();
    } else {
        $insertQuery = "INSERT INTO users(firstName, lastName, username, email, password)
                       VALUES ('$firstName', '$lastName', '$username', '$email', '$password')";
        if ($conn->query($insertQuery) === TRUE) {
            // ดึง user_id ล่าสุดที่เพิ่งสมัคร
            $user_id = $conn->insert_id;
            $_SESSION['user_id'] = $user_id; // เซ็ต session user_id

            header("Location: main.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password = md5($password);

    $sql = "SELECT user_id FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        session_start();
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id']; // เก็บ user_id แทนอีเมล
        header("Location: main.php");
        exit();
    } else {
        $_SESSION['error'] = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
        header("Location: login.php");
        exit();
    }
}
