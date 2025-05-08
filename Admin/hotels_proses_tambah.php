<?php
session_start();
require_once '../koneksi/koneksi.php';

function image_upload($img){
    $tmp_loc = $img['tmp_name'];
    $new_name = random_int(11111,99999).$img['name'];

    $new_loc= UPLOAD_SRC.$new_name;

    if(!move_uploaded_file($tmp_loc,$new_loc)){
        header("location: hotels.php?alert=img_upload");
        exit;
    }
    else{
        return $new_name;
    }
}

if(isset($_POST['tambahHotel'])){
    foreach($_POST as $key => $value){
        $_POST[$key] = mysqli_real_escape_string($koneksi,$value);
    }

    $imgpath = image_upload($_FILES['gambar_hotel']);
    
    $query="INSERT INTO `hotels`(`kota_hotel`, `nama_hotel`, `bintang_hotel`, `lokasi_hotel`, `alamat_hotel`, `fasilitas_hotel`, `gambar_hotel`) VALUES ('$_POST[kota_hotel]','$_POST[nama_hotel]','$_POST[bintang_hotel]','$_POST[lokasi_hotel]','$_POST[alamat_hotel]','$_POST[fasilitas_hotel]','$imgpath')";

    if(mysqli_query($koneksi,$query)){
        header("location: hotels.php?success=tambah");
    } else{
        header("location: hotels.php?alert=tambah_gagal");
    }
}

?>