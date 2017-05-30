<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bienes_por_unidad extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }

    $this->load->helper(array('form', 'paginacion'));
    $this->load->library(array('table'));
    $this->load->model('ActivoFijo/Datos_Comunes_Model');
    $this->load->model('mtps/Seccion_model');
  }

  public function RecibirBienesUnidad() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      //var_dump($this->Datos_Comunes_Model->obtenerBienesOficina(32));
      var_dump($this->input->post('seccion'));
      if ($this->input->post('seccion')!=NULL && $this->input->post('oficina')!=NULL) {
          redirect('ActivoFijo/Reportes/Bienes_por_unidad/reporte/'.$this->input->post('seccion').'/'.$this->input->post('oficina'));
      } elseif ($this->input->post('seccion')!=NULL) {
          redirect('ActivoFijo/Reportes/Bienes_por_unidad/reporte/'.$this->input->post('seccion').'/0');
      } else {
        redirect('ActivoFijo/Reportes/Bienes_por_unidad/reporte/');
      }
    } else {
      redirect('login');
    }
  }

  public function reporte(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "3- Bienes por Unidad";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $data['js'] = 'assets/js/validate/reporte/activofijo/bunidad.js';
      $table = '';
      if ($this->uri->segment(5) != NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Descripción','Modelo', 'Marca', 'Serie', 'Código', 'Codigo anterior', 'Color', 'Oficina', 'Empleado',
          'Codificar', 'Precio Unitario');
        $num = '10';
        $registros;
        if ($this->uri->segment(6) != 0) {
          $registros = $this->Datos_Comunes_Model->obtenerBienesOficina($this->uri->segment(6), $num, $this->uri->segment(7));
          $total = $this->Datos_Comunes_Model->totalBienesOficina($this->uri->segment(6));
          $pagination = paginacion('index.php/ActivoFijo/Reportes/Bienes_por_unidad/reporte/'.$this->uri->segment(5).'/'.$this->uri->segment(6),
            $total->total, $num, '7');
        } else {
          // solo recibe unidad o seccion
          $registros = $this->Datos_Comunes_Model->obtenerBienesUnidad($this->uri->segment(5), $num, $this->uri->segment(7));
          $total = $this->Datos_Comunes_Model->totalBienesUnidad($this->uri->segment(5));
          $pagination = paginacion('index.php/ActivoFijo/Reportes/Bienes_por_unidad/reporte/'.$this->uri->segment(5).'/'.$this->uri->segment(6),
            $total->total, $num, '7');
        }

        if (!($registros == FALSE)) {
          $i = 1;
          foreach($registros as $bien) {
            $nombre = $this->Seccion_model->nombreEmpleado($bien->id_empleado);
            $this->table->add_row($i,$bien->descripcion, $bien->modelo, $bien->nombre_marca, $bien->serie, $bien->codigo_anterior, $bien->codigo,
              $bien->color, $bien->nombre_oficina, $nombre, $bien->codificar, $bien->precio_unitario);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "12");
          $this->table->add_row($msg);
        }
        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".$this->uri->segment(4)."</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar'><a href='".base_url('/index.php/ActivoFijo/Reportes/Bienes_por_unidad/ReporteExcel/'.$this->uri->segment(5).'/'.
                    $this->uri->segment(6))."' class='icono icon-file-excel'>Exportar Excel</a> &nbsp;
                    <a href='".base_url('/index.php/ActivoFijo/Reportes/Bienes_por_unidad/ImprimirReporte/'.$this->uri->segment(5).'/'.
                    $this->uri->segment(6))."' class='icono icon-printer' target='_blank'>Imprimir</a></div>" .
                  "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }

      $data['body'] = $this->load->view('ActivoFijo/Reportes/Bienes_por_unidad_view', '',TRUE) . "<br>" . $table;
      $this->load->view('base', $data);
    } else {
      redirect('login/index/forbidden');
    }
  }

  public function ImprimirReporte() {
    $USER = $this->session->userdata('logged_in');
    if ($USER) {
      if ($this->uri->segment(5) != NULL || $this->uri->segment(6) != NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Descripción','Modelo', 'Marca', 'Serie', 'Código', 'Codigo anterior', 'Color', 'Oficina', 'Empleado',
          'Codificar', 'Precio Unitario');

        $total_registros = 0;
        if ($this->uri->segment(6) != 0) {
          $total_registros = $this->Datos_Comunes_Model->totalBienesOficina($this->uri->segment(6))->total;
        } else {
          // solo recibe unidad o seccion
          $total_registros = $this->Datos_Comunes_Model->totalBienesUnidad($this->uri->segment(5))->total;
        }

        if ($total_registros > 0) {

          $registros;
          if ($this->uri->segment(6) != 0) {
            $registros = $this->Datos_Comunes_Model->obtenerBienesOficina($this->uri->segment(6));
          } else {
            // solo recibe unidad o seccion
            $registros = $this->Datos_Comunes_Model->obtenerBienesUnidad($this->uri->segment(5));
          }

          if (!($registros == FALSE)) {
            $i = 1;
            foreach($registros as $bien) {
              $nombre = $this->Seccion_model->nombreEmpleado($bien->id_empleado);
              $this->table->add_row($i,$bien->descripcion, $bien->modelo, $bien->nombre_marca, $bien->serie, $bien->codigo_anterior, $bien->codigo,
                $bien->color, $bien->nombre_oficina, $nombre, $bien->codificar, $bien->precio_unitario);
              $i++;
            }
          } else {
            $msg = array('data' => "No se encontraron resultados", 'colspan' => "12");
            $this->table->add_row($msg);
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "12");
          $this->table->add_row($msg);
        }

        $data = array(
          'table' => $this->table->generate(),
          'title' => "3- Bienes por Unidad"
        );
        $this->load->view('ActivoFijo/Reportes/imprimir_reporte_view', $data);
      }
    } else {
      redirect('login');
    }
  }

  public function Bienes_oficina() {
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('oficina') == "")) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Descripción','Modelo', 'Marca', 'Serie', 'Código', 'Codigo anterior', 'Color', 'Oficina', 'Empleado',
          'Codificar', 'Precio Unitario');

        $registros = $this->Datos_Comunes_Model->obtenerBienesOficina($this->input->post('oficina'));

        if ($registros) {
          $i = 1;
          foreach($registros as $bien) {
            $nombre = $this->Seccion_model->nombreEmpleado($bien->id_empleado);
            $this->table->add_row($i,$bien->descripcion, $bien->modelo, $bien->nombre_marca, $bien->serie, $bien->codigo_anterior, $bien->codigo,
              $bien->color, $bien->nombre_oficina, $nombre, $bien->codificar, $bien->precio_unitario);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "12");
          $this->table->add_row($msg);
        }

        print("<div class='table-responsive'>" . $this->table->generate() . "</div>");
      }
    }
  }

  public function autocompleteOficina() {
    $registros = '';
    if ($this->input->is_ajax_request()) {
      $id_seccion = $this->input->post('seccion');
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Seccion_model->buscarOficinasSeccion($id_seccion, $this->input->post('autocomplete'));
      } else {
          $registros = $this->Seccion_model->obtenerOficinasSeccion($id_seccion);
      }
    } else {
          $registros = $this->Seccion_model->obtenerOficinasSeccion($id_seccion);
    }

    if ($registros == '') {
      echo '';
    }else {
      foreach ($registros as $oficina) {
        echo '<div class="suggest-element" ida="producto'.$oficina->id_oficina.'"><a id="producto'.
        $oficina->id_oficina.'" data="'.$oficina->id_oficina.'"  data1="'.$oficina->nombre_oficina.'" >'
        .$oficina->nombre_oficina.'</a></div>';
      }
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
    						 ->setTitle("Reporte de bienes por sección")
    						 ->setSubject("Reporte de bienes por sección")
    						 ->setDescription("Reporte de bienes por sección.")
    						 ->setKeywords("office PHPExcel php")
    						 ->setCategory("Reporte de bienes por sección");
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Descripción')
                ->setCellValue('B1', 'Modelo')
                ->setCellValue('C1', 'Marca')
                ->setCellValue('D1', 'Serie')
                ->setCellValue('E1', 'Color')
                ->setCellValue('F1', 'Oficina')
                ->setCellValue('G1', 'Empleado')
                ->setCellValue('H1', 'Código anterior')
                ->setCellValue('I1', 'Codigo actual')
                ->setCellValue('J1', 'Codificar')
                ->setCellValue('K1', 'Precio Unitario');
    $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($estilo_titulo);

    $total_registros = 0;
    if ($this->uri->segment(6) != 0) {
      $total_registros = $this->Datos_Comunes_Model->totalBienesOficina($this->uri->segment(6))->total;
    } else {
      // solo recibe unidad o seccion
      $total_registros = $this->Datos_Comunes_Model->totalBienesUnidad($this->uri->segment(5))->total;
    }

    if ($total_registros > 0) {

      $registros;
      if ($this->uri->segment(6) != 0) {
        $registros = $this->Datos_Comunes_Model->obtenerBienesOficina($this->uri->segment(6));
      } else {
        // solo recibe unidad o seccion
        $registros = $this->Datos_Comunes_Model->obtenerBienesUnidad($this->uri->segment(5));
      }

      $i = 2;
      while ($registro = current($registros)) {
        $nombre = $this->Seccion_model->nombreEmpleado($registro->id_empleado);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $registro->descripcion)
                    ->setCellValue('B'.$i, $registro->modelo)
                    ->setCellValue('C'.$i, $registro->nombre_marca)
                    ->setCellValue('D'.$i, $registro->serie)
                    ->setCellValue('E'.$i, $registro->color)
                    ->setCellValue('F'.$i, $registro->nombre_oficina)
                    ->setCellValue('G'.$i, $nombre)
                    ->setCellValue('H'.$i, $registro->codigo_anterior)
                    ->setCellValue('I'.$i, $registro->codigo)
                    ->setCellValue('J'.$i, $registro->codificar)
                    ->setCellValue('K'.$i, $registro->precio_unitario);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->applyFromArray($estilo_contenido);

        $next = next($registros);
        $i++;
      }
    } else {
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:K2');
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "No se encontraron resultados");
      $objPHPExcel->getActiveSheet()->getStyle('A2:f2')->applyFromArray($estilo_contenido);
    }

    foreach(range('A','K') as $columnID){
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='reporte_bienes_unidades.xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }

}

?>
