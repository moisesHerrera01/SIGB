<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DetalleProveedor extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Proveedor'));
  }

  public function index(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "Datos de Proveedor";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';
      if ($this->uri->segment(4) != NULL) {
        $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Dato','Valor');
        $registros = $this->Proveedor->obtenerDatoProveedor($this->uri->segment(4));
        if (!($registros == FALSE)) {
            $this->table->add_row(1,'<strong>Nombre',$registros->nombre_proveedor);
            $this->table->add_row(2,'<strong>NIT',$registros->nit);
            $this->table->add_row(3,'<strong>Telefono',$registros->telefono);
            $this->table->add_row(4,'<strong>Correo',$registros->correo);
            $this->table->add_row(5,'<strong>Dirección',$registros->direccion);
            $this->table->add_row(6,'<strong>Categoria',$registros->nombre_categoria);
            $this->table->add_row(7,'<strong>Empresa',$registros->tipo_empresa);
            $this->table->add_row(8,'<strong>Tipo de obra',$registros->rubro);
            $this->table->add_row(9,'<strong>Nombre de contacto',$registros->nombre_contacto);
            $this->table->add_row(10,'<strong>Descripción',$registros->descripcion);
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "6");
          $this->table->add_row($msg);
        }
        $table = "<div class='content_table'>".
                "<div class='limit-content-title'><span class='icono icon-table icon-title'> ". $registros->nombre_proveedor."</span></div>".
                "<div class='table-responsive'>" . $this->table->generate() . "</div><div>";
      }
      $data['body'] = $this->load->view('Bodega/DetalleProveedor_view', '',TRUE) . "<br>" . $table;
      $this->load->view('base', $data);
    } else {
      redirect('login/index/forbidden');
    }
  }
}
?>
