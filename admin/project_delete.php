<?php

include '../koneksi.php';


if (isset($_GET['id'])) {

    $project_id = $_GET['id'];

    
    
    
    

    $stmt = $koneksi->prepare("DELETE FROM projects WHERE id = ?");

    
    $stmt->bind_param("i", $project_id);

    
    if ($stmt->execute()) {
        
        
    } else {
        
        
        echo "Error: Gagal menghapus data. " . $stmt->error;
    }

    $stmt->close();

} else {
    
    echo "Error: ID proyek tidak ditemukan.";
}



header("Location: projects.php");
exit; 

?>