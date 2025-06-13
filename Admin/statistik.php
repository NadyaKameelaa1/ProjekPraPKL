<?php
session_start();
require_once '../Koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}


try {
    $pdo = new PDO("mysql:host=$server;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Query untuk mengambil top 10 hotel berdasarkan total pendapatan
$query = "
    SELECT 
        h.nama_hotel,
        COALESCE(SUM(p.total_bayar), 0) as total_pendapatan
    FROM hotels h
    LEFT JOIN pesanan p ON h.id_hotel = p.id_hotel
    GROUP BY h.id_hotel, h.nama_hotel
    ORDER BY total_pendapatan DESC
    LIMIT 40
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$hotelData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Konversi data untuk JavaScript
$hotelNames = [];
$hotelRevenues = [];

foreach ($hotelData as $hotel) {
    $hotelNames[] = $hotel['nama_hotel'];
    $hotelRevenues[] = (int)$hotel['total_pendapatan'];
}

// Hitung statistik untuk dashboard
$totalRevenue = array_sum($hotelRevenues);
$avgRevenue = count($hotelRevenues) > 0 ? $totalRevenue / count($hotelRevenues) : 0;
$topHotel = count($hotelNames) > 0 ? $hotelNames[0] : '-';

// Query untuk total hotel
$totalHotelsQuery = "SELECT COUNT(*) as total FROM hotels";
$totalHotelsStmt = $pdo->prepare($totalHotelsQuery);
$totalHotelsStmt->execute();
$totalHotels = $totalHotelsStmt->fetch(PDO::FETCH_ASSOC)['total'];


// Query untuk mengambil detail pendapatan per hotel
$query_tabel = "
    SELECT 
        h.nama_hotel,
        COALESCE(SUM(pes.total_bayar), 0) as total_pendapatan,
        COUNT(p.id_pembayaran) as total_booking,
        COUNT(CASE WHEN p.booking_status = 'Selesai' THEN 1 END) as booking_selesai,
        CASE 
            WHEN COUNT(p.id_pembayaran) > 0 
            THEN COALESCE(SUM(pes.total_bayar), 0) / COUNT(p.id_pembayaran)
            ELSE 0 
        END as rata_rata_per_booking
    FROM hotels h
    LEFT JOIN pesanan pes ON h.id_hotel = pes.id_hotel
    LEFT JOIN pembayaran p ON pes.id_pesanan = p.id_pesanan
    GROUP BY h.id_hotel, h.nama_hotel
    HAVING total_pendapatan > 0 OR total_booking > 0
    ORDER BY total_pendapatan DESC
    LIMIT 40
";

$stmt = $pdo->prepare($query_tabel);
$stmt->execute();
$hotelDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

function formatRupiah($amount) {
    return "Rp " . number_format($amount, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pendapatan Hotel - Admin Dashboard</title>
    <link rel="stylesheet" href="statistik.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- navbar -->
<div class="navbar">
        <div class="logo">
            <img src="logo/Logo_Javast.png" alt="Logo_Javast">
        </div>
    <div class="top-bar">
        <div class="left-buttons">
        <div class="navbar-actions">
            <select id="periodFilter">
                <option value="all">Semua Periode</option>
               <option value="2024">Tahun 2024</option>
                <option value="2023">Tahun 2023</option>
                <option value="month">Bulan Ini</option>
                <option value="week">Minggu Ini</option>
            </select>

            <select id="sortFilter">
                <option value="desc">Tertinggi ke Terendah</option>
                <option value="asc">Terendah ke Tertinggi</option>
                <option value="name">Nama Hotel (A-Z)</option>
            </select>

            <select id="limitFilter">
                <option value="10">Top 10 Hotel</option>
                <option value="15">Top 15 Hotel</option>
                <option value="20">Top 20 Hotel</option>
            </select>

            <!-- <button class="btn btn-primary">
                <i class="fas fa-download"></i> Export Excel
            </button> -->
        </div>
        <div>
            <button class="button" onclick="window.location.href='login.php'"> 
                <i class="fas fa-user"></i> Log Out
            </button>
        </div>
        
        </div>
        </div>
        
    </div>
    
     <div class="dashboard-container">
        <div class="sidebar">
        <div class="sidebar-header">
            <br><br><br><br><br><br><br>
            <h1>ADMIN PANEL</h1>
        </div>
            <div class="sidebar-menu">
                <div class="menu-item" onclick="window.location.href='dashboard.php'">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </div>
                <div class="menu-item" onclick="window.location.href='users.php'">
                    <i class="fas fa-users"></i> Users
                </div>
                <div class="menu-item" onclick="window.location.href='hotels.php'">
                    <i class="fas fa-hotel"></i> Hotel
                </div>
                <div class="menu-item" onclick="window.location.href='kamar.php'">
                    <i class="fas fa-bed"></i> Kamar
                </div>
                <div class="menu-item" onclick="window.location.href='kontak.php'">
                    <i class="fas fa-envelope"></i> Kontak Kami
                </div>
                <div class="menu-item active" onclick="window.location.href='statistik.php'">
                    <i class="fas fa-chart-simple"></i> Statistik
                </div>
                <div class="menu-item" onclick="window.location.href='booking.php'">
                    <i class="fas fa-calendar-check"></i> Booking <i class="fa-solid fa-caret-up"></i>
                </div>
            </div>
        </div>
    


    <div class="main-content">
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <div class="header">
        <h1>Laporan Pendapatan Hotel</h1>
    </div>
    <div class="header-2">
        <h1>Dashboard Admin - Statistik & Analisis Pendapatan</h1>
    </div>

    <!-- <div class="table-controls">
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="userSearch" placeholder="Cari id pengirim, nama pengirim, email pengirim, pesan, waktu,...">
        </div>


    </div> -->

    <main class="container">
        <!-- Statistik Overview -->
        <div class="stats-grid">
            <!-- Total Pendapatan -->

            <div class="stats-card">
                <div class="stats-content">
                    <div>
                        
                        <p class="stats-value stats-green" id="totalRevenue">Rp <?php echo number_format($totalRevenue, 0, ',', '.'); ?></p>
                        <p class="stats-label">Total Pendapatan</p>
                        <br>
                        <!-- <p class="stats-subtext">6 bulan terakhir</p> -->
                    </div>
                    <div class="stats-icon bg-green">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
                <div class="stats-content">
                    <div>
                        
                        <p class="stats-value stats-green"  id="avgRevenue">Rp <?php echo number_format($avgRevenue, 0, ',', '.'); ?></p>
                        <p class="stats-label">Rata-Rata Pendapatan</p>
                        <br>
                        <!-- <p class="stats-subtext">6 bulan terakhir</p> -->
                    </div>
                    <div class="stats-icon bg-green">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
                <div class="stats-content">
                    <div>
                        
                        <p class="stats-value stats-green"  id="topHotel"><?php echo htmlspecialchars($topHotel); ?></p>
                        <p class="stats-label">Hotel Terbaik</p>
                        <br>
                        
                    </div>
                    <div class="stats-icon bg-green">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
                <div class="stats-content">
                    <div>
                        
                        <p class="stats-value stats-green" id="totalHotels"><?php echo $totalHotels; ?></p>
                        <p class="stats-label">Total Hotel</p>
                        <br>
                        <!-- <p class="stats-subtext">6 bulan terakhir</p> -->
                    </div>
                    <div class="stats-icon bg-green">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>

         

        <!-- Charts Section -->
        <div class="charts-grid single-chart">
            <!-- Grafik Pendapatan Bulanan -->
            <!-- <div class="chart-card">
                <h3>Tren Pendapatan Bulanan</h3>
                <div class="chart-container">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
            </div> -->

            <!-- Grafik Pendapatan per Hotel -->
            <div class="chart-card ">
                <h3>Pendapatan per hotel</h3>
                <div class="chart-container">
                    <canvas id="hotelRevenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabel Detail Pendapatan per Hotel -->
        <div class="table-card">
            <div class="table-header">
                <h3>Detail Pendapatan per Hotel</h3>
                <p>Ringkasan performa dan statistik setiap hotel</p>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th class="text-left">Nama Hotel</th>
                            <th class="text-right">Total Pendapatan</th>
                            <th class="text-right">Total Booking</th>
                            <th class="text-right">Booking Selesai</th>
                            <th class="text-right">Rata-rata per Booking</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($hotelDetails)): ?>
                            <?php foreach ($hotelDetails as $hotel): ?>
                                <tr>
                                    <td class="text-left">
                                        <div class="hotel-name"><?php echo htmlspecialchars($hotel['nama_hotel']); ?></div>
                                    </td>
                                    <td class="text-right">
                                        <div class="revenue-value"><?php echo formatRupiah($hotel['total_pendapatan']); ?></div>
                                    </td>
                                    <td class="text-right">
                                        <span class="booking-count"><?php echo number_format($hotel['total_booking']); ?></span>
                                    </td>
                                    <td class="text-right">
                                        <span class="booking-completed"><?php echo number_format($hotel['booking_selesai']); ?></span>
                                    </td>
                                    <td class="text-right">
                                        <div class="avg-revenue"><?php echo formatRupiah($hotel['rata_rata_per_booking']); ?></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="no-data">
                                    📊 Tidak ada data hotel yang ditemukan
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <br><br><br><br>
    </div>

    <script>

// Data dari PHP
        const initialData = {
            labels: <?php echo json_encode($hotelNames); ?>,
            revenues: <?php echo json_encode($hotelRevenues); ?>
        };
        
        let currentChart = null;
        let currentData = [...initialData.revenues];
        let currentLabels = [...initialData.labels];
        
        // Fungsi untuk format mata uang Rupiah
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }
        
        // Fungsi untuk format angka pendek (K, M, B)
        function formatShortNumber(num) {
            if (num >= 1000000000) {
                return (num / 1000000000).toFixed(1) + 'M';
            } else if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'Jt';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'K';
            }
            return num.toString();
        }
        
        // Fungsi untuk generate warna yang berbeda untuk setiap bar
        function generateColors(count) {
            const colors = [
                'rgba(54, 162, 235, 0.8)',   // Biru
                'rgba(75, 192, 192, 0.8)',   // Hijau
                'rgba(255, 159, 64, 0.8)',   // Orange
                'rgba(255, 99, 132, 0.8)',   // Merah
                'rgba(153, 102, 255, 0.8)',  // Ungu
                'rgba(255, 205, 86, 0.8)',   // Kuning
                'rgba(231, 76, 60, 0.8)',    // Merah Gelap
                'rgba(46, 204, 113, 0.8)',   // Hijau Terang
            ];
            
            const result = [];
            for (let i = 0; i < count; i++) {
                if (i < colors.length) {
                    result.push(colors[i]);
                } else {
                    // Generate warna random jika melebihi warna yang tersedia
                    const hue = (i * 137.508) % 360; // Golden angle approximation
                    result.push(`hsl(${hue}, 70%, 60%)`);
                }
            }
            return result;
        }
        
        // Fungsi untuk membuat/update chart
        function createChart(data) {
            const ctx = document.getElementById('hotelRevenueChart').getContext('2d');
            
            if (currentChart) {
                currentChart.destroy();
            }
            
            const labels = data.labels || currentLabels;
            const revenues = data.revenues || currentData;
            const colors = generateColors(revenues.length);
            
            currentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: revenues,
                        backgroundColor: colors,
                        borderColor: colors.map(color => color.replace('0.8', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Pendapatan: ' + formatRupiah(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatShortNumber(value);
                                }
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    },
                    onHover: (event, activeElements) => {
                        event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
                    }
                }
            });
        }
        
        // Fungsi untuk update statistik
        function updateStats(data) {
            const revenues = data.revenues || currentData;
            const labels = data.labels || currentLabels;
            
            const totalRevenue = revenues.reduce((sum, revenue) => sum + revenue, 0);
            const avgRevenue = revenues.length > 0 ? totalRevenue / revenues.length : 0;
            const topHotel = labels.length > 0 ? labels[0] : '-';
            
            document.getElementById('totalRevenue').textContent = formatRupiah(totalRevenue);
            document.getElementById('avgRevenue').textContent = formatRupiah(avgRevenue);
            document.getElementById('topHotel').textContent = topHotel;
        }
        
        // Fungsi untuk filter dan sort data
        function filterAndSortData() {
            const periodFilter = document.getElementById('periodFilter').value;
            const sortFilter = document.getElementById('sortFilter').value;
            const limitFilter = parseInt(document.getElementById('limitFilter').value);
            
            let filteredData = [...initialData.revenues];
            let filteredLabels = [...initialData.labels];
            
            // Simulasi filter berdasarkan periode (dalam implementasi nyata, ini akan query database)
            if (periodFilter !== 'all') {
                // Untuk demo, kita simulasikan filter dengan mengurangi pendapatan
                filteredData = filteredData.map(revenue => {
                    const multiplier = periodFilter === 'month' ? 0.1 : 
                                     periodFilter === 'week' ? 0.02 : 
                                     periodFilter === '2024' ? 0.8 : 0.6;
                    return Math.floor(revenue * multiplier);
                });
            }
            
            // Gabungkan data untuk sorting
            const combinedData = filteredLabels.map((label, index) => ({
                label: label,
                revenue: filteredData[index]
            }));
            
            // Sort data
            if (sortFilter === 'desc') {
                combinedData.sort((a, b) => b.revenue - a.revenue);
            } else if (sortFilter === 'asc') {
                combinedData.sort((a, b) => a.revenue - b.revenue);
            } else if (sortFilter === 'name') {
                combinedData.sort((a, b) => a.label.localeCompare(b.label));
            }
            
            // Limit data
            const limitedData = combinedData.slice(0, limitFilter);
            
            // Pisahkan kembali labels dan revenues
            currentData = limitedData.map(item => item.revenue);
            currentLabels = limitedData.map(item => item.label);
            
            // Update chart dan statistik
            createChart({ labels: currentLabels, revenues: currentData });
            updateStats({ labels: currentLabels, revenues: currentData });
        }
        
        // Fungsi untuk refresh data (dalam implementasi nyata, ini akan fetch data baru dari server)
        function refreshData() {
            const refreshBtn = document.querySelector('.refresh-btn');
            refreshBtn.innerHTML = '⏳ Loading...';
            refreshBtn.disabled = true;
            
            // Simulasi loading
            setTimeout(() => {
                // Reset ke data awal
                currentData = [...initialData.revenues];
                currentLabels = [...initialData.labels];
                
                // Apply filter yang sedang aktif
                filterAndSortData();
                
                refreshBtn.innerHTML = '🔄 Refresh Data';
                refreshBtn.disabled = false;
            }, 1000);
        }
        
        // Event listeners untuk filter
        document.getElementById('periodFilter').addEventListener('change', filterAndSortData);
        document.getElementById('sortFilter').addEventListener('change', filterAndSortData);
        document.getElementById('limitFilter').addEventListener('change', filterAndSortData);
        
        // Inisialisasi chart saat DOM loaded
        document.addEventListener('DOMContentLoaded', function() {
            createChart(initialData);
            updateStats(initialData);
        });

    // Data untuk berbagai periode
