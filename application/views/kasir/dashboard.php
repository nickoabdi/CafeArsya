<?php $this->load->view('layout/header', ['judul' => 'Buat Pesanan', 'aktif' => 'kasir']); ?>

<div class="judul-baris">
  <div>
    <h1 class="jdl">Kasir / Buat Pesanan</h1>
    <p class="sub">Pilih menu dari katalog, atur jumlah, lalu kirim pesanan langsung ke dapur.</p>
  </div>
</div>

<div class="dua">
  <!-- KOLOM KIRI: KATALOG MENU -->
  <div class="kartu">
    <div class="filter-wrapper" style="margin-bottom: 16px;">
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="cari" placeholder="Cari menu makanan atau minuman...">
      </div>
      <div class="filter-chips">
        <button class="chip on" type="button" data-jenis="">Semua</button>
        <button class="chip" type="button" data-jenis="makanan">Makanan</button>
        <button class="chip" type="button" data-jenis="minuman">Minuman</button>
      </div>
    </div>

    <!-- Grid Menu Items -->
    <div class="gmenu pilih" id="grid" style="max-height: 620px; overflow-y: auto; padding-right: 4px;"></div>
  </div>

  <!-- KOLOM KANAN: DETAIL / KERANJANG PESANAN -->
  <div class="kartu" style="position: sticky; top: 20px;">
    <div class="jd" id="jdlDetail" style="margin-bottom: 14px;">
      <i class="fa-solid fa-receipt" style="color: var(--gold-500);"></i>
      <span>Detail Pesanan</span>
    </div>
    
    <div class="dua-in">
      <div>
        <label style="margin-top:0;">Nama Pelanggan <span style="color:var(--danger)">*</span></label>
        <input type="text" id="nama" placeholder="Contoh: Budi" maxlength="100" required>
      </div>
      <div>
        <label style="margin-top:0;">No. Meja (opsional)</label>
        <input type="text" id="meja" placeholder="Contoh: 05" maxlength="20">
      </div>
    </div>

    <div class="tabel-wrapper" style="margin-bottom: 16px; max-height: 320px; overflow-y: auto;">
      <table>
        <thead>
          <tr>
            <th>Menu</th>
            <th class="c" style="width: 90px;">Qty</th>
            <th class="r">Subtotal</th>
          </tr>
        </thead>
        <tbody id="keranjang"></tbody>
      </table>
    </div>

    <div style="background: var(--bg-card-subtle); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 14px 16px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
      <div>
        <small style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); display: block;">Total Tagihan</small>
        <span style="font-size: 12px; color: var(--text-light);">Belum termasuk diskon</span>
      </div>
      <b id="total" style="font-size: 20px; font-weight: 800; color: var(--coffee-700);">Rp 0</b>
    </div>

    <button class="btn btn-success blok" id="kirim" disabled style="padding: 14px; font-size: 14px;">
      <i class="fa-solid fa-paper-plane"></i>
      <span>Kirim ke Dapur</span>
    </button>
  </div>
</div>

<style>
  /* Search input padding & icon alignment */
  .search-box {
    position: relative;
    flex: 1;
  }
  .search-box i {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-light);
    font-size: 15px;
    pointer-events: none;
    z-index: 3;
    line-height: 1;
  }
  .search-box input {
    width: 100% !important;
    padding: 11px 18px 11px 48px !important;
    border-radius: var(--radius-full) !important;
    box-sizing: border-box !important;
  }

  /* Menu Without Image Styling */
  .mcard .nofoto {
    width: 100% !important;
    height: 125px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: #f7f3ed !important;
    border-bottom: 1px solid var(--border-subtle) !important;
    user-select: none !important;
  }
  .mcard .nofoto span {
    display: inline-block !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.8px !important;
    text-transform: uppercase !important;
    color: #8c7667 !important;
    background: rgba(255, 255, 255, 0.9) !important;
    padding: 6px 14px !important;
    border-radius: var(--radius-full) !important;
    border: 1px solid rgba(140, 118, 103, 0.25) !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    text-align: center !important;
  }

  /* Stepper Qty Buttons in Cart Table */
  .qty-stepper {
    display: inline-flex;
    align-items: center;
    background: var(--bg-card-subtle);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-xs);
    overflow: hidden;
  }
  .qty-stepper button {
    width: 26px;
    height: 26px;
    border: none;
    background: transparent;
    color: var(--text-title);
    font-weight: 700;
    cursor: pointer;
    transition: background var(--transition-fast);
  }
  .qty-stepper button:hover {
    background: var(--coffee-100);
    color: var(--coffee-800);
  }
  .qty-stepper span {
    display: inline-block;
    width: 26px;
    text-align: center;
    font-weight: 700;
    font-size: 12px;
  }
  .mcard .badge-kat {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-muted);
    background: var(--bg-card-subtle);
    padding: 2px 7px;
    border-radius: var(--radius-full);
    display: inline-block;
    margin-bottom: 6px;
    width: fit-content;
  }
</style>

