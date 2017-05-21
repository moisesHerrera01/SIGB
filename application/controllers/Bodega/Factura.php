<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Factura extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Factura_Model', 'Bodega/Proveedor', 'Bodega/Fuentefondos_model',
    'Bodega/Solicitud_Model','Bodega/Detalle_solicitud_producto_model','Bodega/Detallefactura_Model',
    'Compras/Compromiso_Presupuestario_Model', 'Notificacion_model'));
  }

  public function index(){

    $data['title'] = "Facturas";
    $data['js'] = "assets/js/validate/factura.js";
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");

    $msg = array('alert' => $this->uri->segment(4), 'fecha'=>$fecha_actual,'controller'=>'factura');

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Bodega/factura_view',$msg,TRUE) .
                    "<br><div class='content_table '>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Facturas</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('Bodega/Factura');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'select')) {
    /*
    * Configuracion de la tabla
    */

        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Número', 'Fecha Ingreso', 'Proveedor', 'Fuente Fondo', 'Total',
                                  'Estado','Detalle', 'Liquidar', 'Acta','Actualizar','Eliminar');
        /*
        * Filtro a la BD
        */
        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Factura_Model->buscarFacturas($this->input->post('busca'));
          } else {
              $registros = $this->Factura_Model->obtenerFacturasLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/Bodega/factura/index/', $this->Factura_Model->totalFacturas(),
                            $num, '4');
          }
        } else {
              $registros = $this->Factura_Model->obtenerFacturasLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/Bodega/factura/index/', $this->Factura_Model->totalFacturas(),
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $fact) {
              $prov = $this->Proveedor->obtenerProveedor($fact->id_proveedores);
              $fuente = $this->Fuentefondos_model->obtenerFuente($fact->id_fuentes);
              $seccion = $this->Solicitud_Model->obtenerSeccion($fact->id_seccion);
              $botones;
              $onClick = "llenarFormulario('factura', ['id', 'numeroFactura', 'nombreEntrega',
               'fechaFactura','fechaIngreso','compromiso','autocomplete1'],
                          [$fact->id_factura, '$fact->numero_factura','$fact->nombre_entrega',
                          '$fact->fecha_factura', '$fact->fecha_ingreso','$fact->numero_compromiso',
                          '$fact->numero_compromiso'], false, false,
                           false, 'comentario_productos', '$fact->comentario_productos')";

              if($fact->estado=='INGRESADA'){
                  $botones='<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>';
                  $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/factura/EliminarDato/'.$fact->id_factura).'></a>';
                  $liquidada = '<a class="icono icon-liquidar" href="'.base_url('index.php/Bodega/factura/Liquidar/'.$fact->id_factura.'/'.$fact->id_fuentes.'/').'"></a>';
                  $acta = '<a class="icono icon-lock"></a>';

              } else {
                  $botones='<a class="icono icon-denegar"></a>';
                  $eliminar='<a class="icono icon-denegar"></a>';
                  $liquidada = '<a class="icono icon-denegar" href="'.base_url('index.php/Bodega/factura/Liquidar/'.$fact->id_factura.'/'.$fact->id_fuentes.'/').'"></a>';
                  $acta = '<a class="icono icon-acta" href="'.base_url('index.php/Bodega/Acta/index/'.$fact->id_factura.'/'.$fact->id_fuentes.'/').'" target="_blank"></a>';

              }

              if ($fact->numero_factura == 0 || $fact->id_proveedores == 0 || $fact->numero_compromiso == 0) {
                $acta = '<a class="icono icon-lock"></a>';
              }

              $this->table->add_row($fact->id_factura, $fact->numero_factura, $fact->fecha_ingreso, $prov, $fuente, $fact->total, $fact->estado,
                              '<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detallefactura/index/'.$fact->id_factura.'/').'"></a>',$liquidada,
                              $acta,$botones,$eliminar);

          }
        } else {
          $msg = array('data' => "Texto no encontrado", 'colspan' => "12");
          $this->table->add_row($msg);
        }

        /*
        * vuelve a verificar para mostrar los datos
        */
        if ($this->input->is_ajax_request()) {
          echo "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
        } else {
          return  "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
        }
      } else {
        redirect('/Bodega/factura/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */

  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Factura');
    date_default_timezone_set('America/El_Salvador');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'numero_factura' => $this->input->post('numeroFactura'),
          'id_proveedores' => $this->input->post('proveedor'),
          'nombre_entrega' => $this->input->post('nombreEntrega'),
          'fecha_factura' => $this->input->post('fechaFactura'),
          'fecha_ingreso' => $this->input->post('fechaIngreso'),
          'id_fuentes' => $this->input->post('fuente'),
          'numero_compromiso' => $this->input->post('compromiso'),
          'orden_compra' => $this->input->post('orden'),
          'id_seccion' => $this->input->post('seccion'),
          'comentario_productos' => $this->input->post('comentario_productos')
      );
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
          $this->Factura_Model->actualizarFactura($this->input->post('id'),$data);
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
        } else {
          redirect('/Bodega/factura/index/forbidden');
        }
        redirect('/Bodega/factura/index/update');
      }else{
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
            $data['hora']=date("H:i:s");
            $rastrea['operacion']='INSERTA';
            $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_factura');
            $this->Factura_Model->insertarFactura($data);
            $this->User_model->insertarRastreabilidad($rastrea);
            $detalles = $this->Compromiso_Presupuestario_Model->obtenerDetalleOrden($data['numero_compromiso']);
            foreach ($detalles as $det) {
                $datos = array(
                  'id_factura' => $rastrea['id_registro'],
                  'id_detalleproducto' => $det['id_detalleproducto'],
                  'cantidad' => $det['cantidad'],
                  'precio' => $det['precio'],
                );
                //$this->Detallefactura_Model->insertarDetalleFactura($datos);
                $this->Notificacion_model->NotificacionProductoActivoFijo($datos['id_detalleproducto']);
            }
            redirect('/Bodega/factura/index/new');
          } else {
            redirect('/Bodega/factura/index/forbidden');
          }
        }

    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Factura');
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
      $id = $this->uri->segment(4);
      if ($this->Detallefactura_Model->existeFactura($id)){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          redirect('/Bodega/factura/index/existe_fact');
        } else {
          redirect('/Bodega/factura/index/forbidden');
        }
      }
      else {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          $this->Factura_Model->eliminarFactura($id);
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Bodega/factura/index/delete');
        } else {
          redirect('/Bodega/factura/index/forbidden');
        }
      }
      }else {
        redirect('login/index/error_no_autenticado');
      }
  }

  public function Liquidar(){
      $id = $this->uri->segment(4);
      if($this->Factura_Model->retornarEstado($id)=='INGRESADA'){
        if($this->Factura_Model->validarMontoTotalFactura($id)){
        $correlativo = $this->Factura_Model->obtenerCorrelativoFuente($this->uri->segment(5));
        $this->Factura_Model->liquidar($id,$correlativo);
        redirect('/Bodega/kardex/insertar?controller=factura&&id='.$id);
      }else{
        redirect('/Bodega/factura/index/valida_monto');
      }}else{
        redirect('/Bodega/factura/index/fact_liquidada');
      }
  }
  public function Autocomplete(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Factura_Model->buscarFacturas($this->input->post('autocomplete'));
      } else {
          $registros = $this->Factura_Model->obtenerFacturas();
      }
    } else {
          $registros = $this->Factura_Model->obtenerFacturas();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $fact) {
        echo '<div id="'.$i.'" class="suggest-element" ida="factura'.$fact->id_factura.'"><a id="factura'.
        $fact->id_factura.'" data="'.$fact->id_factura.'"  data1="'.$fact->numero_factura.'" >'
        .$fact->numero_factura.'</a></div>';
        $i++;
      }
    }
  }

  public function RecibirIngresos() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('fecha_inicio')) {
      if($this->input->post('fecha_fin')==NULL){
        redirect('Bodega/factura/reporteIngresoSeccion/'.$this->input->post('fecha_inicio').'/'
        .$fecha_actual.'/'.$this->input->post('seccion'));
      }else{
        redirect('Bodega/factura/reporteIngresoSeccion/'.$this->input->post('fecha_inicio').'/'
        .$this->input->post('fecha_fin').'/'.$this->input->post('seccion'));
      }} else {
        redirect('Bodega/factura/reporteIngresoSeccion/');
    }
  }

  public function reporteIngresoSeccion(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $this->load->model(array('mtps/Seccion_model'));
      $data['title'] = "Ingreso Global";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';
      if (($this->uri->segment(4)) != '') {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Sección','Proveedor', 'Factura', 'Acta','Orden Compra','Compromiso','Fecha Ingreso',
        'Cantidad', 'Existencia','Producto','U.M');

        $num = '10';
        $registros = $this->Factura_Model->obtenerProductosSeccionLimit($this->uri->segment(4),
        $this->uri->segment(5),$this->uri->segment(6),$num, $this->uri->segment(7));
        $total = $this->Factura_Model->obtenerProductosSeccionTotal($this->uri->segment(4),
        $this->uri->segment(5),$this->uri->segment(6));
        $cant=$total->numero;
        $pagination = paginacion('index.php/Bodega/factura/reporteIngresoSeccion/'.$this->uri->segment(4).
        '/'.$this->uri->segment(5).'/'.$this->uri->segment(6),$cant,$num, '7');

        if (!($registros == FALSE)) {
          $fecha_inicio=$this->uri->segment(4);
          $fecha_fin=$this->uri->segment(5);
          $seccion=$this->uri->segment(6);
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
            }
            $entradas=$entradas-$salidas;
            $total=$this->Factura_Model->obtenerTotalFuentes($pro->id_fuentes,$pro->fecha_factura);
            $this->table->add_row($pro->nombre_seccion,$pro->nombre_proveedor,$pro->numero_factura,$pro->nombre_fuente.'-'.$total,
            $pro->orden_compra,$pro->numero_compromiso,$pro->fecha_ingreso,$pro->cantidad,$entradas,$pro->producto,$pro->unidad);
            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "11");
          $this->table->add_row($msg);
        }
        $table =  "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".$this->Seccion_model->obtenerPorIdSeccion($this->uri->segment(6)). " " . $this->uri->segment(4) . " - " . $this->uri->segment(5) ."</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar icono'><a href='".base_url('/index.php/Bodega/factura/ReporteExcel/'.$this->uri->segment(4).'/'
                  .$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icon-file-excel'>
                  Exportar Excel</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }

      $data['body'] = $this->load->view('Bodega/Reportes/ingreso_seccion_view', '',TRUE) . "<br>" . $table;
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
                 ->setTitle("Reporte Ingreso Global por Sección .")
                 ->setSubject("Reporte Ingreso Global por Sección .")
                 ->setDescription("Reporte generado para identificar los ingresos de cada solicitante. ")
                 ->setKeywords("office PHPExcel php")
                 ->setCategory("Reporte Ingreso Global por Sección .");

    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Sección')
                 ->setCellValue('B1', 'Proveedor')
                 ->setCellValue('C1', 'Factura')
                 ->setCellValue('D1', 'Acta')
                 ->setCellValue('E1', 'Orden Compra')
                 ->setCellValue('F1', 'Compromiso')
                 ->setCellValue('G1', 'Fecha Ingreso')
                 ->setCellValue('H1', 'Cantidad')
                 ->setCellValue('I1', 'Existencia')
                 ->setCellValue('J1', 'Producto')
                 ->setCellValue('K1', 'U.M');
    $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($estilo_titulo);

    $registros = $this->Factura_Model->obtenerProductosSeccionTodo($this->uri->segment(4),
    $this->uri->segment(5),$this->uri->segment(6));

    if (!($registros == FALSE)) {
      $i = 2;
      foreach($registros as $prod) {
        $entradas=0;
        $salidas=0;
        $kardex=$this->Detalle_solicitud_producto_model->obtenerKardexProducto($prod->id_detalleproducto);
        foreach ($kardex as $kar) {
            if($kar->movimiento=='SALIDA'){
              $salidas=$salidas+$kar->cantidad;
            }else{
              $entradas=$entradas+$kar->cantidad;
            }
        }
        $entradas=$entradas-$salidas;
        $total=$this->Factura_Model->obtenerTotalFuentes($prod->id_fuentes,$prod->fecha_factura);

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $prod->nombre_seccion)
                    ->setCellValue('B'.$i, $prod->nombre_proveedor)
                    ->setCellValue('C'.$i, $prod->numero_factura)
                    ->setCellValue('D'.$i, $prod->nombre_fuente.'-'.$total)
                    ->setCellValue('E'.$i, $prod->orden_compra)
                    ->setCellValue('F'.$i, $prod->numero_compromiso)
                    ->setCellValue('G'.$i, $prod->fecha_ingreso)
                    ->setCellValue('H'.$i, $prod->cantidad)
                    ->setCellValue('I'.$i, $entradas)
                    ->setCellValue('J'.$i, $prod->producto)
                    ->setCellValue('K'.$i, $prod->unidad);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->applyFromArray($estilo_contenido);
        $i++;
      }

      foreach(range('A','K') as $columnID){
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }

      $objPHPExcel->setActiveSheetIndex(0);

      header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header("Content-Disposition: attachment; filename='reporte_ingreso_seccion.xlsx'");
      header('Cache-Control: max-age=0');

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
    }
  }

}
?>
