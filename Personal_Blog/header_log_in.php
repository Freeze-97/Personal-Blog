<header class="containter">
    <h1>My Blog</h1>
    <nav class="navbar">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="admin/index.php">Adminpanel</a></li>
            <li><a href="admin/logout.php">Logout</a></li>
        </ul>
    </nav>
    <p style="text-align:right; margin-right:20px;">
        Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> |
    </p>
</header>