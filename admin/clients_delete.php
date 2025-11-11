<?php
// 1. Hubungkan ke database
include '../koneksi.php';

// 2. Cek apakah ada ID yang dikirim melalui URL (GET)
if (isset($_GET['id'])) {
    
    $client_id = $_GET['id'];

    // --- Logika Hapus File ---
    // Ambil path gambar DULU sebelum dihapus dari DB
    $stmt_select = $koneksi->prepare("SELECT logo_url FROM clients WHERE id = ?");
    $stmt_select->bind_param("i", $client_id);
    $stmt_select->execute();
    $result_img = $stmt_select->get_result();
    if ($row_img = $result_img->fetch_assoc()) {
        $image_path_to_delete = "../" . $row_img['logo_url']; // Cth: ../uploads/clients/file.png
        
        // Hapus file fisik dari server jika ada
        if (!empty($row_img['logo_url']) && file_exists($image_path_to_delete)) {
            unlink($image_path_to_delete);
        }
    }
    $stmt_select->close();
    // --- Akhir Logika Hapus File ---

    // 3. Buat query DELETE data dari database
    $stmt_delete = $koneksi->prepare("DELETE FROM clients WHERE id = ?");
    $stmt_delete->bind_param("i", $client_id);

    // 4. Eksekusi query delete
    if ($stmt_delete->execute()) {
        // Berhasil
    } else {
        echo "Error: Gagal menghapus data. " . $stmt_delete->error;
    }
    $stmt_delete->close();

} else {
    echo "Error: ID klien tidak ditemukan.";
}

// 5. Arahkan kembali (redirect) admin ke halaman daftar klien
header("Location: clients.php");
exit; // Pastikan untuk keluar setelah redirect

?>