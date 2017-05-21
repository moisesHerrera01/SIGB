<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Almacenes extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Oficinas_model','ActivoFijo/Almacenes_Model','Bodega/Solicitud_Model'));
  }

  public function index(){

    $data['title'] = "Almacenes";
    $msg = array('alert' => $this->uri->segment(4),'controller'=>'Almacenes' );

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/almacenes_view', '', TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Almacenes</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */
    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Almacenes');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {

        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Almacen','Sección', 'Editar','Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Oficinas_model->buscarSeccionesAlmacenes($this->input->post('busca'));
          } else {
              $registros = $this->Almacenes_Model->obtenerSeccionesAlmacenesLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Almacenes/index/', $this->Almacenes_Model->totalAlmacenes(),
                            $num, '4');
          }
        } else {
              $registros = $this->Almacenes_Model->obtenerSeccionesAlmacenesLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Almacenes/index/', $this->Almacenes_Model->totalAlmacenes(),
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $of) {
              $onClick = "llenarFormulario('form', ['id', 'almacen','autocomplete1','seccion','autocomplete2'],
              [$of->id_seccion_has_almacen,'$of->id_almacen','$of->nombre_almacen', '$of->id_seccion','$of->nombre_seccion'])";

              $this->table->add_row($of->id_seccion_has_almacen,$of->nombre_almacen, $of->nombre_seccion,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                              '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Almacenes/EliminarDato/'.$of->id_seccion_has_almacen).'></a>');
          }
        } else {
          $msg = array('data' => "Texto no encontrado", 'colspan' => "4");
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
        redirect('/ActivoFijo/Almacenes/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Almacenes');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'id_almacen' => $this->input->post('almacen'),
          'id_seccion'=>$this->input->post('seccion'),
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Almacenes_Model->actualizarAlmacen($this->input->post('id'),$data);
          redirect('/ActivoFijo/Almacenes/index/update');
        } else {
          redirect('/ActivoFijo/Almacenes/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Almacenes_Model->insertarAlmacen($data);
        redirect('/ActivoFijo/Almacenes/index/new');
      } else {
        redirect('/ActivoFijo/Almacenes/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Almacenes');;
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      if ($this->Almacenes_Model->contieneOficina($id)->asociados>0){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          redirect('/ActivoFijo/Almacenes/index/no_delete');
        } else {
          redirect('/ActivoFijo/Almacenes/index/forbidden');
        }}else {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          $this->Almacenes_Model->eliminarAlmacen($id);
          redirect('/ActivoFijo/Almacenes/index/delete');
        } else {
          redirect('/ActivoFijo/Almacenes/index/forbidden');
        }
      }
      }else {
        redirect('login/index/error_no_autenticado');
      }
  }

  public function AutocompleteAlmacen(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Almacenes_Model->buscarAlmacenes($this->input->post('autocomplete'));
      } else {
          $registros = $this->Almacenes_Model->obtenerAlmacenes();
      }
    } else {
          $registros = $this->Almacenes_Model->obtenerAlmacenes();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $al) {
        echo '<div id="'.$i.'" class="suggest-element" ida="almacen'.$al->id_almacen.'"><a id="almacen'.
        $al->id_almacen.'" data="'.$al->id_almacen.'"  data1="'.$al->nombre_almacen.'" >'
        .$al->nombre_almacen.'</a></div>';
        $i++;
      }
    }
  }

  public function AutocompleteSeccion(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Solicitud_Model->buscarSecciones($this->input->post('autocomplete'));
      } else {
          $registros = $this->Solicitud_Model->obtenerSecciones();
      }
    } else {
          $registros = $this->Solicitud_Model->obtenerSecciones();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $sec) {
        echo '<div id="'.$i.'" class="suggest-element" ida="seccion'.$sec->id_seccion.'"><a id="seccion'.
        $sec->id_seccion.'" data="'.$sec->id_seccion.'"  data1="'.$sec->nombre_seccion.'" >'
        .$sec->nombre_seccion.'</a></div>';
        $i++;
      }
    }
  }
}
?>
