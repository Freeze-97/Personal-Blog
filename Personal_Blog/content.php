<?php
require_once('db.php');

if ($post):
?>
    <h2><?= htmlspecialchars($post['title']) ?></h2>
    <p><em><?= htmlspecialchars($post['created']) ?> by <?= htmlspecialchars($post['username']) ?></em></p>
    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

    
    <?php 
    $img = get_image_by_post_id($post['id']); 
        if($img): ?>
        <div class="post-image-container">
            <img src="../uploads/<?= htmlspecialchars($img['filename']) ?>" width="500" alt="Content image"><br>
            <em><?= htmlspecialchars($img['description']) ?></em>
        </div>
        <?php endif; ?>

<?php else: ?>
    <p>Could not find post.</p>
<?php endif; ?>
