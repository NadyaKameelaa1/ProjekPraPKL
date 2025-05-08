<?php
session_start();
require_once '../Koneksi/koneksi.php';

$sql = "SELECT * FROM kamar";
$query = mysqli_query($koneksi,$sql);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kamar | Javast - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="kamar.css">

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
            <div class="menu-item active" onclick="window.location.href='kamar.php'">
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
        <h1>KAMAR</h1>
    </div>
    <div class="header-2">
        <h1>Pilihan Kamar</h1>
    </div>

    <div class="table-controls">
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="userSearch" placeholder="Cari id kamar, id hotel, nama kamar, tipe kasur, ukuran, fasilitas, harga, jumlah kamar, tamu, deskripsi...">
        </div>

         <button class="btn-add" class="button" onclick="openPopup()">
            <i class="fas fa-plus"></i> Tambah
        </button> 
    </div>
    
    <div class="table-container">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Id Kamar</th>
                    <th>Id Hotel</th>
                    <th>Nama Kamar</th>
                    <th>Tipe Kasur</th>
                    <th>Ukuran</th>
                    <th>Kapasitas</th>
                    <th>Fasilitas</th>
                    <th>Harga</th>
                    <th>Jumlah Kamar</th>
                    <th>Jumlah Dewasa</th>
                    <th>Jumlah Anak</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $fetch_src=FETCH_SRC;

            while($kamar=mysqli_fetch_assoc($query)){ 
            
            echo<<<kamar
            <td>$kamar[id_kamar]</td>
            <td>$kamar[id_hotel]</td>
            <td>$kamar[nama_kamar]</td>
            <td>$kamar[tipe_kasur]</td>
            <td>$kamar[ukuran_kamar]</td>
            <td>$kamar[kapasitas_kamar]</td>
            <td>$kamar[fasilitas_kamar]</td>
            <td>$kamar[harga_kamar]</td>
            <td>$kamar[jumlah_kamar]</td>
            <td>$kamar[jumlah_dewasa]</td>
            <td>$kamar[jumlah_anak]</td>
            <td>$kamar[deskripsi_kamar]</td>
            <td>
                <button class="btn-edit" onclick="openPopupedit()"><a href="id_kamar=$kamar[id_kamar]"><i class="fa-solid fa-pen-to-square"></i> Edit</a></button>
                <button class="btn-delete"><a href="kamar_hapus.php?id_hotel=$kamar[id_kamar]"><i class="fas fa-trash"></i>Hapus</a></button>
            </td>
            </tr>
            
            kamar;
            }  ?>
            </tbody>
        </table>
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
        <h2>TAMBAH KAMAR</h2>
        <span class="close-btn" onclick="closePopup()">&times;</span>
      </div>
  
      <div class="form-container">
        <form class="room-form">
            <div class="form-group">
                <label for="hotel-id">Id Hotel</label>
                <div class="hotel-selection">
                    <select id="hotel-id">
                        <option value="201">201 - Semarang</option>
                        <option value="202">202 - Semarang</option>
                        <option value="203">203 - Purwokerto</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="room-name">Nama Kamar</label>
                <input type="text" id="room-name" placeholder="Nama Kamar Hotel...">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="room-name">Tipe Kasur</label>
                    <input type="text" id="room-bed" placeholder="Tipe Kasur Hotel...">
                </div>

                <div class="form-group">
                    <label for="room-size">Ukuran (m²)</label>
                    <input type="text" id="room-size" placeholder="Ukuran Kamar Hotel...">
                </div>
            </div>

            <div class="form-group">
                <label for="capacity">Kapasitas</label>
                <input type="number" id="capacity" placeholder="Kapasitas Hotel..." min="1" max="10">
            </div>

            <div class="form-group">
                <label for="facilities">Fasilitas</label>
                <textarea id="facilities" placeholder="Fasilitas Hotel..."></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Harga</label>
                    <input type="text" id="price" placeholder="Harga Hotel...">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" placeholder="Deskripsi Hotel..."></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closePopup()">Batal</button>
                <button type="submit" class="btn-submit">Tambah Kamar</button>
              </div>
        </form>
    </div>
</div>


  <!-- Popup edit -->
  <div class="popup-overlay" id="popupedit">
    <div class="popup-content">
      <div class="popup-header">
        <div>
        <h2>EDIT KAMAR</h2>
        <h3>New Deluxe Twin Room Only</h3>
        </div>
        <span class="close-btn" onclick="closePopupedit()">&times;</span>
      </div>
  
        <form class="room-form">
            <div class="form-row">
            <div class="form-group">
                <label for="hotel-id">Id Hotel</label>
                <div class="hotel-selection">
                    <select id="hotel-id">
                        <option value="201" selected>201 - Semarang</option>
                        <option value="202">202 - Semarang</option>
                        <option value="203">203 - Purwokerto</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="room-name">Nama Kamar</label>
                <input type="text" id="room-name" placeholder="Nama Kamar Hotel...">
            </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="room-name">Tipe Kasur</label>
                    <input type="text" id="room-bed" placeholder="Tipe Kasur Hotel...">
                </div>

                <div class="form-group">
                    <label for="room-size">Ukuran (m²)</label>
                    <input type="text" id="room-size" placeholder="Ukuran Kamar Hotel...">
                </div>
            </div>

            <div class="form-group">
                <label for="capacity">Kapasitas</label>
                <input type="number" id="capacity" placeholder="Kapasitas Hotel..." min="1" max="10">
            </div>

            <div class="form-group">
                <label for="facilities">Fasilitas</label>
                <textarea id="facilities" placeholder="Fasilitas Hotel..."></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Harga</label>
                    <input type="text" id="price" placeholder="Harga Hotel...">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" placeholder="Deskripsi Hotel..."></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closePopupedit()">Batal</button>
                <button type="submit" class="btn-submit">Tambah Kamar</button>
              </div>
        </form>
    </div>
</div>


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