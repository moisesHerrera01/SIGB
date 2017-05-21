<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proyecto extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model('ActivoFijo/proyecto_model');
  }

  public function index(){
    $data['title'] = "Registro de Proyectos";
    $data['js'] = "assets/js/validate/fdf.js";

    $msg = array('alert' => $this->uri->segment(4), );

    $data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/proyecto_view',$msg,TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Proyectos</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
     }

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */
    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Proyecto');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Nombre Proyecto', 'Número Proyecto','Descripción','Modificar', 'Eliminar');

        /*
        * Filtro a la BD
        */
        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->proyecto_model->buscarProyectos($this->input->post('busca'));
          } else {
              $registros = $this->proyecto_model->obtenerProyectosLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/proyecto/index/', $this->proyecto_model->totalProyectos(),
                            $num, '4');
          }
        } else {
            $registros = $this->proyecto_model->obtenerProyectosLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/ActivoFijo/proyecto/index/', $this->proyecto_model->totalProyectos(),
                          $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $fdf) {
              $onClick = "llenarFormulario('proyecto', ['id', 'nombre_proyecto', 'numero_proyecto','descripcion'], [$fdf->id_proyecto, '$fdf->nombre_proyecto' , '$fdf->numero_proyecto','$fdf->descripcion'])";
              $this->table->add_row($fdf->id_proyecto, $fdf->nombre_proyecto, $fdf->numero_proyecto, $fdf->descripcion,
                              //form_button($btn_act), $form_el,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                              '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/proyecto/EliminarDato/'.$fdf->id_proyecto).'></a>');
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
        redirect('/ActivoFijo/proyecto/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Proyecto');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'nombre_proyecto' => $this->input->post('nombre_proyecto'),
          'numero_proyecto' => $this->input->post('numero_proyecto'),
          'descripcion' => $this->input->post('descripcion'),
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->proyecto_model->actualizarProyecto($this->input->post('id'),$data);
          redirect('/ActivoFijo/proyecto/index/update');
        } else {
          redirect('/ActivoFijo/proyecto/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
        $this->proyecto_model->insertarProyecto($data);
        redirect('/ActivoFijo/proyecto/index/new');
      } else {
        redirect('/ActivoFijo/proyecto/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Proyecto');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
        $id = $this->uri->segment(4);
        $this->proyecto_model->eliminarProyecto($id);
        redirect('/ActivoFijo/proyecto/index/delete');
      } else {
        redirect('/ActivoFijo/proyecto/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }
//Metodo autocomplete para la selección del proyecto
  public function Autocomplete(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->proyecto_model->buscarProyectos($this->input->post('autocomplete'));
      } else {
          $registros = $this->proyecto_model->obtenerProyectos();
      }
    } else {
          $registros = $this->proyecto_model->obtenerProyectos();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $fuente) {
        echo '<div id="'.$i.'" class="suggest-element" ida="fuente'.$fuente->id_proyecto.'"><a id="fuente'.
        $fuente->id_proyecto.'" data="'.$fuente->id_proyecto.'"  data1="'.$fuente->nombre_proyecto.'" >'
        .$fuente->nombre_proyecto.'</a></div>';
        $i++;
      }
    }
  }
}
?>
