<?php
if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
    // session isn't started
    session_start();
}
require_once('db.php');

$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['login_username'] ?? '');
    $password = trim($_POST['login_password'] ?? '');

    // Validate user input
     if($username === '') {
        $error = "Username must be provided.";
    }
    elseif($password === '') {
         $error = "Password must be provided.";
    }
    else {
        $user = get_user($username);
        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
             header("Location:admin/index.php");
             exit;
        }
        else {
            $error = "Incorrect username or passwrod.";
        }
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
    <h3>Log in:</h3>
    <form method="post" action="login.php">
        <label for="username">Username:</label><br>
        <input type="text" name="login_username" id="login_username" value="<?= htmlspecialchars($_POST['login_username'] ?? '') ?>"><br>

        <label for="password">Password:</label><br>
        <input type="password" name="login_password" id="login_password"><br>

        <input type="submit" value="Log in">
    </form>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
    </div>
</main>

<?php require_once ('footer.php'); ?>
</body>
</html>