<?php $edit = isset($m) && $m; ?>
<?php $this->load->view('layout/header', ['judul' => $edit ? 'Edit Menu' : 'Tambah Menu', 'aktif' => 'menu']); ?>

<div class="judul-baris">
  <div>
    <h1 class="jdl"><?= $edit ? 'Edit Menu' : 'Tambah Menu Baru'; ?></h1>
    <p class="sub">Lengkapi informasi data menu di bawah ini. Foto menu bersifat opsional.</p>
  </div>
  <a class="btn btn-secondary" href="<?= site_url('menu'); ?>">
    <i class="fa-solid fa-arrow-left"></i> Kembali ke Menu
  </a>
</div>

<div class="kartu" style="max-width: 580px; margin: 0 auto 30px;">
  <?php if ($this->session->flashdata('error')): ?>
    <div class="flash f-err">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <span><?= $this->session->flashdata('error'); ?></span>
    </div>
  <?php endif; ?>

  <?= form_open_multipart('menu/simpan'); ?>
    <input type="hidden" name="id" value="<?= $edit ? $m->id : ''; ?>">

    <div style="margin-bottom: 16px;">
      <label style="margin-top:0;">Nama Menu <span style="color:var(--danger)">*</span></label>
      <input type="text" name="nama" value="<?= $edit ? htmlspecialchars($m->nama) : ''; ?>" placeholder="Contoh: Kopi Susu Gula Aren" required autofocus>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
      <div>
        <label style="margin-top:0;">Jenis Menu <span style="color:var(--danger)">*</span></label>
        <select name="jenis" required>
          <option value="makanan" <?= ($edit && $m->jenis == 'makanan') ? 'selected' : ''; ?>>Makanan</option>
          <option value="minuman" <?= ($edit && $m->jenis == 'minuman') ? 'selected' : ''; ?>>Minuman</option>
        </select>
      </div>

      <div>
        <label style="margin-top:0;">Kategori <span style="color:var(--danger)">*</span></label>
        <input type="text" name="kategori" list="daftar-kategori" value="<?= $edit ? htmlspecialchars($m->kategori) : ''; ?>" placeholder="Pilih / ketik baru" required>
        <datalist id="daftar-kategori">
          <?php foreach ($kategori as $k): ?>
            <option value="<?= htmlspecialchars($k->kategori); ?>">
          <?php endforeach; ?>
        </datalist>
      </div>
    </div>

    <div style="margin-bottom: 16px;">
      <label style="margin-top:0;">Harga Jual (Rp) <span style="color:var(--danger)">*</span></label>
      <input type="number" name="harga" min="0" step="500" value="<?= $edit ? $m->harga : ''; ?>" placeholder="Contoh: 18000" required>
    </div>

    <div style="margin-bottom: 18px; background: var(--bg-card-subtle); padding: 16px; border-radius: var(--radius-sm); border: 1px dashed var(--border-subtle);">
      <label style="margin-top:0;">Foto Menu (Format JPG/PNG, Maks. 2MB)</label>
      <?php if ($edit && $m->foto): ?>
        <div style="margin-bottom: 12px; display:flex; align-items:center; gap:14px;">
          <img src="<?= base_url('uploads/menu/' . $m->foto); ?>" alt="" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-subtle);">
          <label class="cek" style="margin:0;">
            <input type="checkbox" name="hapus_foto" value="1">
            <span style="font-size:12.5px; color:var(--danger);">Hapus foto saat ini</span>
          </label>
        </div>
      <?php endif; ?>
      <input type="file" name="foto" accept="image/*" style="background:#fff; border:1px solid var(--border-subtle); padding:8px 12px; border-radius:var(--radius-xs); width:100%;">
    </div>

    <div style="margin-bottom: 24px;">
      <label class="cek" style="margin:0; font-weight:600;">
        <input type="checkbox" name="aktif" value="1" <?= (!$edit || $m->aktif) ? 'checked' : ''; ?>>
        <span>Status Tersedia (Menu aktif &amp; dapat dipesan pelanggan)</span>
      </label>
      <small style="color:var(--text-muted); display:block; margin-left: 24px;">Hilangkan centang jika stok menu sedang habis.</small>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:12px; border-top:1px solid var(--border-subtle); padding-top:18px;">
      <a class="btn btn-secondary" href="<?= site_url('menu'); ?>">Batal</a>
      <button class="btn btn-primary" type="submit">
        <i class="fa-solid fa-floppy-disk"></i>
        <span>Simpan Data Menu</span>
      </button>
    </div>
  <?= form_close(); ?>
</div>

<?php $this->load->view('layout/footer'); ?>