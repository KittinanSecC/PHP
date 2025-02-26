<?php
include("include.php"); // เชื่อมต่อฐานข้อมูล

$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

$sql = "SELECT * FROM product";

// เพิ่มตัวกรอง
if (!empty($filter)) {
    $sql .= " WHERE Name LIKE '%" . mysqli_real_escape_string($conn, $filter) . "%'";
}

// เพิ่มการเรียงลำดับ
if (!empty($sort)) {
    switch ($sort) {
        case 'price-asc':
            $sql .= " ORDER BY Price ASC";
            break;
        case 'price-desc':
            $sql .= " ORDER BY Price DESC";
            break;
        case 'name-asc':
            $sql .= " ORDER BY Name ASC";
            break;
        case 'name-desc':
            $sql .= " ORDER BY Name DESC";
            break;
    }
}

$result = mysqli_query($conn, $sql);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

// ส่งข้อมูลกลับในรูปแบบ JSON
header('Content-Type: application/json');
echo json_encode($products);

mysqli_close($conn);
?>
