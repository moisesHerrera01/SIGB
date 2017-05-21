<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detalle_Movimiento extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Detalle_movimiento_model','ActivoFijo/Bienes_inmuebles_model'));
  }

  public function index(){
    $data['title'] = "Detalle Movimiento";
    $id_mov = $this->uri->segment(5);
    $nombre = $this->Detalle_movimiento_model->obtenerMovimiento($id_mov);
    $msg = array('alert' => $this->uri->segment(6),'id_mov' => $id_mov,'controller'=>'detalle_movimiento');
		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/Reportes/detalle_movimiento_view',$msg,TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Detalle Movimientos</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla()."</div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    $template = array(
        'table_open' => '<table class="table table-striped table-bordered">'
    );
    $this->table->set_template($template);
    $this->table->set_heading('Id','Descripción','Marca', 'Modelo','Color','Serie','Código','Código anterior');
    $num = '10';
    $pagination = '';
    $registros;
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('busca') == "")) {
          $registros = $this->Detalle_movimiento_model->buscarDetalleMovimiento($this->input->post('busca'),$this->uri->segment(5));
      } else {
          $registros = $this->Detalle_movimiento_model->obtenerDetalleMovimientosLimit($num, $this->uri->segment(6),$this->uri->segment(5));
          $pagination = paginacion('index.php/ActivoFijo/Detalle_Movimiento/index/'.$this->uri->segment(5), $this->Detalle_movimiento_model->totalDetalleMovimientos($this->uri->segment(5)),
                        $num, '5');
      }
    } else {
          $registros = $this->Detalle_movimiento_model->obtenerDetalleMovimientosLimit($num, $this->uri->segment(6),$this->uri->segment(5));
          $pagination = paginacion('index.php/ActivoFijo/Detalle_Movimiento/index/'.$this->uri->segment(5), $this->Detalle_movimiento_model->totalDetalleMovimientos($this->uri->segment(5)),
                        $num, '6');
    }
    if (!($registros == FALSE)) {
      foreach($registros as $det) {
          $onClick = "llenarFormulario('detalle', ['id_detalle_movimiento','autocomplete','bien'],
          ['$det->id_detalle_movimiento','$det->codigo','$det->id_bien'])";
          $this->table->add_row($det->id_detalle_movimiento, $det->descripcion,$det->nombre_marca,$det->modelo,$det->color,
          $det->serie,$det->codigo,$det->codigo_anterior);
      }
    } else {
      $msg = array('data' => "Texto no encontrado", 'colspan' => "8");
      $this->table->add_row($msg);
    }
    if ($this->input->is_ajax_request()) {
      echo $this->table->generate() . $pagination;
    } else {
      return $this->table->generate() . $pagination;
    }
  }
}
?>