//     const periodData = {
//     3: {
//         stats: {
//             totalRevenue: 'Rp 510.000.000',
//             totalBooking: '2,465',
//             bookingSukses: '2,150',
//             successRate: '87.2%',
//             period: '3 bulan terakhir'
//         },
//         monthly: {
//             labels: ['April', 'Mei', 'Juni'],
//             values: [161000000, 175000000, 174000000]
//         },
//         hotels: [
//             { name: 'Grand Hotel Jakarta', revenue: 160000000, booking: 625, selesai: 550, rate: '88.0%' },
//             { name: 'Luxury Resort Bali', revenue: 140000000, booking: 490, selesai: 425, rate: '86.7%' },
//             { name: 'Beach Hotel Lombok', revenue: 116000000, booking: 560, selesai: 475, rate: '84.8%' },
//             { name: 'City Inn Surabaya', revenue: 94000000, booking: 790, selesai: 700, rate: '88.6%' }
//         ]
//     },
//     6: {
//         stats: {
//             totalRevenue: 'Rp 1.020.000.000',
//             totalBooking: '4,930',
//             bookingSukses: '4,300',
//             successRate: '87.2%',
//             period: '6 bulan terakhir'
//         },
//         monthly: {
//             labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'],
//             values: [140000000, 157000000, 161000000, 175000000, 187000000, 200000000]
//         },
//         hotels: [
//             { name: 'Grand Hotel Jakarta', revenue: 320000000, booking: 1250, selesai: 1100, rate: '88.0%' },
//             { name: 'Luxury Resort Bali', revenue: 280000000, booking: 980, selesai: 850, rate: '86.7%' },
//             { name: 'Beach Hotel Lombok', revenue: 232000000, booking: 1120, selesai: 950, rate: '84.8%' },
//             { name: 'City Inn Surabaya', revenue: 188000000, booking: 1580, selesai: 1400, rate: '88.6%' }
//         ]
//     },
//     12: {
//         stats: {
//             totalRevenue: 'Rp 2.040.000.000',
//             totalBooking: '9,860',
//             bookingSukses: '8,600',
//             successRate: '87.2%',
//             period: '1 tahun terakhir'
//         },
//         monthly: {
//             labels: ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
//             values: [120000000, 135000000, 145000000, 155000000, 165000000, 175000000, 140000000, 157000000, 161000000, 175000000, 187000000, 200000000]
//         },
//         hotels: [
//             { name: 'Grand Hotel Jakarta', revenue: 640000000, booking: 2500, selesai: 2200, rate: '88.0%' },
//             { name: 'Luxury Resort Bali', revenue: 560000000, booking: 1960, selesai: 1700, rate: '86.7%' },
//             { name: 'Beach Hotel Lombok', revenue: 464000000, booking: 2240, selesai: 1900, rate: '84.8%' },
//             { name: 'City Inn Surabaya', revenue: 376000000, booking: 3160, selesai: 2800, rate: '88.6%' }
//         ]
//     }
// };

