<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kontak | Javast - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="kontak.css">

</head>
<body>
    
    <div class="navbar">
        <div class="logo">
            <img src="logo/Logo_Javast.png" alt="Logo_Javast">

        </div>
        <div>
            <button class="button" onclick="window.location.href='login.php'"> 
                <i class="fas fa-user"></i> Log Out
            </button>
        </div>

    </div>
    
<div class="dashboard">
    <div class="sidebar">
        <div class="sidebar-header">
            <br><br><br><br><br>
            <h1><b>ADMIN PANEL</b></h1>
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
            <div class="menu-item active" onclick="window.location.href='kontak.php'">
                <i class="fas fa-envelope"></i> Kontak Kami
            </div>
            <div class="menu-item" onclick="window.location.href='booking.php'">
                <i class="fas fa-calendar-check"></i> Booking <i class="fa-solid fa-caret-up"></i>
            </div>
        </div>
    </div>
</div>

<div class="main-content">
    <br>
    <br>
    <br>
    <br>
    <div class="header">
        <h1>KONTAK KAMI</h1>
    </div>
    <div class="header-2">
        <h1>Pesan dari user</h1>
    </div>

    <div class="table-controls">
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="userSearch" placeholder="Cari id pengirim, nama pengirim, email pengirim, pesan, waktu,...">
        </div>

         <button class="btn-mark">
            <i class="fa-solid fa-check-double"></i> Tandai Dibaca Semua
        </button> 

        <button class="btn-deleteAll">
            <i class="fas fa-trash"></i> Hapus Semua
        </button> 
    </div>
    
    

    <div class="table-container">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Pengirim</th>
                    <th>Email Pengirim</th>
                    <th>Pesan</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <tr>
            <td>101</td>
            <td>Nadyaaaaaaaa</td>
            <td>nadyakameela162@gmail.com</td>
            <td>websitenya keren bangetttt</td>
            <td>01-02-2025</td>
            <td>
                <button class="btn-edit"><i class="fa-solid fa-check"></i> Tandai Dibaca</button><br><br>
                <button class="btn-delete"><i class="fas fa-trash"></i> Hapus</button>
            </td>
            </tr>

            <tr>
            <td>102</td>
            <td>Arin</td>
            <td>arinarin@gmail.com</td>
            <td>hotelnya jelek, perbaiki lg yh kk</td>
            <td>05-10-2025</td>
            <td>
                <button class="btn-edit"><i class="fa-solid fa-check"></i> Tandai Dibaca</button><br><br>
                <button class="btn-delete"><i class="fas fa-trash"></i> Hapus</button>
            </td>
            </tr>

            </tbody>
        </table>
    </div>
</div>
</div>

</div>

<script>
    document.getElementById('userSearch').addEventListener('input', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('.crud-table tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});
</script>

</body>
</html>