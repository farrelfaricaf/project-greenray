<?php

include '../koneksi.php';


if (isset($_GET['id'])) {

    $faq_id = $_GET['id'];

    
    $stmt = $koneksi->prepare("DELETE FROM faqs WHERE id = ?");

    
    $stmt->bind_param("i", $faq_id);

    
    if ($stmt->execute()) {
        
    } else {
        
        echo "Error: Gagal menghapus data. " . $stmt->error;
    }

    $stmt->close();

} else {
    echo "Error: ID FAQ tidak ditemukan.";
}


header("Location: faqs.php");
exit; 

?>