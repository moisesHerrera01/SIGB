<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Depreciacion_cuenta_contable extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'paginacion', 'form'));
    $this->load->library(array('table'));
    $this->load->model(array('ActivoFijo/Cuenta_contable_model'));
  }

  public function RecibirDatos() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('cuenta')!=NULL && $this->input->post('fuente')!=NULL && $this->input->post('fecha')!=NULL) {
          redirect('ActivoFijo/Reportes/Depreciacion_cuenta_contable/reporte/'.$this->input->post('cuenta') . '/' . $this->input->post('fuente') . '/' .$this->input->post('fecha'));
        } else {
          redirect('ActivoFijo/Reportes/Depreciacion_cuenta_contable/reporte/');
      }
    } else {
      redirect('login');
    }
  }

  public function Reporte() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "8- Reporte Depreciacion por Cuenta Contable";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';
      if ($this->uri->segment(5) != '' && $this->uri->segment(6) != '' && $this->uri->segment(7) != '') {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );

        $cuenta = $this->Cuenta_contable_model->obtenerCuenta($this->uri->segment(5));

        $this->table->set_template($template);
        $num = '3';
        $registros = $this->Cuenta_contable_model->depreciacionCuentaContable($this->uri->segment(5), $this->uri->segment(6), $num, $this->uri->segment(8));
        $total = $this->Cuenta_contable_model->totalDepreciacionCuentaContable($this->uri->segment(5), $this->uri->segment(6))->total;
        $pagination = paginacion('index.php/ActivoFijo/Reportes/Depreciacion_cuenta_contable/Reporte/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7)
                    ,$total, $num, '8');

        $head = 0;

        if (!($registros == FALSE)) {
          $i = $this->uri->segment(8) + 1;
          foreach($registros as $bien) {

            $final = $bien->precio_unitario * $bien->porcentaje_depreciacion;
            $depreciar = $bien->precio_unitario - $final;
            $acumulada = 0;
            $libro = 0;

            $row = array($i, $bien->descripcion, $bien->nombre_marca, $bien->modelo, $bien->serie, $bien->codigo_anterior, $bien->codigo,
                   $bien->nombre_oficina, $bien->primer_nombre ." ". $bien->primer_apellido ." ".$bien->nr, $bien->nombre_doc_ampara ." ". $bien->documento,
                   $bien->fecha_adquisicion, "$".$bien->precio_unitario, "$".$final, "$".$depreciar);

           $head = array('#', 'Descripcion', 'Marca', 'Modelo', 'Serie/Chasis/Placa', 'Codigo anterior', 'Codigo actual','Oficina', 'Empleado', 'Documento',
                     'Fecha Adquisicion', 'Precio', 'Valor residual', 'Valor a Depreciar');

            $fecha1 = new DateTime($bien->fecha_adquisicion);
            $fecha2 = new DateTime($this->uri->segment(7));
            $interval = $fecha1->diff($fecha2);

            $dias = $interval->format('%a');
            $pos = $interval->format('%R');

            if ($pos == '+') {
              if ($dias > $cuenta->vida_util * 365) {
                $dias = $cuenta->vida_util * 365;
              }
            }

            $dep_diaria = (($bien->precio_unitario - $final)/$cuenta->vida_util)/365;
            $fecha = substr($bien->fecha_adquisicion, -6);
            $anio_uno = substr($bien->fecha_adquisicion, 0, 4);

            for ($i=$anio_uno; $i < $anio_uno + $cuenta->vida_util + 1; $i++) {

              $head[] = $i;

              if ($pos == '+') {

                if ($dias > 0) {

                  if ($dias > 365) {

                    if ($i == $anio_uno) {

                      $fecha1 = new DateTime($i.$fecha);
                      $fecha2 = new DateTime($i."-12-31");
                      $interval = $fecha1->diff($fecha2);

                      $aux_d = $interval->format('%a');

                      if ($aux_d != 0) {

                        $dias = $dias - $aux_d;
                        $dep = $dep_diaria * $aux_d;

                      } else {
                        $dep = 0;
                      }

                    } else {

                      $dias = $dias - 365;
                      $dep = $dep_diaria * 365;

                    }

                  } else {

                    $dep = $dep_diaria * $dias;
                    $dias = 0;

                  }

                } else {

                  $dep = 0;

                }

              } else {

                $dep = 0;

              }

              $row[] = '$'.$dep;

              $acumulada = $acumulada + $dep;
            }

            $libro = $bien->precio_unitario - $acumulada;

            array_push($head, 'Depreciacion acumulada', 'Valor en Libros');
            array_push($row, '$'.$acumulada, '$'.$libro);

            $this->table->add_row($head);
            $this->table->add_row($row);
            $msg = array('data' => "", 'colspan' => count($head));
            $this->table->add_row($msg);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => count($head));
          $this->table->add_row($msg);
        }

        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'>Depreciacion Cuenta Contable</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar'>
                  <a href='".base_url('/index.php/ActivoFijo/Reportes/Depreciacion_cuenta_contable/ReporteExcel/'.$this->uri->segment(5).'/'.
                        $this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>
                  Exportar Excel</a>&nbsp;
                  <a href='".base_url('/index.php/ActivoFijo/Reportes/Depreciacion_cuenta_contable/ImprimirReporte/'.$this->uri->segment(5).'/'.
                        $this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-printer' target='_blank'>
                  Imprimir</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }

      $data['body'] = $this->load->view('ActivoFijo/Reportes/depreciacion_cuenta_contable_view', '',TRUE) . "<br>" . $table;
      $this->load->view('base', $data);

    } else {
      redirect('login/index/forbidden');
    }
  }

  public function ImprimirReporte() {
    $USER = $this->session->userdata('logged_in');
    if ($USER) {
      if ($this->uri->segment(5) != NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);

        $cuenta = $this->Cuenta_contable_model->obtenerCuenta($this->uri->segment(5));
        $total = $this->Cuenta_contable_model->totalDepreciacionCuentaContable($this->uri->segment(5), $this->uri->segment(6))->total;
        $registros = $this->Cuenta_contable_model->depreciacionCuentaContable($this->uri->segment(5), $this->uri->segment(6), $total, 0);

        $head = 0;

        if (!($registros == FALSE)) {
          $i = $this->uri->segment(8) + 1;
          foreach($registros as $bien) {

            $final = $bien->precio_unitario * $bien->porcentaje_depreciacion;
            $depreciar = $bien->precio_unitario - $final;
            $acumulada = 0;
            $libro = 0;

            $row = array($i, $bien->descripcion, $bien->nombre_marca, $bien->modelo, $bien->serie, $bien->codigo_anterior, $bien->codigo,
                   $bien->nombre_oficina, $bien->primer_nombre ." ". $bien->primer_apellido ." ".$bien->nr, $bien->nombre_doc_ampara ." ". $bien->documento,
                   $bien->fecha_adquisicion, "$".$bien->precio_unitario, "$".$final, "$".$depreciar);

           $head = array('#', 'Descripcion', 'Marca', 'Modelo', 'Serie/Chasis/Placa', 'Codigo anterior', 'Codigo actual','Oficina', 'Empleado', 'Documento',
                     'Fecha Adquisicion', 'Precio', 'Valor residual', 'Valor a Depreciar');

            $fecha1 = new DateTime($bien->fecha_adquisicion);
            $fecha2 = new DateTime($this->uri->segment(7));
            $interval = $fecha1->diff($fecha2);

            $dias = $interval->format('%a');
            $pos = $interval->format('%R');

            if ($pos == '+') {
              if ($dias > $cuenta->vida_util * 365) {
                $dias = $cuenta->vida_util * 365;
              }
            }

            $dep_diaria = (($bien->precio_unitario - $final)/$cuenta->vida_util)/365;
            $fecha = substr($bien->fecha_adquisicion, -6);
            $anio_uno = substr($bien->fecha_adquisicion, 0, 4);

            for ($i=$anio_uno; $i < $anio_uno + $cuenta->vida_util + 1; $i++) {

              $head[] = $i;

              if ($pos == '+') {

                if ($dias > 0) {

                  if ($dias > 365) {

                    if ($i == $anio_uno) {

                      $fecha1 = new DateTime($i.$fecha);
                      $fecha2 = new DateTime($i."-12-31");
                      $interval = $fecha1->diff($fecha2);

                      $aux_d = $interval->format('%a');

                      if ($aux_d != 0) {

                        $dias = $dias - $aux_d;
                        $dep = $dep_diaria * $aux_d;

                      } else {
                        $dep = 0;
                      }

                    } else {

                      $dias = $dias - 365;
                      $dep = $dep_diaria * 365;

                    }

                  } else {

                    $dep = $dep_diaria * $dias;
                    $dias = 0;

                  }

                } else {

                  $dep = 0;

                }

              } else {

                $dep = 0;

              }

              $row[] = '$'.$dep;

              $acumulada = $acumulada + $dep;
            }

            $libro = $bien->precio_unitario - $acumulada;

            array_push($head, 'Depreciacion acumulada', 'Valor en Libros');
            array_push($row, '$'.$acumulada, '$'.$libro);

            $this->table->add_row($head);
            $this->table->add_row($row);
            $msg = array('data' => "", 'colspan' => count($head));
            $this->table->add_row($msg);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => count($head));
          $this->table->add_row($msg);
        }

        $data = array(
          'table' => $this->table->generate(),
          'title' => '8-Reporte Depreciacion por Cuenta Contable'
        );
        $this->load->view('ActivoFijo/Reportes/imprimir_reporte_view', $data);
      }
    } else {
      redirect('login');
    }
  }

  public function ReporteExcel() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
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
                   ->setTitle("Reporte Depreciacion Cuenta Contable.")
                   ->setSubject("Reporte Depreciacion Cuenta Contable.")
                   ->setDescription("Reporte Depreciacion Cuenta Contable.")
                   ->setKeywords("office PHPExcel php")
                   ->setCategory("Reporte Depreciacion Cuenta Contable.");

      $cuenta = $this->Cuenta_contable_model->obtenerCuenta($this->uri->segment(5));
      $total = $this->Cuenta_contable_model->totalDepreciacionCuentaContable($this->uri->segment(5), $this->uri->segment(6))->total;
      $registros = $this->Cuenta_contable_model->depreciacionCuentaContable($this->uri->segment(5), $this->uri->segment(6), $total, 0);

      if (!($registros == FALSE)) {
        $i = 1;
        $j = 1;
        foreach($registros as $bien) {

          $final = $bien->precio_unitario * $bien->porcentaje_depreciacion;
          $depreciar = $bien->precio_unitario - $final;
          $acumulada = 0;
          $libro = 0;

          $x = $i + 1;

          $objPHPExcel->setActiveSheetIndex(0)
                       ->setCellValue('A'.$i, '#')
                       ->setCellValue('B'.$i, 'Descripcion')
                       ->setCellValue('C'.$i, 'Marca')
                       ->setCellValue('D'.$i, 'Modelo')
                       ->setCellValue('E'.$i, 'Serie/Chasis/Plac')
                       ->setCellValue('F'.$i, 'Codigo anterior')
                       ->setCellValue('G'.$i, 'Codigo actual')
                       ->setCellValue('H'.$i, 'Oficina')
                       ->setCellValue('I'.$i, 'Empleado')
                       ->setCellValue('J'.$i, 'Documento')
                       ->setCellValue('K'.$i, 'Fecha Adquisicion')
                       ->setCellValue('L'.$i, 'Precio')
                       ->setCellValue('M'.$i, 'Valor residual')
                       ->setCellValue('N'.$i, 'Valor a Depreciar');

          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$x, $j)
                      ->setCellValue('B'.$x, $bien->descripcion)
                      ->setCellValue('C'.$x, $bien->nombre_marca)
                      ->setCellValue('D'.$x, $bien->modelo)
                      ->setCellValue('E'.$x, $bien->serie)
                      ->setCellValue('F'.$x, $bien->codigo_anterior)
                      ->setCellValue('G'.$x, $bien->codigo)
                      ->setCellValue('H'.$x, $bien->nombre_oficina)
                      ->setCellValue('I'.$x, $bien->primer_nombre ." ". $bien->primer_apellido ." ".$bien->nr)
                      ->setCellValue('J'.$x, $bien->nombre_doc_ampara ." ". $bien->documento)
                      ->setCellValue('K'.$x, $bien->fecha_adquisicion)
                      ->setCellValue('L'.$x, $bien->precio_unitario)
                      ->setCellValue('M'.$x, $final)
                      ->setCellValue('N'.$x, $depreciar);

          $fecha1 = new DateTime($bien->fecha_adquisicion);
          $fecha2 = new DateTime($this->uri->segment(7));
          $interval = $fecha1->diff($fecha2);

          $dias = $interval->format('%a');
          $pos = $interval->format('%R');

          if ($pos == '+') {
            if ($dias > $cuenta->vida_util * 365) {
              $dias = $cuenta->vida_util * 365;
            }
          }

          $dep_diaria = (($bien->precio_unitario - $final)/$cuenta->vida_util)/365;
          $fecha = substr($bien->fecha_adquisicion, -6);
          $anio_uno = substr($bien->fecha_adquisicion, 0, 4);

          $m = 14;

          for ($k=$anio_uno; $k < $anio_uno + $cuenta->vida_util + 1; $k++) {

            $m++;

            if ($pos == '+') {

              if ($dias > 0) {

                if ($dias > 365) {

                  if ($k == $anio_uno) {

                    $fecha1 = new DateTime($k.$fecha);
                    $fecha2 = new DateTime($k."-12-31");
                    $interval = $fecha1->diff($fecha2);

                    $aux_d = $interval->format('%a');

                    if ($aux_d != 0) {

                      $dias = $dias - $aux_d;
                      $dep = $dep_diaria * $aux_d;

                    } else {
                      $dep = 0;
                    }

                  } else {

                    $dias = $dias - 365;
                    $dep = $dep_diaria * 365;

                  }

                } else {

                  $dep = $dep_diaria * $dias;
                  $dias = 0;

                }

              } else {

                $dep = 0;

              }

            } else {

              $dep = 0;

            }

            $col = $this->obtenerColumnaExcel($m);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.$i, $k);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.$x, $dep);

            $acumulada = $acumulada + $dep;
          }

          $libro = $bien->precio_unitario - $acumulada;

          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue($this->obtenerColumnaExcel($m+1).$i, 'Depreciacion acumulada')
                      ->setCellValue($this->obtenerColumnaExcel($m+2).$i, 'Valor en Libros');

          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue($this->obtenerColumnaExcel($m+1).$x, $acumulada)
                      ->setCellValue($this->obtenerColumnaExcel($m+2).$x, $libro);

          $col = $this->obtenerColumnaExcel($m+2);
          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$col.$i)->applyFromArray($estilo_titulo);
          $objPHPExcel->getActiveSheet()->getStyle('A'.$x.':'.$col.$x)->applyFromArray($estilo_contenido);

        }

        foreach(range('A',$col) as $columnID){
          $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename='depreciacion_cuenta_contable.xlsx'");
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
      }
    } else {
      redirect('login');
    }
  }


  /*
  * devueve hasta la ZZ
  */
  public function obtenerColumnaExcel($col) {
    if ($col > 0) {
      $letras = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S',
                'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

      if ($col > 26) {

        $columna = '';

        $num_col_uno = floor($col/26) - 1;
        if ($num_col_uno >= 0) {

          if ($col%26 == 0) {
            $num_col_uno = $num_col_uno - 1;
          }

          $columna = $letras[$num_col_uno];

        } else {

          $columna = '';

        }


        $num_col_dos = $col % 26;

        if ($num_col_dos == 0) {
          $num_col_dos = 26;
        }

        $columna .= $letras[$num_col_dos - 1];

        return $columna;

      } else {

        return $letras[$col - 1];

      }
    } else {
      return 0;
    }

  }

}
?>
