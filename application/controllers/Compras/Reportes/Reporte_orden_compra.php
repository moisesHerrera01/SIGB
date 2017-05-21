<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_orden_compra extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }

    $this->load->helper(array('form', 'paginacion'));
    $this->load->library(array('table'));
    $this->load->model('Compras/Orden_Compra_Model');
  }

  public function Recibirfechas() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('tipo_empresa')=='MICRO EMPRESA'){
      $tipo=1;
    } elseif ($this->input->post('tipo_empresa')=='PEQUEÑA EMPRESA') {
      $tipo=2;
    } elseif ($this->input->post('tipo_empresa')=='MEDIANA EMPRESA') {
      $tipo=3;
    } elseif ($this->input->post('tipo_empresa')=='GRAN EMPRESA') {
      $tipo=4;
    } elseif ($this->input->post('tipo_empresa')=='OTROS CONTRIBUYENTES') {
      $tipo=5;
    } elseif ($this->input->post('tipo_empresa')=='default') {
      $tipo=0;
    }


    if ($this->input->post('fecha_inicio')!=NULL && $this->input->post('fecha_fin')!=NULL) {
      redirect('Compras/Reportes/Reporte_orden_compra/reporte/'.$tipo.'/'.$this->input->post('fecha_inicio').'/'.$this->input->post('fecha_fin'));
    } elseif ($this->input->post('fecha_inicio')!=NULL && $this->input->post('fecha_fin')==NULL){
      redirect('Compras/Reportes/Reporte_orden_compra/reporte/'.$tipo.'/'.$this->input->post('fecha_inicio').'/'.$fecha_actual);
    } elseif ($this->input->post('fecha_inicio')==NULL && $this->input->post('fecha_fin')==NULL){
      redirect('Compras/Reportes/Reporte_orden_compra/reporte/');
    }
  }

  public function reporte(){
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $anyo_en_curso=date($anyo."y");
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "1- Reporte Orden Compras";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';
      if ($this->uri->segment(5)!=NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Nº','Nº Orden','Nº Req','Fecha','Solicitante', 'Descripción', 'Proveedor','Tipo Empresa','Monto');
        $num = '10';
        if ($this->uri->segment(5)==1){
          $tipo='MICRO EMPRESA';
        } elseif ($this->uri->segment(5)==2) {
          $tipo='PEQUEÑA EMPRESA';
        } elseif ($this->uri->segment(5)==3) {
          $tipo='MEDIANA EMPRESA';
        } elseif ($this->uri->segment(5)==4) {
          $tipo='GRAN EMPRESA';
        } elseif ($this->uri->segment(5)==5) {
          $tipo='OTROS CONTRIBUYENTES';
        } elseif ($this->uri->segment(5)==0) {
          $tipo=NULL;
        }
          $registros = $this->Orden_Compra_Model->obtenerOrdenesFiltro($tipo,$this->uri->segment(6),$this->uri->segment(7),$num, $this->uri->segment(8));
          //$total = $this->Orden_Compra_Model->obtenerOrdenesFiltroTotal($tipo,$this->uri->segment(6),$this->uri->segment(7));
          error_reporting(0);
          $contador=0;
          foreach($registros as $cont) {
            $contador=$contador+1;
          }
          $pagination = paginacion('index.php/Compras/Reportes/Reporte_orden_compra/reporte/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7),
            $contador, $num, '7');
        if (!($registros == FALSE)) {
          $i = 1;
          $descripcion='';
          $monto_total=0.0;
          foreach($registros as $orden) {
            $numero;
            if ($orden->nivel_solicitud==6) {
              $numero='S/E';
            }else {
              $numero=$orden->nombre_fuente.'-'.$orden->numero_solicitud_compra.'/'.substr($orden->fecha_solicitud_compra,0,-6);
            }
            $descripcion=$this->Orden_Compra_Model->obtenerDescripcionProductos($orden->id_orden_compra);
            $this->table->add_row($i,$orden->numero_orden_compra.'/'.$anyo_en_curso, $numero,
             $orden->fecha, $orden->nombre_seccion, $descripcion, $orden->nombre_proveedor,$orden->tipo_empresa,'$'.number_format($orden->monto_total_oc,2));
             $monto_total+=$orden->monto_total_oc;
             $i++;
          }
          $tot = array('data' => "TOTAL:", 'colspan' => "8");
          $this->table->add_row($tot,  '$'.number_format($monto_total, 2));
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "8");
          $this->table->add_row($msg);
        }
        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ". $this->uri->segment(6) . " - " . $this->uri->segment(7)."</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar'><a href='".base_url('/index.php/Compras/Reportes/Reporte_orden_compra/ReporteExcel/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.
                    $this->uri->segment(7))."' class='icono icon-file-excel'>
                  Exportar Excel</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }

      $data['body'] = $this->load->view('Compras/Reportes/Reporte_orden_compra_view', '',TRUE) . "<br>" . $table;
      $this->load->view('base', $data);
    } else {
      redirect('login/index/forbidden');
    }
  }

  public function ReporteExcel() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $anyo_en_curso=date($anyo."y");
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
      $objDrawing->setCoordinates('H1');
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
                 ->setCellValue('H6', 'Tipo Empresa')
                 ->setCellValue('I6', 'Monto');
    $objPHPExcel->getActiveSheet()->getStyle('A6:I6')->applyFromArray($estilo_titulo);

    if ($this->uri->segment(5)==1){
      $tipo='MICRO EMPRESA';
    } elseif ($this->uri->segment(5)==2) {
      $tipo='PEQUEÑA EMPRESA';
    } elseif ($this->uri->segment(5)==3) {
      $tipo='MEDIANA EMPRESA';
    } elseif ($this->uri->segment(5)==4) {
      $tipo='GRAN EMPRESA';
    } elseif ($this->uri->segment(5)==5) {
      $tipo='OTROS CONTRIBUYENTES';
    } elseif ($this->uri->segment(5)==0) {
      $tipo=NULL;
    }

    //$total_registros = $this->Orden_Compra_Model->obtenerOrdenesFiltroTotal($this->uri->segment(5),$this->uri->segment(6), $this->uri->segment(7));
    $registros = $this->Orden_Compra_Model->obtenerOrdenesExcel($tipo, $this->uri->segment(6), $this->uri->segment(7));
    $contador=0;
    foreach($registros as $cont) {
      $contador=$contador+1;
    }
    if ($contador > 0) {
      $registros = $this->Orden_Compra_Model->obtenerOrdenesExcel($tipo, $this->uri->segment(6), $this->uri->segment(7));

      $total = 0;
      $i = 7;
      $num=1;
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:I1');
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:I2');
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A4:I4');
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A1', "MINISTERIO DE TRABAJO Y PREVISIÓN SOCIAL")
                  ->setCellValue('A2', "UNIDAD DE ADQUISICIONES Y CONTRATACIONES INSTITUCIONAL")
                  ->setCellValue('A4', "CUADRO DE REPORTE DE ORDENES DE COMPRAS".' DEL '.$this->uri->segment(6).
                  ' AL '.$this->uri->segment(7));
      $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($estilo_contenido);
      $objPHPExcel->getActiveSheet()->getStyle('A2:I2')->applyFromArray($estilo_contenido);
      $objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($estilo_titulo);

      while ($registro = current($registros)) {
        $descripcion=$this->Orden_Compra_Model->obtenerDescripcionProductos($registro->id_orden_compra);
        $numero;
        if ($registro->nivel_solicitud==6) {
          $numero='S/E';
        }else {
          $numero=$registro->nombre_fuente.'-'.$registro->numero_solicitud_compra.'/'.$anyo_en_curso;
        }
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $num)
                    ->setCellValue('B'.$i, $registro->numero_orden_compra.'/'.$anyo_en_curso)
                    ->setCellValue('C'.$i, $numero)
                    ->setCellValue('D'.$i, $registro->fecha)
                    ->setCellValue('E'.$i, $registro->nombre_seccion)
                    ->setCellValue('F'.$i, $descripcion)
                    ->setCellValue('G'.$i, $registro->nombre_proveedor)
                    ->setCellValue('H'.$i, $registro->tipo_empresa)
                    ->setCellValue('I'.$i, '$'.number_format($registro->monto_total_oc,2));
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($estilo_contenido);

        $total += $registro->monto_total_oc;

        $next = next($registros);
        $num++;
        $i++;
      }
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':H'.$i);
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A'.$i, "TOTAL:")
                  ->setCellValue('I'.$i, '$'.number_format($total,2));
      $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($estilo_titulo);
    } else {
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:I2');
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "No se encontraron resultados");
      $objPHPExcel->getActiveSheet()->getStyle('A2:I2')->applyFromArray($estilo_contenido);
    }

    foreach(range('A','I') as $columnID){
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    ob_end_clean();
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='reporte_orden_compra.xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }
}

?>
