<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class Productos_especifico extends CI_Controller {

    public function __construct() {
      parent::__construct();
      if($this->session->userdata('logged_in') == FALSE){
        redirect('login/index/error_no_autenticado');
      }
      $this->load->library(array('table','excel'));
      $this->load->helper(array('form','paginacion'));
      $this->load->model(array('Bodega/Producto','Bodega/Solicitud_Model',
      'Bodega/Fuentefondos_model','Bodega/UnidadMedida', 'Bodega/Kardex_model','Bodega/Especifico'));
    }

  public function recibirProductos() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('Tactico/Productos_especifico/reporteProductosEspecifico');
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
        redirect('Tactico/Productos_especifico/reporteProductosEspecifico/'.$fecha_actual);
      }else{
        redirect('Tactico/Productos_especifico/reporteProductosEspecifico/'.$this->input->post('fecha_fin'));
      }
  }

  public function reporteProductosEspecifico(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $this->load->model(array('mtps/Seccion_model'));
      $data['title'] = "Productos OE";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $data['js'] = 'assets/js/validate/reporte/bodega/productos_especifico.js';
      $table = '';
      if (($this->uri->segment(4))!=NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Especifico','Nombre especifico', 'Cantidad', 'Saldo');

        $num = '15';
        $segmento = 8;
                $seccion = ($this->uri->segment(6)==NULL) ? 0 : $this->uri->segment(6);

        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
            $registros = $this->Producto->buscarProductosEspecifico($this->uri->segment(4),$this->input->post('busca'));
            $total = count($registros);
          } else {
            $registros = $this->Producto->ProductosEspecifico($this->uri->segment(4),$num, $this->uri->segment(5));
            $total = $this->Producto->totalproductosEspecifico($this->uri->segment(4));
          }
        } else {
          $registros = $this->Producto->ProductosEspecifico($this->uri->segment(4),$num, $this->uri->segment(5));
          $total = $this->Producto->totalproductosEspecifico($this->uri->segment(4));
        }

        $pagination = paginacion('index.php/Tactico/Productos_especifico/reporteProductosEspecifico/'.$this->uri->segment(4).'/',
        $total,$num, '5');
        if (!($registros == FALSE)) {
          $i = 1;
          $total_saldo=0;
          $total_cantidad=0;
          foreach($registros as $pro) {
            $this->table->add_row($pro->id_especifico,$pro->nombre_especifico,$pro->cantidad,'$'.number_format($pro->saldo,3));
            $total_saldo+=$pro->saldo;
            $total_cantidad+=$pro->cantidad;
            $i++;
          }
          $msg = array('data' => "Total:", 'colspan' => "2");
          $this->table->add_row($msg, $total_cantidad ,'$'.number_format($total_saldo, 3));
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
          $this->table->add_row($msg);
        }

        if ($this->input->is_ajax_request()) {
          echo "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
          return false;
        }

                 // paginacion del header
                 $pagaux = $total/ $num;

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
                   'placeholder' => 'BUSCAR POR ESPECIFICO',
                   'class' => 'form-control',
                   'autocomplete' => 'off',
                   'id' => 'buscar',
                   'url' => 'index.php/Tactico/Productos_especifico/reporteProductosEspecifico/'.$this->uri->segment(4).'/'
                 );
                 $table =  "<div class='content_table '>" .
                           "<div class='limit-content-title'>".
                             "<div class='title-reporte'>".
                               "Reporte de productos por especifico.".
                             "</div>".
                             "<div class='title-header'>
                               <ul>
                                 <li>Fecha emisión: ".date('d/m/Y')."</li>
                                 <li>Nombre la compañia: MTPS</li>
                                 <li>N° pagina: ". $pag .'/'. $pags ."</li>
                                 <li>Usuario: ".$USER['nombre_completo']."</li>
                                 <br />
                                 <li>Parametros: Fecha última de cálculo: ".$this->uri->segment(4)."</li>
                               </ul>
                             </div>".
                           "</div>".
                           "<div class='limit-content'>" .
                           "<div class='exportar'><a href='".base_url('/index.php/Tactico/Productos_especifico/ReporteExcel/'.$this->uri->segment(4))."' class='icono icon-file-excel'>
                            Exportar Excel</a><span class='content_buscar'><i class='glyphicon glyphicon-search'></i>".form_input($buscar)."</span></div>" . "<div class='table-content'><div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div></div>";
                 $data['body'] = $table;
      }else {
          $data['body'] = $this->load->view('Tactico/productos_especifico_view', '',TRUE);
      }
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

    $objPHPExcel->getProperties()->setCreator("SICBAF")
                 ->setLastModifiedBy("SICBAF")
                 ->setTitle("Reporte productos por especifico.")
                 ->setSubject("Reporte productos por especifico.")
                 ->setDescription("Reporte productos por especifico.")
                 ->setKeywords("office PHPExcel php")
                 ->setCategory("Reporte productos por especifico.");

    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Especifico')
                 ->setCellValue('B1', 'Nombre especifico')
                 ->setCellValue('C1', 'Cantidad')
                 ->setCellValue('D1', 'Saldo');
        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($estilo_titulo);

    $registros = $this->Producto->productosEspecificoExcel($this->uri->segment(4));

    if (!($registros == FALSE)) {
      $i = 2;
      $total_cantidad=0;
      $total_saldo=0;
      foreach($registros as $pro) {

        $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A'.$i, $pro->id_especifico)
                     ->setCellValue('B'.$i, $pro->nombre_especifico)
                     ->setCellValue('C'.$i, $pro->cantidad)
                     ->setCellValue('D'.$i, '$'.number_format($pro->saldo,3));

        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($estilo_contenido);
        $total_cantidad+=$pro->cantidad;
        $total_saldo+=$pro->saldo;
        $i++;
      }
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, 'Total');
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':B'.$i);
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $total_cantidad);
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, '$'.number_format($total_saldo,3));
      $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($estilo_contenido);
    } else {
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:D2');
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "No se encontraron resultados");
      $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->applyFromArray($estilo_contenido);
    }

    foreach(range('A','D') as $columnID){
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    ob_end_clean();
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='productos_especifico.xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }
  }
  ?>
