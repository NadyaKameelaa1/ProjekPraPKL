<?php
header('Content-Type: application/json');
require_once '../koneksi/koneksi.php';

$id_kamar = $_GET['id_kamar'] ?? 0;
$id_kamar = (int)$id_kamar; // Pastikan integer

$stmt = $koneksi->prepare("SELECT nama_kamar FROM kamar WHERE id_kamar = ?");
$stmt->bind_param("i", $id_kamar);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode([
        'status' => 'success',
        'nama_kamar' => $data['nama_kamar']
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data kamar tidak ditemukan'
    ]);
}