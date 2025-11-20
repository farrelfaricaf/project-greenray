<?php

include '../koneksi.php';
include 'auth_check.php';

$alert_message = ""; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $order_index = $_POST['order_index'];

    
    $stmt = $koneksi->prepare("INSERT INTO faqs (question, answer, order_index) VALUES (?, ?, ?)");

    
    $stmt->bind_param("ssi", $question, $answer, $order_index);

    
    if ($stmt->execute()) {
        $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> FAQ baru berhasil ditambahkan.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
    } else {
        $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Gagal menyimpan: ' . $stmt->error . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Tambah FAQ - GreenRay Admin</title>
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

                    <h1 class="mt-4">Tambah FAQ Baru</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="faqs.php">Data FAQ</a></li>
                        <li class="breadcrumb-item active">Tambah FAQ</li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-plus me-1"></i>
                            Formulir FAQ Baru
                        </div>
                        <div class="card-body">
                            <form action="faqs_add.php" method="POST">

                                <div class="mb-3">
                                    <label class="small mb-1" for="question">Pertanyaan (Question)</label>
                                    <input class="form-control" id="question" name="question" type="text"
                                        placeholder="Tulis pertanyaan..." required>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="answer">Jawaban (Answer)</label>
                                    <textarea class="form-control" id="answer" name="answer" rows="5"
                                        placeholder="Tulis jawaban... (Boleh menggunakan tag HTML seperti <strong>)"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="order_index">Nomor Urut (Order Index)</label>
                                    <input class="form-control" id="order_index" name="order_index" type="number"
                                        placeholder="Cth: 1" value="0" required>
                                </div>

                                <button class="btn btn-primary" type="submit">Simpan FAQ</button>
                                <a href="faqs.php" class="btn btn-secondary">Batal</a>
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