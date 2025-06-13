<?php
header('Content-Type: application/json');
require_once '../Koneksi/koneksi.php';

try {
    $db = new Database();
    $koneksi = $db->getConnection();

    // Query untuk mengambil data pendapatan per hotel
    $query = "SELECT 
                h.id_hotel,
                h.nama_hotel,
                SUM(p.total_bayar) as revenue
              FROM pesanan p
              JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
              JOIN hotels h ON p.id_hotel = h.id_hotel
              WHERE pb.booking_status IN ('Dibayar', 'Selesai')
              GROUP BY h.id_hotel, h.nama_hotel
              ORDER BY revenue DESC";

    $stmt = $koneksi->prepare($query);
    $stmt->execute();

    $hotels = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $hotels[] = [
            'id' => $row['id_hotel'],
            'name' => $row['nama_hotel'],
            'revenue' => (int)$row['revenue']
        ];
    }

    echo json_encode(['hotels' => $hotels]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}