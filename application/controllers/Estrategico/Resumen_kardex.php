<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resumen_kardex extends CI_Controller {

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

  public function RecibirResumen(){
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('fecha_inicio') != NULL &&  $this->input->post('fuente') != NULL) {
      if($this->input->post('fecha_fin') == NULL){
        $esp = ($this->input->post('especifico') == NULL) ? '0' : $this->input->post('especifico');
        redirect('Estrategico/Resumen_kardex/kardexResumido/'.$this->input->post('fecha_inicio')
              .'/'. $esp .'/'.$this->input->post('fuente'));
      } else{
        $esp = ($this->input->post('especifico') == NULL) ? '0' : $this->input->post('especifico');
        redirect('Estrategico/Resumen_kardex/kardexResumido/'.$this->input->post('fecha_inicio').'/'.$this->input->post('fecha_fin')
              .'/'. $esp .'/'.$this->input->post('fuente'));
      }
    } else {
        redirect('Estrategico/Resumen_kardex/kardexResumido/');
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
          $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);

        $num = 5;
        $segmento=8;

        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
            $kardex = $this->Kardex_model->buscarKardexResumido($this->uri->segment(6), $this->uri->segment(7), $this->uri->segment(4),
             $this->uri->segment(5), $this->input->post('busca'));
            $cant = count($kardex);
          } else {
            $kardex = $this->Kardex_model->obtenerKardexResumido($this->uri->segment(6), $this->uri->segment(7), $this->uri->segment(4),
             $this->uri->segment(5), $num, $this->uri->segment(8));
            $cant = $this->Kardex_model->totalKardexResumido($this->uri->segment(6), $this->uri->segment(7), $this->uri->segment(4),
            $this->uri->segment(5));
          }
        } else {
          $kardex = $this->Kardex_model->obtenerKardexResumido($this->uri->segment(6), $this->uri->segment(7), $this->uri->segment(4),
           $this->uri->segment(5), $num, $this->uri->segment(8));
          $cant = $this->Kardex_model->totalKardexResumido($this->uri->segment(6), $this->uri->segment(7), $this->uri->segment(4),
           $this->uri->segment(5));
        }

        $pagination = paginacion('index.php/Estrategico/Resumen_kardex/kardexResumido/'.$this->uri->segment(4).
        '/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7),$cant,$num, '8');

        if (!($kardex == FALSE)) {
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
              $this->table->add_row(array('data' => $value->id_especifico .' '. $value->nombre_producto, 'colspan' => "8"));
              $cell1 = array('data' => 'Inventario Inicial', 'colspan' => 2);
              $cell2 = array('data' => 'Ingresos', 'colspan' => 2);
              $cell3 = array('data' => 'Salidas', 'colspan' => 2);
              $cell4 = array('data' => 'Saldo Actual', 'colspan' => 2);
              $this->table->add_row($cell1, $cell2, $cell3, $cell4);
              $this->table->add_row("Cantidad", "Precio", "Cantidad", "Precio" , "Cantidad", "Precio", "Cantidad", "Precio");
              $this->table->add_row($data);
            }
          }
          }else {
            $this->table->set_template($template);
              $msg = array('data' => "No se encontraron resultados", 'colspan' => "8");
            $this->table->add_row($msg);
          }
          if ($this->input->is_ajax_request()) {
            echo "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
            return false;
          }

                    // paginacion del header
                    $pagaux = $cant / $num;

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
                      'url' => 'index.php/Estrategico/Resumen_kardex/kardexResumido/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7).'/'
                    );

                    $titulo = ($this->uri->segment(6) != 0) ?   $this->Especifico->obtenerEspecifico($this->uri->segment(6)) : 'TODOS' ;
                    $table =  "<div class='content_table '>" .
                              "<div class='limit-content-title'>".
                                "<div class='title-reporte'>".
                                  "Reporte de resumen de kardex.".
                                "</div>".
                                "<div class='title-header'>
                                  <ul>
                                    <li>Fecha emisión: ".date('d/m/Y')."</li>
                                    <li>Nombre la compañia: MTPS</li>
                                    <li>N° pagina: ". $pag .'/'. $pags ."</li>
                                    <li>Nombre pantalla:</li>
                                    <li>Usuario: ".$USER['nombre_completo']."</li>
                                    <br />
                                    <li>Parametros: ".$this->Fuentefondos_model->obtenerFuente($this->uri->segment(7)). " " .$titulo." ". $this->uri->segment(4) . " - " . $this->uri->segment(5)."</li>
                                  </ul>
                                </div>".
                              "</div>".
                              "<div class='limit-content'>" .
                              "<div class='exportar'><a href='".base_url('/index.php/Estrategico/Resumen_kardex/kardexResumidoExcel/'.$this->uri->segment(4).'/'
                              .$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>
                              Exportar Excel</a><span class='content_buscar'><i class='glyphicon glyphicon-search'></i>".form_input($buscar)."</span></div>" . "<div class='table-content'><div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div></div>";
                    $data1['body'] = $table;

      }else {
          $data1['body'] = $this->load->view('Estrategico/resumen_kardex_view','', TRUE);
      }
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
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$k, $value->id_especifico.' '.$value->nombre_producto);
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
      $s=$j+1;

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A'.$s, 'Cantidad')
                   ->setCellValue('B'.$s, 'Precio')
                   ->setCellValue('C'.$s, 'Cantidad')
                   ->setCellValue('D'.$s, 'Precio')
                   ->setCellValue('E'.$s, 'Cantidad')
                   ->setCellValue('F'.$s, 'Precio')
                   ->setCellValue('G'.$s, 'Cantidad')
                   ->setCellValue('H'.$s, 'Precio');

      $objPHPExcel->getActiveSheet()->getStyle('A'.$s.':H'.$s)->applyFromArray($estilo_contenido);

      for ($i=0; $i < $total; $i++) {

        $data = array();
        if ($i < $num_inicial && $inicial != 0) {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($j + $i + 2), 'Cantidad');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($j + $i + 2), 'precio');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($j + $i + 2), $inicial[$i]->existencia);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($j + $i + 2), $inicial[$i]->precio_unitario);
        } else {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($j + $i + 2), 'Cantidad');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($j + $i + 2), 'precio');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($j + $i + 2), "-");
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($j + $i + 2), "-");
        }

        if ($i < $num_ingreso) {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($j + $i + 2), 'Cantidad');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($j + $i + 2), 'precio');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($j + $i + 2), $value->detalle_ingreso[$i]->cantidad);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($j + $i + 2), $value->detalle_ingreso[$i]->precio);
        } else {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($j + $i + 2), 'Cantidad');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($j + $i + 2), 'precio');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($j + $i + 2), "-");
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.($j + $i + 2), "-");
        }

        if ($i < $num_salida) {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($j + $i + 2), 'Cantidad');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($j + $i + 2), 'precio');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($j + $i + 2), $value->detalle_salida[$i]->cantidad);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($j + $i + 2), $value->detalle_salida[$i]->precio);
        } else {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($j + $i + 2), 'Cantidad');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($j + $i + 2), 'precio');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($j + $i + 2), "-");
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($j + $i + 2), "-");
        }

        if ($i < $num_final) {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($j + $i + 2), 'Cantidad');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($j + $i + 2), 'precio');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($j + $i + 2), $final[$i]->existencia);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($j + $i + 2), $final[$i]->precio_unitario);
        } else {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($j + $i + 2), 'Cantidad');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($j + $i + 2), 'precio');
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($j + $i + 2), "-");
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($j + $i + 2), "-");
        }
        $objPHPExcel->getActiveSheet()->getStyle('A'.($j + $i + 2).':H'.($j + $i + 2))->applyFromArray($estilo_contenido);
        $this->table->add_row($data);
      }
      $j = $j + $i + 3;
    }

    $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='kardex_resumen".$this->uri->segment(6).".xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }
}
?>
