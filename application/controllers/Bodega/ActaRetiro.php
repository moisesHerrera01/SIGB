<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ActaRetiro extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login');
    }
    $this->load->helper(array('fecha'));
    $this->load->library('table');
    $this->load->model('Bodega/Solicitud_Model');
  }

  public function index(){

    $data['acta']=$this->Solicitud_Model->actaRetiro($this->uri->segment(4));

    $data['title'] = "Retiro de bodega";

    $this->load->view('Bodega/ActaRetiro_view',$data);

  }

}
?>