// const hotelData = [
//             { name: 'Gumaya Tower Hotel', revenue: 850000000 },
//             { name: 'PO Hotel Semarang', revenue: 720000000 },
//             { name: 'The Royal Surakarta Heritage', revenue: 650000000 },
//             { name: 'City Inn Surabaya', revenue: 580000000 },
//             { name: 'Mountain View Bandung', revenue: 520000000 },
//             { name: 'Paradise Resort Yogyakarta', revenue: 480000000 },
//             { name: 'Ocean Blue Makassar', revenue: 450000000 },
//             { name: 'Royal Hotel Medan', revenue: 420000000 },
//             { name: 'Sunset Villa Bali', revenue: 390000000 },
//             { name: 'Urban Hotel Semarang', revenue: 360000000 },
//             { name: 'Garden Resort Malang', revenue: 340000000 },
//             { name: 'Plaza Hotel Palembang', revenue: 320000000 },
//             { name: 'Tropical Inn Batam', revenue: 300000000 },
//             { name: 'Heritage Hotel Solo', revenue: 280000000 },
//             { name: 'Modern Lodge Balikpapan', revenue: 260000000 },
//             { name: 'Seaside Resort Manado', revenue: 240000000 },
//             { name: 'Business Hotel Pontianak', revenue: 220000000 },
//             { name: 'Comfort Inn Pekanbaru', revenue: 200000000 },
//             { name: 'Budget Hotel Jambi', revenue: 180000000 },
//             { name: 'Express Hotel Lampung', revenue: 160000000 }
//         ];

