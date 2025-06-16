<?php
if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
    // session isn't started
    session_start();
}
require_once('../db.php');


if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$post_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];
$error = '';
$success = '';
$title = $content = $image_filename = $image_description = "";

// Get post by id and user id
if ($post_id) {
    $post_data = get_post_and_image($post_id, $user_id);
    if ($post_data) {
        $title = $post_data['title'];
        $content = $post_data['content'];
        $image_filename = $post_data['filename'];
        $image_description = $post_data['description'];
    } else {
        $error = "Post not found.";
    }
} else {
    $error = "No post ID provided.";
}

// Handle update of post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $new_title = trim($_POST['title'] ?? '');
    $new_content = $_POST['content'] ?? '';
    $new_description = trim($_POST['description'] ?? '');
    $upload_dir = "../../uploads/";
    $new_filename = $image_filename;

    if (empty($new_title) || empty($new_content)) {
        $error = "Title and content are required.";
    } else {
        // Image upload if it is set
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $original_filename = basename($_FILES['image']['name']);
            $ext = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION)); // Get extension type

            if (in_array($ext, $allowed_extensions)) { // See if extension is allowed
                $new_filename = uniqid('img_', true) . '.' . $ext; // Give filename unique name
                $target_path = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    // delete old image from disk if exists
                    if ($image_filename && file_exists($upload_dir . $image_filename)) {
                        unlink($upload_dir . $image_filename);
                    }
                } else {
                    $error = "Failed to upload new image.";
                }
            } else {
                $error = "Invalid file type.";
            }
        }

        if (empty($error)) {
            // Update post
            if(!update_post($post_id, $user_id, $new_title, $new_content)) {
                $error = "Error uploading post.";
            }

            // Update or insert image info
            if ($new_filename) {
                if(!upload_image($post_id, $new_filename, $new_description)) {
                    $error = "Error uploading image.";
                }
            }

            $success = "Post updated successfully.";
            // Update current data shown in form
            $title = $new_title;
            $content = $new_content;
            $image_filename = $new_filename;
            $image_description = $new_description;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Manage posts</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php require_once ('admin_header.php'); ?>
<h3><a href="manage_posts.php" class="back-link">Go Back</a></h3>

<main class="form-panel">
    <div class="form-card">
        <h2>Edit Post</h2>

        <?php if ($error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php elseif ($success): ?>
            <p style="color:green;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if (!empty($post_id) && empty($error)): ?>
        <form method="post" enctype="multipart/form-data">
            <label>Title:</label><br>
            <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required><br><br>

            <label>Content:</label><br>
            <textarea name="content" rows="10" required><?= htmlspecialchars($content) ?></textarea><br><br>

            <?php if ($image_filename): ?>
                <label>Current Image:</label><br>
                <img src="../../uploads/<?= htmlspecialchars($image_filename) ?>" style="max-width:300px;" alt="current_image"><br><br>
            <?php endif; ?>

            <label>Change Image (optional):</label><br>
            <input type="file" name="image" accept="image/*"><br><br>

            <label>Image Description:</label><br>
            <input type="text" name="description" value="<?= htmlspecialchars($image_description ?? '') ?>"><br><br>

            <input type="submit" value="Update Post" class="form-submit">
        </form>
        <?php endif; ?>
    </div>
</main>

<?php include_once ('../footer.php'); ?>
</body>
</html>