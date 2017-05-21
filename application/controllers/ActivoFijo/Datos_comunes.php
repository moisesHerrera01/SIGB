<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Datos_comunes extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Datos_Comunes_Model','ActivoFijo/Subcategoria_Model','ActivoFijo/Tipo_movimiento_model',
    'ActivoFijo/Marcas_model','ActivoFijo/Doc_Ampara_Model', 'ActivoFijo/Cuenta_contable_model'));
  }

  public function index(){

    $data['title'] = "Datos Comunes";
    $data['js'] = "assets/js/validate/datos_comunes.js";

    $msg = array('alert' => $this->uri->segment(4),'controller'=>'Datos_comunes');

    $data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/datos_comunes_view', '', TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'>Datos Comunes</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Datos_comunes');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Descripción','Sub Categoría', 'Código Anterior','Marca','Modelo','Serie','Fecha Adquisición',
        'Precio Unitario','Editar','Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Datos_Comunes_Model->buscarDatosComunes($this->input->post('busca'));
          } else {
              $registros = $this->Datos_Comunes_Model->obtenerDatosComunesLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Datos_comunes/index/', $this->Datos_Comunes_Model->totalDatosComunes()->total,
                            $num, '4');
          }
        } else {
              $registros = $this->Datos_Comunes_Model->obtenerDatosComunesLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Datos_comunes/index/', $this->Datos_Comunes_Model->totalDatosComunes()->total,
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */
        if (!($registros == FALSE)) {
          foreach($registros as $dat) {
              $onClick = "llenarFormulario('datos', ['id', 'subcategoria','autocomplete1','marca','autocomplete3',
            'descripcion','modelo','color','doc_ampara','autocomplete4','nombre_doc','fecha','precio','proveedor',
            'autocomplete5','proyecto','autocomplete6','garantia','observacion','cuenta','autocomplete7'],
            [$dat->id_dato_comun, '$dat->id_subcategoria','$dat->nombre_subcategoria','$dat->id_marca','$dat->nombre_marca',
            '$dat->descripcion','$dat->modelo','$dat->color','$dat->id_doc_ampara','$dat->nombre_doc_ampara',
            '$dat->nombre_doc','$dat->fecha_adquisicion','$dat->precio_unitario','$dat->id_proveedores','$dat->nombre_proveedor',
            '$dat->id_fuentes','$dat->nombre_fuente','$dat->garantia_mes','$dat->observacion','$dat->id_cuenta_contable',
            '$dat->nombre_cuenta'])";

            $this->table->set_heading('Id','Descripción','Sub Categoría', 'Código Anterior','Marca','Modelo','Serie','Fecha Adquisición',
            'Precio Unitario','Editar','Eliminar');
              $this->table->add_row($dat->id_dato_comun,$dat->descripcion,$dat->nombre_subcategoria,$dat->codigo_anterior,$dat->nombre_marca,$dat->modelo,
              $dat->serie,$dat->fecha_adquisicion,$dat->precio_unitario,'<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
            '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Datos_comunes/EliminarDato/'.$dat->id_dato_comun).'></a>');
          }
        } else {
          $msg = array('data' => "Texto no encontrado", 'colspan' => "9");
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
        redirect('/ActivoFijo/Datos_comunes/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Datos_comunes');
    $USER = $this->session->userdata('logged_in');
    $id_tipo_movimiento=$this->Tipo_movimiento_model->obtenerMovimientoId('REGISTRO 1A. VEZ');
    if($USER){
        $data = array(
            'id_subcategoria' => $this->input->post('subcategoria'),
            'id_tipo_movimiento' => $id_tipo_movimiento,
            'id_marca' => $this->input->post('marca'),
            'descripcion' => $this->input->post('descripcion'),
            'modelo' => $this->input->post('modelo'),
            'color' => $this->input->post('color'),
            'id_doc_ampara' => $this->input->post('doc_ampara'),
            'nombre_doc_ampara' => $this->input->post('nombre_doc'),
            'fecha_adquisicion' => $this->input->post('fecha'),
            'precio_unitario' => $this->input->post('precio'),
            'id_proveedores' => $this->input->post('proveedor'),
            'id_fuentes' => $this->input->post('proyecto'),
            'garantia_mes' => $this->input->post('garantia'),
            'observacion' => $this->input->post('observacion'),
            'id_cuenta_contable' => $this->input->post('cuenta')
        );

        if (!($this->input->post('id') == '')){
            if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
                $this->Datos_Comunes_Model->actualizarDatosComunes($this->input->post('id'),$data);
                redirect('/ActivoFijo/Datos_comunes/index/update');
              } else {
                redirect('/ActivoFijo/Datos_comunes/index/forbidden');
              }
        }
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Datos_Comunes_Model->insertarDatosComunes($data);
        redirect('/ActivoFijo/Datos_comunes/index/new');
      } else {
        redirect('/ActivoFijo/Datos_comunes/index/forbidden');
      }
      } else {
        redirect('login/index/error_no_autenticado');
      }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Datos_comunes');;
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      if ($this->Datos_Comunes_Model->contieneBien($id)->asociados>0){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          redirect('/ActivoFijo/Datos_comunes/index/no_delete');
        } else {
          redirect('/ActivoFijo/Datos_comunes/index/forbidden');
        }}else {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          $this->Datos_Comunes_Model->eliminarDatosComunes($id);
          redirect('/ActivoFijo/Datos_comunes/index/delete');
        } else {
          redirect('/ActivoFijo/Datos_comunes/index/forbidden');
        }
      }
      }else {
        redirect('login/index/error_no_autenticado');
      }
  }

  public function AutocompleteSubcategorias(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Subcategoria_Model->buscarSubcategoriasAutocomplete($this->input->post('autocomplete'));
      } else {
          $registros = $this->Subcategoria_Model->obtenerSubcategorias();
      }
    } else {
          $registros = $this->almacenes_model->obtenerSubcategorias();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $al) {
        echo '<div id="'.$i.'" class="suggest-element" ida="subcategoria'.$al->id_subcategoria.'"><a id="subcategoria'.
        $al->id_subcategoria.'" data="'.$al->id_subcategoria.'"  data1="'.$al->nombre_subcategoria.'" >'
        .$al->nombre_subcategoria.'</a></div>';
        $i++;
      }
    }
  }

  public function AutocompleteSubcategoriasinformatico(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Subcategoria_Model->buscarSubcategoriasAutocomplete($this->input->post('autocomplete'));
      } else {
          $registros = $this->Subcategoria_Model->obtenerSubcategoriasInformatico();
      }
    } else {
          $registros = $this->Subcategoria_Model->obtenerSubcategoriasInformatico();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $al) {
        if($al->nombre_subcategoria=='PROCESADOR' || $al->nombre_subcategoria=='DISCO DURO' ||
         $al->nombre_subcategoria=='MEMORIA' || $al->nombre_subcategoria=='SISTEMA OPERATIVO' ||
         $al->nombre_subcategoria=='OFFICE' || $al->nombre_subcategoria=='IP' ||
          $al->nombre_subcategoria=='PUNTO DE RED'){
            if ($al->nombre_categoria=='SOFTWARE' || $al->nombre_categoria=='HARDWARE' ||
             $al->nombre_categoria=='RED') {
              echo '<div id="'.$i.'" class="suggest-element" ida="subcategoria'.$al->id_subcategoria.'"><a id="subcategoria'.
              $al->id_subcategoria.'" data="'.$al->id_subcategoria.'"  data1="'.$al->nombre_subcategoria.'" >'
              .$al->nombre_subcategoria.'</a></div>';
            }
        }
        $i++;
      }
    }
  }

  public function AutocompleteMovimientos(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Tipo_movimiento_model->buscarMovimientos($this->input->post('autocomplete'));
      } else {
          $registros = $this->Tipo_movimiento_model->obtenerMovimientos();
      }
    } else {
          $registros = $this->Tipo_movimiento_model->obtenerMovimientos();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $al) {
        echo '<div id="'.$i.'" class="suggest-element" ida="movimiento'.$al->id_tipo_movimiento.'"><a id="movimiento'.
        $al->id_tipo_movimiento.'" data="'.$al->id_tipo_movimiento.'"  data1="'.$al->nombre_movimiento.'" >'
        .$al->nombre_movimiento.'</a></div>';
        $i++;
      }
    }
  }

  public function AutocompleteMarcas(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Marcas_model->buscarMarcas($this->input->post('autocomplete'));
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
      foreach ($registros as $al) {
        echo '<div id="'.$i.'" class="suggest-element" ida="marcas'.$al->id_marca.'"><a id="marcas'.
        $al->id_marca.'" data="'.$al->id_marca.'"  data1="'.$al->nombre_marca.'" >'
        .$al->nombre_marca.'</a></div>';
        $i++;
      }
    }
  }

  public function AutocompleteDocumentos(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Doc_Ampara_Model->buscarDocumentos($this->input->post('autocomplete'));
      } else {
          $registros = $this->Doc_Ampara_Model->obtenerDocumentos();
      }
    } else {
          $registros = $this->Doc_Ampara_Model->obtenerDocumentos();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $al) {
        echo '<div id="'.$i.'" class="suggest-element" ida="doc_ampara'.$al->id_doc_ampara.'"><a id="doc_ampara'.
        $al->id_doc_ampara.'" data="'.$al->id_doc_ampara.'"  data1="'.$al->nombre_doc_ampara.'" >'
        .$al->nombre_doc_ampara.'</a></div>';
        $i++;
      }
    }
  }

  public function AutocompleteCuentas(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Cuenta_contable_model->buscarCuentas($this->input->post('autocomplete'));
      } else {
          $registros = $this->Cuenta_contable_model->obtenerCuentas();
      }
    } else {
          $registros = $this->Cuenta_contable_model->obtenerCuentas();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $al) {
        echo '<div id="'.$i.'" class="suggest-element" ida="cuenta'.$al->id_cuenta_contable.'"><a id="cuenta'.
        $al->id_cuenta_contable.'" data="'.$al->id_cuenta_contable.'"  data1="'.$al->nombre_cuenta.'" >'
        .$al->nombre_cuenta.'</a></div>';
        $i++;
      }
    }
  }
}
?>
