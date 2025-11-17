<?php

include '../koneksi.php';
include 'auth_check.php';

if (isset($_GET['id'])) {

    $message_id = $_GET['id'];

    
    $stmt = $koneksi->prepare("DELETE FROM contact_messages WHERE id = ?");

    
    $stmt->bind_param("i", $message_id);

    
    if ($stmt->execute()) {
        
    } else {
        
        echo "Error: Gagal menghapus data. " . $stmt->error;
    }

    $stmt->close();

} else {
    echo "Error: ID pesan tidak ditemukan.";
}


header("Location: contact_messages.php");
exit; 

?>