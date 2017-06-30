<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ingreso_seccion_especifico extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Factura_Model'));
  }

  public function RecibirRetiro() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('fecha_inicio')!=NULL) {
        $USER = $this->session->userdata('logged_in');
        $modulo=$this->User_model->obtenerModulo('Tactico/Gasto_global/reporteGastoSeccion');
        $hora=date("H:i:s");
        $rastrea = array(
          'id_usuario' =>$USER['id'],
          'id_modulo' =>$modulo,
          'fecha' =>$fecha_actual,
          'hora' =>$hora,
          'operacion'=> 'CONSULTA'
        );
        $this->User_model->insertarRastreabilidad($rastrea);
      if($this->input->post('fecha_fin')==NULL){
        redirect('Tactico/Ingreso_seccion_especifico/reporte/'.$this->input->post('fecha_inicio').'/'
        .$fecha_actual.'/'.$this->input->post('seccion'));
      }else{
        redirect('Tactico/Ingreso_seccion_especifico/reporte/'.$this->input->post('fecha_inicio').'/'
        .$this->input->post('fecha_fin').'/'.$this->input->post('seccion'));
      }} else {
        redirect('Tactico/Ingreso_seccion_especifico/reporte/');
    }
  }

  public function reporte(){
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date("d-m-".$anyo."y");
    $USER = $this->session->userdata('logged_in');
    $data['title'] = "Reporte Ingresos por Sección";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $data['js'] = 'assets/js/validate/reporte/bodega/gasto_seccion.js';
    $table = '';
    if (($this->uri->segment(4))!=NULL && ($this->uri->segment(5))!=NULL && ($this->uri->segment(6))!=NULL) {
      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('Número Especifico','Nombre Especifico', 'Cantidad de Facturas', 'Cantidad de productos','Total');

      $num = 10;
      $segmento = 7;if ($this->input->is_ajax_request()) {
        if (!($this->input->post('busca') == "")) {
          $registros = $this->Factura_Model->buscaIngresoSeccionEspecifico($this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6),$this->input->post('busca'));
          $count=count($registros);
        } else {
          $count = $this->Factura_Model->totalIngresoSeccionEspecifico($this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6));
          $registros = $this->Factura_Model->ingresoSeccionEspecifico($this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6),$num, $this->uri->segment(7));

        }
      } else {
        $count = $this->Factura_Model->totalIngresoSeccionEspecifico($this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6));
        $registros = $this->Factura_Model->ingresoSeccionEspecifico($this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6),$num, $this->uri->segment(7));
      }
      $sumtotal= $this->Factura_Model->SumaTotalIngresoSeccionEspecifico($this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6));

      $pagination = paginacion('index.php/Tactico/Ingreso_seccion_especifico/reporte/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6),
                    $count, $num, $segmento);
      if (!($registros == FALSE)) {
        $totales=0;
        foreach($registros as $salida) {

          $this->table->add_row($salida->id_especifico,$salida->nombre_especifico, $salida->facturas,$salida->cantidad,'$'.number_format($salida->total,3));
          $totales += $salida->total;

        }

        $cell = array('data' => 'Total por pagina', 'colspan' => 4);
        $this->table->add_row($cell, '$'.number_format($totales,3), "");
        $cell = array('data' => 'Total global', 'colspan' => 4);
        $this->table->add_row($cell, '$'.number_format($sumtotal->tot,3), "");
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "6");
        $this->table->add_row($msg);
      }
      if ($this->input->is_ajax_request()) {
        echo "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
        return false;
      }

      // paginacion del header
      $pagaux = $count / $num;

      $pags = intval($pagaux);

      if ($pagaux > $pags || $pags == 0) {
        $pags++;
      }

      $seg = intval($this->uri->segment($segmento)) + 1;

      $segaux = $seg / $num;

      $pag = intval($segaux);

      if ($segaux > $pag) {
        $pag++;
      }

      $buscar = array(
        'name' => 'buscar',
        'type' => 'search',
        'placeholder' => 'BUSCAR POR NOMBRE ESPECIFICO',
        'class' => 'form-control',
        'autocomplete' => 'off',
        'id' => 'buscar',
        'url' => 'index.php/Tactico/Ingreso_seccion_especifico/reporte/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'
      );


      $table =  "<div class='content_table '>" .
                "<div class='limit-content-title'>".
                  "<div class='title-reporte'>".
                    "Reporte de ingresos por sección y especifico.".
                  "</div>".
                  "<div class='title-header'>
                    <ul>
                      <li>Fecha emisión: ".$fecha_actual."</li>
                      <li>Nombre la compañia: MTPS</li>
                      <li>N° pagina: ". $pag .'/'. $pags ."</li>
                      <li>Usuario: ".$USER['nombre_completo']."</li>
                      <br />
                      <li>Parametros: ".$this->Factura_Model->obtenerSeccion($this->uri->segment(6)). " " . $this->uri->segment(4) . " - " . $this->uri->segment(5)."</li>
                    </ul>
                  </div>".
                "</div>".
                "<div class='limit-content'>" .
                "<div class='exportar'><a href='".base_url('/index.php/Tactico/Ingreso_seccion_especifico/RetiroReporteExcel/'.$this->uri->segment(4).'/'
                .$this->uri->segment(5).'/'.$this->uri->segment(6))."' class='icono icon-file-excel'>
                Exportar Excel</a><span class='content_buscar'><i class='glyphicon glyphicon-search'></i>".form_input($buscar)."</span></div>" . "<div class='table-content'><div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";

      $data['body'] = $table;

    } else {

      $data['body'] = $this->load->view('Tactico/Ingreso_seccion_especifico_view', array('user' =>  $this->session->userdata('logged_in')), TRUE);

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
                 ->setTitle("Reporte de Ingresos por Sección y por Objeto Especifico.")
                 ->setSubject("Reporte de Ingresos por Sección y por Objeto Especifico.")
                 ->setDescription("Reporte generado para conciliaciones contables en un rango de fechas...")
                 ->setKeywords("office PHPExcel php")
                 ->setCategory("Reporte de Ingresos por Sección y por Objeto Especifico.");

    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Número Especifico')
                 ->setCellValue('B1', 'Nombre Especifico')
                 ->setCellValue('C1', 'Cantidad de Facturas')
                 ->setCellValue('D1', 'Cantidad de Productos')
                 ->setCellValue('E1', 'Total');
    $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($estilo_titulo);

    $registros = $this->Factura_Model->todosIngresoSeccionEspecifico($this->uri->segment(4),
    $this->uri->segment(5),$this->uri->segment(6));

    if (!($registros == FALSE)) {
      $fecha_inicio=$this->uri->segment(4);
      $fecha_fin=$this->uri->segment(5);
      $seccion=$this->uri->segment(6);
      $i = 2;
      $total=0;
      foreach($registros as $salida) {
      $total+=$salida->total;

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $salida->id_especifico)
                    ->setCellValue('B'.$i, $salida->nombre_especifico)
                    ->setCellValue('C'.$i, $salida->facturas)
                    ->setCellValue('D'.$i, $salida->cantidad)
                    ->setCellValue('E'.$i, $salida->total);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($estilo_contenido);
        $i++;
      }
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('D'.$i, 'Total')
                  ->setCellValue('E'.$i, $total);
      $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($estilo_contenido);
      foreach(range('A','E') as $columnID){
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }

      $objPHPExcel->setActiveSheetIndex(0);

      header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header("Content-Disposition: attachment; filename='reporte_ingreso_seccion_especifico.xlsx'");
      header('Cache-Control: max-age=0');

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
    }
  }

}

?>
