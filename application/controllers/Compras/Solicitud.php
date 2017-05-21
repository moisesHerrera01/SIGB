<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitud extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login');
    }
    $this->load->helper(array('fecha'));
    $this->load->library('table');
    $this->load->model('Compras/Solicitud_Compra_Model');
  }

  public function index(){
    $data['prueba']=$this->Solicitud_Compra_Model->obtenerDatosSolicitud($this->uri->segment(4));
    $data['autorizante'] = $this->Solicitud_Compra_Model->obtenerAutorizante_AdminOC($this->uri->segment(4),1);
    $data['adminoc'] = $this->Solicitud_Compra_Model->obtenerAutorizante_AdminOC($this->uri->segment(4),2);
    $data['title'] = "Solicitud";
    $this->load->view('Compras/Solicitud_view',$data);
  }
}
?>
