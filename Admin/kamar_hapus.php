<?php
session_start();
require_once '../koneksi/koneksi.php';

// Validasi ID kamar
if (!isset($_GET['id_kamar']) || !is_numeric($_GET['id_kamar'])) {
    $_SESSION['error'] = "ID kamar tidak valid";
    header("Location: kamar.php?hapus=gagal");
    exit;
}

$id_kamar = (int)$_GET['id_kamar'];

// Perbaikan 1: Nama variabel query yang benar ($sql_delete bukan $sql.delete)
// Perbaikan 2: Nama tabel yang sesuai (kamer bukan kamar)
$sql_delete = "DELETE FROM kamar WHERE id_kamar = ?";
$stmt_delete = mysqli_prepare($koneksi, $sql_delete);

// Perbaikan 3: Gunakan $id_kamar bukan $id_user
mysqli_stmt_bind_param($stmt_delete, "i", $id_kamar);
$query = mysqli_stmt_execute($stmt_delete);

if ($query) {
    $_SESSION['success'] = "Data kamar berhasil dihapus"; // Perbaikan typo "dibapus"
} else {
    $_SESSION['error'] = "Gagal menghapus data kamar: ". mysqli_error($koneksi);
}

mysqli_stmt_close($stmt_delete);

// Tambahkan parameter hapus hanya sebagai fallback
if ($query) {
    header("Location: kamar.php?hapus=sukses");
} else {
    header("Location: kamar.php?hapus=gagal");
}
exit;
?>