Tambah <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller {

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
    $this->db->order_by('jenis, kategori, nama');
    $data['menu'] = $this->db->get('menu')->result();
    $this->load->view('menu/index', $data);
  }

  public function tambah() {
    $data['m'] = NULL;
    $data['kategori'] = $this->_daftar_kategori();
    $this->load->view('menu/form', $data);
  }

  public function edit($id) {
    $data['m'] = $this->db->get_where('menu', ['id' => $id])->row();
    if (!$data['m']) {
      show_404();
    }
    $data['kategori'] = $this->_daftar_kategori();
    $this->load->view('menu/form', $data);
  }

  public function simpan() {
    $id    = $this->input->post('id');
    $nama  = trim($this->input->post('nama', TRUE));
    $harga = (int) $this->input->post('harga');

    if ($nama == '' || $harga <= 0) {
      $this->session->set_flashdata('error', 'Nama dan harga harus diisi dengan benar');
      redirect($id ? 'menu/edit/' . $id : 'menu/tambah');
    }

    $data = [
      'nama'     => $nama,
      'jenis'    => $this->input->post('jenis') == 'minuman' ? 'minuman' : 'makanan',
      'kategori' => trim($this->input->post('kategori', TRUE)),
      'harga'    => $harga,
      'aktif'    => $this->input->post('aktif') ? 1 : 0
    ];

    // foto (tidak wajib)
    $lama = $id ? $this->db->get_where('menu', ['id' => $id])->row() : NULL;

    if (!empty($_FILES['foto']['name'])) {
      $config['upload_path']   = './uploads/menu/';
      $config['allowed_types'] = 'jpg|jpeg|png|webp';
      $config['max_size']      = 2048;
      $config['encrypt_name']  = TRUE;
      $this->load->library('upload', $config);

      if (!$this->upload->do_upload('foto')) {
        $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
        redirect($id ? 'menu/edit/' . $id : 'menu/tambah');
      }
      $data['foto'] = $this->upload->data('file_name');
      if ($lama && $lama->foto) @unlink('./uploads/menu/' . $lama->foto);
    } elseif ($lama && $lama->foto && $this->input->post('hapus_foto')) {
      @unlink('./uploads/menu/' . $lama->foto);
      $data['foto'] = NULL;
    }

    if ($id) {
      $this->db->where('id', $id)->update('menu', $data);
      $this->session->set_flashdata('sukses', 'Menu berhasil diperbarui');
    } else {
      $this->db->insert('menu', $data);
      $this->session->set_flashdata('sukses', 'Menu berhasil ditambahkan');
    }
    redirect('menu');
  }

  public function hapus($id) {
    $m = $this->db->get_where('menu', ['id' => $id])->row();
    if ($m && $m->foto) @unlink('./uploads/menu/' . $m->foto);
    $this->db->delete('menu', ['id' => $id]);
    $this->session->set_flashdata('sukses', 'Menu berhasil dihapus');
    redirect('menu');
  }

  private function _daftar_kategori() {
    $this->db->distinct()->select('kategori')->order_by('kategori');
    return $this->db->get('menu')->result();
  }
}