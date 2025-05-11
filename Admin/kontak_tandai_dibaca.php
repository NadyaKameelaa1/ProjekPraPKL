<?php
require_once '../koneksi/koneksi.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id_user'];
    
    $query = "UPDATE kontak_kami SET status_dibaca = 1 WHERE id_user = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id_user);
    
    if(mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($koneksi)]);
    }
    
    mysqli_stmt_close($stmt);
    exit;
}
?>