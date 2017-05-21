<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seleccion_Categoria extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model('ActivoFijo/Categoria_model');
  }

  public function index(){

    $data['title'] = "Selección Categoría";
    $data['body'] = $this->load->view('ActivoFijo/seleccion_categoria_view','', TRUE);
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}
  //Se selecciona la categoria para posteriormente enlazar con subcategoria
  public function RecibirCategoria() {
      $USER = $this->session->userdata('logged_in');
      if($USER){
        if ($this->input->post('categoria')!=NULL) {
            redirect('ActivoFijo/Subcategoria/index/'.$this->input->post('categoria'));
          } else {
            redirect('ActivoFijo/Seleccion_Categoria/');
        }
      } else {
        redirect('login/index/error_no_autenticado');
      }
  }
}
?>
