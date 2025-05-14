<?php
session_start();
require_once '../koneksi/koneksi.php';

if(isset($_POST['upload'])) {
    $id_kamar = $_POST['id_kamar'];
    $upload_dir = '/JAVAST/Admin/Gambar/GambarKamar/    ';
    
    // Buat folder jika belum ada
    if(!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Array untuk menyimpan nama file
    $gambar_data = [
        'id_kamar' => $id_kamar,
        'gambarA' => NULL,
        'gambarB' => NULL,
        'gambarC' => NULL,
        'gambarD' => NULL,
        'gambarE' => NULL
    ];

    // Proses upload file
    $i = 0;
    $columns = ['gambarA', 'gambarB', 'gambarC', 'gambarD', 'gambarE'];
    
    foreach($_FILES['gambar']['tmp_name'] as $key => $tmp_name) {
        if($i >= 5) break; // Maksimal 5 gambar
        
        if(!empty($tmp_name)) {
            $file_name = $_FILES['gambar']['name'][$key];
            $file_tmp = $tmp_name;
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
            
            if(in_array($file_ext, $allowed_ext)) {
                $new_file_name = uniqid('img_', true).'.'.$file_ext;
                $upload_path = $upload_dir.$new_file_name;
                
                if(move_uploaded_file($file_tmp, $upload_path)) {
                    $gambar_data[$columns[$i]] = $new_file_name;
                    $i++;
                }
            }
        }
    }

    // Insert ke database
    $columns = [];
    $values = [];
    
    foreach($gambar_data as $col => $val) {
        if($val !== NULL) {
            $columns[] = $col;
            $values[] = "'".mysqli_real_escape_string($koneksi, $val)."'";
        }
    }
    
    if(!empty($columns)) {
        $sql = "INSERT INTO kamar_gambar (".implode(', ', $columns).") 
                VALUES (".implode(', ', $values).")";
        
        if(mysqli_query($koneksi, $sql)) {
            $_SESSION['success'] = "Gambar berhasil diupload!";
        } else {
            $_SESSION['error'] = "Error: ".mysqli_error($koneksi);
        }
    } else {
        $_SESSION['error'] = "Tidak ada gambar yang valid diupload";
    }
    
    header("Location: kamar.php?tambahgambar=sukses");
    exit();
}