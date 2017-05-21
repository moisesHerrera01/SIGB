<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedor extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model('ActivoFijo/proveedor_model');
  }

  public function index(){
    $data['title'] = "Proveedores";
    $msg = array('alert' => $this->uri->segment(4), );

    $data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/proveedor_view',$msg,TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Proveedores</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
     }

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud(196, $USER['id'], 'update')) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Nombre Proveedor', 'NIT','Correo','telefono','Modificar', 'Eliminar');

        /*
        * Filtro a la BD
        */
        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->proveedor_model->buscarProveedor($this->input->post('busca'));
          } else {
              $registros = $this->proveedor_model->obtenerProveedorLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/proveedor/index/', $this->proveedor_model->totalProveedor(),
                            $num, '4');
          }
        } else {
            $registros = $this->proveedor_model->obtenerProveedorLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/ActivoFijo/proveedor/index/', $this->proveedor_model->totalProveedor(),
                          $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $prov) {
              $onClick = "llenarFormulario('proveedor', ['id', 'nombre', 'nit','correo','telefono'],
              [$prov->id_proveedor, '$prov->nombre_proveedor' , '$prov->nit_proveedor','$prov->correo_proveedor','$prov->telefono_proveedor'])";
              $this->table->add_row($prov->id_proveedor, $prov->nombre_proveedor, $prov->nit_proveedor, $prov->correo_proveedor, $prov->telefono_proveedor,
                              //form_button($btn_act), $form_el,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                              '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/proveedor/EliminarDato/></a>'));
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
        redirect('/ActivoFijo/proveedor/index/forbidden');
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
    if($USER){
      $data = array(
        'nombre_proveedor' => $this->input->post('nombre'),
        'nit_proveedor' => $this->input->post('nit'),
        'correo_proveedor' => $this->input->post('correo'),
        'telefono_proveedor' => $this->input->post('telefono')
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud(196, $USER['id'], 'update')) {
          $this->proveedor_model->actualizarProveedor($this->input->post('id'),$data);
          redirect('/ActivoFijo/proveedor/index/update');
        } else {
          redirect('/ActivoFijo/proveedor/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud(196, $USER['id'], 'update')) {
        $this->proveedor_model->insertarProveedor($data);
        redirect('/ActivoFijo/proveedor/index/new');
      } else {
        redirect('/ActivoFijo/proveedor/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud(196, $USER['id'], 'delete')) {
        $id = $this->uri->segment(4);
        $this->proveedor_model->eliminarProveedor($id);
        redirect('/ActivoFijo/proveedor/index/delete');
      } else {
        redirect('/ActivoFijo/proveedor/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function Autocomplete(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->proveedor_model->buscarProveedor($this->input->post('autocomplete'));
      } else {
          $registros = $this->proveedor_model->obtenerProveedor();
      }
    } else {
          $registros = $this->proveedor_model->obtenerProveedor();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $fuente) {
        echo '<div id="'.$i.'" class="suggest-element" ida="fuente'.$fuente->id_proveedor.'"><a id="fuente'.
        $fuente->id_proveedor.'" data="'.$fuente->id_proveedor.'"  data1="'.$fuente->nombre_proveedor.'" >'
        .$fuente->nombre_proveedor.'</a></div>';
        $i++;
      }
    }
  }
}
?>
