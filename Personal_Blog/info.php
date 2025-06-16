<?php
if (!isset($post) || !$post) {
    echo "<p>No blog information to show</p>";
    return;
}

$user_id = $post['userId'];

if (!$user_id) {
    echo "<p>No user assigned to this post.</p>";
    return;
}

$user = get_user_by_id($user_id);

if ($user):
?>
    <div class="blogger-info">
        <h3>Blogger: <?= htmlspecialchars($user['username']) ?></h3>

        <?php if (!empty($user['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($user['image']) ?>" width="150">
        <?php endif; ?>

        <p><strong>Title: <br><?= htmlspecialchars($user['title']) ?></strong></p>
        <p>Presentation: <br><?= nl2br(htmlspecialchars($user['presentation'])) ?></p>
    </div>
<?php endif; ?>