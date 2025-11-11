<?php
include '../koneksi.php';
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $stmt = $koneksi->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
    } else {
        echo "Error: Gagal menghapus data. " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Error: ID user tidak ditemukan.";
}
header("Location: users.php");
exit;
?>