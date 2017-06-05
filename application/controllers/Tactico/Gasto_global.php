<?php
  public function RecibirGastos() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('fecha_inicio')!=NULL) {
      if($this->input->post('fecha_fin')==NULL){
        redirect('Bodega/Detalle_solicitud_producto/reporteGastoSeccion/'.$this->input->post('fecha_inicio').'/'
        .$fecha_actual.'/'.$this->input->post('seccion'));
      }else{
        redirect('Bodega/Detalle_solicitud_producto/reporteGastoSeccion/'.$this->input->post('fecha_inicio').'/'
        .$this->input->post('fecha_fin').'/'.$this->input->post('seccion'));
      }} else {
        redirect('Bodega/Detalle_solicitud_producto/reporteGastoSeccion/');
    }
  }

  public function reporteGastoSeccion(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $this->load->model(array('mtps/Seccion_model'));
      $data['title'] = "Gasto Global";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $data['js'] = 'assets/js/validate/reporte/bodega/gasto_seccion.js';
      $table = '';
      if (($this->uri->segment(4))!=NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Solicitud','Fecha Salida', 'Seccion', 'Especifico','Número Producto',
        'Producto', 'Unidad Medida','Cantidad','Total');

        $num = '10';
        $seccion = ($this->uri->segment(6)==NULL) ? 0 : $this->uri->segment(6);
        $registros = $this->Detalle_solicitud_producto_model->obtenerProductosSeccion($this->uri->segment(4),
        $this->uri->segment(5),$seccion,$num, $this->uri->segment(7));
        $total = $this->Detalle_solicitud_producto_model->obtenerProductosSeccionTotal($this->uri->segment(4),
        $this->uri->segment(5),$seccion);
        $cant=$total->numero;

        $pagination = paginacion('index.php/Bodega/Detalle_solicitud_producto/reporteGastoSeccion/'.$this->uri->segment(4).
        '/'.$this->uri->segment(5).'/'.$seccion.'/',$cant,$num, '7');

        if (!($registros == FALSE)) {
          $i = 1;
          foreach($registros as $pro) {
            $this->table->add_row($pro->numero_solicitud, $pro->fecha_salida,$pro->nombre_seccion,$pro->id_especifico,
            $pro->numero_producto,$pro->producto,$pro->unidad,$pro->cantidad,$pro->total);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
          $this->table->add_row($msg);
        }
        $table = "<div class='content_table '>" .
                 "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".$this->Seccion_model->obtenerPorIdSeccion($this->uri->segment(6)). " " . $this->uri->segment(4) . " - " . $this->uri->segment(5) . "</span></div>".
                 "<div class='limit-content'>" .
                 "<div class='exportar'><a href='".base_url('/index.php/Bodega/Detalle_solicitud_producto/ReporteExcel/'.$this->uri->segment(4).'/'
                 .$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>
                 Exportar Excel</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }

      $data['body'] = $this->load->view('Bodega/Reportes/gasto_seccion_view', '',TRUE) . "<br>" . $table;
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
                 ->setCellValue('A1', 'Solicitud')
                 ->setCellValue('B1', 'Fecha Salida')
                 ->setCellValue('C1', 'Seccion')
                 ->setCellValue('D1', 'Especifico')
                 ->setCellValue('E1', 'Número Producto')
                 ->setCellValue('F1', 'Producto')
                 ->setCellValue('G1', 'Unidad Medida')
                 ->setCellValue('H1', 'Cantidad')
                 ->setCellValue('I1', 'Total');
    $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($estilo_titulo);

    $seccion = ($this->uri->segment(6)==NULL) ? 0 : $this->uri->segment(6);
    $registros = $this->Detalle_solicitud_producto_model->obtenerProductosSeccionTodo($this->uri->segment(4), $this->uri->segment(5),$seccion);

    if (!($registros == FALSE)) {
      $i = 2;
      foreach($registros as $pro) {

        $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A'.$i, $pro->numero_solicitud)
                     ->setCellValue('B'.$i, $pro->fecha_salida)
                     ->setCellValue('C'.$i, $pro->nombre_seccion)
                     ->setCellValue('D'.$i, $pro->id_especifico)
                     ->setCellValue('E'.$i, $pro->numero_producto)
                     ->setCellValue('F'.$i, $pro->producto)
                     ->setCellValue('G'.$i, $pro->unidad)
                     ->setCellValue('H'.$i, $pro->cantidad)
                     ->setCellValue('I'.$i, $pro->total);

        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($estilo_contenido);
        $i++;
      }
    } else {
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:I2');
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "No se encontraron resultados");
      $objPHPExcel->getActiveSheet()->getStyle('A2:I2')->applyFromArray($estilo_contenido);
    }

    foreach(range('A','I') as $columnID){
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    ob_end_clean();
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='reporte_gasto_seccion.xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }
  }
  ?>
