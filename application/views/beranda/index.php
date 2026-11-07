<?php $this->load->view('layout/header', ['judul' => 'Dashboard Utama', 'aktif' => 'beranda']); ?>

<!-- 1. METRICS / STATS CARDS -->
<div class="stats-grid">
  <!-- Pesanan Hari Ini -->
  <div class="stat-card">
    <div>
      <span class="lbl">Pesanan Hari Ini</span>
      <b class="stat-value"><?= number_format($total_pesanan); ?></b>
      <small style="color: var(--text-muted); margin-top: 4px;">
        <i class="fa-solid fa-receipt" style="color: var(--gold-500); margin-right: 4px;"></i>Transaksi aktif
      </small>
    </div>
    <div class="stat-icon">
      <i class="fa-solid fa-bag-shopping"></i>
    </div>
  </div>

  <!-- Omzet Hari Ini -->
  <div class="stat-card omzet">
    <div>
      <span class="lbl">Omzet Hari Ini</span>
      <b class="stat-value" style="color: var(--success);">Rp <?= number_format($omzet, 0, ',', '.'); ?></b>
      <?php if (isset($persen) && $persen !== NULL): ?>
        <small style="margin-top: 4px; font-weight: 700; color: <?= $persen >= 0 ? 'var(--success)' : 'var(--danger)'; ?>;">
          <i class="fa-solid fa-arrow-trend-<?= $persen >= 0 ? 'up' : 'down'; ?>"></i>
          <?= abs($persen); ?>% vs kemarin
        </small>
      <?php else: ?>
        <small style="color: var(--text-muted); margin-top: 4px;">
          <i class="fa-solid fa-coins" style="color: var(--gold-500); margin-right: 4px;"></i>Total pendapatan
        </small>
      <?php endif; ?>
    </div>
    <div class="stat-icon" style="background: var(--success-bg); color: var(--success); border-color: var(--success-border);">
      <i class="fa-solid fa-wallet"></i>
    </div>
  </div>

  <!-- Pelanggan Hari Ini -->
  <div class="stat-card">
    <div>
      <span class="lbl">Pelanggan</span>
      <b class="stat-value"><?= number_format($pelanggan); ?></b>
      <small style="color: var(--text-muted); margin-top: 4px;">
        <i class="fa-solid fa-user-check" style="color: var(--gold-500); margin-right: 4px;"></i>Pelanggan unik
      </small>
    </div>
    <div class="stat-icon" style="background: var(--gold-100); color: var(--gold-500); border-color: var(--gold-300);">
      <i class="fa-solid fa-users"></i>
    </div>
  </div>

  <!-- Menu Aktif -->
  <div class="stat-card">
    <div>
      <span class="lbl">Menu Aktif</span>
      <b class="stat-value"><?= number_format($menu_aktif); ?></b>
      <small style="color: var(--text-muted); margin-top: 4px;">
        <i class="fa-solid fa-utensils" style="color: var(--gold-500); margin-right: 4px;"></i>Siap dipesan
      </small>
    </div>
    <div class="stat-icon" style="background: var(--coffee-100); color: var(--coffee-700); border-color: var(--coffee-200);">
      <i class="fa-solid fa-mug-saucer"></i>
    </div>
  </div>
</div>

<!-- 2. GRAFIK OMZET & PESANAN TERBARU -->
<div class="grid2" style="align-items: stretch; margin-bottom: 24px;">
  <!-- Grafik Omzet 7 Hari -->
  <div class="kartu" style="display: flex; flex-direction: column; margin-bottom: 0;">
    <div class="jd" style="justify-content: space-between;">
      <div style="display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-chart-area" style="color: var(--coffee-500);"></i>
        <span>Tren Omzet (7 Hari Terakhir)</span>
      </div>
    </div>
    <div style="position: relative; flex: 1; min-height: 250px; width: 100%;">
      <canvas id="omzetChart"></canvas>
    </div>
  </div>

  <!-- Tabel Transaksi Terbaru -->
  <div class="kartu" style="display: flex; flex-direction: column; margin-bottom: 0;">
    <div class="jd" style="justify-content: space-between;">
      <div style="display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-list-check" style="color: var(--gold-500);"></i>
        <span>Transaksi Terbaru</span>
      </div>
      <a href="<?= site_url('riwayat'); ?>" style="font-size: 12px; color: var(--coffee-500); font-weight: 700;">
        Lihat Semua &rarr;
      </a>
    </div>

    <div class="tabel-wrapper" style="flex: 1; border: none;">
      <table>
        <thead>
          <tr>
            <th>Kode</th>
            <th>Pelanggan</th>
            <th>Total</th>
            <th class="c">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($terbaru)): ?>
            <?php foreach ($terbaru as $row): ?>
              <tr>
                <td>
                  <b style="color: var(--coffee-700); font-size: 12.5px;">#<?= htmlspecialchars($row->kode); ?></b>
                  <small><?= date('H:i', strtotime($row->dibuat)); ?> WIB</small>
                </td>
                <td>
                  <span style="font-weight: 600; color: var(--text-title);"><?= htmlspecialchars($row->nama_pelanggan ?: 'Pelanggan'); ?></span>
                </td>
                <td style="font-weight: 700; color: var(--text-title);">
                  Rp <?= number_format($row->total, 0, ',', '.'); ?>
                </td>
                <td class="c">
                  <?php if ($row->status_bayar == 'lunas'): ?>
                    <span class="badge b-lunas">Lunas</span>
                  <?php elseif ($row->status_bayar == 'batal'): ?>
                    <span class="badge b-batal">Batal</span>
                  <?php else: ?>
                    <span class="badge b-belum">Belum</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="kosong">Belum ada transaksi hari ini.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  (function() {
    const labelsData = <?= json_encode(isset($label) ? $label : []); ?>;
    const nilaiData = <?= json_encode(isset($nilai) ? $nilai : []); ?>;

    const ctx = document.getElementById('omzetChart').getContext('2d');
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 240);
    gradient.addColorStop(0, 'rgba(123, 75, 42, 0.28)');
    gradient.addColorStop(1, 'rgba(123, 75, 42, 0.01)');

    const maxVal = Math.max(...nilaiData, 0);

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labelsData,
        datasets: [{
          label: 'Omzet Penjualan',
          data: nilaiData,
          borderColor: '#7b4b2a',
          borderWidth: 3,
          backgroundColor: gradient,
          fill: true,
          tension: 0.35,
          pointBackgroundColor: '#d49544',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1a0f08',
            titleFont: { family: 'Plus Jakarta Sans', size: 12 },
            bodyFont: { family: 'Plus Jakarta Sans', size: 13, weight: 'bold' },
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: function(ctx) {
                return 'Rp ' + Number(ctx.raw).toLocaleString('id-ID');
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            min: 0,
            suggestedMax: maxVal > 0 ? maxVal * 1.15 : 100000,
            grid: { color: 'rgba(237, 229, 218, 0.6)' },
            ticks: {
              precision: 0,
              font: { family: 'Plus Jakarta Sans', size: 11 },
              color: '#7d6b5f',
              callback: function(v) {
                return 'Rp ' + Number(v).toLocaleString('id-ID');
              }
            }
          },
          x: {
            grid: { display: false },
            ticks: {
              font: { family: 'Plus Jakarta Sans', size: 11 },
              color: '#7d6b5f'
            }
          }
        }
      }
    });
  })();
</script>

<?php $this->load->view('layout/footer'); ?>