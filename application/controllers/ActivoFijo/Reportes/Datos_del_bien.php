<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Datos_del_bien extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Datos_Comunes_Model'));
  }

  public function RecibirDatosBien() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('bien')!=NULL) {
          redirect('ActivoFijo/Reportes/Datos_del_bien/reporte/'.$this->input->post('bien'));
        } else {
          redirect('ActivoFijo/Reportes/Datos_del_bien/reporte/');
      }
    } else {
      redirect('login');
    }
  }

  public function reporte(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "4-Datos del bien";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';
      if ($this->uri->segment(5) != NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Dato Común','Valor','#','Dato especifico','Valor');
        $registros = $this->Datos_Comunes_Model->obtenerBien($this->uri->segment(5));

        if (!($registros == FALSE)) {
            $this->table->add_row(1,'<strong>Categoría',$registros->nombre_categoria,1,'<strong>Serie/Chasis',$registros->serie);
            $this->table->add_row(2,'<strong>Sub Categoría',$registros->nombre_subcategoria,2,'<strong>Número de motor',$registros->numero_motor);
            $this->table->add_row(3,'<strong>Marca',$registros->nombre_marca,3,'<strong>Número de placa',$registros->numero_placa);
            $this->table->add_row(4,'<strong>Descripción',$registros->descripcion,4,'<strong>Matricula',$registros->matricula);
            $this->table->add_row(5,'<strong>Modelo',$registros->modelo,5,'<strong>Condición',$registros->nombre_condicion_bien);
            $this->table->add_row(6,'<strong>Color',$registros->color,6,'<strong>Código',$registros->codigo);
            $this->table->add_row(7,'<strong>Doc que ampara',$registros->nombre_doc_ampara,7,'<strong>Código anterior',$registros->codigo_anterior);
            $this->table->add_row(8,'<strong>Número Doc que ampara',$registros->numero_doc,8,'<strong>Oficina Asignación',$registros->nombre_oficina);
            $this->table->add_row(9,'<strong>Fecha de adquisición',$registros->fecha_adquisicion,9,'<strong>Empleado Asignación',
            $registros->primer_nombre.' '.$registros->segundo_nombre.' '.$registros->primer_apellido.' '.$registros->segundo_apellido);
            $this->table->add_row(10,'<strong>Precio Unitario',$registros->precio_unitario,'','','');
            $this->table->add_row(11,'<strong>Proveedor',$registros->nombre_proveedor,'','','');
            $this->table->add_row(12,'<strong>Proyecto',$registros->nombre_fuente,'','','');
            $this->table->add_row(13,'<strong>Garantía en meses',$registros->garantia_mes,'','','');
            $this->table->add_row(14,'<strong>Cuenta Contable',$registros->nombre_cuenta,'','','');
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "6");
          $this->table->add_row($msg);
        }
        $table = "<div class='content_table'>".
                "<div class='limit-content-title'><span class='icono icon-table icon-title'> ". $registros->descripcion."</span></div>".
                "<div class='limit-content'>" . "<div class='exportar'><a href='".
                base_url('/index.php/ActivoFijo/Reportes/Historial_movimientos/index/'.$this->uri->segment(5))."' class='icono icon-share'>
                Historial de movimientos</a> &nbsp;
                <a href='".base_url('/index.php/ActivoFijo/Reportes/Datos_del_bien/ImprimirReporte/'.$this->uri->segment(5))."' class='icono icon-printer' target='_blank'>
                Imprimir</a>
                </div>". "<div class='table-responsive'>" . $this->table->generate() . "</div></div><div>";
      }
      $data['body'] = $this->load->view('ActivoFijo/Reportes/Datos_del_bien_view', '',TRUE) . "<br>" . $table;
      $this->load->view('base', $data);
    } else {
      redirect('login/index/forbidden');
    }
  }

  public function ImprimirReporte() {
    $USER = $this->session->userdata('logged_in');
    if ($USER) {
      if ($this->uri->segment(5) != NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Dato Común','Valor','#','Dato especifico','Valor');

        $registros = $this->Datos_Comunes_Model->obtenerBien($this->uri->segment(5));

        if (!($registros == FALSE)) {
            $this->table->add_row(1,'<strong>Categoría',$registros->nombre_categoria,1,'<strong>Serie/Chasis',$registros->serie);
            $this->table->add_row(2,'<strong>Sub Categoría',$registros->nombre_subcategoria,2,'<strong>Número de motor',$registros->numero_motor);
            $this->table->add_row(3,'<strong>Marca',$registros->nombre_marca,3,'<strong>Número de placa',$registros->numero_placa);
            $this->table->add_row(4,'<strong>Descripción',$registros->descripcion,4,'<strong>Matricula',$registros->matricula);
            $this->table->add_row(5,'<strong>Modelo',$registros->modelo,5,'<strong>Condición',$registros->nombre_condicion_bien);
            $this->table->add_row(6,'<strong>Color',$registros->color,6,'<strong>Código',$registros->codigo);
            $this->table->add_row(7,'<strong>Doc que ampara',$registros->nombre_doc_ampara,7,'<strong>Código anterior',$registros->codigo_anterior);
            $this->table->add_row(8,'<strong>Número Doc que ampara',$registros->numero_doc,8,'<strong>Oficina Asignación',$registros->nombre_oficina);
            $this->table->add_row(9,'<strong>Fecha de adquisición',$registros->fecha_adquisicion,9,'<strong>Empleado Asignación',
            $registros->primer_nombre.' '.$registros->segundo_nombre.' '.$registros->primer_apellido.' '.$registros->segundo_apellido);
            $this->table->add_row(10,'<strong>Precio Unitario',$registros->precio_unitario,'','','');
            $this->table->add_row(11,'<strong>Proveedor',$registros->nombre_proveedor,'','','');
            $this->table->add_row(12,'<strong>Proyecto',$registros->nombre_fuente,'','','');
            $this->table->add_row(13,'<strong>Garantía en meses',$registros->garantia_mes,'','','');
            $this->table->add_row(14,'<strong>Cuenta Contable',$registros->nombre_cuenta,'','','');
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "6");
          $this->table->add_row($msg);
        }

        $data = array(
          'table' => $this->table->generate(),
          'title' => '4-Datos del bien'
        );
        $this->load->view('ActivoFijo/Reportes/imprimir_reporte_view', $data);
      }
    } else {
      redirect('login');
    }
  }

  public function AutocompleteBien(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Datos_Comunes_Model->buscarBienesAutocomplete($this->input->post('autocomplete'));
      } else {
          $registros = $this->Datos_Comunes_Model->obtenerBienesAutocomplete();
      }
    } else {
          $registros = $this->Datos_Comunes_Model->obtenerBienesAutocomplete();
    }

    if ($registros == '') {
      echo '';
    }else {
      foreach ($registros as $bien) {
        echo '<div class="suggest-element" ida="bien'.$bien->id_bien.'"><a id="bien'.
        $bien->id_bien.'" data="'.$bien->id_bien.'"  data1="'.$bien->descripcion.' '.$bien->codigo.' '.$bien->serie.'" >'
        .$bien->descripcion.' '.$bien->codigo.' '.$bien->serie.'</a></div>';
      }
    }
  }
}
?>
