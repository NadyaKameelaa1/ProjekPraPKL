<?php
session_start();
require_once '../Koneksi/koneksi.php';

$email = mysqli_real_escape_string($koneksi, $_POST['email_user']);
$password = $_POST['password_user'];

$sql = "SELECT * FROM users WHERE email_user = '$email'";
$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: login.php?error=Email tidak terdaftar");
    exit;
}

$user = mysqli_fetch_assoc($result);

$input_hash = md5($password);
$db_hash = $user['password_user'];

echo "Input Hash: $input_hash<br>";
echo "DB Hash: $db_hash<br>";

if ($input_hash === $db_hash) {
    $_SESSION['email_user'] = $user['email_user'];
    $_SESSION['name_user'] = $user['name_user'];
    header("Location: dashboard.php");
    exit;
} else {

    if ($password === $user['password_user']) { 
        $_SESSION['email_user'] = $user['email_user'];
        $_SESSION['name_user'] = $user['name_user'];
        header("Location: dashboard.php");
        exit;
    } else {
        header("Location: login.php?error=Password salah!");
        exit;
    }

}


?>