<?php

include '../koneksi.php';
include 'auth_check.php';

if (isset($_GET['id'])) {

    $consultation_id = $_GET['id'];


    $stmt = $koneksi->prepare("DELETE FROM consultation_requests WHERE id = ?");


    $stmt->bind_param("i", $consultation_id);


    if ($stmt->execute()) {

    } else {

        echo "Error: Gagal menghapus data. " . $stmt->error;
    }

    $stmt->close();

} else {
    echo "Error: ID konsultasi tidak ditemukan.";
}


header("Location: consultations.php");
exit;

?>