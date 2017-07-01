<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salidas_saldos extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Solicitud_Model','Bodega/Detalle_solicitud_producto_model', 'Bodega/Fuentefondos_model'));
  }

  public function RecibirRetiro() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('fecha_inicio')!=NULL && $this->input->post('fuente')!=NULL) {
      $USER = $this->session->userdata('logged_in');
      $modulo=$this->User_model->obtenerModulo('Estrategico/Salidas_saldos/reporte');
      $hora=date("H:i:s");
      $rastrea = array(
        'id_usuario' =>$USER['id'],
        'id_modulo' =>$modulo,
        'fecha' =>$fecha_actual,
        'hora' =>$hora,
        'operacion'=>'CONSULTA'
      );
      $this->User_model->insertarRastreabilidad($rastrea);
      if($this->input->post('fecha_fin')==NULL){
        redirect('Estrategico/Salidas_saldos/reporte/'.$this->input->post('fecha_inicio').'/'
        .$fecha_actual.'/'.$this->input->post('fuente'));
      }else{
        redirect('Estrategico/Salidas_saldos/reporte/'.$this->input->post('fecha_inicio').'/'
        .$this->input->post('fecha_fin').'/'.$this->input->post('fuente'));
      }} else {
        redirect('Estrategico/Salidas_saldos/');
    }
  }

  public function reporte(){
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date("d-m-".$anyo."y");
    $USER = $this->session->userdata('logged_in');
    $data['title'] = "Reporte Salidas y Saldos";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $data['js'] = 'assets/js/validate/reporte/bodega/salida.js';
    $table = '';
    if (($this->uri->segment(4))!=NULL && ($this->uri->segment(5))!=NULL && ($this->uri->segment(6))!=NULL) {
      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('#','Número Especifico', 'Nombre Especifico', 'Saldo','Salida', 'Detalle');

      $num = 10;
      $segmento = 7;
      $count = $this->Detalle_solicitud_producto_model->totalEspecifico($this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6));
      $registros = $this->Detalle_solicitud_producto_model->obtenerEspecificosLimit($this->uri->segment(4),
      $this->uri->segment(5),$this->uri->segment(6),$num, $this->uri->segment(7));
      $pagination = paginacion('index.php/Estrategico/Salidas_saldos/reporte/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6),
                    $count, $num, $segmento);

      if (!($registros == FALSE)) {
        $tsalida = 0;
        $saldos = 0;
        $fecha_inicio=$this->uri->segment(4);
        $fecha_fin=$this->uri->segment(5);
        $fuente=$this->uri->segment(6);
        $i = 1;
        foreach($registros as $salida) {
          $saldo=0.0;
          $entradas=0.0;
          $salidas=0.0;
          $salidas_rango=0.0;
          $kardex=$this->Detalle_solicitud_producto_model->obtenerKardex();
          foreach ($kardex as $kar) {
            if($kar->id_especifico==$salida->id_especifico){
              if($kar->movimiento=='SALIDA'){
                $salidas=$salidas+$kar->cantidad*$kar->precio;
                if($kar->fecha_ingreso>=$fecha_inicio &&
                 $kar->fecha_ingreso<=$fecha_fin && $kar->id_fuentes==$fuente){
                   $salidas_rango=$salidas_rango+$kar->cantidad*$kar->precio;
                 }
              }else{
                $entradas=$entradas+$kar->cantidad*$kar->precio;
              }
            }
          }
          $saldo=$entradas-$salidas;
          $this->table->add_row($i, $salida->id_especifico,$salida->nombre_especifico, '$'.number_format($saldo,3), '$'.number_format($salidas_rango,3),
          '<a class="icono icon-detalle" href="'.base_url('index.php/Estrategico/Salidas_saldos/reporteDetalleRetiro/'
            .$salida->id_especifico.'/'.$fecha_inicio.'/'.$fecha_fin.'/'.$fuente.'/').'"></a>');

          $saldos += $saldo;
          $tsalida += $salidas_rango;
          $i++;
        }
        $cell = array('data' => 'Total', 'colspan' => 3);
        $this->table->add_row($cell, '$'.number_format($saldos,3), '$'.number_format($tsalida,3), "");
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "6");
        $this->table->add_row($msg);
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


      $table =  "<div class='content_table '>" .
                "<div class='limit-content-title'>".
                  "<div class='title-reporte'>".
                    "Reporte de salidas y saldos por especifico.".
                  "</div>".
                  "<div class='title-header'>
                    <ul>
                      <li>Fecha emisión: ".$fecha_actual."</li>
                      <li>Nombre la compañia: MTPS</li>
                      <li>N° pagina: ". $pag .'/'. $pags ."</li>
                      <li>Usuario: ".$USER['nombre_completo']."</li>
                      <br />
                      <li>Parametros: ".$this->Fuentefondos_model->obtenerFuente($this->uri->segment(6)). " " . $this->uri->segment(4) . " - " . $this->uri->segment(5)."</li>
                    </ul>
                  </div>".
                "</div>".
                "<div class='limit-content'>" .
                "<div class='exportar'><a href='".base_url('/index.php/Estrategico/Salidas_saldos/RetiroReporteExcel/'.$this->uri->segment(4).'/'
                .$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>
                Exportar Excel</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";

      $data['body'] = $table;

    } else {

      $data['body'] = $this->load->view('Estrategico/salidas_saldos_view', array('user' =>  $this->session->userdata('logged_in')), TRUE);

    }

    $this->load->view('base', $data);
  }
  public function reporteDetalleRetiro(){

    $data['title'] = "Reporte Detalle";
    $table = '';
    if (($this->uri->segment(4))!=NULL) {
      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('Solicitud','Fecha Salida', 'Producto', 'Unidad Medida','Cantidad', 'Sub Total');

      $num = '2';
      $registros = $this->Detalle_solicitud_producto_model->obtenerProductosLimit($this->uri->segment(4),
      $this->uri->segment(5),$this->uri->segment(6),$this->uri->segment(7));

      if (!($registros == FALSE)) {
        $i = 1;
        $total_productos=0.0;
        foreach($registros as $prod) {
          $this->table->add_row($prod->numero_solicitud,$prod->fecha_salida,$prod->producto,$prod->unidad,
        $prod->cantidad,$prod->total);
          $total_productos+=$prod->total;
          $i++;
        }
        $msg = array('data' => "Total:", 'colspan' => "5");
        $this->table->add_row($msg,  number_format($total_productos, 3));
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "6");
        $this->table->add_row($msg);
      }
      $table = "<div class='exportar icono icon-file-excel'><a href='".base_url('/index.php/Estrategico/Salidas_saldos/DetalleRetiroReporteExcel/'
      .$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."'>
                Exportar Excel</a></div>" . $this->table->generate();
    }

    $data['body'] = $this->load->view('Estrategico/detalle_salidas_view', '',TRUE) . "<br>" . $table;
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

    $registros = $this->Detalle_solicitud_producto_model->obtenerEspecificosTotal($this->uri->segment(4),
    $this->uri->segment(5),$this->uri->segment(6));

    if (!($registros == FALSE)) {
      $fecha_inicio=$this->uri->segment(4);
      $fecha_fin=$this->uri->segment(5);
      $fuente=$this->uri->segment(6);
      $i = 2;
      foreach($registros as $salida) {
        $saldo=0.0;
        $entradas=0.0;
        $salidas=0.0;
        $salidas_rango=0.0;
        $kardex=$this->Detalle_solicitud_producto_model->obtenerKardex();
        foreach ($kardex as $kar) {
          if($kar->id_especifico==$salida->id_especifico){
            if($kar->movimiento=='SALIDA'){
              $salidas=$salidas+$kar->cantidad*$kar->precio;
              if($kar->fecha_ingreso>$fecha_inicio &&
               $kar->fecha_ingreso<$fecha_fin && $kar->id_fuentes==$fuente){
                 $salidas_rango=$salidas_rango+$kar->cantidad*$kar->precio;
               }
            }else{
              $entradas=$entradas+$kar->cantidad*$kar->precio;
            }
          }
        }
        $saldo=$entradas-$salidas;

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $i-1)
                    ->setCellValue('B'.$i, $salida->id_especifico)
                    ->setCellValue('C'.$i, $salida->nombre_especifico)
                    ->setCellValue('D'.$i, $saldo)
                    ->setCellValue('E'.$i, $salidas_rango);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($estilo_contenido);
        $i++;
      }

      foreach(range('A','E') as $columnID){
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
