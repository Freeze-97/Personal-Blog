<?php
if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
    // session isn't started
    session_start();
}
require_once ('../db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$result = get_all_posts_image_by_user_id($user_id);
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Manage posts</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php require_once ('admin_header.php');?>

<h3><a href="index.php" class="back-link">Go Back</a></h3>

<main class="manage-posts-panel">
    <div class="manage-posts-card">
        <h2>Your Posts</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <ul class="post-list">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <li class="post-item">
                        <div class="post-header">
                            <strong><?= htmlspecialchars($row['title']) ?></strong>
                            <em>(<?= htmlspecialchars($row['created']) ?>)</em>
                        </div>

                        <?php if (!empty($row['filename'])): ?>
                            <div class="post-image">
                                <img src="../../uploads/<?= htmlspecialchars($row['filename']) ?>" alt="Image" class="img_small">
                            </div>
                        <?php endif; ?>

                        <div class="post-actions">
                            <a href="view_post.php?id=<?= $row['id'] ?>">View</a> |
                            <a href="edit_post.php?id=<?= $row['id'] ?>">Edit</a> |
                            <a href="delete_post.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="no-posts-message">You have not created any posts yet.</p>
        <?php endif; ?>
    </div>
</main>

<?php include_once ('../footer.php'); ?>
</body>
</html>