<?php
// 1. Hubungkan ke database
include '../koneksi.php';

// 2. Cek apakah ada ID yang dikirim melalui URL (GET)
if (isset($_GET['id'])) {

    $product_id = $_GET['id'];

    // 3. Buat query DELETE (Gunakan Prepared Statements agar aman)
    // Menghapus dari tabel 'products'

    $stmt = $koneksi->prepare("DELETE FROM products WHERE id = ?");

    // 'i' berarti parameter ini adalah integer
    $stmt->bind_param("i", $product_id);

    // 4. Eksekusi query
    if ($stmt->execute()) {
        // Jika berhasil, tidak perlu tampilkan pesan,
        // langsung arahkan kembali ke halaman daftar produk.
    } else {
        // Jika gagal, kamu bisa mencatat errornya
        echo "Error: Gagal menghapus data. " . $stmt->error;
    }

    $stmt->close();

} else {
    // Jika tidak ada ID di URL, beri pesan error
    echo "Error: ID produk tidak ditemukan.";
}

// 5. Arahkan kembali (redirect) admin ke halaman daftar produk
header("Location: products.php");
exit; // Pastikan untuk keluar setelah redirect

?>