//         let currentChart = null;
//         let currentData = [...hotelData];

//         // Fungsi untuk format mata uang Rupiah
//         function formatRupiah(amount) {
//             return new Intl.NumberFormat('id-ID', {
//                 style: 'currency',
//                 currency: 'IDR',
//                 minimumFractionDigits: 0,
//                 maximumFractionDigits: 0
//             }).format(amount);
//         }

//         // Fungsi untuk format angka pendek (K, M, B)
//         function formatShortNumber(num) {
//             if (num >= 1000000000) {
//                 return (num / 1000000000).toFixed(1) + 'M';
//             } else if (num >= 1000000) {
//                 return (num / 1000000).toFixed(1) + 'Jt';
//             } else if (num >= 1000) {
//                 return (num / 1000).toFixed(1) + 'K';
//             }
//             return num.toString();
//         }

//         // Fungsi untuk generate warna yang berbeda untuk setiap bar
//         function generateColors(count) {
//             const colors = [
//                 'rgba(54, 162, 235, 0.8)',   // Biru
//                 'rgba(75, 192, 192, 0.8)',   // Hijau
//                 'rgba(255, 159, 64, 0.8)',   // Orange
//                 'rgba(255, 99, 132, 0.8)',   // Merah
//                 'rgba(153, 102, 255, 0.8)',  // Ungu
//                 'rgba(255, 206, 86, 0.8)',   // Kuning
//                 'rgba(231, 76, 60, 0.8)',    // Merah Gelap
//                 'rgba(46, 204, 113, 0.8)',   // Hijau Terang
//                 'rgba(155, 89, 182, 0.8)',   // Ungu Muda
//                 'rgba(52, 152, 219, 0.8)'    // Biru Terang
//             ];
            
