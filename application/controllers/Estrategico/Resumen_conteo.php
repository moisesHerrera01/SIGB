<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resumen_conteo extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    } else {
      $USER = $this->session->userdata('logged_in');
      $modulo = $this->User_model->obtenerModulo('Estrategico/Resumen_conteo/Reporte');
      if (!$this->User_model->validarAccesoCrud($modulo, $USER['id'], 'select')) {
        redirect('dashboard/index/forbidden');
      }
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library(array('table'));
    $this->load->model(array('Bodega/Conteofisico_model', 'Bodega/Producto', 'Bodega/DetalleConteoFisico_model',
                      'Bodega/Especifico', 'Bodega/DetalleProducto_model', 'Bodega/Kardex_model', 'Bodega/UnidadMedida'));
    date_default_timezone_set('America/El_Salvador');
  }

  public function RecibirConteo() {
    $fecha_actual=date("Y-m-d");
    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('Estrategico/Resumen_conteo/Reporte');
    $hora=date("H:i:s");
    $rastrea = array(
      'id_usuario' =>$USER['id'],
      'id_modulo' =>$modulo,
      'fecha' =>$fecha_actual,
      'hora' =>$hora,
      'operacion'=> 'CONSULTA'
    );
    if($USER){
      if (($this->input->post()) != '') {
        $this->User_model->insertarRastreabilidad($rastrea);
        $conteo = str_replace(" ", "_", $this->input->post('conteo'));
        redirect('Estrategico/Resumen_conteo/Reporte/'.$conteo);
      } else {
        redirect('Estrategico/Resumen_conteo/Reporte/');
      }
    } else {
      redirect('login');
    }
  }

  public function Reporte(){
    $nom_conteo = str_replace("_", " ", $this->uri->segment(4));
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "Reporte Conteo Fisico";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $data['js'] = 'assets/js/validate/reporte/bodega/comp_conteo.js';
      $table = '';
      if (($nom_conteo) != '') {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Especifico', 'Nombre del producto', 'Unidad Medida', 'Fuente de Fondos', 'Conteo',
                                  'Contador', 'Contador Sistema','Diferencia');

        $num = '15';
        $segmento = '5';
        $count = 0;

        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->DetalleConteoFisico_model->obtenerDetalleConteosBusca($nom_conteo, $this->input->post('busca'));
          } else {
            $registros = $this->DetalleConteoFisico_model->obtenerDetalleConteosTotal($nom_conteo);
          }
        } else {
            $registros = $this->DetalleConteoFisico_model->obtenerDetalleConteosTotal($nom_conteo);
        }

        $resumen = array();

        $fecha = $this->Conteofisico_model->obtenerFechaConteo($nom_conteo);
        if (!($registros == FALSE)) {
          $i = 1;
          foreach($registros as $conteo) {
            $fuente = $this->Kardex_model->obtenerFuenteFondo($conteo->id_detalleproducto, $fecha);
            $existencia = intval($this->Kardex_model->obtenerExistencias($conteo->id_detalleproducto, $fecha));
            if ($conteo->cantidad - $existencia != 0) {
              $row = array($i, $conteo->id_especifico, $conteo->nombre_producto, $conteo->nombre_unidad, $fuente, $nom_conteo,
                                    $conteo->cantidad, $existencia,  $conteo->cantidad - $existencia);
              array_push($resumen, $row);
              $i++;
            }
          }

          $count = $i;
          $resumen = array_slice($resumen, $this->uri->segment(5), $num);

        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
          $this->table->add_row($msg);
        }

        $pagination = paginacion('index.php/Estrategico/Resumen_conteo/Reporte/'.$nom_conteo, $count , $num, $segmento);

        if ($this->input->is_ajax_request()) {
          echo "<div class='table-responsive'>" . $this->table->generate($resumen) . "</div>" . $pagination;
          return false;
        }

        // paginacion del header
        $pagaux = $count / $num;

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

        $buscar = array(
          'name' => 'buscar',
          'type' => 'search',
          'placeholder' => 'Buscar',
          'class' => 'form-control',
          'autocomplete' => 'off',
          'id' => 'buscar',
          'url' => 'index.php/Estrategico/Resumen_conteo/Reporte/'.str_replace(" ", "_", $nom_conteo)
        );

        $table = $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 3))) .
                  "<div class='content_table '>" .
                  "<div class='limit-content-title'>".
                    "<div class='title-reporte'>".
                      "Reporte resumen conteo fisico.".
                    "</div>".
                    "<div class='title-header'>
                      <ul>
                        <li>Fecha emisión: ".date('d/m/Y')."</li>
                        <li>Nombre la compañia: MTPS</li>
                        <li>N° pagina: ". $pag .'/'. $pags ."</li>
                        <li>Usuario: ".$USER['nombre_completo']."</li>
                        <br />
                        <li>Parametros: ".$nom_conteo."</li>
                      </ul>
                    </div>".
                  "</div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar'><a href='".base_url('/index.php/Estrategico/Resumen_conteo/ReporteExcel/'.str_replace(" ", "_", $nom_conteo))."' class='icono icon-file-excel'>
                  Exportar Excel</a> <span class='content_buscar'><i class='glyphicon glyphicon-search'></i>".form_input($buscar)."</span></div>".
                  "<div class='table-content'><div class='table-responsive'>" . $this->table->generate($resumen) . "</div>" . $pagination . "</div></div></div>";

        $data['body'] = $table;
      } else {

        $data['body'] = $this->load->view('Estrategico/resumen_conteo_view', array('user' => $USER), TRUE);

      }
      $this->load->view('base', $data);
    } else {
      redirect('login/index/forbidden');
    }
	}

  public function ReporteExcel() {
    $USER = $this->session->userdata('logged_in');
    $nom_conteo = str_replace("_", " ", $this->uri->segment(4));
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
      						 ->setTitle("Reporte comparación conteo físico")
      						 ->setSubject("Reporte comparación conteo físico")
      						 ->setDescription("Reporte generado para comprarar el conteo fisico con los registros del sistema.")
      						 ->setKeywords("office PHPExcel php")
      						 ->setCategory("Reporte comparación conteo físico");
      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', '#')
                   ->setCellValue('B1', 'Especifico')
                   ->setCellValue('C1', 'Nombre del producto')
                   ->setCellValue('D1', 'Unidad Medida')
                   ->setCellValue('E1', 'Fuente de Fondos')
                   ->setCellValue('F1', 'Conteo')
                   ->setCellValue('G1', 'Contador')
                   ->setCellValue('H1', 'Contador Sistema')
                   ->setCellValue('I1', 'Diferencia');
      $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($estilo_titulo);

      $registros = $this->DetalleConteoFisico_model->obtenerDetalleConteosTotal($nom_conteo);
      $fecha = $this->Conteofisico_model->obtenerFechaConteo($nom_conteo);

      if (!($registros == FALSE)) {
        $i = 2;
        foreach($registros as $conteo) {
          $fuente = $this->Kardex_model->obtenerFuenteFondo($conteo->id_detalleproducto, $fecha);
          $existencia = intval($this->Kardex_model->obtenerExistencias($conteo->id_detalleproducto, $fecha));

          if ($conteo->cantidad - $existencia != 0){
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $i-1)
                        ->setCellValue('B'.$i, $conteo->id_especifico)
                        ->setCellValue('C'.$i, $conteo->nombre_producto)
                        ->setCellValue('D'.$i, $conteo->nombre_unidad)
                        ->setCellValue('E'.$i, $fuente)
                        ->setCellValue('F'.$i, $this->uri->segment(4))
                        ->setCellValue('G'.$i, $conteo->cantidad)
                        ->setCellValue('H'.$i, $existencia)
                        ->setCellValue('I'.$i, $conteo->cantidad - $existencia);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($estilo_contenido);
            $i++;
          }
        }

        foreach(range('A','I') as $columnID){
          $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename='comparacion_conteo_fisico.xlsx'");
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
