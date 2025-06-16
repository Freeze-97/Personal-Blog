<?php
if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
    // session isn't started
    session_start();
}
require_once('../db.php');
require_once ('admin_header.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_posts.php");
    exit;
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Make sure the post belongs to the logged-in user
$sql = "SELECT * FROM post WHERE id = ? AND userId = ?";
$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "ii", $post_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$post) {
    header("Location: manage_posts.php");
    exit;
}

// Check if the post has an image
$sql_img = "SELECT * FROM image WHERE postId = ?";
$stmt_img = mysqli_prepare($connection, $sql_img);
mysqli_stmt_bind_param($stmt_img, "i", $post_id);
mysqli_stmt_execute($stmt_img);
$result_img = mysqli_stmt_get_result($stmt_img);
$image = mysqli_fetch_assoc($result_img);
mysqli_stmt_close($stmt_img);

if ($image) {
    $image_path = "../../uploads/" . $image['filename'];
    
    // Delete image file from filesystem
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    // Delete image row from database
    $sql_delete_img = "DELETE FROM image WHERE id = ?";
    $stmt_delete_img = mysqli_prepare($connection, $sql_delete_img);
    mysqli_stmt_bind_param($stmt_delete_img, "i", $image['id']);
    mysqli_stmt_execute($stmt_delete_img);
    mysqli_stmt_close($stmt_delete_img);
}

// Delete the post
$sql_delete_post = "DELETE FROM post WHERE id = ? AND userId = ?";
$stmt_post = mysqli_prepare($connection, $sql_delete_post);
mysqli_stmt_bind_param($stmt_post, "ii", $post_id, $user_id);
mysqli_stmt_execute($stmt_post);
mysqli_stmt_close($stmt_post);

header("Location: manage_posts.php");
exit;
?>