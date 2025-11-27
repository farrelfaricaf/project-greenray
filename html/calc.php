<?php
// WAJIB ADA DI BARIS PALING ATAS
session_start();

// 1. Hubungkan ke database
include '../koneksi.php';

$alert_message = ""; // Variabel untuk menyimpan pesan notifikasi

// 2. LOGIKA UNTUK MENERIMA DATA FORM SAAT DI-SUBMIT
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // (Ambil semua data $_POST seperti sebelumnya... )
    $user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : NULL;
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $kelurahan = $_POST['kelurahan'];
    $kecamatan = $_POST['kecamatan'];
    $postal_code = $_POST['postal_code'];
    $calc_monthly_bill = (int) str_replace(['Rp ', '.'], '', $_POST['calc_monthly_bill']);
    $calc_va_capacity = $_POST['calc_va_capacity'];
    $calc_location = $_POST['calc_location'];
    $calc_property_type = $_POST['calc_property_type'];
    $calc_installation_timeline = $_POST['calc_installation_timeline'];
    $calc_roof_constraints = $_POST['calc_roof_constraints'];
    $calc_roof_area = $_POST['roofArea'];
    $calc_notes = $_POST['notes'];
    $result_monthly_savings = (int) $_POST['result_monthly_savings'];
    $result_system_capacity_kwp = (float) $_POST['result_system_capacity_kwp'];
    $result_investment_estimate = (int) $_POST['result_investment_estimate'];
    $result_roi_years = (float) $_POST['result_roi_years'];

    // 3. Buat query INSERT (Tanpa order_number)
    $stmt = $koneksi->prepare("INSERT INTO consultation_requests 
        (user_id, full_name, email, phone, address, kelurahan, kecamatan, postal_code, 
        calc_monthly_bill, calc_va_capacity, calc_location, calc_property_type, 
        calc_installation_timeline, calc_roof_constraints, 
        calc_roof_area, calc_notes,
        result_monthly_savings, result_system_capacity_kwp, result_investment_estimate, result_roi_years) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "isssssssisssssssidid",
        $user_id,
        $full_name,
        $email,
        $phone,
        $address,
        $kelurahan,
        $kecamatan,
        $postal_code,
        $calc_monthly_bill,
        $calc_va_capacity,
        $calc_location,
        $calc_property_type,
        $calc_installation_timeline,
        $calc_roof_constraints,
        $calc_roof_area,
        $calc_notes,
        $result_monthly_savings,
        $result_system_capacity_kwp,
        $result_investment_estimate,
        $result_roi_years
    );

    // 4. Eksekusi query
    // 4. Eksekusi query
    if ($stmt->execute()) {
        // ▼▼▼ LOGIKA NOMOR PESANAN BARU (DIPERBAIKI) ▼▼▼

        // Dapatkan ID dari data yang BARU SAJA di-insert
        $new_id = $stmt->insert_id;

        // Buat Order Number unik (cth: GR-1001, GR-1002)
        $order_number = 'GR-' . (0000 + $new_id); // (Saya ganti 10 jadi 1000 agar lebih bagus)

        // Update baris tadi dengan Order Number yang baru
        $koneksi->query("UPDATE consultation_requests SET order_number = '$order_number' WHERE id = $new_id");

        // SIAPKAN VARIABEL INI UNTUK MEMICU MODAL JS DI BAWAH
        $sukses_order_number = $order_number;

        // Kita tidak pakai $alert_message lagi
        // $alert_message = '<div class="alert alert-success">...</div>';

        // ▲▲▲ AKHIR LOGIKA NOMOR PESANAN ▲▲▲

    } else {
        $alert_message = '<div class="alert alert-danger">Error: Gagal menyimpan data. ' . $stmt->error . '</div>';
    }
    $stmt->close();
}


// 5. LOGIKA UNTUK MENGAMBIL DATA DROPDOWN
$location_options = [];
$tariff_options = [];
$location_js_map = [];
$tariff_js_map = [];

$result_loc = $koneksi->query("SELECT city_name, irradiance_factor FROM locations WHERE is_active = 1 ORDER BY city_name ASC");
if ($result_loc) {
    while ($row = $result_loc->fetch_assoc()) {
        $location_options[] = $row['city_name'];
        $location_js_map[$row['city_name']] = (float) $row['irradiance_factor'];
    }
}

