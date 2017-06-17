<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comparativo_fuente extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Kardex_model'));
  }

  public function RecibirDato() {
    $fecha_actual=date("Y-m-d");
    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('Estrategico/Comparativo_fuente/Reporte');
    $hora=date("H:i:s");
    $rastrea = array(
      'id_usuario' =>$USER['id'],
      'id_modulo' =>$modulo,
      'fecha' =>$fecha_actual,
      'hora' =>$hora,
      'operacion'=> 'CONSULTA'
    );
    if ($this->input->post('anio')!=NULL ) {
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('Estrategico/Comparativo_fuente/Reporte/'.$this->input->post('anio').'/');
    } else {
        redirect('Estrategico/Comparativo_fuente/Reporte');
    }
  }

  public function Reporte(){

    $USER = $this->session->userdata('logged_in');
    $data['title'] = "Comparativo de gastos por fuente de fondo";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $data['js'] = 'assets/js/validate/reporte/bodega/comparativo.js';
    $table = '';
    if (($this->uri->segment(4))!=NULL) {
      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_empty("&nbsp;");
      $column = 1;

      $cell = array('');
      $head = array('Fuente de Fondo');
      $comp = array();
      $total = array();

      for ($i=0; $i < $this->uri->segment(4); $i++) {
        $cell[$i+1] = array('data' => date('Y') - $i, 'colspan' => 2);
        array_push($head, 'Cantidad', 'Saldo');

        $total[$i] = 0;

        $registros = $this->Kardex_model->comparacionFuenteFondo(date('Y') - $i);

        if (!($registros == FALSE)) {

          foreach($registros as $fuente) {
            if ($i == 0) {
              $fila = array($fuente->nombre_fuente, number_format($fuente->cantidad), '$' . number_format($fuente->saldo, 3));
              $fila = $this->reñenarEspacio($fila, $this->uri->segment(4) - 1);
              array_push($comp, $fila);
            } else {

              for ($k=0; $k < count($comp); $k++) {
                if ($comp[$k][0] == $fuente->nombre_fuente) {
                  $comp[$k][$i * 2 + 1] = number_format($fuente->cantidad);
                  $comp[$k][$i * 2 + 2] = '$' . number_format($fuente->saldo, 3);
                  $k = count($comp);
                } else {
                  $fila = array($fuente->nombre_fuente);
                  $fila = $this->reñenarEspacio($fila, $i);
                  array_push($fila, number_format($fuente->cantidad), '$' . number_format($fuente->saldo, 3));
                  $fila = $this->reñenarEspacio($fila, $this->uri->segment(4) - $i - 1);
                  array_push($comp, $fila);
                  $k = count($comp);
                }
              }

            }

            $total[$i] += $fuente->saldo;

          }

        }
      }

      $aux = array('Total');

      for ($i=0; $i < count($total); $i++) {
        array_push($aux, '', '$' . number_format($total[$i], 3));
      }

      array_push($comp, $aux);

      $this->table->set_heading($cell);
      $this->table->add_row($head);

      $table =  "<div class='content_table '>" .
                "<div class='limit-content-title'>".
                  "<div class='title-reporte'>".
                    "Cuadro comparativo de gastos por fuente de fondo por año.".
                  "</div>".
                  "<div class='title-header'>
                    <ul>
                      <li>Fecha emisión: ".date('d/m/Y')."</li>
                      <li>Nombre la compañia: MTPS</li>
                      <li>N° pagina: 1/1</li>
                      <li>Nombre pantalla:</li>
                      <li>Usuario: ".$USER['nombre_completo']."</li>
                      <br />
                      <li>Parametros: ".$this->uri->segment(4)." años</li>
                    </ul>
                  </div>".
                "</div>".
                "<div class='limit-content'>" .
                "<div class='exportar'><a href='".base_url('/index.php/Estrategico/Comparativo_fuente/ReporteExcel/'.$this->uri->segment(4).'/')."'
                class='icono icon-file-excel'>Exportar Excel</a></div>" .
                "<div class='table-responsive'>" . $this->table->generate($comp) . "</div></div></div>";

      $data['body'] = $table;

    } else {

      $data['body'] = $this->load->view('Estrategico/comparativo_fuente_view', array('user' =>  $this->session->userdata('logged_in')), TRUE);

    }

    $this->load->view('base', $data);
  }

  public function reñenarEspacio($array, $anios) {
    for ($j=0; $j < $anios; $j++) {
      array_push($array, '', '');
    }

    return $array;
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
                 ->setTitle("Cuadro comparativo de gastos por fuente de fondo por año.")
                 ->setSubject("Cuadro comparativo de gastos por fuente de fondo por año.")
                 ->setDescription("Cuadro comparativo de gastos por fuente de fondo por año")
                 ->setKeywords("office PHPExcel php")
                 ->setCategory("Cuadro comparativo de gastos por fuente de fondo por año.");

    $registros = FALSE;
    $afuente = array();
    $total = array();

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', 'Fuente de Fondo');

    for ($i=0; $i < $this->uri->segment(4); $i++) {
      $total[$i] = 0;
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue($this->obtenerColumnaExcel($i*2 + 2) .'1', date('Y') - $i);

      $objPHPExcel->setActiveSheetIndex(0)->mergeCells($this->obtenerColumnaExcel($i*2 + 2).'1'.':'.$this->obtenerColumnaExcel($i*2 + 3).'1');

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue($this->obtenerColumnaExcel($i*2 + 2).'2', 'Cantidad')
                   ->setCellValue($this->obtenerColumnaExcel($i*2 + 3).'2', 'Saldo');

      $registros = $this->Kardex_model->comparacionFuenteFondo(date('Y') - $i);

      if (!($registros == FALSE)) {

        $j = 3;

        foreach($registros as $fuente) {

          if ($i == 0) {
            array_push($afuente, $fuente->nombre_fuente);
            $objPHPExcel->setActiveSheetIndex(0)
                         ->setCellValue('A'.$j, $fuente->nombre_fuente)
                         ->setCellValue('B'.$j, number_format($fuente->cantidad))
                         ->setCellValue('C'.$j, '$' . number_format($fuente->saldo, 3));
          } else {

            for ($k=0; $k < count($afuente); $k++) {
              if ($afuente[$k] == $fuente->nombre_fuente) {
                $objPHPExcel->setActiveSheetIndex(0)
                             ->setCellValue($this->obtenerColumnaExcel($i*2 + 2).($k + 3), number_format($fuente->cantidad))
                             ->setCellValue($this->obtenerColumnaExcel($i*2 + 3).($k + 3), '$' . number_format($fuente->saldo, 3));
                $k = count($afuente);
              } else {
                $objPHPExcel->setActiveSheetIndex(0)
                             ->setCellValue('A'.(count($afuente)+3), $fuente->nombre_fuente)
                             ->setCellValue($this->obtenerColumnaExcel($i*2 + 2).(count($afuente)+3), number_format($fuente->cantidad))
                             ->setCellValue($this->obtenerColumnaExcel($i*2 + 3).(count($afuente)+3), '$' . number_format($fuente->saldo, 3));
                $k = count($afuente);
              }
            }

          }

          $total[$i] += $fuente->saldo;
          $j++;

        }

      }

    }

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($j+2), 'Total');

    for ($o=0; $o < count($total); $o++) {
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue($this->obtenerColumnaExcel($o*2 + 2) . ($j+2), '')
                  ->setCellValue($this->obtenerColumnaExcel($o*2 + 3) . ($j+2), '$' . number_format($total[$o], 3));
    }

    $objPHPExcel->getActiveSheet()->getStyle('A1:'.$this->obtenerColumnaExcel($i*2 + 1).'1')->applyFromArray($estilo_titulo);
    $objPHPExcel->getActiveSheet()->getStyle('A2:'.$this->obtenerColumnaExcel($i*2 + 1).'2')->applyFromArray($estilo_titulo);

    for ($l=0; $l < count($afuente) + 2; $l++) {
      $objPHPExcel->getActiveSheet()->getStyle('A'.($l + 3).':'.$this->obtenerColumnaExcel($i*2 + 1).($l + 3))->applyFromArray($estilo_contenido);
    }

    foreach(range('A', $this->obtenerColumnaExcel($i*2 + 1)) as $columnID){
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='reporte_salidas_saldos.xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
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

          if ($col % 26 == 0) {
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
