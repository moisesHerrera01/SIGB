<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_tipo_computadora extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'paginacion', 'form'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Equipo_informatico_model'));
  }

  public function RecibirDatos() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('tipo_computadora')!=NULL && $this->input->post('fechaMin')!=NULL && $this->input->post('fechaMax')!=NULL) {
          redirect('ActivoFijo/Reportes/Reporte_tipo_computadora/Reporte/'.$this->input->post('tipo_computadora') . '/' . $this->input->post('fechaMin') . '/' .$this->input->post('fechaMax'));
        } else {
          redirect('ActivoFijo/Reportes/Reporte_tipo_computadora/Reporte/');
      }
    } else {
      redirect('login');
    }
  }

  public function Reporte() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "10- Reporte Tipo Computadora";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';

      if ($this->uri->segment(5) != '' && $this->uri->segment(6) != '' && $this->uri->segment(7) != '') {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );

        $this->table->set_template($template);
        $this->table->set_heading('#', 'Bien' ,'Descripción', 'Tipo Computadora', 'Marca', 'Procesador', 'Disco Duro', 'Memoria', 'Sistema Operativo',
                                    'Office', 'Direccion IP', 'Numero de Punto');

        $num = '10';
        $registros = $this->Equipo_informatico_model->obtenerEquipoPorTipoComputadoraLimit($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7), $num, $this->uri->segment(8));
        $total = $this->Equipo_informatico_model->totalEquipoPorTipoComputadoraLimit($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7))->total;
        $pagination = paginacion('index.php/ActivoFijo/Reportes/Reporte_tipo_computadora/reporte/'.$this->uri->segment(5) . '/' . $this->uri->segment(6) . '/' .$this->uri->segment(7),
                      $total, $num, '8');

        if (!($registros == FALSE)) {
          foreach($registros as $bien) {
            $this->table->add_row($bien['id_bien'], $bien['id_equipo_informatico'], $bien['descripcion'], $bien['tipo_computadora'], $bien['nombre_marca'],
                          $bien['nombre_procesador'].' '.$bien['velocidad_procesador'], $bien['capacidad'].' '.$bien['velocidad_disco_duro'],
                          $bien['tipo_memoria'].' '.$bien['velocidad_memoria'], $bien['version_sistema_operativo'], $bien['version_office'],
                          $bien['direccion_ip'], $bien['numero_de_punto']);
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => 12);
          $this->table->add_row($msg);
        }

        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ". $this->uri->segment(5) . " - " . $this->uri->segment(6) . " - " . $this->uri->segment(7) ."</span></div>".
                  "<div class='limit-content'>".
                  "<div class='exportar icono'>
                  <a href='".base_url('/index.php/ActivoFijo/Reportes/Reporte_tipo_computadora/ReporteExcel/'.$this->uri->segment(5).'/'.
                        $this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>
                  Exportar Excel</a></div>" .
                  "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";

      }
      $data['body'] = $this->load->view('ActivoFijo/Reportes/reporte_tipo_computadora_view', '',TRUE) . "<br>" . $table;
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
    						 ->setTitle("Reporte Tipo Computadora.")
    						 ->setSubject("Reporte Tipo Computadora.")
    						 ->setDescription("Reporte Tipo Computadora.")
    						 ->setKeywords("office PHPExcel php")
    						 ->setCategory("Reporte Tipo Computadora.");

    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Nº')
                 ->setCellValue('B1', 'Bien')
                 ->setCellValue('C1', 'Descripción')
                 ->setCellValue('D1', 'Tipo Computadora')
                 ->setCellValue('E1', 'Marca')
                 ->setCellValue('F1', 'Procesador')
                 ->setCellValue('G1', 'Disco Duro')
                 ->setCellValue('H1', 'Memoria')
                 ->setCellValue('I1', 'Sistema Operativo')
                 ->setCellValue('J1', 'Office')
                 ->setCellValue('K1', 'Direccion IP')
                 ->setCellValue('L1', 'Numero de Punto');
    $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($estilo_titulo);

    $total = $this->Equipo_informatico_model->totalEquipoPorTipoComputadoraLimit($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7))->total;
    $registros = $this->Equipo_informatico_model->obtenerEquipoPorTipoComputadoraLimit($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7), $total, 0);

    if (!($registros == FALSE)) {
      $i = 2;
      foreach($registros as $bien) {

        $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A'.$i, $bien['id_equipo_informatico'])
                     ->setCellValue('B'.$i, $bien['id_bien'])
                     ->setCellValue('C'.$i, $bien['descripcion'])
                     ->setCellValue('D'.$i, $bien['tipo_computadora'])
                     ->setCellValue('E'.$i, $bien['nombre_marca'])
                     ->setCellValue('F'.$i, $bien['nombre_procesador'].' '.$bien['velocidad_procesador'])
                     ->setCellValue('G'.$i, $bien['capacidad'].' '.$bien['velocidad_disco_duro'])
                     ->setCellValue('H'.$i, $bien['tipo_memoria'].' '.$bien['velocidad_memoria'])
                     ->setCellValue('I'.$i, $bien['version_sistema_operativo'])
                     ->setCellValue('J'.$i, $bien['version_office'])
                     ->setCellValue('K'.$i, $bien['direccion_ip'])
                     ->setCellValue('L'.$i, $bien['numero_de_punto']);

        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->applyFromArray($estilo_contenido);
        $i++;
      }
    } else {
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:L2');
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "No se encontraron resultados");
      $objPHPExcel->getActiveSheet()->getStyle('A2:L2')->applyFromArray($estilo_contenido);
    }

    foreach(range('A','L') as $columnID){
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='Reporte_tipo_computadora.xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }

}

?>
