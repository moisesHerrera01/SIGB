<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_general extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Datos_Comunes_Model'));
  }

  public function RecibirDatos() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('criterio')!=NULL) {
          redirect('ActivoFijo/Reportes/Reporte_general/reporte/'.$this->input->post('criterio'));
        } else {
          redirect('ActivoFijo/Reportes/Reporte_general/reporte/'.'ssssxxxx');
      }
    } else {
      redirect('login');
    }
  }

  public function reporte(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "Reporte General";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';
      if ($this->uri->segment(5) != NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id bien','Descripción','id marca','Marca','Modelo',
          'Serie/Chasis','Código','Cod. ant.','Num. Motor','Placa','Matricula','Color',
          'Id cuenta','Num cat.');
        $num = '10';
          if($this->uri->segment(5)=='ssssxxxx'){
            $total=$this->Datos_Comunes_Model->obtenerPorCualquierCampoTotal()->total;
            $titulo_tabla='TODOS LOS BIENES';
          }else{
            $total = $this->Datos_Comunes_Model->totalPorCualquierCampo($this->uri->segment(5))->total;
            $titulo_tabla='CRITERIO DE BÚSQUEDA:' .$this->uri->segment(5);
          }
          $registros = $this->Datos_Comunes_Model->buscarPorCualquierCampo($this->uri->segment(5),$num, $this->uri->segment(6));
          $pagination = paginacion('index.php/ActivoFijo/Reportes/Reporte_general/reporte/'.$this->uri->segment(5),$total,$num, '6');
        if (!($registros == FALSE)) {
          foreach($registros as $pro) {
            $this->table->add_row($pro->id_bien,$pro->descripcion,$pro->id_marca,$pro->nombre_marca,$pro->modelo,
            $pro->serie,$pro->codigo,$pro->codigo_anterior,$pro->numero_motor,$pro->numero_placa,$pro->matricula,
            $pro->color,$pro->id_cuenta_contable,$pro->numero_categoria);
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "14");
          $this->table->add_row($msg);
        }
        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".$titulo_tabla."</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar'><a href='".base_url('/index.php/ActivoFijo/Reportes/Reporte_general/ReporteExcel/'.$this->uri->segment(5))."' class='icono icon-file-excel'>
                  Exportar Excel</a> &nbsp;
                  <a href='".base_url('/index.php/ActivoFijo/Reportes/Reporte_general/ImprimirReporte/'.$this->uri->segment(5))."' class='icono icon-printer' target='_blank'>
                  Imprimir</a></div>" .
                  "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }

      $data['body'] = $this->load->view('ActivoFijo/Reportes/Reporte_general_view', '',TRUE) . "<br>" . $table;
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
        $this->table->set_heading('Id bien','Descripción','id marca','Marca','Modelo',
          'Serie/Chasis','Código','Cod. ant.','Num. Motor','Placa','Matricula','Color',
          'Id cuenta','Num cat.');

        $registros = $this->Datos_Comunes_Model->buscarPorCualquierCampoAutocomplete($this->uri->segment(5));

        if (!($registros == FALSE)) {
          $i = 1;
          foreach($registros as $pro) {
            $this->table->add_row($pro->id_bien,$pro->descripcion,$pro->id_marca,$pro->nombre_marca,$pro->modelo,
              $pro->serie,$pro->codigo,$pro->codigo_anterior,$pro->numero_motor,$pro->numero_placa,$pro->matricula,
              $pro->color,$pro->id_cuenta_contable,$pro->numero_categoria);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "14");
          $this->table->add_row($msg);
        }

        $data = array(
          'table' => $this->table->generate(),
          'title' => '5-Reporte General'
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
                   ->setTitle("Reporte general .")
                   ->setSubject("Reporte general .")
                   ->setDescription("Reporte general. ")
                   ->setKeywords("Reporte general")
                   ->setCategory("Reporte general .");

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', 'Id bien')
                   ->setCellValue('B1', 'Descipción')
                   ->setCellValue('C1', 'Marca')
                   ->setCellValue('D1', 'Id marca')
                   ->setCellValue('E1', 'Modelo')
                   ->setCellValue('F1', 'Serie/Chasis')
                   ->setCellValue('G1', 'Número motor')
                   ->setCellValue('H1', 'Placa')
                   ->setCellValue('I1', 'Matricula')
                   ->setCellValue('J1', 'Color')
                   ->setCellValue('K1', 'Id cuenta')
                   ->setCellValue('L1', 'Número categoría')
                   ->setCellValue('M1', 'Código anterior')
                   ->setCellValue('N1', 'Código');
      $objPHPExcel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($estilo_titulo);

      $registros = $this->Datos_Comunes_Model->buscarPorCualquierCampoAutocomplete($this->uri->segment(5));
      if (!($registros == FALSE)) {
        $i = 2;
        foreach($registros as $pro) {
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, $pro->id_bien)
                      ->setCellValue('B'.$i, $pro->descripcion)
                      ->setCellValue('C'.$i, $pro->nombre_marca)
                      ->setCellValue('D'.$i, $pro->id_marca)
                      ->setCellValue('E'.$i, $pro->modelo)
                      ->setCellValue('F'.$i, $pro->serie)
                      ->setCellValue('G'.$i, $pro->numero_motor)
                      ->setCellValue('H'.$i, $pro->numero_placa)
                      ->setCellValue('I'.$i, $pro->matricula)
                      ->setCellValue('J'.$i, $pro->color)
                      ->setCellValue('K'.$i, $pro->id_cuenta_contable)
                      ->setCellValue('L'.$i, $pro->numero_categoria)
                      ->setCellValue('M'.$i, $pro->codigo_anterior)
                      ->setCellValue('N'.$i, $pro->codigo);

          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':N'.$i)->applyFromArray($estilo_contenido);
          $i++;
        }

        foreach(range('A','N') as $columnID){
          $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename='reporte_general.xlsx'");
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
      }
    } else {
      redirect('login');
    }
  }
}
?>
