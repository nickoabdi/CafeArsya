<?php $this->load->view('layout/header', ['judul' => 'Display Dapur', 'aktif' => 'dapur']); ?>

<div class="judul-baris">
  <div>
    <h1 class="jdl">Monitor Pesanan Dapur</h1>
    <p class="sub" id="info">Memuat antrean pesanan...</p>
  </div>
  <button class="chip" id="suara" type="button" style="display:inline-flex; align-items:center; gap:8px;">
    <i class="fa-solid fa-volume-xmark" id="suara-icon"></i>
    <span id="suara-teks">Notifikasi Suara: Mati</span>
  </button>
</div>

<!-- 1. SUMMARY STAT BOXES -->
<div class="ringkasan-grid">
  <div class="box-ringkasan box-baru">
    <div>
      <span class="label">Pesanan Baru (Menunggu)</span>
      <b class="jumlah" id="cnt-baru">0</b>
    </div>
    <div class="icon">
      <i class="fa-solid fa-bell"></i>
    </div>
  </div>

  <div class="box-ringkasan box-proses">
    <div>
      <span class="label">Sedang Dimasak / Dibuat</span>
      <b class="jumlah" id="cnt-proses">0</b>
    </div>
    <div class="icon">
      <i class="fa-solid fa-fire-burner"></i>
    </div>
  </div>

  <div class="box-ringkasan box-selesai">
    <div>
      <span class="label">Selesai (Sesi Ini)</span>
      <b class="jumlah" id="cnt-selesai">0</b>
    </div>
    <div class="icon">
      <i class="fa-solid fa-circle-check"></i>
    </div>
  </div>
</div>

<!-- 2. KITCHEN TICKETS GRID -->
<div class="gdapur" id="grid"></div>

<style>
  .gdapur {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 18px;
  }
  .kartu-dapur {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-md);
    padding: 18px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-normal);
    border-top: 4px solid var(--coffee-400);
  }
  .kartu-dapur.baru {
    border-top-color: var(--danger);
  }
  .kartu-dapur.diproses {
    border-top-color: var(--warning);
  }
  .kartu-dapur:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-2px);
  }
  .dapur-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 1px dashed var(--border-subtle);
  }
  .dapur-customer {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-title);
  }
  .dapur-meta {
    font-size: 11.5px;
    color: var(--text-muted);
    margin-top: 2px;
  }
  .dapur-item-list {
    margin-bottom: 16px;
    flex: 1;
  }
  .dapur-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 0;
    border-bottom: 1px solid var(--border-subtle);
    font-size: 13.5px;
  }
  .dapur-item:last-child {
    border-bottom: none;
  }
  .dapur-qty {
    width: 28px;
    height: 28px;
    border-radius: var(--radius-xs);
    background: var(--coffee-50);
    color: var(--coffee-800);
    border: 1px solid var(--coffee-200);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 12px;
    flex-shrink: 0;
  }
  .btn-dapur-action {
    width: 100%;
    padding: 11px;
    border-radius: var(--radius-sm);
    font-weight: 700;
    font-size: 13px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: var(--transition-fast);
  }
  .btn-dapur-action.baru {
    background: var(--coffee-700);
    color: #ffffff;
  }
  .btn-dapur-action.baru:hover {
    background: var(--coffee-900);
  }
  .btn-dapur-action.diproses {
    background: var(--success);
    color: #ffffff;
  }
  .btn-dapur-action.diproses:hover {
    background: #166534;
  }
</style>

