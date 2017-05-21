<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prueba_excel extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->library('excel');
    $this->load->model('Bodega/UnidadMedida');
  }

  public function index(){

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
    						 ->setTitle("PHPExcel Test Document")
    						 ->setSubject("PHPExcel Test Document")
    						 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
    						 ->setKeywords("office PHPExcel php")
    						 ->setCategory("Test result file");
    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Codigo')
                 ->setCellValue('B1', 'Nombre')
                 ->setCellValue('C1', 'Abreviatura');
    $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($estilo_titulo);

    $i = 2;
    foreach ($this->UnidadMedida->obtenerUnidades() as $unidad) {
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $unidad->id_unidad_medida)
                    ->setCellValue('B'.$i, $unidad->nombre)
                    ->setCellValue('C'.$i, $unidad->abreviatura);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->applyFromArray($estilo_contenido);
        $i++;
    }

    foreach(range('A','C') as $columnID){
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='prueba.xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
	}
}
?>
