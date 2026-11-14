<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Akun extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if (!$this->session->userdata('logged_in')) {
      redirect('auth');
    }
    if ($this->session->userdata('role') != 'admin') {
      redirect('dashboard');
    }
  }

  public function index() {
    $data['users'] = $this->db->order_by('role, nama')->get('users')->result();
    $this->load->view('akun/index', $data);
  }

  public function simpan() {
    $nama     = trim($this->input->post('nama', TRUE));
    $username = strtolower(trim($this->input->post('username', TRUE)));
    $password = (string) $this->input->post('password');
    $role     = $this->input->post('role');

    if ($nama == '' || $username == '' || strlen($password) < 6 || !in_array($role, ['admin', 'kasir', 'dapur'])) {
      return $this->_balik('error', 'Isi semua kolom dengan benar (password minimal 6 karakter)');
    }
    if ($this->db->get_where('users', ['username' => $username])->row()) {
      return $this->_balik('error', 'Username sudah dipakai');
    }
    $this->db->insert('users', [
      'nama'     => $nama,
      'username' => $username,
      'password' => password_hash($password, PASSWORD_DEFAULT),
      'role'     => $role
    ]);
    $this->_balik('sukses', 'Akun berhasil ditambahkan');
  }

  public function password() {
    $id       = (int) $this->input->post('id');
    $password = (string) $this->input->post('password');

    if (strlen($password) < 6) {
      return $this->_balik('error', 'Password minimal 6 karakter');
    }
    $this->db->where('id', $id)->update('users', ['password' => password_hash($password, PASSWORD_DEFAULT)]);
    $this->_balik('sukses', 'Password berhasil diganti');
  }

  public function status() {
    $id = (int) $this->input->post('id');
    if ($id == $this->session->userdata('id')) {
      return $this->_balik('error', 'Akun yang sedang dipakai tidak bisa dinonaktifkan');
    }
    $u = $this->db->get_where('users', ['id' => $id])->row();
    if (!$u) {
      return $this->_balik('error', 'Akun tidak ditemukan');
    }
    $this->db->where('id', $id)->update('users', ['aktif' => $u->aktif ? 0 : 1]);
    $this->_balik('sukses', $u->aktif ? 'Akun dinonaktifkan' : 'Akun diaktifkan');
  }

  public function nama() {
    $id   = (int) $this->input->post('id');
    $nama = trim($this->input->post('nama', TRUE));

    if ($nama == '') {
      return $this->_balik('error', 'Nama tidak boleh kosong');
    }
    $this->db->where('id', $id)->update('users', ['nama' => $nama]);

    // kalau yang diubah akun sendiri, nama di pojok kanan atas ikut berubah
    if ($id == $this->session->userdata('id')) {
      $this->session->set_userdata('nama', $nama);
    }
    $this->_balik('sukses', 'Nama berhasil diubah');
  }

  private function _balik($jenis, $pesan) {
    $this->session->set_flashdata($jenis, $pesan);
    redirect('akun');
    
  }
  public function hapus() {
    $id = (int) $this->input->post('id');
    if ($id == $this->session->userdata('id')) {
      return $this->_balik('error', 'Akun yang sedang dipakai tidak bisa dihapus');
    }
    if (!$this->db->get_where('users', ['id' => $id])->row()) {
      return $this->_balik('error', 'Akun tidak ditemukan');
    }
    $this->db->delete('users', ['id' => $id]);
    $this->_balik('sukses', 'Akun berhasil dihapus');
  }
}