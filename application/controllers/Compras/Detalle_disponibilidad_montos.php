<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detalle_disponibilidad_montos extends CI_Controller {

 public function __construct() {
   parent::__construct();
   if($this->session->userdata('logged_in') == FALSE){
     redirect('login/index/error_no_autenticado');
   }
   $this->load->helper(array('form', 'paginacion'));
   $this->load->library('table');
   $this->load->model(array('Compras/Detalle_solicitud_compra_model','Bodega/Detalle_solicitud_producto_model',
   'Compras/Solicitud_Compra_Model','Compras/Detalle_disponibilidad_montos_model'));
 }

 public function index(){

   if ($this->uri->segment(4) == '') {
     $data['body'] = "ERRROR";
     $this->load->view('base', $data);
   } else {
     $solicitud_compra=$this->Detalle_disponibilidad_montos_model->obtenerSolicitudCompra($this->uri->segment(4));
     $data['title'] = "Detalle Sol.";
     $dis=$this->uri->segment(5);
     $data['js'] = "assets/js/validate/detalle_montos.js";
     $msg['disponibilidad'] = $this->uri->segment(4);
     $men = array('alert' => $this->uri->segment(5),'controller'=>'Detalle_disponibilidad_montos',
     'compra'=>$solicitud_compra);

     $data['body'] = $this->load->view('mensajes', $men, TRUE) . $this->load->view('Compras/detalle_disponibilidad_montos_view',$msg,TRUE) .
                     "<br><div class='content_table '>" .
                     "<div class='limit-content-title'><span class='icono icon-table icon-title'> Montos </span></div>".
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
   $this->table->set_heading('#','Requerimiento','Disponibilidad','Linea','Monto','Eliminar','Editar');

   $registros;

   if ($this->input->is_ajax_request()) {
   } else {
         $registros = $this->Detalle_disponibilidad_montos_model->obtenerDetalleDisponibilidad($this->uri->segment(4));
   }

   if (!($registros == FALSE)) {
     $i = 1;
       foreach ($registros as $det) {
         $onClick = "llenarFormulario('solicitud', ['id_detalle_solicitud_disponibilidad', 'linea',
         'autocomplete1', 'monto'],[$det->id_detalle_solicitud_disponibilidad, '$det->id_linea_trabajo',
          '$det->linea_trabajo','$det->monto_sub_total'])";
         if($det->estado_solicitud_compra=='APROBADA COMPRAS' && $det->estado_solicitud_compra==6 || $det->nivel_solicitud>5 ){
           $eliminar='<a class="icono icon-denegar"></a>';
           $editar='<a class="icono icon-denegar"></a>';
         }
         else{
           $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Compras/Detalle_disponibilidad_montos/EliminarDato/'
           .$det->id_detalle_solicitud_disponibilidad.'/'.$this->uri->segment(5)).'></a>';
           $editar='<a class="icono icon-pencil" onClick="'.$onClick.'"></a>';
         }
           $this->table->add_row($i,$det->id_solicitud_compra,$det->id_solicitud_disponibilidad,
           $det->linea_trabajo,'$'.number_format($det->monto_sub_total,2),$eliminar,$editar);
         $i++;
       }
   } else {
     $msg = array('data' => "No se encontraron resultados", 'colspan' => "7");
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
   $mod = $this->User_model->obtenerModulo('Compras/Detalle_disponibilidad_montos');
   $USER = $this->session->userdata('logged_in');
   $precio=0.0;
   $data = array(
       'id_detalle_solicitud_disponibilidad' =>$this->input->post('id_detalle_solicitud_disponibilidad'),
       'id_linea_trabajo'=>$this->input->post('linea'),
       'monto_sub_total' => $this->input->post('monto'),
       'id_solicitud_disponibilidad'=>$this->input->post('disponibilidad')
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
     if (!($data['id_detalle_solicitud_disponibilidad'] == '')){
         $this->Detalle_disponibilidad_montos_model->actualizarDetalleDisponibilidad($data['id_detalle_solicitud_disponibilidad'],$data);
         $rastrea['operacion']='ACTUALIZA';
         $rastrea['id_registro']=$this->input->post('id_detalle_solicitud_disponibilidad');
         $this->User_model->insertarRastreabilidad($rastrea);
         redirect('/Compras/Detalle_disponibilidad_montos/index/'.$data['id_solicitud_disponibilidad'].'/update');
     }else{
               $this->Detalle_disponibilidad_montos_model->insertarDetalleDisponibilidad($data);
               $rastrea['operacion']='INSERTA';
               $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_disponibilidad')-1;
               $this->User_model->insertarRastreabilidad($rastrea);
               redirect('/Compras/Detalle_disponibilidad_montos/index/'.$data['id_solicitud_disponibilidad'].'/new');
             }
 }

 public function EliminarDato(){
   $mod = $this->User_model->obtenerModulo('Compras/Detalle_disponibilidad_montos');
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
       $this->Detalle_disponibilidad_montos_model->eliminarDetalleDisponibilidad($id);
       $this->User_model->insertarRastreabilidad($rastrea);
       redirect('/Compras/Detalle_disponibilidad_montos/index/'.$this->uri->segment(5).'/delete');
 }
}
?>
