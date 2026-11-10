<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Beranda extends CI_Controller {

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
    $hari    = date('Y-m-d');
    $kemarin = date('Y-m-d', strtotime('-1 day'));
    $dari    = $hari . ' 00:00:00';
    $sampai  = $hari . ' 23:59:59';

    $d['total_pesanan'] = $this->db->where('dibuat >=', $dari)->where('dibuat <=', $sampai)
                                   ->where('status_bayar !=', 'batal')->count_all_results('pesanan');
    $d['menu_aktif']    = $this->db->where('aktif', 1)->count_all_results('menu');
    $d['omzet']         = $this->_omzet($hari);

    $kmrn = $this->_omzet($kemarin);
    $d['persen'] = $kmrn > 0 ? round(($d['omzet'] - $kmrn) / $kmrn * 100) : NULL;

    $d['pelanggan'] = (int) $this->db->select('COUNT(DISTINCT LOWER(nama_pelanggan)) AS n', FALSE)
                                     ->where('dibuat >=', $dari)->where('dibuat <=', $sampai)
                                     ->where('status_bayar !=', 'batal')->get('pesanan')->row()->n;

    $d['terbaru'] = $this->db->select('kode, nama_pelanggan, total, status_bayar, dibuat')
                             ->order_by('id', 'DESC')->limit(5)->get('pesanan')->result();

    // grafik omzet 7 hari terakhir
    $awal = date('Y-m-d', strtotime('-6 day'));
    $rows = $this->db->select('DATE(dibayar_pada) AS k, SUM(total) AS omzet', FALSE)
                     ->where('status_bayar', 'lunas')->where('dibayar_pada >=', $awal . ' 00:00:00')
                     ->group_by('k')->get('pesanan')->result();
    $peta = [];
    foreach ($rows as $r) { $peta[$r->k] = (int) $r->omzet; }

    $bln = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $d['label'] = [];
    $d['nilai'] = [];
    for ($i = 0; $i < 7; $i++) {
      $t = strtotime($awal . ' +' . $i . ' day');
      $k = date('Y-m-d', $t);
      $d['label'][] = date('j', $t) . ' ' . $bln[date('n', $t) - 1];
      $d['nilai'][] = isset($peta[$k]) ? $peta[$k] : 0;
    }

    $this->load->view('beranda/index', $d);
  }

  private function _omzet($tgl) {
    $r = $this->db->select('COALESCE(SUM(total),0) AS n', FALSE)
                  ->where('status_bayar', 'lunas')
                  ->where('dibayar_pada >=', $tgl . ' 00:00:00')
                  ->where('dibayar_pada <=', $tgl . ' 23:59:59')
                  ->get('pesanan')->row();
    return (int) $r->n;
  }
}