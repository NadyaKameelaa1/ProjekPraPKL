<?php
session_start();
require_once '../koneksi/koneksi.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if(isset($input['delete_all']) && $input['delete_all']) {
        // Mulai transaksi untuk keamanan
        mysqli_begin_transaction($koneksi);
        
        try {
            // Dapatkan jumlah data sebelum dihapus
            $countQuery = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kontak_kami");
            $countData = mysqli_fetch_assoc($countQuery);
            $totalDeleted = $countData['total'];
            
            // Eksekusi penghapusan
            $deleteQuery = "DELETE FROM kontak_kami";
            $result = mysqli_query($koneksi, $deleteQuery);
            
            if($result) {
                mysqli_commit($koneksi);
                $_SESSION['success'] = "Semua pesan ($totalDeleted) berhasil dihapus";
                echo json_encode([
                    'success' => true,
                    'message' => "Semua pesan ($totalDeleted) berhasil dihapus"
                ]);
            } else {
                throw new Exception(mysqli_error($koneksi));
            }
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    exit;
}
?>