<?php
session_start();
require_once '../Koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

$id_kamar = isset($_GET['id_kamar']) ? intval($_GET['id_kamar']) : 0;

$kamar = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT k.*, h.nama_hotel
    FROM kamar k
    JOIN hotels h ON k.id_hotel = h.id_hotel
    WHERE k.id_kamar = $id_kamar"));

if (!$kamar) {
    $_SESSION['errors'] = ["Kamar tidak ditemukan!"];
    header("Location: kamar.php");
    exit;
}
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kamar | Javast - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="kamar_detail_gambar.css">

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
        <h1>Edit Kamar</h1>
    </div>

  <div >
    <div>
      
<div class="container">
    
    <div class="header-actions">
        <h3><?= htmlspecialchars($kamar['nama_kamar']) ?></h3>
        <small>Hotel: <?= htmlspecialchars($kamar['nama_hotel']) ?></small>
    </div>


    <form id="editKamarForm" action="kamar_proses_edit.php" class="room-form" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_kamar" id="kamarIdInput" value="<?= htmlspecialchars($kamar['id_kamar'] ?? '') ?>">
    <input type="hidden" name="editKamar" value="1">

            <div class="form-row">

            <div class="form-group">
                <label>Nama Kamar</label>
                <input type="text" name="nama_kamar" id="editnama" value="<?= htmlspecialchars($kamar['nama_kamar'] ?? '') ?>" placeholder="Nama Kamar Hotel..." required>
            </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tipe Kasur</label>
                    <input type="text" name="tipe_kasur" id="edittipekasur" value="<?= htmlspecialchars($kamar['tipe_kasur'] ?? '') ?>" placeholder="Tipe Kasur Kamar..." required>
                </div>

                <div class="form-group">
                    <label>Ukuran (m²)</label>
                    <input type="number" name="ukuran_kamar" id="editukuran" value="<?= htmlspecialchars($kamar['ukuran_kamar'] ?? '') ?>" placeholder="Ukuran Kamar Kamar..." required>
                </div>
            </div>

            <div class="form-group">
                <label>Kapasitas</label>
                <input type="number" name="kapasitas_kamar" id="editkapasitas" value="<?= htmlspecialchars($kamar['kapasitas_kamar'] ?? '') ?>" placeholder="Kapasitas Kamar..." min="1" max="10" required>
            </div>

            <div class="form-group">
                <label>Fasilitas</label>
                <textarea name="fasilitas_kamar" id="editfasilitas" placeholder="Fasilitas Kamar..." required><?php echo htmlspecialchars($kamar['fasilitas_kamar'] ?? ''); ?></textarea>
            </div>
 
            <div class="form-row">
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga_kamar" id="editharga" value="<?= htmlspecialchars($kamar['harga_kamar'] ?? '') ?>" placeholder="Harga Kamar..." required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jumlah Kamar</label>
                    <input type="number" name="jumlah_kamar" id="editjumlahkamar" value="<?= htmlspecialchars($kamar['jumlah_kamar'] ?? '') ?>" placeholder="Jumlah Kamar..." required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jumlah Dewasa</label>
                    <input type="number" name="jumlah_dewasa" id="editjumlahdewasa" value="<?= htmlspecialchars($kamar['jumlah_dewasa'] ?? '') ?>" placeholder="Jumlah Kapasitas Maksimal untuk Orang Dewasa..." required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jumlah Anak</label>
                    <input type="number" name="jumlah_anak" id="editjumlahanak" value="<?= htmlspecialchars($kamar['jumlah_anak'] ?? '') ?>" placeholder="Jumlah Kapasitas Maksimal untuk Anak-anak..." required>
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi Kamar</label>
                <textarea name="deskripsi_kamar" id="editdeskripsi" placeholder="Deskripsi Kamar..." required><?php echo htmlspecialchars($kamar['deskripsi_kamar'] ?? ''); ?></textarea>
            </div>


            <div class="form-actions">
                <button type="button" class="btn-cancel"  onclick="window.location.href='kamar.php'">Batal</button>
                <button type="submit" class="btn-submit" id="submit">Simpan Perubahan</button>
              </div>
        </form>
</div>


<script>
    // memunculkan popup edit dengan data hotel
function openPopupedit(id_kamar) {
    console.log('Mengedit kamar ID:', id_kamar);
    
    // Pastikan ini mengisi nilai form
    document.getElementById('kamarIdInput').value = id_kamar;
    
    fetch('kamar_data.php?id_kamar=' + id_kamar)
    .then(response => {
        if(!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if(data.success) {
            // Isi form dengan data yang diterima
            document.getElementById('editnama').value = data.nama_kamar;
            document.getElementById('edittipekasur').value = data.tipe_kasur;
            document.getElementById('editukuran').value = data.ukuran_kamar;
            document.getElementById('editkapasitas').value = data.kapasitas_kamar;
            document.getElementById('editfasilitas').value = data.fasilitas_kamar;
            document.getElementById('editharga').value = data.harga_kamar;
            document.getElementById('editjumlahkamar').value = data.jumlah_kamar;
            document.getElementById('editjumlahdewasa').value = data.jumlah_dewasa;
            document.getElementById('editjumlahanak').value = data.jumlah_anak;
            document.getElementById('editdeskripsi').value = data.deskripsi_kamar;

            // Tambahkan input hidden untuk id_kamar
            const form = document.querySelector('.room-form');
            let idInput = form.querySelector('input[name="id_kamar"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id_kamar';
                form.prepend(idInput);
            }
            idInput.value = id_kamar;

            // Tampilkan nama hotel di header popup
            document.querySelector('#popupedit h3').textContent = data.nama_kamar;
            
            // Tampilkan popup
            document.getElementById("popupedit").style.display = "flex";
        } else {
            alert(data.error || 'Gagal memuat data kamar');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat data: ' + error.message);
    });
}

function closePopupedit() {
    document.getElementById("popupedit").style.display = "none";
}

document.getElementById('editKamarForm').addEventListener('submit', function(e) {
    console.log('Form submitted!'); 
});

document.getElementById('kamarEditForm').addEventListener('submit', function(e) {
    console.log('Form submit diproses...');
});


</script>
</body>
</html>