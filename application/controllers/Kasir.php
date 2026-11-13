<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kasir extends CI_Controller {

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
    $this->db->where('aktif', 1)->order_by('jenis, kategori, nama');
    $data['menu'] = $this->db->get('menu')->result();

    // mode ubah pesanan: kasir?ubah=ID
    $data['edit'] = NULL;
    $ubah = (int) $this->input->get('ubah');
    if ($ubah) {
      $p = $this->db->get_where('pesanan', ['id' => $ubah, 'status_bayar' => 'belum'])->row();
      if (!$p) {
        redirect('bayar');
      }
      $p->item = $this->db->select('menu_id, nama, harga, qty')
                          ->get_where('pesanan_detail', ['pesanan_id' => $p->id])->result();
      $data['edit'] = $p;
    }

    $this->load->view('kasir/dashboard', $data);
  }

  public function simpan() {
    $id    = (int) $this->input->post('id');
    $items = json_decode($this->input->post('items'), TRUE);
    $nama  = trim($this->input->post('nama', TRUE));
    $meja  = trim($this->input->post('meja', TRUE));

    if ($nama == '') {
      return $this->_json(['ok' => FALSE, 'pesan' => 'Nama pelanggan wajib diisi']);
    }
    if (empty($items)) {
      return $this->_json(['ok' => FALSE, 'pesan' => 'Belum ada menu yang dipilih']);
    }

    // harga dihitung ulang dari database, bukan dari browser
    $total = 0;
    $detail = [];
    foreach ($items as $it) {
      $qty = (int) $it['qty'];
      $m = $this->db->get_where('menu', ['id' => (int) $it['id'], 'aktif' => 1])->row();
      if (!$m || $qty < 1) continue;
      $sub = $m->harga * $qty;
      $total += $sub;
      $detail[] = [
        'menu_id'  => $m->id,
        'nama'     => $m->nama,
        'harga'    => $m->harga,
        'qty'      => $qty,
        'subtotal' => $sub
      ];
    }
    if (!$detail) {
      return $this->_json(['ok' => FALSE, 'pesan' => 'Tidak ada menu yang valid']);
    }

    $lama = NULL;
    if ($id) {
      $lama = $this->db->get_where('pesanan', ['id' => $id, 'status_bayar' => 'belum'])->row();
      if (!$lama) {
        return $this->_json(['ok' => FALSE, 'pesan' => 'Pesanan tidak ditemukan, sudah dibayar, atau dibatalkan']);
      }
    }

    $this->db->trans_start();
    if ($lama) {
      // ubah pesanan: dapur melihatnya lagi sebagai pesanan baru
      $this->db->where('id', $id)->update('pesanan', [
        'nama_pelanggan' => $nama,
        'meja'           => $meja,
        'total'          => $total,
        'status'         => 'baru'
      ]);
      $this->db->delete('pesanan_detail', ['pesanan_id' => $id]);
      $pid  = $id;
      $kode = $lama->kode;
    } else {
      $kode = 'CA' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
      $this->db->insert('pesanan', [
        'kode'           => $kode,
        'nama_pelanggan' => $nama,
        'meja'           => $meja,
        'total'          => $total,
        'kasir_id'       => $this->session->userdata('id')
      ]);
      $pid = $this->db->insert_id();
    }
    foreach ($detail as &$d) { $d['pesanan_id'] = $pid; }
    unset($d);
    $this->db->insert_batch('pesanan_detail', $detail);
    $this->db->trans_complete();

    if (!$this->db->trans_status()) {
      return $this->_json(['ok' => FALSE, 'pesan' => 'Gagal menyimpan pesanan']);
    }
    $this->_json(['ok' => TRUE, 'kode' => $kode, 'nama' => $nama]);
  }

  private function _json($data) {
    $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }
}