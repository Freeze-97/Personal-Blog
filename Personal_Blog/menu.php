<?php 
    require_once ('db.php');
    $posts = get_all_posts(); // Function from db.php
?>

<h2>Latest posts:</h2>
<ul>
    <?php foreach($posts as $item): ?>
    <li>
        <a href="index.php?post_id=<?= htmlspecialchars($item['id']) ?>">
            <?= htmlspecialchars($item['title']) ?> (<?= $item['created'] ?>)
        </a>
    </li>
    <?php endforeach; ?>

    <?php
    if(empty($posts)) {
        echo "No posts available.";
    }
    ?>
</ul>