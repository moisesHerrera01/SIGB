<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resumen_cuenta_contable extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Cuenta_contable_model'));
  }

  public function Reporte() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "7- Resumen Cuenta Contable";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';

      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('#', 'Nombre de cuenta', 'Numero de Cuenta', 'Precio de Adquisicion', 'Depreciacion ' . date('Y'),
                    'Depreciacion acumulada', 'Valor en Libros');
      $num = '10';
      $registros = $this->Cuenta_contable_model->resumenCuentaContable($num, $this->uri->segment(5));
      $total = $this->Cuenta_contable_model->totalCuentas();
      $pagination = paginacion('index.php/ActivoFijo/Reportes/Resumen_cuenta_contable/Reporte/'
                  ,$total, $num, '5');

      if (!($registros == FALSE)) {
        foreach($registros as $cuenta) {
          $this->table->add_row($cuenta['id_cuenta_contable'], $cuenta['nombre_cuenta'], $cuenta['numero_cuenta'], $cuenta['precio'],
                        $cuenta['dep_anual'], $cuenta['dep_acum'], $cuenta['valor_libro']);
        }
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "7");
        $this->table->add_row($msg);
      }

      $table =  "<div class='content_table '>" .
                "<div class='limit-content-title'><span class='icono icon-table icon-title'>Resumen Cuentas Contables</span></div>".
                "<div class='limit-content'>" .
                "<div class='exportar'>
                <a href='".base_url('/index.php/ActivoFijo/Reportes/Resumen_cuenta_contable/ReporteExcel/')."' class='icono icon-file-excel'>
                Exportar Excel</a>&nbsp;
                <a href='".base_url('/index.php/ActivoFijo/Reportes/Resumen_cuenta_contable/ImprimirReporte/')."' class='icono icon-printer' target='_blank'>
                Imprimir</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";

      $data['body'] = $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 4))) .
                     "<div style='text-align:center'>" .
                       "<div class='form-group'>" .
                          "<h3><font color=black>7- Resumen Cuenta Contable</font></h3>" .
                       "</div>" .
                     "</div>" .
                     $table;
      $this->load->view('base', $data);

    } else {
      redirect('login/index/forbidden');
    }
  }

  public function ImprimirReporte() {
    $USER = $this->session->userdata('logged_in');
    if ($USER) {
      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('#', 'Nombre de cuenta', 'Numero de Cuenta', 'Precio de Adquisicion', 'Depreciacion ' . date('Y'),
                    'Depreciacion acumulada', 'Valor en Libros');

      $total = $this->Cuenta_contable_model->totalCuentas();
      $registros = $this->Cuenta_contable_model->resumenCuentaContable($total, 0);

      if (!($registros == FALSE)) {
        foreach($registros as $cuenta) {
          $this->table->add_row($cuenta['id_cuenta_contable'], $cuenta['nombre_cuenta'], $cuenta['numero_cuenta'], $cuenta['precio'],
                        $cuenta['dep_anual'], $cuenta['dep_acum'], $cuenta['valor_libro']);
        }
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "7");
        $this->table->add_row($msg);
      }

      $data = array(
        'table' => $this->table->generate(),
        'title' => 'Bienes Usuario'
      );
      $this->load->view('ActivoFijo/Reportes/imprimir_reporte_view', $data);
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
                   ->setTitle("Resumen Cuenta Contable.")
                   ->setSubject("Resumen Cuenta Contable.")
                   ->setDescription("Resumen Cuenta Contable. ")
                   ->setKeywords("office PHPExcel php")
                   ->setCategory("Resumen Cuenta Contable.");

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', '#')
                   ->setCellValue('B1', 'Nombre de cuenta')
                   ->setCellValue('C1', 'Numero de Cuenta')
                   ->setCellValue('D1', 'Precio de Adquisicion')
                   ->setCellValue('E1', 'Depreciacion ' . date('Y'))
                   ->setCellValue('F1', 'Depreciacion acumulada')
                   ->setCellValue('G1', 'Valor en Libros');

      $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($estilo_titulo);

      $total = $this->Cuenta_contable_model->totalCuentas();
      $registros = $this->Cuenta_contable_model->resumenCuentaContable($total, 0);
      if (!($registros == FALSE)) {
        $i = 2;
        foreach($registros as $cuenta) {
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, $cuenta['id_cuenta_contable'])
                      ->setCellValue('B'.$i, $cuenta['nombre_cuenta'])
                      ->setCellValue('C'.$i, $cuenta['numero_cuenta'])
                      ->setCellValue('D'.$i, $cuenta['precio'])
                      ->setCellValue('E'.$i, $cuenta['dep_anual'])
                      ->setCellValue('F'.$i, $cuenta['dep_acum'])
                      ->setCellValue('G'.$i, $cuenta['valor_libro']);

          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estilo_contenido);
          $i++;
        }

        foreach(range('A','G') as $columnID){
          $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename='resumen_cuenta_contable.xlsx'");
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
