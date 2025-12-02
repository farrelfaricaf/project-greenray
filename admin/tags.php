<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";

// baca status dari redirect (opsional)
$status = $_GET['status'] ?? '';
if ($status === 'deleted') {
    $alert_message = '<div class="alert alert-success">Tag deleted successfully.</div>';
} elseif ($status === 'has_products') {
    $alert_message = '<div class="alert alert-warning">Tag is still used by one or more products.</div>';
} elseif ($status === 'error') {
    $alert_message = '<div class="alert alert-danger">Failed to delete tag.</div>';
}

// ambil list tag + jumlah produk
$sql = "
  SELECT
    s.id,
    s.name,
    s.slug,
    COUNT(m.product_id) AS product_count
  FROM product_segments s
  LEFT JOIN product_segment_map m ON s.id = m.segment_id
  GROUP BY s.id, s.name, s.slug
  ORDER BY s.name ASC
";
$res = $koneksi->query($sql);
$tags = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $tags[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Tags - GreenRay Admin</title>
  <link href="css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
</head>
<body class="sb-nav-fixed">
<?php include 'includes/navbar.php'; ?>
<div id="layoutSidenav">
  <?php include 'includes/sidebar.php'; ?>

  <div id="layoutSidenav_content">
    <main>
      <div class="container-fluid px-4">
        <h1 class="mt-4">Product Tags</h1>
        <ol class="breadcrumb mb-4">
          <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Tags</li>
        </ol>

        <?php echo $alert_message; ?>

        <div class="card mb-4">
          <div class="card-header">
            Existing Tags
          </div>
          <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Slug</th>
                  <th style="width:140px;">Products Used</th>
                  <th style="width:120px;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($tags)): ?>
                  <tr><td colspan="5" class="text-center">No tags found.</td></tr>
                <?php else: ?>
                  <?php foreach ($tags as $tag): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($tag['name']); ?></td>
                      <td><?php echo htmlspecialchars($tag['slug']); ?></td>
                      <td><?php echo (int)$tag['product_count']; ?></td>
                      <td>
                        <form action="tag_delete.php" method="POST"
                              onsubmit="return confirm('Delete this tag permanently?');"
                              class="d-inline">
                          <input type="hidden" name="segment_id"
                                 value="<?php echo $tag['id']; ?>">
                          <button type="submit" class="btn btn-sm btn-danger"
                            <?php echo ($tag['product_count'] > 0
                                          ? 'disabled title="Tag is still used by products"'
                                          : ''); ?>>
                            Delete
                          </button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </main>
    <?php include 'includes/footer.php'; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
