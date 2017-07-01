<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rastreabilidad extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }  else {
      $USER = $this->session->userdata('logged_in');
      $modulo = $this->User_model->obtenerModulo('Rastreabilidad/reporte');
      if (!$this->User_model->validarAccesoCrud($modulo, $USER['id'], 'select')) {
        redirect('dashboard/index/forbidden');
      }
    }

    $this->load->helper(array('form', 'paginacion'));
    $this->load->library(array('table'));
    //$this->load->model('ActivoFijo/datos_comunes_model');
    //$this->load->model('mtps/Seccion_model');
  }

  public function Recibirfechas() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('fecha_inicio')) {
      if($this->input->post('fecha_fin')==NULL){
        redirect('Rastreabilidad/reporte/'.$this->input->post('fecha_inicio').'/'.$fecha_actual);
      }else{
        redirect('Rastreabilidad/reporte/'.$this->input->post('fecha_inicio').'/'.$this->input->post('fecha_fin'));
      }} else {
        redirect('Rastreabilidad/reporte/');
    }
  }

  public function reporte(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "Rastreabilidad";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';
      if (($this->uri->segment(3)) != '') {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Usuario','Rol','Modulo','Registro','Operación','Fecha','Hora');
        $num = '10';
        $registros = $this->User_model->obtenerRastreabilidadFiltro($this->uri->segment(3),
        $this->uri->segment(4),$num, $this->uri->segment(5));
        $total = $this->User_model->obtenerRastreabilidadFiltroTotal($this->uri->segment(3),
        $this->uri->segment(4));
        $cant=$total->total;
        $pagination = paginacion('index.php/Rastreabilidad/reporte/'.$this->uri->segment(3).
        '/'.$this->uri->segment(4),$cant,$num, '5');

        if (!($registros == FALSE)) {
          $fecha_inicio=$this->uri->segment(3);
          $fecha_fin=$this->uri->segment(4);
          $i = 1;
          foreach($registros as $pro) {
            if ($pro->id_registro==NULL) {
              $pro->id_registro='N/A';
            }
            $this->table->add_row($pro->nombre_completo,$pro->nombre_rol,$pro->nombre_modulo,$pro->id_registro,$pro->operacion,$pro->fecha,$pro->hora);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "6");
          $this->table->add_row($msg);
        }
        date_default_timezone_set('America/El_Salvador');
        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'>".
                    "<div class='title-reporte'>".
                      "Reporte de Rastreabilidad de los usuarios..".
                    "</div>".
                    "<div class='title-header'>
                      <ul>
                        <li>Fecha emisión: ".date('d/m/Y')."</li>
                        <li>Nombre la compañia: MTPS</li>
                        <li>N° pagina: 1/1</li>
                        <li>Nombre pantalla:</li>
                        <li>Usuario: ".$USER['nombre_completo']."</li>
                        <br />
                        <li>Parametros: ".$this->uri->segment(4) . " - " . $this->uri->segment(5)."</li>
                      </ul>
                    </div>".
                  "</div>".
                  "<div class='limit-content'>". "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }
      $data['body'] = $this->load->view('rastreabilidad_view', array('user' => $USER), TRUE) . "<br>" . $table;
      $this->load->view('base', $data);
    } else {
      redirect('login/index/forbidden');
    }
  }
}
?>
