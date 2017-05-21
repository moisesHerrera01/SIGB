<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categoria extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model('ActivoFijo/Categoria_model');
  }

  public function index(){

    $data['title'] = "Categorías";
    $data['js'] = "assets/js/validate/categoria.js";
    $count = $this->Categoria_model->totalCategorias()+1;

    $msg = array('alert' => $this->uri->segment(4),'count'=>$count, 'controller'=>'tipo_movimiento');

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/categoria_view',$msg, TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Categorías</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Categoria');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {

        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Nombre', 'Numero','Descripción','Modificar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Categoria_model->buscarCategorias($this->input->post('busca'));
          } else {
              $registros = $this->Categoria_model->obtenerCategoriasLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Categoria/index/', $this->Categoria_model->totalCategorias(),
                            $num, '4');
          }
        } else {
              $registros = $this->Categoria_model->obtenerCategoriasLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Categoria/index/', $this->Categoria_model->totalCategorias(),
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $cate) {
              $onClick = "llenarFormulario('categoria', ['id', 'nombre_categoria','numero_categoria','descripcion'],
              [$cate->id_categoria, '$cate->nombre_categoria','$cate->numero_categoria','$cate->descripcion'])";

              $this->table->add_row($cate->id_categoria, $cate->nombre_categoria,$cate->numero_categoria,$cate->descripcion,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>');
          }
        } else {
          $msg = array('data' => "Texto no encontrado", 'colspan' => "3");
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
        redirect('/ActivoFijo/Categoria/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Categoria');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'nombre_categoria' => $this->input->post('nombre_categoria'),
          'numero_categoria' => $this->input->post('numero_categoria'),
          'descripcion' => $this->input->post('descripcion'),
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Categoria_model->actualizarCategoria($this->input->post('id'),$data);
          redirect('/ActivoFijo/Categoria/index/update');
        } else {
          redirect('/ActivoFijo/Categoria/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Categoria_model->insertarCategoria($data);
        redirect('/ActivoFijo/Categoria/index/new');
      } else {
        redirect('/ActivoFijo/Categoria/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Categoria');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      if ($this->Categoria_model->contieneSubCategoria($id)->asociados > 0){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          redirect('/ActivoFijo/Categoria/index/no_delete');
        } else {
          redirect('/ActivoFijo/Categoria/index/forbidden');
        }
      } else {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          $this->Categoria_model->eliminarCategoria($id);
          redirect('/ActivoFijo/Categoria/index/delete');
        } else {
          redirect('/ActivoFijo/Categoria/index/forbidden');
        }
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function Autocomplete(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Categoria_model->buscarCategorias($this->input->post('autocomplete'));
      } else {
          $registros = $this->Categoria_model->obtenerCategorias();
      }
    } else {
          $registros = $this->Categoria_model->obtenerCategorias();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    } else {
      $i = 1;
      foreach ($registros as $cat) {
        echo '<div id="'.$i.'" class="suggest-element" ida="categoria'.$cat->id_categoria.'"><a id="categoria'.
        $cat->id_categoria.'" data="'.$cat->id_categoria.'"  data1="'.$cat->nombre_categoria.'" >'
        .$cat->numero_categoria.' - '.$cat->nombre_categoria.'</a></div>';
        $i++;
      }
    }
  }
}
?>
