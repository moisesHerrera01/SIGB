<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_linea_presupuestaria extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }

    $this->load->helper(array('form', 'paginacion'));
    $this->load->library(array('table'));
    $this->load->model(array('Compras/Solicitud_Disponibilidad_Model','Compras/Orden_Compra_Model'));
  }

  public function Recibirfechas() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('fecha_inicio')) {
      if($this->input->post('fecha_fin')==NULL){
        redirect('Compras/Reportes/Reporte_linea_presupuestaria/reporte/'.$this->input->post('fecha_inicio').'/'
        .$fecha_actual.'/'.$this->input->post('id_linea'));
      }else{
        redirect('Compras/Reportes/Reporte_linea_presupuestaria/reporte/'.$this->input->post('fecha_inicio').'/'
        .$this->input->post('fecha_fin').'/'.$this->input->post('id_linea'));
      }} else {
        redirect('Compras/Reportes/Reporte_linea_presupuestaria/reporte/');
    }
  }

  public function reporte(){
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $anyo_en_curso=date($anyo."y");
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "3-Reporte linea";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $data['js'] = 'assets/js/validate/reporte/compra/linea_presupuestaria.js';
      $table = '';
      if ($this->uri->segment(5)!=NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Nº','Nº Orden','Nº Req','Fecha','Solicitante', 'Descripción', 'Proveedor', 'Monto');
        $num = '10';
          $registros = $this->Solicitud_Disponibilidad_Model->obtenerSolicitudesDisponibilidadFiltro($this->uri->segment(5),
          $this->uri->segment(6),$this->uri->segment(7),$num, $this->uri->segment(8));
          $total = $this->Solicitud_Disponibilidad_Model->obtenerSolicitudesDisponibilidadTotal($this->uri->segment(5),
          $this->uri->segment(6),$this->uri->segment(7));
          error_reporting(0);
          $pagination = paginacion('index.php/Compras/Reportes/Reporte_linea_presupuestaria/reporte/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7),$total->total, $num, '8');
          $descripcion='';
          $monto_total=0.0;
          $linea='';
        if (!($registros == FALSE)) {
          $i = 1;
          foreach($registros as $orden) {
            $descripcion=$this->Orden_Compra_Model->obtenerDescripcionProductos($orden->id_orden_compra);
            $this->table->add_row($i,$orden->numero_orden_compra.'/'.$anyo_en_curso,
             $orden->nombre_fuente.'-'.$orden->numero_solicitud_compra.'/'.substr($orden->fecha_solicitud_compra,0,-6),$orden->fecha,
             $orden->nombre_seccion, $descripcion, $orden->nombre_proveedor,
             '$'.number_format($orden->monto_sub_total,2));
             $monto_total+=$orden->monto_sub_total;
             $linea=$orden->linea_trabajo;
             $i++;
          }
          $tot = array('data' => "TOTAL:", 'colspan' => "7");
          $this->table->add_row($tot,  '$'.number_format($monto_total, 2));
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "8");
          $this->table->add_row($msg);
        }
        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ". $this->uri->segment(5) . " - " . $this->uri->segment(6)." - Linea presupuestaria/trabajo: $linea"."</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar'><a href='".base_url('/index.php/Compras/Reportes/Reporte_linea_presupuestaria/ReporteExcel/'.$this->uri->segment(5).'/'.
                    $this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>
                  Exportar Excel</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }

      $data['body'] = $this->load->view('Compras/Reportes/Reporte_linea_presupuestaria_view', '',TRUE) . "<br>" . $table;
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
      $objDrawing->setCoordinates('G1');
      $objDrawing->setHeight(100); // logo height
      $objDrawing->setOffsetX(135);    // setOffsetX works properly
      $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
      $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);


    $objPHPExcel->getProperties()->setCreator("SICBAF")
    						 ->setLastModifiedBy("SICBAF")
    						 ->setTitle("Reporte de ordenes de compra.")
    						 ->setSubject("Reporte de ordenes de compra.")
    						 ->setDescription("Reporte de ordenes de compra.")
    						 ->setKeywords("office PHPExcel php")
    						 ->setCategory("Reporte de ordenes de compra.");
    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A6', 'Nº')
                 ->setCellValue('B6', 'Nº de orden')
                 ->setCellValue('C6', 'Nº Req')
                 ->setCellValue('D6', 'Fecha')
                 ->setCellValue('E6', 'Solicitante')
                 ->setCellValue('F6', 'Descripción')
                 ->setCellValue('G6', 'Proveedor')
                 ->setCellValue('H6', 'Monto');
    $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->applyFromArray($estilo_titulo);

    $total_registros = $this->Solicitud_Disponibilidad_Model->obtenerSolicitudesDisponibilidadTotal($this->uri->segment(5),
     $this->uri->segment(6), $this->uri->segment(7));
    $linea='';
    if ($total_registros->total > 0) {
      $registros = $this->Solicitud_Disponibilidad_Model->obtenerSolicitudesDisponibilidadExcel($this->uri->segment(5),
       $this->uri->segment(6),$this->uri->segment(7));
       foreach ($this->Solicitud_Disponibilidad_Model->obtenerLineasTrabajo() as $lineas) {
         if($lineas->id_linea_trabajo==$this->uri->segment(7)){
           $linea=$lineas->linea_trabajo;
         }
       }
      $total = 0;
      $i = 7;
      $num=1;
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:H1');
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:H2');
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A4:H4');
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A1', "MINISTERIO DE TRABAJO Y PREVISIÓN SOCIAL")
                  ->setCellValue('A2', "UNIDAD DE ADQUISICIONES Y CONTRATACIONES INSTITUCIONAL")
                  ->setCellValue('A4', "CUADRO DE REPORTE POR LINEA PRESUPUESTARIA".' DEL '.$this->uri->segment(5).
                  ' AL '.$this->uri->segment(6). " LINEA PRESUPUESTARIA/TRABAJO: $linea");
      $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($estilo_contenido);
      $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($estilo_contenido);
      $objPHPExcel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($estilo_titulo);
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $anyo_en_curso=date($anyo."y");
      while ($registro = current($registros)) {
        $descripcion=$this->Orden_Compra_Model->obtenerDescripcionProductos($registro->id_orden_compra);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $num)
                    ->setCellValue('B'.$i, $registro->numero_orden_compra.'/'.$anyo_en_curso)
                    ->setCellValue('C'.$i, $registro->nombre_fuente.'-'.$registro->numero_solicitud_compra.'/'.$anyo_en_curso)
                    ->setCellValue('D'.$i, $registro->fecha)
                    ->setCellValue('E'.$i, $registro->nombre_seccion)
                    ->setCellValue('F'.$i, $descripcion)
                    ->setCellValue('G'.$i, $registro->nombre_proveedor)
                    ->setCellValue('H'.$i, '$'.number_format($registro->monto_sub_total,2));
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($estilo_contenido);

        $total += $registro->monto_sub_total;

        $next = next($registros);
        $num++;
        $i++;
      }
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':G'.$i);
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A'.$i, "TOTAL:")
                  ->setCellValue('H'.$i, '$'.number_format($total,2));
      $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($estilo_titulo);
    } else {
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:H2');
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "No se encontraron resultados");
      $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($estilo_contenido);
    }

    foreach(range('A','H') as $columnID){
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='reporte_por_linea_.xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }
}

?>
