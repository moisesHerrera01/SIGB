<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends CI_Controller {

  function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('Login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Producto', 'Bodega/UnidadMedida','Bodega/Detalle_solicitud_producto_model', 'Bodega/DetalleProducto_model'));
  }

  public function index(){

    $data['title'] = "Producto";
    $data['js'] = "assets/js/validate/producto.js";

    $msg = array('alert' => $this->uri->segment(4), );

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Bodega/Productos',$msg,TRUE) .
                    "<br><div class='content_table '>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Productos</span></div>".
                    "<div class='limit-content'>"  . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      /*
      * Configuracion de la tabla
      */

      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('#','Nombre', 'Unidad Medida', 'Descripción', 'Estado', 'Fecha Caducidad',
                                'Stock Minimo' ,'Modificar', 'Eliminar');

      /*
      * Filtro a la BD
      */
      /*Obtiene el numero de registros a mostrar por pagina */
      $num = '15';
      $pagination = '';
      $registros;
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('busca') == "")) {
            $registros = $this->Producto->buscarProductos($this->input->post('busca'));
        } else {
            $registros = $this->Producto->obtenerProdutosLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Bodega/Productos/index/', $this->Producto->totalProdutos(),
                          $num, '4');
        }
      } else {
            $registros = $this->Producto->obtenerProdutosLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Bodega/Productos/index/', $this->Producto->totalProdutos(),
                          $num, '4');
      }

      /*
      * llena la tabla con los datos consultados
      */

      if (!($registros == FALSE)) {
        foreach($registros as $producto) {
            $nmUnidad = $this->UnidadMedida->obtenerUnidad($producto->id_unidad_medida);
            $onClick = "llenarFormulario('Producto', ['id', 'nombre', 'unidadMedida', 'autocomplete','descripcion', 'fecha', 'stok'],
                        [$producto->id_producto, '$producto->nombre', '$producto->id_unidad_medida', '$nmUnidad','$producto->descripcion',
                        '$producto->fecha_caducidad', '$producto->stock_minimo'], 'estado', '$producto->estado')";

            $this->table->add_row($producto->id_producto, $producto->nombre, $nmUnidad, $producto->descripcion, $producto->estado, $producto->fecha_caducidad, $producto->stock_minimo,
                            //form_button($btn_act), $form_el,
                            '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                            '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/Productos/EliminarDato/'.$producto->id_producto).'></a>');
        }
      } else {
        $msg = array('data' => "Texto no encontrado", 'colspan' => "9");
        $this->table->add_row($msg);
      }

      /*
      * vuelve a verificar para mostrar los datos
      */
      if ($this->input->is_ajax_request()) {
        echo "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
      } else {
        return "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
      }
    } else {
      redirect('Login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Productos');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'nombre' => $this->input->post('nombre'),
          'id_unidad_medida' => $this->input->post('unidadMedida'),
          'descripcion' => $this->input->post('descripcion'),
          'estado' => $this->input->post('estado'),
          'fecha_caducidad' => $this->input->post('fecha'),
          'stock_minimo' => $this->input->post('stok')
      );
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $fecha_actual=date($anyo."y-m-d");
      $hora=date("H:i:s");
      $rastrea = array(
        'id_usuario' =>$USER['id'],
        'id_modulo' =>$modulo,
        'fecha' =>$fecha_actual,
        'hora' =>$hora,
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          $this->Producto->actualizarProducto($this->input->post('id'),$data);
          redirect('/Bodega/Productos/index/update');
        } else {
          redirect('/Bodega/Productos/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_producto');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Producto->insertarProducto($data);
        redirect('/Bodega/Productos/index/new');
      } else {
        redirect('/Bodega/Productos/index/forbidden');
      }
    } else {
      redirect('Login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Productos');
    $USER = $this->session->userdata('logged_in');
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    $hora=date("H:i:s");
    $rastrea = array(
      'id_usuario' =>$USER['id'],
      'id_modulo' =>$modulo,
      'fecha' =>$fecha_actual,
      'hora' =>$hora,
      'operacion' =>'ELIMINA',
      'id_registro' =>$this->uri->segment(4),
    );
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
        $id = $this->uri->segment(4);
        $this->Producto->eliminarProducto($id);
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Bodega/Productos/index/delete');
      } else {
        redirect('/Bodega/Productos/index/forbidden');
      }
    } else {
      redirect('Login/index/error_no_autenticado');
    }
  }

  public function Autocomplete(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->Producto->buscarProductos($this->input->post('autocomplete'));
        } else {
            $registros = $this->Producto->obtenerProductos();
        }
      } else {
            $registros = $this->Producto->obtenerProductos();
      }

      if ($registros == '') {
        echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      }else {
        $i = 1;
        foreach ($registros as $producto) {
          echo '<div id="'.$i.'" class="suggest-element" ida="producto'.$producto->id_producto.'"><a id="producto'.
          $producto->id_producto.'" data="'.$producto->id_producto.'"  data1="'.$producto->nombre .' - '. $producto->nombre_unidad .'" >'
          .$producto->nombre .' - '. $producto->nombre_unidad .'</a></div>';
          $i++;
        }
      }
    } else {
      redirect('Login');
    }
  }

  public function obtenerExistencia(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $entradas=$this->Producto->obtenerProductosExistencia('ENTRADA');
      $salidas=$this->Producto->obtenerProductosExistencia('SALIDA');
      if($salidas==FALSE){
            return $entradas;
      }else{
        foreach ($entradas as $en) {
          foreach ($salidas as $sal) {
            if($en->id_producto==$sal->id_producto){
              $en->existencia=$en->existencia-$sal->existencia;
            }
          }
        }
        $i=0;
        foreach ($entradas as $en) {
          if($en->existencia==0){
            unset($entradas[$i]);
          }
          $i++;
        }
        return $entradas;
      }
    } else {
      redirect('Login');
    }
  }

  public function obtenerExistenciaFiltrada($busca){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      error_reporting(0);
      $entradas=$this->Producto->buscarProductosExistencia($busca,'ENTRADA');
      $salidas=$this->Producto->buscarProductosExistencia($busca,'SALIDA');
      if($salidas==FALSE){
            return $entradas;
      }else{
        foreach ($entradas as $en) {
          foreach ($salidas as $sal) {
            if($en->id_producto==$sal->id_producto){
              $en->existencia=$en->existencia-$sal->existencia;
            }
          }
        }
        $i=0;
        foreach ($entradas as $en) {
          if($en->existencia==0){
            unset($entradas[$i]);
          }
          $i++;
        }
        return $entradas;
      }
    } else {
      redirect('Login');
    }
  }

  public function AutocompleteExistencia(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        $total_detalle = count($this->DetalleProducto_model->obtenerDetalleProductos());
        $porpagina = $this->input->post('porpagina');
        if ($porpagina >= $total_detalle) {
          $porpagina = $total_detalle;
        }

        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->Producto->obtenerExistenciaDetalleProducto($this->input->post('fuente'), $this->input->post('porpagina'), $this->input->post('autocomplete'));
        } else {
            $registros=$this->Producto->obtenerExistenciaDetalleProducto($this->input->post('fuente'), $porpagina);
        }
      } else {
            $registros=$this->Producto->obtenerExistenciaDetalleProducto($this->input->post('fuente'), 20);
      }

      if ($registros == '') {
        echo '<div class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      }else {
        $total = count($registros);
        $i = 1;
        foreach ($registros as $producto) {
          if ($producto->existencia > 0) {
            echo '<div id="'.$i.'" class="suggest-element" ida="producto'.$producto->id_detalleproducto.'"><a id="producto'.
            $producto->id_detalleproducto.'" data="'.$producto->id_detalleproducto.'"  data1="'.$producto->id_especifico.'-'.$producto->nombre_producto.'" >'
            .$producto->id_especifico.' - '.$producto->nombre_producto. ' - ' . $producto->nombre_unidad .'</a></div>';
          } else {
            echo '<div id="'.$i.'" class="suggest-element-not" ida="producto'.$producto->id_producto.'"><a id="producto'.
            $producto->id_producto.'" data="'.$producto->id_detalleproducto.'"  data1="'.$producto->id_especifico.'-'.$producto->nombre_producto.'" >'
            .$producto->id_especifico.' - '.$producto->nombre_producto. ' - ' . $producto->nombre_unidad .'</a></div>';
          }
          $i++;
        }
        if ($total >= 20) {
          echo '<div ida="cargar_mas" class="suggest-element"><a>CARGAR MAS</a></div>';
        }
      }
    } else {
      redirect('Login');
    }
  }

   public function RecibirMovimiento() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('fuente')!=NULL) {
          redirect('Bodega/Productos/reporte/'.$this->input->post('fuente'));
        } else {
          redirect('Bodega/Productos/reporte/');
      }
    } else {
      redirect('Login');
    }
  }
   public function reporte(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "Lento movimiento";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';
      if (($this->uri->segment(4))!=NULL) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Número Producto','Especifico', 'Producto', 'U.M','Existencia','Fuente Fondos',
        'Fecha Registro','Alerta', 'Sección');
        $num = '10';
        $registros = $this->Producto->obtenerProductosFuenteLimit($this->uri->segment(4),$num, $this->uri->segment(5));
        $total = $this->Producto->obtenerProductosFuenteTotal($this->uri->segment(4));
        $cant=$total->numero;
        $pagination = paginacion('index.php/Bodega/Productos/reporte/'.$this->uri->segment(4),$cant,$num, '5');

        if (!($registros == FALSE)) {
          $fuente=$this->uri->segment(4);
          $i = 1;
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
            $this->table->add_row($pro->numero_producto,$pro->id_especifico,$pro->producto,$pro->unidad,
            $entradas,$pro->nombre_fuente,$pro->fecha_ingreso,$alerta,$pro->nombre_seccion);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
          $this->table->add_row($msg);
        }
        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".$this->uri->segment(4)."</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar icono'><a href='".base_url('/index.php/Bodega/Productos/ReporteExcel/'.$this->uri->segment(4))."' class='icon-file-excel'>
                  Exportar Excel</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }

      $data['body'] = $this->load->view('Bodega/Reportes/lento_movimiento_view', '',TRUE) . "<br>" . $table;
      $this->load->view('base', $data);
    } else {
      redirect('Login/index/forbidden');
    }
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
      $objPHPExcel->getProperties()->setCreator("SICBAF")
                   ->setLastModifiedBy("SICBAF")
                   ->setTitle("Reporte Productos con lento movimiento .")
                   ->setSubject("Reporte Productos con lento movimiento .")
                   ->setDescription("Reporte generado para evitar compras innecesarias cuando aún hay existencias. ")
                   ->setKeywords("office PHPExcel php")
                   ->setCategory("Reporte Productos con lento movimiento .");

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', 'Número Producto')
                   ->setCellValue('B1', 'Especifico')
                   ->setCellValue('C1', 'Producto')
                   ->setCellValue('D1', 'U.M')
                   ->setCellValue('E1', 'Existecia')
                   ->setCellValue('F1', 'Fuente Fondos')
                   ->setCellValue('G1', 'Fecha Registro')
                   ->setCellValue('H1', 'Alerta')
                   ->setCellValue('I1', 'Sección');
      $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($estilo_titulo);

      $registros = $this->Producto->obtenerProductosFuenteTodo($this->uri->segment(4));
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
                      ->setCellValue('B'.$i, $pro->id_especifico)
                      ->setCellValue('C'.$i, $pro->producto)
                      ->setCellValue('D'.$i, $pro->unidad)
                      ->setCellValue('E'.$i, $entradas)
                      ->setCellValue('F'.$i, $pro->nombre_fuente)
                      ->setCellValue('G'.$i, $pro->fecha_ingreso)
                      ->setCellValue('H'.$i, $alerta)
                      ->setCellValue('I'.$i, $pro->nombre_seccion);
          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($estilo_contenido);
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
