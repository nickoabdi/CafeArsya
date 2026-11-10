<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

  public function index() {
    if ($this->session->userdata('logged_in')) {
      redirect('dashboard');
    }
    $this->load->view('login');
  }

  public function proses() {
    $username = $this->input->post('username', TRUE);
    $password = $this->input->post('password');

    $user = $this->db->get_where('users', ['username' => $username])->row();

    if ($user && !$user->aktif) {
      $this->session->set_flashdata('error', 'Akun ini dinonaktifkan');
      redirect('auth');
    }

    if ($user && password_verify($password, $user->password)) {
      $this->session->set_userdata([
        'logged_in' => TRUE,
        'id'        => $user->id,
        'nama'      => $user->nama,
        'role'      => $user->role
      ]);
      redirect('dashboard');
    }

    $this->session->set_flashdata('error', 'Username atau password salah');
    redirect('auth');
  }

  public function logout() {
    $this->session->sess_destroy();
    redirect('auth');
  }

  // Sementara saja. HAPUS setelah dipakai.
  public function buat_admin() {
    $this->db->insert('users', [
      'nama'     => 'Owner',
      'username' => 'admin',
      'password' => password_hash('admin123', PASSWORD_DEFAULT),
      'role'     => 'admin'
    ]);
    echo 'Akun admin dibuat';
  }

  // Sementara saja. HAPUS setelah dipakai.
  public function buat_akun() {
    $this->db->insert_batch('users', [
      [
        'nama'     => 'Kasir 1',
        'username' => 'kasir',
        'password' => password_hash('kasir123', PASSWORD_DEFAULT),
        'role'     => 'kasir'
      ],
      [
        'nama'     => 'Dapur 1',
        'username' => 'dapur',
        'password' => password_hash('dapur123', PASSWORD_DEFAULT),
        'role'     => 'dapur'
      ]
    ]);
    echo 'Akun kasir dan dapur dibuat';
  }
}
