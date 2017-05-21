<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bienes_por_usuario extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Datos_Comunes_Model','ActivoFijo/Bienes_Muebles_Model'));
  }

  public function RecibirBienesUsuario() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('empleado')!=NULL) {
          redirect('ActivoFijo/Reportes/Bienes_por_usuario/reporte/'.$this->input->post('empleado'));
        } else {
          redirect('ActivoFijo/Reportes/Bienes_por_usuario/reporte/');
      }
    } else {
      redirect('login');
    }
  }

  public function reporte(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "Bienes Usuario";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';
      if ($this->uri->segment(5) != NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Descripción','Marca','Modelo','Serie','Código',
          'Código anterior','Color', 'Precio');
        $num = '10';
        $registros = $this->Datos_Comunes_Model->obtenerBienesUsuario($this->uri->segment(5),$num, $this->uri->segment(6));
        $total = $this->Datos_Comunes_Model->totalBienesUsuario($this->uri->segment(5));
        $pagination = paginacion('index.php/ActivoFijo/Reportes/Bienes_por_usuario/reporte/'.$this->uri->segment(5),$total->total,$num, '6');

        if (!($registros == FALSE)) {
          $i = 1;
          foreach($registros as $pro) {
            $this->table->add_row($i,$pro->descripcion,$pro->nombre_marca,$pro->serie,
              $pro->codigo,$pro->codigo_anterior,$pro->modelo,$pro->color,$pro->precio_unitario);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
          $this->table->add_row($msg);
        }
        $empleado=$this->Datos_Comunes_Model->buscarEmpleado($this->uri->segment(5));
        foreach ($empleado as $e) {
          $emp="$e->primer_nombre $e->segundo_nombre $e->primer_apellido $e->segundo_apellido";
        }
        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".$emp."</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar '><a href='".base_url('/index.php/ActivoFijo/Reportes/Bienes_por_usuario/ReporteExcel/'.$this->uri->segment(5))."' class='icono icon-file-excel'>
                  Exportar Excel</a> &nbsp;
                  <a href='".base_url('/index.php/ActivoFijo/Reportes/Bienes_por_usuario/ImprimirReporte/'.$this->uri->segment(5))."' class='icono icon-printer' target='_blank'>
                  Imprimir</a></div>" .
                  "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }

      $data['body'] = $this->load->view('ActivoFijo/Reportes/Bienes_por_usuario_view', '',TRUE) . "<br>" . $table;
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
        $this->table->set_heading('#','Descripción','Marca','Modelo','Serie','Código',
          'Código anterior','Color', 'Precio');

        $registros = $this->Datos_Comunes_Model->obtenerBienesUsuarioExcel($this->uri->segment(5));

        if (!($registros == FALSE)) {
          $i = 1;
          foreach($registros as $pro) {
            $this->table->add_row($i,$pro->descripcion,$pro->nombre_marca,$pro->serie,
              $pro->codigo,$pro->codigo_anterior,$pro->modelo,$pro->color,$pro->precio_unitario);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
          $this->table->add_row($msg);
        }

        $data = array(
          'table' => $this->table->generate(),
          'title' => 'Bienes Usuario'
        );
        $this->load->view('ActivoFijo/Reportes/imprimir_reporte_view', $data);
      }
    } else {
      redirect('login');
    }
  }

  public function Bienes_empleado() {
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('empleado') == "")) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Descripción','Marca','Modelo','Serie','Código',
          'Código anterior','Color', 'Precio');

        $registros = $this->Datos_Comunes_Model->obtenerBienesUsuarioExcel($this->input->post('empleado'));

        if (!($registros == FALSE)) {
          $i = 1;
          foreach($registros as $pro) {
            $this->table->add_row($i,$pro->descripcion,$pro->nombre_marca,$pro->serie,
              $pro->codigo,$pro->codigo_anterior,$pro->modelo,$pro->color,$pro->precio_unitario);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
          $this->table->add_row($msg);
        }

        print("<div class='table-responsive'>" . $this->table->generate() . "</div>");
      }
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
                   ->setTitle("Reporte de bienes por usuario .")
                   ->setSubject("Reporte de bienes por usuario .")
                   ->setDescription("Reporte de bienes por usuario. ")
                   ->setKeywords("office PHPExcel php")
                   ->setCategory("Reporte de bienes por usuario .");

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', 'Descipción')
                   ->setCellValue('B1', 'Marca')
                   ->setCellValue('C1', 'Modelo')
                   ->setCellValue('D1', 'Serie')
                   ->setCellValue('E1', 'Código')
                   ->setCellValue('F1', 'Código anterior')
                   ->setCellValue('G1', 'Color')
                   ->setCellValue('H1', 'Precio');
      $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($estilo_titulo);

      $registros = $this->Datos_Comunes_Model->obtenerBienesUsuarioExcel($this->uri->segment(5));
      if (!($registros == FALSE)) {
        $i = 2;
        foreach($registros as $pro) {
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, $pro->descripcion)
                      ->setCellValue('B'.$i, $pro->nombre_marca)
                      ->setCellValue('C'.$i, $pro->modelo)
                      ->setCellValue('D'.$i, $pro->serie)
                      ->setCellValue('E'.$i, $pro->codigo)
                      ->setCellValue('F'.$i, $pro->codigo_anterior)
                      ->setCellValue('G'.$i, $pro->color)
                      ->setCellValue('H'.$i, $pro->precio_unitario);
          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($estilo_contenido);
          $i++;
        }

        foreach(range('A','H') as $columnID){
          $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename='reporte_bienes_usuario.xlsx'");
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
      }
    } else {
      redirect('login');
    }
  }
    public function AutocompleteEmpleado(){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->Bienes_Muebles_Model->buscarEmpleados($this->input->post('autocomplete'));
        } else {
            $registros = $this->Bienes_Muebles_Model->obtenerEmpleados();
        }
      } else {
            $registros = $this->Bienes_Muebles_Model->obtenerEmpleados();
      }

      if ($registros == '') {
        echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      }else {
        $i = 1;
        foreach ($registros as $emp) {
          echo '<div id="'.$i.'" class="suggest-element" ida="empleado'.$emp->id_empleado.'"><a id="empleado'.
          $emp->id_empleado.'" data="'.$emp->id_empleado.'"  data1="'.$emp->nombre_completo.'" >'
          .$emp->nombre_completo.'</a></div>';
          $i++;
      }
    }
  }
}
?>
