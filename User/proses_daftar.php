<?php

session_start();
require_once '../Koneksi/koneksi.php';

$id_user = $_POST['id_user'];
$nama_user = $_POST['nama_user'];
$email_user = $_POST['email_user'];
$no_telp = $_POST['no_telp'];
$alamat_user = $_POST['alamat_user'];
$password_user = $_POST['password_user'];

$errors = [];
$old_input = $_POST;

// Validasi Nama
if (!preg_match("/^[A-Za-z\s]+$/", $_POST['nama_user'])) {
    $errors['nama'] = "Nama hanya boleh mengandung huruf dan spasi";
}

// Validasi Nomor Telepon
if (!preg_match("/^[0-9]+$/", $_POST['no_telp'])) {
    $errors['telepon'] = "Nomor telepon hanya boleh angka";
}

// Validasi Password
if (strlen($_POST['password_user']) < 4) {
    $errors['password'] = "Password minimal 4 karakter";
}

// Jika ada error
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_input'] = $old_input;
    header("Location: daftar.php");
    exit();
}


$sql = "INSERT INTO users (id_user, nama_user, email_user, no_telp, alamat_user, password_user) VALUES ('$id_user','$nama_user','$email_user','$no_telp','$alamat_user', md5('$password_user'))";

$query = mysqli_query($koneksi, $sql);

if($query){
    header("location:login.php?daftar=sukses");
    exit;
} else{
    header("location:daftar.php?daftar=gagal");
    exit;
}


?>