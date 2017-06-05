<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resumen_conteo extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library(array('table'));
    $this->load->model(array('Bodega/Conteofisico_model', 'Bodega/Producto', 'Bodega/DetalleConteoFisico_model',
                      'Bodega/Especifico', 'Bodega/DetalleProducto_model', 'Bodega/Kardex_model', 'Bodega/UnidadMedida'));
  }

  public function RecibirConteo() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if (($this->input->post()) != '') {
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
        $count = $this->DetalleConteoFisico_model->totalDetalleConteo($nom_conteo);
        $segmento = '5';
        $registros = $this->DetalleConteoFisico_model->obtenerDetalleConteosLimit($nom_conteo, $num, $this->uri->segment(5));
        $pagination = paginacion('index.php/Bodega/ConteoFisico/reporte/',
                      $count , $num, $segmento);

        $fecha = $this->Conteofisico_model->obtenerFechaConteo($nom_conteo);
        if (!($registros == FALSE)) {
          $i = 1;
          foreach($registros as $conteo) {
            $producto = $this->Producto->obtenerTodoProducto($conteo->id_producto);
            $unidad = $this->UnidadMedida->obtenerUnidad($producto->id_unidad_medida);
            $fuente = $this->Kardex_model->obtenerFuenteFondo($conteo->id_detalleproducto, $fecha);
            $nombre_especifico = $this->Especifico->obtenerEspecifico($conteo->id_especifico);
            $existencia = intval($this->Kardex_model->obtenerExistencias($conteo->id_detalleproducto, $fecha));
            $this->table->add_row($i, $conteo->id_especifico, $producto->nombre, $unidad, $fuente, $nom_conteo,
                                  $conteo->cantidad, $existencia,  $conteo->cantidad - $existencia);

            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
          $this->table->add_row($msg);
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

        $table = "<div class='content_table '>" .
                  "<div class='limit-content-title'>".
                    "<div class='title-reporte'>".
                      "Reporte resumen conteo fisico.".
                    "</div>".
                    "<div class='title-header'>
                      <ul>
                        <li>Fecha emisión: ".date('d/m/Y')."</li>
                        <li>Nombre la compañia: MTPS</li>
                        <li>N° pagina: ". $pag .'/'. $pags ."</li>
                        <li>Nombre pantalla:</li>
                        <li>Usuario: ".$USER['nombre_completo']."</li>
                        <br />
                        <li>Parametros: ".$nom_conteo."</li>
                      </ul>
                    </div>".
                  "</div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar'><a href='".base_url('/index.php/Bodega/ConteoFisico/ReporteExcel/'.$nom_conteo)."' class='icono icon-file-excel'>
                  Exportar Excel</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";

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

      $registros = $this->DetalleConteoFisico_model->obtenerDetalleConteosTotal($this->uri->segment(4));
      $fecha = $this->Conteofisico_model->obtenerFechaConteo($this->uri->segment(4));

      if (!($registros == FALSE)) {
        $i = 2;
        foreach($registros as $conteo) {
          $producto = $this->Producto->obtenerTodoProducto($conteo->id_producto);
          $unidad = $this->UnidadMedida->obtenerUnidad($producto->id_unidad_medida);
          $fuente = $this->Kardex_model->obtenerFuenteFondo($conteo->id_detalleproducto, $fecha);
          $nombre_especifico = $this->Especifico->obtenerEspecifico($conteo->id_especifico);
          $existencia = intval($this->Kardex_model->obtenerExistencias($conteo->id_detalleproducto, $fecha));

          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, $i-1)
                      ->setCellValue('B'.$i, $conteo->id_especifico)
                      ->setCellValue('C'.$i, $producto->nombre)
                      ->setCellValue('D'.$i, $unidad)
                      ->setCellValue('E'.$i, $fuente)
                      ->setCellValue('F'.$i, $this->uri->segment(4))
                      ->setCellValue('G'.$i, $conteo->cantidad)
                      ->setCellValue('H'.$i, $existencia)
                      ->setCellValue('I'.$i, $conteo->cantidad - $existencia);
          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($estilo_contenido);
          $i++;
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
