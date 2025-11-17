<?php

include '../koneksi.php';
include 'auth_check.php';

if (isset($_GET['id'])) {

    $project_id = $_GET['id'];

    
    
    $stmt_hero = $koneksi->prepare("SELECT hero_image_url FROM projects WHERE id = ?");
    $stmt_hero->bind_param("i", $project_id);
    $stmt_hero->execute();
    $result_hero = $stmt_hero->get_result();
    if ($row_hero = $result_hero->fetch_assoc()) {
        $hero_path = "../" . $row_hero['hero_image_url']; 
        if (file_exists($hero_path) && !empty($row_hero['hero_image_url'])) {
            unlink($hero_path); 
        }
    }
    $stmt_hero->close();

    
    
    $stmt_gallery = $koneksi->prepare("SELECT image_url FROM project_gallery_images WHERE project_id = ?");
    $stmt_gallery->bind_param("i", $project_id);
    $stmt_gallery->execute();
    $result_gallery = $stmt_gallery->get_result();
    while ($row_gallery = $result_gallery->fetch_assoc()) {
        $gallery_path = "../" . $row_gallery['image_url'];
        if (file_exists($gallery_path) && !empty($row_gallery['image_url'])) {
            unlink($gallery_path); 
        }
    }
    $stmt_gallery->close();

    
    
    
    
    

    $stmt_delete = $koneksi->prepare("DELETE FROM projects WHERE id = ?");
    $stmt_delete->bind_param("i", $project_id);
    $stmt_delete->execute();
    $stmt_delete->close();

} else {
    echo "Error: ID proyek tidak ditemukan.";
}


header("Location: projects.php");
exit;

?>