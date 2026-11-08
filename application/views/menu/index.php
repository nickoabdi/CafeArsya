<?php $this->load->view('layout/header', ['judul' => 'Daftar Menu', 'aktif' => 'menu']); ?>

<div class="judul-baris">
  <div>
    <h1 class="jdl">Manajemen Menu Resto</h1>
    <p class="sub">Kelola katalog makanan, minuman, ketersediaan stok, dan variasi harga.</p>
  </div>
  <a class="btn btn-primary" href="<?= site_url('menu/tambah'); ?>">
    <i class="fa-solid fa-plus"></i>
    <span>Tambah Menu Baru</span>
  </a>
</div>

<?php if ($this->session->flashdata('sukses')): ?>
  <div class="flash f-ok">
    <i class="fa-solid fa-circle-check"></i>
    <span><?= $this->session->flashdata('sukses'); ?></span>
  </div>
<?php endif; ?>

<div class="filter-wrapper" style="margin-bottom: 22px;">
  <div class="search-box">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" id="cari" placeholder="Cari nama menu atau kategori...">
  </div>
  <div class="filter-chips">
    <button class="chip on" type="button" data-jenis="">Semua Menu</button>
    <button class="chip" type="button" data-jenis="makanan">Makanan</button>
    <button class="chip" type="button" data-jenis="minuman">Minuman</button>
  </div>
</div>

<div class="gmenu" id="isi">
  <?php foreach ($menu as $m): ?>
  <div class="mcard" data-nama="<?= strtolower(htmlspecialchars($m->nama)); ?>" data-jenis="<?= strtolower($m->jenis); ?>">
    
    <!-- Status Badge -->
    <span class="badge <?= $m->aktif ? 'b-aktif' : 'b-habis'; ?>" style="position:absolute; top:12px; right:12px; z-index:2; box-shadow:0 2px 6px rgba(0,0,0,0.15);">
      <?= $m->aktif ? 'Tersedia' : 'Habis'; ?>
    </span>

    <!-- Image Thumbnail -->
    <?php if ($m->foto): ?>
      <img src="<?= base_url('uploads/menu/' . $m->foto); ?>" alt="<?= htmlspecialchars($m->nama); ?>" style="height:150px; width:100%; object-fit:cover;" onerror="this.onerror=null; this.parentElement.querySelector('.bd').insertAdjacentHTML('beforebegin', '<div class=\'nofoto\' style=\'height:150px;\'><span>No Image</span></div>'); this.remove();">
    <?php else: ?>
      <div class="nofoto" style="height:150px;">
        <span>No Image</span>
      </div>
    <?php endif; ?>

    <!-- Card Body -->
    <div class="bd">
      <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--text-muted); margin-bottom:4px;">
        <?= ucfirst($m->jenis); ?> &bull; <?= htmlspecialchars($m->kategori); ?>
      </div>
      <div class="n" style="font-size:15px; margin-bottom:6px;"><?= htmlspecialchars($m->nama); ?></div>
      <div class="h" style="font-size:16px; margin-bottom:14px;">Rp <?= number_format($m->harga, 0, ',', '.'); ?></div>
      
      <div class="aksi2" style="display:flex; gap:8px; border-top:1px dashed var(--border-subtle); padding-top:12px; margin-top:auto;">
        <a class="btn btn-secondary btn-sm" href="<?= site_url('menu/edit/' . $m->id); ?>" style="flex:1;">
          <i class="fa-solid fa-pen"></i> Edit
        </a>
        <a class="btn btn-danger btn-sm" href="<?= site_url('menu/hapus/' . $m->id); ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')" style="flex:1;">
          <i class="fa-solid fa-trash"></i> Hapus
        </a>
      </div>
    </div>

  </div>
  <?php endforeach; ?>
</div>

<div id="kosong-cari" class="kosong-box" style="display:none;">
  <i class="fa-solid fa-bowl-food" style="font-size:32px; color:var(--text-light); display:block; margin-bottom:10px;"></i>
  Tidak ada menu yang sesuai dengan pencarian Anda.
</div>

<script>
  const cari = document.getElementById('cari');
  const kosongBox = document.getElementById('kosong-cari');
  let jenis = '';

  function saring() {
    const kata = cari.value.toLowerCase();
    let ditemukan = 0;

    document.querySelectorAll('#isi .mcard').forEach(c => {
      const ok = c.dataset.nama.includes(kata) && (jenis === '' || c.dataset.jenis === jenis);
      c.style.display = ok ? 'flex' : 'none';
      if (ok) ditemukan++;
    });

    if (kosongBox) {
      kosongBox.style.display = ditemukan === 0 ? 'block' : 'none';
    }
  }

  cari.addEventListener('input', saring);

  document.querySelectorAll('[data-jenis]').forEach(b => b.addEventListener('click', () => {
    document.querySelectorAll('[data-jenis]').forEach(x => x.classList.remove('on'));
    b.classList.add('on');
    jenis = b.dataset.jenis;
    saring();
  }));
</script>

<?php $this->load->view('layout/footer'); ?>