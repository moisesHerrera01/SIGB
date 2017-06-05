<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comparativo_fuente extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    //$this->load->model(array());
  }

  public function RecibirDato() {
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('anio')!=NULL ) {
        redirect('Estrategico/Comparativo_fuente/Reporte/'.$this->input->post('anio').'/');
    } else {
        redirect('Estrategico/Comparativo_fuente/Reporte');
    }
  }

  public function Reporte(){

    $USER = $this->session->userdata('logged_in');
    $data['title'] = "Comparativo de gastos por fuente de fondo";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $data['js'] = 'assets/js/validate/reporte/comparativo.js';
    $table = '';
    if (($this->uri->segment(4))!=NULL) {
      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $column = 1;

      $registros = FALSE;

      if (!($registros == FALSE)) {

        foreach($registros as $fuente) {

        }

      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => $column);
        $this->table->add_row($msg);
      }

      $table =  "<div class='content_table '>" .
                "<div class='limit-content-title'>".
                  "<div class='title-reporte'>".
                    "Cuadro comparativo de gastos por fuente de fondo por año.".
                  "</div>".
                  "<div class='title-header'>
                    <ul>
                      <li>Fecha emisión: ".date('d/m/Y')."</li>
                      <li>Nombre la compañia: MTPS</li>
                      <li>N° pagina: 1/1</li>
                      <li>Nombre pantalla:</li>
                      <li>Usuario: ".$USER['nombre_completo']."</li>
                      <br />
                      <li>Parametros: ".$this->uri->segment(4)."</li>
                    </ul>
                  </div>".
                "</div>".
                "<div class='limit-content'>" .
                "<div class='exportar'><a href='".base_url('/index.php/Estrategico/Comparativo_fuente/ReporteExcel/'.$this->uri->segment(4).'/')."'
                class='icono icon-file-excel'>Exportar Excel</a></div>" .
                "<div class='table-responsive'>" . $this->table->generate() . "</div></div></div>";

      $data['body'] = $table;

    } else {

      $data['body'] = $this->load->view('Estrategico/comparativo_fuente_view', array('user' =>  $this->session->userdata('logged_in')), TRUE);

    }

    $this->load->view('base', $data);
  }

  public function ReporteExcel() {

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
                 ->setTitle("Reporte de Salidas y Saldos de Bodega de productos por Objeto Especifico.")
                 ->setSubject("Reporte de Salidas y Saldos de Bodega de productos por Objeto Especifico.")
                 ->setDescription("Reporte generado para conciliaciones contables al cierre de cada mes..")
                 ->setKeywords("office PHPExcel php")
                 ->setCategory("Reporte de Salidas y Saldos de Bodega de productos por Objeto Especifico.");

    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', '#')
                 ->setCellValue('B1', 'Número Especifico')
                 ->setCellValue('C1', 'Nombre Especifico')
                 ->setCellValue('D1', 'Saldo')
                 ->setCellValue('E1', 'Salida');
    $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($estilo_titulo);

    $registros = FALSE;

    if (!($registros == FALSE)) {
      $i = 2;
      foreach($registros as $fuente) {


        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $i-1)
                    ->setCellValue('B'.$i, $salida->id_especifico);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->applyFromArray($estilo_contenido);
        $i++;
      }

      foreach(range('A','B') as $columnID){
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }

      $objPHPExcel->setActiveSheetIndex(0);

      header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header("Content-Disposition: attachment; filename='reporte_salidas_saldos.xlsx'");
      header('Cache-Control: max-age=0');

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
    }
  }

}

?>
