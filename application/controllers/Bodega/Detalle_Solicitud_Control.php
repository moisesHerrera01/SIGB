<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detalle_solicitud_control extends CI_Controller {

 public function __construct() {
   parent::__construct();
   if($this->session->userdata('logged_in') == FALSE){
     redirect('login/index/error_no_autenticado');
   }
   $this->load->helper(array('form', 'paginacion'));
   $this->load->library('table');
   $this->load->model(array('Bodega/Detalle_solicitud_producto_model', 'Bodega/Producto','Bodega/Solicitud_Model',
   'Bodega/Fuentefondos_model','Bodega/UnidadMedida', 'Bodega/Kardex_model'));
 }

 public function index(){

   if ($this->uri->segment(4) == '' || $this->Solicitud_Model->obtenerSolicitud($this->uri->segment(4)) == '') {
     $data['body'] = "ERRROR";
     $this->load->view('base', $data);
   } else {
     $data['title'] = "Solicitud Productos";
     $data['js'] = "assets/js/validate/detsol.js";
     $USER = $this->session->userdata('logged_in');
     $rol=$USER['rol'];
     $estado=$this->Solicitud_Model->retornarEstado($this->uri->segment(4));
     $solicitud=$this->Solicitud_Model->obtenerTodaSolicitud($this->uri->segment(4));
     foreach ($solicitud as $sol) {
        $nivel=$sol->nivel_solicitud;
        $fuente=$sol->id_fuentes;
     }
     $msg=array('id_solicitud'=>$this->uri->segment(4), 'id_fuente' => $fuente, 'controller'=>'Detalle_Solicitud_Control','nivel'=>$nivel,'rol'=>$rol);
     $men = array('alert' => $this->uri->segment(6),'controller'=>'detalle_solicitud_producto','estado'=>$estado,'nivel'=>$nivel);
     $data['body'] = $this->load->view('mensajes', $men, TRUE) . $this->load->view('Bodega/detalle_solicitud_producto_view',$msg,TRUE) .
                     "<br><div class='content_table '>" .
                     "<div class='limit-content-title'><span class='icono icon-table icon-title'> Detalle Solicitud </span></div>".
                     "<div class='limit-content'>" . $this->mostrarTabla() . "</div>";
     $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
     $this->load->view('base', $data);
   }
 }

 public function mostrarTabla($accion = TRUE){
  $USER = $this->session->userdata('logged_in');
   /*
   * Configuracion de la tabla    */
   $template = array(
       'table_open' => '<table class="table table-striped table-bordered">'
   );
   $this->table->set_template($template);
   $this->table->set_heading('Producto','Unidad de Medida','Cantidad','Fuente Fondos','Estado','Eliminar','Editar');
   $registros;

   if ($this->input->is_ajax_request()) {
   } else {
         $registros = $this->Detalle_solicitud_producto_model->obtenerDetalleSolicitudProductos($this->uri->segment(4));
   }

   if (!($registros == FALSE)) {
     $i = 1;
     foreach($registros as $det) {
       $datos=$this->Detalle_solicitud_producto_model->obtenerDatos($det->id_detalleproducto);
       $fuente=$this->Fuentefondos_model->obtenerFuente($det->id_fuentes);
       $nivel = $this->Solicitud_Model->retornarNivel($det->id_solicitud);
       foreach ($datos as $detsol) {
         $onClick = "llenarFormulario('solicitud', ['id_detalle_solicitud_producto', 'detalleproducto', 'autocomplete1', 'cantidad','fuente','autocomplete2'],
                     [$det->id_detalle_solicitud_producto, '$det->id_detalleproducto', '$detsol->producto',
                     '$det->cantidad','$det->id_fuentes','$fuente'])";
                     if ($USER['rol'] == 'JEFE UNIDAD' || $USER['rol'] == 'JEFE BODEGA'  || $USER['rol'] == 'COLABORADOR BODEGA'  || $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'COLABORADOR COMPRAS' ||
                        $USER['rol'] == 'JEFE AF' || $USER['rol'] == 'COLABORADOR AF' || $USER['rol'] == 'JEFE UACI' || $USER['rol'] == 'COLABORADOR UACI') {
                       if ($nivel == 1 || $nivel == 2){
                         $actualizar = '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>';
                         $eliminar =  '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/Detalle_Solicitud_Control/EliminarDato/'.$det->id_detalle_solicitud_producto.'/'.$det->id_solicitud.'/'.$det->total).'></a>';
                       }
                       if ($nivel == 3 || $nivel == 4 || $nivel== 9){
                         $actualizar = '<a class="icono icon-denegar"></a>';
                         $eliminar = '<a class="icono icon-denegar"></a>';
                       }
                     } elseif ($USER['rol'] == 'DIRECTOR ADMINISTRATIVO'){
                       if ($nivel == 2 || $nivel == 3){
                         $actualizar = '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>';
                         $eliminar =  '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/Detalle_Solicitud_Control/EliminarDato/'.$det->id_detalle_solicitud_producto.'/'.$det->id_solicitud.'/'.$det->total).'></a>';
                       }
                       if ($nivel == 4 || $nivel== 9){
                         $actualizar = '<a class="icono icon-denegar"></a>';
                         $eliminar = '<a class="icono icon-denegar"></a>';
                       }
                     } elseif ($USER['rol'] == 'ADMINISTRADOR SICBAF'){
                       if ($nivel == 1 || $nivel == 2 || $nivel == 3){
                         $actualizar = '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>';
                         $eliminar =  '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/Detalle_Solicitud_Control/EliminarDato/'.$det->id_detalle_solicitud_producto.'/'.$det->id_solicitud.'/'.$det->total).'></a>';
                       }
                       if ($nivel == 4 || $nivel== 9){
                         $actualizar = '<a class="icono icon-denegar"></a>';
                         $eliminar = '<a class="icono icon-denegar"></a>';
                       }
                     }


           $this->table->add_row($detsol->producto,$detsol->unidad,$det->cantidad,$fuente,
           $det->estado_solicitud_producto,$eliminar,$actualizar);
       $i++;
     }
     }
   } else {
     $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
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
   $USER = $this->session->userdata('logged_in');
   $id_producto=$this->input->post('detalleproducto');
   $precio=0.0;
   $data = array(
       'id_detalle_solicitud_producto' =>$this->input->post('id_detalle_solicitud_producto'),
       'id_detalleproducto'=>$id_producto,
       'cantidad' => $this->input->post('cantidad'),
       'precio' => $precio,
       'id_solicitud'=>$this->input->post('solicitud'),
       'id_fuentes'=>$this->input->post('fuente'),
       'total'=>0.0
   );
   if ($this->Kardex_model->obtenerExistenciaFuente($data['id_detalleproducto'], $data['id_fuentes']) >= $data['cantidad']) {
     $modulo=$this->User_model->obtenerModulo('Bodega/detalle_solicitud_control');
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
           $this->Detalle_Solicitud_Producto_Model->actualizarDetalleSolicitudProducto($data['id_detalle_solicitud_producto'],$data);
           redirect('/Bodega/Detalle_Solicitud_Control/index/'.$data['id_solicitud'].'/'.$USER['id_rol'].'/update');
         }else{
           $rastrea['operacion']='INSERTA';
           $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_producto');
           $this->User_model->insertarRastreabilidad($rastrea);
           $this->Detalle_solicitud_producto_model->insertarDetalleSolicitudProducto($data);
           redirect('/Bodega/Detalle_Solicitud_Control/index/'.$data['id_solicitud'].'/'.$USER['id_rol'].'/new');
         }
   } else {
     redirect('/Bodega/Detalle_Solicitud_Control/index/'.$data['id_solicitud'].'/'.$USER['id_rol'].'/sin_existencia');
   }
 }

 public function EliminarDato(){
     $USER = $this->session->userdata('logged_in');
     $id = $this->uri->segment(4);
     $modulo=$this->User_model->obtenerModulo('Bodega/detalle_solicitud_control');
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
     $detalle=$this->Detalle_solicitud_producto_model->obtenerDetalleCompleto($id);
     if($detalle->precio>0){
       redirect('/Bodega/Detalle_Solicitud_Control/index/'.$this->uri->segment(5).'/'.$USER['id_rol'].'/proceso');
     }else{
       $this->Detalle_solicitud_producto_model->eliminarDetalleSolicitudProducto($id);
       $this->User_model->insertarRastreabilidad($rastrea);
       redirect('/Bodega/Detalle_Solicitud_Control/index/'.$this->uri->segment(5).'/'.$USER['id_rol'].'/delete');
     }
 }
}
?>
