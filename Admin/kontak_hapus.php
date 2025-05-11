<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_GET['id_user']) || !is_numeric($_GET['id_user'])) {
    $_SESSION['error'] = "ID user tidak valid";
    header("Location: kontak.php");
    exit;
}

$id_user = (int)$_GET['id_user'];

$sql_delete = "DELETE FROM kontak_kami WHERE id_user = ?";
$stmt_delete = mysqli_prepare($koneksi, $sql_delete);
mysqli_stmt_bind_param($stmt_delete, "i", $id_user);
$query = mysqli_stmt_execute($stmt_delete);

if ($query) {
    $_SESSION['success'] = "Data user berhasil dihapus";
} else {
    $_SESSION['error'] = "Gagal menghapus data user: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt_delete);

header("Location: kontak.php");
exit;
?>