<?php
session_start();
require_once '../Koneksi/koneksi.php';

$sql = "SELECT * FROM hotels";
$query = mysqli_query($koneksi,$sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hotel | Javast - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="hotels.css">

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
            <div class="menu-item active" onclick="window.location.href='hotels.php'">
                <i class="fas fa-hotel"></i> Hotel
            </div>
            <div class="menu-item" onclick="window.location.href='kamar.php'">
                <i class="fas fa-bed"></i> Kamar
            </div>
            <div class="menu-item" onclick="window.location.href='kontak.php'">
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
        <h1>HOTEL</h1>
    </div>
    <div class="header-2">
        <h1>Pilihan Hotel</h1>
    </div>

    <div class="table-controls">
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="userSearch" placeholder="Cari kota, nama hotel, lokasi, alamat, fasilitas...">
        </div>

         <button class="btn-add" class="button" onclick="openPopup()">
            <i class="fas fa-plus"></i> Tambah
        </button> 
    </div>
  

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>  
    <div class="table-container">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>id Hotel</th>
                    <th>Kota Hotel</th>
                    <th>Nama Hotel</th>
                    <th>Bintang</th>
                    <th>Lokasi</th>
                    <th>Alamat</th>
                    <th>Fasilitas</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $fetch_src=FETCH_SRC;

            while($hotels=mysqli_fetch_assoc($query)){ 
            
            echo<<<hotels
            <td>$hotels[id_hotel]</td>
            <td>$hotels[kota_hotel]</td>
            <td>$hotels[nama_hotel]</td>
            <td>$hotels[bintang_hotel]</td>
            <td>$hotels[lokasi_hotel]</td>
            <td>$hotels[alamat_hotel]</td>
            <td>$hotels[fasilitas_hotel]</td>
            <td><img src="$fetch_src$hotels[gambar_hotel]" width="150px"></td>
            <td>
                <button class="btn-edit" onclick="openPopupedit()"><a href="id_hotel=$hotels[id_hotel]"><i class="fa-solid fa-pen-to-square"></i> Edit</a></button>
                <button class="btn-delete"><a href="hotels_hapus.php?id_hotel=$hotels[id_hotel]"><i class="fas fa-trash"></i> Hapus</a></button>
            </td>
            </tr>
            
            hotels;
            }  ?>
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


  
  <!-- Popup tambah -->
  <div class="popup-overlay" id="popup">
    <div class="popup-content">
      <div class="popup-header">
        <h2>TAMBAH HOTEL</h2>
        <span class="close-btn" onclick="closePopup()">&times;</span>
      </div>
  
      <form action="hotels_proses_tambah.php" method="POST" class="hotel-form" enctype="multipart/form-data">
        <div class="form-group">
          <label>Kota Hotel</label>
          <input type="text" name="kota_hotel" placeholder="Kota Hotel" required>
        </div>
        
        <div class="form-group">
          <label>Nama Hotel</label>
          <input type="text" name="nama_hotel" placeholder="Nama Hotel" required>
        </div>
        
        <div class="form-group">
          <label>Bintang Hotel (1-5)</label>
          <input type="number" name="bintang_hotel" placeholder="Bintang Hotel" max="5" required>
        </div>
        
        <div class="form-group">
          <label>Lokasi</label>
          <input type="text" name="lokasi_hotel" placeholder="Lokasi Hotel" required>
        </div>
        
        <div class="form-group">
          <label>Alamat Hotel</label>
          <textarea name="alamat_hotel" placeholder="Alamat Hotel" required></textarea>
        </div>
        
        <div class="form-group">
          <label>Fasilitas Hotel</label>
          <textarea name="fasilitas_hotel" placeholder="Fasilitas Hotel" required></textarea>
        </div>
        
        <div class="form-group">
          <label>Tambah Gambar Hotel (Max: 1)</label>
          <input type="file" name="gambar_hotel" id="hotel-image" accept=".jpg,.png,.svg" required>
        </div>
        
        <div class="form-actions">
          <button type="button" class="btn-cancel" onclick="closePopup()">Batal</button>
          <button type="submit" class="btn-submit" name="tambahHotel">Tambah Hotel</button>
        </div>
      </form>
    </div>
  </div>
  
  

<div class="popup-overlay" id="popupedit">
    <div class="popup-content">
      <div class="popup-header">
        <div>
          <h2>EDIT HOTEL</h2>
          <h3>Gumaya Tower Hotel</h3>
        </div>
        <span class="close-btn" onclick="closePopupedit()">&times;</span>
      </div>
         

      <!-- Tambahkan input hidden untuk ID hotel -->
<form action="hotels_proses_edit.php" method="POST" class="hotel-form" enctype="multipart/form-data">
    
    <div class="form-row">
        <div class="form-group">
            <label>Kota Hotel</label>
            <input type="text" name="kota_hotel" id="editkota" required>
        </div>

        <div class="form-group">
            <label>Nama Hotel</label>
            <input type="text" name="nama_hotel" id="editnama" required>
        </div>
    </div>

    <div class="form-group">
        <label>Bintang Hotel (1-5)</label>
        <input type="number" name="bintang_hotel" id="editbintang" min="1" max="5" required>
    </div>

    <div class="form-group">
        <label>Lokasi</label>
        <input type="text" name="lokasi_hotel" id="editlokasi" required>
    </div>

    <div class="form-group">
        <label>Alamat Hotel</label>
        <textarea name="alamat_hotel" id="editalamat" required></textarea>
    </div>

    <div class="form-group">
        <label>Fasilitas Hotel</label>
        <textarea name="fasilitas_hotel" id="editfasilitas" required></textarea>
    </div>

    <img src="" id="editgambar" width="100%" class="mb-3"><br>
    <div class="form-group">
          <label>Edit Gambar Hotel (Max: 1)</label>
          <input type="file" name="gambar_hotel" id="hotel-image" accept=".jpg,.png,.svg" required>
    </div>

    <div class="form-actions">
        <button type="button" class="btn-cancel" onclick="closePopupedit()">Batal</button>
        <button type="submit" class="btn-submit" name="edithotel">Simpan Perubahan</button>
    </div>
    </form>
    </div>
  </div>

<?php

if(isset($_GET['popupedit']) && $_GET['popupedit']>0){
$query="SELECT * FROM `hotels` WHERE `id_hotel` ='$id_hotel'";
$result=mysqli_query($koneksi, $query);
$hotels=mysqli_fetch_assoc($result);

echo"
    <script>
        var edithotel = new bootstrap.Modal(document.getElementById('edithotel'), {
    });
    document.querySelector('#editkota').value=`$hotels[kota_hotel]`;
    document.querySelector('#editnama').value=`$hotels[nama_hotel]`;
    document.querySelector('#editbintang').value=`$hotels[bintang_hotel]`;
    document.querySelector('#editlokasi').value=`$hotels[lokasi_hotel]`;
    document.querySelector('#editalamat').value=`$hotels[alamat_hotel]`;
    document.querySelector('#editfasilitas').value=`$hotels[fasilitas_hotel]`;
    document.querySelector('#hotel-image').src=`$fetch_src$hotels[gambar_hotel]`;
    editproduct.show();
    </script>
";

}
?>

    <script>

        // Script untuk menampilkan nama file yang dipilih
        document.getElementById('hotel-image').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
            document.querySelector('.file-chosen').textContent = fileName;
        });
 
    // tambah
    function openPopup() {
      document.getElementById("popup").style.display = "flex";
    }
    
    function closePopup() {
      document.getElementById("popup").style.display = "none";
    }

    // edit
    function openPopupedit() {
      document.getElementById("popupedit").style.display = "flex";
    }
    
    function closePopupedit() {
      document.getElementById("popupedit").style.display = "none";
    }
  </script>


</body>
</html>