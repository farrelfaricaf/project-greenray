<?php
session_start();
include '../koneksi.php';



$segments = [];
$resSeg = $koneksi->query("
    SELECT s.slug, s.name, MIN(s.id) AS id
    FROM product_segments s
    JOIN product_segment_map m ON s.id = m.segment_id
    GROUP BY s.slug, s.name
    ORDER BY s.name ASC
");
if ($resSeg) {
    while ($row = $resSeg->fetch_assoc()) {
        $segments[] = $row;
    }
}


$active_segment = $_GET['segment'] ?? 'all';


$products = [];

$sql = "
  SELECT
    p.*,
    GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ',') AS segment_names,
    GROUP_CONCAT(DISTINCT s.slug ORDER BY s.name SEPARATOR ',') AS segment_slugs
  FROM products p
  LEFT JOIN product_segment_map m ON p.id = m.product_id
  LEFT JOIN product_segments     s ON m.segment_id = s.id
";

$params = [];
$types = '';

if ($active_segment !== 'all') {
    $sql .= " WHERE s.slug = ? ";
    $types = 's';
    $params[] = $active_segment;
}

$sql .= " GROUP BY p.id ORDER BY p.id ASC";

$stmt = $koneksi->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}


$page_data = [];
$res_page = $koneksi->query("SELECT * FROM page_katalog WHERE id = 1");
if ($res_page && $res_page->num_rows > 0) {
    $page_data = $res_page->fetch_assoc();
} else {
    $page_data = [
        'header_title' => 'Our Products',
        'header_desc' => 'Explore our range of high-quality solar products.',
        'header_image' => 'img/cover-header.png'
    ];
}


function fixPath($path)
{
    return !empty($path) ? str_replace('../', '', $path) : 'img/placeholder.png';
}


$is_logged_in = isset($_SESSION['user_id']);
$user_name = '';
$profile_pic = '../img/default-profile.png';

