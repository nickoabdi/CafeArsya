<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if (!$this->session->userdata('logged_in')) {
      redirect('auth');
    }
    if (!in_array($this->session->userdata('role'), ['admin', 'kasir'])) {
      redirect('dashboard');
    }
  }

  public function index() {
    $this->load->view('laporan/index');
  }

  public function data() {
    list($periode, $awal, $akhir) = $this->_param();
    $dari   = $awal . ' 00:00:00';
    $sampai = $akhir . ' 23:59:59';

    $ring = $this->_lunas($dari, $sampai)
                 ->select('COUNT(*) AS jumlah, COALESCE(SUM(total),0) AS omzet', FALSE)
                 ->get('pesanan')->row();

    $metode = $this->_lunas($dari, $sampai)
                   ->select('metode, COUNT(*) AS jumlah, SUM(total) AS omzet', FALSE)
                   ->group_by('metode')->get('pesanan')->result();

    $terlaris = $this->_lunas($dari, $sampai)
                     ->select('d.nama, SUM(d.qty) AS qty, SUM(d.subtotal) AS omzet', FALSE)
                     ->from('pesanan p')
                     ->join('pesanan_detail d', 'd.pesanan_id = p.id')
                     ->group_by('d.nama')->order_by('qty', 'DESC')->limit(10)
                     ->get()->result();

    // data grafik
    $kunci = [];
    $label = [];
    switch ($periode) {
      case 'harian':
        $expr = 'HOUR(dibayar_pada)';
        for ($h = 0; $h < 24; $h++) { $kunci[] = $h; $label[] = sprintf('%02d', $h); }
        break;
      case 'mingguan':
        $expr = 'DATE(dibayar_pada)';
        $hari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        for ($i = 0; $i < 7; $i++) {
          $kunci[] = date('Y-m-d', strtotime($awal . ' +' . $i . ' day'));
          $label[] = $hari[$i];
        }
        break;
      case 'bulanan':
        $expr = 'DAY(dibayar_pada)';
        $n = (int) date('t', strtotime($awal));
        for ($d = 1; $d <= $n; $d++) { $kunci[] = $d; $label[] = $d; }
        break;
      default:
        $expr = 'MONTH(dibayar_pada)';
        $bln = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($m = 1; $m <= 12; $m++) { $kunci[] = $m; $label[] = $bln[$m - 1]; }
    }

    $rows = $this->_lunas($dari, $sampai)
                 ->select($expr . ' AS k, SUM(total) AS omzet', FALSE)
                 ->group_by('k')->get('pesanan')->result();
    $peta = [];
    foreach ($rows as $r) { $peta[$r->k] = (int) $r->omzet; }
    $nilai = [];
    foreach ($kunci as $k) { $nilai[] = isset($peta[$k]) ? $peta[$k] : 0; }

    $this->output->set_content_type('application/json')->set_output(json_encode([
      'periode'  => $periode,
      'awal'     => $awal,
      'akhir'    => $akhir,
      'jumlah'   => (int) $ring->jumlah,
      'omzet'    => (int) $ring->omzet,
      'metode'   => $metode,
      'terlaris' => $terlaris,
      'grafik'   => ['label' => $label, 'nilai' => $nilai]
    ]));
  }

  public function unduh() {
    list($periode, $awal, $akhir) = $this->_param();
    $pesanan = $this->_lunas($awal . ' 00:00:00', $akhir . ' 23:59:59')
                    ->order_by('dibayar_pada', 'ASC')->get('pesanan')->result();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="laporan-' . $periode . '-' . $awal . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Kode', 'Waktu Bayar', 'Nama Pelanggan', 'Meja', 'Metode', 'Total', 'Dibayar', 'Kembali', 'Pesanan'], ';');

    foreach ($pesanan as $p) {
      $item = $this->db->get_where('pesanan_detail', ['pesanan_id' => $p->id])->result();
      $teks = [];
      foreach ($item as $i) { $teks[] = $i->qty . 'x ' . $i->nama; }
      fputcsv($out, [
        $p->kode, $p->dibayar_pada, $this->_aman($p->nama_pelanggan), $this->_aman($p->meja),
        $p->metode, $p->total, $p->bayar, $p->kembali, $this->_aman(implode(', ', $teks))
      ], ';');
    }
    fclose($out);
    exit;
  }

  // pesanan lunas dalam rentang waktu bayar
  private function _lunas($dari, $sampai) {
    return $this->db->where('status_bayar', 'lunas')
                    ->where('dibayar_pada >=', $dari)
                    ->where('dibayar_pada <=', $sampai);
  }

  private function _param() {
    $periode = $this->input->get('periode');
    if (!in_array($periode, ['harian', 'mingguan', 'bulanan', 'tahunan'])) {
      $periode = 'harian';
    }
    $tgl = (string) $this->input->get('tanggal');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl) || !strtotime($tgl)) {
      $tgl = date('Y-m-d');
    }
    $t = strtotime($tgl);

    switch ($periode) {
      case 'mingguan':
        $awal  = date('Y-m-d', strtotime('monday this week', $t));
        $akhir = date('Y-m-d', strtotime('sunday this week', $t));
        break;
      case 'bulanan':
        $awal  = date('Y-m-01', $t);
        $akhir = date('Y-m-t', $t);
        break;
      case 'tahunan':
        $awal  = date('Y-01-01', $t);
        $akhir = date('Y-12-31', $t);
        break;
      default:
        $awal = $akhir = date('Y-m-d', $t);
    }
    return [$periode, $awal, $akhir];
  }

  // cegah isi sel diawali = + - @ dibaca sebagai rumus di Excel
  private function _aman($teks) {
    $teks = (string) $teks;
    return ($teks !== '' && strpos('=+-@', $teks[0]) !== FALSE) ? "'" . $teks : $teks;
  }
}