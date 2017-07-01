<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedor_factura_especifico extends CI_Controller {


  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Proveedor','Bodega/Fuentefondos_model', 'Bodega/Fuentefondos_model', 'Bodega/Solicitud_Model'));
    date_default_timezone_set('America/El_Salvador');

  }

  public function RecibirFiltro() {

    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('fechaMin')!=NULL && $this->input->post('fuente')!=NULL) {
      $USER = $this->session->userdata('logged_in');
      $modulo=$this->User_model->obtenerModulo('Tactico/Proveedor_factura_especifico/Reporte');
      $hora=date("H:i:s");
      $rastrea = array(
        'id_usuario' =>$USER['id'],
        'id_modulo' =>$modulo,
        'fecha' =>$fecha_actual,
        'hora' =>$hora,
        'operacion'=>'CONSULTA'
      );
      $this->User_model->insertarRastreabilidad($rastrea);
      if($this->input->post('fechaMax')==NULL){
        redirect('Tactico/Proveedor_factura_especifico/Reporte/'.$this->input->post('fuente').'/'
        .post('fechaMin').'/'.$this->input->post('fechaMax'));
      }else{
        redirect('Tactico/Proveedor_factura_especifico/Reporte/'.$this->input->post('fuente').'/'
        .$this->input->post('fechaMin').'/'.$this->input->post('fechaMax'));
      }} else {
        redirect('Tactico/Proveedor_factura_especifico/');
    }
  }

  public function Reporte(){

    $USER = $this->session->userdata('logged_in');
    if($USER){
      $this->load->model('Bodega/Fuentefondos_model');
      $this->load->library(array('table'));

      $data['title'] = "Reporte por Proveedor, Factura y Especifico";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $data['js'] = 'assets/js/validate/reporte/bodega/general.js';
      $table = '';

    if (($this->uri->segment(4))!=NULL && ($this->uri->segment(5))!=NULL && ($this->uri->segment(6))!=NULL) {
      $cant=0;
      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('Fecha Factura', 'Numero Factura', 'Compromiso', 'Proveedor', 'Objeto Especifico','Total OE');

      $num = 12;


      $total_registros = $this->Proveedor->TotalReporteProveedores($this->uri->segment(4),$this->uri->segment(5), $this->uri->segment(6));



      if ($total_registros > 0) {

        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Proveedor->ReporteProveedoresBuscar($this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6), $num, $this->uri->segment(7), $this->input->post('busca'));
              $count = count($registros);

          } else {
             $registros = $this->Proveedor->ReporteProveedores($this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6), $num, $this->uri->segment(7));
             $total = $this->Proveedor->TotalReporteProveedores($this->uri->segment(4),$this->uri->segment(5), $this->uri->segment(6));
             $count = count($registros);

            }
        } else {
             $registros = $this->Proveedor->ReporteProveedores($this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6), $num, $this->uri->segment(7));
             $total = $this->Proveedor->TotalReporteProveedores($this->uri->segment(4),$this->uri->segment(5), $this->uri->segment(6));
             $count = count($registros);
        }
        $pagination = paginacion('index.php/Tactico/Proveedor_factura_especifico/Reporte/' .$this->uri->segment(4). '/' .$this->uri->segment(5). '/' . $this->uri->segment(6),
                    $count, $num, '8');

        $total = 0;
        while ($registro = current($registros)) {
          $this->table->add_row($registro['fecha_factura'], $registro['numero_factura'], $registro['numero_compromiso'],
                                $registro['nombre_proveedor'], $registro['id_especifico'],"$". number_format($registro['total'], 3));

          $total += $registro['total'];
          $cant=$num;
          $next = next($registros);
          //  var_dump($registros);
          if ($next != FALSE) {
            if($registro['id_factura'] != $next['id_factura'] && $total != 0){
              $msg = array('data' => "Total factura: " , 'colspan' => "5");
              $this->table->add_row($msg, "$". number_format($total, 3));
              $total = 0;
            }
          } else {
            $msg = array('data' => "Total factura:", 'colspan' => "5");
            $this->table->add_row($msg, "$". number_format($total, 3));
            $total = 0;
          }
        }
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "6");
        $this->table->add_row($msg);
      }

      if ($this->input->is_ajax_request()) {
          echo "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
          return false;
        }
                $segmento=8;

                 // paginacion del header
                $pagaux = $cant / $num;

                 $pags = intval($pagaux);

                 if ($pagaux > $pags || $pags == 0) {
                   $pags++;
                 }

                 $seg = intval($this->uri->segment($segmento)) + 1;

                 $segaux = $seg / $num;

                 $pag = intval($segaux);

                 if ($segaux > $pag) {
                   $pag++;
                 }

                 $fuente = ($this->uri->segment(4) != 0) ?   $this->Fuentefondos_model->obtenerFuente($this->uri->segment(4)) : 'N/E' ;
                 $seccion = ($this->uri->segment(6) != 0) ?   $this->Solicitud_Model->obtenerSeccion($this->uri->segment(6)) : 'N/E' ;


                 $buscar = array(
                    'name' => 'buscar',
                    'type' => 'search',
                    'placeholder' => 'Escriba el Especifico o Proveedor',
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                    'id' => 'buscar',
                    'url' => 'index.php/Tactico/Proveedor_factura_especifico/Reporte/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/');

                 $table =  "<div class='content_table '>" .
                           "<div class='limit-content-title'>".
                             "<div class='title-reporte'>".
                               "Reporte por Proveedor, factura y especifico.".
                             "</div>".
                             "<div class='title-header'>
                               <ul>
                               <li>Fecha emisión: ".date('d/m/Y')."</li>
                                 <li>Nombre la compañia: MTPS</li>
                                 <li>N° pagina: ". $pag .'/'. $pags ."</li>
                                 <li>Usuario: ".$USER['nombre_completo']."</li>
                                 <br />
                                 <li>Parametros: ".$fuente." ". $this->uri->segment(5) ." - ". $this->uri->segment(6). "</li>
                               </ul>
                             </div>".
                           "</div>".
                           "<div class='limit-content'>" .
                           "<div class='exportar'><a href='".base_url('/index.php/Tactico/Proveedor_factura_especifico/ReporteExcel/'.$this->uri->segment(4).'/'
                           .$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>
                           Exportar Excel</a><span class='content_buscar'><i class='glyphicon glyphicon-search'></i>".form_input($buscar)."</span></div>" . "<div class='table-content'><div class='table-responsive'>" . $this->table->generate() . "</div>"  . "</div></div></div>";
                 $data['body'] = $table;
      }else {
          $data['body'] = $this->load->view('Tactico/filtro_fuentes_view', '',TRUE);
      }
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
    						 ->setTitle("Reporte por Proveedor, Factura y Especifico")
    						 ->setSubject("Reporte por Proveedor, Factura y Especifico")
    						 ->setDescription("Reporte por Proveedor, Factura y Especifico.")
    						 ->setKeywords("office PHPExcel php")
    						 ->setCategory("Reporte por Proveedor, Factura y Especifico");
    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Fecha Factura')
                 ->setCellValue('B1', 'Numero Factura')
                 ->setCellValue('C1', 'Compromiso')
                 ->setCellValue('D1', 'Proveedor')
                 ->setCellValue('E1', 'Objeto Especifico')
                 ->setCellValue('F1', ' Total OE');
    $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($estilo_titulo);

    $total_registros = $this->Proveedor->TotalReporteProveedores($this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6));

    if ($total_registros > 0) {

      $registros = $this->Proveedor->ReporteProveedores($this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6));

      $total = 0;
      $i = 2;
      while ($registro = current($registros)) {

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $registro['fecha_factura'])
                    ->setCellValue('B'.$i, $registro['numero_factura'])
                    ->setCellValue('C'.$i, $registro['numero_compromiso'])
                    ->setCellValue('D'.$i, $registro['nombre_proveedor'])
                    ->setCellValue('E'.$i, $registro['id_especifico'])
                    ->setCellValue('F'.$i, $registro['total']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':f'.$i)->applyFromArray($estilo_contenido);

        $total += $registro['total'];

        $next = next($registros);

        if ($next != FALSE) {
          if($registro['id_factura'] != $next['id_factura'] && $total != 0){
            $i++;
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':E'.$i);
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, "Total factura:")
                        ->setCellValue('F'.$i, $total);
            $total = 0;
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':f'.$i)->applyFromArray($estilo_contenido);
          }
        } else {
          $i++;
          $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':E'.$i);
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, "Total factura:")
                      ->setCellValue('F'.$i, $total);
          $total = 0;
          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':f'.$i)->applyFromArray($estilo_contenido);
        }
        $i++;
      }
    } else {
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:F2');
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "No se encontraron resultados");
      $objPHPExcel->getActiveSheet()->getStyle('A2:f2')->applyFromArray($estilo_contenido);
    }

    foreach(range('A','F') as $columnID){
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='reporte_proveedores.xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }


  public function ReporteExcelProveedores() {
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
                   ->setTitle("Reporte de proveedores.")
                   ->setSubject("Reporte de proveedores.")
                   ->setDescription("Reporte de proveedores. ")
                   ->setKeywords("office PHPExcel php")
                   ->setCategory("Reporte de proveedores.");

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', 'ID')
                   ->setCellValue('B1', 'Nombre proveedor')
                   ->setCellValue('C1', 'Nombre contacto')
                   ->setCellValue('D1', 'NIT')
                   ->setCellValue('E1', 'Correo Electrónico')
                   ->setCellValue('F1', 'Teléfono')
                   ->setCellValue('G1', 'Fax')
                   ->setCellValue('H1', 'Dirección')
                   ->setCellValue('I1', 'Categoría')
                   ->setCellValue('J1', 'Tipo Servicio')
                   ->setCellValue('K1', 'Tipo Empresa');

      $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($estilo_titulo);

      $registros = $this->Proveedor->obtenerProveedores();
      if (!($registros == FALSE)) {
        $i = 2;
        foreach($registros as $pro) {
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, $pro->id_proveedores)
                      ->setCellValue('B'.$i, $pro->nombre_proveedor)
                      ->setCellValue('C'.$i, $pro->nombre_contacto)
                      ->setCellValue('D'.$i, $pro->nit)
                      ->setCellValue('E'.$i, $pro->correo)
                      ->setCellValue('F'.$i, $pro->telefono)
                      ->setCellValue('G'.$i, $pro->fax)
                      ->setCellValue('H'.$i, $pro->direccion)
                      ->setCellValue('I'.$i, $pro->nombre_categoria)
                      ->setCellValue('J'.$i, $pro->rubro)
                      ->setCellValue('K'.$i, $pro->tipo_empresa);

          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->applyFromArray($estilo_contenido);
          $i++;
        }

        foreach(range('A','K') as $columnID){
          $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename='reporte_proveedores.xlsx'");
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
