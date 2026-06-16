<?php
session_start();

$conn = new mysqli("localhost","root","","blog_db");

if(isset($_POST['register'])){
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn->query("INSERT INTO users(username,password)
                  VALUES('$username','$password')");
}

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query(
        "SELECT * FROM users WHERE username='$username'"
    );

    $user = $result->fetch_assoc();

    if($user && password_verify($password,$user['password'])){
        $_SESSION['user_id']=$user['id'];
        $_SESSION['username']=$user['username'];
    }
}

if(isset($_POST['post'])){
    $title = $_POST['title'];
    $content = $_POST['content'];

    $user_id = $_SESSION['user_id'];

    $conn->query(
        "INSERT INTO posts(user_id,title,content)
         VALUES('$user_id','$title','$content')"
    );
}

if(isset($_GET['logout'])){
    session_destroy();
    header("Location:index.php");
}

if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM posts WHERE id=$id");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Full Stack Blog</title>

<style>
body{
    font-family:Arial;
    background:#f5f5f5;
    width:80%;
    margin:auto;
}
.container{
    background:white;
    padding:20px;
    margin-top:20px;
    border-radius:10px;
}
input,textarea{
    width:100%;
    padding:10px;
    margin:8px 0;
}
button{
    padding:10px 20px;
}
.post{
    border:1px solid #ddd;
    padding:15px;
    margin-top:15px;
}
</style>

</head>
<body>

<div class="container">

<h1>Full Stack Blog with Auth</h1>

<?php if(!isset($_SESSION['user_id'])){ ?>

<h2>Register</h2>

<form method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button name="register">Register</button>
</form>

<h2>Login</h2>

<form method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button name="login">Login</button>
</form>

<?php } else { ?>

<h2>Welcome, <?php echo $_SESSION['username']; ?></h2>

<a href="?logout=1">Logout</a>

<h2>Create Blog Post</h2>

<form method="POST">
<input type="text" name="title" placeholder="Post Title" required>

<textarea name="content"
placeholder="Write your blog..."
required></textarea>

<button name="post">Publish</button>
</form>

<?php } ?>

<h2>All Blog Posts</h2>

<?php

$result = $conn->query(
"SELECT posts.*,users.username
 FROM posts
 JOIN users ON posts.user_id=users.id
 ORDER BY created_at DESC"
);

while($row=$result->fetch_assoc()){
?>

<div class="post">

<h3><?php echo $row['title']; ?></h3>

<p><?php echo $row['content']; ?></p>

<small>
By <?php echo $row['username']; ?>
</small>

<?php
if(isset($_SESSION['user_id']) &&
$_SESSION['user_id']==$row['user_id']){
?>

<br><br>

<a href="?delete=<?php echo $row['id']; ?>">
Delete
</a>

<?php } ?>

</div>

<?php } ?>

</div>

</body>
</html>