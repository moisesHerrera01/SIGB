<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_bienes_sin_movimiento extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Detalle_movimiento_model'));
  }

  public function RecibirDatos() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('fechaMin')!=NULL && $this->input->post('fechaMax')!=NULL) {
          redirect('ActivoFijo/Reportes/Reporte_bienes_sin_movimiento/Reporte/'.$this->input->post('fechaMin').'/'.$this->input->post('fechaMax'));
        } else {
          redirect('ActivoFijo/Reportes/Reporte_bienes_sin_movimiento/Reporte/');
      }
    } else {
      redirect('login');
    }
  }

  public function reporte(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "15-Reporte bienes sin movimiento";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';
      if ($this->uri->segment(5) != NULL && $this->uri->segment(6) != NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Descripcion','Marca','Modelo','Serie/Chasis','Codigo','Codigo Anterior');

        $num = '10';
        $registros = $this->Detalle_movimiento_model->bienes_sin_movimiento($this->uri->segment(6), $this->uri->segment(5),
                            $num, $this->uri->segment(7));

        $total = $this->Detalle_movimiento_model->total_bienes_sin_movimiento($this->uri->segment(6), $this->uri->segment(5));

        $pagination = paginacion('index.php/ActivoFijo/Reportes/Reporte_bienes_sin_movimiento/reporte/'.$this->uri->segment(5).'/'.$this->uri->segment(6),
                            $total, $num, '7');

        if (!($registros == FALSE)) {
          foreach($registros as $bien) {
            $this->table->add_row($bien->id_bien, $bien->descripcion, $bien->nombre_marca, $bien->modelo, $bien->serie, $bien->codigo, $bien->codigo_anterior);
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "7");
          $this->table->add_row($msg);
        }
        $table = "<div class='content_table'>".
                "<div class='limit-content-title'><span class='icono icon-table icon-title'> </span></div>".
                "<div class='limit-content'>" . "<div class='exportar'><a href='".
                base_url('/index.php/ActivoFijo/Reportes/Reporte_bienes_sin_movimiento/ReporteExcel/'.$this->uri->segment(5).'/'.$this->uri->segment(6))."' class='icono icon-file-excel'>
                Exportar Excel</a> &nbsp;
                <a href='".base_url('/index.php/ActivoFijo/Reportes/Reporte_bienes_sin_movimiento/ImprimirReporte/'.$this->uri->segment(5).'/'.$this->uri->segment(6))."' class='icono icon-printer' target='_blank'>
                Imprimir</a>
                </div>". "<div class='table-responsive'>" . $this->table->generate() . "</div>". $pagination ."</div>";
      }
      $data['body'] = $this->load->view('ActivoFijo/Reportes/reporte_bienes_sin_movimiento_view', '',TRUE) . "<br>" . $table;
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
        $this->table->set_heading('#','Descripcion','Marca','Modelo','Serie/Chasis','Codigo','Codigo Anterior');

        $num = $this->Detalle_movimiento_model->total_bienes_sin_movimiento($this->uri->segment(6), $this->uri->segment(5));
        $registros = $this->Detalle_movimiento_model->bienes_sin_movimiento($this->uri->segment(6), $this->uri->segment(5),
                            $num, $this->uri->segment(7));

        if (!($registros == FALSE)) {
          foreach($registros as $bien) {
            $this->table->add_row($bien->id_bien, $bien->descripcion, $bien->nombre_marca, $bien->modelo, $bien->serie, $bien->codigo, $bien->codigo_anterior);
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "7");
          $this->table->add_row($msg);
        }

        $data = array(
          'table' => $this->table->generate(),
          'title' => '15-Reporte bienes sin movimientos'
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
                   ->setTitle("Reporte bienes sin movimiento.")
                   ->setSubject("Reporte bienes sin movimiento.")
                   ->setDescription("Reporte bienes sin movimiento.")
                   ->setKeywords("Reporte bienes sin movimiento")
                   ->setCategory("Reporte bienes sin movimiento.");

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', 'Id bien')
                   ->setCellValue('B1', 'Descipción')
                   ->setCellValue('C1', 'Marca')
                   ->setCellValue('D1', 'Modelo')
                   ->setCellValue('E1', 'Serie/Chasis')
                   ->setCellValue('F1', 'Código')
                   ->setCellValue('G1', 'Código anterior');
      $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($estilo_titulo);

      $total = $this->Detalle_movimiento_model->total_bienes_sin_movimiento($this->uri->segment(6), $this->uri->segment(5));
      $registros = $this->Detalle_movimiento_model->bienes_sin_movimiento($this->uri->segment(6), $this->uri->segment(5), $total, 1);

      if (!($registros == FALSE)) {
        $i = 2;
        foreach($registros as $bien) {
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, $bien->id_bien)
                      ->setCellValue('B'.$i, $bien->descripcion)
                      ->setCellValue('C'.$i, $bien->nombre_marca)
                      ->setCellValue('D'.$i, $bien->modelo)
                      ->setCellValue('E'.$i, $bien->serie)
                      ->setCellValue('F'.$i, $bien->codigo)
                      ->setCellValue('G'.$i, $bien->codigo_anterior);

          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estilo_contenido);
          $i++;
        }

        foreach(range('A','N') as $columnID){
          $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename='Reporte_bienes_sin_movimiento.xlsx'");
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
