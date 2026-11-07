<?php $this->load->view('layout/header', ['judul' => 'Bayar Pesanan', 'aktif' => 'bayar']); ?>

<div class="judul-baris">
  <div>
    <h1 class="jdl">Kasir / Bayar Pesanan</h1>
    <p class="sub" id="info">Memuat tagihan aktif...</p>
  </div>
</div>

<div class="gbayar" id="grid"></div>

<!-- MODAL PEMBAYARAN -->
<div class="modal" id="modal">
  <div class="dlg">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
      <h3 style="font-size:18px; font-weight:800; color:var(--text-title); margin:0;" id="m-nama">Pelanggan</h3>
      <button type="button" onclick="tutup()" style="background:none; border:none; font-size:18px; color:var(--text-muted); cursor:pointer;">&times;</button>
    </div>

    <!-- Items Preview List -->
    <div class="items-list" id="m-detail" style="max-height: 140px; overflow-y: auto; background: var(--bg-card-subtle); padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle); margin-bottom: 14px;"></div>
    
    <!-- Total Tagihan -->
    <div style="display:flex; justify-content:space-between; align-items:center; padding: 12px 16px; background: var(--coffee-50); border: 1px solid var(--coffee-100); border-radius: var(--radius-sm); margin-bottom: 16px;">
      <span style="font-size:12px; font-weight:700; text-transform:uppercase; color:var(--text-muted);">Total Tagihan</span>
      <b id="m-total" style="font-size: 20px; font-weight:800; color: var(--coffee-700);">Rp 0</b>
    </div>

    <!-- Pilihan Metode Pembayaran -->
    <label style="margin-top:0;">Pilih Metode Pembayaran</label>
    <div class="filter-metode" style="display:flex; gap:8px; margin-bottom:14px;">
      <button class="chip-metode on" type="button" data-metode="tunai">
        <i class="fa-solid fa-money-bill-wave"></i> Tunai
      </button>
      <button class="chip-metode" type="button" data-metode="qris">
        <i class="fa-solid fa-qrcode"></i> QRIS
      </button>
      <button class="chip-metode" type="button" data-metode="transfer">
        <i class="fa-solid fa-building-columns"></i> Transfer
      </button>
    </div>

    <!-- Input Uang Tunai & Quick Chips -->
    <div id="blok-tunai">
      <label>Nominal Diterima (Rp)</label>
      <input type="number" class="input-uang" id="uang" min="0" placeholder="Ketik nominal atau klik tombol cepat..." style="font-size:16px; font-weight:700; margin-bottom:8px;">
      
      <!-- Tombol Cepat Uang -->
      <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:14px;">
        <button type="button" class="btn-uang-cepat" onclick="pasUang()">Uang Pas</button>
        <button type="button" class="btn-uang-cepat" onclick="tambahUang(10000)">+10.000</button>
        <button type="button" class="btn-uang-cepat" onclick="tambahUang(20000)">+20.000</button>
        <button type="button" class="btn-uang-cepat" onclick="tambahUang(50000)">+50.000</button>
        <button type="button" class="btn-uang-cepat" onclick="tambahUang(100000)">+100.000</button>
      </div>
    </div>

    <!-- Blok QRIS -->
    <div id="blok-qris" style="display:none; text-align:center; padding: 12px 0 16px;">
      <img id="qr" src="<?= base_url('assets/img/qris.png'); ?>" alt="QRIS Cafe Arsa" style="max-width:180px; width:100%; border-radius: 10px; margin: 0 auto 8px; border: 1px solid var(--border-subtle);"
           onerror="this.style.display='none';document.getElementById('qrpesan').style.display='block'">
      <div id="qrpesan" class="kosong-box" style="display:none; padding: 16px; font-size: 12px; background:var(--bg-card-subtle); border-radius:var(--radius-sm);">
        Gambar QRIS belum tersedia di <b>assets/img/qris.png</b>
      </div>
      <small style="color: var(--text-muted); display: block;">Scan QRIS untuk menyelesaikan pembayaran otomatis</small>
    </div>

    <!-- Ringkasan Kembalian -->
    <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 12px 16px; margin-bottom: 18px;">
      <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13px;">
        <span style="color:var(--text-muted);">Uang Diterima:</span>
        <b id="r-bayar" style="color:var(--text-title);">Rp 0</b>
      </div>
      <div style="display:flex; justify-content:space-between; font-size:14px;">
        <span style="font-weight:600; color:var(--text-title);">Kembalian:</span>
        <b id="r-kembali" style="color: var(--success); font-weight:800; font-size:16px;">Rp 0</b>
      </div>
    </div>

    <div style="display:flex; gap:10px;">
      <button class="btn btn-secondary" id="tutup" type="button" style="flex:1;">Batal</button>
      <button class="btn btn-success" id="ok" type="button" disabled style="flex:1.5;">
        <i class="fa-solid fa-check-circle"></i> Selesai Bayar
      </button>
    </div>
  </div>
</div>

