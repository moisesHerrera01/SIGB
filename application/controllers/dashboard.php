<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model(array('Bodega/Factura_Model', 'Bodega/Producto'));
  }

  public function index(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $msg = array('alert' => $this->uri->segment(3),'controller'=>'dashboard');
      $data = array(
        'title' => "Home",
        'menu' => $this->menu_dinamico->menus($USER, $this->uri->segment(1)),
        'usuario' => $USER['nombre_completo'],
        'rol' => $USER['rol'],
        'msg' => $this->load->view('mensajes', $msg, TRUE)
      );


      $this->load->view('dashboard_view', $data);

    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

}

?>
