<?php
header('Content-Type: application/json');

require_once '../koneksi/koneksi.php';

if(isset($_GET['id_hotel'])) {
    $id_hotel = (int)$_GET['id_hotel'];
    $query = "SELECT * FROM hotels WHERE id_hotel = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id_hotel);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($hotel = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'success' => true,
            'id_hotel' => $hotel['id_hotel'],
            'kota_hotel' => $hotel['kota_hotel'],
            'nama_hotel' => $hotel['nama_hotel'],
            'bintang_hotel' => $hotel['bintang_hotel'],
            'lokasi_hotel' => $hotel['lokasi_hotel'],
            'alamat_hotel' => $hotel['alamat_hotel'],
            'fasilitas_hotel' => $hotel['fasilitas_hotel'],
            'gambar_hotel' => '/JAVAST/Admin/Gambar/Hotel/'.$hotel['gambar_hotel'] // Path lengkap ke gambar
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Hotel tidak ditemukan']);
    }
    exit;
}
?>