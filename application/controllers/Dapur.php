<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dapur extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if (!$this->session->userdata('logged_in')) {
      redirect('auth');
    }
    if (!in_array($this->session->userdata('role'), ['dapur', 'admin','kasir'])) {
      redirect('dashboard');
    }
  }

  public function index() {
    $this->load->view('dapur/index');
  }

  public function daftar() {
    $pesanan = $this->db->select('id, kode,nama_pelanggan, meja, status, dibuat')
                        ->where_in('status', ['baru', 'diproses'])
                        ->order_by('id', 'ASC')
                        ->get('pesanan')->result();
    foreach ($pesanan as $p) {
      $p->item = $this->db->select('nama, qty')
                          ->get_where('pesanan_detail', ['pesanan_id' => $p->id])->result();
    }
    $this->_json($pesanan);
  }

  public function ubah() {
    $id     = (int) $this->input->post('id');
    $status = $this->input->post('status');

    if (!in_array($status, ['diproses', 'selesai'])) {
      return $this->_json(['ok' => FALSE]);
    }
    // urutan wajib: baru -> diproses -> selesai
    $dari = ($status == 'diproses') ? 'baru' : 'diproses';
    $this->db->where(['id' => $id, 'status' => $dari])->update('pesanan', ['status' => $status]);
    $this->_json(['ok' => TRUE]);
  }

  private function _json($data) {
    $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }
}