<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Movimientos_por_oficina extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Movimiento_Model'));
  }

  public function RecibirMovimientosOficina(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('seccion')!=NULL && $this->input->post('fecha_inicio')!=NULL && $this->input->post('fecha_fin')!=NULL) {
          redirect('ActivoFijo/Reportes/Movimientos_por_oficina/Reporte/'.$this->input->post('seccion').'/'.$this->input->post('fecha_inicio').'/'.$this->input->post('fecha_fin'));
        } else {
          redirect('ActivoFijo/Reportes/Movimientos_por_oficina/Reporte/');
      }
    } else {
      redirect('login');
    }
	}

  public function Reporte() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "Movimientos Por Oficina";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';

      if ($this->uri->segment(5) != '' && $this->uri->segment(6) != '' && $this->uri->segment(7) != '') {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Cantidad','Tipo de Movimiento');
        $num = '10';
        $registros = $this->Movimiento_Model->obtenerMovimientosOficina($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7), $num, $this->uri->segment(8));
        $total = $this->Movimiento_Model->totalMovimientosOficina($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7));
        $pagination = paginacion('index.php/ActivoFijo/Reportes/Movimientos_por_oficina/Reporte/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7)
                    ,$total, $num, '8');

        if (!($registros == FALSE)) {
          foreach($registros as $tip_mov => $cant) {
            $this->table->add_row($cant, $tip_mov);
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "2");
          $this->table->add_row($msg);
        }

        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".$this->uri->segment(6).' - '.$this->uri->segment(7)."</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar'>
                  <a href='".base_url('/index.php/ActivoFijo/Reportes/Movimientos_por_oficina/ReporteExcel/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>
                  Exportar Excel</a> &nbsp;
                  <a href='".base_url('/index.php/ActivoFijo/Reportes/Movimientos_por_oficina/ImprimirReporte/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-printer' target='_blank'>
                  Imprimir</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";

      }

      $data['body'] = $this->load->view('ActivoFijo/Reportes/movimientos_por_oficina_view', '',TRUE) . "<br>" . $table;
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
        $this->table->set_heading('Cantidad','Tipo de Movimiento');

        $registros = $this->Movimiento_Model->obtenerMovimientosOficina($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7));

        if (!($registros == FALSE)) {
          foreach($registros as $tip_mov => $cant) {
            $this->table->add_row($cant, $tip_mov);
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "2");
          $this->table->add_row($msg);
        }

        $data = array(
          'table' => $this->table->generate(),
          'title' => '6-Movimientos Por Oficina'
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
                   ->setTitle("Reporte de Movimientos por Oficina.")
                   ->setSubject("Reporte de Movimientos por Oficina.")
                   ->setDescription("Reporte de Movimientos por Oficina. ")
                   ->setKeywords("office PHPExcel php")
                   ->setCategory("Reporte de Movimientos por Oficina.");

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', 'Cantidad')
                   ->setCellValue('B1', 'Tipo de Movimeinto');

      $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->applyFromArray($estilo_titulo);

      $registros = $this->Movimiento_Model->obtenerMovimientosOficina($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7));
      if (!($registros == FALSE)) {
        $i = 2;
        foreach($registros as $tip_mov => $cant) {
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, $tip_mov)
                      ->setCellValue('B'.$i, $cant);

          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->applyFromArray($estilo_contenido);
          $i++;
        }

        foreach(range('A','B') as $columnID){
          $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename='reporte_movimiento_por_oficina.xlsx'");
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
      }
    } else {
      redirect('login');
    }
  }

}
?>
