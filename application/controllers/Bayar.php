<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bayar extends CI_Controller {

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
    $this->load->view('bayar/index');
  }

  public function daftar() {
    $pesanan = $this->db->select('id, kode, nama_pelanggan, meja, total, status, dibuat')
                        ->where('status_bayar', 'belum')
                        ->order_by('id', 'ASC')
                        ->get('pesanan')->result();
    foreach ($pesanan as $p) {
      $p->item = $this->db->select('nama, qty')
                          ->get_where('pesanan_detail', ['pesanan_id' => $p->id])->result();
    }
    $this->_json($pesanan);
  }

  public function proses() {
    $id     = (int) $this->input->post('id');
    $metode = $this->input->post('metode');
    $uang   = (int) $this->input->post('uang');

    $p = $this->db->get_where('pesanan', ['id' => $id, 'status_bayar' => 'belum'])->row();
    if (!$p) {
      return $this->_json(['ok' => FALSE, 'pesan' => 'Pesanan tidak ditemukan atau sudah dibayar']);
    }
    if (!in_array($metode, ['tunai', 'qris', 'transfer'])) {
      return $this->_json(['ok' => FALSE, 'pesan' => 'Metode pembayaran tidak valid']);
    }
    if ($metode != 'tunai') {
      $uang = $p->total;
    }
    if ($uang < $p->total) {
      return $this->_json(['ok' => FALSE, 'pesan' => 'Uang yang diterima kurang']);
    }

    $this->db->where(['id' => $id, 'status_bayar' => 'belum'])->update('pesanan', [
      'status_bayar' => 'lunas',
      'metode'       => $metode,
      'bayar'        => $uang,
      'kembali'      => $uang - $p->total,
      'dibayar_pada' => date('Y-m-d H:i:s')
    ]);
    $this->_json(['ok' => TRUE, 'kembali' => $uang - $p->total]);
  }
  public function batal() {
    $id = (int) $this->input->post('id');
    $this->db->where(['id' => $id, 'status_bayar' => 'belum'])
             ->update('pesanan', ['status_bayar' => 'batal', 'status' => 'batal']);
    if ($this->db->affected_rows() < 1) {
      return $this->_json(['ok' => FALSE, 'pesan' => 'Pesanan tidak ditemukan atau sudah dibayar']);
    }
    $this->_json(['ok' => TRUE]);
  }

  private function _json($data) {
    $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }
}