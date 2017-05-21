<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cuenta_Contable extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model('ActivoFijo/Cuenta_contable_model');
  }

  public function index(){
    $USER = $this->session->userdata('logged_in');
    $data['title'] = "Cuentas Contables";
    $data['js'] = "assets/js/validate/cuenta_contable.js";
    $msg = array('alert' => $this->uri->segment(4),'controller'=>'Cuenta_Contable');

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/cuenta_contable_view', '', TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Cuentas Contables</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'), $this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */
    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Cuenta_Contable');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Nombre', 'Numero','Porcentaje depreciación','Vida util años','Modificar','Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Cuenta_contable_model->buscarCuentas($this->input->post('busca'));
          } else {
              $registros = $this->Cuenta_contable_model->obtenerCuentasLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Cuenta_Contable/index/', $this->Cuenta_contable_model->totalCuentas(),
                            $num, '4');
          }
        } else {
              $registros = $this->Cuenta_contable_model->obtenerCuentasLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Cuenta_Contable/index/', $this->Cuenta_contable_model->totalCuentas(),
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $cuent) {
              $onClick = "llenarFormulario('cuentas', ['id', 'nombre_cuenta','numero_cuenta','porcentaje_depreciacion','vida_util'],
              [$cuent->id_cuenta_contable, '$cuent->nombre_cuenta','$cuent->numero_cuenta','$cuent->porcentaje_depreciacion','$cuent->vida_util'])";

              $this->table->add_row($cuent->id_cuenta_contable, $cuent->nombre_cuenta,$cuent->numero_cuenta,$cuent->porcentaje_depreciacion*100,$cuent->vida_util,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                              '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Cuenta_Contable/EliminarDato/'.$cuent->id_cuenta_contable).'></a>');
          }
        } else {
          $msg = array('data' => "Texto no encontrado", 'colspan' => "7");
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
        redirect('/ActivoFijo/Cuenta_Contable/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
      $USER = $this->session->userdata('logged_in');
      $modulo=$this->User_model->obtenerModulo('ActivoFijo/Cuenta_Contable');
      if($USER){
        $data = array(
          'nombre_cuenta' => $this->input->post('nombre_cuenta'),
          'numero_cuenta' => $this->input->post('numero_cuenta'),
          'porcentaje_depreciacion' => $this->input->post('porcentaje_depreciacion'),
          'vida_util' => $this->input->post('vida_util'),
        );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Cuenta_contable_model->actualizarCuenta($this->input->post('id'),$data);
          redirect('/ActivoFijo/Cuenta_Contable/index/update');
        } else {
          redirect('/ActivoFijo/Cuenta_Contable/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Cuenta_contable_model->insertarCuenta($data);
        redirect('/ActivoFijo/Cuenta_Contable/index/new');
      } else {
        redirect('/ActivoFijo/Cuenta_Contable/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Cuenta_Contable');;
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      if ($this->Cuenta_contable_model->contieneDatoComun($id)->asociados>0){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          redirect('/ActivoFijo/Cuenta_Contable/index/no_delete');
        } else {
          redirect('/ActivoFijo/Cuenta_Contable/index/forbidden');
        }}else {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          $this->Cuenta_contable_model->eliminarCuenta($id);
          redirect('/ActivoFijo/Cuenta_Contable/index/delete');
        } else {
          redirect('/ActivoFijo/Cuenta_Contable/index/forbidden');
        }
      }
      }else {
        redirect('login/index/error_no_autenticado');
      }
  }
}
?>
