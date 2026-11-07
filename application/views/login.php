<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Cafe Arsa POS</title>
  
  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- FontAwesome 6 Icons (Local First + CDN Fallback) -->
  <link rel="stylesheet" href="<?= base_url('assets/vendor/fontawesome/all.min.css'); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Main Stylesheet with Cache-Buster -->
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css?v=' . (file_exists(FCPATH . 'assets/css/app.css') ? filemtime(FCPATH . 'assets/css/app.css') : time())); ?>">

  <style>
    .login-container {
      display: flex;
      min-height: 100vh;
      background: var(--coffee-900);
    }
    
    .login-art {
      flex: 1.2;
      position: relative;
      background: #25160d url('<?= base_url("assets/img/login.jpg"); ?>') center center/cover no-repeat;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 56px 48px;
      color: #ffffff;
      overflow: hidden;
    }

    .login-art::before {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(26, 15, 8, 0.9) 0%, rgba(66, 39, 21, 0.78) 100%);
      backdrop-filter: blur(2px);
    }

    .art-content {
      position: relative;
      z-index: 2;
    }

    .art-brand {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .art-brand-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--gold-500), var(--coffee-500));
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      box-shadow: 0 4px 14px rgba(212, 149, 68, 0.4);
    }

    .art-brand h1 {
      font-size: 22px;
      font-weight: 800;
      letter-spacing: 0.5px;
      margin: 0;
      line-height: 1.2;
    }

    .art-brand span {
      font-size: 11px;
      color: var(--gold-400);
      font-weight: 600;
      letter-spacing: 1.5px;
      text-transform: uppercase;
    }

    .art-hero {
      max-width: 520px;
      margin: auto 0;
      padding: 40px 0;
    }

    .art-hero h2 {
      font-size: 38px;
      font-weight: 800;
      line-height: 1.25;
      letter-spacing: -0.5px;
      margin-bottom: 14px;
      color: #fff;
    }

    .art-hero p {
      font-size: 15px;
      color: #dfcebf;
      line-height: 1.6;
      margin-bottom: 24px;
    }

    .art-pills {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .art-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.15);
      padding: 8px 14px;
      border-radius: var(--radius-full);
      font-size: 12.5px;
      font-weight: 600;
      color: #f7ede2;
      backdrop-filter: blur(4px);
    }

    .art-footer {
      font-size: 12px;
      color: #a89485;
    }

    .login-box-area {
      flex: 1;
      max-width: 500px;
      background: var(--bg-body);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 32px;
    }

    .login-card {
      width: 100%;
      max-width: 380px;
      background: #ffffff;
      padding: 36px 32px;
      border-radius: var(--radius-xl);
      border: 1px solid var(--border-subtle);
      box-shadow: var(--shadow-lg);
    }

    .login-card-header {
      margin-bottom: 24px;
      text-align: left;
    }

    .login-card-header h2 {
      font-size: 24px;
      font-weight: 800;
      color: var(--text-title);
      margin-bottom: 4px;
    }

    .login-card-header p {
      font-size: 13px;
      color: var(--text-muted);
    }

    .input-group {
      margin-bottom: 18px;
      position: relative;
    }

    .input-group label {
      font-size: 12px;
      font-weight: 700;
      color: var(--text-muted);
      margin-bottom: 6px;
      display: block;
    }

    .input-field-wrapper {
      position: relative;
      width: 100%;
    }

    .input-field-wrapper i.prefix-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
      font-size: 15px;
      pointer-events: none;
      z-index: 5;
      line-height: 1;
    }

    .input-field-wrapper input {
      width: 100% !important;
      padding: 12px 48px 12px 48px !important;
      border: 1px solid var(--border-subtle) !important;
      border-radius: var(--radius-sm) !important;
      background: var(--bg-input) !important;
      color: var(--text-title) !important;
      font-size: 14px !important;
      box-sizing: border-box !important;
      outline: none !important;
      transition: border-color var(--transition-fast), box-shadow var(--transition-fast) !important;
    }

    .input-field-wrapper input:focus {
      border-color: var(--coffee-500) !important;
      box-shadow: 0 0 0 3px rgba(123, 75, 42, 0.12) !important;
    }

    .btn-toggle-pw {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--text-light);
      cursor: pointer;
      font-size: 15px;
      padding: 6px;
      line-height: 1;
      z-index: 5;
      transition: color var(--transition-fast);
    }

    .btn-toggle-pw:hover {
      color: var(--text-title);
    }

    .btn-login-submit {
      width: 100%;
      padding: 13px;
      font-size: 14.5px;
      font-weight: 700;
      border-radius: var(--radius-sm);
      margin-top: 8px;
      box-shadow: 0 4px 14px rgba(45, 27, 15, 0.25);
    }

    @media (max-width: 900px) {
      .login-art {
        display: none;
      }
      .login-box-area {
        max-width: 100%;
      }
    }
  </style>
