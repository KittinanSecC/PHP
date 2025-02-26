<?php
session_start();
include("include.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $firstName = htmlspecialchars($_POST['firstName'], ENT_QUOTES, 'UTF-8');
    $lastName = htmlspecialchars($_POST['lastName'], ENT_QUOTES, 'UTF-8');
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');

    if (!empty($_FILES['profile_img']['name'])) {
        $target_dir = "myfile/";
        $profile_img = basename($_FILES["profile_img"]["name"]);
        $target_file = $target_dir . $profile_img;

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($file_type, $allowed_types)) {
            // 1. Get the old profile image filename
            $get_old_image_sql = "SELECT profile_img FROM users WHERE user_id = ?";
            $get_old_image_stmt = $conn->prepare($get_old_image_sql);
            $get_old_image_stmt->bind_param("i", $user_id);
            $get_old_image_stmt->execute();
            $get_old_image_result = $get_old_image_stmt->get_result();

            if ($get_old_image_result->num_rows > 0) {
                $row = $get_old_image_result->fetch_assoc();
                $old_profile_img = $row['profile_img'];
            }
            $get_old_image_stmt->close();


            if (move_uploaded_file($_FILES["profile_img"]["tmp_name"], $target_file)) {
                $sql = "UPDATE users SET firstName = ?, lastName = ?, username = ?, email = ?, profile_img = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssi", $firstName, $lastName, $username, $email, $profile_img, $user_id);

                if ($stmt->execute()) {
                    // 2. Delete the old image if it exists and is different from the new one
                    if (!empty($old_profile_img) && $old_profile_img != $profile_img && file_exists($target_dir . $old_profile_img)) {
                        unlink($target_dir . $old_profile_img);
                    }
                    echo "Profile updated successfully!";
                    header("Location: profile.php");
                    exit();
                } else {
                    echo "Error updating profile: " . $stmt->error;
                }

            } else {
                echo "Error uploading file.";
                exit();
            }
        } else {
            echo "Invalid file type.";
            exit();
        }
    } else {
        $sql = "UPDATE users SET firstName = ?, lastName = ?, username = ?, email = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $firstName, $lastName, $username, $email, $user_id);

        if ($stmt->execute()) {
            echo "Profile updated successfully!";
            header("Location: profile.php");
            exit();
        } else {
            echo "Error updating profile: " . $stmt->error;
        }
    }
}
?>