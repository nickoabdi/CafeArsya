<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Riwayat extends CI_Controller {

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
    $this->load->view('riwayat/index');
  }

  public function data() {
    $q      = trim((string) $this->input->get('q', TRUE));
    $dari   = (string) $this->input->get('dari');
    $sampai = (string) $this->input->get('sampai');
    $status = (string) $this->input->get('status');

    $this->db->select("p.id, p.kode, p.nama_pelanggan, p.meja, p.total, p.metode, p.status_bayar, p.dibuat,
      (SELECT GROUP_CONCAT(CONCAT(d.qty, 'x ', d.nama) SEPARATOR ', ')
         FROM pesanan_detail d WHERE d.pesanan_id = p.id) AS isi", FALSE)
             ->from('pesanan p');

    if ($q !== '') {
      $this->db->group_start()->like('p.nama_pelanggan', $q)->or_like('p.kode', $q)->group_end();
    }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dari)) {
      $this->db->where('p.dibuat >=', $dari . ' 00:00:00');
    }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampai)) {
      $this->db->where('p.dibuat <=', $sampai . ' 23:59:59');
    }
    if (in_array($status, ['belum', 'lunas', 'batal'])) {
      $this->db->where('p.status_bayar', $status);
    }

    $rows = $this->db->order_by('p.id', 'DESC')->limit(200)->get()->result();
    $this->output->set_content_type('application/json')->set_output(json_encode($rows));
  }
}