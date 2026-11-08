<?php $this->load->view('layout/header', ['judul' => 'Riwayat Transaksi', 'aktif' => 'riwayat']); ?>

<div class="judul-baris">
  <div>
    <h1 class="jdl">Riwayat &amp; Arsip Transaksi</h1>
    <p class="sub" id="info">Memuat riwayat transaksi...</p>
  </div>
</div>

<!-- 1. SUMMARY METRICS CARDS -->
<div class="stat-grid">
  <div class="stat-box">
    <div>
      <span class="lbl">Total Tampil</span>
      <b class="val" id="cnt-total">0</b>
    </div>
    <div class="ik" style="width:40px; height:40px; border-radius:var(--radius-xs); background:var(--coffee-50); color:var(--coffee-600); display:flex; align-items:center; justify-content:center; font-size:18px;">
      <i class="fa-solid fa-list-check"></i>
    </div>
  </div>

  <div class="stat-box lunas">
    <div>
      <span class="lbl">Lunas (Selesai)</span>
      <b class="val" id="cnt-lunas">0</b>
    </div>
    <div class="ik" style="width:40px; height:40px; border-radius:var(--radius-xs); background:var(--success-bg); color:var(--success); border:1px solid var(--success-border); display:flex; align-items:center; justify-content:center; font-size:18px;">
      <i class="fa-solid fa-circle-check"></i>
    </div>
  </div>

  <div class="stat-box belum">
    <div>
      <span class="lbl">Belum Bayar</span>
      <b class="val" id="cnt-belum">0</b>
    </div>
    <div class="ik" style="width:40px; height:40px; border-radius:var(--radius-xs); background:var(--warning-bg); color:var(--warning); border:1px solid var(--warning-border); display:flex; align-items:center; justify-content:center; font-size:18px;">
      <i class="fa-solid fa-clock"></i>
    </div>
  </div>

  <div class="stat-box batal">
    <div>
      <span class="lbl">Dibatalkan</span>
      <b class="val" id="cnt-batal">0</b>
    </div>
    <div class="ik" style="width:40px; height:40px; border-radius:var(--radius-xs); background:var(--danger-bg); color:var(--danger); border:1px solid var(--danger-border); display:flex; align-items:center; justify-content:center; font-size:18px;">
      <i class="fa-solid fa-ban"></i>
    </div>
  </div>
</div>

<!-- 2. DATA TABLE WITH COMPREHENSIVE FILTERS -->
<div class="kartu">
  <div class="filter-bar" style="margin-bottom: 20px;">
    <select id="status" style="width: auto; min-width: 150px;">
      <option value="">Semua Status</option>
      <option value="lunas">Lunas</option>
      <option value="belum">Belum Bayar</option>
      <option value="batal">Dibatalkan</option>
    </select>
    
    <div style="display:flex; align-items:center; gap:8px;">
      <span style="font-size:12px; font-weight:600; color:var(--text-muted);">Periode:</span>
      <input type="date" id="dari" title="Dari Tanggal" style="width: auto;">
      <span style="color:var(--text-muted);">-</span>
      <input type="date" id="sampai" title="Sampai Tanggal" style="width: auto;">
    </div>

    <div class="search-wrapper">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="q" placeholder="Cari kode transaksi, pelanggan, atau menu...">
    </div>
  </div>

  <div class="tabel-wrapper">
    <table style="min-width: 760px;">
      <thead>
        <tr>
          <th>Tanggal &amp; Waktu</th>
          <th>Pelanggan / Kode</th>
          <th>Rincian Pesanan</th>
          <th>Total Tagihan</th>
          <th>Status</th>
          <th class="c" style="width: 100px;">Struk</th>
        </tr>
      </thead>
      <tbody id="isi"></tbody>
    </table>
  </div>
</div>

