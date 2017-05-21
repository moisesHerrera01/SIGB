<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marcas extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model('ActivoFijo/Marcas_model');
  }

  public function index(){
    $data['title'] = "Registro de Marcas";
    $data['js'] = "assets/js/validate/marcas.js";

    $msg = array('alert' => $this->uri->segment(4),'controller'=>'marcas' );
    $data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/marcas_view',$msg,TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Registro de marcas</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
     }

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */
    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Marcas');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Nombre Marcas','Modificar', 'Eliminar');

        /*
        * Filtro a la BD
        */
        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Marcas_model->buscarMarcas($this->input->post('busca'));
          } else {
              $registros = $this->Marcas_model->obtenerMarcasLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Marcas/index/', $this->Marcas_model->totalMarcas(),
                            $num, '4');
          }
        } else {
            $registros = $this->Marcas_model->obtenerMarcasLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/ActivoFijo/Marcas/index/', $this->Marcas_model->totalMarcas(),
                          $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $marc) {
              $onClick = "llenarFormulario('marca', ['id', 'nombre_marca'],
               [$marc->id_marca, '$marc->nombre_marca'])";
              $this->table->add_row($marc->id_marca, $marc->nombre_marca,
                              //form_button($btn_act), $form_el,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                              '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Marcas/EliminarDato/'.$marc->id_marca).'></a>');
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
        redirect('/ActivoFijo/Marcas/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Marcas');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'nombre_marca' => $this->input->post('nombre_marca'),

      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Marcas_model->actualizarMarcas($this->input->post('id'),$data);
          redirect('/ActivoFijo/Marcas/index/update');
        } else {
          redirect('/ActivoFijo/Marcas/index/forbidden');
        }
      }
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Marcas_model->insertarMarcas($data);
        redirect('/ActivoFijo/Marcas/index/new');
      } else {
        redirect('/ActivoFijo/Marcas/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */

  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Marcas');;
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      if ($this->Marcas_model->contieneDatoComun($id)->asociados>0){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          redirect('/ActivoFijo/Marcas/index/no_delete');
        } else {
          redirect('/ActivoFijo/Marcas/index/forbidden');
        }}else {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          $this->Marcas_model->eliminarMarcas($id);
          redirect('/ActivoFijo/Marcas/index/delete');
        } else {
          redirect('/ActivoFijo/Marcas/index/forbidden');
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
          $registros = $this->Marcas_model->buscaMarcas($this->input->post('autocomplete'));
      } else {
          $registros = $this->Marcas_model->obtenerMarcas();
      }
    } else {
          $registros = $this->Marcas_model->obtenerMarcas();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $fuente) {
        echo '<div id="'.$i.'" class="suggest-element" ida="fuente'.$fuente->id_marca.'"><a id="fuente'.
        $fuente->id_marca.'" data="'.$fuente->id_marca.'"  data1="'.$fuente->nombre_marca.'" >'
        .$fuente->nombre_proyecto.'</a></div>';
        $i++;
      }
    }
  }
}
?>
