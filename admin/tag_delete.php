<?php
include '../koneksi.php';
include 'auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $segment_id = (int) ($_POST['segment_id'] ?? 0);
    if ($segment_id > 0) {
        
        $stmt = $koneksi->prepare("
            SELECT COUNT(*) AS cnt
            FROM product_segment_map
            WHERE segment_id = ?
        ");
        $stmt->bind_param("i", $segment_id);
        $stmt->execute();
        $cnt = $stmt->get_result()->fetch_assoc()['cnt'] ?? 0;

        if ($cnt == 0) {
            
            $del = $koneksi->prepare("DELETE FROM product_segments WHERE id = ?");
            $del->bind_param("i", $segment_id);
            $del->execute();
            header("Location: tags.php?status=deleted");
        } else {
            
            header("Location: tags.php?status=has_products");
        }
        exit;
    }
}
header("Location: tags.php");
