<?php

include '../koneksi.php';


if (isset($_GET['id'])) {

    $product_id = $_GET['id'];

    
    

    $stmt = $koneksi->prepare("DELETE FROM products WHERE id = ?");

    
    $stmt->bind_param("i", $product_id);

    
    if ($stmt->execute()) {
        
        
    } else {
        
        echo "Error: Gagal menghapus data. " . $stmt->error;
    }

    $stmt->close();

} else {
    
    echo "Error: ID produk tidak ditemukan.";
}


header("Location: products.php");
exit; 

?>