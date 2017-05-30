<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_ingreso extends CI_Controller {

  public function __construct() {
    parent::__construct();

   if($this->session->userdata('logged_in') == FALSE){
     redirect('login/index/error_no_autenticado');
   }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Reporte_Ingreso_Model'));
    $this->load->model(array('ActivoFijo/Datos_Comunes_Model'));
  }

  public function Recibirfechas(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('fecha_inicio')!=NULL && $this->input->post('fecha_fin')!=NULL) {
        redirect('ActivoFijo/Reportes/Reporte_ingreso/Reporte/'.$this->input->post('fecha_inicio').'/'.$this->input->post('fecha_fin'));
      } else {
        redirect('ActivoFijo/Reportes/Reporte_ingreso/Reporte/');
      }
    } else {
      redirect('login');
    }
  }


  public function reporte(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "Reporte de Ingresos Adquiridos";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';

      if ($this->uri->segment(5) != '' && $this->uri->segment(6) != '') {
        $template = array(
        'table_open' => '<table class="table table-striped table-bordered">'
        );

        $this->table->set_template($template);
        $this->table->set_heading('Descripción','Marca','Modelo','Serie','Código', 'CódigoAnterior',
          'CuentaContable','NombreDocumentoAmpara','FechaDeAdquisición','Precio','SubCategoría',
          'Categoria','Proyecto', 'Sección', 'Empleado');

        $num = '10';
        $registros = $this->Reporte_Ingreso_Model->obtenerIngresosFiltro($this->uri->segment(5),
        $this->uri->segment(6),$num, $this->uri->segment(7));
        $total = $this->Reporte_Ingreso_Model->obtenerIngresosFiltroTotal($this->uri->segment(5),$this->uri->segment(6));
        $pagination = paginacion('index.php/ActivoFijo/Reportes/Reporte_ingreso/reporte/'.$this->uri->segment(5).
        '/'.$this->uri->segment(6),$total,$num, '7');


        if (!($registros == FALSE)) {
          $fecha_inicio=$this->uri->segment(5);
          $fecha_fin=$this->uri->segment(6);
          $i = 1;
          foreach($registros as $pro) {
            $this->table->add_row($pro->descripcion,$pro->nombre_marca,$pro->modelo,$pro->serie,$pro->codigo,
            $pro->codigo_anterior,$pro->nombre_cuenta,$pro->nombre_doc_ampara,$pro->fecha_adquisicion,
            $pro->precio_unitario,$pro->nombre_subcategoria,$pro->nombre_categoria,$pro->nombre_fuente,
            $pro->nombre_seccion,$pro->primer_nombre.' '.$pro->segundo_nombre.' '.$pro->primer_apellido.' '.$pro->segundo_apellido);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "15");
          $this->table->add_row($msg);
        }
        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ". $this->uri->segment(5) . " - " . $this->uri->segment(6) ."</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar'><a href='".base_url('/index.php/ActivoFijo/Reportes/Reporte_ingreso/ImprimirReporte/'.$this->uri->segment(5).'/'.$this->uri->segment(6))."' class='icono icon-printer' target='_blank'>
                  Imprimir</a></div>".
                  "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }
      $data['body'] = $this->load->view('ActivoFijo/Reportes/Reporte_de_ingreso_view', '',TRUE) . "<br>" . $table;
      $this->load->view('base', $data);
    } else {
      redirect('login/index/forbidden');
    }
  }

  public function ImprimirReporte() {
    $USER = $this->session->userdata('logged_in');
    if ($USER) {
      if ($this->uri->segment(5) != NULL && $this->uri->segment(6) != NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Descripción','Marca','Modelo','Serie','Código', 'CódigoAnterior',
          'CuentaContable','NombreDocumentoAmpara','FechaDeAdquisición','Precio','SubCategoría',
          'Categoria','Proyecto', 'Sección', 'Empleado');

        $total = $this->Reporte_Ingreso_Model->obtenerIngresosFiltroTotal($this->uri->segment(5),$this->uri->segment(6));
        $registros = $this->Reporte_Ingreso_Model->obtenerIngresosFiltro($this->uri->segment(5),
            $this->uri->segment(6),$total, $this->uri->segment(7));

        if (!($registros == FALSE)) {
          $fecha_inicio=$this->uri->segment(5);
          $fecha_fin=$this->uri->segment(6);
          $i = 1;
          foreach($registros as $pro) {
            $this->table->add_row($pro->descripcion,$pro->nombre_marca,$pro->modelo,$pro->serie,$pro->codigo,
            $pro->codigo_anterior,$pro->nombre_cuenta,$pro->nombre_doc_ampara,$pro->fecha_adquisicion,
            $pro->precio_unitario,$pro->nombre_subcategoria,$pro->nombre_categoria,$pro->nombre_fuente,
            $pro->nombre_seccion,$pro->primer_nombre.' '.$pro->segundo_nombre.' '.$pro->primer_apellido.' '.$pro->segundo_apellido);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "15");
          $this->table->add_row($msg);
        }

        $data = array(
          'table' => $this->table->generate(),
          'title' => 'Bienes Usuario'
        );
        $this->load->view('ActivoFijo/Reportes/imprimir_reporte_view', $data);
      }
    } else {
      redirect('login');
    }
  }
}
?>