if ($is_logged_in) {
    $user_name = $_SESSION['user_name'] ?? 'User';
    $profile_pic = $_SESSION['user_profile_pic'] ?? '../img/default-profile.png';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalog</title>

    <link rel="stylesheet" href="..\css\katalog.css">
    <link rel="stylesheet" href="..\css\globals.css">
    <link rel="stylesheet" href="..\css\styleguide.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">

    <style>
       
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-picture-header {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid #136000;
        }

        .dropdown-menu-header {
            display: none;
            position: absolute;
            right: 0;
            top: 60px;
            background-color: white;
            min-width: 180px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.1);
            z-index: 100;
            border-radius: 8px;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-align: left;
           
        }

        .dropdown-menu-header.show {
            display: block;
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

       
        .card-img-top {
            height: 200px;
            object-fit: contain;
            padding: 10px;
            background-color: #f8f9fa;
        }

       
        .custom-bullet-list {
            list-style-type: disc;
            padding-left: 1.5rem;
            text-align: left;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .custom-bullet-list li {
            color: #6c757d;
            margin-bottom: 5px;
            font-size: 0.95rem;
        }

        .catalog-header-img {
            width: 100%;
           
            height: auto;
           
            max-height: 350px;
           
            object-fit: cover;
           
            object-position: center;
           
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .catalog-header-wrapper {
            width: 100%;
            max-width: 1280px;
           
            margin: 0 auto 40px auto;
           
            text-align: center;
           
        }

       
       
       

        .catalog-filter-bar {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            margin: 0.75rem 0 1.5rem 0;
        }

        .filter-label {
            color: var(--hitam);
            font-size: 0.9rem;
            font-weight: 500;
        }

       
        .filter-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.4rem 1.1rem;
            border-radius: 999px;
            border: 0.125rem solid var(--hitam);
            background: var(--putih);
            color: var(--hitam);
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition:
                background-color 0.2s ease,
                color 0.2s ease,
                box-shadow 0.2s ease,
                border-color 0.2s ease,
                transform 0.15s ease;
        }

        .filter-pill:hover {
            background-color: #f3f3f3;
            transform: translateY(-1px);
        }

       
        .filter-pill.is-active {
            background-color: var(--hijau);
            border-color: var(--hijau);
            color: var(--putih);
            box-shadow: 0rem 0.0625rem 0.1875rem rgba(0, 0, 0, 0.26),
                0rem 0.3125rem 0.3125rem rgba(0, 0, 0, 0.23),
                0rem 0.6875rem 0.4375rem rgba(0, 0, 0, 0.13);
        }

       
       
       

        @media (max-width: 768px) {
            .catalog-filter-bar {
                gap: 0.5rem;
                margin-bottom: 1.25rem;
            }

            .filter-label {
                width: 100%;
                text-align: center;
                margin-bottom: 0.25rem;
            }

            .filter-pill {
                font-size: 0.85rem;
                padding: 0.35rem 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .catalog-filter-bar {
                padding: 0 0.5rem;
            }

            .filter-pill {
                flex: 1 1 auto;
               
                text-align: center;
            }
        }

        .product-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .tag-pill {
            display: inline-block;
            padding: 0.12rem 0.6rem;
            border-radius: 999px;
            background: #e6f3ff;
            color: #1b3b6f;
            font-size: 0.78rem;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="beranda">

        <div class="header-wrapper">
            <?php include 'includes/header.php'; ?>
        </div>

        <div class="row mt-5 hero-wrapper">
            <div class="catalog-header-wrapper">
                <img class="img-head w-100 rounded-3 shadow catalog-header-img"
                    src="../<?php echo fixPath($page_data['header_image']); ?>" alt="Catalog Header" />
            </div>
        </div>

        <div class="big-container">
            <div class="row justify-content-center text-center mb-4">
                <div class="col-lg-8">
                    <h2 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($page_data['header_title']); ?></h2>
                    <p class="lead text-secondary">
                        <?php echo nl2br(htmlspecialchars($page_data['header_desc'])); ?>
                    </p>
                </div>
            </div>

            <div class="catalog-filter-bar">
                <span class="filter-label">Filter:</span>

                <!-- All -->
                <a href="katalog.php?segment=all"
                    class="btn btn-outline-dark <?php echo ($active_segment === 'all') ? 'active' : ''; ?>">
                    All
                </a>

                <!-- Segment dari DB -->
                <?php foreach ($segments as $seg): ?>
                    <a href="katalog.php?segment=<?php echo urlencode($seg['slug']); ?>"
                        class="btn btn-outline-dark <?php echo ($active_segment === $seg['slug']) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($seg['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="card-container">

                <?php if (empty($products)): ?>
                    <div class="col-12 text-center w-100">
                        <p class="text-muted">Belum ada produk yang ditambahkan.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                        
                        $features = json_decode($product['key_features_json'], true);
                        
                        $display_features = array_slice($features ?? [], 0, 2);
                        ?>
                        <div class="card card-solar">
                            <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top"
                                alt="<?php echo htmlspecialchars($product['name']); ?>">

                            <div class="card-body">
                                <h5 class="card-title text-center">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                    <br>
                                    <span style="font-size: 0.9em; font-weight: normal;">
                                        <?php echo htmlspecialchars($product['subtitle']); ?>
                                    </span>
                                </h5>

                                <?php if (!empty($product['segment_names'])): ?>
                                    <div class="product-tags mt-2">
                                        <?php foreach (explode(',', $product['segment_names']) as $tagName): ?>
                                            <span class="tag-pill">
                                                <?php echo htmlspecialchars(trim($tagName)); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <ul class="custom-bullet-list">
                                    <?php if (!empty($display_features)): ?>
                                        <?php foreach ($display_features as $feat): ?>
                                            <li><?php echo htmlspecialchars($feat['title']); ?></li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li style="list-style: none;">No features listed</li>
                                    <?php endif; ?>
                                </ul>
                            </div>

                            <a href="katalog_detail.php?slug=<?php echo htmlspecialchars($product['slug']); ?>"
                                class="btn btn-cta-solar">
                                <span>View Details</span>
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>


        <?php include 'includes/footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
</body>

</html>