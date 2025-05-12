<?php
header('Content-Type: application/json');
require_once '../koneksi/koneksi.php';

if(!isset($_GET['id_hotel']) || !is_numeric($_GET['id_hotel'])) {
    echo json_encode(['success' => false, 'message' => 'ID Hotel tidak valid']);
    exit;
}

$id_hotel = (int)$_GET['id_hotel'];
$query = "SELECT * FROM hotels WHERE id_hotel = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'i', $id_hotel);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) > 0) {
    $hotels = mysqli_fetch_assoc($result);
    // Sesuaikan path gambar sesuai struktur folder Anda
    $hotels['gambar_hotel'] = '/JAVAST/Admin/Gambar/Hotel/' . $hotels['gambar_hotel'];
    echo json_encode(['success' => true, ...$hotels]);
} else {
    echo json_encode(['success' => false, 'message' => 'Hotel tidak ditemukan']);
}

mysqli_stmt_close($stmt);
exit;
?>