<style>
  .gbayar {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 18px;
  }
  .kartu-bayar {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-md);
    padding: 18px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-normal);
  }
  .kartu-bayar:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
    border-color: var(--coffee-300);
  }
  .kartu-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
  }
  .customer-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-title);
  }
  .order-id {
    font-size: 11.5px;
    color: var(--text-muted);
    margin-top: 2px;
  }
  .status-dapur {
    font-size: 11.5px;
    font-weight: 600;
    color: var(--coffee-700);
    background: var(--coffee-50);
    border: 1px solid var(--coffee-100);
    padding: 6px 10px;
    border-radius: var(--radius-xs);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .items-list-card {
    border-top: 1px dashed var(--border-subtle);
    padding-top: 10px;
    margin-bottom: 12px;
  }
  .item-row {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    margin-bottom: 5px;
    color: var(--text-main);
  }
  .total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid var(--border-subtle);
    padding-top: 10px;
    margin-bottom: 14px;
  }
  .total-row span {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
  }
  .total-row b {
    font-size: 18px;
    font-weight: 800;
    color: var(--coffee-700);
  }
  .btn-bayar-action {
    width: 100%;
    background: var(--success);
    color: #ffffff;
    border: none;
    padding: 12px;
    border-radius: var(--radius-sm);
    font-weight: 700;
    font-size: 13.5px;
    cursor: pointer;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 2px 6px rgba(21, 128, 61, 0.2);
    transition: var(--transition-fast);
  }
  .btn-bayar-action:hover {
    background: #166534;
    transform: translateY(-1px);
  }
  .aksi-sekunder {
    display: flex;
    gap: 8px;
  }
  .btn-sub {
    flex: 1;
    text-align: center;
    padding: 8px 0;
    border-radius: var(--radius-xs);
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid var(--border-subtle);
    cursor: pointer;
    transition: var(--transition-fast);
  }
  .btn-ubah { background: var(--bg-card-subtle); color: var(--text-title); }
  .btn-ubah:hover { background: var(--border-subtle); }
  .btn-batal { background: var(--danger-bg); color: var(--danger); border-color: var(--danger-border); }
  .btn-batal:hover { background: var(--danger); color: #fff; }

  .chip-metode {
    flex: 1;
    background: var(--bg-card-subtle);
    border: 1px solid var(--border-subtle);
    padding: 10px 6px;
    border-radius: var(--radius-sm);
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    color: var(--text-muted);
    transition: var(--transition-fast);
  }
  .chip-metode.on {
    background: var(--coffee-700);
    color: #fff;
    border-color: var(--coffee-700);
    box-shadow: 0 2px 6px rgba(66, 39, 21, 0.2);
  }
  .btn-uang-cepat {
    padding: 5px 10px;
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-xs);
    background: var(--bg-card-subtle);
    font-size: 11.5px;
    font-weight: 600;
    color: var(--coffee-700);
    cursor: pointer;
    transition: var(--transition-fast);
  }
  .btn-uang-cepat:hover {
    background: var(--coffee-100);
    border-color: var(--coffee-200);
  }
</style>

