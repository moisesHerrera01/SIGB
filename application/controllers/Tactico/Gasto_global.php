<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class Gasto_global extends CI_Controller {

    public function __construct() {
      parent::__construct();
      if($this->session->userdata('logged_in') == FALSE){
        redirect('login/index/error_no_autenticado');
      }
      $this->load->library(array('table','excel'));
      $this->load->helper(array('form','paginacion'));
      $this->load->model(array('Bodega/Detalle_solicitud_producto_model', 'Bodega/Producto','Bodega/Solicitud_Model',
      'Bodega/Fuentefondos_model','Bodega/UnidadMedida', 'Bodega/Kardex_model','Bodega/Especifico'));
    }

  public function RecibirGastos() {
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
        redirect('Tactico/Gasto_global/reporteGastoSeccion/'.$this->input->post('fecha_inicio').'/'
        .$fecha_actual.'/'.$this->input->post('seccion'));
      }else{
        redirect('Tactico/Gasto_global/reporteGastoSeccion/'.$this->input->post('fecha_inicio').'/'
        .$this->input->post('fecha_fin').'/'.$this->input->post('seccion'));
      }} else {
        redirect('Tactico/Gasto_global/reporteGastoSeccion/');
    }
  }

  public function reporteGastoSeccion(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $this->load->model(array('mtps/Seccion_model'));
      $data['title'] = "Gasto Global";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $data['js'] = 'assets/js/validate/reporte/bodega/gasto_seccion.js';
      $table = '';
      if (($this->uri->segment(4))!=NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Especifico','Nombre especifico','Cantidad de solicitudes','Cantidad de productos','Sub total');

        $num = '10';
        $segmento = 7;
                $seccion = ($this->uri->segment(6)==NULL) ? 0 : $this->uri->segment(6);

        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
            $registros = $this->Detalle_solicitud_producto_model->buscarProductosSeccion($this->uri->segment(4),
            $this->uri->segment(5),$seccion,$this->input->post('busca'));
            $total = count($registros);
          } else {
            $registros = $this->Detalle_solicitud_producto_model->obtenerProductosSeccion($this->uri->segment(4),
            $this->uri->segment(5),$seccion,$num, $this->uri->segment(7));
            $total = $this->Detalle_solicitud_producto_model->obtenerProductosSeccionTotal($this->uri->segment(4),
            $this->uri->segment(5),$seccion);
          }
        } else {
          $registros = $this->Detalle_solicitud_producto_model->obtenerProductosSeccion($this->uri->segment(4),
          $this->uri->segment(5),$seccion,$num, $this->uri->segment(7));
          $total = $this->Detalle_solicitud_producto_model->obtenerProductosSeccionTotal($this->uri->segment(4),
          $this->uri->segment(5),$seccion);
        }

        $pagination = paginacion('index.php/Tactico/Gasto_global/reporteGastoSeccion/'.$this->uri->segment(4).
        '/'.$this->uri->segment(5).'/'.$seccion.'/',$total,$num, '7');
        if (!($registros == FALSE)) {
          $i = 1;
          $total=0;
          foreach($registros as $pro) {
            $this->table->add_row($pro->id_especifico,$pro->nombre_especifico,$pro->solicitudes,$pro->cantidad,'$'.number_format($pro->total,3));
            $total+=$pro->total;
            $i++;
          }
          $msg = array('data' => "Total:", 'colspan' => "4");
          $this->table->add_row($msg,  '$'.number_format($total, 3));
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
          $this->table->add_row($msg);
        }

        if ($this->input->is_ajax_request()) {
          echo "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
          return false;
        }

                 // paginacion del header
                 $pagaux = $total / $num;

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
                   'placeholder' => 'BUSCAR POR PRODUCTO',
                   'class' => 'form-control',
                   'autocomplete' => 'off',
                   'id' => 'buscar',
                   'url' => 'index.php/Tactico/Gasto_global/reporteGastoSeccion/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$seccion.'/'
                 );

                 $seccion = ($this->uri->segment(6) != 0) ?   $this->Solicitud_Model->obtenerSeccion($this->uri->segment(6)) : 'N/E' ;
                 $especifico = ($this->uri->segment(7) != 0) ?   $this->Especifico->obtenerEspecifico($this->uri->segment(7)) : 'N/E' ;
                 $table =  "<div class='content_table '>" .
                           "<div class='limit-content-title'>".
                             "<div class='title-reporte'>".
                               "Reporte gasto global especifico por sección.".
                             "</div>".
                             "<div class='title-header'>
                               <ul>
                                 <li>Fecha emisión: ".date('d/m/Y')."</li>
                                 <li>Nombre la compañia: MTPS</li>
                                 <li>N° pagina: ". $pag .'/'. $pags ."</li>
                                 <li>Nombre pantalla:</li>
                                 <li>Usuario: ".$USER['nombre_completo']."</li>
                                 <br />
                                 <li>Parametros: ".$seccion." ". $this->uri->segment(4) . " - " . $this->uri->segment(5)."</li>
                               </ul>
                             </div>".
                           "</div>".
                           "<div class='limit-content'>" .
                           "<div class='exportar'><a href='".base_url('/index.php/Tactico/Gasto_global/ReporteExcel/'.$this->uri->segment(4).'/'
                           .$this->uri->segment(5).'/'.$this->uri->segment(6))."' class='icono icon-file-excel'>
                           Exportar Excel</a><span class='content_buscar'><i class='glyphicon glyphicon-search'></i>".form_input($buscar)."</span></div>" . "<div class='table-content'><div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div></div>";
                 $data['body'] = $table;
      }else {
          $data['body'] = $this->load->view('Tactico/gasto_global_view', '',TRUE);
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
                 ->setTitle("Reporte Version Sistema Operativo.")
                 ->setSubject("Reporte Version Sistema Operativo.")
                 ->setDescription("Reporte Version Sistema Operativo.")
                 ->setKeywords("office PHPExcel php")
                 ->setCategory("Reporte Version Sistema Operativo.");

    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Especifico')
                 ->setCellValue('B1', 'Nombre especifico')
                 ->setCellValue('C1', 'Cantidad de solicitudes')
                 ->setCellValue('D1', 'Cantidad de productos')
                 ->setCellValue('E1', 'Sub total');

    $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($estilo_titulo);

    $seccion = ($this->uri->segment(6)==NULL) ? 0 : $this->uri->segment(6);
    $registros = $this->Detalle_solicitud_producto_model->obtenerProductosSeccionTodo($this->uri->segment(4), $this->uri->segment(5),
    $seccion,$this->uri->segment(7));

    if (!($registros == FALSE)) {
      $i = 2;
      $total=0;
      foreach($registros as $pro) {

        $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A'.$i, $pro->id_especifico)
                     ->setCellValue('B'.$i, $pro->nombre_especifico)
                     ->setCellValue('C'.$i, $pro->solicitudes)
                     ->setCellValue('D'.$i, $pro->cantidad)
                     ->setCellValue('E'.$i, '$'.number_format($pro->total,3));
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($estilo_contenido);
        $total+=$pro->total;
        $i++;
      }
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, 'Total');
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':D'.$i);
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, '$'.number_format($total,3));
      $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($estilo_contenido);
    } else {
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:E2');
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "No se encontraron resultados");
      $objPHPExcel->getActiveSheet()->getStyle('A2:I2')->applyFromArray($estilo_contenido);
    }

    foreach(range('A','E') as $columnID){
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    ob_end_clean();
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='reporte_gasto_seccion.xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }
  }
  ?>
