<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resumen_solicitudes extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Solicitud_Model'));
    date_default_timezone_set('America/El_Salvador');
  }

  public function Recibirfechas() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('minFecha')) {
      $USER = $this->session->userdata('logged_in');
      $modulo=$this->User_model->obtenerModulo('Estrategico/Resumen_solicitudes/reporte');
      $hora=date("H:i:s");
      $rastrea = array(
        'id_usuario' =>$USER['id'],
        'id_modulo' =>$modulo,
        'fecha' =>$fecha_actual,
        'hora' =>$hora,
        'operacion'=>'CONSULTA'
      );
      $this->User_model->insertarRastreabilidad($rastrea);
      if($this->input->post('maxFecha')==NULL){
        redirect('Estrategico/Resumen_solicitudes/reporte/'.$this->input->post('minFecha').'/'.$fecha_actual);
      }else{
        redirect('Estrategico/Resumen_solicitudes/reporte/'.$this->input->post('minFecha').'/'.$this->input->post('maxFecha'));
      }} else {
        redirect('Estrategico/Resumen_solicitudes/reporte');
    }
  }

  public function reporte(){

    $USER = $this->session->userdata('logged_in');
    $data['title'] = "Reporte Resumen de Solicitudes de Bodega";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $data['js'] = 'assets/js/validate/reporte/bodega/resumen_solicitud.js';
    $table = '';
    if (($this->uri->segment(4))!=NULL && ($this->uri->segment(5))!=NULL) {
      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('#','Nivel de Solicitud', 'Cantidad de Solicitudes');

    $registros = $this->Solicitud_Model->totalSolicitudesBodega($this->uri->segment(4),$this->uri->segment(5));


      if (!($registros == FALSE)) {
        $this->table->add_row(1,'<strong>Ingresadas',$registros->nivel0);
        $this->table->add_row(2,'<strong>Enviadas',$registros->nivel1);
        $this->table->add_row(3,'<strong>Aprobadas por jefatura',$registros->nivel2);
        $this->table->add_row(4,'<strong>Aprobadas por autorizante',$registros->nivel3);
        $this->table->add_row(5,'<strong>Liquidadas',$registros->nivel4);
        $this->table->add_row(6,'<strong>Solicitudes denegadas',$registros->nivel9);
        $this->table->add_row(7,'<strong>Total de solicitudes',$registros->total);
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "3");
        $this->table->add_row($msg);
      }



      $table =  $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 3))) .
                "<div class='content_table '>" .
                "<div class='limit-content-title'>".
                  "<div class='title-reporte'>".
                    "Reporte resumen de solicitudes de bodega".
                  "</div>".
                  "<div class='title-header'>
                    <ul>
                      <li>Fecha emisión: ".date('d/m/Y')."</li>
                      <li>Nombre la compañia: MTPS</li>
                      <li>N° pagina: ". 1 .'/'. 1 ."</li>
                      <li>Usuario: ".$USER['nombre_completo']."</li>
                      <br />
                      <li>Parametros: " . $this->uri->segment(4) . " - " . $this->uri->segment(5)."</li>
                    </ul>
                  </div>".
                "</div>".
                "<div class='limit-content'>" .
                "<div class='exportar'><a href='".base_url('/index.php/Estrategico/Resumen_solicitudes/RetiroReporteExcel/'.$this->uri->segment(4).'/'
                .$this->uri->segment(5))."' class='icono icon-file-excel'>
                Exportar Excel</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div></div>";

      $data['body'] = $table;

    } else {

      $data['body'] = $this->load->view('Estrategico/resumen_solicitudes_view', array('user' =>  $this->session->userdata('logged_in')), TRUE);

    }

    $this->load->view('base', $data);
  }

  public function RetiroReporteExcel() {

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
    $objPHPExcel->getProperties()->setCreator("SIGB")
                 ->setLastModifiedBy("SIGB")
                 ->setTitle("Reporte resumen de solicitudes de bodega.")
                 ->setSubject("Reporte resumen de solicitudes de bodega.")
                 ->setDescription("Reporte generado control de proceso de solicitud en un rango de fechas.")
                 ->setKeywords("office PHPExcel php")
                 ->setCategory("Reporte resumen de solicitudes de bodega.");

    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', '#')
                 ->setCellValue('B1', 'Nivel de solicitud')
                 ->setCellValue('C1', 'Cantidad de solicitudes');
    $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($estilo_titulo);

    $registros = $this->Solicitud_Model->totalSolicitudesBodega($this->uri->segment(4),$this->uri->segment(5));

    if (!($registros == FALSE)) {


        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A2', 1)
                    ->setCellValue('A3', 2)
                    ->setCellValue('A4', 3)
                    ->setCellValue('A5', 4)
                    ->setCellValue('A6', 5)
                    ->setCellValue('A7', 6)
                    ->setCellValue('A8', 7)
                    ->setCellValue('B2', 'Ingresadas')
                    ->setCellValue('B3', 'Enviadas')
                    ->setCellValue('B4', 'Aprobadas por jefatura')
                    ->setCellValue('B5', 'Aprobadas por autorizante')
                    ->setCellValue('B6', 'Liquidadas')
                    ->setCellValue('B7', 'Solicitudes denegadas')
                    ->setCellValue('B8', 'Total de solicitudes')
                    ->setCellValue('C2', $registros->nivel0)
                    ->setCellValue('C3', $registros->nivel1)
                    ->setCellValue('C4', $registros->nivel2)
                    ->setCellValue('C5', $registros->nivel3)
                    ->setCellValue('C6', $registros->nivel4)
                    ->setCellValue('C7', $registros->nivel9)
                    ->setCellValue('C8', $registros->total);


        $objPHPExcel->getActiveSheet()->getStyle('A2:C8')->applyFromArray($estilo_contenido);

      foreach(range('A','C') as $columnID){
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }

      $objPHPExcel->setActiveSheetIndex(0);

      header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header("Content-Disposition: attachment; filename='reporte_resumen_solicitudes.xlsx'");
      header('Cache-Control: max-age=0');

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
    }
  }

  public function DetalleRetiroReporteExcel() {

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
                 ->setTitle("Reporte Detalle de Salidas y Saldos de Bodega de productos por Objeto Especifico.")
                 ->setSubject("Reporte de Detalle Salidas y Saldos de Bodega de productos por Objeto Especifico.")
                 ->setDescription("Reporte generado para conciliaciones contables al cierre de cada mes.")
                 ->setKeywords("office PHPExcel php")
                 ->setCategory("Reporte de DetalleSalidas y Saldos de Bodega de productos por Objeto Especifico.");

    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Número Solicitud')
                 ->setCellValue('B1', 'Fecha Salida')
                 ->setCellValue('C1', 'Producto')
                 ->setCellValue('D1', 'Unidad Medidad')
                 ->setCellValue('E1', 'Cantidad')
                 ->setCellValue('F1', 'Sub Total');
    $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($estilo_titulo);

    $registros = $this->Detalle_solicitud_producto_model->obtenerProductosLimit($this->uri->segment(4),
    $this->uri->segment(5),$this->uri->segment(6),$this->uri->segment(7));

    if (!($registros == FALSE)) {
      $i = 2;
      $total=0.0;
      foreach($registros as $prod) {
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $prod->numero_solicitud)
                    ->setCellValue('B'.$i, $prod->fecha_salida)
                    ->setCellValue('C'.$i, $prod->producto)
                    ->setCellValue('D'.$i, $prod->unidad)
                    ->setCellValue('E'.$i, $prod->cantidad)
                    ->setCellValue('F'.$i, $prod->total);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray($estilo_contenido);
        $i++;
        $total+=$prod->total;
      }
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':E'.$i);
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A'.$i, "TOTAL:")
                  ->setCellValue('F'.$i, $total);
      $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray($estilo_contenido);

      foreach(range('A','F') as $columnID){
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }

      $objPHPExcel->setActiveSheetIndex(0);

      header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header("Content-Disposition: attachment; filename='reporte_detalle_salidas_saldos.xlsx'");
      header('Cache-Control: max-age=0');

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
    }
  }

}

?>
