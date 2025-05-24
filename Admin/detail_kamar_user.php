<?php
$id_kamar = $_GET['id'] ?? 0;
$kamar = mysqli_fetch_assoc(mysqli_query($koneksi, 
    "SELECT k.*, kg.* FROM kamar k
    LEFT JOIN kamar_gambar kg ON k.id_kamar = kg.id_kamar
    WHERE k.id_kamar = $id_kamar"));

if ($kamar) {
    echo '<div class="room-details">';
    echo '<h2>'.$kamar['name_kamar'].'</h2>';
    
    // Tampilkan semua gambar yang ada
    for ($i = 1; $i <= 5; $i++) {
        $field = "gambar".chr(64+$i);
        if (!empty($kamar[$field])) {
            echo '<img src="/JAVAST/Admin/Gambar/Kamar/'.$kamar[$field].'" class="room-image">';
        }
    }
    
    echo '</div>';
}
?>