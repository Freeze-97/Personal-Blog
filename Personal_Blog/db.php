<?php
require_once('db_credentials.php');

// Connect to the database — we do this once when the script starts (when the page loads)
$connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

function add_user($username, $password, $title, $presentation)
{
    global $connection; // So we can access the global variable

    // Create the SQL query
    $sql = 'INSERT INTO user (username, password, title, presentation) VALUES (?,?,?,?)';

    // Prepare the query
    $statment = mysqli_prepare($connection, $sql);

    // Bind the variables to the statement — username and password are strings (s)
    mysqli_stmt_bind_param($statment, "ssss", $username, $password, $title, $presentation);

    // Execute the query
    mysqli_stmt_execute($statment);
   
    // Close the statement when we're done
    mysqli_stmt_close($statment);
}

/**
 * Takes in a statement that has been executed, fetches the result, and puts
 * the result into an array of rows, where each row contains an array of fields
 */
function get_result($statment)
{
    $rows = array();
    $result = mysqli_stmt_get_result($statment);
    if($result) // If there is a result
    {
        // Fetch row by row from the result and put into $row
        while ($row = mysqli_fetch_assoc($result))
        {
            $rows[] = $row;
        }
    }
    return $rows;
}

function get_users()
{
    global $connection;
    $sql = 'SELECT * FROM user';
    $statment = mysqli_prepare($connection, $sql);
    mysqli_stmt_execute($statment);
    $result = get_result($statment);
    mysqli_stmt_close($statment);
    return $result;
}



function get_user($username)
{
    global $connection;

    $sql = 'SELECT id, username, password FROM user WHERE username=?';
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    // Bind result variables
    mysqli_stmt_bind_result($stmt, $id, $fetched_username, $hashed_password);

    if (mysqli_stmt_fetch($stmt)) {
        // Build and return associative array
        return [
            'id' => $id,
            'username' => $fetched_username,
            'password' => $hashed_password
        ];
    } else {
        return null;
    }

    mysqli_stmt_close($stmt);
}

function get_password($id)
{
    global $connection;
    $sql = 'SELECT password FROM user WHERE id=?';
    $statment = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($statment, "s", $id);
    mysqli_stmt_execute($statment);
    $result = get_result($statment);
    mysqli_stmt_close($statment);
    return $result;
}

function get_images($id)
{
    global $connection;
    $sql = 'SELECT image.filename, image.description FROM image JOIN post ON image.postId=post.id WHERE post.userId=?';
    $statment = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($statment, "i", $id);
    mysqli_stmt_execute($statment);
    $result = get_result($statment);
    mysqli_stmt_close($statment);
    return $result;
}


function change_avatar($filename, $id)
{
    global $connection;
    $sql = 'UPDATE user SET image=? WHERE id=?';
    $statment = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($statment, "si", $filename, $id);
    $result = mysqli_stmt_execute($statment);
    mysqli_stmt_close($statment);
    return $result;
}

function delete_post($id)
{
    global $connection;
    $sql = 'DELETE FROM post WHERE id=?';
    $statment = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($statment, "i", $id);
    $result = mysqli_stmt_execute($statment);
    mysqli_stmt_close($statment);
    return $result;
}

// Get all posts and the username +  image
function get_all_posts() {
    global $connection;
    $sql = 'SELECT post.id, post.title, post.content, post.userId, DATE_FORMAT(post.created, "%Y-%m-%d %H:%i") AS created, 
            user.username, user.image
        FROM post
        JOIN user ON post.userId = user.id
        ORDER BY post.created DESC';
    $statement = mysqli_prepare($connection, $sql);
    mysqli_stmt_execute($statement);
    $result = get_result($statement);
    mysqli_stmt_close($statement);
    return $result;
}

// Get spcific post based on post id
function get_post_by_id($post_id) {
    global $connection;
        $sql = 'SELECT post.id, post.title, post.content, post.userId, DATE_FORMAT(post.created, "%Y-%m-%d %H:%i") AS created, 
            user.username, user.image
        FROM post
        JOIN user ON post.userid = user.id
        WHERE post.id = ?';
    $statement = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($statement, "i", $post_id);
    mysqli_stmt_execute($statement);
    $result = get_result($statement);
    mysqli_stmt_close($statement);
    return $result[0] ?? null; // Return first post or just null 
}

