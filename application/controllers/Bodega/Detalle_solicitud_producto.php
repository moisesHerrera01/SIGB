 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detalle_solicitud_producto extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Detalle_solicitud_producto_model', 'Bodega/Producto','Bodega/Solicitud_Model',
    'Bodega/Fuentefondos_model','Bodega/UnidadMedida','Compras/Detalle_solicitud_compra_model', 'Bodega/Kardex_model'));
  }

  public function index(){

    if ($this->uri->segment(4) == '' || $this->Solicitud_Model->obtenerSolicitud($this->uri->segment(4)) == '') {
      $data['body'] = "ERRROR";
      $this->load->view('base', $data);
    } else {
      $data['title'] = "Solicitud Productos";
      $data['js'] = "assets/js/validate/detsol.js";
      $numero=$this->Solicitud_Model->obtenerId();
      $USER = $this->session->userdata('logged_in');
      $rol=$USER['id_rol'];
      $estado=$this->Solicitud_Model->retornarEstado($this->uri->segment(4));
      $solicitud=$this->Solicitud_Model->obtenerTodaSolicitud($this->uri->segment(4));
      foreach ($solicitud as $sol) {
        $fuente=$sol->id_fuentes;
        $nivel=$sol->nivel_solicitud;
      }
      $msg = array('id_solicitud' => $this->uri->segment(4),'id_fuente' => $fuente,'controller'=>'detalle_solicitud_producto','nivel'=>$nivel);
      $men = array('alert' => $this->uri->segment(5),'controller'=>'detalle_solicitud_producto','numero'=>$numero-1,
      'estado'=>$estado,'nivel'=>$nivel,'rol'=>$rol);

    	$data['body'] = $this->load->view('mensajes', $men, TRUE) . $this->load->view('Bodega/detalle_solicitud_producto_view',$msg,TRUE) .
                      "<br><div class='content_table '>" .
                      "<div class='limit-content-title'><span class='icono icon-table icon-title'> Detalle Solicitud </span></div>".
                      "<div class='limit-content'>" . $this->mostrarTabla() . "</div>";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $this->load->view('base', $data);
	  }
  }

  public function MostrarDetalleSolicitud(){

    $this->load->model(array('mtps/Seccion_model'));

    if ($this->uri->segment(4) == '' || $this->Solicitud_Model->obtenerSolicitud($this->uri->segment(4)) == '') {
      $data['body'] = "ERRROR";
      $this->load->view('base', $data);
    } else {
      $data['title'] = "Detalle Solicitud";

      $solicitud = '';
      foreach ($this->Solicitud_Model->obtenerTodaSolicitud($this->uri->segment(4)) as $sol) {
        $solicitud = $sol;
      }

      $table = $this->mostrarTabla(FALSE);

      $data['body'] = "
        <div>
          <ul class='lista_detalle'>
            <li>Numero de Pedido: ".$solicitud->numero_solicitud."</li>
            <li>Fecha de Pedido: ".$solicitud->fecha_solicitud."</li>
            <li>Fecha ingreso de Pedido: ".$solicitud->fecha_salida."</li>
            <li>Requisitor: ".$this->Seccion_model->obtenerPorIdSeccion($solicitud->id_seccion)."</li>
          </ul>
          <br>
          ".$table."
        </div>
      ";
      $this->load->view('base', $data);
    }
  }

  public function mostrarTabla($accion = TRUE){
    /*
    * Configuracion de la tabla    */
    $template = array(
        'table_open' => '<table class="table table-striped table-bordered">'
    );
    $this->table->set_template($template);
    ($accion ? $this->table->set_heading('Producto','Unidad de Medida','Cantidad','Fuente Fondos','Estado','Editar','Eliminar')
     : $this->table->set_heading('Producto','Unidad de Medida','Cantidad','Fuente Fondos','Estado') );

    $registros;

    if ($this->input->is_ajax_request()) {
    } else {
          $registros = $this->Detalle_solicitud_producto_model->obtenerDetalleSolicitudProductos($this->uri->segment(4));
    }

    if (!($registros == FALSE)) {
      $i = 1;
      foreach($registros as $det) {
        $estado_solicitud=$this->Solicitud_Model->retornarEstado($det->id_solicitud);
        $datos=$this->Detalle_solicitud_producto_model->obtenerDatos($det->id_detalleproducto);
        $fuente=$this->Fuentefondos_model->obtenerFuente($det->id_fuentes);
        foreach ($datos as $detsol) {
          if ($accion) {
            $onClick = "llenarFormulario('solicitud', ['id_detalle_solicitud_producto', 'detalleproducto', 'autocomplete1', 'cantidad','fuente','autocomplete2'],
                        [$det->id_detalle_solicitud_producto, '$det->id_detalleproducto', '$detsol->producto',
                        '$det->cantidad','$det->id_fuentes','$fuente'])";
            $editar='<a class="icono icon-pencil" onClick="'.$onClick.'"></a>';
            if($estado_solicitud=='DENEGADA' || $estado_solicitud=='APROBADA' || $estado_solicitud=='LIQUIDADA'){
              $eliminar='<a class="icono icon-denegar"></a>';
              $editar='<a class="icono icon-denegar"></a>';
            }else{
              $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/Detalle_solicitud_producto/EliminarDato/'
              .$det->id_detalle_solicitud_producto.'/'.$det->id_solicitud.'/'.$det->total).'></a>';
            }
            $this->table->add_row($detsol->producto,$detsol->unidad,$det->cantidad,$fuente,
            $det->estado_solicitud_producto,$editar,$eliminar);
          } else {
            $this->table->add_row($detsol->producto,$detsol->unidad,$det->cantidad,$fuente,
            $det->estado_solicitud_producto);
          }
        $i++;
      }
      }
    } else {
      $msg = array('data' => "No se encontraron resultados", 'colspan' => "8");
      $this->table->add_row($msg);
    }

    if ($this->input->is_ajax_request()) {
      echo "<div class='table-responsive'>" . $this->table->generate() . "</div>";
    } else {
      return "<div class='table-responsive'>" . $this->table->generate() . "</div>";
    }
  }
  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $precio=0.0;
    $solicitud = $this->Solicitud_Model->obtenerTodaSolicitud($this->input->post('solicitud'));
    $data = array(
        'id_detalle_solicitud_producto' =>$this->input->post('id_detalle_solicitud_producto'),
        'id_detalleproducto'=>$this->input->post('detalleproducto'), // se aclara que este es el detalle del producto
        'cantidad' => $this->input->post('cantidad'),
        'precio' => $precio,
        'id_solicitud'=>$this->input->post('solicitud'),
        'id_fuentes' => $solicitud[0]->id_fuentes,
        'total'=>0.0
    );
    if ($this->Kardex_model->obtenerExistenciaFuente($data['id_detalleproducto'], $data['id_fuentes']) >= $data['cantidad']) {
      $modulo=$this->User_model->obtenerModulo('Bodega/detalle_solicitud_producto');
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
      );
      if (!($data['id_detalle_solicitud_producto'] == '')){
        $rastrea['operacion']='ACTUALIZA';
        $rastrea['id_registro']=$data['id_detalle_solicitud_producto'];
        $this->User_model->insertarRastreabilidad($rastrea);
          $this->Detalle_solicitud_producto_model->actualizarDetalleSolicitudProducto($data['id_detalle_solicitud_producto'],$data);
          redirect('/Bodega/Detalle_solicitud_producto/index/'.$data['id_solicitud'].'/update');
      }else{
        $detalles=$this->Detalle_solicitud_producto_model->obtenerDetallesSolicitud($data['id_solicitud']);
        $detalle=$this->Detalle_solicitud_compra_model->obtenerEspecifico($data['id_detalleproducto']);
        $i='0';
        foreach ($detalles as $det) {
          if($det->nombre==$detalle->nombre){
            $i='1';
          }
        }
        if($i=='1'){
          redirect('/Bodega/Detalle_solicitud_producto/index/'.$data['id_solicitud'].'/mismos');
        }else{
          $rastrea['operacion']='INSERTA';
          $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_producto');
          $this->User_model->insertarRastreabilidad($rastrea);
          $this->Detalle_solicitud_producto_model->insertarDetalleSolicitudProducto($data);
          redirect('/Bodega/Detalle_solicitud_producto/index/'.$data['id_solicitud'].'/new');
        }
      }
    } else {
      redirect('/Bodega/Detalle_solicitud_producto/index/'.$data['id_solicitud'].'/sin_existencia');
    }
  }

  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Bodega/detalle_solicitud_producto');
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
      $id = $this->uri->segment(4);
      $detalle=$this->Detalle_solicitud_producto_model->obtenerDetalleCompleto($id);
      if($detalle->precio>0){
        redirect('/Bodega/Detalle_solicitud_producto/index/'.$detalle->id_solicitud.'/proceso');
      }else{
        $this->Detalle_solicitud_producto_model->eliminarDetalleSolicitudProducto($id);
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Bodega/Detalle_solicitud_producto/index/'.$detalle->id_solicitud.'/delete');
      }
  }

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
