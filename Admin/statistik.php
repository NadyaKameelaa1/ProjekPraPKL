<?php
session_start();
require_once '../Koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pendapatan Hotel - Admin Dashboard</title>
    <link rel="stylesheet" href="statistik.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <option value="3">3 Bulan Terakhir</option>
                <option value="6">6 Bulan Terakhir</option>
                <option value="12">1 Tahun Terakhir</option>
            </select>
            <button class="btn btn-primary">
                <i class="fas fa-download"></i> Export Excel
            </button>
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
            <br><br><br><br><br>
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
                    <i class="fa-solid fa-chart-simple"></i> Statistik
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
                        <p class="stats-label">Total Pendapatan</p>
                        <p class="stats-value stats-green">Rp 1.020.000.000</p>
                        <p class="stats-subtext">6 bulan terakhir</p>
                    </div>
                    <div class="stats-icon bg-green">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>

         

        <!-- Charts Section -->
        <div class="charts-grid">
            <!-- Grafik Pendapatan Bulanan -->
            <div class="chart-card">
                <h3>Tren Pendapatan Bulanan</h3>
                <div class="chart-container">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
            </div>

            <!-- Grafik Pendapatan per Hotel -->
            <div class="chart-card">
                <h3>Pendapatan per Hotel</h3>
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
                            <th class="text-right">Success Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-left">
                                <div class="hotel-name">Grand Hotel Jakarta</div>
                            </td>
                            <td class="text-right">
                                <div class="revenue-value">Rp 320.000.000</div>
                            </td>
                            <td class="text-right">1,250</td>
                            <td class="text-right">1,100</td>
                            <td class="text-right">Rp 290.909</td>
                            <td class="text-right">
                                <span class="status-badge status-high">88.0%</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left">
                                <div class="hotel-name">Luxury Resort Bali</div>
                            </td>
                            <td class="text-right">
                                <div class="revenue-value">Rp 280.000.000</div>
                            </td>
                            <td class="text-right">980</td>
                            <td class="text-right">850</td>
                            <td class="text-right">Rp 329.412</td>
                            <td class="text-right">
                                <span class="status-badge status-high">86.7%</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left">
                                <div class="hotel-name">Beach Hotel Lombok</div>
                            </td>
                            <td class="text-right">
                                <div class="revenue-value">Rp 232.000.000</div>
                            </td>
                            <td class="text-right">1,120</td>
                            <td class="text-right">950</td>
                            <td class="text-right">Rp 244.211</td>
                            <td class="text-right">
                                <span class="status-badge status-high">84.8%</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left">
                                <div class="hotel-name">City Inn Surabaya</div>
                            </td>
                            <td class="text-right">
                                <div class="revenue-value">Rp 188.000.000</div>
                            </td>
                            <td class="text-right">1,580</td>
                            <td class="text-right">1,400</td>
                            <td class="text-right">Rp 134.286</td>
                            <td class="text-right">
                                <span class="status-badge status-medium">88.6%</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    // Data untuk berbagai periode
    const periodData = {
        3: {
            stats: {
                totalRevenue: 'Rp 510.000.000',
                totalBooking: '2,465',
                bookingSukses: '2,150',
                successRate: '87.2%',
                period: '3 bulan terakhir'
            },
            monthly: {
                labels: ['April', 'Mei', 'Juni'],
                values: [161000000, 175000000, 174000000]
            },
            hotels: [
                { name: 'Grand Hotel Jakarta', revenue: 160000000, booking: 625, selesai: 550, rate: '88.0%' },
                { name: 'Luxury Resort Bali', revenue: 140000000, booking: 490, selesai: 425, rate: '86.7%' },
                { name: 'Beach Hotel Lombok', revenue: 116000000, booking: 560, selesai: 475, rate: '84.8%' },
                { name: 'City Inn Surabaya', revenue: 94000000, booking: 790, selesai: 700, rate: '88.6%' }
            ]
        },
        6: {
            stats: {
                totalRevenue: 'Rp 1.020.000.000',
                totalBooking: '4,930',
                bookingSukses: '4,300',
                successRate: '87.2%',
                period: '6 bulan terakhir'
            },
            monthly: {
                labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'],
                values: [140000000, 157000000, 161000000, 175000000, 187000000, 200000000]
            },
            hotels: [
                { name: 'Grand Hotel Jakarta', revenue: 320000000, booking: 1250, selesai: 1100, rate: '88.0%' },
                { name: 'Luxury Resort Bali', revenue: 280000000, booking: 980, selesai: 850, rate: '86.7%' },
                { name: 'Beach Hotel Lombok', revenue: 232000000, booking: 1120, selesai: 950, rate: '84.8%' },
                { name: 'City Inn Surabaya', revenue: 188000000, booking: 1580, selesai: 1400, rate: '88.6%' }
            ]
        },
        12: {
            stats: {
                totalRevenue: 'Rp 2.040.000.000',
                totalBooking: '9,860',
                bookingSukses: '8,600',
                successRate: '87.2%',
                period: '1 tahun terakhir'
            },
            monthly: {
                labels: ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                values: [120000000, 135000000, 145000000, 155000000, 165000000, 175000000, 140000000, 157000000, 161000000, 175000000, 187000000, 200000000]
            },
            hotels: [
                { name: 'Grand Hotel Jakarta', revenue: 640000000, booking: 2500, selesai: 2200, rate: '88.0%' },
                { name: 'Luxury Resort Bali', revenue: 560000000, booking: 1960, selesai: 1700, rate: '86.7%' },
                { name: 'Beach Hotel Lombok', revenue: 464000000, booking: 2240, selesai: 1900, rate: '84.8%' },
                { name: 'City Inn Surabaya', revenue: 376000000, booking: 3160, selesai: 2800, rate: '88.6%' }
            ]
        }
    };

    let monthlyChart, hotelChart;

    // Fungsi untuk format rupiah
    function formatRupiah(angka) {
        return 'Rp ' + angka.toLocaleString('id-ID');
    }

    // Fungsi untuk update statistik
    function updateStats(period) {
        const data = periodData[period];
        
        // Update stats cards
        document.querySelector('.stats-value.stats-green').textContent = data.stats.totalRevenue;
        document.querySelector('.stats-value.stats-blue').textContent = data.stats.totalBooking;
        document.querySelector('.stats-value.stats-purple').textContent = data.stats.bookingSukses;
        
        // Update subtext
        document.querySelector('.stats-green').nextElementSibling.textContent = data.stats.period;
    }

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

    // Fungsi untuk update charts
    function updateCharts(period) {
        const data = periodData[period];
        
        // Update monthly chart
        monthlyChart.data.labels = data.monthly.labels;
        monthlyChart.data.datasets[0].data = data.monthly.values;
        monthlyChart.update();
        
        // Update hotel chart
        hotelChart.data.labels = data.hotels.map(h => h.name);
        hotelChart.data.datasets[0].data = data.hotels.map(h => h.revenue);
        hotelChart.update();
    }

    // Inisialisasi charts
    function initCharts() {
        // Chart Pendapatan Bulanan
        const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: periodData[6].monthly.labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: periodData[6].monthly.values,
                    borderColor: '#3182ce',
                    backgroundColor: 'rgba(49, 130, 206, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3182ce',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
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
                    }
                }
            }
        });

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

    // Event listener untuk dropdown
    document.addEventListener('DOMContentLoaded', function() {
        initCharts();
        
        const periodFilter = document.getElementById('periodFilter');
        periodFilter.addEventListener('change', function() {
            const selectedPeriod = parseInt(this.value);
            updateStats(selectedPeriod);
            updateTable(selectedPeriod);
            updateCharts(selectedPeriod);
        });
    });
</script>
</body>
</html>
