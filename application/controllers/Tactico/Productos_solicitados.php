<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Productos_solicitados extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }

    $this->load->model('Bodega/Fuentefondos_model');
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Producto','Bodega/Detalle_solicitud_producto_model', 'Bodega/Fuentefondos_model', 'Bodega/Solicitud_Model'));
  }

   public function RecibirFiltro() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('fechaMin')!=NULL && $this->input->post('fechaMax')!=NULL && $this->input->post('cantidad')!=NULL) {
          redirect('Tactico/Productos_solicitados/Reporte/'.$this->input->post('cantidad').'/'.$this->input->post('fechaMin').'/'.$this->input->post('fechaMax'));
        } else {
          redirect('Tactico/Productos_solicitados/Reporte');
      }
    } else {
      redirect('login');
    }
  }
   public function Reporte(){

       $this->load->model('Bodega/Producto');
       $this->load->library(array('table'));
       $USER = $this->session->userdata('logged_in');

      $data['title'] = "Productos mas Solicitados ";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $data['js'] = "assets/js/validate/reporte/bodega/productos_solicitados.js";
      $table = '';

      if (($this->uri->segment(4))!=NULL && ($this->uri->segment(5))!=NULL && ($this->uri->segment(6))!=NULL) {
        $cant=0;
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Nombre del Producto','Detalle Producto','Unidad de Medida', 'Especifico','cantidad');

        $num = 10;

      $registros = $this->Producto->obtenerProductoMasSolicitado($this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6));

      if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Producto->obtenerProductoMasSolicitadoBuscar($this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6), $this->input->post('busca'));
              $total = count($registros);

          } else {
            $registros = $this->Producto->obtenerProductoMasSolicitado($this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6));
            $total = count($registros);
          }
        } else {
            $registros = $this->Producto->obtenerProductoMasSolicitado($this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6));
            $total = count($registros);

        }


      if ($registros != 0) {
        $total = 0;

        while ($registro = current($registros)) {
          $this->table->add_row($registro['nombre_producto'], $registro['id_detalleproducto'],
                                $registro['nombre_unidad_medida'], $registro['id_especifico'], $registro['cant']);

          $total += $registro['cant'];
          $cant=$num;
          $next = next($registros);

          if ($next == FALSE) {
            if($registro['cant'] != $next['cant'] && $total != 0){
              $msg = array('data' => "Total :", 'colspan' => "4");
              $this->table->add_row($msg,  '$'.number_format($total, 3));
              $total = 0;
            }
          }
        }
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "6");
        $this->table->add_row($msg);
      }

      if ($this->input->is_ajax_request()) {
            echo "<div class='table-responsive'>" . $this->table->generate() . "</div>" ;
            return false;
        }
                //$cant=10;
                $segmento=7;

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

                 $seccion = ($this->uri->segment(4) != 0) ?   $this->Solicitud_Model->obtenerSeccion($this->uri->segment(4)) : 'N/E' ;
                 $especifico = ($this->uri->segment(7) != 0) ?   $this->Especifico->obtenerEspecifico($this->uri->segment(7)) : 'N/E' ;

                 $buscar = array(
                    'name' => 'buscar',
                    'type' => 'search',
                    'placeholder' => 'Escriba el nombre del producto a buscar',
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                    'id' => 'buscar',
                    'url' => 'index.php/Tactico/Productos_solicitados/Reporte/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6));



                 $table =  "<div class='content_table '>" .
                           "<div class='limit-content-title'>".
                             "<div class='title-reporte'>".
                               "Reporte de Productos más Solicitados.".
                             "</div>".
                             "<div class='title-header'>
                               <ul>
                                 <li>Fecha emisión: ".date('d/m/Y')."</li>
                                 <li>Nombre la compañia: MTPS</li>
                                 <li>N° pagina: ". $pag .'/'. $pags ."</li>
                                 <li>Usuario: ".$USER['nombre_completo']."</li>
                                 <br />
                                 <li>Parametros: ".$this->uri->segment(4)." - ". $this->uri->segment(5) ." - ". $this->uri->segment(6). "</li>
                               </ul>
                             </div>".
                           "</div>".
                           "<div class='limit-content'>" .
                           "<div class='exportar'><a href='".base_url('/index.php/Tactico/Productos_solicitados/ReporteExcel/'.$this->uri->segment(4).'/'
                           .$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>
                           Exportar Excel</a><span class='content_buscar'><i class='glyphicon glyphicon-search'></i>".form_input($buscar)."</span></div>" . "<div class='table-content'><div class='table-responsive'>" . $this->table->generate() . "</div>"  . "</div></div></div>";
                 $data['body'] = $table;
      }else {
          $data['body'] = $this->load->view('Tactico/productos_solicitados_view', '',TRUE);
      }
      $this->load->view('base', $data);
    }


  public function ReporteExcel(){
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
      $objPHPExcel->getProperties()->setCreator("SIGB")
                   ->setLastModifiedBy("SIGB")
                   ->setTitle("Reporte Productos más Solicitados.")
                   ->setSubject("Reporte Productos más Solicitados .")
                   ->setDescription("Reporte generado para conocer que productos se solicitan más. ")
                   ->setKeywords("office PHPExcel php")
                   ->setCategory("Reporte Productos más Solicitados .");

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', 'Nombre del Producto')
                   ->setCellValue('B1', 'Detalle del Producto')
                   ->setCellValue('C1', 'U. M.')
                   ->setCellValue('D1', 'Especifico')
                   ->setCellValue('E1', 'Cantidad');

      $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($estilo_titulo);

      $registros = $this->Producto->obtenerProductoMasSolicitado($this->uri->segment(4),
    $this->uri->segment(5),$this->uri->segment(6));

    if (!($registros == FALSE)) {
      $cantidad=$this->uri->segment(4);
      $fecha_inicio=$this->uri->segment(5);
      $fecha_fin=$this->uri->segment(6);

      $i = 2;
      foreach($registros as $salida) {


        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $salida['nombre_producto'])
                    ->setCellValue('B'.$i, $salida['id_detalleproducto'])
                    ->setCellValue('C'.$i, $salida['nombre_unidad_medida'])
                    ->setCellValue('D'.$i, $salida['id_especifico'])
                    ->setCellValue('E'.$i, $salida['cant']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($estilo_contenido);
        $i++;
      }

      foreach(range('A','E') as $columnID){
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }

      $objPHPExcel->setActiveSheetIndex(0);

      header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header("Content-Disposition: attachment; filename='reporte_salidas_saldos.xlsx'");
      header('Cache-Control: max-age=0');

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
    }
  }
}
}
?>