<style>
  .btn-struk {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--bg-card-subtle);
    border: 1px solid var(--border-subtle);
    color: var(--coffee-700);
    padding: 6px 12px;
    border-radius: var(--radius-xs);
    font-weight: 700;
    font-size: 11.5px;
    transition: var(--transition-fast);
  }
  .btn-struk:hover {
    background: var(--coffee-700);
    color: #ffffff;
    border-color: var(--coffee-700);
  }
  .metode-tag {
    display: inline-block;
    background: var(--coffee-50);
    color: var(--coffee-800);
    border: 1px solid var(--coffee-100);
    padding: 2px 7px;
    border-radius: var(--radius-full);
    font-size: 10px;
    font-weight: 700;
    margin-top: 3px;
  }
</style>

<script>
  const URL_DATA = "<?= site_url('riwayat/data'); ?>";
  const URL_STRUK = "<?= site_url('struk/lihat/'); ?>";
  const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
  const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const LABEL = { lunas: 'Lunas', belum: 'Belum', batal: 'Batal' };
  let tunda;

  async function muat() {
    const p = new URLSearchParams({
      q: document.getElementById('q').value,
      dari: document.getElementById('dari').value,
      sampai: document.getElementById('sampai').value,
      status: document.getElementById('status').value
    });

    try {
      const data = await (await fetch(URL_DATA + '?' + p)).json();
      
      document.getElementById('info').innerHTML = `Menampilkan <b>${data.length} transaksi</b>${data.length >= 200 ? ' (dibatasi 200 data terbaru)' : ''}`;
      
      document.getElementById('cnt-total').textContent = data.length;
      document.getElementById('cnt-lunas').textContent = data.filter(r => r.status_bayar === 'lunas').length;
      document.getElementById('cnt-belum').textContent = data.filter(r => r.status_bayar === 'belum').length;
      document.getElementById('cnt-batal').textContent = data.filter(r => r.status_bayar === 'batal').length;

      document.getElementById('isi').innerHTML = data.length ? data.map(r => `
        <tr>
          <td>
            <b style="color:var(--text-title);">${esc(r.dibuat.substr(8, 2) + '/' + r.dibuat.substr(5, 2) + '/' + r.dibuat.substr(0, 4))}</b>
            <small style="color:var(--text-muted);"><i class="fa-regular fa-clock" style="margin-right:3px;"></i>${esc(r.dibuat.substr(11, 5))} WIB</small>
          </td>
          <td>
            <b style="color:var(--text-title);">${esc(r.nama_pelanggan || 'Pelanggan')}</b>
            <small style="color:var(--coffee-600); font-weight:600;">${r.meja ? 'Meja ' + esc(r.meja) + ' &middot; ' : ''}#${esc(r.kode)}</small>
          </td>
          <td>
            <span style="color: var(--text-main); font-weight: 500; font-size:12.5px;">${esc(r.isi || '-')}</span>
          </td>
          <td>
            <b style="color: var(--coffee-700); font-size:13.5px;">${rp(r.total)}</b>
            ${r.metode ? `<br><span class="metode-tag"><i class="fa-solid fa-credit-card" style="margin-right:3px;"></i>${esc(String(r.metode).toUpperCase())}</span>` : ''}
          </td>
          <td>
            <span class="badge b-${esc(r.status_bayar)}">${LABEL[r.status_bayar] || esc(r.status_bayar)}</span>
          </td>
          <td class="c">
            ${r.status_bayar === 'lunas' ? `<a href="${URL_STRUK}${r.id}" target="_blank" class="btn-struk"><i class="fa-solid fa-print"></i> Struk</a>` : '<span style="color:var(--text-light); font-size:12px;">-</span>'}
          </td>
        </tr>`).join('') : '<tr><td colspan="6" class="td-kosong"><i class="fa-solid fa-folder-open" style="font-size:28px; display:block; margin-bottom:8px; opacity:0.3;"></i>Tidak ada data pesanan yang sesuai filter</td></tr>';
    } catch (e) {
      document.getElementById('info').textContent = 'Gagal memuat riwayat pesanan';
    }
  }

  ['dari', 'sampai', 'status'].forEach(id => document.getElementById(id).addEventListener('change', muat));
  document.getElementById('q').addEventListener('input', () => { 
    clearTimeout(tunda); 
    tunda = setTimeout(muat, 300); 
  });

  muat();
</script>

<?php $this->load->view('layout/footer'); ?>