<?php

include '../koneksi.php';


$total_projects = $koneksi->query("SELECT COUNT(*) as total FROM projects")->fetch_assoc()['total'];
$total_consultations = $koneksi->query("SELECT COUNT(*) as total FROM consultation_requests")->fetch_assoc()['total'];
$total_users = $koneksi->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_reviews = $koneksi->query("SELECT COUNT(*) as total FROM reviews")->fetch_assoc()['total'];



$daily_labels = [];
$daily_data = [];

$query_daily = "SELECT 
                    DATE(created_at) as tgl_penuh, 
                    COUNT(*) as jumlah 
                FROM consultation_requests 
                WHERE created_at >= CURDATE() - INTERVAL 6 DAY 
                GROUP BY tgl_penuh 
                ORDER BY tgl_penuh ASC";
$result_daily = $koneksi->query($query_daily);
if ($result_daily) {
    while ($row = $result_daily->fetch_assoc()) {
        
        $dateObj = DateTime::createFromFormat('!Y-m-d', $row['tgl_penuh']);
        $daily_labels[] = $dateObj->format('M d'); 
        $daily_data[] = $row['jumlah']; 
    }
}

$js_daily_labels = json_encode($daily_labels);
$js_daily_data = json_encode($daily_data);



$monthly_labels = [];
$monthly_data = [];

$query_monthly = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as bulan_tahun, 
                    COUNT(*) as jumlah 
                  FROM consultation_requests 
                  WHERE created_at >= CURDATE() - INTERVAL 11 MONTH
                  GROUP BY bulan_tahun
                  ORDER BY bulan_tahun ASC";
$result_monthly = $koneksi->query($query_monthly);
if ($result_monthly) {
    while ($row = $result_monthly->fetch_assoc()) {
        
        $dateObj = DateTime::createFromFormat('!Y-m', $row['bulan_tahun']);
        $monthly_labels[] = $dateObj->format('M Y'); 
        $monthly_data[] = $row['jumlah']; 
    }
}

$js_monthly_labels = json_encode($monthly_labels);
$js_monthly_data = json_encode($monthly_data);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Admin GreenRay" />
    <meta name="author" content="Farrel" />
    <title>Dashboard - GreenRay Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">

    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
        <a class="navbar-brand ps-3" href="index.php">GreenRay Admin</a>

        <ul class="navbar-nav ms-auto me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">

        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Utama</div>
                        <a class="nav-link active" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>

                        <div class="sb-sidenav-menu-heading">Manajemen Konten</div>
                        <a class="nav-link" href="projects.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-briefcase"></i></div>
                            Proyek
                        </a>
                        <a class="nav-link" href="products.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-solar-panel"></i></div>
                            Produk
                        </a>
                        <a class="nav-link" href="clients.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                            Klien
                        </a>
                        <a class="nav-link" href="reviews.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-star"></i></div>
                            Reviews
                        </a>
                        <a class="nav-link" href="faqs.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-question-circle"></i></div>
                            FAQ
                        </a>

                        <div class="sb-sidenav-menu-heading">Interaksi User</div>
                        <a class="nav-link" href="consultations.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-calculator"></i></div>
                            Konsultasi (Leads)
                        </a>
                        <a class="nav-link" href="contact_messages.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                            Pesan Kontak
                        </a>

                        <div class="sb-sidenav-menu-heading">Pengaturan Sistem</div>
                        <a class="nav-link" href="users.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Users
                        </a>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                            data-bs-target="#collapseKalkulator" aria-expanded="false"
                            aria-controls="collapseKalkulator">
                            <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                            Setting Kalkulator
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseKalkulator" aria-labelledby="headingOne"
                            data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="locations.php">Manajemen Lokasi</a>
                                <a class="nav-link" href="tariffs.php">Manajemen Tarif</a>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    Admin
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Ringkasan Sistem</li>
                    </ol>

                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <div class="fs-4"><?php echo $total_consultations; ?></div>
                                    <div>Total Konsultasi (Leads)</div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="consultations.php">Lihat Detail</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">
                                    <div class="fs-4"><?php echo $total_projects; ?></div>
                                    <div>Total Proyek</div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="projects.php">Lihat Detail</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <div class="fs-4"><?php echo $total_users; ?></div>
                                    <div>Total Users Terdaftar</div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="users.php">Lihat Detail</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">
                                    <div class="fs-4"><?php echo $total_reviews; ?></div>
                                    <div>Total Reviews</div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="reviews.php">Lihat Detail</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-area me-1"></i>
                                    Leads Masuk (7 Hari Terakhir)
                                </div>
                                <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Total Leads per Bulan (12 Bulan Terakhir)
                                </div>
                                <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            5 Konsultasi (Leads) Terbaru
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Lokasi</th>
                                        <th>Sistem (kWp)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    
                                    $query_leads = "SELECT id, created_at, full_name, calc_location, result_system_capacity_kwp 
                                                        FROM consultation_requests 
                                                        ORDER BY id DESC LIMIT 5";
                                    $result_leads = $koneksi->query($query_leads);

                                    if ($result_leads && $result_leads->num_rows > 0) {
                                        while ($row = $result_leads->fetch_assoc()) {
                                            $tanggal = date('d M Y', strtotime($row['created_at']));
                                            echo '<tr>';
                                            echo '<td>' . $tanggal . '</td>';
                                            echo '<td>' . htmlspecialchars($row['full_name']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['calc_location']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['result_system_capacity_kwp']) . ' kWp</td>';
                                            echo '<td>
                                                        <a href="consultation_view.php?id=' . $row['id'] . '" class="btn btn-info btn-sm">Lihat</a>
                                                      </td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center">Belum ada data konsultasi yang masuk.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; GreenRay 2025</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

    <script type="text/javascript">
        
        Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#292b2c';

        
        var ctxArea = document.getElementById("myAreaChart");
        var myAreaChart = new Chart(ctxArea, {
            type: 'line',
            data: {
                labels: <?php echo $js_daily_labels; ?>, 
                datasets: [{
                    label: "Leads Masuk",
                    lineTension: 0.3,
                    backgroundColor: "rgba(2,117,216,0.2)",
                    borderColor: "rgba(2,117,216,1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(2,117,216,1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(2,117,216,1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: <?php echo $js_daily_data; ?>, 
                }],
            },
            options: {
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            maxTicksLimit: 5,
                            precision: 0 
                        },
                        gridLines: {
                            color: "rgba(0, 0, 0, .125)",
                        }
                    }],
                },
                legend: {
                    display: false
                }
            }
        });

        
        var ctxBar = document.getElementById("myBarChart");
        var myBarChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?php echo $js_monthly_labels; ?>, 
                datasets: [{
                    label: "Total Leads",
                    backgroundColor: "rgba(2,117,216,1)",
                    borderColor: "rgba(2,117,216,1)",
                    data: <?php echo $js_monthly_data; ?>, 
                }],
            },
            options: {
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 12
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            maxTicksLimit: 5,
                            precision: 0 
                        },
                        gridLines: {
                            display: true
                        }
                    }],
                },
                legend: {
                    display: false
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
</body>

</html>