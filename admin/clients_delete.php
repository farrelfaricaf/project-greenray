<?php

include '../koneksi.php';

if (isset($_GET['id'])) {

    $client_id = $_GET['id'];

   
    $stmt = $koneksi->prepare("DELETE FROM clients WHERE id = ?");

    $stmt->bind_param("i", $client_id);

    if ($stmt->execute()) {
    } else {
        echo "Error: Gagal menghapus data. " . $stmt->error;
    }

    $stmt->close();

} else {
    echo "Error: ID klien tidak ditemukan.";
}

header("Location: clients.php");
exit; 

?>