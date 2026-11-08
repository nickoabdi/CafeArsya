<?php $this->load->view('layout/header', ['judul' => 'Laporan Penjualan', 'aktif' => 'laporan']); ?>

<div class="judul-baris">
  <div>
    <h1 class="jdl">Laporan Rekapitulasi Penjualan</h1>
    <p class="sub" id="rentang">Memuat periode...</p>
  </div>
  <a class="btn btn-unduh" id="unduh" href="#">
    <i class="fa-solid fa-file-excel"></i>
    <span>Unduh Data Excel (CSV)</span>
  </a>
</div>

<!-- FILTER BAR -->
<div class="filter-bar" style="margin-bottom: 22px;">
  <div class="filter-chips">
    <button class="chip on" type="button" data-p="harian">Harian</button>
    <button class="chip" type="button" data-p="mingguan">Mingguan</button>
    <button class="chip" type="button" data-p="bulanan">Bulanan</button>
    <button class="chip" type="button" data-p="tahunan">Tahunan</button>
  </div>
  
  <div style="display:flex; align-items:center; gap:8px;">
    <span style="font-size:12px; font-weight:600; color:var(--text-muted);">Tanggal Acuan:</span>
    <input type="date" id="tanggal" title="Pilih Tanggal Acuan" style="width: auto;">
  </div>
</div>

<!-- METRICS CARDS -->
<div class="stats-grid">
  <div class="stat-card omzet">
    <div>
      <span class="lbl">Total Pendapatan</span>
      <b class="stat-value" id="omzet" style="color:var(--success);">-</b>
    </div>
    <div class="stat-icon icon-emerald">
      <i class="fa-solid fa-money-bill-trend-up"></i>
    </div>
  </div>

  <div class="stat-card">
    <div>
      <span class="lbl">Total Transaksi</span>
      <b class="stat-value" id="jumlah">-</b>
    </div>
    <div class="stat-icon">
      <i class="fa-solid fa-receipt"></i>
    </div>
  </div>

  <div class="stat-card">
    <div>
      <span class="lbl">Rata-rata per Transaksi</span>
      <b class="stat-value" id="rata">-</b>
    </div>
    <div class="stat-icon" style="background:var(--coffee-100); color:var(--coffee-700); border-color:var(--coffee-200);">
      <i class="fa-solid fa-calculator"></i>
    </div>
  </div>
</div>

<!-- GRAFIK PENJUALAN BAR -->
<div class="kartu" style="margin-bottom: 24px;">
  <div class="jd">
    <i class="fa-solid fa-chart-column" style="color: var(--coffee-500);"></i>
    <span>Grafik Distribusi Penjualan</span>
  </div>
  <div class="chart-wrapper" id="chart" style="display:flex; align-items:flex-end; gap:12px; height:240px; padding:20px 10px 10px; border-bottom:1px solid var(--border-subtle); overflow-x:auto;"></div>
</div>

<!-- DETAIL METODE & MENU TERLARIS -->
<div class="grid2">
  <div class="kartu" style="margin-bottom:0;">
    <div class="jd">
      <i class="fa-solid fa-credit-card" style="color: var(--gold-500);"></i>
      <span>Metode Pembayaran</span>
    </div>
    <div class="tabel-wrapper" id="metode" style="border:none;"></div>
  </div>

  <div class="kartu" style="margin-bottom:0;">
    <div class="jd">
      <i class="fa-solid fa-trophy" style="color: var(--gold-500);"></i>
      <span>Menu Terlaris</span>
    </div>
    <div class="tabel-wrapper" id="terlaris" style="border:none;"></div>
  </div>
</div>

<style>
  .chart-wrapper .kol {
    flex: 1;
    min-width: 44px;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
    justify-content: flex-end;
  }
  .chart-wrapper .area {
    width: 100%;
    max-width: 36px;
    height: 100%;
    display: flex;
    align-items: flex-end;
    background: var(--bg-card-subtle);
    border-radius: var(--radius-xs) var(--radius-xs) 0 0;
    overflow: hidden;
  }
  .chart-wrapper .bar {
    width: 100%;
    background: linear-gradient(180deg, var(--gold-400), var(--coffee-600));
    border-radius: var(--radius-xs) var(--radius-xs) 0 0;
    transition: height 0.4s ease;
  }
  .chart-wrapper .kol:hover .bar {
    opacity: 0.85;
    background: var(--gold-500);
  }
  .chart-wrapper span {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-muted);
    margin-top: 8px;
    white-space: nowrap;
  }
