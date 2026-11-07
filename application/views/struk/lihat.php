<?php
$lunas = $p->status_bayar == 'lunas';
$waktu = $lunas ? $p->dibayar_pada : $p->dibuat;
$rp = function ($n) { return 'Rp ' . number_format($n, 0, ',', '.'); };
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Struk #<?= htmlspecialchars($p->kode); ?> - Cafe Arsa</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/vendor/fontawesome/all.min.css'); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Space Mono', monospace;
      font-size: 12.5px;
      color: #1a0f08;
      background: #f4efe9;
      padding: 24px 12px;
      min-height: 100vh;
    }

    .struk-card {
      width: 78mm;
      max-width: 100%;
      margin: 0 auto;
      background: #ffffff;
      padding: 24px 20px;
      border-radius: 8px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      position: relative;
    }

    /* Sawtooth edge effect for top and bottom */
    .struk-header {
      text-align: center;
      margin-bottom: 14px;
    }
    .struk-brand {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-weight: 800;
      font-size: 19px;
      letter-spacing: 1px;
      color: #1a0f08;
      line-height: 1.2;
    }
    .struk-sub {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 11px;
      color: #6f5f55;
      margin-top: 2px;
    }

    hr.dashed {
      border: none;
      border-top: 1.5px dashed #44372f;
      margin: 10px 0;
    }

    .baris {
      display: flex;
      justify-content: space-between;
      gap: 8px;
      font-size: 12px;
      line-height: 1.45;
    }

    .item-block {
      margin: 8px 0;
    }
    .item-nama {
      font-weight: 700;
      font-size: 12px;
      margin-bottom: 2px;
    }
    .item-calc {
      display: flex;
      justify-content: space-between;
      font-size: 11.5px;
      color: #38251a;
    }

    .tebal {
      font-weight: 700;
      font-size: 14px;
    }

    .footer-msg {
      text-align: center;
      font-size: 11px;
      margin-top: 14px;
      color: #44372f;
      line-height: 1.4;
    }

    .tombol-bar {
      text-align: center;
      margin-top: 20px;
      display: flex;
      justify-content: center;
      gap: 10px;
    }

    .btn-act {
      font-family: 'Plus Jakarta Sans', sans-serif;
      padding: 9px 18px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border: 1px solid #d4c8bc;
      background: #ffffff;
      color: #2c1a0e;
      transition: all 0.2s;
    }
    .btn-act:hover {
      background: #eadecd;
    }
    .btn-act.print {
      background: #422715;
      color: #ffffff;
      border-color: #422715;
    }
    .btn-act.print:hover {
      background: #20130a;
    }

    @media print {
      body {
        background: #ffffff;
        padding: 0;
      }
      .struk-card {
        width: 100%;
        margin: 0;
        padding: 0;
        box-shadow: none;
        border-radius: 0;
      }
      .tombol-bar {
        display: none !important;
      }
      @page {
        size: 80mm auto;
        margin: 2mm;
      }
    }
  </style>
</head>
<body>

  <div class="struk-card">
    <div class="struk-header">
      <div class="struk-brand">CAFE ARSA</div>
      <div class="struk-sub">Artisan Coffee &amp; Eatery</div>
      <div class="struk-sub">Jl. Sukasenang No. 42 &bull; 0812-3456-7890</div>
    </div>

    <hr class="dashed">

    <div class="baris">
      <span>No. Resi</span>
      <b>#<?= htmlspecialchars($p->kode); ?></b>
    </div>
    <div class="baris">
      <span>Waktu</span>
      <span><?= date('d/m/Y H:i', strtotime($waktu)); ?></span>
    </div>
    <div class="baris">
      <span>Pelanggan</span>
      <span><?= htmlspecialchars($p->nama_pelanggan ?: 'Umum'); ?></span>
    </div>
    <?php if ($p->meja): ?>
      <div class="baris">
        <span>No. Meja</span>
        <b>Meja <?= htmlspecialchars($p->meja); ?></b>
      </div>
    <?php endif; ?>
    <?php if ($p->kasir): ?>
      <div class="baris">
        <span>Kasir</span>
        <span><?= htmlspecialchars($p->kasir); ?></span>
      </div>
    <?php endif; ?>

    <hr class="dashed">

    <?php foreach ($item as $i): ?>
      <div class="item-block">
        <div class="item-nama"><?= htmlspecialchars($i->nama); ?></div>
        <div class="item-calc">
          <span><?= $i->qty; ?> x <?= number_format($i->harga, 0, ',', '.'); ?></span>
          <b><?= number_format($i->subtotal, 0, ',', '.'); ?></b>
        </div>
      </div>
    <?php endforeach; ?>

    <hr class="dashed">

    <div class="baris tebal" style="font-size: 15px; margin-bottom: 4px;">
      <span>TOTAL</span>
      <span><?= $rp($p->total); ?></span>
    </div>

    <?php if ($lunas): ?>
      <div class="baris">
        <span>Metode Bayar</span>
        <span><?= strtoupper($p->metode); ?></span>
      </div>
      <?php if ($p->metode == 'tunai'): ?>
        <div class="baris">
          <span>Tunai Diterima</span>
          <span><?= $rp($p->bayar); ?></span>
        </div>
        <div class="baris">
          <span>Kembalian</span>
          <span><?= $rp($p->kembali); ?></span>
        </div>
      <?php endif; ?>
      <hr class="dashed">
      <div class="footer-msg">
        <b>TERIMA KASIH ATAS KUNJUNGAN ANDA!</b><br>
        Selamat menikmati hidangan spesial Cafe Arsa.
      </div>
    <?php else: ?>
      <hr class="dashed">
      <div class="footer-msg" style="font-weight:700; color:#b91c1c;">
        <?= $p->status_bayar == 'batal' ? '*** PESANAN DIBATALKAN ***' : '*** BELUM LUNAS / MENUNGGU BAYAR ***'; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="tombol-bar">
    <button class="btn-act print" onclick="window.print()">
      <i class="fa-solid fa-print"></i> Cetak Struk
    </button>
    <button class="btn-act" onclick="window.close()">
      <i class="fa-solid fa-xmark"></i> Tutup
    </button>
  </div>

  <?php if ($this->input->get('cetak')): ?>
  <script>
    window.onload = function () {
      setTimeout(function() { window.print(); }, 300);
    };
  </script>
  <?php endif; ?>

</body>
</html>