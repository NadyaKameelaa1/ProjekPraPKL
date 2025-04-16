<?php
session_start();
require_once '../Koneksi/koneksi.php';

$email = $_SESSION['email_user'];
$old_pass = md5($_POST['old_password']);
$new_pass = $_POST['new_password'];
$confirm_pass = $_POST['confirm_password'];

// Validasi konfirmasi password
if ($new_pass != $confirm_pass) {
    $_SESSION['error'] = "Konfirmasi password salah";
    header("Location: profil.php");
    exit;
}

// Verifikasi password lama
$sql = "SELECT password_user FROM users WHERE email_user = '$email' AND password_user = '$old_pass'";
$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "Password lama salah";
    header("Location: profil.php");
    exit;
}

// Update password baru (hash password sebelum disimpan)
$hashed_password = md5($new_pass);
$update_sql = "UPDATE users SET password_user = '$hashed_password' WHERE email_user = '$email'";
mysqli_query($koneksi, $update_sql);

$_SESSION['success'] = "Password berhasil diubah";
header("Location: profil.php");
exit;

// // Update password baru
// $update_sql = "UPDATE users SET password_user = '$new_pass' WHERE email_user = '$email'";
// mysqli_query($koneksi, $update_sql);

// $_SESSION['success'] = "Password berhasil diubah";
// header("Location: profil.php");




// session_start();
// include 'koneksi.php';

// // Validasi
// if (empty($_POST['old_password']) || empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
//     $_SESSION['error'] = "Semua field harus diisi";
//     header("Location: profil.php");
//     exit;
// }

// if ($_POST['new_password'] != $_POST['confirm_password']) {
//     $_SESSION['error'] = "Password baru dan konfirmasi tidak sama";
//     header("Location: profil.php");
//     exit;
// }

// // Verifikasi password lama
// $email = $_SESSION['email_user'];
// $sql = "SELECT password_user FROM users WHERE email_user = '$email'";
// $result = mysqli_query($koneksi, $sql);
// $user = mysqli_fetch_assoc($result);

// if (md5($_POST['old_password']) != $user['password_user']) {
//     $_SESSION['error'] = "Password lama salah";
//     header("Location: profil.php");
//     exit;
// }

// // Update password baru
// $new_password = md5($_POST['new_password']);
// $update_sql = "UPDATE users SET password_user = '$new_password' WHERE email_user = '$email'";

// if (mysqli_query($koneksi, $update_sql)) {
//     $_SESSION['success'] = "Password berhasil diubah";
// } else {
//     $_SESSION['error'] = "Gagal mengubah password";
// }

// header("Location: profil.php");
// exit;
?>