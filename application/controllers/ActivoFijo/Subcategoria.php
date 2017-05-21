<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subcategoria extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Subcategoria_Model','ActivoFijo/Categoria_model'));
  }

  public function index(){

    $nombre = $this->Categoria_model->obtenerCategoria($this->uri->segment(4));
    $id_cat = $this->uri->segment(4);
    $data['title'] = "Sub Categorías";
    $data['js'] = "assets/js/validate/subcategoria.js";
    $count = $this->Subcategoria_Model->totalSubcategorias($this->uri->segment(4))+1;

    $msg = array('alert' => $this->uri->segment(5),'count'=>$count,'id_cat'=>$id_cat,'nombre'=>$nombre);

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/subcategoria_view',$msg, TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Sub Categorías</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */

    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Subcategoria');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Categoria','Sub-categoria', 'Numero','Descripción','Modificar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Subcategoria_Model->buscarSubcategorias($this->input->post('busca'),$this->uri->segment(4));
          } else {
              $registros = $this->Subcategoria_Model->obtenerSubcategoriasLimit($num, $this->uri->segment(5),$this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Subcategoria/index/'.$this->uri->segment(4), $this->Subcategoria_Model->totalSubcategorias($this->uri->segment(4)),
                            $num, '5');
          }
        } else {
              $registros = $this->Subcategoria_Model->obtenerSubcategoriasLimit($num, $this->uri->segment(5),$this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Subcategoria/index/'.$this->uri->segment(4), $this->Subcategoria_Model->totalSubcategorias($this->uri->segment(4)),
                            $num, '5');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {

          foreach($registros as $sub) {
              $nombre = $this->Categoria_model->obtenerCategoria($this->uri->segment(4));
              $onClick = "llenarFormulario('subcategoria', ['id','nombre_categoria','nombre_subcategoria','numero_subcategoria','descripcion'],
              [$sub->id_subcategoria, '$nombre','$sub->nombre_subcategoria','$sub->numero_subcategoria','$sub->descripcion'])";

              $this->table->add_row($sub->id_subcategoria,$nombre,$sub->nombre_subcategoria,$sub->numero_subcategoria,$sub->descripcion,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>');
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
        redirect('/ActivoFijo/Subcategoria/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Subcategoria');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'id_categoria' => $this->input->post('id_cat'),
          'nombre_subcategoria' => $this->input->post('nombre_subcategoria'),
          'numero_subcategoria' => $this->input->post('numero_subcategoria'),
          'descripcion' => $this->input->post('descripcion'),
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Subcategoria_Model->actualizarSubcategoria($this->input->post('id'),$data);
          redirect('/ActivoFijo/Subcategoria/index/'.$data['id_categoria'].'/update');
        } else {
          redirect('/ActivoFijo/Subcategoria/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
        $this->Subcategoria_Model->insertarSubcategoria($data);
        redirect('/ActivoFijo/Subcategoria/index/'.$data['id_categoria'].'/new');
      } else {
        redirect('/ActivoFijo/Subcategoria/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Subcategoria');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
        $id = $this->uri->segment(4);
        $this->Subcategoria_Model->eliminarSubategoria($id);
        redirect('/ActivoFijo/Subcategoria/index/delete');
      } else {
        redirect('/ActivoFijo/Subcategoria/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

/*  public function Autocomplete(){
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
        .$cat->id_categoria.' - '.$cat->nombre_categoria.'</a></div>';
        $i++;
      }
    }
  }*/
}
?>