<script>
  const MENU = <?= json_encode($menu); ?>;
  const URL_SIMPAN = "<?= site_url('kasir/simpan'); ?>";
  const BASE = "<?= base_url(); ?>";
  const EDIT = <?= json_encode($edit); ?>;
  const URL_BAYAR = "<?= site_url('bayar'); ?>";
  const cart = {};
  let jenis = '';

  const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
  const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const total = () => Object.values(cart).reduce((t, i) => t + i.harga * i.qty, 0);

  function tampilMenu() {
    const kata = document.getElementById('cari').value.toLowerCase();
    const hasil = MENU.filter(m => (!jenis || m.jenis === jenis) && m.nama.toLowerCase().includes(kata));
    document.getElementById('grid').innerHTML = hasil.length ? hasil.map(m =>
      `<div class="mcard" data-id="${m.id}" title="Klik untuk menambahkan ke pesanan">
        ${m.foto ? `<img src="${BASE}uploads/menu/${encodeURIComponent(m.foto)}" alt="${esc(m.nama)}" onerror="this.onerror=null;this.parentElement.querySelector('.bd').insertAdjacentHTML('beforebegin','<div class=\\'nofoto\\'><span>No Image</span></div>');this.remove();">` : '<div class="nofoto"><span>No Image</span></div>'}
        <div class="bd">
          <span class="badge-kat">${esc(m.kategori)}</span>
          <div class="n">${esc(m.nama)}</div>
          <div class="h">${rp(m.harga)}</div>
        </div>
      </div>`).join('') : '<div class="kosong" style="grid-column:1/-1"><i class="fa-solid fa-magnifying-glass" style="font-size:24px; display:block; margin-bottom:8px; opacity:0.4;"></i>Menu tidak ditemukan</div>';
  }

  function tampilKeranjang() {
    const ids = Object.keys(cart);
    document.getElementById('keranjang').innerHTML = ids.length ? ids.map(id => {
      const i = cart[id];
      return `<tr>
        <td>
          <div style="font-weight:600; color:var(--text-title);">${esc(i.nama)}</div>
          <small>${rp(i.harga)}</small>
        </td>
        <td class="c">
          <div class="qty-stepper">
            <button type="button" data-aksi="kurang" data-id="${id}">-</button>
            <span>${i.qty}</span>
            <button type="button" data-aksi="tambah" data-id="${id}">+</button>
          </div>
        </td>
        <td class="r" style="font-weight:700; color:var(--text-title);">${rp(i.harga * i.qty)}</td>
      </tr>`;
    }).join('') : '<tr><td colspan="3" class="td-kosong"><i class="fa-solid fa-basket-shopping" style="font-size:24px; display:block; margin-bottom:8px; opacity:0.3;"></i>Belum ada menu yang dipilih</td></tr>';
    hitung();
  }

  function hitung() {
    const t = total();
    document.getElementById('total').textContent = rp(t);
    document.getElementById('kirim').disabled = !(t > 0 && document.getElementById('nama').value.trim() !== '');
  }

  document.getElementById('grid').addEventListener('click', e => {
    const k = e.target.closest('.mcard');
    if (!k) return;
    const m = MENU.find(x => x.id == k.dataset.id);
    if (!m) return;
    if (cart[m.id]) cart[m.id].qty++;
    else cart[m.id] = { id: m.id, nama: m.nama, harga: Number(m.harga), qty: 1 };
    tampilKeranjang();
  });

  document.getElementById('keranjang').addEventListener('click', e => {
    const b = e.target.closest('button');
    if (!b) return;
    const i = cart[b.dataset.id];
    if (!i) return;
    if (b.dataset.aksi === 'tambah') i.qty++;
    else if (--i.qty <= 0) delete cart[b.dataset.id];
    tampilKeranjang();
  });

  document.querySelectorAll('[data-jenis]').forEach(b => b.addEventListener('click', () => {
    document.querySelectorAll('[data-jenis]').forEach(x => x.classList.remove('on'));
    b.classList.add('on');
    jenis = b.dataset.jenis;
    tampilMenu();
  }));

  document.getElementById('cari').addEventListener('input', tampilMenu);
  document.getElementById('nama').addEventListener('input', hitung);

  document.getElementById('kirim').addEventListener('click', async () => {
    const btn = document.getElementById('kirim');
    btn.disabled = true;
    const f = new FormData();
    f.append('items', JSON.stringify(Object.values(cart)));
    f.append('nama', document.getElementById('nama').value);
    f.append('meja', document.getElementById('meja').value);
    if (EDIT) f.append('id', EDIT.id);
    try {
      const r = await (await fetch(URL_SIMPAN, { method: 'POST', body: f })).json();
      if (r.ok) {
        if (EDIT) { alert('Perubahan pesanan tersimpan.'); location.href = URL_BAYAR; return; }
        alert('Pesanan atas nama ' + r.nama + ' sudah dikirim ke dapur.');
        Object.keys(cart).forEach(k => delete cart[k]);
        document.getElementById('nama').value = '';
        document.getElementById('meja').value = '';
        tampilKeranjang();
      } else {
        alert(r.pesan);
        hitung();
      }
    } catch (err) {
      alert('Gagal terhubung ke server');
      hitung();
    }
  });

  if (EDIT) {
    document.getElementById('nama').value = EDIT.nama_pelanggan || '';
    document.getElementById('meja').value = EDIT.meja || '';
    const hilang = [];
    EDIT.item.forEach(d => {
      const m = MENU.find(x => x.id == d.menu_id);
      if (m) cart[m.id] = { id: m.id, nama: m.nama, harga: Number(m.harga), qty: Number(d.qty) };
      else hilang.push(d.nama);
    });
    if (hilang.length) alert('Menu ini sudah tidak tersedia dan dihapus dari pesanan: ' + hilang.join(', '));
    document.getElementById('kirim').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> <span>Simpan Perubahan</span>';
    document.getElementById('jdlDetail').innerHTML = '<i class="fa-solid fa-pen-to-square"></i> <span>Ubah Pesanan ' + esc(EDIT.kode) + '</span>';
  }

  tampilMenu();
  tampilKeranjang();
</script>

<?php $this->load->view('layout/footer'); ?>