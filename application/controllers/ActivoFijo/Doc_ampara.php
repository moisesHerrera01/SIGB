<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Doc_ampara extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model('ActivoFijo/Doc_Ampara_Model');
  }

  public function index(){

    $data['title'] = "Documentos";
    $data['js'] = "assets/js/validate/doc_ampara.js";
    $msg = array('alert' => $this->uri->segment(4), 'controller'=>'doc_ampara');

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/doc_ampara_view', '', TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Documentos</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Doc_ampara');
    /*
    * Configuracion de la tabla
    */
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Nombre', 'Editar', 'Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Doc_Ampara_Model->buscarDocumentos($this->input->post('busca'));
          } else {
              $registros = $this->Doc_Ampara_Model->obtenerDocumentosLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Doc_ampara/index/', $this->Doc_Ampara_Model->totalDocumentos(),
                            $num, '4');
          }
        } else {
              $registros = $this->Doc_Ampara_Model->obtenerDocumentosLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Doc_ampara/index/', $this->Doc_Ampara_Model->totalDocumentos(),
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $doc) {
              $onClick = "llenarFormulario('documentos', ['id', 'nombre_doc_ampara'], [$doc->id_doc_ampara, '$doc->nombre_doc_ampara'])";

              $this->table->add_row($doc->id_doc_ampara, $doc->nombre_doc_ampara,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                              '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Doc_ampara/EliminarDato/'.$doc->id_doc_ampara).'></a>');
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
        redirect('/ActivoFijo/Doc_ampara/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Doc_ampara');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'nombre_doc_ampara' => $this->input->post('nombre_doc_ampara')
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Doc_Ampara_Model->actualizarDocumento($this->input->post('id'),$data);
          redirect('/ActivoFijo/Doc_ampara/index/update');
        } else {
          redirect('/ActivoFijo/cuenta_contable/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Doc_Ampara_Model->insertarDocumento($data);
        redirect('/ActivoFijo/Doc_ampara/index/new');
      } else {
        redirect('/ActivoFijo/Doc_ampara/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Doc_ampara');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      if ($this->Doc_Ampara_Model->contieneDatoComun($id)->asociados>0){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          redirect('/ActivoFijo/Doc_ampara/index/no_delete');
        } else {
          redirect('/ActivoFijo/cuenta_contable/index/forbidden');
        }
      }
      else {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
        $this->Doc_Ampara_Model->eliminarDocumento($id);
          redirect('/ActivoFijo/Doc_ampara/index/delete');
        } else {
          redirect('/ActivoFijo/Doc_ampara/index/forbidden');
        }
      }
      }else {
        redirect('login/index/error_no_autenticado');
      }
  }
}
?>
