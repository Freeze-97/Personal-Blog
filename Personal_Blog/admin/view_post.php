<?php
if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
    // session isn't started
    session_start();
}
require_once('../db.php');

$post_id = $_GET['id'] ?? null;

if(!$post_id || !is_numeric($post_id)) {
    echo "<p>Invalid post ID</p>";
    exit;
}

$post = get_post_user_image_by_post_id($post_id);

if (!$post) {
    echo "<p>Post not found.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>View post</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php require_once ('admin_header.php');?>

<h3><a href="manage_posts.php" class="back-link">Go Back</a></h3>

<main>
    <div class="view-post-container">
        <article class="view-post-card">
            <h2><?= htmlspecialchars($post['title']) ?></h2>
            <p class="meta">By <?= htmlspecialchars($post['username']) ?> on <?= htmlspecialchars($post['created']) ?></p>
            
            <?php if (!empty($post['image'])): ?>
                <img src="../../uploads/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['image_desc']) ?>" class="img_small">
                <small><?= htmlspecialchars($post['image_desc']) ?></small>
            <?php endif; ?>

            <div class="post-content">
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
            </div>
        </article>
    </div>
</main>
<?php include_once ('../footer.php'); ?>
</body>
</html>