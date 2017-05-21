<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detalle_orden_resumen extends CI_Controller {

 public function __construct() {
   parent::__construct();
   if($this->session->userdata('logged_in') == FALSE){
     redirect('login/index/error_no_autenticado');
   }
   $this->load->helper(array('form', 'paginacion'));
   $this->load->library('table');
   $this->load->model(array('Compras/Detalle_orden_resumen_model','Bodega/Detalle_solicitud_producto_model',
   'Compras/Solicitud_Compra_Model'));
 }

 public function index(){
     $data['title'] = "Detalle Sol.";
     $data['js'] = "assets/js/validate/detalle_disponibilidad.js";

     $msg['id_orden_compra'] = $this->uri->segment(4);
     $solicitud_compra=$this->Detalle_orden_resumen_model->obtenerSolicitudCompleta($this->uri->segment(4));
     $estado=$solicitud_compra->estado_solicitud_compra;
     $nivel=$solicitud_compra->nivel_solicitud;
     $numero=$this->Solicitud_Compra_Model->obtenerIdSolicitudCompra();
     $modulo=$this->User_model->obtenerModuloNombre($this->uri->segment(5));
     $id_modulo=$this->uri->segment(5);
     $men = array('alert' => $this->uri->segment(6),'controller'=>'Detalle_orden_resumen','estado'=>$estado,
     'numero'=>$numero-1,'nivel'=>$nivel,'modulo'=>$modulo,'id_modulo'=>$id_modulo);

     $data['body'] = $this->load->view('mensajes', $men, TRUE) . $this->load->view('Compras/detalle_orden_resumen_view',$msg,TRUE) .
                     "<br><div class='content_table '>" .
                     "<div class='limit-content-title'><span class='icono icon-table icon-title'> Productos, bienes o servicios </span></div>".
                     "<div class='limit-content'>" . $this->mostrarTabla() . "</div>";
     $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
     $this->load->view('base', $data);
 }

 public function mostrarTabla($accion = TRUE){
   /*
   * Configuracion de la tabla    */
   $template = array(
       'table_open' => '<table class="table table-striped table-bordered">'
   );
   $this->table->set_template($template);
   $this->table->set_heading('#','Especifico','Producto','U.M','Cantidad','Descripción','Eliminar','Editar');

   $registros;

   if ($this->input->is_ajax_request()) {
   } else {
         $registros = $this->Detalle_orden_resumen_model->obtenerDetalleSolicitudCompra($this->uri->segment(4));
   }

   if (!($registros == FALSE)) {
     $i = 1;
     foreach($registros as $det) {
       $datos=$this->Detalle_solicitud_producto_model->obtenerDatos($det->id_detalleproducto);
       foreach ($datos as $detsol) {
         $solicitud_compra=$this->Detalle_orden_resumen_model->obtenerSolicitudCompleta($det->id_orden_compra);
         $estado=$solicitud_compra->estado_solicitud_compra;
         $onClick = "llenarFormulario('solicitud', ['id_detalle_orden_resumen', 'producto', 'autocomplete1', 'cantidad'],
                     [$det->id_detalle_orden_resumen, '$det->id_detalleproducto', '$detsol->producto','$det->cantidad'],false,false,false,'especificaciones','$det->especificaciones')";
         if($estado=='APROBADA COMPRAS' && $solicitud_compra->nivel_solicitud==6 || $solicitud_compra->nivel_solicitud>5 ){
           $eliminar='<a class="icono icon-denegar"></a>';
           $editar='<a class="icono icon-denegar"></a>';
         }
         else{
           $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Compras/Detalle_orden_resumen/EliminarDato/'
           .$det->id_detalle_orden_resumen.'/'.$det->id_orden_compra.'/'.$this->uri->segment(5)).'></a>';
           $editar='<a class="icono icon-pencil" onClick="'.$onClick.'"></a>';
         }
           $this->table->add_row($i,$detsol->id_especifico,$detsol->producto,$detsol->unidad,$det->cantidad,
           $det->especificaciones,$eliminar,$editar);
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
   $modulo=$this->input->post('id_modulo');
   $mod = $this->User_model->obtenerModulo('Compras/Detalle_orden_resumen');
   $USER = $this->session->userdata('logged_in');
   $precio=0.0;
   $data = array(
       'id_detalle_orden_resumen' =>$this->input->post('id_detalle_orden_resumen'),
       'id_detalleproducto'=>$this->input->post('producto'),
       'cantidad' => $this->input->post('cantidad'),
       'id_orden_compra'=>$this->input->post('orden'),
       'total' => 0,
       'precio' => 0,
       'especificaciones'=>$this->input->post('especificaciones')
   );
   date_default_timezone_set('America/El_Salvador');
   $anyo=20;
   $fecha_actual=date($anyo."y-m-d");
   $hora=date("H:i:s");
   $rastrea = array(
     'id_usuario' =>$USER['id'],
     'id_modulo' =>$mod,
     'fecha' =>$fecha_actual,
     'hora' =>$hora,
   );
   $detalles=$this->Detalle_orden_resumen_model->obtenerDetallesSolicitud($data['id_orden_compra']);
   $detalle=$this->Detalle_orden_resumen_model->obtenerEspecifico($this->input->post('producto'));
     if (!($data['id_detalle_orden_resumen'] == '')){
         $this->Detalle_orden_resumen_model->actualizarDetalleSolicitudCompra($data['id_detalle_orden_resumen'],$data);
         $rastrea['operacion']='ACTUALIZA';
         $rastrea['id_registro']=$this->input->post('id_detalle_orden_resumen');
         $this->User_model->insertarRastreabilidad($rastrea);
         redirect('/Compras/Detalle_orden_resumen/index/'.$data['id_orden_compra'].'/'.$modulo.'/update');
     }else{
       if(!($detalles=='')){
           if($detalles->id_especifico==$detalle->id_especifico){
             if($detalles->nombre==$detalle->nombre && $detalles->id_unidad_medida==$detalle->id_unidad_medida){
               redirect('/Compras/Detalle_orden_resumen/index/'.$data['id_orden_compra'].'/'.$modulo.'/mismo');
             }else{
               $this->Detalle_orden_resumen_model->insertarDetalleSolicitudCompra($data);
               $rastrea['operacion']='INSERTA';
               $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_orden_resumen')-1;
               $this->User_model->insertarRastreabilidad($rastrea);
               redirect('/Compras/Detalle_orden_resumen/index/'.$data['id_orden_compra'].'/'.$modulo.'/new');
             }
           }else{
             redirect('/Compras/Detalle_orden_resumen/index/'.$data['id_orden_compra'].'/'.$modulo.'/noespecifico');
           }
       }else{
          $this->Detalle_orden_resumen_model->insertarDetalleSolicitudCompra($data);
          $rastrea['operacion']='INSERTA';
          $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_orden_resumen')-1;
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Compras/Detalle_orden_resumen/index/'.$data['id_orden_compra'].'/'.$modulo.'/new');
       }
     }

 }

 public function EliminarDato(){
   $mod = $this->User_model->obtenerModulo('Compras/Detalle_orden_resumen');
   $USER = $this->session->userdata('logged_in');
     $id = $this->uri->segment(4);
     date_default_timezone_set('America/El_Salvador');
     $anyo=20;
     $fecha_actual=date($anyo."y-m-d");
     $hora=date("H:i:s");
     $rastrea = array(
       'id_usuario' =>$USER['id'],
       'id_modulo' =>$mod,
       'fecha' =>$fecha_actual,
       'hora' =>$hora,
       'operacion' =>'ELIMINA',
       'id_registro' =>$this->uri->segment(4),
     );
       $this->Detalle_orden_resumen_model->eliminarDetalleSolicitudCompra($id);
       $this->User_model->insertarRastreabilidad($rastrea);
       redirect('/Compras/Detalle_orden_resumen/index/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/delete');
 }
 public function AutocompleteDetalleOrdenResumen(){
   $USER = $this->session->userdata('logged_in');
   $id_orden_compra=$this->uri->segment(4);
   if($USER){
     $registros = '';
     if ($this->input->is_ajax_request()) {
       if (!($this->input->post('autocomplete') == "")) {
           $registros = $this->Detalle_orden_resumen_model->buscarDetalleOrdenResumen($id_orden_compra,
           $this->input->post('autocomplete'));
       } else {
           $registros = $this->Detalle_orden_resumen_model->obtenerDetalleOrdenResumen($id_orden_compra);
       }
     } else {
           $registros = $this->Detalle_orden_resumen_model->obtenerDetalleOrdenResumen($id_orden_compra);
     }
     if ($registros == '') {
       echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
     }else {
       $i = 1;
       foreach ($registros as $producto) {
           echo '<div id="'.$i.'" class="suggest-element" ida="producto'.$producto->id_detalleproducto.'"><a id="producto'.
           $producto->id_detalleproducto.'" data="'.$producto->id_detalleproducto.'"  data1="'.$producto->id_especifico.' - '.$producto->nombre.' - '.$producto->nombre_unidad.'"
           data2="'.$producto->cantidad.'" data3="'.$producto->especificaciones.'">'
           .$producto->id_especifico.' - '.$producto->nombre.' - '.$producto->nombre_unidad.'</a></div>';
           $i++;
       }
     }
   } else {
     redirect('login');
   }
 }
}
?>
