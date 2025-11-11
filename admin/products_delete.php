<?php

include '../koneksi.php';


if (isset($_GET['id'])) {

    $product_id = $_GET['id'];

    
    
    $stmt_select = $koneksi->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt_select->bind_param("i", $product_id);
    $stmt_select->execute();
    $result_img = $stmt_select->get_result();
    if ($row_img = $result_img->fetch_assoc()) {
        $image_path_to_delete = "../" . $row_img['image_url']; 

        
        if (!empty($row_img['image_url']) && file_exists($image_path_to_delete)) {
            unlink($image_path_to_delete);
        }
    }
    $stmt_select->close();
    

    
    $stmt_delete = $koneksi->prepare("DELETE FROM products WHERE id = ?");
    $stmt_delete->bind_param("i", $product_id);

    
    if ($stmt_delete->execute()) {
        
    } else {
        echo "Error: Gagal menghapus data. " . $stmt_delete->error;
    }
    $stmt_delete->close();

} else {
    echo "Error: ID produk tidak ditemukan.";
}


header("Location: products.php");
exit; 

?>