//             const result = [];
//             for (let i = 0; i < count; i++) {
//                 if (i < colors.length) {
//                     result.push(colors[i]);
//                 } else {
//                     // Generate warna random jika melebihi warna yang tersedia
//                     const hue = (i * 137.508) % 360; // Golden angle approximation
//                     result.push(`hsla(${hue}, 70%, 60%, 0.8)`);
//                 }
//             }
//             return result;
//         }

//         // Fungsi untuk membuat/update chart
//         function createChart(data) {
//             const ctx = document.getElementById('hotelRevenueChart').getContext('2d');
            
//             if (currentChart) {
//                 currentChart.destroy();
//             }

//             const labels = data.map(hotel => hotel.name);
//             const revenues = data.map(hotel => hotel.revenue);
//             const colors = generateColors(data.length);

//             currentChart = new Chart(ctx, {
//                 type: 'bar',
//                 data: {
//                     labels: labels,
//                     datasets: [{
//                         label: 'Pendapatan (Rp)',
//                         data: revenues,
//                         backgroundColor: colors,
//                         borderColor: colors.map(color => color.replace('0.8', '1')),
//                         borderWidth: 1
//                     }]
//                 },
//                 options: {
//                     responsive: true,
//                     maintainAspectRatio: false,
//                     plugins: {
//                         legend: {
//                             display: false
//                         },
//                         tooltip: {
//                             callbacks: {
//                                 label: function(context) {
//                                     return 'Pendapatan: ' + formatRupiah(context.parsed.y);
//                                 }
//                             }
//                         }
//                     },
//                     scales: {
//                         y: {
//                             beginAtZero: true,
//                             ticks: {
//                                 callback: function(value) {
//                                     return formatShortNumber(value);
//                                 }
//                             }
//                         },
//                         x: {
//                             ticks: {
//                                 maxRotation: 45,
//                                 minRotation: 0
//                             }
//                         }
//                     },
//                     onHover: (event, activeElements) => {
//                         event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
//                     }
//                 }
//             });
//         }

