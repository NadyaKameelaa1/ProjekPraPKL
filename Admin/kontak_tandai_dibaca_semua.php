<?php
require_once '../koneksi/koneksi.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if(isset($input['mark_all']) && $input['mark_all']) {
        $query = "UPDATE kontak_kami SET status_dibaca = 1 WHERE status_dibaca = 0";
        $result = mysqli_query($koneksi, $query);
        
        if($result) {
            echo json_encode(['success' => true, 'affected_rows' => mysqli_affected_rows($koneksi)]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($koneksi)]);
        }
    }
    exit;
}
?>