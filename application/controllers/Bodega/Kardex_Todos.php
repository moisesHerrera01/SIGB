<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kardex_Todos extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->library(array('table','excel'));
    $this->load->helper(array('form','paginacion'));
    $this->load->model(array('Bodega/Kardex_model', 'Bodega/Kardex_saldo_model',
               'Bodega/detalleProducto_model', 'mtps/Seccion_model'));
  }


  public function RecibirTodo() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('fecha_inicio')!=NULL) {
      if($this->input->post('fecha_fin')==NULL){
        redirect('Bodega/Kardex_Todos/ReporteKardex/'.$this->input->post('fecha_inicio').'/'.$fecha_actual);
      }else{
        redirect('Bodega/Kardex_Todos/ReporteKardex/'.$this->input->post('fecha_inicio').'/'.$this->input->post('fecha_fin'));
      }
    } else {
        redirect('Bodega/Kardex_Todos/ReporteKardex/');
    }
  }

  public function ReporteKardex() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      setlocale(LC_MONETARY, 'en_US');
      setlocale(LC_TIME, 'en_US');

      $data['title'] = "Reporte Generación del Kardex";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $data['js'] = 'assets/js/validate/reporte/bodega/kardex_todos.js';
      $table = '';
      if (($this->uri->segment(4)) != '' ) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Fecha de transacción', 'Sección requisidora', 'Acción','Producto', 'Unidad Medida','Cantidad', 'Precio',
                                  'Numero Doc', 'Detalle', 'Existencia', 'Saldo');

        $num = '15';
        $registros_entrada = $this->Kardex_model->GeneracionKardexEntradaLimit($this->uri->segment(4),
        $this->uri->segment(5), $num, intval($this->uri->segment(6)));
        $registros_salida = $this->Kardex_model->GeneracionKardexSalidaLimit($this->uri->segment(4),
        $this->uri->segment(5),$num, intval($this->uri->segment(6)));
        $total=$this->Kardex_model->TotalDetalleProductoTodos($this->uri->segment(4),$this->uri->segment(5));
        $pagination = paginacion('index.php/Bodega/Kardex_Todos/ReporteKardex/'.$this->uri->segment(4).'/'.$this->uri->segment(5),
        intval($total->cantidad) , $num, 6);

        $registros = array();
        $aux = array();
        if (!($registros_entrada == FALSE)) {
          foreach($registros_entrada as $entrada) {
            array_push($aux, $entrada->id_kardex);
            array_push($registros, array(
              'id_kardex' => $entrada->id_kardex,
              'fecha' => $entrada->fecha_ingreso,
              'requisidor' => $this->Seccion_model->obtenerPorIdSeccion($entrada->id_seccion),
              'accion' => 'CARGO',
              'cantidad' => $entrada->cantidad,
              'precio' => $entrada->precio,
              'doc' => $entrada->numero_compromiso,
              'detalle' => $entrada->id_factura,
              'producto'=>$entrada->producto,
              'id'=>$entrada->id_detalleproducto,
              'existencia' => $entrada->existencia,
              'saldo' => $entrada->total,
              'unidad' => $entrada->unidad,
            ));
          }
        }

        if (!($registros_salida == FALSE)) {
          foreach($registros_salida as $salida) {
            array_push($aux, $salida->id_kardex);
            array_push($registros, array(
              'id_kardex' => $salida->id_kardex,
              'fecha' => $salida->fecha_ingreso,
              'requisidor' => $this->Seccion_model->obtenerPorIdSeccion($salida->id_seccion),
              'accion' => 'DESCARGO',
              'cantidad' => $salida->cantidad,
              'precio' => $salida->precio,
              'doc' => $salida->id_solicitud,
              'detalle' => $salida->id_solicitud,
              'producto'=>$salida->producto,
              'id'=>$salida->id_detalleproducto,
              'existencia' => $salida->existencia,
              'saldo' => $salida->total,
              'unidad' => $salida->unidad,
            ));
          }
        }
        array_multisort($aux, SORT_DESC, $registros);

        if (!empty($registros)) {
          $k = 0;
          foreach ($registros as $reg) {
            if (0 == $k) {
              $k = $this->Kardex_saldo_model->NumeroResgistros($reg['id_kardex']);

              $reg['cantidad'] = $this->Kardex_saldo_model->ObtenerExistenciaKardex($reg['id_kardex']);

              $fecha = array('data' => $reg['fecha'], 'rowspan' => $k);
              $requisidor = array('data' => $reg['requisidor'], 'rowspan' => $k);
              $accion = array('data' => $reg['accion'], 'rowspan' => $k);
              $producto = array('data' => $reg['producto'], 'rowspan' => $k);
              $cantidad = array('data' => $reg['cantidad'], 'rowspan' => $k);
              $precio = array('data' => $reg['precio'], 'rowspan' => $k);
              $doc = array('data' => $reg['doc'], 'rowspan' => $k);
              $detalle = array('data' => $reg['detalle'], 'rowspan' => $k);
              $unidad = array('data' => $reg['unidad'], 'rowspan' => $k);

              $this->table->add_row($fecha, $requisidor, $accion, $producto, $unidad, $cantidad,
                                    $precio,  $doc, $detalle, $reg['existencia'], number_format($reg['saldo'], 2));
              $k--;
            } else {
              $this->table->add_row($reg['existencia'], number_format($reg['saldo'], 2));
              $k--;
            }
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "10");
          $this->table->add_row($msg);
        }

        $table = "<div class='content_table'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".$this->uri->segment(4) . " - " . $this->uri->segment(5) ."</span></div>".
                    "<div class='limit-content'>" .
                      "<div class='exportar'><a href='".base_url('/index.php/Bodega/Kardex_Todos/ReporteKardexExcel/'
                      .$this->uri->segment(4).'/'.$this->uri->segment(5))."' class='icono icon-file-excel'>
                      Exportar Excel</a></div>" ."<div class='table-responsive'>". $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }

      $data['body'] = $this->load->view('Bodega/Reportes/kardex_todos_view', '', TRUE) .
                      "<br>" . $table;
      $this->load->view('base', $data);
    } else {
      redirect('login/index/forbidden');
    }
  }

  public function ReporteKardexExcel() {

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
    						 ->setTitle("Reporte de Inventario General PEPS.")
    						 ->setSubject("Reporte de Inventario General PEPS.")
    						 ->setDescription("Reporte de Inventario General PEPS.")
    						 ->setKeywords("office PHPExcel php")
    						 ->setCategory("Reporte de Inventario General PEPS.");
    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Fecha de transacción')
                 ->setCellValue('B1', 'Sección requisidora')
                 ->setCellValue('C1', 'Acción')
                 ->setCellValue('D1', 'Producto')
                 ->setCellValue('E1', 'Cantidad')
                 ->setCellValue('F1', 'Precio')
                 ->setCellValue('G1', 'Existencia')
                 ->setCellValue('H1', 'Saldo')
                 ->setCellValue('I1', 'Numero Doc');
    $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($estilo_titulo);

    setlocale(LC_MONETARY, 'en_US');
    setlocale(LC_TIME, 'en_US');
    $registros_entrada = $this->Kardex_model->GeneracionKardexEntrada($this->uri->segment(4),$this->uri->segment(5));
    $registros_salida = $this->Kardex_model->GeneracionKardexSalida($this->uri->segment(4),$this->uri->segment(5));
    $id_detalleproducto = $this->detalleProducto_model->obtenerIdDetalleProducto($this->uri->segment(4));

    $registros = array();
    $aux = array();
    if (!($registros_entrada == FALSE)) {
      foreach($registros_entrada as $entrada) {
        array_push($aux, $entrada->id_kardex);
        array_push($registros, array(
          'fecha' => $entrada->fecha_ingreso,
          'requisidor' => $this->Seccion_model->obtenerPorIdSeccion($entrada->id_seccion),
          'accion' => 'CARGO',
          'cantidad' => $entrada->cantidad,
          'precio' => $entrada->precio,
          'doc' => $entrada->numero_compromiso,
          'detalle' => $entrada->id_factura,
          'producto'=>$entrada->producto,
          'id'=>$entrada->id_detalleproducto,
          'existencia' => $entrada->existencia,
          'saldo' => $entrada->total,
        ));
      }
    }

    if (!($registros_salida == FALSE)) {
      foreach($registros_salida as $salida) {
        array_push($aux, $salida->id_kardex);
        array_push($registros, array(
          'fecha' => $salida->fecha_ingreso,
          'requisidor' => $this->Seccion_model->obtenerPorIdSeccion($salida->id_seccion),
          'accion' => 'DESCARGO',
          'cantidad' => $salida->cantidad,
          'precio' => $salida->precio,
          'doc' => $salida->id_solicitud,
          'detalle' => $salida->id_solicitud,
          'producto'=>$salida->producto,
          'id'=>$salida->id_detalleproducto,
          'existencia' => $salida->existencia,
          'saldo' => $salida->total,
        ));
      }
    }
    array_multisort($aux, SORT_DESC, $registros);

    if (!empty($registros)) {
      $i = 2;
      foreach ($registros as $reg) {
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $reg['fecha'])
                    ->setCellValue('B'.$i, $reg['requisidor'])
                    ->setCellValue('C'.$i, $reg['accion'])
                    ->setCellValue('D'.$i, $reg['producto'])
                    ->setCellValue('E'.$i, $reg['cantidad'])
                    ->setCellValue('F'.$i, $reg['precio'])
                    ->setCellValue('G'.$i, $reg['existencia'])
                    ->setCellValue('H'.$i, $reg['saldo'])
                    ->setCellValue('I'.$i, $reg['doc']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($estilo_contenido);
        $i++;
      }

      foreach(range('A','I') as $columnID){
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }
      $objPHPExcel->setActiveSheetIndex(0);

      header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header("Content-Disposition: attachment; filename='Kardex_Todos.xlsx'");
      header('Cache-Control: max-age=0');

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');

    }
  }

  public function RecibirResumen(){
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('fecha_inicio') != NULL &&  $this->input->post('fuente') != NULL) {
      if($this->input->post('fecha_fin') == NULL){
        $esp = ($this->input->post('especifico') == NULL) ? '0' : $this->input->post('especifico');
        redirect('Bodega/Kardex_Todos/kardexResumido/'.$this->input->post('fecha_inicio')
              .'/'. $esp .'/'.$this->input->post('fuente'));
      } else{
        $esp = ($this->input->post('especifico') == NULL) ? '0' : $this->input->post('especifico');
        redirect('Bodega/Kardex_Todos/kardexResumido/'.$this->input->post('fecha_inicio').'/'.$this->input->post('fecha_fin')
              .'/'. $esp .'/'.$this->input->post('fuente'));
      }
    } else {
        redirect('Bodega/Kardex_Todos/kardexResumido/');
    }
  }

  public function kardexResumido() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $this->load->model(array('Bodega/Especifico', 'Bodega/Fuentefondos_model'));
      $data1['title'] = "kardex Resumido";
      $data1['js'] = "assets/js/validate/reporte/bodega/kardex_resumen.js";
      $data1['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';

      if ($this->uri->segment(4) != '' && $this->uri->segment(5) != '' && $this->uri->segment(7) != '') {

        $num = 5;

        $kardex = $this->Kardex_model->obtenerKardexResumido($this->uri->segment(6), $this->uri->segment(7), $this->uri->segment(4), $this->uri->segment(5), $num, $this->uri->segment(8));
        $cant = $this->Kardex_model->totalKardexResumido($this->uri->segment(6), $this->uri->segment(7), $this->uri->segment(4), $this->uri->segment(5));
        $pagination = paginacion('index.php/Bodega/Kardex_Todos/kardexResumido/'.$this->uri->segment(4).
        '/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7),$cant,$num, '8');

        if (!($kardex == FALSE)) {
          $template = array(
              'table_open' => '<table class="table table-striped table-bordered">'
          );
          $this->table->set_template($template);

          foreach ($kardex as $value) {
            $kardex_prev =  $this->Kardex_saldo_model->obtenerAnteriorKardexSaldo($value->rango[0]->min, $value->id_detalleproducto);

            if ($kardex_prev) {
              $inicial = $this->Kardex_saldo_model->obtenerSaldosXKardex($kardex_prev);
            } else {
              $inicial = 0;
            }
            $final = $this->Kardex_saldo_model->obtenerSaldosXKardex($value->rango[0]->max);

            $num_ingreso = count($value->detalle_ingreso);
            $num_salida = count($value->detalle_salida);
            $num_inicial = count($inicial);
            $num_final = count($final);

            //obtener el mayor para iterar
            $mayor1 = ($num_ingreso > $num_salida) ? $num_ingreso : $num_salida;
            $mayor2 = ($num_inicial > $num_final) ? $num_inicial : $num_final;
            $total = ($mayor1 > $mayor2) ? $mayor1 : $mayor2;

            $this->table->add_row(array('data' => $value->id_especifico .' '. $value->nombre_producto, 'colspan' => "8"));
            $cell1 = array('data' => 'Inventario Inicial', 'colspan' => 2);
            $cell2 = array('data' => 'Ingresos', 'colspan' => 2);
            $cell3 = array('data' => 'Salidas', 'colspan' => 2);
            $cell4 = array('data' => 'Saldo Actual', 'colspan' => 2);
            $this->table->add_row($cell1, $cell2, $cell3, $cell4);
            $this->table->add_row("Cantidad", "Precio", "Cantidad", "Precio" , "Cantidad", "Precio", "Cantidad", "Precio");

            for ($i=0; $i < $total; $i++) {

              $data = array();
              if ($i < $num_inicial && $inicial != 0) {
                $data[] = $inicial[$i]->existencia;
                $data[] = $inicial[$i]->precio_unitario;
              } else {
                $data[] = "-";
                $data[] = "-";
              }

              if ($i < $num_ingreso) {
                $data[] = $value->detalle_ingreso[$i]->cantidad;
                $data[] = $value->detalle_ingreso[$i]->precio;
              } else {
                $data[] = "-";
                $data[] = "-";
              }

              if ($i < $num_salida) {
                $data[] = $value->detalle_salida[$i]->cantidad;
                $data[] = $value->detalle_salida[$i]->precio;
              } else {
                $data[] = "-";
                $data[] = "-";
              }

              if ($i < $num_final) {
                if ($final) {
                  $data[] = $final[$i]->existencia;
                  $data[] = $final[$i]->precio_unitario;
                } else {
                  $data[] = "-";
                  $data[] = "-";
                }
              } else {
                $data[] = "-";
                $data[] = "-";
              }

              $this->table->add_row($data);
            }
          }
          $titulo = ($this->uri->segment(6) != 0) ?   $this->Especifico->obtenerEspecifico($this->uri->segment(6)) : 'TODOS' ;
          $table = "<div class='content_table '>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".
                    $titulo .
                    "</span></div>".
                    "<div class='limit-content'>" .
                    "<div class='exportar icono'><a href='".base_url('/index.php/Bodega/Kardex_Todos/KardexResumidoExcel/'.$this->uri->segment(4).'/'
                    .$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>
                    Exportar Excel</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
        }
      }

      $data1['body'] = $this->load->view('Bodega/Reportes/kardex_resumido_view', '',TRUE) . "<br>" . $table;
      $this->load->view('base', $data1);
    } else {
      redirect('login/index/forbidden');
    }
  }

  public function KardexResumidoExcel() {
    $this->load->library(array('excel'));
    $this->load->model(array('Bodega/Especifico', 'Bodega/FuenteFondos_model'));

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
    						 ->setTitle("Reporte Kardex Resumido")
    						 ->setSubject("Reporte Kardex Resumido")
    						 ->setDescription("Reporte Kardex Resumido.")
    						 ->setKeywords("office PHPExcel php")
    						 ->setCategory("Reporte Kardex Resumido");

    $kardex = $this->Kardex_model->obtenerKardexResumido($this->uri->segment(6), $this->uri->segment(7), $this->uri->segment(4), $this->uri->segment(5));
    $j = 2;
    foreach ($kardex as $value) {
      $kardex_prev =  $this->Kardex_saldo_model->obtenerAnteriorKardexSaldo($value->rango[0]->min, $value->id_detalleproducto);

      if ($kardex_prev) {
        $inicial = $this->Kardex_saldo_model->obtenerSaldosXKardex($kardex_prev);
      } else {
        $inicial = 0;
      }
      $final = $this->Kardex_saldo_model->obtenerSaldosXKardex($value->rango[0]->max);

      $num_ingreso = count($value->detalle_ingreso);
      $num_salida = count($value->detalle_salida);
      $num_inicial = count($inicial);
      $num_final = count($final);

      //obtener el mayor para iterar
      $mayor1 = ($num_ingreso > $num_salida) ? $num_ingreso : $num_salida;
      $mayor2 = ($num_inicial > $num_final) ? $num_inicial : $num_final;
      $total = ($mayor1 > $mayor2) ? $mayor1 : $mayor2;

      $k = $j-1;
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$k, $value->nombre_producto);
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$k.':H'.$k);
      $objPHPExcel->getActiveSheet()->getStyle('A'.$k.':H'.$k)->applyFromArray($estilo_titulo);

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A'.$j, 'Inventario Inicial')
                   ->setCellValue('C'.$j, 'Ingresos')
                   ->setCellValue('E'.$j, 'Salidas')
                   ->setCellValue('G'.$j, 'Saldo Actual');
       $objPHPExcel->setActiveSheetIndex(0)
                    ->mergeCells('A'.$j.':B'.$j)
                    ->mergeCells('C'.$j.':D'.$j)
                    ->mergeCells('E'.$j.':F'.$j)
                    ->mergeCells('G'.$j.':H'.$j);

      $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':H'.$j)->applyFromArray($estilo_titulo);

      for ($i=0; $i < $total; $i++) {

        $data = array();
        if ($i < $num_inicial && $inicial != 0) {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($j + $i + 1), $inicial[$i]->existencia);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($j + $i + 1), $inicial[$i]->precio_unitario);
        } else {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($j + $i + 1), "-");
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($j + $i + 1), "-");
        }

        if ($i < $num_ingreso) {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($j + $i + 1), $value->detalle_ingreso[$i]->cantidad);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($j + $i + 1), $value->detalle_ingreso[$i]->precio);
        } else {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($j + $i + 1), "-");
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($j + $i + 1), "-");
        }

        if ($i < $num_salida) {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($j + $i + 1), $value->detalle_salida[$i]->cantidad);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($j + $i + 1), $value->detalle_salida[$i]->precio);
        } else {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($j + $i + 1), "-");
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($j + $i + 1), "-");
        }

        if ($i < $num_final) {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($j + $i + 1), $final[$i]->existencia);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($j + $i + 1), $final[$i]->precio_unitario);
        } else {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($j + $i + 1), "-");
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($j + $i + 1), "-");
        }
        $objPHPExcel->getActiveSheet()->getStyle('A'.($j + $i + 1).':H'.($j + $i + 1))->applyFromArray($estilo_contenido);
        $this->table->add_row($data);
      }
      $j = $j + $i + 2;
    }

    $objPHPExcel->setActiveSheetIndex(0);

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='kardex_resumen".$this->uri->segment(6).".xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }
}
?>
