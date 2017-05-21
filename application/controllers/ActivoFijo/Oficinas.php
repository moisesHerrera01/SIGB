<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Oficinas extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model('ActivoFijo/Oficinas_model');
  }

  public function index(){

    $data['title'] = "Oficinas";
    $data['js'] = "assets/js/validate/oficinas.js";

    $msg = array('alert' => $this->uri->segment(4), 'controller'=>'Oficinas');

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/oficinas_view', '', TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Oficinas</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Oficinas');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Almacen','Sección','Oficina', 'Editar','Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Oficinas_model->buscarOficinas($this->input->post('busca'));
          } else {
              $registros = $this->Oficinas_model->obtenerDatosLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Oficinas/index/', $this->Oficinas_model->totalOficinas(),
                            $num, '4');
          }
        } else {
              $registros = $this->Oficinas_model->obtenerDatosLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Oficinas/index/', $this->Oficinas_model->totalOficinas(),
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $of) {
              $onClick = "llenarFormulario('oficinas', ['id', 'seccion_almacen','autocomplete','nombre'],
               [$of->id_oficina,$of->id_seccion_has_almacen,'$of->nombre_seccion', '$of->nombre_oficina'])";

              $this->table->add_row($of->id_oficina,$of->nombre_almacen, $of->nombre_seccion,$of->nombre_oficina,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                              '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Oficinas/EliminarDato/'.$of->id_oficina).'></a>');
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
        redirect('/ActivoFijo/Oficinas/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Oficinas');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'id_seccion_has_almacen' => $this->input->post('seccion_almacen'),
          'nombre_oficina'=>$this->input->post('nombre')
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Oficinas_model->actualizarOficina($this->input->post('id'),$data);
          redirect('/ActivoFijo/Oficinas/index/update');
        } else {
          redirect('/ActivoFijo/Oficinas/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
        $this->Oficinas_model->insertarOficina($data);
        redirect('/ActivoFijo/Oficinas/index/new');
      } else {
        redirect('/ActivoFijo/Oficinas/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Oficinas');;
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      if ($this->Oficinas_model->contieneBien($id)->asociados>0 || $this->Oficinas_model->contieneMovimiento($id)->asociados>0){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          redirect('/ActivoFijo/Oficinas/index/no_delete');
        } else {
          redirect('/ActivoFijo/Oficinas/index/forbidden');
        }}else {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          $this->Oficinas_model->eliminarOficina($id);
          redirect('/ActivoFijo/Oficinas/index/delete');
        } else {
          redirect('/ActivoFijo/Oficinas/index/forbidden');
        }
      }
      }else {
        redirect('login/index/error_no_autenticado');
      }
  }

  public function Autocomplete(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Oficinas_model->buscarSeccionesAlmacenes($this->input->post('autocomplete'));
      } else {
          $registros = $this->Oficinas_model->obtenerSeccionesAlmacenes();
      }
    } else {
          $registros = $this->Oficinas_model->obtenerSeccionesAlmacenes();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $of) {
        echo '<div id="'.$i.'" class="suggest-element" ida="seccion_almacen'.$of->id_seccion_has_almacen.'"><a id="seccion_almacen'.
        $of->id_seccion_has_almacen.'" data="'.$of->id_seccion_has_almacen.'"  data1="'.$of->nombre_seccion.'-'.$of->nombre_almacen.'" >'
        .$of->nombre_seccion.'-'.$of->nombre_almacen.'</a></div>';
        $i++;
      }
    }
  }
}
?>
