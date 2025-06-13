<?php
session_start();
require_once '../koneksi/koneksi.php';

// Set header untuk JSON response
header('Content-Type: application/json');

// Cek apakah user sudah login
if (!isset($_SESSION['email_user'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

try {
    // Ambil input dari request
    $input = json_decode(file_get_contents('php://input'), true);
    
    $period = isset($input['period']) ? $input['period'] : 'all';
    $sort = isset($input['sort']) ? $input['sort'] : 'desc';
    $limit = isset($input['limit']) ? intval($input['limit']) : 10;
    
    // Validasi input
    $allowedPeriods = ['all', '2024', '2023', 'month', 'week'];
    $allowedSorts = ['desc', 'asc', 'name'];
    
    if (!in_array($period, $allowedPeriods)) {
        $period = 'all';
    }
    
    if (!in_array($sort, $allowedSorts)) {
        $sort = 'desc';
    }
    
    if ($limit < 1 || $limit > 100) {
        $limit = 10;
    }
    
    // Base query - sesuaikan dengan struktur database kamu
    $query = "
        SELECT 
            h.id_hotel,
            h.nama_hotel,
            h.alamat_hotel,
            h.kota_hotel,
            h.lokasi_hotel,
            COALESCE(SUM(ps.total_bayar), 0) as total_pendapatan,
            COUNT(DISTINCT ps.id_pesanan) as total_pesanan,
            COUNT(DISTINCT p.id_pembayaran) as total_pembayaran
        FROM hotels h
        LEFT JOIN pesanan ps ON h.id_hotel = ps.id_hotel
        LEFT JOIN pembayaran p ON ps.id_pesanan = p.id_pesanan
    ";
    
    // Tambahkan kondisi periode
    $whereConditions = [];
    $params = [];
    
    switch ($period) {
        case '2024':
            $whereConditions[] = "YEAR(ps.tanggal_pesan) = ?";
            $params[] = 2024;
            break;
        case '2023':
            $whereConditions[] = "YEAR(ps.tanggal_pesan) = ?";
            $params[] = 2023;
            break;
        case 'month':
            $whereConditions[] = "ps.tanggal_pesan >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            break;
        case 'week':
            $whereConditions[] = "ps.tanggal_pesan >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
            break;
        case 'all':
        default:
            // Tidak ada kondisi tambahan
            break;
    }
    
    // Tambahkan kondisi untuk pembayaran yang sudah dikonfirmasi (jika ada status)
    // Sesuaikan dengan field status di database kamu
    if (isset($input['confirmed_only']) && $input['confirmed_only']) {
        $whereConditions[] = "p.booking_status = 'Dikonfirmasi'";
    }
    
    // Tambahkan WHERE clause jika ada kondisi
    if (!empty($whereConditions)) {
        $query .= " WHERE " . implode(' AND ', $whereConditions);
    }
    
    // Group by hotel
    $query .= " GROUP BY h.id_hotel, h.nama_hotel, h.alamat_hotel, h.kota_hotel, h.lokasi_hotel";
    
    // Tambahkan ORDER BY
    switch ($sort) {
        case 'asc':
            $query .= " ORDER BY total_pendapatan ASC";
            break;
        case 'name':
            $query .= " ORDER BY h.nama_hotel ASC";
            break;
        case 'desc':
        default:
            $query .= " ORDER BY total_pendapatan DESC";
            break;
    }
    
    // Tambahkan LIMIT
    if ($limit > 0) {
        $query .= " LIMIT ?";
        $params[] = $limit;
    }
    
    // Prepare dan execute query
    $stmt = $koneksi->prepare($query);
    
    if (!empty($params)) {
        // Tentukan tipe parameter
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } else {
                $types .= 's';
            }
        }
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Ambil data
    $hotelData = [];
    while ($row = $result->fetch_assoc()) {
        $hotelData[] = [
            'id_hotel' => $row['id_hotel'],
            'nama_hotel' => $row['nama_hotel'],
            'alamat_hotel' => $row['alamat_hotel'],
            'kota_hotel' => $row['kota_hotel'],
            'lokasi_hotel' => $row['lokasi_hotel'],
            'total_pendapatan' => floatval($row['total_pendapatan']),
            'total_pesanan' => intval($row['total_pesanan']),
            'total_pembayaran' => intval($row['total_pembayaran']),
            'rata_rata_per_pesanan' => $row['total_pesanan'] > 0 ? 
                (floatval($row['total_pendapatan']) / intval($row['total_pesanan'])) : 0
        ];
    }
    
    // Hitung statistik tambahan
    $totalRevenue = array_sum(array_column($hotelData, 'total_pendapatan'));
    $totalPesanan = array_sum(array_column($hotelData, 'total_pesanan'));
    $totalPembayaran = array_sum(array_column($hotelData, 'total_pembayaran'));
    $avgRevenue = count($hotelData) > 0 ? ($totalRevenue / count($hotelData)) : 0;
    
    // Query tambahan untuk mendapatkan informasi detail
    $detailQuery = "
        SELECT 
            COUNT(DISTINCT h.id_hotel) as total_hotels_with_revenue,
            COUNT(DISTINCT ps.id_pesanan) as total_all_pesanan,
            COUNT(DISTINCT p.id_pembayaran) as total_all_pembayaran,
            MIN(ps.total_bayar) as min_payment,
            MAX(ps.total_bayar) as max_payment,
            AVG(ps.total_bayar) as avg_payment
        FROM hotels h
        LEFT JOIN pesanan ps ON h.id_hotel = ps.id_hotel
        LEFT JOIN pembayaran p ON ps.id_pesanan = p.id_pesanan
        WHERE ps.total_bayar IS NOT NULL
    ";
    
    $detailResult = $koneksi->query($detailQuery);
    $detailStats = $detailResult->fetch_assoc();
    
    // Response sukses
    echo json_encode([
        'success' => true,
        'data' => $hotelData,
        'statistics' => [
            'total_revenue' => $totalRevenue,
            'total_pesanan' => $totalPesanan,
            'total_pembayaran' => $totalPembayaran,
            'average_revenue' => $avgRevenue,
            'total_hotels' => count($hotelData),
            'total_hotels_with_revenue' => intval($detailStats['total_hotels_with_revenue']),
            'min_payment' => floatval($detailStats['min_payment']),
            'max_payment' => floatval($detailStats['max_payment']),
            'avg_payment' => floatval($detailStats['avg_payment']),
            'period' => $period,
            'sort' => $sort,
            'limit' => $limit
        ],
        'query_info' => [
            'period_filter' => $period,
            'sort_order' => $sort,
            'result_limit' => $limit,
            'total_results' => count($hotelData)
        ],
        'message' => 'Data loaded successfully'
    ]);
    
} catch (Exception $e) {
    // Log error (optional)
    error_log("Hotel Revenue Error: " . $e->getMessage());
    
    // Response error
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'data' => [],
        'debug_info' => [
            'error_line' => $e->getLine(),
            'error_file' => $e->getFile()
        ]
    ]);
}

// Close connection
if (isset($koneksi)) {
    $koneksi->close();
}
?>