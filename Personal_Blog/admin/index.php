<?php
if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
    // session isn't started
    session_start();
}
if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Adminpanel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include 'admin_header.php'; ?>

<main class="admin-panel">
    <div class="admin-card">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>What would you like to do?<p>
        <div class="admin-actions">
            <a href="../index.php" class="admin-btn">Go to the homepage</a>
            <a href="create_post.php" class="admin-btn">Create a new post</a>
            <a href="manage_posts.php" class="admin-btn">Handle posts</a>
            <a href="logout.php" class="admin-btn logout">Log out</a>
        </div>
    </div>
</main>

<?php include_once ('../footer.php'); ?>
</body>
</html>