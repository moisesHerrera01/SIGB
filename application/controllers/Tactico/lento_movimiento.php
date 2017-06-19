<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Lento_movimiento extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }

    $this->load->model('Bodega/Fuentefondos_model');
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Producto','Bodega/Detalle_solicitud_producto_model', 'Bodega/Fuentefondos_model', 'Bodega/Solicitud_Model', 'Bodega/Especifico'));
  }

   public function RecibirMovimiento() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('fuente')!=NULL && $this->input->post('especifico')!=NULL) {
          redirect('Tactico/Lento_movimiento/Reporte/'.$this->input->post('fuente').'/'.$this->input->post('especifico'));
        } else {
          redirect('Tactico/Lento_movimiento/');
      }
    } else {
      redirect('login');
    }
  }
   public function Reporte(){
    $USER = $this->session->userdata('logged_in');

      $data['title'] = "Lento movimiento";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $data['js'] = "assets/js/validate/reporte/bodega/lento_movimiento.js";
      $table = '';


      if (($this->uri->segment(4))!=NULL && ($this->uri->segment(5))!=NULL) {
        $cant=0;
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Nombre de Producto','Numero de  Producto','Unidad de medida', 'Existencia', 'Fuente de Fondos','Fecha de Ingreso','Alerta','Seccion Solicitante');
        $num = 10;
        $segmento=6;

         if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Producto->obtenerProductosFuenteLimitBusca($this->uri->segment(4),$this->uri->segment(5), $this->input->post('busca'));
              $count = count($registros);
              $pagination =0;
          } else {
            $registros = $this->Producto->obtenerProductosFuenteLimit($this->uri->segment(4),$this->uri->segment(5),$num,$this->uri->segment(6));
            $total = $this->Producto->obtenerProductosFuenteTotal($this->uri->segment(4),$this->uri->segment(5));
          }
        } else {
            $registros = $this->Producto->obtenerProductosFuenteLimit($this->uri->segment(4),$this->uri->segment(5),$num,$this->uri->segment(6));
            $total = $this->Producto->obtenerProductosFuenteTotal($this->uri->segment(4),$this->uri->segment(5));
            $cant=$total->numero;
           
        }

        $pagination = paginacion('index.php/Tactico/Lento_movimiento/Reporte/'.$this->uri->segment(4).'/'.$this->uri->segment(5),$cant,$num, $segmento);


        if (!($registros == FALSE)) {

          $fuente=$this->uri->segment(4);
          $especifico = $this->uri->segment(5);
          $i = 1;
          foreach($registros as $pro) {
            $entradas=0;
            $salidas=0;
            $kardex=$this->Detalle_solicitud_producto_model->obtenerKardexProducto($pro->id_detalleproducto);
            foreach ($kardex as $kar) {
                if($kar->movimiento=='SALIDA'){
                  $salidas=$salidas+$kar->cantidad;
                }else{
                  $entradas=$entradas+$kar->cantidad;
                }
               $entradas=$entradas-$salidas;
              date_default_timezone_set('America/El_Salvador');
              $anyo=20;
              $fecha_actual=date($anyo."y-m-d");
              $ingreso=$pro->fecha_ingreso;
              if (!is_integer($fecha_actual)) $fecha_actual = strtotime($fecha_actual);
              if (!is_integer($ingreso)) $ingreso = strtotime($ingreso);
              $dif=floor(abs($fecha_actual - $ingreso) / 60 / 60 / 24);
              $alerta;
              if($dif<30){
                $alerta='Normal';
              }elseif ($dif>30 && $dif<45) {
                $alerta='Lento';
              }elseif ($dif>60) {
                $alerta='Muy Lento';
              }
            }

            
              $this->table->add_row($pro->nombre_producto,$pro->numero_producto,$pro->nombre,$pro->existencia,$pro->nombre_fuente,$pro->fecha_ingreso,$alerta,$pro->nombre_seccion);
          

              $i++;
          }

        }else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
          $this->table->add_row($msg);
        }

          if ($this->input->is_ajax_request()) {
            echo "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
            return false;
        }

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
            $especifico = ($this->uri->segment(5) != 0) ?   $this->Especifico->obtenerEspecifico($this->uri->segment(5)) : 'N/E' ;

                  $buscar = array(
                    'name' => 'buscar',
                    'type' => 'search',
                    'placeholder' => 'Escriba el nombre del producto a buscar',
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                    'id' => 'buscar',
                    'url' => 'index.php/Tactico/Lento_movimiento/Reporte/'.$this->uri->segment(4).'/'.$this->uri->segment(5));

                  $table =  "<div class='content_table '>" .
                           "<div class='limit-content-title'>".
                             "<div class='title-reporte'>".
                               "Reporte de productos con lento movimiento.".
                             "</div>".
                             "<div class='title-header'>
                               <ul>
                                 <li>Fecha emisión: ".date('d/m/Y')."</li>
                                 <li>Nombre la compañia: MTPS</li>
                                 <li>N° pagina: ". $pag .'/'. $pags ."</li>
                                 <li>Nombre pantalla:</li>
                                 <li>Usuario: ".$USER['nombre_completo']."</li>
                                 <br />
                                 <li>Parametros: ".$fuente." ".$especifico." ". $this->uri->segment(4) . " - " . $this->uri->segment(5)."</li>
                               </ul>
                             </div>".
                           "</div>".
                           "<div class='limit-content'>" .
                           "<div class='exportar'><a href='".base_url('/index.php/Tactico/Lento_movimiento/ReporteExcel/'.$this->uri->segment(4).'/'
                           .$this->uri->segment(5))."' class='icono icon-file-excel'>
                           Exportar Excel</a><span class='content_buscar'><i class='glyphicon glyphicon-search'></i>".form_input($buscar)."</span></div>" . "<div class='table-content'><div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div></div>";
          $data['body'] = $table;

    } else {
          $data['body'] = $this->load->view('Tactico/lento_movimiento_view', '', TRUE);
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
                   ->setTitle("Reporte Productos con lento movimiento .")
                   ->setSubject("Reporte Productos con lento movimiento .")
                   ->setDescription("Reporte generado para evitar compras innecesarias cuando aún hay existencias. ")
                   ->setKeywords("office PHPExcel php")
                   ->setCategory("Reporte Productos con lento movimiento .");

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', 'Número Producto')
                   ->setCellValue('B1', 'U.M')
                   ->setCellValue('C1', 'Existencia')
                   ->setCellValue('D1', 'Fuente de Fondos')
                   ->setCellValue('E1', 'Fecha de Ingreso')
                   ->setCellValue('F1', 'Alerta')
                   ->setCellValue('G1', 'Seccion Solicitante');

      $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($estilo_titulo);

      $registros = $this->Producto->obtenerProductosFuenteTodo($this->uri->segment(4),$this->uri->segment(5));
      if (!($registros == FALSE)) {
        $i = 2;
        $fuente=$this->uri->segment(4);
        foreach($registros as $pro) {
          $entradas=0;
          $salidas=0;
          $kardex=$this->Detalle_solicitud_producto_model->obtenerKardexProducto($pro->id_detalleproducto);
          foreach ($kardex as $kar) {
            //if($pro->id_fuentes==$fuente && $pro->id_fuentes==$kar->id_fuentes){
              if($kar->movimiento=='SALIDA'){
                $salidas=$salidas+$kar->cantidad;
              }else{
                $entradas=$entradas+$kar->cantidad;
              }
          //}
          }
          date_default_timezone_set('America/El_Salvador');
          $anyo=20;
          $fecha_actual=date($anyo."y-m-d");
          $ingreso=$pro->fecha_ingreso;
          if (!is_integer($fecha_actual)) $fecha_actual = strtotime($fecha_actual);
          if (!is_integer($ingreso)) $ingreso = strtotime($ingreso);
          $dif=floor(abs($fecha_actual - $ingreso) / 60 / 60 / 24);
          $alerta;
          if($dif<30){
            $alerta='Normal';
          }elseif ($dif>30 && $dif<45) {
            $alerta='Lento';
          }elseif ($dif>60) {
            $alerta='Muy Lento';
          }
          $entradas=$entradas-$salidas;
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, $pro->numero_producto)
                      ->setCellValue('B'.$i, $pro->nombre)
                      ->setCellValue('C'.$i, $pro->existencia)
                      ->setCellValue('D'.$i, $pro->nombre_fuente)
                      ->setCellValue('E'.$i, $pro->fecha_ingreso)
                      ->setCellValue('F'.$i, $alerta)
                      ->setCellValue('G'.$i, $pro->nombre_seccion);
          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estilo_contenido);
          $i++;
        }

        foreach(range('A','I') as $columnID){
          $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename='reporte_lento_mov.xlsx'");
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
      }
    } else {
      redirect('Login');
    }
  }
}
?>
