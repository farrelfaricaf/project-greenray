<?php

include '../koneksi.php';


if (isset($_GET['id'])) {

    $review_id = $_GET['id'];

    
    
    $stmt_select = $koneksi->prepare("SELECT image_url FROM reviews WHERE id = ?");
    $stmt_select->bind_param("i", $review_id);
    $stmt_select->execute();
    $result_img = $stmt_select->get_result();

    if ($row_img = $result_img->fetch_assoc()) {
        $image_path_to_delete = "../" . $row_img['image_url']; 

        
        if (!empty($row_img['image_url']) && file_exists($image_path_to_delete)) {
            
            unlink($image_path_to_delete);
        }
    }
    $stmt_select->close();
    


    
    $stmt_delete = $koneksi->prepare("DELETE FROM reviews WHERE id = ?");

    
    $stmt_delete->bind_param("i", $review_id);

    
    if ($stmt_delete->execute()) {
        
        
    } else {
        
        echo "Error: Gagal menghapus data. " . $stmt_delete->error;
    }

    $stmt_delete->close();

} else {
    
    echo "Error: ID review tidak ditemukan.";
}


header("Location: reviews.php");
exit; 

?>