//         // Fungsi untuk update statistik
//         function updateStats(data) {
//             const totalRevenue = data.reduce((sum, hotel) => sum + hotel.revenue, 0);
//             const avgRevenue = totalRevenue / data.length;
//             const topHotel = data.length > 0 ? data[0].name : '-';

//             document.getElementById('totalRevenue').textContent = formatShortNumber(totalRevenue);
//             document.getElementById('avgRevenue').textContent = formatShortNumber(avgRevenue);
//             document.getElementById('topHotel').textContent = topHotel;
//             document.getElementById('totalHotels').textContent = data.length;
//         }

//         // Fungsi untuk filter dan sort data
//         function filterAndSortData() {
//             const periodFilter = document.getElementById('periodFilter').value;
//             const sortFilter = document.getElementById('sortFilter').value;
//             const limitFilter = document.getElementById('limitFilter').value;

//             let filteredData = [...hotelData];

//             // Filter berdasarkan periode (simulasi - dalam implementasi nyata akan query database)
//             if (periodFilter !== 'all') {
//                 // Untuk demo, kita simulasikan filter dengan mengurangi pendapatan
//                 filteredData = filteredData.map(hotel => ({
//                     ...hotel,
//                     revenue: hotel.revenue * (periodFilter === 'month' ? 0.1 : 
//                              periodFilter === 'week' ? 0.02 : 
//                              periodFilter === '2024' ? 0.8 : 0.6)
//                 }));
//             }

//             // Sort data
//             if (sortFilter === 'desc') {
//                 filteredData.sort((a, b) => b.revenue - a.revenue);
//             } else if (sortFilter === 'asc') {
//                 filteredData.sort((a, b) => a.revenue - b.revenue);
//             } else if (sortFilter === 'name') {
//                 filteredData.sort((a, b) => a.name.localeCompare(b.name));
//             }

//             // Limit data
//             if (limitFilter !== 'all') {
//                 filteredData = filteredData.slice(0, parseInt(limitFilter));
//             }

//             currentData = filteredData;
//             createChart(currentData);
//             updateStats(currentData);
//         }

//         // Event listeners
//         document.getElementById('periodFilter').addEventListener('change', filterAndSortData);
//         document.getElementById('sortFilter').addEventListener('change', filterAndSortData);
//         document.getElementById('limitFilter').addEventListener('change', filterAndSortData);

