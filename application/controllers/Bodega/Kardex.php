<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kardex extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form'));
    $this->load->model(array('Bodega/Factura_Model', 'Bodega/Kardex_model', 'Bodega/Detallefactura_Model',
    'Bodega/Detalle_solicitud_producto_model','Bodega/Solicitud_Model', 'Bodega/Fuentefondos_model', 'Bodega/Kardex_saldo_model'));
  }

  public function insertar(){

    $data = array(
      'controller' => $this->input->get('controller'),
      'id' => $this->input->get('id'),
    );

    print_r($data);
    if ($data['controller'] == "factura") {
      foreach ($this->Detallefactura_Model->obtenerDetalleFacturas($data['id']) as $detalle) {
        $datos = array(
          'id_detalleproducto' => $detalle->id_detalleproducto,
          'cantidad' => $detalle->cantidad,
          'precio' => $detalle->precio,
          'movimiento' => 'ENTRADA',
          'fecha_ingreso' => '',
          'id_fuentes'=> '',
        );

        foreach ($this->Factura_Model->obtenerTodaFactura($data['id']) as $factura) {
          $datos['fecha_ingreso'] = $factura->fecha_ingreso;
          $datos['id_fuentes']=$factura->id_fuentes;
        }
        $this->Kardex_model->insertarKardex($datos);
      }
    }

    redirect('/Bodega/factura/index/fact_liquidar');
  }

  public function insertarDescargo(){

    $data = array(
      'controller' => $this->input->get('controller'),
      'id' => $this->input->get('id'),
    );

    if ($data['controller'] == "retiro") {
      foreach ($this->Detalle_solicitud_producto_model->obtenerDetalleSolicitudProductosDescargados($data['id']) as $detalle) {
        $datos = array(
          'id_detalleproducto' => $detalle->id_detalleproducto,
          'cantidad' => $detalle->cantidad,
          'precio' => $detalle->precio,
          'movimiento' => 'SALIDA',
          'fecha_ingreso' => '',
          'id_fuentes'=>$detalle->id_fuentes,
        );

        foreach ($this->Solicitud_Model->obtenerTodaSolicitud($data['id']) as $sol) {
          $datos['fecha_ingreso'] = $sol->fecha_salida;
        }
        $this->Kardex_model->insertarKardex($datos);
      }
    }

    redirect('/Bodega/retiro/index/liquidar');
  }

  public function insertarDescargoSolicitud_retiro(){

    $data = array(
      'controller' => $this->input->get('controller'),
      'id' => $this->input->get('id'),
    );

    if ($data['controller'] == "solicitud_retiro") {
      foreach ($this->Detalle_solicitud_producto_model->obtenerDetalleSolicitudProductosDescargados($data['id']) as $detalle) {
        $datos = array(
          'id_detalleproducto' => $detalle->id_detalleproducto,
          'cantidad' => $detalle->cantidad,
          'precio' => $detalle->precio,
          'movimiento' => 'SALIDA',
          'fecha_ingreso' => '',
          'id_fuentes'=>$detalle->id_fuentes,
        );

        foreach ($this->Solicitud_Model->obtenerTodaSolicitud($data['id']) as $sol) {
          $datos['fecha_ingreso'] = $sol->fecha_salida;
        }
        $this->Kardex_model->insertarKardex($datos);
      }
    }

    redirect('/Bodega/Solicitud_retiro/index/liquidar');
  }

  public function RecibirProducto() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('fecha_inicio')!=NULL && $this->input->post('producto')!=NULL) {
      if($this->input->post('fecha_fin')==NULL){
        redirect('Bodega/Kardex/ReporteKardex/'.$this->input->post('fecha_inicio').'/'
        .$fecha_actual.'/'.$this->input->post('producto').'/'.$this->input->post('fuente'));
      }else{
        redirect('Bodega/Kardex/ReporteKardex/'.$this->input->post('fecha_inicio').'/'
        .$this->input->post('fecha_fin').'/'.$this->input->post('producto').'/'.$this->input->post('fuente'));
      }} else {
        redirect('Bodega/Kardex/ReporteKardex/');
    }
  }

  public function ReporteKardex() {

    $this->load->library(array('table'));
    $this->load->model(array('Bodega/detalleProducto_model', 'mtps/Seccion_model', 'Bodega/Producto'));
    $this->load->helper(array('paginacion'));

    setlocale(LC_MONETARY, 'en_US');
    setlocale(LC_TIME, 'en_US');

    $data['title'] = "Reporte Generación del Kardex";
    $table = '';
    if ($this->uri->segment(4) != '' && $this->uri->segment(5) != '' && $this->uri->segment(5) != '') {
      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('Fecha de transacción', 'Sección requisidora', 'Acción','Cantidad', 'Precio',
                                'Numero Doc', 'Detalle', 'Existencia', 'Saldo');

      $num = 10;
      $registros_entrada = $this->Kardex_model->GeneracionKardexProductoEntrada($this->uri->segment(4),
                            $this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7));

      $registros_salida = $this->Kardex_model->GeneracionKardexProductoSalida($this->uri->segment(4),
                            $this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7));

      $id_detalleproducto = $this->detalleProducto_model->obtenerIdDetalleProducto($this->uri->segment(6));

      $total_productos = $this->Kardex_model->TotalDetalleProducto($id_detalleproducto, $this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(7));

      $pagination = paginacion('index.php/Bodega/kardex/ReporteKardex/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.
            $this->uri->segment(6).'/'.$this->uri->segment(7), $total_productos->cantidad , $num, 8);

      $registros = array();
      $aux = array();
      if (!($registros_entrada == FALSE)) {
        foreach($registros_entrada as $entrada) {
          array_push($aux, $entrada->id_kardex);
          $factura = $this->Factura_Model->obtenerFacturaKardex($entrada->fecha_ingreso, $entrada->id_detalleproducto, $entrada->cantidad, $entrada->precio);
          array_push($registros, array(
            'id_kardex' => $entrada->id_kardex,
            'fecha' => $entrada->fecha_ingreso,
            'requisidor' => $this->Seccion_model->obtenerPorIdSeccion($factura->id_seccion),
            'accion' => 'CARGO',
            'cantidad' => $entrada->cantidad,
            'precio' => $entrada->precio,
            'doc' => $factura->numero_compromiso,
            'existencia' => $entrada->existencia,
            'saldo' => $entrada->total,
            'detalle' => $factura->id_factura
          ));
        }
      }

      if (!($registros_salida == FALSE)) {
        foreach($registros_salida as $salida) {
          array_push($aux, $salida->id_kardex);
          $solicitud = $this->Solicitud_Model->obtenerSolicitudKardex($salida->fecha_ingreso, $salida->id_detalleproducto, $salida->cantidad, $salida->precio);
          array_push($registros, array(
            'id_kardex' => $salida->id_kardex,
            'fecha' => $salida->fecha_ingreso,
            'requisidor' => $this->Seccion_model->obtenerPorIdSeccion($solicitud->id_seccion),
            'accion' => 'DESCARGO',
            'cantidad' => $salida->cantidad,
            'precio' => $salida->precio,
            'doc' => $solicitud->id_solicitud,
            'existencia' => $salida->existencia,
            'saldo' => $salida->total,
            'detalle' => $solicitud->id_solicitud
          ));
        }
      }

      array_multisort($aux, SORT_DESC, $registros);

      $registros = array_slice($registros, $this->uri->segment(8), $num);

      if (!empty($registros)) {
        $k = 0;
        while ($registro = current($registros)) {

          $detalle = array();
          if ($registro['accion'] == 'CARGO') {
            $detalle['data'] = '<a class="icono icon-detalle" href='.base_url('index.php/Bodega/Detallefactura/MostrarDetalleFactura/'.$registro['detalle']).' target="_blank"></a>';
          } elseif ($registro['accion'] == 'DESCARGO') {
            $detalle['data'] = '<a class="icono icon-detalle" href='.base_url('index.php/Bodega/Detalle_solicitud_producto/MostrarDetalleSolicitud/'.$registro['detalle']).' target="_blank"></a>';
          }

          if (0 == $k) {
            $k = $this->Kardex_saldo_model->NumeroResgistros($registro['id_kardex'], $this->uri->segment(7));

            $fecha = array('data' => $registro['fecha'], 'rowspan' => $k);
            $requisidor = array('data' => $registro['requisidor'], 'rowspan' => $k);
            $accion = array('data' => $registro['accion'], 'rowspan' => $k);
            $cantidad = array('data' => $registro['cantidad'], 'rowspan' => $k);
            $precio = array('data' => $registro['precio'], 'rowspan' => $k);
            $doc = array('data' => $registro['doc'], 'rowspan' => $k);
            $detalle['rowspan'] = $k;

            $this->table->add_row($fecha, $requisidor, $accion, $cantidad,
                                  $precio,  $doc, $detalle, $registro['existencia'], number_format($registro['saldo'], 2));
            $k--;
          } else {
            $this->table->add_row($registro['existencia'], number_format($registro['saldo'], 2));
            $k--;
          }

          next($registros);
        }

      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
        $this->table->add_row($msg);
      }

      $retiro = "";
      if ($this->uri->segment(8) != '' ) {
        $retiro = "<div class='content-btn-table'><a href=".base_url('/index.php/Bodega/Detalle_retiro/index/'.$this->uri->segment(8).'/'.$this->uri->segment(9)).
                  " class='btn btn-default'>Regresar al retiro</a></div>";
      }

      $table = "<div class='content_table'>".
              "<div class='limit-content-title'><span class='icono icon-table icon-title'> ". $this->Producto->obtenerProducto($this->uri->segment(6))."</span></div>".
              "<div class='limit-content'>" . "<div class='exportar'><a href='".
              base_url('/index.php/Bodega/Kardex/ReporteKardexExcel/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>Exportar Excel</a></div>".
              $retiro. "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
    }

    $data['body'] = $this->load->view('Bodega/Reportes/kardex_view', '', TRUE) .
                    "<br>" . $table;
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
  }

  public function ReporteKardexExcel() {

    $this->load->library(array('excel'));
    $this->load->model(array('Bodega/detalleProducto_model', 'mtps/Seccion_model'));

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
    						 ->setTitle("Reporte Kardex por producto")
    						 ->setSubject("Reporte Kardex por producto")
    						 ->setDescription("Reporte Kardex por producto.")
    						 ->setKeywords("office PHPExcel php")
    						 ->setCategory("Reporte Kardex por producto");
    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Fecha de transacción')
                 ->setCellValue('B1', 'Sección requisidora')
                 ->setCellValue('C1', 'Acción')
                 ->setCellValue('D1', 'Cantidad')
                 ->setCellValue('E1', 'Precio')
                 ->setCellValue('F1', 'Existencia')
                 ->setCellValue('G1', 'Saldo')
                 ->setCellValue('H1', 'Numero Doc');
    $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($estilo_titulo);

    setlocale(LC_MONETARY, 'en_US');
    setlocale(LC_TIME, 'en_US');

    $registros_entrada = $this->Kardex_model->GeneracionKardexProductoEntrada($this->uri->segment(4),
    $this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7));
    $registros_salida = $this->Kardex_model->GeneracionKardexProductoSalida($this->uri->segment(4),
    $this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7));
    $id_detalleproducto = $this->detalleProducto_model->obtenerIdDetalleProducto($this->uri->segment(4));

    $registros = array();
    $aux = array();
    if (!($registros_entrada == FALSE)) {
      foreach($registros_entrada as $entrada) {
        array_push($aux, $entrada->id_kardex);
        $factura = $this->Factura_Model->obtenerFacturaKardex($entrada->fecha_ingreso, $entrada->id_detalleproducto, $entrada->cantidad, $entrada->precio);
        array_push($registros, array(
          'fecha' => $entrada->fecha_ingreso,
          'requisidor' => $this->Seccion_model->obtenerPorIdSeccion($factura->id_seccion),
          'accion' => 'CARGO',
          'cantidad' => $entrada->cantidad,
          'precio' => $entrada->precio,
          'doc' => $factura->numero_compromiso,
          'existencia' => $entrada->existencia,
          'saldo' => $entrada->total,
          'detalle' => $factura->id_factura
        ));
      }
    }

    if (!($registros_salida == FALSE)) {
      foreach($registros_salida as $salida) {
        array_push($aux, $salida->id_kardex);
        $solicitud = $this->Solicitud_Model->obtenerSolicitudKardex($salida->fecha_ingreso, $salida->id_detalleproducto, $salida->cantidad, $salida->precio);
        array_push($registros, array(
          'fecha' => $salida->fecha_ingreso,
          'requisidor' => $this->Seccion_model->obtenerPorIdSeccion($solicitud->id_seccion),
          'accion' => 'DESCARGO',
          'cantidad' => $salida->cantidad,
          'precio' => $salida->precio,
          'doc' => $solicitud->id_solicitud,
          'existencia' => $salida->existencia,
          'saldo' => $salida->total,
          'detalle' => $solicitud->id_solicitud
        ));
      }
    }

    array_multisort($aux, SORT_DESC, $registros);

    if (!empty($registros)) {
      $existencia = 0;
      $saldo = 0.0;
      $i = 2;
      while ($registro = current($registros)) {

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $registro['fecha'])
                    ->setCellValue('B'.$i, $registro['requisidor'])
                    ->setCellValue('C'.$i, $registro['accion'])
                    ->setCellValue('D'.$i, $registro['cantidad'])
                    ->setCellValue('E'.$i, $registro['precio'])
                    ->setCellValue('F'.$i, $registro['existencia'])
                    ->setCellValue('G'.$i, number_format($registro['saldo'], 2))
                    ->setCellValue('H'.$i, $registro['doc']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($estilo_contenido);

        $i++;
        next($registros);
      }

      foreach(range('A','H') as $columnID){
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }

      $objPHPExcel->setActiveSheetIndex(0);

      header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header("Content-Disposition: attachment; filename='kardex.xlsx'");
      header('Cache-Control: max-age=0');

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');

    }

  }

  public function RecibirGeneral() {
    if (($this->input->post()) != '' ) {
      redirect('Bodega/Kardex/ReporteGeneral/'.$this->input->post('fuente').'/'.$this->input->post('fechaMin').'/'.$this->input->post('fechaMax') );
    } else {
      redirect('Bodega/Kardex/ReporteGeneral/');
    }
  }

  public function ReporteGeneral() {

    $this->load->library(array('table'));
    $this->load->helper(array('paginacion'));

    $data['title'] = "2-Reporte de Inventario General";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));

    $table = '';
    if ($this->uri->segment(4) != '') {

      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('Numero Objeto', 'Nombre Objeto', 'Numero Producto','Nombre Producto', 'Unidad Medida',
                                'Existencia', 'Precio Unitario', 'Subtotal');

      $num = 10;
      $detalles = $this->Kardex_model->ObtenerProductosInventario($this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6), $num, $this->uri->segment(7));
      $pagination = '';

      if (count($detalles['cuenta']) > 0) {
        $pagination = paginacion('index.php/Bodega/Kardex/ReporteGeneral/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/' ,
                      count($detalles['cuenta']), $num, '7');

        foreach ($detalles['registros'] as $detalle) {
          $inventario = $this->Kardex_model->ObtenerInventarioGeneral($detalle->id_producto, $this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6));
          if (!is_null($inventario)) {
            foreach ($inventario as $row) {
              $subtotal = $row['precio'] * $row['existencia'];
              $this->table->add_row($row['id_especifico'], $row['nombre_especifico'], $row['id_producto'], $row['nombre_producto'],
                                    $row['unidad_medida'], $row['existencia'], $row['precio'], number_format($subtotal, 3));
            }
          }
        }
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "8");
        $this->table->add_row($msg);
      }

      $table = "<div class='content_table'>" .
              "<div class='limit-content-title'><span class='icono icon-table icon-title'> ". $this->Fuentefondos_model->obtenerFuente($this->uri->segment(4)). " " . $this->uri->segment(5) ." - ". $this->uri->segment(6) ."</span></div>".
              "<div class='limit-content'>" .
              "<div class='exportar'>
                 <a href='".base_url('/index.php/Bodega/Kardex/ReporteGeneralExcel/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6))."'
                 class='icono icon-file-excel'> Exportar Excel</a>
              </div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";

    }

    $data['body'] =  $this->load->view('Bodega/Reportes/filtroFuentes_view', array('url' => '/Bodega/kardex/RecibirGeneral','title'=>$data['title']),TRUE) . "<br>" . $table;

    $this->load->view('base', $data);
  }

  public function ReporteGeneralExcel() {

    $this->load->library(array('excel'));
    $this->load->model(array('Bodega/detalleProducto_model'));

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
    						 ->setTitle("Reporte de Inventario General")
    						 ->setSubject("Reporte de Inventario General")
    						 ->setDescription("Reporte de Inventario General.")
    						 ->setKeywords("office PHPExcel php")
    						 ->setCategory("Reporte de Inventario General");
    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Numero Objeto')
                 ->setCellValue('B1', 'Nombre Objeto')
                 ->setCellValue('C1', 'Numero Producto')
                 ->setCellValue('D1', 'Nombre Producto')
                 ->setCellValue('E1', 'Unidad Medida')
                 ->setCellValue('F1', 'Existencia')
                 ->setCellValue('G1', 'Precio Unitario')
                 ->setCellValue('H1', 'Subtotal');
    $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($estilo_titulo);

    $detalles = $this->Kardex_model->ObtenerProductosInventario($this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6));
    if ($detalles['cuenta'] > 0) {
      $i = 2;
      foreach ($detalles['registros'] as $detalle) {
        $inventario = $this->Kardex_model->ObtenerInventarioGeneral($detalle->id_producto, $this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6));
        if (!is_null($inventario)) {
          foreach ($inventario as $row) {
            $subtotal = $row['precio'] * $row['existencia'];
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $row['id_especifico'])
                        ->setCellValue('B'.$i, $row['nombre_especifico'])
                        ->setCellValue('C'.$i, $row['id_producto'])
                        ->setCellValue('D'.$i, $row['nombre_producto'])
                        ->setCellValue('E'.$i, $row['unidad_medida'])
                        ->setCellValue('F'.$i, $row['existencia'])
                        ->setCellValue('G'.$i, $row['precio'])
                        ->setCellValue('H'.$i, $subtotal);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($estilo_contenido);
            $i++;
          }
        }
      }
    }

    foreach(range('A','H') as $columnID){
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='General_inventario.xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }
}
?>
