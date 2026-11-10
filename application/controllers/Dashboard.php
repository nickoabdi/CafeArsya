<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
  public function index() {
    if (!$this->session->userdata('logged_in')) {
      redirect('auth');
    }

    if ($this->session->userdata('role') == 'dapur') {
      redirect('dapur');
    } else {
      redirect('beranda');
    }
  }
}