</head>
<body>

<div class="login-container">
  <!-- SHOWCASE SIDEBAR BANNER -->
  <div class="login-art">
    <div class="art-content art-brand">
      <div class="art-brand-icon">
        <i class="fa-solid fa-mug-hot"></i>
      </div>
      <div>
        <h1>CAFE ARSA</h1>
        <span>Artisan Coffee &amp; Eatery</span>
      </div>
    </div>

    <div class="art-content art-hero">
      <h2>Sistem Kasir &amp; Operasional Cerdas</h2>
      <p>Manajemen pesanan cepat dari kasir ke dapur, pencatatan transaksi otomatis, dan analisis omzet harian secara akurat dan realtime.</p>
      
      <div class="art-pills">
        <div class="art-pill">
          <i class="fa-solid fa-bolt" style="color:var(--gold-400);"></i>
          <span>Cepat &amp; Efisien</span>
        </div>
        <div class="art-pill">
          <i class="fa-solid fa-chart-pie" style="color:var(--gold-400);"></i>
          <span>Laporan Realtime</span>
        </div>
        <div class="art-pill">
          <i class="fa-solid fa-fire-burner" style="color:var(--gold-400);"></i>
          <span>Kitchen Display</span>
        </div>
      </div>
    </div>

    <div class="art-content art-footer">
      &copy; <?= date('Y'); ?> Cafe Arsa. Hak Cipta Dilindungi Undang-Undang.
    </div>
  </div>

  <!-- LOGIN FORM SECTION -->
  <div class="login-box-area">
    <div class="login-card">
      <div class="login-card-header">
        <h2>Selamat Datang</h2>
        <p>Silakan masuk menggunakan akun staf Anda</p>
      </div>

      <?php if ($this->session->flashdata('error')): ?>
        <div class="flash f-err" style="margin-bottom: 18px;">
          <i class="fa-solid fa-circle-exclamation"></i>
          <span><?= $this->session->flashdata('error'); ?></span>
        </div>
      <?php endif; ?>

      <?= form_open('auth/proses'); ?>
        <div class="input-group">
          <label>Username</label>
          <div class="input-field-wrapper">
            <i class="fa-regular fa-user prefix-icon"></i>
            <input type="text" name="username" placeholder="Masukkan username" required autofocus>
          </div>
        </div>

        <div class="input-group">
          <label>Password</label>
          <div class="input-field-wrapper">
            <i class="fa-solid fa-lock prefix-icon"></i>
            <input type="password" name="password" id="pw" placeholder="Masukkan password" required>
            <button type="button" class="btn-toggle-pw" id="mata" aria-label="Toggle password">
              <i class="fa-regular fa-eye" id="mata-icon"></i>
            </button>
          </div>
        </div>

        <button class="btn btn-primary btn-login-submit" type="submit">
          <span>Masuk ke Sistem</span>
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      <?= form_close(); ?>

      <div style="text-align:center; margin-top: 24px; font-size: 11.5px; color: var(--text-light);">
        <span>Cafe Arsa POS v2.0 &bull; Akses Terbatas Staf</span>
      </div>
    </div>
  </div>
</div>

<script>
  (function() {
    var p = document.getElementById('pw');
    var mata = document.getElementById('mata');
    var icon = document.getElementById('mata-icon');

    if (mata && p) {
      mata.addEventListener('click', function () {
        var isPw = p.type === 'password';
        p.type = isPw ? 'text' : 'password';
        icon.className = isPw ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
      });
    }
  })();
</script>
</body>
</html>
