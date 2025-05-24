<?php
session_start();
require_once '../koneksi/koneksi.php';

// Tampilkan pesan error/sukses
if (isset($_SESSION['errors'])) {
    echo '<div class="alert alert-danger">';
    foreach($_SESSION['errors'] as $error) {
        echo '<p>'.$error.'</p>';
    }
    echo '</div>';
    unset($_SESSION['errors']);
}

if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
    unset($_SESSION['success']);
}

// Validasi ID Kamar
$id_kamar = isset($_GET['id_kamar']) ? intval($_GET['id_kamar']) : 0;

// Cek apakah kamar ada
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // PERBAIKAN 1: Gunakan path absolut dan pastikan folder ada
    $upload_dir = $_SERVER['DOCUMENT_ROOT'].'/JAVAST/Admin/Gambar/Kamar/';
    
    // Buat folder jika belum ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true); // PERBAIKAN 2: Set permission 0755
    }

    // PERBAIKAN 3: Cek apakah folder writable
    if (!is_writable($upload_dir)) {
        $_SESSION['errors'] = ["Folder upload tidak memiliki izin write!"];
        header("Location: kamar_detail_gambar.php?id_kamar=$id_kamar");
        exit;
    }

    $gambar_fields = ['gambarA', 'gambarB', 'gambarC', 'gambarD', 'gambarE'];
    $data_gambar = [];

    foreach ($gambar_fields as $field) {
        if (!empty($_FILES[$field]['name']) && $_FILES[$field]['error'] == UPLOAD_ERR_OK) {
            // PERBAIKAN 4: Validasi file upload
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            $file_type = mime_content_type($_FILES[$field]['tmp_name']);
            
            if (!in_array($file_type, $allowed_types)) {
                $_SESSION['errors'][] = "File ".$_FILES[$field]['name']." bukan gambar valid!";
                continue;
            }

            $ext = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
            $filename = 'kamar_'.$id_kamar.'_'.$field.'_'.time().'.'.$ext;
            $target = $upload_dir.$filename;

            // PERBAIKAN 5: Cek apakah file berhasil diupload
            if (move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
                $data_gambar[$field] = $filename;
            } else {
                $_SESSION['errors'][] = "Gagal upload ".$_FILES[$field]['name'];
            }
        }
    }

    // Update atau insert data gambar
    $check = mysqli_query($koneksi, "SELECT * FROM kamar_gambar WHERE id_kamar = $id_kamar");

    if (mysqli_num_rows($check) > 0) {
        // Update
        $update_fields = [];
        foreach ($data_gambar as $field => $filename) {
            $update_fields[] = "$field = '$filename'";
        }

        if (!empty($update_fields)) {
            mysqli_query($koneksi,
                "UPDATE kamar_gambar SET ".implode(', ', $update_fields)."
                WHERE id_kamar = $id_kamar");
        }
    } else {
        // Insert baru
        mysqli_query($koneksi,
            "INSERT INTO kamar_gambar (id_kamar, gambarA, gambarB, gambarC, gambarD, gambarE)
            VALUES ($id_kamar,
            '".($data_gambar['gambarA'] ?? '')."',
            '".($data_gambar['gambarB'] ?? '')."',
            '".($data_gambar['gambarC'] ?? '')."',
            '".($data_gambar['gambarD'] ?? '')."',
            '".($data_gambar['gambarE'] ?? '')."')");
    }

    $_SESSION['success'] = "Gambar kamar berhasil diupdate!";
    header("Location: kamar_detail_gambar.php?id_kamar=$id_kamar");
    exit;
}

// Ambil data gambar yang sudah ada
$gambar = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT * FROM kamar_gambar WHERE id_kamar = $id_kamar"));
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
        <h1>Edit Gambar Kamar</h1>
    </div>

  <div >
    <div>
      
<div class="container">
    <div class="header-actions">
        <h2><?= htmlspecialchars($kamar['nama_kamar']) ?></h2>
        <small>Hotel: <?= htmlspecialchars($kamar['nama_hotel']) ?></small>
    </div>


      <!-- <div class="form-container"> -->
    <form class="image-upload-form" method="POST" enctype="multipart/form-data">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <?php 
            $field = 'gambar'.chr(64+$i);
            $current = $gambar[$field] ?? '';
            ?>
            <div class="form-group">
                <label>Gambar <?= $i ?></label>
                <?php if ($current): ?>
                    <!-- PERBAIKAN 6: Pastikan path gambar benar -->
                    <img src="/JAVAST/Admin/Gambar/Kamar/<?= $current ?>" 
                         class="preview-image" 
                         style="max-width: 200px; display: block; margin: 10px 0;">
                <?php endif; ?>
                <input type="file" name="<?= $field ?>" accept="image/*">
                <!-- PERBAIKAN 7: Tampilkan nama file jika ada -->
                <?php if ($current): ?>
                    <div class="current-file">File saat ini: <?= $current ?></div>
                <?php endif; ?>
            </div>
        <?php endfor; ?>

        <div class="form-actions">
            <button type="button" onclick="window.history.back()" class="btn-cancel">Batal</button>
            <button type="submit" class="btn-submit">Simpan Gambar</button>
        </div>
    </form>
</div>


<script>
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

// Fungsi untuk membuka popup gambar
function openPopupgambar(id_kamar) {
    console.log('Mencoba membuka popup untuk kamar ID:', id_kamar); // Debug 1
    
    fetch(`kamar_get_data.php?id_kamar=${id_kamar}`)
        .then(response => {
            console.log('Response status:', response.status); // Debug 2
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            console.log('Data diterima:', data); // Debug 3
            if (data && data.nama_kamar) {
                document.querySelector('#popup-gambar .nama-kamar-display').textContent = data.nama_kamar;
                document.querySelector('#popup-gambar input[name="id_kamar"]').value = id_kamar;
                document.getElementById("popup-gambar").style.display = "flex";
            } else {
                console.error('Data tidak valid atau nama_kamar tidak ada');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback: Tampilkan popup meski tanpa data
            document.getElementById("popup-gambar").style.display = "flex";
        });
}

// Fungsi penutup
function closePopupgambar() {
    document.getElementById('popup-gambar').style.display = 'none';
}

// Event listener untuk form submit
document.querySelector('.room-form')?.addEventListener('submit', function() {
    setTimeout(closePopupgambar, 1000);
});

//----------------------------------------------------------------

// // Membuka popup dengan data kamar yang dipilih
// function openAddImagePopup(id_kamar, nama_kamar) {
//     const popup = document.getElementById('image-popup');
//     popup.querySelector('.nama-kamar-display').textContent = nama_kamar;
//     popup.querySelector('input[name="id_kamar"]').value = id_kamar;
//     popup.style.display = 'block';
// }

</script>

</body>
</html>