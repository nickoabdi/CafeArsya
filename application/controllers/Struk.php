<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Struk extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if (!$this->session->userdata('logged_in')) {
      redirect('auth');
    }
    if (!in_array($this->session->userdata('role'), ['admin', 'kasir'])) {
      redirect('dashboard');
    }
  }

  public function lihat($id = 0) {
    $p = $this->db->select('p.*, u.nama AS kasir', FALSE)
                  ->from('pesanan p')
                  ->join('users u', 'u.id = p.kasir_id', 'left')
                  ->where('p.id', (int) $id)->get()->row();
    if (!$p) {
      show_404();
    }
    $data['p'] = $p;
    $data['item'] = $this->db->get_where('pesanan_detail', ['pesanan_id' => $p->id])->result();
    $this->load->view('struk/lihat', $data);
  }
}