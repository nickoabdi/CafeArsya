<?php $this->load->view('layout/header', ['judul' => 'Kelola Users', 'aktif' => 'akun']); ?>

<div class="judul-baris">
  <div>
    <h1 class="jdl">Manajemen Pengguna Sistem</h1>
    <p class="sub">Kelola akun staf kasir, dapur, dan administrator untuk hak akses operasional.</p>
  </div>
</div>

<!-- FLASH MESSAGES -->
<?php if ($this->session->flashdata('sukses')): ?>
  <div class="flash f-ok">
    <i class="fa-solid fa-circle-check"></i>
    <span><?= $this->session->flashdata('sukses'); ?></span>
  </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
  <div class="flash f-err">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <span><?= $this->session->flashdata('error'); ?></span>
  </div>
<?php endif; ?>

<!-- FORM TAMBAH USER BARU -->
<div class="kartu" style="margin-bottom: 24px;">
  <div class="jd">
    <i class="fa-solid fa-user-plus" style="color: var(--gold-500);"></i>
    <span>Tambah Pengguna Baru</span>
  </div>
  <?= form_open('akun/simpan'); ?>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 140px; gap: 12px; align-items: end;">
      <div>
        <label style="margin-top:0;">Nama Lengkap <span style="color:var(--danger)">*</span></label>
        <input type="text" name="nama" placeholder="Contoh: Rian Anggara" required>
      </div>

      <div>
        <label style="margin-top:0;">Username <span style="color:var(--danger)">*</span></label>
        <input type="text" name="username" placeholder="Contoh: rian_kasir" required>
      </div>

      <div>
        <label style="margin-top:0;">Password Awal <span style="color:var(--danger)">*</span></label>
        <input type="password" name="password" placeholder="Min. 6 karakter" minlength="6" required>
      </div>

      <div>
        <label style="margin-top:0;">Hak Akses / Role <span style="color:var(--danger)">*</span></label>
        <select name="role" required>
          <option value="kasir">Kasir</option>
          <option value="dapur">Dapur</option>
          <option value="admin">Administrator</option>
        </select>
      </div>

      <div>
        <button class="btn btn-primary blok" type="submit" style="padding: 11px;">
          <i class="fa-solid fa-plus"></i> Tambah
        </button>
      </div>
    </div>
  <?= form_close(); ?>
</div>

<!-- TABEL DATA USER -->
<div class="kartu">
  <div class="jd">
    <i class="fa-solid fa-users" style="color: var(--coffee-500);"></i>
    <span>Daftar Staf &amp; Pengguna Aktif</span>
  </div>
  
  <div class="tabel-wrapper">
    <table style="min-width: 860px;">
      <thead>
        <tr>
          <th style="width: 40px;" class="c">No</th>
          <th>Nama Staf</th>
          <th>Username</th>
          <th>Hak Akses</th>
          <th class="c">Status</th>
          <th>Pengaturan Akun</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $n => $u): ?>
        <tr>
          <td class="c" style="color:var(--text-light); font-weight:700;"><?= $n + 1; ?></td>
          
          <!-- UBAH NAMA -->
          <td>
            <?= form_open('akun/nama', ['style' => 'display:flex; align-items:center; gap:6px;']); ?>
              <input type="hidden" name="id" value="<?= $u->id; ?>">
              <input type="text" name="nama" value="<?= htmlspecialchars($u->nama); ?>" style="width: 150px; padding: 6px 10px; font-size: 13px;" required>
              <button class="btn btn-secondary btn-sm" type="submit" title="Simpan perubahan nama">
                <i class="fa-solid fa-check"></i>
              </button>
            <?= form_close(); ?>
          </td>

          <td>
            <b style="color:var(--text-title);"><?= htmlspecialchars($u->username); ?></b>
          </td>
          
          <td>
            <span class="badge-role">
              <i class="fa-solid fa-user-tag" style="margin-right:4px;"></i>
              <?= ucfirst($u->role); ?>
            </span>
          </td>
          
          <td class="c">
            <span class="badge <?= $u->aktif ? 'b-aktif' : 'b-nonaktif'; ?>">
              <?= $u->aktif ? 'Aktif' : 'Nonaktif'; ?>
            </span>
          </td>
          
          <td>
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
              <!-- GANTI PASSWORD -->
              <?= form_open('akun/password', ['style' => 'display:inline-flex; align-items:center; gap:6px;']); ?>
                <input type="hidden" name="id" value="<?= $u->id; ?>">
                <input type="password" name="password" placeholder="Pass baru" minlength="6" style="width: 110px; padding: 6px 10px; font-size: 12px;" required>
                <button class="btn btn-secondary btn-sm" type="submit" title="Reset password pengguna">
                  <i class="fa-solid fa-key"></i> Reset
                </button>
              <?= form_close(); ?>

              <!-- NONAKTIFKAN & HAPUS (KECUALI AKUN SENDIRI) -->
              <?php if ($u->id != $this->session->userdata('id')): ?>
                <?= form_open('akun/status', ['style' => 'display:inline;']); ?>
                  <input type="hidden" name="id" value="<?= $u->id; ?>">
                  <button class="btn btn-sm <?= $u->aktif ? 'btn-danger' : 'btn-secondary'; ?>" type="submit" title="<?= $u->aktif ? 'Nonaktifkan akun' : 'Aktifkan kembali akun'; ?>">
                    <?= $u->aktif ? '<i class="fa-solid fa-ban"></i> Nonaktifkan' : '<i class="fa-solid fa-check"></i> Aktifkan'; ?>
                  </button>
                <?= form_close(); ?>

                <?= form_open('akun/hapus', ['style' => 'display:inline;', 'onsubmit' => "return confirm('Hapus akun ini secara permanen?')"]); ?>
                  <input type="hidden" name="id" value="<?= $u->id; ?>">
                  <button class="btn btn-danger btn-sm" type="submit" title="Hapus permanen">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                <?= form_close(); ?>
              <?php else: ?>
                <span style="font-size: 11.5px; font-weight:700; color: var(--gold-500); background: var(--gold-50); border: 1px solid var(--gold-100); padding: 4px 8px; border-radius: var(--radius-xs);">
                  <i class="fa-solid fa-shield-halved"></i> Akun Anda
                </span>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('layout/footer'); ?>