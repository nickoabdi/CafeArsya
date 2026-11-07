<?php
$role = $this->session->userdata('role');
$nama = (string) $this->session->userdata('nama');
$sidebar = [
  ['beranda', 'Dashboard',         ['admin', 'kasir'],         'fa-solid fa-chart-pie'],
  ['kasir',   'Buat Pesanan',      ['admin', 'kasir'],         'fa-solid fa-cart-plus'],
  ['bayar',   'Bayar Pesanan',     ['admin', 'kasir'],         'fa-solid fa-cash-register'],
  ['riwayat', 'Riwayat Transaksi', ['admin', 'kasir'],         'fa-solid fa-clock-rotate-left'],
  ['menu',    'Kelola Menu',       ['admin', 'kasir'],         'fa-solid fa-utensils'],
  ['dapur',   'Monitor Dapur',     ['admin', 'kasir', 'dapur'],'fa-solid fa-fire-burner'],
  ['akun',    'Kelola Users',      ['admin'],                  'fa-solid fa-users-gear'],
  ['laporan', 'Laporan Penjualan', ['admin', 'kasir'],         'fa-solid fa-file-invoice-dollar'],
];
$css_ver = file_exists(FCPATH . 'assets/css/app.css') ? filemtime(FCPATH . 'assets/css/app.css') : time();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($judul ?? 'Cafe Arsa'); ?> - Cafe Arsa POS</title>
  
  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- FontAwesome 6 Icons (Local First + CDN Fallback) -->
  <link rel="stylesheet" href="<?= base_url('assets/vendor/fontawesome/all.min.css'); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- App Unified CSS with Cache-Buster -->
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css?v=' . $css_ver); ?>">

  <!-- Critical Layout Direct Style to Prevent Stale Caching Glitches -->
  <style>
    body { background-color: #f7f4ee !important; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
    
    /* Ensure FontAwesome pseudo-elements always use FontAwesome font */
    .fa, .fa-solid, .fa-regular, .fa-brands, [class*="fa-"] {
      font-family: "Font Awesome 6 Free" !important;
      font-style: normal !important;
      display: inline-block !important;
      line-height: 1 !important;
    }
    .fa, .fa-solid { font-weight: 900 !important; }
    .fa-regular { font-weight: 400 !important; }
    .fa-brands { font-family: "Font Awesome 6 Brands" !important; font-weight: 400 !important; }

    .app { display: flex; min-height: 100vh; background-color: #f7f4ee; }
    .side { width: 250px !important; background: #1e120a !important; color: #eedcc9 !important; display: flex !important; flex-direction: column !important; position: fixed !important; top: 0; bottom: 0; left: 0; z-index: 50; padding: 20px 14px 16px !important; box-shadow: 4px 0 24px rgba(0,0,0,0.2); }
    .isi { margin-left: 250px !important; flex: 1 !important; min-width: 0 !important; padding: 24px 32px 60px !important; display: flex !important; flex-direction: column !important; }
    
    /* Topbar Header Card */
    .topbar {
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      background: #ffffff !important;
      padding: 14px 22px !important;
      border-radius: 14px !important;
      border: 1px solid #ede5da !important;
      box-shadow: 0 2px 8px rgba(26, 15, 8, 0.04) !important;
      margin-bottom: 24px !important;
      width: 100% !important;
      box-sizing: border-box !important;
    }
    .topbar-left { display: flex !important; align-items: center !important; gap: 14px !important; }
    .topbar-title h1, .topbar-title .page-jdl { font-size: 20px !important; font-weight: 800 !important; color: #1f140c !important; margin: 0 !important; line-height: 1.2 !important; }
    .topbar-title p, .topbar-title .page-sub { font-size: 12px !important; color: #7d6b5f !important; margin: 2px 0 0 !important; }
    .topbar-right { display: flex !important; align-items: center !important; gap: 10px !important; }
    .topbar-badge { display: inline-flex !important; align-items: center !important; gap: 6px !important; padding: 6px 14px !important; background: #faf7f2 !important; border: 1px solid #ede5da !important; border-radius: 9999px !important; font-size: 12px !important; font-weight: 600 !important; color: #6f5f55 !important; }
    .topbar-status { display: inline-flex !important; align-items: center !important; gap: 6px !important; padding: 6px 14px !important; background: #ecfdf5 !important; border: 1px solid #a7f3d0 !important; color: #15803d !important; border-radius: 9999px !important; font-size: 11.5px !important; font-weight: 700 !important; }
    .status-dot { width: 8px !important; height: 8px !important; border-radius: 50% !important; background: #15803d !important; display: inline-block !important; box-shadow: 0 0 0 3px rgba(21, 128, 61, 0.2) !important; }

    /* Metric Grid & Cards */
    .stats-grid, .stats {
      display: grid !important;
      grid-template-columns: repeat(4, 1fr) !important;
      gap: 16px !important;
      margin-bottom: 24px !important;
      width: 100% !important;
      box-sizing: border-box !important;
    }
    @media (max-width: 1100px) { .stats-grid, .stats { grid-template-columns: repeat(2, 1fr) !important; } }
    @media (max-width: 600px) { .stats-grid, .stats { grid-template-columns: 1fr !important; } }

    .stat-card, .stat {
      background: #ffffff !important;
      border: 1px solid #ede5da !important;
      border-radius: 14px !important;
      padding: 18px 20px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      box-shadow: 0 2px 8px rgba(26, 15, 8, 0.04) !important;
      box-sizing: border-box !important;
    }
    .stat-card .lbl { font-size: 11px !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.8px !important; color: #7d6b5f !important; display: block !important; margin-bottom: 4px !important; }
    .stat-card .stat-value { display: block !important; font-size: 24px !important; font-weight: 800 !important; color: #1f140c !important; line-height: 1.2 !important; }
    .stat-icon { width: 48px !important; height: 48px !important; border-radius: 12px !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 20px !important; flex-shrink: 0 !important; background: #fbf6f0 !important; color: #7b4b2a !important; border: 1px solid #f4e3cf !important; margin-left: 12px !important; }

    /* Sidebar Footer User */
    .side-footer { margin-top: auto !important; padding-top: 14px !important; border-top: 1px solid rgba(255, 255, 255, 0.08) !important; }
    .side-user-card { display: flex !important; align-items: center !important; gap: 10px !important; padding: 10px 12px !important; background: rgba(255, 255, 255, 0.06) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 10px !important; margin-bottom: 8px !important; }
    .side-user-avatar { width: 36px !important; height: 36px !important; border-radius: 50% !important; background: linear-gradient(135deg, #d49544, #7b4b2a) !important; color: #ffffff !important; display: flex !important; align-items: center !important; justify-content: center !important; font-weight: 800 !important; font-size: 14px !important; flex-shrink: 0 !important; border: 1.5px solid rgba(255, 255, 255, 0.25) !important; }
    .side-user-info { flex: 1 !important; min-width: 0 !important; }
    .side-user-name { font-size: 13px !important; font-weight: 700 !important; color: #ffffff !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; line-height: 1.2 !important; }
    .side-user-role { display: inline-block !important; font-size: 10px !important; font-weight: 700 !important; text-transform: uppercase !important; color: #e3ab5e !important; background: rgba(212, 149, 68, 0.2) !important; padding: 2px 7px !important; border-radius: 9999px !important; margin-top: 3px !important; }
    .btn-logout { display: flex !important; align-items: center !important; justify-content: center !important; gap: 8px !important; width: 100% !important; padding: 9px 12px !important; border-radius: 10px !important; font-size: 12.5px !important; font-weight: 600 !important; color: #fca5a5 !important; background: rgba(185, 28, 28, 0.15) !important; border: 1px solid rgba(185, 28, 28, 0.3) !important; text-decoration: none !important; }
    .btn-logout:hover { background: #b91c1c !important; color: #ffffff !important; }

    /* Search Box & Input Icon Fixes (Prevent Overlap) */
    .search-box, .search-wrapper, .input-field-wrapper {
      position: relative !important;
    }
    .search-box i, .search-wrapper i {
      position: absolute !important;
      left: 18px !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      color: #a9998d !important;
      font-size: 15px !important;
      pointer-events: none !important;
      z-index: 3 !important;
      line-height: 1 !important;
    }
    .search-box input, .search-wrapper input {
      width: 100% !important;
      padding-left: 48px !important;
      padding-right: 18px !important;
      border-radius: 9999px !important;
      box-sizing: border-box !important;
    }
    .input-field-wrapper i.prefix-icon {
      position: absolute !important;
      left: 18px !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      color: #a9998d !important;
      font-size: 15px !important;
      pointer-events: none !important;
      z-index: 3 !important;
      line-height: 1 !important;
    }
    .input-field-wrapper input {
      width: 100% !important;
      padding-left: 48px !important;
      padding-right: 48px !important;
      box-sizing: border-box !important;
    }

    /* Menu Without Image Badge (No Image Text) */
    .nofoto {
      width: 100% !important;
      height: 125px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      background: #f7f3ed !important;
      border-bottom: 1px solid #ede5da !important;
      user-select: none !important;
    }
    .nofoto span {
      display: inline-block !important;
      font-size: 11px !important;
      font-weight: 700 !important;
      letter-spacing: 0.8px !important;
      text-transform: uppercase !important;
      color: #8c7667 !important;
      background: rgba(255, 255, 255, 0.9) !important;
      padding: 6px 14px !important;
      border-radius: 9999px !important;
      border: 1px solid rgba(140, 118, 103, 0.25) !important;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
      text-align: center !important;
    }

    @media (max-width: 900px) {
      .side { transform: translateX(-100%) !important; }
      .side.buka { transform: translateX(0) !important; }
      .isi { margin-left: 0 !important; padding: 16px !important; }
      .burger { display: flex !important; }
    }
  </style>
</head>
<body>
<div class="app">

  <!-- Mobile Backdrop Overlay -->
  <div class="sidebar-overlay" id="sideOverlay"></div>

  <!-- Sidebar Navigation -->
  <aside class="side" id="side">
    <div class="brand">
      <div class="brand-icon">
        <i class="fa-solid fa-mug-hot"></i>
      </div>
      <div class="brand-text">
        <h2>CAFE ARSA</h2>
        <span>Coffee &amp; Resto</span>
      </div>
    </div>

    <div class="side-nav-heading">Navigasi Utama</div>
    <nav>
      <?php foreach ($sidebar as $s): if (!in_array($role, $s[2])) continue; ?>
        <a href="<?= site_url($s[0]); ?>" class="<?= ($aktif ?? '') == $s[0] ? 'on' : ''; ?>">
          <i class="<?= $s[3]; ?>"></i>
          <span><?= $s[1]; ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="side-footer">
      <div class="side-user-card">
        <div class="side-user-avatar">
          <?= htmlspecialchars(strtoupper(substr($nama ?: 'U', 0, 1))); ?>
        </div>
        <div class="side-user-info">
          <div class="side-user-name" title="<?= htmlspecialchars($nama); ?>"><?= htmlspecialchars($nama); ?></div>
          <span class="side-user-role"><?= ucfirst($role); ?></span>
        </div>
      </div>
      <a class="btn-logout" href="<?= site_url('auth/logout'); ?>" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
        <span>Keluar</span>
      </a>
    </div>
  </aside>

  <!-- Main Body Content -->
  <main class="isi">
    <!-- Topbar Header -->
    <header class="topbar">
      <div class="topbar-left">
        <button class="burger" id="burger" type="button" aria-label="Toggle Navigation">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div class="topbar-title">
          <h1 class="page-jdl"><?= htmlspecialchars($judul ?? 'Dashboard'); ?></h1>
          <p class="page-sub">Sistem Manajemen &amp; Kasir Cafe Arsa</p>
        </div>
      </div>

      <div class="topbar-right">
        <span class="topbar-badge">
          <i class="fa-regular fa-calendar-check" style="color: var(--gold-500);"></i>
          <?= date('d M Y'); ?>
        </span>
        <span class="topbar-status">
          <span class="status-dot"></span>
          <span>Buka</span>
        </span>
      </div>
    </header>