<script>
  const URL_DAFTAR = "<?= site_url('bayar/daftar'); ?>";
  const URL_PROSES = "<?= site_url('bayar/proses'); ?>";
  const URL_BATAL = "<?= site_url('bayar/batal'); ?>";
  const URL_KASIR = "<?= site_url('kasir'); ?>";
  const URL_STRUK = "<?= site_url('struk/lihat/'); ?>";
  const STATUS = { baru: 'Menunggu dapur', diproses: 'Sedang dimasak', selesai: 'Siap diantar' };
  let data = [], terpilih = null, metode = 'tunai';

  const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
  const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const el = id => document.getElementById(id);

  function render() {
    const jumlah = data.reduce((t, p) => t + Number(p.total), 0);
    el('info').innerHTML = `<span style="font-weight:600; color:var(--text-title);">${data.length} pelanggan</span> belum bayar (Total: <b>${rp(jumlah)}</b>) &middot; <small style="color:var(--text-light)">Update otomatis tiap 5 dtk</small>`;
    
    el('grid').innerHTML = data.length ? data.map(p => `
      <div class="kartu-bayar">
        <div>
          <div class="kartu-head">
            <div>
              <div class="customer-name">${esc(p.nama_pelanggan || 'Pelanggan')}</div>
              <div class="order-id">${p.meja ? 'Meja ' + esc(p.meja) + ' &middot; ' : ''}#${esc(p.kode)} &middot; ${esc(p.dibuat.substr(11, 5))} WIB</div>
            </div>
            <span class="badge b-belum">Belum Bayar</span>
          </div>

          <div class="status-dapur">
            <i class="fa-solid fa-fire-burner"></i>
            <span>Dapur: <b>${STATUS[p.status] || p.status}</b></span>
          </div>

          <div class="items-list-card">
            ${p.item.map(i => `
              <div class="item-row">
                <span>${esc(i.nama)}</span>
                <b style="color:var(--text-muted);">${i.qty}x</b>
              </div>
            `).join('')}
          </div>
        </div>

        <div>
          <div class="total-row">
            <span>Total Tagihan</span>
            <b>${rp(p.total)}</b>
          </div>
          
          <button type="button" class="btn-bayar-action bayar" data-id="${p.id}">
            <i class="fa-solid fa-credit-card"></i> Proses Pembayaran
          </button>
          
          <div class="aksi-sekunder">
            <a class="btn-sub btn-ubah" href="${URL_KASIR}?ubah=${p.id}">
              <i class="fa-solid fa-pen-to-square"></i> Ubah
            </a>
            <button type="button" class="btn-sub btn-batal batal" data-id="${p.id}">
              <i class="fa-solid fa-xmark"></i> Batalkan
            </button>
          </div>
        </div>
      </div>`).join('') : '<div class="kosong-box" style="grid-column:1/-1"><i class="fa-solid fa-circle-check" style="font-size:32px; color:var(--success); display:block; margin-bottom:10px;"></i>Semua pesanan sudah lunas dibayar!</div>';
  }

  async function muat() {
    try {
      data = await (await fetch(URL_DAFTAR)).json();
      render();
    } catch (e) {
      el('info').textContent = 'Koneksi terputus, mencoba lagi...';
    }
  }

  function hitungModal() {
    if (!terpilih) return;
    const t = Number(terpilih.total);
    const uang = parseInt(el('uang').value) || 0;
    const tunai = metode === 'tunai';
    
    el('blok-tunai').style.display = tunai ? '' : 'none';
    el('blok-qris').style.display = metode === 'qris' ? '' : 'none';
    
    const bayar = tunai ? uang : t;
    el('r-bayar').textContent = rp(bayar);
    el('r-kembali').textContent = rp(Math.max(bayar - t, 0));
    el('ok').disabled = tunai ? uang < t : false;
  }

  function pasUang() {
    if (!terpilih) return;
    el('uang').value = terpilih.total;
    hitungModal();
  }

  function tambahUang(n) {
    if (!terpilih) return;
    const cur = parseInt(el('uang').value) || 0;
    el('uang').value = cur + n;
    hitungModal();
  }

  function tutup() {
    el('modal').classList.remove('buka');
    terpilih = null;
  }

  el('grid').addEventListener('click', async e => {
    const b = e.target.closest('button.bayar');
    if (b) {
      terpilih = data.find(p => p.id == b.dataset.id);
      if (!terpilih) return;
      el('m-nama').textContent = (terpilih.nama_pelanggan || 'Pelanggan') + (terpilih.meja ? ' (Meja ' + terpilih.meja + ')' : '');
      el('m-detail').innerHTML = terpilih.item.map(i => `
        <div class="item-row">
          <span>${esc(i.nama)}</span>
          <b>${i.qty}x</b>
        </div>
      `).join('');
      el('m-total').textContent = rp(terpilih.total);
      el('uang').value = '';
      metode = 'tunai';
      document.querySelectorAll('[data-metode]').forEach(x => x.classList.toggle('on', x.dataset.metode === 'tunai'));
      el('modal').classList.add('buka');
      hitungModal();
      return;
    }

    const c = e.target.closest('button.batal');
    if (!c) return;
    if (!confirm('Batalkan pesanan ini? Pesanan akan dihapus dari antrean.')) return;
    const f = new FormData();
    f.append('id', c.dataset.id);
    try {
      const r = await (await fetch(URL_BATAL, { method: 'POST', body: f })).json();
      if (!r.ok) alert(r.pesan);
    } catch (err) {
      alert('Gagal terhubung ke server');
    }
    muat();
  });

  document.querySelectorAll('[data-metode]').forEach(b => b.addEventListener('click', () => {
    document.querySelectorAll('[data-metode]').forEach(x => x.classList.remove('on'));
    b.classList.add('on');
    metode = b.dataset.metode;
    hitungModal();
  }));

  el('uang').addEventListener('input', hitungModal);
  el('tutup').addEventListener('click', tutup);

  el('ok').addEventListener('click', async () => {
    if (!terpilih) return;
    el('ok').disabled = true;
    const f = new FormData();
    f.append('id', terpilih.id);
    f.append('metode', metode);
    f.append('uang', el('uang').value || 0);
    try {
      const r = await (await fetch(URL_PROSES, { method: 'POST', body: f })).json();
      if (r.ok) {
        const idBayar = terpilih.id;
        const info = 'Pembayaran berhasil disimpan.' + (metode === 'tunai' ? '\nKembalian: ' + rp(r.kembali) : '');
        tutup();
        muat();
        if (confirm(info + '\n\nCetak struk pembayaran sekarang?')) {
          window.open(URL_STRUK + idBayar + '?cetak=1', '_blank');
        }
      } else {
        alert(r.pesan);
        hitungModal();
      }
    } catch (err) {
      alert('Gagal terhubung ke server');
      hitungModal();
    }
  });

  muat();
  setInterval(muat, 5000);
</script>

<?php $this->load->view('layout/footer'); ?>