<?php

include '../koneksi.php';
include 'auth_check.php';

$alert_message = ""; 
$faq_id = null;
$faq = []; 


if (isset($_GET['id'])) {
    $faq_id = $_GET['id'];

    
    $stmt_select = $koneksi->prepare("SELECT * FROM faqs WHERE id = ?");
    $stmt_select->bind_param("i", $faq_id); 
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result->num_rows > 0) {
        $faq = $result->fetch_assoc();
    } else {
        $alert_message = '<div class="alert alert-danger">Error: FAQ tidak ditemukan!</div>';
    }
    $stmt_select->close();
} else {
    $alert_message = '<div class="alert alert-danger">Error: ID FAQ tidak valid.</div>';
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $faq_id = $_POST['faq_id'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $order_index = $_POST['order_index'];

    
    $stmt_update = $koneksi->prepare("UPDATE faqs SET question = ?, answer = ?, order_index = ? WHERE id = ?");

    
    $stmt_update->bind_param("ssii", $question, $answer, $order_index, $faq_id);

    
    if ($stmt_update->execute()) {
        $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> FAQ berhasil diperbarui.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';

        
        $stmt_select = $koneksi->prepare("SELECT * FROM faqs WHERE id = ?");
        $stmt_select->bind_param("i", $faq_id);
        $stmt_select->execute();
        $faq = $stmt_select->get_result()->fetch_assoc();
        $stmt_select->close();

    } else {
        $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Gagal memperbarui: ' . $stmt_update->error . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
    }

    $stmt_update->close();
}


if (empty($faq)) {
    $faq = array_fill_keys(['question', 'answer', 'order_index'], '');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit FAQ #<?php echo $faq_id; ?> - GreenRay Admin</title>
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="../img/favicon.png?v=1.1" sizes="180x180">
</head>

<body class="sb-nav-fixed">

    <?php include 'includes/navbar.php'; ?>
    
    <div id="layoutSidenav">

        <?php include 'includes/sidebar.php'; ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">

                    <h1 class="mt-4">Edit FAQ</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="faqs.php">Data FAQ</a></li>
                        <li class="breadcrumb-item active">Edit FAQ #<?php echo $faq_id; ?></li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-edit me-1"></i>
                            Formulir Edit FAQ (ID: <?php echo $faq_id; ?>)
                        </div>
                        <div class="card-body">

                            <form action="faqs_edit.php?id=<?php echo $faq_id; ?>" method="POST">
                                <input type="hidden" name="faq_id" value="<?php echo $faq_id; ?>">

                                <div class="mb-3">
                                    <label class="small mb-1" for="question">Pertanyaan (Question)</label>
                                    <input class="form-control" id="question" name="question" type="text"
                                        value="<?php echo htmlspecialchars($faq['question']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="answer">Jawaban (Answer)</label>
                                    <textarea class="form-control" id="answer" name="answer"
                                        rows="5"><?php echo htmlspecialchars($faq['answer']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="order_index">Nomor Urut (Order Index)</label>
                                    <input class="form-control" id="order_index" name="order_index" type="number"
                                        value="<?php echo htmlspecialchars($faq['order_index']); ?>" required>
                                </div>

                                <button class="btn btn-primary" type="submit">Update FAQ</button>
                                <a href="faqs.php" class="btn btn-secondary">Kembali ke Daftar</a>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
            
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>