//         // Inisialisasi chart saat DOM loaded
//         document.addEventListener('DOMContentLoaded', function() {
//             filterAndSortData();
//         });

        // Fungsi untuk mengambil data dari database (implementasi nyata)
        /*
        async function fetchHotelRevenueData(period = 'all', limit = 'all') {
            try {
                const response = await fetch(`get_hotel_revenue.php?period=${period}&limit=${limit}`);
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error fetching hotel revenue data:', error);
                return [];
            }
        }
        */
// ----------------------------------------------------------------------------------------------------------
// HANYA VARIABLE UNTUK HOTEL CHART (monthly chart dihapus)
// let hotelChart;

// Fungsi untuk format rupiah
function formatRupiah(angka) {
    return 'Rp ' + angka.toLocaleString('id-ID');
}

// Fungsi untuk update statistik
// function updateStats(period) {
//     const data = periodData[period];
    
//     // Update stats cards
//     document.querySelector('.stats-value.stats-green').textContent = data.stats.totalRevenue;
//     document.querySelector('.stats-value.stats-blue').textContent = data.stats.totalBooking;
//     document.querySelector('.stats-value.stats-purple').textContent = data.stats.bookingSukses;
    
//     // Update subtext
//     document.querySelector('.stats-green').nextElementSibling.textContent = data.stats.period;
// }

// Fungsi untuk update tabel
function updateTable(period) {
    const data = periodData[period];
    const tbody = document.querySelector('tbody');
    
    tbody.innerHTML = '';
    
    data.hotels.forEach(hotel => {
        const avgPerBooking = hotel.selesai > 0 ? formatRupiah(hotel.revenue / hotel.selesai) : 'Rp 0';
        const statusClass = parseFloat(hotel.rate) >= 85 ? 'status-high' : 'status-medium';
        
        const row = `
            <tr>
                <td class="text-left">
                    <div class="hotel-name">${hotel.name}</div>
                </td>
                <td class="text-right">
                    <div class="revenue-value">${formatRupiah(hotel.revenue)}</div>
                </td>
                <td class="text-right">${hotel.booking.toLocaleString('id-ID')}</td>
                <td class="text-right">${hotel.selesai.toLocaleString('id-ID')}</td>
                <td class="text-right">${avgPerBooking}</td>
                <td class="text-right">
                    <span class="status-badge ${statusClass}">${hotel.rate}</span>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

// Fungsi untuk update charts (HANYA HOTEL CHART)
function updateCharts(period) {
    const data = periodData[period];
    
    // HAPUS BAGIAN MONTHLY CHART - HANYA UPDATE HOTEL CHART
    hotelChart.data.labels = data.hotels.map(h => h.name);
    hotelChart.data.datasets[0].data = data.hotels.map(h => h.revenue);
    hotelChart.update();
}

// Inisialisasi charts (HANYA HOTEL CHART)
function initCharts() {
    // HAPUS BAGIAN MONTHLY CHART INITIALIZATION
    
    // Chart Pendapatan per Hotel
    const hotelCtx = document.getElementById('hotelRevenueChart').getContext('2d');
    const backgroundColors = [
        'rgba(49, 130, 206, 0.8)',
        'rgba(56, 161, 105, 0.8)',
        'rgba(221, 107, 32, 0.8)',
        'rgba(197, 48, 48, 0.8)'
    ];

    hotelChart = new Chart(hotelCtx, {
        type: 'bar',
        data: {
            labels: periodData[6].hotels.map(h => h.name),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: periodData[6].hotels.map(h => h.revenue),
                backgroundColor: backgroundColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toFixed(0) + 'M';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Pendapatan: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                },
                legend: {
                    display: false
                }
            }
        }
    });
}

// // Event listener untuk dropdown
// document.addEventListener('DOMContentLoaded', function() {
//     initCharts();
    
//     const periodFilter = document.getElementById('periodFilter');
//     if (periodFilter) {
//         periodFilter.addEventListener('change', function() {
//             const selectedPeriod = parseInt(this.value);
//             updateStats(selectedPeriod);
//             updateTable(selectedPeriod);
//             updateCharts(selectedPeriod); // Sekarang hanya update hotel chart
//         });
//     }
// });
</script>
</body>
</html>