<script>
  const URL_DAFTAR = "<?= site_url('dapur/daftar'); ?>";
  const URL_UBAH = "<?= site_url('dapur/ubah'); ?>";
  const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  let suaraOn = false, ctx = null, dikenal = null;
  let jumlahSelesaiSesi = 0;

  function bip() {
    if (!ctx) return;
    [0, 0.22].forEach(t => {
      const o = ctx.createOscillator(), g = ctx.createGain();
      o.type = 'sine';
      o.frequency.value = 880;
      o.connect(g);
      g.connect(ctx.destination);
      g.gain.setValueAtTime(0.3, ctx.currentTime + t);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + t + 0.18);
      o.start(ctx.currentTime + t);
      o.stop(ctx.currentTime + t + 0.18);
    });
  }

  document.getElementById('suara').addEventListener('click', e => {
    suaraOn = !suaraOn;
    if (suaraOn && !ctx) ctx = new (window.AudioContext || window.webkitAudioContext)();
    if (ctx && ctx.state === 'suspended') ctx.resume();
    
    const chip = document.getElementById('suara');
    const icon = document.getElementById('suara-icon');
    const teks = document.getElementById('suara-teks');

    chip.classList.toggle('on', suaraOn);
    if (suaraOn) {
      icon.className = 'fa-solid fa-volume-high';
      teks.textContent = 'Notifikasi Suara: Aktif';
      bip();
    } else {
      icon.className = 'fa-solid fa-volume-xmark';
      teks.textContent = 'Notifikasi Suara: Mati';
    }
  });

  function cekBaru(data) {
    const sekarang = {};
    data.filter(p => p.status === 'baru').forEach(p => {
      sekarang[p.id + ':' + p.item.map(i => i.qty + i.nama).join('|')] = true;
    });
    if (dikenal !== null && suaraOn && Object.keys(sekarang).some(k => !(k in dikenal))) bip();
    dikenal = sekarang;
  }

  function render(data) {
    const baru = data.filter(p => p.status === 'baru').length;
    const proses = data.filter(p => p.status === 'diproses').length;

    document.getElementById('cnt-baru').textContent = baru;
    document.getElementById('cnt-proses').textContent = proses;
    document.getElementById('cnt-selesai').textContent = jumlahSelesaiSesi;

    document.title = (baru ? '(' + baru + ') ' : '') + 'Monitor Dapur - Cafe Arsa';
    document.getElementById('info').innerHTML =
      `<b>${data.length} pesanan aktif</b> (${baru} menunggu, ${proses} sedang dibuat) &middot; <small style="color:var(--text-light)">Update tiap 5 dtk</small>`;

    document.getElementById('grid').innerHTML = data.length ? data.map(p => `
      <div class="kartu-dapur ${p.status}">
        <div>
          <div class="dapur-head">
            <div>
              <div class="dapur-customer">${esc(p.nama_pelanggan || 'Pelanggan')}</div>
              <div class="dapur-meta">
                ${p.meja ? '<b>Meja ' + esc(p.meja) + '</b> &middot; ' : ''}#${esc(p.kode)} &middot; ${esc(p.dibuat.substr(11, 5))} WIB
              </div>
            </div>
            <span class="badge ${p.status === 'baru' ? 'b-belum' : 'b-baru'}">
              ${p.status === 'baru' ? 'Baru' : 'Diproses'}
            </span>
          </div>
          
          <div class="dapur-item-list">
            ${p.item.map(i => `
              <div class="dapur-item">
                <span class="dapur-qty">${i.qty}x</span>
                <span style="font-weight:600; color:var(--text-title);">${esc(i.nama)}</span>
              </div>
            `).join('')}
          </div>
        </div>

        <div>
          <button class="btn-dapur-action ${p.status}" data-id="${p.id}" data-ke="${p.status === 'baru' ? 'diproses' : 'selesai'}">
            ${p.status === 'baru' 
              ? '<i class="fa-solid fa-fire-burner"></i> Mulai Proses Masak' 
              : '<i class="fa-solid fa-circle-check"></i> Tandai Selesai'}
          </button>
        </div>
      </div>`).join('') : '<div class="kosong-box" style="grid-column:1/-1"><i class="fa-solid fa-mug-hot" style="font-size:36px; color:var(--coffee-300); display:block; margin-bottom:12px;"></i>Tidak ada pesanan aktif di dapur saat ini.</div>';
  }

  async function muat() {
    try {
      const data = await (await fetch(URL_DAFTAR)).json();
      cekBaru(data);
      render(data);
    } catch (e) {
      document.getElementById('info').textContent = 'Koneksi terputus, mencoba lagi...';
    }
  }

  document.getElementById('grid').addEventListener('click', async e => {
    const b = e.target.closest('button');
    if (!b) return;
    b.disabled = true;

    const keStatus = b.dataset.ke;
    const f = new FormData();
    f.append('id', b.dataset.id);
    f.append('status', keStatus);

    await fetch(URL_UBAH, { method: 'POST', body: f });

    if (keStatus === 'selesai') {
      jumlahSelesaiSesi++;
    }

    muat();
  });

  muat();
  setInterval(muat, 5000);
</script>

<?php $this->load->view('layout/footer'); ?>