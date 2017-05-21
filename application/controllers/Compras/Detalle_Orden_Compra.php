<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detalle_orden_compra extends CI_Controller {

 public function __construct() {
   parent::__construct();
   if($this->session->userdata('logged_in') == FALSE){
     redirect('login/index/error_no_autenticado');
   }
   $this->load->helper(array('form', 'paginacion'));
   $this->load->library('table');
   $this->load->model(array('Compras/Detalle_solicitud_compra_model','Bodega/Detalle_solicitud_producto_model',
   'Compras/Solicitud_Compra_Model'));
 }

 public function index(){

   if ($this->uri->segment(4) == '' || $this->Solicitud_Compra_Model->obtenerSolicitudCompra($this->uri->segment(4)) == '') {
     $data['body'] = "ERRROR";
     $this->load->view('base', $data);
   } else {
     $data['title'] = "Detalle Sol.";
     $data['js'] = "assets/js/validate/detalle_disponibilidad.js";

     $msg['id_solicitud_compra'] = $this->uri->segment(4);
     $solicitud_compra=$this->Solicitud_Compra_Model->obtenerSolicitudCompleta($this->uri->segment(4));
     $estado=$solicitud_compra->estado_solicitud_compra;
     $nivel=$solicitud_compra->nivel_solicitud;
     $numero=$this->Solicitud_Compra_Model->obtenerIdSolicitudCompra();
     $modulo=$this->User_model->obtenerModuloNombre($this->uri->segment(5));
     $id_modulo=$this->uri->segment(5);
     $men = array('alert' => $this->uri->segment(6),'controller'=>'Detalle_Orden_Compra','estado'=>$estado,
     'numero'=>$numero-1,'nivel'=>$nivel,'modulo'=>$modulo,'id_modulo'=>$id_modulo);

     $data['body'] = $this->load->view('mensajes', $men, TRUE) . $this->load->view('Compras/detalle_orden_compra_view',$msg,TRUE) .
                     "<br><div class='content_table '>" .
                     "<div class='limit-content-title'><span class='icono icon-table icon-title'> Productos, bienes o servicios </span></div>".
                     "<div class='limit-content'>" . $this->mostrarTabla() . "</div>";
     $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
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
   $this->table->set_heading('#','Especifico','Producto','Unidad de Medida','Cantidad','Especificaciones','Eliminar','Editar');

   $registros;

   if ($this->input->is_ajax_request()) {
   } else {
         $registros = $this->Detalle_solicitud_compra_model->obtenerDetalleSolicitudCompra($this->uri->segment(4));
   }

   if (!($registros == FALSE)) {
     $i = 1;
     foreach($registros as $det) {
       $datos=$this->Detalle_solicitud_producto_model->obtenerDatos($det->id_detalleproducto);
       foreach ($datos as $detsol) {
         $solicitud_compra=$this->Solicitud_Compra_Model->obtenerSolicitudCompleta($det->id_solicitud_compra);
         $estado=$solicitud_compra->estado_solicitud_compra;
         $onClick = "llenarFormulario('solicitud', ['id_detalle_solicitud_compra', 'producto', 'autocomplete1', 'cantidad'],
                     [$det->id_detalle_solicitud_compra, '$det->id_detalleproducto', '$detsol->producto','$det->cantidad',
                     '$det->precio'],false,false,false,'especificaciones','$det->especificaciones')";
        if($solicitud_compra->nivel_solicitud>6){
            $eliminar = '<a class="icono icon-denegar"></a>';
            $editar = '<a class="icono icon-denegar"></a>';
        }else{
          $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Compras/Detalle_Orden_Compra/EliminarDato/'
          .$det->id_detalle_solicitud_compra.'/'.$det->id_solicitud_compra.'/'.$this->uri->segment(5)).'></a>';
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
   $mod = $this->User_model->obtenerModulo('Compras/Detalle_orden_compra');
   $USER = $this->session->userdata('logged_in');
   $data = array(
       'id_detalle_solicitud_compra' =>$this->input->post('id_detalle_solicitud_compra'),
       'id_detalleproducto'=>$this->input->post('producto'),
       'cantidad' => $this->input->post('cantidad'),
       'precio' => 0.0,
       'id_solicitud_compra'=>$this->input->post('solicitud'),
       'total'=>0.0,
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
   $detalles=$this->Detalle_solicitud_compra_model->obtenerDetallesSolicitud($data['id_solicitud_compra']);
   $detalle=$this->Detalle_solicitud_compra_model->obtenerEspecifico($this->input->post('producto'));
     if (!($data['id_detalle_solicitud_compra'] == '')){
         $this->Detalle_solicitud_compra_model->actualizarDetalleSolicitudCompra($data['id_detalle_solicitud_compra'],$data);
         $rastrea['operacion']='ACTUALIZA';
         $rastrea['id_registro']=$this->input->post('id_detalle_solicitud_compra');
         $this->User_model->insertarRastreabilidad($rastrea);
         redirect('/Compras/Detalle_Orden_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/update');
     }else{
       if(!($detalles=='')){
           if($detalles->id_especifico==$detalle->id_especifico){
             if($detalles->nombre==$detalle->nombre){
               redirect('/Compras/Detalle_Orden_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/mismo');
             }else{
               $this->Detalle_solicitud_compra_model->insertarDetalleSolicitudCompra($data);
               $rastrea['operacion']='INSERTA';
               $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_compra')-1;
               $this->User_model->insertarRastreabilidad($rastrea);
               redirect('/Compras/Detalle_Orden_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/new');
             }
           }else{
             redirect('/Compras/Detalle_Orden_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/noespecifico');
           }
       }else{
          $this->Detalle_solicitud_compra_model->insertarDetalleSolicitudCompra($data);
          $rastrea['operacion']='INSERTA';
          $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_compra')-1;
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Compras/Detalle_Orden_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/new');
       }
     }

 }

 public function EliminarDato(){
   $USER = $this->session->userdata('logged_in');
   $mod = $this->User_model->obtenerModulo('Compras/Detalle_orden_compra');
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
     $id = $this->uri->segment(4);
       $this->Detalle_solicitud_compra_model->eliminarDetalleSolicitudCompra($id);
       $this->User_model->insertarRastreabilidad($rastrea);
       redirect('/Compras/Detalle_Orden_Compra/index/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/delete');
 }
}
?>
