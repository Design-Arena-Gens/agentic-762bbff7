<?php
require_once __DIR__ . '/../config.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($AIPS_SETTINGS['site_name']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --aips-green: <?php echo $AIPS_SETTINGS['brand_green']; ?>;
      --aips-blue: <?php echo $AIPS_SETTINGS['brand_blue']; ?>;
    }
    .aips-navbar { background: var(--aips-blue); }
    .aips-navbar .nav-link, .aips-navbar .navbar-brand { color: #fff !important; }
    .aips-cta { background: linear-gradient(135deg, var(--aips-green), var(--aips-blue)); color: #fff; }
    .aips-btn { background: var(--aips-green); color: #fff; border: none; }
    .aips-btn:hover { background: #026833; color: #fff; }
    .aips-price { color: var(--aips-green); font-weight: 600; }
    .aips-footer { background: #0f143a; color: #cbd5e1; }
    .aips-badge { background: var(--aips-green); }
    a { text-decoration: none; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg aips-navbar">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/">
      <img src="/<?php echo get_logo_path(); ?>" alt="AIPS" style="height:36px"/>
      <span class="fw-semibold">AIPS</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/shop.php">Shop</a></li>
        <li class="nav-item"><a class="nav-link" href="/cart.php">Cart <?php $count = 0; if (!empty($_SESSION['cart'])) { foreach ($_SESSION['cart'] as $it) { $count += (int)$it['quantity']; } } echo $count ? '<span class=\'badge aips-badge ms-1\'>' . $count . '</span>' : ''; ?></a></li>
        <li class="nav-item"><a class="nav-link" href="/contact.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="/admin/">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>
<main>