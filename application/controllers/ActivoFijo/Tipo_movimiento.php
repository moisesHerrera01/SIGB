<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tipo_movimiento extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model('ActivoFijo/Tipo_movimiento_model');
  }

  public function index(){
    $data['title'] = "Tipos Movimientos";
    $data['js'] = "assets/js/validate/tipo_mov.js";

    $msg = array('alert' => $this->uri->segment(4),'controller'=>'tipo_movimiento' );

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/tipo_movimiento_view', '', TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Tipos de movimiento</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);

	}

  public function mostrarTabla(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Tipo_movimiento');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {

        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Nombre Movimiento','Modificar', 'Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '15';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Tipo_movimiento_model->buscarMovimientos($this->input->post('busca'));
          } else {
              $registros = $this->Tipo_movimiento_model->obtenerMovimientosLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Tipo_movimiento/index/', $this->Tipo_movimiento_model->totalMovimientos(),
                            $num, '4');
          }
        } else {
              $registros = $this->Tipo_movimiento_model->obtenerMovimientosLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Tipo_movimiento/index/', $this->Tipo_movimiento_model->totalMovimientos(),
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $unidad) {
              $onClick = "llenarFormulario('movimiento_model', ['id', 'nombre_movimiento'], [$unidad->id_tipo_movimiento, '$unidad->nombre_movimiento'])";

              $this->table->add_row($unidad->id_tipo_movimiento, $unidad->nombre_movimiento,
                              //form_button($btn_act), $form_el,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                              '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Tipo_movimiento/EliminarDato/'.$unidad->id_tipo_movimiento).'></a>');
          }
        } else {
          $msg = array('data' => "Texto no encontrado", 'colspan' => "6");
          $this->table->add_row($msg);
        }

        /*
        * vuelve a verificar para mostrar los datos
        */
        if ($this->input->is_ajax_request()) {
          echo $this->table->generate() . $pagination;
        } else {
          return $this->table->generate() . $pagination;
        }
      } else {
        redirect('/ActivoFijo/Tipo_movimiento/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Tipo_movimiento');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'nombre_movimiento' => $this->input->post('nombre_movimiento')
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Tipo_movimiento_model->actualizarMovimiento($this->input->post('id'),$data);
          redirect('/ActivoFijo/Tipo_movimiento/index/update');
        } else {
          redirect('/ActivoFijo/Tipo_movimiento/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
        $this->Tipo_movimiento_model->insertarMovimiento($data);
        redirect('/ActivoFijo/Tipo_movimiento/index/new');
      } else {
        redirect('/ActivoFijo/Tipo_movimiento/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Tipo_movimiento');
      $USER = $this->session->userdata('logged_in');
      if($USER){
      $id = $this->uri->segment(4);
      if ($this->Tipo_movimiento_model->contieneMovimiento($id)->asociados>0){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          redirect('/ActivoFijo/Tipo_movimiento/index/no_delete');
        } else {
          redirect('/ActivoFijo/Tipo_movimiento/index/forbidden');
        }
      }
      else {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          $this->Tipo_movimiento_model->eliminarMovimiento($id);
          redirect('/ActivoFijo/Tipo_movimiento/index/delete');
        } else {
          redirect('/ActivoFijo/Tipo_movimiento/index/forbidden');
        }
      }
      }else {
        redirect('login/index/error_no_autenticado');
      }
  }
}
?>
