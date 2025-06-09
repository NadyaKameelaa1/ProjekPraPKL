<?php
header('Content-Type: application/json');

require_once '../koneksi/koneksi.php';

if(isset($_GET['id_kamar'])) {
    $id_kamar = (int)$_GET['id_kamar'];
    $query = "SELECT * FROM kamar WHERE id_kamar = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id_kamar);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($kamar = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'success' => true,
            'id_kamar' => $kamar['id_kamar'],
            'id_hotel' => $kamar['id_hotel'],
            'nama_kamar' => $kamar['nama_kamar'],
            'tipe_kasur' => $kamar['tipe_kasur'],
            'ukuran_kamar' => $kamar['ukuran_kamar'],
            'kapasitas_kamar' => $kamar['kapasitas_kamar'],
            'fasilitas_kamar' => $kamar['fasilitas_kamar'],
            'harga_kamar' => $kamar['harga_kamar'],
            'jumlah_kamar' => $kamar['jumlah_kamar'],
            'jumlah_dewasa' => $kamar['jumlah_dewasa'],
            'jumlah_anak' => $kamar['jumlah_anak'],
            'deskripsi_kamar' => $kamar['deskripsi_kamar']
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Kamar tidak ditemukan']);
    }
    exit;
}
?>