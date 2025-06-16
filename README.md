# 💻 Personal Blog Project

This is a simple PHP-based personal blog system where users can create, edit, and delete blog posts with image uploads. It includes admin functionality, user authentication, and post management.

## 🧰 Requirements

- PHP 7.x or 8.x
- MySQL/MariaDB
- Apache (or any server with PHP support)
- Web browser

## ⚙️ Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/personal-blog.git
cd personal-blog
```

### 2. Create the Database
1. Open phpMyAdmin (or use MySQL CLI).
2. Create a new database, for example `blog`.
3. Import the structure using the provided `blog.sql` file:
In phpMyAdmin:
- Select your database
- Go to the Import tab
- Choose the `blog.sql` file
- Click Go

### 3. Update Database Credentials
Edit the file `db_credentials.php` to match your local database credentials:
```php
<?php
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'blog';
?>
```

## 🚀 How to Use
- Visit `index.php` in your browser (e.g. http://localhost/personal-blog/index.php)
- Register a new user
- Log in to view or manage posts
- Admin users can create, edit, or delete posts and uploaded images

## 🔐 Security Notice
- Never upload real credentials to a public repository.
- Add `db_credentials.php` and uploads/ to your `.gitignore` file.

