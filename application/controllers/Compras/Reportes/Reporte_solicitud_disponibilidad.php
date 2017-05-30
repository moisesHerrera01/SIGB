<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_solicitud_disponibilidad extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }

    $this->load->helper(array('form', 'paginacion'));
    $this->load->library(array('table'));
    $this->load->model(array('Compras/Solicitud_Disponibilidad_Model','Compras/Detalle_disponibilidad_montos_model'));
  }

  public function Recibirfechas() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('minFecha')) {
      if($this->input->post('maxFecha')==NULL){
        redirect('Compras/Reportes/Reporte_solicitud_disponibilidad/reporteDisponibilidad/'.$this->input->post('minFecha').'/'.$fecha_actual);
      }else{
        redirect('Compras/Reportes/Reporte_solicitud_disponibilidad/reporteDisponibilidad/'.$this->input->post('minFecha').'/'.$this->input->post('maxFecha'));
      }} else {
        redirect('Compras/Reportes/Reporte_solicitud_disponibilidad/reporteDisponibilidad/');
    }
  }

  public function reporteDisponibilidad(){
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $anyo_en_curso=date($anyo."y");
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "2- Reporte Disponibilidad Financiera";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $data['js'] = 'assets/js/validate/reporte/compra/solicitud_disponibilidad.js';
      $table = '';
      if ($this->uri->segment(5)!=NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Nº','Nº Req','Especifico','Unidad Solicitante', 'Descripción','Monto','Fecha retorno UFI');
        $num = '10';
          $registros = $this->Solicitud_Disponibilidad_Model->reporteDisponibilidad($this->uri->segment(5),
          $this->uri->segment(6),$num, $this->uri->segment(7));
          $total = $this->Solicitud_Disponibilidad_Model->reporteDisponibilidadTotal($this->uri->segment(5),$this->uri->segment(6));
          error_reporting(0);
          $pagination = paginacion('index.php/Compras/Reportes/Reporte_solicitud_disponibilidad/reporteDisponibilidad/'.$this->uri->segment(5).'/'.$this->uri->segment(6),
            $total->total, $num, '7');
        if (!($registros == FALSE)) {
          $i = 1;
          $monto_total=0.0;
          $descripcion='';
          foreach($registros as $disp) {
            $numero;
            if ($disp->nivel_solicitud==6) {
              $numero='S/E';
            }else {
              $numero=$disp->nombre_fuente.'-'.$disp->numero_solicitud_compra.'/'.substr($disp->fecha_solicitud_compra,0,-6);
            }
            $total_disponibilidad=0.0;
            $descripcion=$this->Solicitud_Disponibilidad_Model->obtenerDescripcionProductos($disp->id_solicitud_disponibilidad);
            $montos=$this->Detalle_disponibilidad_montos_model->obtenerDetalleDisponibilidad($disp->id_solicitud_disponibilidad);
            foreach ($montos as $monto) {
              $total_disponibilidad+=$monto->monto_sub_total;
            }
            if ($disp->fecha=='0000-00-00') {
              $disp->fecha='';
            }
            $this->table->add_row($i,$numero, $disp->id_especifico,$disp->nombre_seccion.' - '.$disp->linea_trabajo,
            $descripcion.' - '.$disp->justificacion, '$'.number_format($total_disponibilidad,2),$disp->fecha);
            $monto_total+=$total_disponibilidad;
            $i++;
          }
          $tot = array('data' => "TOTAL:", 'colspan' => "5");
          $monto = array('data' => '$'.number_format($monto_total, 2),'colspan' => "2" );
          $this->table->add_row($tot,$monto);
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "7");
          $this->table->add_row($msg);
        }
        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ". $this->uri->segment(5) . " - " . $this->uri->segment(6)."</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar'><a href='".base_url('/index.php/Compras/Reportes/Reporte_solicitud_disponibilidad/ReporteExcel/'.$this->uri->segment(5).'/'.
                    $this->uri->segment(6))."' class='icono icon-file-excel'>
                  Exportar Excel</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }

      $data['body'] = $this->load->view('Compras/Reportes/Reporte_solicitud_disponibilidad_view', '',TRUE) . "<br>" . $table;
      $this->load->view('base', $data);
    } else {
      redirect('login/index/forbidden');
    }
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
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    $objDrawing->setName('Logo');
    $objDrawing->setDescription('Logo');
    $logo = 'assets/image/icono.jpg'; // Provide path to your logo file
    $objDrawing->setPath($logo);  //setOffsetY has no effect
    $objDrawing->setCoordinates('F1');
    $objDrawing->setHeight(100); // logo height
    $objDrawing->setOffsetX(135);    // setOffsetX works properly
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);


    $objPHPExcel->getProperties()->setCreator("SICBAF")
    						 ->setLastModifiedBy("SICBAF")
    						 ->setTitle("Reporte de solicitudes de disponibilidad financiera")
    						 ->setSubject("Reporte de solicitudes de disponibilidad financiera")
    						 ->setDescription("Reporte de solicitudes de disponibilidad financiera.")
    						 ->setKeywords("office PHPExcel php")
    						 ->setCategory("Reporte de solicitudes de disponibilidad financiera");
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A6', 'Nº')
                ->setCellValue('B6', 'Nº Req.')
                ->setCellValue('C6', 'Especifico')
                ->setCellValue('D6', 'Unidad Solicitante')
                ->setCellValue('E6', 'Descripción')
                ->setCellValue('F6', 'Monto')
                ->setCellValue('G6', 'Fecha Retorno UFI');
    $objPHPExcel->getActiveSheet()->getStyle('A6:G6')->applyFromArray($estilo_titulo);

    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:G1');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:G2');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A4:G4');
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', "MINISTERIO DE TRABAJO Y PREVISIÓN SOCIAL")
                ->setCellValue('A2', "UNIDAD DE ADQUISICIONES Y CONTRATACIONES INSTITUCIONAL")
                ->setCellValue('A4', "CUADRO DE REPORTE DE SOLICITUDES DE DISPONIBILIDAD FINANCIERA".' DEL '.$this->uri->segment(5).
                ' AL '.$this->uri->segment(6));
    $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($estilo_contenido);
    $objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($estilo_contenido);
    $objPHPExcel->getActiveSheet()->getStyle('A4:G4')->applyFromArray($estilo_titulo);

    $registros = $this->Solicitud_Disponibilidad_Model->reporteDisponibilidadExcel($this->uri->segment(5),
    $this->uri->segment(6));
    if (!($registros == FALSE)) {
      $i = 7;
      $j=1;
      $total=0;
      foreach($registros as $dis) {
        $total_disponibilidad=0.0;
        $descripcion=$this->Solicitud_Disponibilidad_Model->obtenerDescripcionProductos($dis->id_solicitud_disponibilidad);
        $montos=$this->Detalle_disponibilidad_montos_model->obtenerDetalleDisponibilidad($dis->id_solicitud_disponibilidad);
        foreach ($montos as $monto) {
          $total_disponibilidad+=$monto->monto_sub_total;
        }
        if ($dis->fecha=='0000-00-00') {
          $dis->fecha='';
        }
        date_default_timezone_set('America/El_Salvador');
        $anyo=20;
        $anyo_en_curso=date($anyo."y");
        $numero;
        if ($dis->nivel_solicitud==6) {
          $numero='S/E';
        }else {
          $numero=$dis->nombre_fuente.'-'.$dis->numero_solicitud_compra.'/'.substr($dis->fecha_solicitud_compra,0,-6);
        }
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $j)
                    ->setCellValue('B'.$i, $numero)
                    ->setCellValue('C'.$i, $dis->id_especifico)
                    ->setCellValue('D'.$i, $dis->nombre_seccion.' - '.$dis->linea_trabajo)
                    ->setCellValue('E'.$i, $descripcion.' - '.$dis->justificacion)
                    ->setCellValue('F'.$i, '$'.number_format($total_disponibilidad,2))
                    ->setCellValue('G'.$i, $dis->fecha);

        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estilo_contenido);
        $i++;
        $j++;
        $total += $total_disponibilidad;
      }
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':E'.$i);
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A'.$i, "TOTAL:")
                  ->setCellValue('F'.$i, '$'.number_format($total,2));
      $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estilo_titulo);

      foreach(range('A','G') as $columnID){
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }

      $objPHPExcel->setActiveSheetIndex(0);
      ob_end_clean();
      header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header("Content-Disposition: attachment; filename='reporte_disponibilidad.xlsx'");
      header('Cache-Control: max-age=0');

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
    }
  }
}
?>
