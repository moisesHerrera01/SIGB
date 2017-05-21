<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_tipo_movimiento extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Tipo_movimiento_model','ActivoFijo/Bienes_Muebles_Model'));
  }

  public function RecibirMovimientos(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('tipo')!=NULL && $this->input->post('fecha_inicio')!=NULL && $this->input->post('fecha_fin')!=NULL) {
          redirect('ActivoFijo/Reportes/Reporte_tipo_movimiento/Reporte/'.$this->input->post('tipo').'/'.$this->input->post('fecha_inicio').'/'.$this->input->post('fecha_fin'));
        } else {
          redirect('ActivoFijo/Reportes/Reporte_tipo_movimiento/Reporte/');
      }
    } else {
      redirect('login');
    }
	}

  public function Reporte() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "Por tipo movimiento";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';

      if ($this->uri->segment(5) != '' && $this->uri->segment(6) != '' && $this->uri->segment(7) != '') {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Oficina entrega','Oficina recibe','Estado','Observaciones','Detalle');
        $num = '10';
        $registros = $this->Tipo_movimiento_model->obtenerMovimientosPorTipoLimit($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7), $num, $this->uri->segment(8));
        $total = $this->Tipo_movimiento_model->totalMovimientosPorTipo($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7));
        $pagination = paginacion('index.php/ActivoFijo/Reportes/Reporte_tipo_movimiento/Reporte/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7),
        $total->total, $num, '8');
        $tipo_movimiento='';
        if (!($registros == FALSE)) {
          foreach($registros as $tip) {
            $nom_ofi_recibe = $this->Bienes_Muebles_Model->obtenerOficina($tip->id_oficina_recibe);
            $this->table->add_row($tip->id_movimiento,$tip->nombre_oficina,$nom_ofi_recibe,$tip->estado_movimiento,$tip->observacion,
            '<a class="icono icon-detalle" href="'.base_url('index.php/ActivoFijo/Reportes/Detalle_Movimiento/index/'.$tip->id_movimiento.'/').'"></a>');
            $tipo_movimiento=$tip->nombre_movimiento;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "5");
          $this->table->add_row($msg);
        }
        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".$tipo_movimiento.' - '.$this->uri->segment(6).' - '.$this->uri->segment(7)."</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar'>
                  <a href='".base_url('/index.php/ActivoFijo/Reportes/Reporte_tipo_movimiento/ReporteExcel/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>
                  Exportar Excel</a> &nbsp;
                  <a href='".base_url('/index.php/ActivoFijo/Reportes/Reporte_tipo_movimiento/ImprimirReporte/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-printer' target='_blank'>
                  Imprimir</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";

      }

      $data['body'] = $this->load->view('ActivoFijo/Reportes/reporte_tipo_movimiento_view', '',TRUE) . "<br>" . $table;
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
        $this->table->set_heading('Id','Oficina entrega','Oficina recibe','Estado','Observacione');
        $registros = $this->Tipo_movimiento_model->obtenerMovimientosPorTipo($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7));
        if (!($registros == FALSE)) {
          foreach($registros as $tip) {
            $nom_ofi_recibe = $this->Bienes_Muebles_Model->obtenerOficina($tip->id_oficina_recibe);
            $this->table->add_row($tip->id_movimiento,$tip->nombre_oficina,$nom_ofi_recibe,$tip->estado_movimiento,$tip->observacion);
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "6");
          $this->table->add_row($msg);
        }
        $data = array(
          'table' => $this->table->generate(),
          'title' => '14- Movimientos por tipo'
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
                   ->setTitle("Reporte de Movimientos por tipo.")
                   ->setSubject("Reporte de Movimientos por tipo.")
                   ->setDescription("Reporte de Movimientos por tipo.")
                   ->setKeywords("office PHPExcel php")
                   ->setCategory("Reporte de Movimientos por tipo.");

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', 'Id')
                   ->setCellValue('B1', 'Oficina entrega')
                   ->setCellValue('C1', 'Oficina recibe')
                   ->setCellValue('D1', 'Estado')
                   ->setCellValue('E1', 'Observaciones');

      $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($estilo_titulo);

      $registros = $this->Tipo_movimiento_model->obtenerMovimientosPorTipo($this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7));
      if (!($registros == FALSE)) {
        $i = 2;
        foreach($registros as $tip) {
        $nom_ofi_recibe = $this->Bienes_Muebles_Model->obtenerOficina($tip->id_oficina_recibe);
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, $tip->id_movimiento)
                      ->setCellValue('B'.$i, $tip->nombre_oficina)
                      ->setCellValue('C'.$i, $nom_ofi_recibe)
                      ->setCellValue('D'.$i, $tip->estado_movimiento)
                      ->setCellValue('E'.$i, $tip->observacion);

          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($estilo_contenido);
          $i++;
        }

        foreach(range('A','B') as $columnID){
          $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename='reporte_movimiento_por_tipo_.xlsx'");
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
