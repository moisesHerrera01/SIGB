<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prueba extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model('mtps/Prueba_model');
  }

  public function index(){

    $data['title'] = "Prueba";
    $data['js'] = "";

    $template = array(
        'table_open' => '<table class="table table-striped table-bordered">'
    );
    $this->table->set_template($template);
    $this->table->set_heading('#','Nombre');

    foreach($this->Prueba_model->obtener() as $unidad) {
        $this->table->add_row($unidad->id_departamento, $unidad->departamento);
    }

    $data['body'] = $this->table->generate();
    $this->load->view('base', $data);
	}
}
?>
