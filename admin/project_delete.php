<?php
// 1. Hubungkan ke database
include '../koneksi.php';

// 2. Cek apakah ada ID yang dikirim melalui URL (GET)
if (isset($_GET['id'])) {

    $project_id = $_GET['id'];

    // 3. Buat query DELETE (Gunakan Prepared Statements agar aman)
    // Kita hanya perlu menghapus dari tabel 'projects'.
    // Jika kamu sudah mengatur relasi 'ON DELETE CASCADE' di database,
    // semua data di 'project_gallery_images' yang terkait akan terhapus otomatis.

    $stmt = $koneksi->prepare("DELETE FROM projects WHERE id = ?");

    // 'i' berarti parameter ini adalah integer
    $stmt->bind_param("i", $project_id);

    // 4. Eksekusi query
    if ($stmt->execute()) {
        // Jika berhasil, tidak perlu tampilkan pesan,
        // langsung arahkan kembali ke halaman daftar proyek.
    } else {
        // Jika gagal, kamu bisa mencatat errornya, tapi tetap redirect
        // (Atau tampilkan halaman error khusus)
        echo "Error: Gagal menghapus data. " . $stmt->error;
    }

    $stmt->close();

} else {
    // Jika tidak ada ID di URL, beri pesan error
    echo "Error: ID proyek tidak ditemukan.";
}

// 5. Arahkan kembali (redirect) admin ke halaman daftar proyek
// Ini akan dijalankan setelah proses hapus selesai ATAU jika tidak ada ID
header("Location: projects.php");
exit; // Pastikan untuk keluar setelah redirect

?>