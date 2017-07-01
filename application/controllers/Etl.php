<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Etl extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login');
    } else {
      $USER = $this->session->userdata('logged_in');
      $modulo = $this->User_model->obtenerModulo('Etl');
      if (!$this->User_model->validarAccesoCrud($modulo, $USER['id'], 'select')) {
        redirect('dashboard/index/forbidden');
      }
    }

    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Etl_model'));
  }

  public function index(){
    $data['title'] = "ETL";
    $msg = array('alert' => $this->uri->segment(3),'controller'=>'Etl' );
    $data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Etl_view', '', TRUE) .
                    "<br><div class='content_table'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> PROCESO DE EXTRACCIÓN TRANSFORMACIÓN Y CARGA (ETL).</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
  }

  public function mostrarTabla(){
          $template = array(
              'table_open' => '<table class="table table-striped table-bordered">'
          );
          $cant=$this->Etl_model->obtenerCantReg();
          $this->table->set_template($template);
          $botones = "<div class=\"content-btn-table\">Acciones a realizar a la base de datos del Sistema Gerencial:
          <a class=\"btn btn-default\" href='".base_url("index.php/Etl/borrar/"). "'>Vaciar todas las tablas (Truncar)</a>
          <a class=\"btn btn-default\" href='".base_url("index.php/Etl/cargarGrupo1/"). "'>Cargar Todas las tablas (ETL)</a></div>";
          $this->table->set_heading('#','Nombre de la tabla','Registros en la BD Transaccional','Registros en la BD Gerencial');
          $i=1;
          $total_sigb=0;
          $total_mtps=0;
          foreach ($cant as $reg) {
            $this->table->add_row($i,$reg->table_name,$reg->cant_mtps,$reg->cant_sigb);
            $total_sigb+=$reg->cant_sigb;
            $total_mtps+=$reg->cant_mtps;
            $i++;
          }
          $cell = array('data' => 'Total de registros', 'colspan' => 2);
          $this->table->add_row($cell, $total_mtps,$total_sigb);
          return $botones."<div class='table-responsive'>" . $this->table->generate() . "</div>";
  }

  public function cargarGrupo1(){
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
        $USER = $this->session->userdata('logged_in');
        $modulo=$this->User_model->obtenerModulo('Etl');
        $hora=date("H:i:s");
        $rastrea = array(
          'id_usuario' =>$USER['id'],
          'id_modulo' =>$modulo,
          'fecha' =>$fecha_actual,
          'hora' =>$hora,
          'operacion'=> 'ACTUALIZA'
        );
      $this->User_model->insertarRastreabilidad($rastrea);
      $this->Etl_model->cargarGrupo1();
      redirect('/Etl/index/etl');
  }
  public function borrar(){
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('Etl');
    $hora=date("H:i:s");
    $rastrea = array(
      'id_usuario' =>$USER['id'],
      'id_modulo' =>$modulo,
      'fecha' =>$fecha_actual,
      'hora' =>$hora,
      'operacion'=> 'ELIMINA'
    );
    $this->User_model->insertarRastreabilidad($rastrea);
    $this->Etl_model->vaciarBD();
    redirect('/Etl/index/cls');
  }
}
?>
