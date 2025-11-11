<?php

include '../koneksi.php';


if (isset($_GET['id'])) {
    
    $location_id = $_GET['id'];

    
    $stmt = $koneksi->prepare("DELETE FROM locations WHERE id = ?");
    
    
    $stmt->bind_param("i", $location_id);

    
    if ($stmt->execute()) {
        
    } else {
        
        echo "Error: Gagal menghapus data. " . $stmt->error;
    }

    $stmt->close();

} else {
    echo "Error: ID lokasi tidak ditemukan.";
}


header("Location: locations.php");
exit; 

?>