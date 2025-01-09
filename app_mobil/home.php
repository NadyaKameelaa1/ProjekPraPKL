<?php
include "koneksi.php";
$sql = "SELECT * FROM mobil";
$query = mysqli_query($koneksi, $sql);

$id = $_POST['id'];
$merk = $_POST['merk'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];

$a = "INSERT INTO mobil (id,merk,harga,stok) VALUES ('$id','$merk','$harga','$stok')";

$qsl = mysqli_query($koneksi, $a);

if($qsl){
    header("location:index.php?simpan=sukses");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobil | Home</title>
</head>
<body>
    <h2>Data Mobil</h2>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label>ID</label>
        <input type="text" name="id"><br>
        <label>MERK</label><br>
        <input type="text" name="merk"><br>
        <label>HARGA</label>
        <input type="text" name="harga"><br>
        <label>STOK</label>
        <input type="text" name="stok"><br>

        <input type="submit" name="simpan" value="SIMPAN">
    </form>
</body>
</html>