</style>

<script>
  const URL_DATA = "<?= site_url('laporan/data'); ?>";
  const URL_UNDUH = "<?= site_url('laporan/unduh'); ?>";
  let periode = 'harian';

  const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
  const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const tgl = document.getElementById('tanggal');
  tgl.value = new Date(Date.now() - new Date().getTimezoneOffset() * 60000).toISOString().slice(0, 10);
  const fmt = (s, opsi) => new Date(s + 'T00:00:00').toLocaleDateString('id-ID', opsi);

  function teksRentang(d) {
    if (d.periode === 'harian') return fmt(d.awal, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    if (d.periode === 'mingguan') return fmt(d.awal, { day: 'numeric', month: 'long' }) + ' s/d ' + fmt(d.akhir, { day: 'numeric', month: 'long', year: 'numeric' });
    if (d.periode === 'bulanan') return fmt(d.awal, { month: 'long', year: 'numeric' });
    return 'Tahun ' + d.awal.slice(0, 4);
  }

  function render(d) {
    document.getElementById('rentang').innerHTML = `<i class="fa-regular fa-calendar" style="color:var(--gold-500); margin-right:4px;"></i>Periode: <b>${teksRentang(d)}</b>`;
    document.getElementById('omzet').textContent = rp(d.omzet);
    document.getElementById('jumlah').textContent = d.jumlah + ' Transaksi';
    document.getElementById('rata').textContent = rp(d.jumlah ? Math.round(d.omzet / d.jumlah) : 0);

    const maks = Math.max(...d.grafik.nilai, 1);
    document.getElementById('chart').innerHTML = d.grafik.label.map((l, i) =>
      `<div class="kol" title="${esc(l)}: ${rp(d.grafik.nilai[i])}">
        <div class="area"><div class="bar" style="height:${(d.grafik.nilai[i] / maks * 100).toFixed(1)}%"></div></div>
        <span>${esc(l)}</span>
      </div>`).join('');

    document.getElementById('metode').innerHTML = d.metode.length
      ? '<table><thead><tr><th>Metode</th><th class="r">Pesanan</th><th class="r">Total Omzet</th></tr></thead><tbody>' +
        d.metode.map(m => `<tr><td><span class="badge-role" style="text-transform:uppercase;">${esc(String(m.metode))}</span></td><td class="r" style="color:var(--text-muted); font-weight:600;">${m.jumlah}x</td><td class="r"><b style="color:var(--coffee-700);">${rp(m.omzet)}</b></td></tr>`).join('') + '</tbody></table>'
      : '<div class="kosong"><i class="fa-solid fa-folder-open" style="font-size:24px; display:block; margin-bottom:6px; opacity:0.3;"></i>Belum ada data transaksi</div>';

    document.getElementById('terlaris').innerHTML = d.terlaris.length
      ? '<table><thead><tr><th>Nama Menu</th><th class="r">Terjual</th><th class="r">Subtotal</th></tr></thead><tbody>' +
        d.terlaris.map(t => `<tr><td><b style="color:var(--text-title);">${esc(t.nama)}</b></td><td class="r" style="color:var(--text-muted); font-weight:600;">${t.qty} porsi</td><td class="r"><b style="color:var(--coffee-700);">${rp(t.omzet)}</b></td></tr>`).join('') + '</tbody></table>'
      : '<div class="kosong"><i class="fa-solid fa-mug-saucer" style="font-size:24px; display:block; margin-bottom:6px; opacity:0.3;"></i>Belum ada data penjualan menu</div>';
  }

  async function muat() {
    const q = '?periode=' + periode + '&tanggal=' + tgl.value;
    document.getElementById('unduh').href = URL_UNDUH + q;
    try {
      render(await (await fetch(URL_DATA + q)).json());
    } catch (e) {
      document.getElementById('rentang').textContent = 'Gagal memuat laporan';
    }
  }

  document.querySelectorAll('[data-p]').forEach(b => b.addEventListener('click', () => {
    document.querySelectorAll('[data-p]').forEach(x => x.classList.remove('on'));
    b.classList.add('on');
    periode = b.dataset.p;
    muat();
  }));

  tgl.addEventListener('change', muat);
  muat();
</script>

<?php $this->load->view('layout/footer'); ?>