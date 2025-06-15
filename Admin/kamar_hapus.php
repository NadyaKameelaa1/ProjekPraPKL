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

mysqli_autocommit($koneksi, FALSE);

$success = true;
$error_message = "";


try {
    // Langkah 1: Hapus semua gambar yang terkait dengan kamar ini
    $sql_delete_gambar = "DELETE FROM kamar_gambar WHERE id_kamar = ?";
    $stmt_delete_gambar = mysqli_prepare($koneksi, $sql_delete_gambar);
    mysqli_stmt_bind_param($stmt_delete_gambar, "i", $id_kamar);
    $query_gambar = mysqli_stmt_execute($stmt_delete_gambar);
    
    if (!$query_gambar) {
        throw new Exception("Gagal menghapus gambar kamar: " . mysqli_error($koneksi));
    }
    
// Langkah 2: Hapus data kamar setelah gambar berhasil dihapus
    $sql_delete = "DELETE FROM kamar WHERE id_kamar = ?";
    $stmt_delete = mysqli_prepare($koneksi, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $id_kamar);
    $query = mysqli_stmt_execute($stmt_delete);
    
    if (!$query) {
        throw new Exception("Gagal menghapus data kamar: " . mysqli_error($koneksi));
    }
    
    // Jika sampai sini, berarti semua berhasil
    mysqli_commit($koneksi);
    $_SESSION['success'] = "Data kamar dan gambar berhasil dihapus";
    
    // Tutup statements
    mysqli_stmt_close($stmt_delete_gambar);
    mysqli_stmt_close($stmt_delete);
    
} catch (Exception $e) {
    // Rollback jika ada error
    mysqli_rollback($koneksi);
    $success = false;
    $error_message = $e->getMessage();
}

// Kembalikan autocommit ke true
mysqli_autocommit($koneksi, TRUE);

// Tambahkan parameter hapus hanya sebagai fallback
if ($success) {
    header("Location: kamar.php?hapus=sukses");
} else {
    $_SESSION['error'] = "Gagal menghapus data kamar: " . $error_message;
    header("Location: kamar.php?hapus=gagal");
}