$result_tariff = $koneksi->query("SELECT va_capacity, tariff_per_kwh FROM power_tariffs WHERE is_active = 1 ORDER BY id ASC");
if ($result_tariff) {
    while ($row = $result_tariff->fetch_assoc()) {
        $tariff_options[] = $row['va_capacity'];
        $tariff_js_map[$row['va_capacity']] = (float) $row['tariff_per_kwh'];
    }
}


// 6. LOGIKA UNTUK STATUS LOGIN (HEADER & AUTO-FILL)
$is_logged_in = isset($_SESSION['user_id']);
$user_name = '';
$user_email = '';
$user_phone = ''; // (Kamu bisa tambahkan 'phone' ke tabel users jika mau)
$profile_pic = '../img/default-profile.png';

if ($is_logged_in) {
    $user_name = $_SESSION['user_name'] ?? 'User';
    $user_email = $_SESSION['user_email'] ?? '';
    $profile_pic = $_SESSION['user_profile_pic'] ?? '../img/default-profile.png';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculator - GreenRay</title>

    <link rel="stylesheet" href="../css/globals.css">
    <link rel="stylesheet" href="../css/styleguide.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/calc.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/favicon.png" sizes="180px180">
    <style>
        /* CSS untuk Dropdown Profil */
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        /* INI YANG MEMPERBAIKI UKURAN GAMBAR */
        .profile-picture-header {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            /* Memastikan gambar tidak gepeng */
            cursor: pointer;
            border: 2px solid #136000;
            /* Border hijau GreenRay */
        }

        .dropdown-menu-header {
            display: none;
            /* Sembunyi by default */
            position: absolute;
            right: 0;
            top: 60px;
            /* Jarak dari ikon */
            background-color: white;
            min-width: 180px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.1);
            z-index: 100;
            border-radius: 8px;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .dropdown-menu-header.show {
            display: block;
            /* Tampilkan saat di-klik */
        }

        .dropdown-menu-header .dropdown-item,
        .dropdown-menu-header .dropdown-item-info {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 0.95rem;
        }

        .dropdown-menu-header .dropdown-item:hover {
            background-color: #f1f1f1;
        }

        .dropdown-menu-header .dropdown-item-info {
            background-color: #f9f9f9;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="kalkulator-1">
        <div class="frame-139">
            <?php include 'includes/header.php'; ?>

            <div id="validation-alert-container" class="alert-fixed-center">
            </div>

            <div class="big-container">
                <div class="step-wrapper">

                    <div id="step-1" class="calc-container padding-extra step active-step" data-step="1">
                        <div class="heading-section">
                            <div class="title-heading legacy-steps">
                                <span>
                                    <span class="title-heading-span">Hitung Potensi</span>
                                    <span class="title-heading-span2">
                                        Hemat
                                        <br />
                                    </span>
                                    <span class="title-heading-span">Anda dengan Panel Surya</span>
                                </span>
                            </div>
                            <div class="dec-heading">
                                Hanya butuh 30 detik. Dapatkan estimasi penghematan biaya listrik
                                bulanan dan temukan kapasitas panel surya yang ideal untuk rumah Anda
                                secara gratis.
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="input-column">
                                <div class="title-input txt-mid">
                                    Berapa rata-rata tagihan listrik bulanan Anda?
                                </div>
                                <input type="text" class="input-calc" placeholder="contoh: Rp 500.000"
                                    inputmode="numeric" id="billInput" />
                            </div>
                            <div class="input-column">
                                <div class="title-input">Pilih Daya VA</div>
                                <div class="custom-dropdown static-dropdown"
                                    data-options="<?php echo implode(',', $tariff_options); ?>"
                                    data-placeholder="Pilih Daya VA" tabindex="0">
                                    <div class="dropdown-header">
                                        <div class="selected-value">Pilih Daya VA</div>
                                        <span class="ep--arrow-down-bold arrow-icon"></span>
                                    </div>
                                    <div class="options-container">
                                    </div>
                                </div>
                            </div>
                            <div class="input-column">
                                <div class="title-input">Di mana lokasi rumah Anda?</div>
                                <div class="custom-dropdown"
                                    data-options="<?php echo implode(',', $location_options); ?>"
                                    data-placeholder="Pilih Lokasi" data-searchable="true" tabindex="0">
                                    <div class="dropdown-header">
                                        <div class="selected-value">Pilih Lokasi</div>
                                        <input type="text" class="search-input" placeholder="Cari lokasi..." />
                                        <span class="ep--arrow-down-bold arrow-icon"></span>
                                    </div>
                                    <div class="options-container">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <div class="btn-hitung btn-next" data-next-step="2">
                                <div class="text-button">Next</div>
                                <span class="tabler--arrow-right"></span>
                            </div>
                        </div>
                    </div>

                    <div id="step-2" class="calc-container step padding-extra" data-step="2">
                        <div class="heading-section">
                            <div class="title-heading">Project Feasibility &amp; Urgency</div>
                            <div class="dec-heading">
                                Help us tailor your savings estimate by providing quick details about your
                                property and timeline.
                            </div>
                        </div>
                        <div class="question-container">
                            <div class="question">
                                1. What type of property are you considering for solar installation?
                            </div>

                            <div id="property-type-selection" class="card-container single-select">
                                <div class="card-option" data-value="single_storey">
                                    <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                        </path>
                                    </svg>
                                    <div class="text-card">Single-Storey House<br />(Rumah 1 Lantai)</div>
                                </div>

                                <div class="card-option" data-value="multi_storey">
                                    <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-4a1 1 0 011-1h2a1 1 0 011 1v4">
                                        </path>
                                    </svg>
                                    <div class="text-card">
                                        Multi-Storey House
                                        <br />
                                        (Rumah Bertingkat)
                                    </div>
                                </div>

                                <div class="card-option" data-value="flat_apartment">
                                    <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 12a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zM4 19a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2z">
                                        </path>
                                    </svg>
                                    <div class="text-card">
                                        Flat/Apartment
                                        <br />
                                        (Apartemen/Lantai Datar)
                                    </div>
                                </div>

                                <div class="card-option" data-value="commercial_business">
                                    <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                    <div class="text-card">
                                        Commercial/Business
                                        <br />
                                        (Komersial/Bisnis)
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <div class="btn-hitung btn-back" data-prev-step="1">
                                <span class="tabler--arrow-left"></span>
                                <div class="text-button">Back</div>
                            </div>
                            <div class="btn-hitung btn-next" data-next-step="3"
                                data-validation-target="#property-type-selection">
                                <div class="text-button">Next</div>
                                <span class="tabler--arrow-right"></span>
                            </div>
                        </div>
                    </div>

                    <div id="step-3" class="calc-container step double-padding-extra" data-step="3">
                        <div class="question-container">
                            <div class="question">
                                2. When are you looking to have your solar system installed?
                            </div>

                            <div id="timeline-selection" class="card-container single-select four-options">

                                <div class="card-option padding-card" data-value="asap">
                                    <div class="text-card">As soon as possible</div>
                                </div>

                                <div class="card-option padding-card" data-value="3-6_months">
                                    <div class="text-card">
                                        3-6 months time
                                    </div>
                                </div>

                                <div class="card-option padding-card" data-value="6-12_months">
                                    <div class="text-card">
                                        6-12 months time
                                    </div>
                                </div>

                                <div class="card-option padding-card" data-value="just_exploring">
                                    <div class="text-card">
                                        12 months+ or just exploring
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <div class="btn-hitung btn-back" data-prev-step="2">
                                <span class="tabler--arrow-left"></span>
                                <div class="text-button">Back</div>
                            </div>
                            <div class="btn-hitung btn-next" data-next-step="4"
                                data-validation-target="#property-type-selection">
                                <div class="text-button">Next</div>
                                <span class="tabler--arrow-right"></span>
                            </div>
                        </div>
                    </div>

                    <div id="step-4" class="calc-container step double-padding-extra" data-step="4">
                        <div class="question-container">
                            <div class="question">
                                3. Does your roof have any potential installation constraints?
                            </div>

                            <div id="constraints-selection" class="card-container single-select four-options">

                                <div class="card-option padding-card" data-value="no_constraints">
                                    <div class="text-card">No known constraints</div>
                                </div>

                                <div class="card-option padding-card" data-value="shade_trees">
                                    <div class="text-card">
                                        Potential shade/trees nearby
                                    </div>
                                </div>

                                <div class="card-option padding-card" data-value="complex_roof">
                                    <div class="text-card">
                                        Small or complex roof area
                                    </div>
                                </div>

                                <div class="card-option padding-card" data-value="not_sure">
                                    <div class="text-card">
                                        I'm not sure yet
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <div class="btn-hitung btn-back" data-prev-step="3">
                                <span class="tabler--arrow-left"></span>
                                <div class="text-button">Back</div>
                            </div>
                            <div class="btn-hitung btn-next" data-next-step="5"
                                data-validation-target="#property-type-selection">
                                <div class="text-button">Next</div>
                                <span class="tabler--arrow-right"></span>
                            </div>
                        </div>
                    </div>

                    <div id="step-5" class="calc-container lead-container step" data-step="5">
                        <div class="lead-inform">
                            <div class="heading-section width-heading">
                                <div class="title-heading2">
                                    Almost There! Unlock Your Personalized Report
                                </div>
                                <div class="dec-heading teks-tengah">
                                    You're just one click away from seeing your projected savings, ideal
                                    system size, <br> and a free consultation offer. Enter your details below to
                                    instantly <br> access your report.
                                </div>
                            </div>
                            <div class="email-container">
                                <div class="question4-container">
                                    <div class="title-input">
                                        What's your email address?
                                    </div>
                                    <input type="email" class="input-calc" name="email" placeholder="example@gmail.com"
                                        id="emailInput" value="<?php echo htmlspecialchars($user_email); ?>" <?php echo $is_logged_in ? 'readonly' : ''; ?> />
                                </div>
                                <div class="btn-container">
                                    <div class="btn-group">
                                        <div class="btn-hitung btn-back" data-prev-step="4">
                                            <span class="tabler--arrow-left"></span>
                                            <div class="text-button">Back</div>
                                        </div>
                                        <div class="btn-hitung button-lead btn-submit btn-next" data-next-step="6"
                                            id="unlockButton">
                                            <div class="text-button teks-lead">View My Report</div>
                                            <span class="tabler--arrow-right"></span>
                                        </div>
                                    </div>
                                    <div class="dec-heading lead-dec">
                                        "We respect your privacy. We will only use this information to send
                                        you the report and schedule your free, personalized consultation."
                                    </div>
                                </div>
                            </div>
                        </div>
                        <img class="vector-solar" src="../img/solar-panel.png" />
                    </div>

                    <div id="step-6" class="calc-container step padding-extra result-container" data-step="6">
                        <div class="result">
                            <div class="heading-section result-heading-section">
                                <div class="title-heading result-title-heading">
                                    <span>
                                        <span class="title-heading-span">
                                            CONGRATULATIONS!
                                            <br />
                                        </span>
                                        <span class="title-heading-span2 result-subtitle">
                                            Your Personalized Solar Savings Are Here.
                                        </span>
                                    </span>
                                </div>
                                <div class="dec-heading result-dec-heading">
                                    Based on your input, here is the projected financial impact of your new
                                    system.
                                </div>
                            </div>
                            <div class="answer-container">
                                <div class="question-container result-question-container">
                                    <div class="title-result result-title-input">POTENTIAL MONTHLY SAVINGS</div>
                                    <div class="money-count" id="monthly-savings">Rp. 800.000</div>
                                </div>
                                <div class="answer-container-2">
                                    <div class="question-container result-question-container-small">
                                        <div class="title-result result-title-input">IDEAL SYSTEM CAPACITY</div>
                                        <div class="kwp-count" id="system-capacity">4.39 kWp</div>
                                    </div>
                                    <div class="question-container result-question-container-small">
                                        <div class="title-result result-title-input">RETURN ON INVESTMENT ESTIMATE</div>
                                        <div class="year-count" id="roi-estimate">6.5 years</div>
                                    </div>
                                </div>
                            </div>
                            <div class="btn-container result-btn-container">

                                <div class="btn-hitung result-cta-btn btn-next" data-next-step="7">
                                    <div class="text-button">
                                        Schedule a FREE &amp; Fast Consultation!
                                    </div>
                                    <span class="tabler--arrow-right"></span>
                                </div>
                                <div class="dec-heading result-dec-heading-small">
                                    The hard part is done! Let our experts finalize your quote, verify roof
                                    feasibility, and guide you through the simple installation process.
                                </div>
                            </div>
                        </div>
                    </div>


                    <div id="step-7" class="calc-container step" data-step="7">
                        <div class="container my-5">

                            <div class="text-center mb-5">
                                <h1 class="display-5 fw-bold text-dark">Form Pemesanan Panel Surya</h1>
                                <p class="lead text-dark mx-auto" style="max-width: 700px;">
                                    Lengkapi data di bawah ini untuk melanjutkan proses pemesanan sistem panel surya
                                    Anda. Tim Greenray akan segera menghubungi Anda.
                                </p>
                            </div>

                            <?php if (!empty($alert_message))
                                echo $alert_message; ?>

                            <div class="bg-success text-white rounded-4 p-4 p-md-5 mb-5 shadow-sm">
                                <h2 class="h3 fw-bold mb-4">📊 Ringkasan Sistem Panel Surya Anda</h2>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="summary-item">
                                            <div class="label">Kapasitas Sistem</div>
                                            <div class="value" id="summary-capacity">...</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="summary-item">
                                            <div class="label">Penghematan per Bulan</div>
                                            <div class="value" id="summary-savings">...</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="summary-item">
                                            <div class="label">Estimasi Investasi</div>
                                            <div class="value" id="summary-investment">Rp 45.000.000</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="summary-item">
                                            <div class="label">Periode Balik Modal</div>
                                            <div class="value" id="summary-roi">...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form id="orderForm" class="needs-validation" action="calc.php" method="POST" novalidate>

                                <input type="hidden" name="calc_location" id="hidden_location">
                                <input type="hidden" name="calc_property_type" id="hidden_property_type">
                                <input type="hidden" name="calc_installation_timeline"
                                    id="hidden_installation_timeline">
                                <input type="hidden" name="calc_roof_constraints" id="hidden_roof_constraints">
                                <input type="hidden" name="calc_va_capacity" id="hidden_va_capacity">
                                <input type="hidden" name="calc_monthly_bill" id="hidden_monthly_bill">
                                <input type="hidden" name="email" id="hidden_email">
                                <input type="hidden" name="result_monthly_savings" id="hidden_monthly_savings">
                                <input type="hidden" name="result_system_capacity_kwp" id="hidden_system_capacity_kwp">
                                <input type="hidden" name="result_investment_estimate" id="hidden_investment_estimate">
                                <input type="hidden" name="result_roi_years" id="hidden_roi_years">

                                <div class="mb-5">
                                    <h3 class="h4 fw-bold text-dark border-bottom pb-2 mb-4">1. Informasi Pribadi</h3>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="full_name" class="form-label">Nama Lengkap <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="fullName" class="form-control form-control-lg"
                                                placeholder="Masukkan nama lengkap Anda" name="full_name"
                                                value="<?php echo htmlspecialchars($user_name); ?>" required>
                                            <div class="invalid-feedback">
                                                Nama lengkap wajib diisi.
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">Nomor Telepon/WhatsApp <span
                                                    class="text-danger">*</span></label>
                                            <input type="tel" id="phone" class="form-control form-control-lg"
                                                placeholder="contoh: 081234567890" pattern="08[0-9]{8,11}"
                                                title="Harap masukkan nomor telepon yang valid (diawali 08, total 10-13 digit)."
                                                name="phone" value="<?php echo htmlspecialchars($user_phone); ?>"
                                                required>
                                            <div class="invalid-feedback">
                                                Harap masukkan nomor telepon yang valid (diawali 08, 10-13 digit).
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <h3 class="h4 fw-bold text-dark border-bottom pb-2 mb-4">2. Detail Alamat Properti
                                    </h3>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="address" class="form-label">Alamat Lengkap (Jalan, Nomor Rumah,
                                                RT/RW) <span class="text-danger">*</span></label>
                                            <input type="text" id="address" name="address"
                                                class="form-control form-control-lg"
                                                placeholder="contoh: Jl. Merdeka No. 123, RT 001/RW 005" required>
                                            <div class="invalid-feedback">
                                                Alamat lengkap wajib diisi.
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="kelurahan" class="form-label">Kelurahan/Desa <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="kelurahan" name="kelurahan"
                                                class="form-control form-control-lg"
                                                placeholder="Masukkan kelurahan/desa" required>
                                            <div class="invalid-feedback">
                                                Kelurahan/Desa wajib diisi.
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="kecamatan" class="form-label">Kecamatan <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="kecamatan" name="kecamatan"
                                                class="form-control form-control-lg" placeholder="Masukkan kecamatan"
                                                required>
                                            <div class="invalid-feedback">
                                                Kecamatan wajib diisi.
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="calc_location" class="form-label">Kota/Kabupaten <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="city" class="form-control form-control-lg"
                                                placeholder="contoh: Surabaya" required readonly>
                                            <div class="invalid-feedback">
                                                Kota/Kabupaten wajib diisi.
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="postal_code" class="form-label">Kode Pos <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="postalCode" name="postal_code"
                                                class="form-control form-control-lg" placeholder="contoh: 60111"
                                                maxlength="5" required>
                                            <div class="invalid-feedback">
                                                Kode pos wajib diisi.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h3 class="h4 fw-bold text-dark border-bottom pb-2 mb-4">3. Detail Instalasi</h3>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="calc_property_type" class="form-label">Tipe Properti</label>
                                            <input type="text" id="propertyType" class="form-control form-control-lg"
                                                value="Multi-Storey House (Rumah Bertingkat)" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="calc_installation_timeline" class="form-label">Preferensi Waktu
                                                Instalasi</label>
                                            <input type="text" id="installationTime"
                                                class="form-control form-control-lg"
                                                value="3-6 months time (Dalam 3-6 bulan)" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="calc_roof_constraints" class="form-label">Kendala Atap</label>
                                            <input type="text" id="roofConstraints" class="form-control form-control-lg"
                                                placeholder="Memuat..." readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="roofArea" class="form-label">Estimasi Luas Atap
                                                (Opsional)</label>
                                            <input type="text" id="roofArea" name="roofArea"
                                                class="form-control form-control-lg"
                                                placeholder="contoh: 50 m² (jika tahu)">
                                        </div>
                                        <div class="col-12">
                                            <label for="notes" class="form-label">Catatan Khusus / Permintaan Tambahan
                                                (Opsional)</label>
                                            <textarea id="notes" name="notes" class="form-control form-control-lg"
                                                placeholder="Tulis catatan atau permintaan khusus di sini..."
                                                rows="4"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 p-md-4 rounded-3 mt-4 agreement-box">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="agreement" name="agreement"
                                            required>
                                        <label class="form-check-label" for="agreement">
                                            Saya menyetujui bahwa data yang saya berikan adalah benar dan dapat
                                            digunakan oleh Greenray untuk proses konsultasi, survey lokasi, dan
                                            instalasi sistem panel surya. Saya juga memahami bahwa ini adalah permohonan
                                            pemesanan dan belum merupakan komitmen pembelian final.
                                        </label>
                                        <div class="invalid-feedback">
                                            Anda harus menyetujui ketentuan ini untuk melanjutkan.
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-4 mt-5 border-top">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <a role="button" data-bs-toggle="modal" data-bs-target="#backModal"
                                                class="btn btn-lg w-100 d-flex align-items-center justify-content-center gap-2 btn-step7-back">

                                                <svg class="step7-arrow-icon" viewBox="0 0 23 23" fill="none">
                                                    <path d="M11.5 2L11.5 21M11.5 21L2 11.5M11.5 21L21 11.5"
                                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                        stroke-linejoin="round" transform="rotate(90 11.5 11.5)" />
                                                </svg>
                                                <span>Kembali</span>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <button type="submit"
                                                class="btn btn-lg w-100 d-flex align-items-center justify-content-center gap-2 btn-step7-submit"
                                                id="submitBtn">
                                                <span>Kirim Pesanan</span>
                                                <svg class="step7-arrow-icon" viewBox="0 0 23 23" fill="none">
                                                    <path d="M11.5 2L11.5 21M11.5 21L2 11.5M11.5 21L21 11.5"
                                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                        stroke-linejoin="round" transform="rotate(-90 11.5 11.5)" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </form>

                            <div class="bg-success text-white rounded-3 p-4 text-center mt-5">
                                <div class="fw-bold mb-1">🔒 Data Anda Aman</div>
                                <div class="small opacity-75">
                                    Setelah mengirim formulir ini, tim Greenray akan menghubungi Anda dalam 1-2 hari
                                    kerja untuk konfirmasi dan survey lokasi.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title h4" id="successModalLabel">✅ Pesanan Berhasil Dikirim!</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tim Greenray akan menghubungi Anda segera.</p>
                        <p class="mb-1">Nomor Pesanan Anda:</p>
                        <h3 class="fw-bold" id="order-number-display">GR-XXXXX</h3>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-lg btn-step7-submit w-100" data-bs-dismiss="modal">
                            <span>OK, Mengerti</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="backModal" tabindex="-1" aria-labelledby="backModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="backModalLabel">Konfirmasi Kembali</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin kembali ke Halaman Utama? Data yang sudah Anda isi di form ini akan
                        hilang.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-lg btn-step7-back" data-bs-dismiss="modal">
                            <span>Batal</span>
                        </button>
                        <a href="home.php" role="button" class="btn btn-lg btn-outline-dark">
                            Ya, Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="submitModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="submitModalLabel">Konfirmasi Pengiriman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Pastikan data yang Anda masukkan sudah benar. Apakah Anda yakin ingin mengirim pesanan ini?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-lg btn-step7-back" data-bs-dismiss="modal">
                            <span>Batal</span>
                        </button>
                        <button type="button" class="btn btn-lg btn-step7-submit" id="confirmSubmitBtn">
                            <span>Ya, Kirim Pesanan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="footer-logo-text">
                        <img class="green-ray-logo-12" src="../img/GreenRay_Logo 1-1.png" />
                        <div class="footer-desc">
                            Powering a cleaner, brighter future for Indonesia. We are your
                            trusted partner in sustainable energy solutions, built on
                            transparency and long-term value.
                        </div>
                    </div>
                </div>
                <div class="copyright">
                    © 2025 GreenRay. All rights reserved.
                </div>
            </div>
            <div class="footer-menu">
                <div class="menu-container-footer">
                    <div class="title-footer">Quick Links</div>
                    <div class="dec-container-footer">
                        <div class="list-footer"><a href="home.php">Home</a></div>
                        <div class="list-footer"><a href="../html/portofolio.html">Our Portfolio</a></div>
                        <div class="list-footer"><a href="../html/calc.html">Saving Calculator</a></div>
                    </div>
                </div>
                <div class="menu-container-footer">
                    <div class="title-footer">Get In Touch</div>
                    <div class="dec-container-footer">
                        <div class="list-footer">
                            <a href="../html/contact-us.html">Quick Consultation via WhatsApp</a>
                        </div>
                        <div class="list-footer">
                            <a href="../html/contact-us.html">Send a Formal Inquiry Email</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const TARIFF_PLN = <?php echo json_encode($tariff_js_map); ?>;
        const SOLAR_IRRADIANCE = <?php echo json_encode($location_js_map); ?>;
    </script>
    <script src="../javascript/dropdown.js"></script>
    <script src="../javascript/card-select.js"></script>
    <script src="../javascript/step-transition.js"></script>
    <script src="../javascript/calculation-logic.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="../javascript/validation-calc.js"></script>

    <?php
    // Jika variabel $sukses_order_number di-set oleh PHP di atas, jalankan skrip ini
    if (!empty($sukses_order_number)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                // Ambil modal Bootstrap
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                
                // Set nomor pesanan yang BENAR (dari PHP)
                document.getElementById('order-number-display').innerText = '" . htmlspecialchars($sukses_order_number) . "';
                
                // Tampilkan modal
                successModal.show();
            });
        </script>";
    }
    ?>

</body>

</html>