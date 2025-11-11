<?php
include '../koneksi.php';
if (isset($_GET['id'])) {
    $review_id = $_GET['id'];
    $stmt = $koneksi->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $review_id);
    if ($stmt->execute()) {

    } else {
   
        echo "Error: Gagal menghapus data. " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Error: ID review tidak ditemukan.";
}
header("Location: reviews.php");
exit; 
?>