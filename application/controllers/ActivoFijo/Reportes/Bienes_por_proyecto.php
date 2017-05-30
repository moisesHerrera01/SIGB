<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bienes_por_proyecto extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Datos_Comunes_Model'));
  }

  public function RecibirBienesProyecto() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('proyecto')!=NULL) {
          redirect('ActivoFijo/Reportes/Bienes_por_proyecto/reporte/'.$this->input->post('proyecto'));
        } else {
          redirect('ActivoFijo/Reportes/Bienes_por_proyecto/reporte/');
      }
    } else {
      redirect('login');
    }
  }

  public function reporte(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "Bienes Proyecto";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $data['js'] = 'assets/js/validate/reporte/activofijo/bproyecto.js';
      $table = '';
      if ($this->uri->segment(5) != NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Descripción', 'Modelo','Codigo','Codigo anterior','Categoría',
          'Sub Categoría','Num Doc Ampara','Fecha adquisición','Precio','Oficina','Empleado');
        $num = '10';
        $registros = $this->Datos_Comunes_Model->obtenerBienesProyecto($this->uri->segment(5),$num, $this->uri->segment(6));
        $total = $this->Datos_Comunes_Model->totalBienesProyecto($this->uri->segment(5));
        error_reporting(0);
        $pagination = paginacion('index.php/ActivoFijo/Reportes/Bienes_por_proyecto/reporte/'.$this->uri->segment(5),$total->total,$num, '6');
        if (!($registros == FALSE)) {
          $i = 1;
          foreach($registros as $pro) {
            $this->table->add_row($i,$pro->descripcion,$pro->modelo,$pro->codigo,$pro->codigo_anterior,
              $pro->nombre_categoria,$pro->nombre_subcategoria,$pro->id_doc_ampara,$pro->fecha_adquisicion,
              $pro->precio_unitario,$pro->nombre_oficina,$pro->nombre_empleado);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "12");
          $this->table->add_row($msg);
        }
        $proyecto=$this->Datos_Comunes_Model->buscarProyecto($this->uri->segment(5));
        foreach ($proyecto as $p) {
          $pro="$p->nombre_fuente";
        }
        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".$pro."</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar'><a href='".base_url('/index.php/ActivoFijo/Reportes/Bienes_por_proyecto/ReporteExcel/'.$this->uri->segment(5))."' class='icono icon-file-excel'>
                  Exportar Excel</a> &nbsp;
                  <a href='".base_url('/index.php/ActivoFijo/Reportes/Bienes_por_proyecto/ImprimirReporte/'.$this->uri->segment(5))."' class='icono icon-printer' target='_blank'>
                  Imprimir</a>
                  </div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }

      $data['body'] = $this->load->view('ActivoFijo/Reportes/Bienes_por_proyecto_view', '',TRUE) . "<br>" . $table;
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
        $this->table->set_heading('#','Descripción', 'Modelo','Codigo','Codigo anterior','Categoría',
          'Sub Categoría','Num Doc Ampara','Fecha adquisición','Precio','Sección','Empleado');

        $registros = $this->Datos_Comunes_Model->obtenerBienesProyectoExcel($this->uri->segment(5));

        if (!($registros == FALSE)) {
          $i = 1;
          foreach($registros as $pro) {
            $this->table->add_row($i,$pro->descripcion,$pro->modelo,$pro->codigo,$pro->codigo_anterior,
              $pro->nombre_categoria,$pro->nombre_subcategoria,$pro->id_doc_ampara,$pro->fecha_adquisicion,
              $pro->precio_unitario,$pro->nombre_oficina,$pro->nombre_empleado);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "12");
          $this->table->add_row($msg);
        }

        $data = array(
          'table' => $this->table->generate(),
          'title' => '3 - Bienes por Proyecto'
        );
        $this->load->view('ActivoFijo/Reportes/imprimir_reporte_view', $data);
      }
    } else {
      redirect('login');
    }
  }

  public function ReporteExcel() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $this->load->library(array('excel'));

      $estilo_titulo = array(
        'font' => array(
          'name' => 'Calibri',
          'bold' => TRUE,
          'size' => 12,
        ),
        'borders' => array(
          'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THICK
          ),
          'color' => array('rgb' => '676767'),
        ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'rotation' => 0,
          'wrap' => TRUE,
        ),
      );


      $estilo_contenido = array(
        'font' => array(
          'name' => 'Calibri',
          'bold' => FALSE,
          'size' => 11,
        ),
        'borders' => array(
          'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN
          ),
          'color' => array('rgb' => '676767'),
        ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'rotation' => 0,
          'wrap' => TRUE,
        ),
      );

      $objPHPExcel = new PHPExcel();
      $objPHPExcel->getProperties()->setCreator("SICBAF")
                   ->setLastModifiedBy("SICBAF")
                   ->setTitle("Reporte de bienes por proyecto.")
                   ->setSubject("Reporte de bienes por proyecto.")
                   ->setDescription("Reporte de bienes por proyecto. ")
                   ->setKeywords("office PHPExcel php")
                   ->setCategory("Reporte de bienes por proyecto.");

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', 'Categoría')
                   ->setCellValue('B1', 'Subcategoría')
                   ->setCellValue('C1', 'Descripción')
                   ->setCellValue('D1', 'Modelo')
                   ->setCellValue('E1', 'Num doc ampara')
                   ->setCellValue('F1', 'Fecha adquisición')
                   ->setCellValue('G1', 'Precio')
                   ->setCellValue('H1', 'Codigo')
                   ->setCellValue('I1', 'Codigo Anterior')
                   ->setCellValue('J1', 'Sección')
                   ->setCellValue('K1', 'Empleado');

      $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($estilo_titulo);

      $registros = $this->Datos_Comunes_Model->obtenerBienesProyectoExcel($this->uri->segment(5));
      if (!($registros == FALSE)) {
        $i = 2;
        foreach($registros as $pro) {
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, $pro->nombre_categoria)
                      ->setCellValue('B'.$i, $pro->nombre_subcategoria)
                      ->setCellValue('C'.$i, $pro->descripcion)
                      ->setCellValue('D'.$i, $pro->modelo)
                      ->setCellValue('E'.$i, $pro->id_doc_ampara)
                      ->setCellValue('F'.$i, $pro->fecha_adquisicion)
                      ->setCellValue('G'.$i, $pro->precio_unitario)
                      ->setCellValue('H'.$i, $pro->codigo)
                      ->setCellValue('I'.$i, $pro->codigo_anterior)
                      ->setCellValue('J'.$i, $pro->nombre_oficina)
                      ->setCellValue('K'.$i, $pro->nombre_empleado);

          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->applyFromArray($estilo_contenido);
          $i++;
        }

        foreach(range('A','K') as $columnID){
          $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename='reporte_bienes_proyecto.xlsx'");
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
      }
    } else {
      redirect('login');
    }
  }
  public function AutocompleteProyecto(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Datos_Comunes_Model->buscarProyectos($this->input->post('autocomplete'));
      } else {
          $registros = $this->Datos_Comunes_Model->obtenerProyectos();
      }
    } else {
          $registros = $this->Datos_Comunes_Model->obtenerProyectos();
    }

    if ($registros == '') {
      echo '';
    }else {
      foreach ($registros as $proyecto) {
        echo '<div class="suggest-element" ida="proyecto'.$proyecto->id_fuentes.'"><a id="proyecto'.
        $proyecto->id_fuentes.'" data="'.$proyecto->id_fuentes.'"  data1="'.$proyecto->nombre_fuente.'" >'
        .$proyecto->nombre_fuente.'</a></div>';
      }
    }
  }
}
?>
