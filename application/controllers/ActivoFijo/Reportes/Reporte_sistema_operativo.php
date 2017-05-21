<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_sistema_operativo extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'paginacion', 'form'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Sistema_operativo_model'));
  }

  public function RecibirDatos() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('version')!=NULL && $this->input->post('fechaMin')!=NULL && $this->input->post('fechaMax')!=NULL) {
          redirect('ActivoFijo/Reportes/Reporte_sistema_operativo/Reporte/'.$this->input->post('version') . '/' . $this->input->post('fechaMin') . '/' .$this->input->post('fechaMax'));
        } else {
          redirect('ActivoFijo/Reportes/Reporte_sistema_operativo/Reporte/');
      }
    } else {
      redirect('login');
    }
  }

  public function Reporte() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "11- Reporte por Version de Sistema Operativo";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';

      if ($this->uri->segment(5) != '' && $this->uri->segment(6) != '' && $this->uri->segment(7) != '') {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );

        $this->table->set_template($template);
        $this->table->set_heading('#', 'Bien' ,'Descripción', 'Tipo Computadora', 'Marca', 'Sistema Operativo', 'Clave OS', 'Procesador', 'Disco Duro', 'Memoria',
                      'Office', 'Direccion IP', 'Numero de Punto');

        $num = '10';
        $registros = $this->Sistema_operativo_model->obtenerEquipoPorOSLimit($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7), $num, $this->uri->segment(8));
        $total = $this->Sistema_operativo_model->totalObtenerEquipoPorOSLimit($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7))->total;
        $pagination = paginacion('index.php/ActivoFijo/Reportes/Reporte_sistema_operativo/reporte/'.$this->uri->segment(5) . '/' . $this->uri->segment(6) . '/' .$this->uri->segment(7),
                      $total, $num, '8');

        if (!($registros == FALSE)) {
          foreach($registros as $bien) {
            $this->table->add_row($bien['id_bien'], $bien['id_equipo_informatico'], $bien['descripcion'], $bien['tipo_computadora'], $bien['nombre_marca'],
                          $bien['version_sistema_operativo'], $bien['clave_sistema_operativo'], $bien['nombre_procesador'].' '.$bien['velocidad_procesador'],
                          $bien['capacidad'].' '.$bien['velocidad_disco_duro'], $bien['tipo_memoria'].' '.$bien['velocidad_memoria'],
                          $bien['version_office'], $bien['direccion_ip'], $bien['numero_de_punto']);
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => 13);
          $this->table->add_row($msg);
        }

        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ". $this->uri->segment(5) . " - " . $this->uri->segment(6) . " - " . $this->uri->segment(7) ."</span></div>".
                  "<div class='limit-content'>".
                  "<div class='exportar icono'>
                  <a href='".base_url('/index.php/ActivoFijo/Reportes/Reporte_sistema_operativo/ReporteExcel/'.$this->uri->segment(5).'/'.
                        $this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>
                  Exportar Excel</a></div>" .
                  "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";

      }
      $data['body'] = $this->load->view('ActivoFijo/Reportes/Reporte_sistema_operativo_view', '',TRUE) . "<br>" . $table;
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
                 ->setCellValue('A1', 'Nº')
                 ->setCellValue('B1', 'Bien')
                 ->setCellValue('C1', 'Descripción')
                 ->setCellValue('D1', 'Tipo Computadora')
                 ->setCellValue('E1', 'Marca')
                 ->setCellValue('F1', 'Sistema Operativo')
                 ->setCellValue('G1', 'Clave OS')
                 ->setCellValue('H1', 'Procesador')
                 ->setCellValue('I1', 'Disco Duro')
                 ->setCellValue('J1', 'Memoria')
                 ->setCellValue('K1', 'Office')
                 ->setCellValue('L1', 'Direccion IP')
                 ->setCellValue('M1', 'Numero de Punto');
    $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($estilo_titulo);

    $total = $this->Sistema_operativo_model->totalObtenerEquipoPorOSLimit($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7))->total;
    $registros = $this->Sistema_operativo_model->obtenerEquipoPorOSLimit($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7), $total, 0);

    if (!($registros == FALSE)) {
      $i = 2;
      foreach($registros as $bien) {

        $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A'.$i, $bien['id_equipo_informatico'])
                     ->setCellValue('B'.$i, $bien['id_bien'])
                     ->setCellValue('C'.$i, $bien['descripcion'])
                     ->setCellValue('D'.$i, $bien['tipo_computadora'])
                     ->setCellValue('E'.$i, $bien['nombre_marca'])
                     ->setCellValue('F'.$i, $bien['version_sistema_operativo'])
                     ->setCellValue('G'.$i, $bien['clave_sistema_operativo'])
                     ->setCellValue('H'.$i, $bien['nombre_procesador'].' '.$bien['velocidad_procesador'])
                     ->setCellValue('I'.$i, $bien['capacidad'].' '.$bien['velocidad_disco_duro'])
                     ->setCellValue('J'.$i, $bien['tipo_memoria'].' '.$bien['velocidad_memoria'])
                     ->setCellValue('K'.$i, $bien['version_office'])
                     ->setCellValue('L'.$i, $bien['direccion_ip'])
                     ->setCellValue('M'.$i, $bien['numero_de_punto']);

        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':M'.$i)->applyFromArray($estilo_contenido);
        $i++;
      }
    } else {
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:M2');
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "No se encontraron resultados");
      $objPHPExcel->getActiveSheet()->getStyle('A2:M2')->applyFromArray($estilo_contenido);
    }

    foreach(range('A','M') as $columnID){
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='reporte_sistema_operativo.xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }

}

?>