function get_image_by_post_id($post_id) {
    global $connection;
    $sql = 'SELECT filename, description FROM image WHERE postId = ?';
    $statement = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($statement, "i", $post_id);
    mysqli_stmt_execute($statement);
    $result = get_result($statement);
    mysqli_stmt_close($statement);
    return $result[0] ?? null;
}


function get_user_by_id($user_id) {
    global $connection;
    
    $sql = 'SELECT * FROM user WHERE id = ?';
    $stmt = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result); // Gets one user row as assoc array

    mysqli_stmt_close($stmt);

    return $user;
}

function create_post($userId, $title, $content, $filename = null, $description = '') {
    global $connection;

    $stmt = mysqli_prepare($connection, "INSERT INTO post (userId, title, content, created) VALUES (?, ?, ?, NOW())");
    mysqli_stmt_bind_param($stmt, "iss", $userId, $title, $content);
    mysqli_stmt_execute($stmt);
    $postId = mysqli_insert_id($connection); // Get post_id
    mysqli_stmt_close($stmt);

    if ($filename) {
        $stmt = mysqli_prepare($connection, "INSERT INTO image (filename, description, created, postid) VALUES (?, ?, NOW(), ?)");
        mysqli_stmt_bind_param($stmt, "ssi", $filename, $description, $postId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    return true;
}

function get_user_posts($user_id) {
    global $connection;

    $sql = "SELECT post.id, post.title, post.created, image.filename
            FROM post
            LEFT JOIN image ON post.id = image.postid
            WHERE post.user_id = ?
            ORDER BY post.created DESC";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function get_post_user_image_by_post_id($post_id) {
    global $connection;

    // Get post and possible image if there is one
    $sql = "SELECT post.id, post.title, post.content, post.created, user.username, image.filename AS image, image.description AS image_desc
        FROM post
        JOIN user ON post.userId = user.id
        LEFT JOIN image ON post.id = image.postId
        WHERE post.id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $post = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $post;
}

function get_all_posts_image_by_user_id($user_id) {
    global $connection;

    // Get all posts by the user who is logged in based on user_id
    $query = "SELECT post.id, post.title, post.created, image.filename
          FROM post
          LEFT JOIN image ON post.id = image.postid
          WHERE post.userId = ?
          ORDER BY post.created DESC";

    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function get_post_and_image($post_id, $user_id) {
    global $connection;

    $sql = "SELECT post.title, post.content, image.filename, image.description
            FROM post
            LEFT JOIN image ON post.id = image.postid
            WHERE post.id = ? AND post.userId = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $post_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $title, $content, $filename, $description);

    $result = null;
    if (mysqli_stmt_fetch($stmt)) {
        $result = [
            'title' => $title,
            'content' => $content,
            'filename' => $filename,
            'description' => $description
        ];
    }

    mysqli_stmt_close($stmt);
    return $result;
}

function update_post($post_id, $user_id, $title, $content) {
    global $connection;

    $stmt = mysqli_prepare($connection, "UPDATE post SET title = ?, content = ? WHERE id = ? AND userId = ?");
    mysqli_stmt_bind_param($stmt, 'ssii', $title, $content, $post_id, $user_id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

function upload_image($post_id, $filename, $description) {
    global $connection;

    // See if image already exists for this post
    $check_stmt = mysqli_prepare($connection, "SELECT id FROM image WHERE postid = ?");
    mysqli_stmt_bind_param($check_stmt, 'i', $post_id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);
    $has_image = mysqli_stmt_num_rows($check_stmt) > 0;
    mysqli_stmt_close($check_stmt);

    if ($has_image) { // Replace image
        $stmt = mysqli_prepare($connection, "UPDATE image SET filename = ?, description = ?, created = NOW() WHERE postid = ?");
        mysqli_stmt_bind_param($stmt, 'ssi', $filename, $description, $post_id);
    } else { // New image
        $stmt = mysqli_prepare($connection, "INSERT INTO image (filename, description, created, postid) VALUES (?, ?, NOW(), ?)");
        mysqli_stmt_bind_param($stmt, 'ssi', $filename, $description, $post_id);
    }

    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}