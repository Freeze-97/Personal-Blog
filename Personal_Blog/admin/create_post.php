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

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $user_id = $_SESSION['user_id'];
    $image_path = null;

    if(empty($title) || empty($content)) {
        $error = "Title and content are required.";
    }
    else {
        if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $upload_dir = "../../uploads/";
            $original_filename = basename($_FILES['image']['name']); // Shows filename only not whole path
            $file_ext = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

            // Check if the file has a valid extension
            if(!in_array($file_ext, $allowed_extensions)) {
                $error = "Invalid file type. Only jpg, jpeg, png and gif are allowed.";
            }
            else {
                // Create unique names for the image files
                $unique_filename = uniqid('img_', true) . '.' . $file_ext;
                $target_path = $upload_dir . $unique_filename;

                if(move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image_path = $unique_filename;
                }
                else {
                    $error = "Failed to upload image.";
                }
            }
        }
    }

    if(empty($error)) {
        if(create_post($user_id, $title, $content, $image_path, $description)) {
            $success = "Post created succesfully.";
        }
        else {
            $error = "Failed to create post.";
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
<?php require_once 'admin_header.php';?>

<h3><a href="index.php" class="back-link">Go Back</a></h3>

<main class="form-panel">
    <div class="form-card">
        <h2>Create New Post</h2>

        <?php if ($error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php elseif ($success): ?>
            <p style="color:green;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <label>Title:</label><br>
            <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required><br><br>

            <label>Content</label><br>
            <textarea name="content" rows="10" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea><br><br>

            <label>Upload image (optional):</label><br>
            <input type="file" name="image" accept="image/*"><br><br>

            <label for="description">Image Description (optional):</label><br>
            <input type="text" name="description" id="description" value="<?= htmlspecialchars($_POST['description'] ?? '') ?>"><br><br>

            <input type="submit" value="Create post" class="form-submit">
        </form>
    </div>
</main>

<?php include_once ('../footer.php'); ?>
</body>
</html>