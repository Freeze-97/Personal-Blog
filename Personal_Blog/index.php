<?php
if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
    // session isn't started
    session_start();
}
$is_logged_in = isset($_SESSION['username']); // check if user is logged in or guest
require_once('db.php');

$post_id = $_GET['post_id'] ?? null;

// Get id of post if it is set
if (!$post_id) {
    $posts = get_all_posts();
    $post = $posts[0] ?? null;
} else {
    $post = get_post_by_id($post_id);
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>My Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php
    if($is_logged_in) {
        require ('header_log_in.php'); 
    }
    else {
        require ('header.php'); 
    }
    ?>
    <main>
        <div class="main-layout">
            <div class="menu">
                <?php include_once 'menu.php'; ?>
            </div>
            <div class="content">
                <?php include_once 'content.php'; ?>
            </div>
            <div class="info">
                <?php include_once 'info.php'; ?>
            </div>
        </div>
    </main>

<?php include_once ('footer.php'); ?>
</body>
</html>