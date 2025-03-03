<?php
// เชื่อมต่อฐานข้อมูล
include("include.php");

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // รับค่าจากฟอร์ม
    $product_id = intval($_POST['txtName']); // product_id ที่เลือกจาก dropdown
    $size = $_POST['txtSize']; // ขนาดที่เลือก
    $stock = intval($_POST['Stock']); // จำนวนสินค้าที่เพิ่ม

    // ตรวจสอบว่าเป็นค่าที่ถูกต้อง
    if ($product_id && $size && $stock > 0) {

        // อัปเดตจำนวนสินค้าในตาราง product_sizes
        $sql = "UPDATE product_sizes SET Stock = Stock + ? WHERE ProductID = ? AND Size = ?";

        // ตรวจสอบว่า $conn สามารถเตรียมคำสั่ง SQL ได้หรือไม่
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iis", $stock, $product_id, $size);

            if ($stmt->execute()) {
                // ถ้าอัปเดตสำเร็จ
                $_SESSION['success_message'] = "เพิ่มจำนวนสินค้าเรียบร้อยแล้ว";
            } else {
                // ถ้าเกิดข้อผิดพลาด
                $_SESSION['error_message'] = "ไม่สามารถเพิ่มจำนวนสินค้าได้: " . $stmt->error;
            }

            // ปิดการเชื่อมต่อฐานข้อมูล
            $stmt->close();
        } else {
            // ถ้าไม่สามารถเตรียมคำสั่ง SQL ได้
            $_SESSION['error_message'] = "ไม่สามารถเตรียมคำสั่ง SQL ได้: " . $conn->error;
        }
    } else {
        $_SESSION['error_message'] = "ข้อมูลไม่ครบถ้วนหรือไม่ถูกต้อง";
    }

    // ส่งกลับไปยังหน้าที่ต้องการแสดงผล
    header("Location: Stocking.php"); // เปลี่ยนเป็นหน้าที่คุณต้องการ
    exit();
}
