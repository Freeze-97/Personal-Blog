<?php
if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
    // session isn't started
    session_start();
}
require_once ('db.php');

$error = ''; // in case we have to output errors
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $title = $_POST['title'] ?? '';
    $presentation = $_POST['presentation'] ?? '';

    // Validate input from the user
    if($username === '') {
        $error = "Username must be provided.";
    }
    elseif($password === '') {
         $error = "Password must be provided.";
    }
    elseif($title === '') {
        $error = "You must provide a title for your blog account.";
    }
    elseif($presentation === '') {
        $error = "You must provide a presentation for your profile.";
    }
    elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    }
    elseif(get_user($username)) {
        $error = "Username is already in use.";
    }
    else { // User can be registred
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        add_user($username, $hashed, $title, $presentation);
        header("Location: login.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Log in</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php require_once('header.php'); ?>

<main>
    <div class="login-container">
    <h3>Register</h3>
    <form method="post" action="register.php">
        <label for="username">Username:</label><br>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" id="password"><br>

        <label for="title">Title:</label><br>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"><br>

        <label for="presentation">Presentation:</label><br>
        <input type="text" name="presentation" id="presentation" value="<?= htmlspecialchars($_POST['presentation'] ?? '') ?>"><br>

        <input type="submit" value="Register">
    </form>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
    </div>
</main>

<?php include_once ('footer.php'); ?>
</body>
</html>