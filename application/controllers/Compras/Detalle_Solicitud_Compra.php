<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detalle_solicitud_compra extends CI_Controller {

 public function __construct() {
   parent::__construct();
   if($this->session->userdata('logged_in') == FALSE){
     redirect('login/index/error_no_autenticado');
   }
   $this->load->helper(array('form', 'paginacion'));
   $this->load->library('table');
   $this->load->model(array('Compras/Detalle_solicitud_compra_model','Bodega/Detalle_solicitud_producto_model',
   'Compras/Solicitud_Compra_Model', 'User_model'));
 }

 public function index(){

   if ($this->uri->segment(4) == '' || $this->Solicitud_Compra_Model->obtenerSolicitudCompra($this->uri->segment(4)) == '') {
     $data['body'] = "ERRROR";
     $this->load->view('base', $data);
   } else {
     $data['title'] = "Detalle Sol.";
     $data['js'] = "assets/js/validate/dsc.js";

     $solicitud_compra=$this->Solicitud_Compra_Model->obtenerSolicitudCompleta($this->uri->segment(4));
     $estado=$solicitud_compra->estado_solicitud_compra;
     $nivel=$solicitud_compra->nivel_solicitud;
     $numero=$this->Solicitud_Compra_Model->obtenerIdSolicitudCompra();
     $modulo=$this->User_model->obtenerModuloNombre($this->uri->segment(5));
     $USER = $this->session->userdata('logged_in');
     $id_modulo=$this->uri->segment(5);
     $nivel = $this->Solicitud_Compra_Model->obtenerNivelSolicitud($this->uri->segment(4));
     $rol = $USER['id_rol'];
     $roles = $this->User_model->obtenerRolesSistema();
     $autorizante = $this->uri->segment(6);
     $msg= array('id_solicitud_compra' => $this->uri->segment(4),'autorizante' => $autorizante,'id_modulo' => $id_modulo, 'rol'=>$rol,'nivel'=>$nivel);
     $men = array('alert' => $this->uri->segment(7),'controller'=>'Detalle_Solicitud_Compra','estado'=>$estado,
     'numero'=>$numero-1,'nivel'=>$nivel,'modulo'=>$modulo,'id_modulo'=>$id_modulo, 'roles'=>$roles);

     $data['body'] = $this->load->view('mensajes', $men, TRUE) . $this->load->view('Compras/detalle_solicitud_compra_view',$msg,TRUE) .
                     "<br><div class='content_table '>" .
                     "<div class='limit-content-title'><span class='icono icon-table icon-title'> Detalle Solicitud </span></div>".
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
   $this->table->set_heading('#','Producto','Unidad de Medida','Cantidad','Especificaciones','Eliminar','Editar');

   $registros;

   if ($this->input->is_ajax_request()) {
   } else {
         $registros = $this->Detalle_solicitud_compra_model->obtenerDetalleSolicitudCompra($this->uri->segment(4));
   }

   if (!($registros == FALSE)) {
     $i = 1;
     foreach($registros as $det) {
       $datos=$this->Detalle_solicitud_producto_model->obtenerDatos($det->id_detalleproducto);
       $nivel = $this->Solicitud_Compra_Model->obtenerNivelSolicitud($det->id_solicitud_compra);
       foreach ($datos as $detsol) {
         $estado=$this->Solicitud_Compra_Model->obtenerSolicitudCompleta($det->id_solicitud_compra);
         $estado=$estado->estado_solicitud_compra;
         $onClick = "llenarFormulario('solicitud', ['id_detalle_solicitud_compra', 'producto', 'autocomplete1', 'cantidad'],
                     [$det->id_detalle_solicitud_compra, '$det->id_detalleproducto', '$detsol->producto','$det->cantidad'],
                     false,false,false,'especificaciones','$det->especificaciones')";
                     $USER = $this->session->userdata('logged_in');
                     $roles = $this->User_model->obtenerRolesSistema();
                     if($this->uri->segment(5) == $this->User_model->obtenerModulo("Compras/Solicitud_Compra")){
                         if ($nivel == 0 || $nivel == 1){
                           $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Compras/Detalle_Solicitud_Compra/EliminarDato/'
                           .$det->id_detalle_solicitud_compra.'/'.$det->id_solicitud_compra.'/'.$this->uri->segment(5)).'></a>';
                           $editar='<a class="icono icon-pencil" onClick="'.$onClick.'"></a>';
                         }
                         if ($nivel == 2 || $nivel == 3 || $nivel == 4 || $nivel == 5 || $nivel == 6 || $nivel == 7 || $nivel == 8 ||$nivel== 9){

                           $editar = '<a class="icono icon-denegar"></a>';
                           $eliminar = '<a class="icono icon-denegar"></a>';
                         }

                     } elseif($this->uri->segment(5) == $this->User_model->obtenerModulo("Compras/Aprobar_Solicitud")){
                       if ($USER['rol'] == 'ADMINISTRADOR SICBAF' || $USER['rol'] == 'JEFE BODEGA' || $USER['rol'] == 'COLABORADOR BODEGA' ||
                          $USER['rol'] == 'JEFE UNIDAD' || $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'JEFE UACI' || $USER['rol'] == 'JEFE AF' ||
                          $USER['rol'] == 'COLABORADOR COMPRAS' || $USER['rol'] == 'COLABORADOR UACI' || $USER['rol'] == 'COLABORADOR AF'){
                         if ($nivel == 1 || $nivel == 2){
                           $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Compras/Detalle_Solicitud_Compra/EliminarDato/'
                           .$det->id_detalle_solicitud_compra.'/'.$det->id_solicitud_compra.'/'.$this->uri->segment(5)).'></a>';
                           $editar='<a class="icono icon-pencil" onClick="'.$onClick.'"></a>';
                         }
                         if ($nivel == 3 || $nivel == 4 || $nivel == 5 || $nivel == 6 || $nivel == 7 || $nivel == 8 ||$nivel== 9){
                           $editar = '<a class="icono icon-denegar"></a>';
                           $eliminar = '<a class="icono icon-denegar"></a>';
                         }
                       }

                     } elseif($this->uri->segment(5) == $this->User_model->obtenerModulo('Compras/Gestionar_Solicitud')){
                       if ($USER['rol'] == 'ADMINISTRADOR SICBAF' || $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'JEFE UACI' ||
                          $USER['rol'] == 'COLABORADOR COMPRAS' || $USER['rol'] == 'COLABORADOR UACI'){
                         if ($nivel == 3 || $nivel == 4){
                           $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Compras/Detalle_Solicitud_Compra/EliminarDato/'
                           .$det->id_detalle_solicitud_compra.'/'.$det->id_solicitud_compra.'/'.$this->uri->segment(5)).'></a>';
                           $editar='<a class="icono icon-pencil" onClick="'.$onClick.'"></a>';
                         }
                         if ($nivel == 5 || $nivel == 6 || $nivel == 7 || $nivel == 8 ||$nivel== 9){
                           $editar = '<a class="icono icon-denegar"></a>';
                           $eliminar = '<a class="icono icon-denegar"></a>';
                         }
                       }

                     }
                     if ($this->uri->segment(6) == 111){
                       if ($nivel == 2 || $nivel == 3){
                         $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Compras/Detalle_Solicitud_Compra/EliminarDato/'
                         .$det->id_detalle_solicitud_compra.'/'.$det->id_solicitud_compra.'/'.$this->uri->segment(5)).'></a>';
                         $editar='<a class="icono icon-pencil" onClick="'.$onClick.'"></a>';
                       }
                       if ($nivel == 4 || $nivel == 5 ||$nivel == 6 ||$nivel == 7 ||$nivel == 8 || $nivel== 9){
                         $editar = '<a class="icono icon-denegar"></a>';
                         $eliminar = '<a class="icono icon-denegar"></a>';
                       }
                     } /*else {
                       $editar = '<a class="icono icon-denegar"></a>';
                       $eliminar = '<a class="icono icon-denegar"></a>';
                     }*/

           $this->table->add_row($i,$detsol->producto,$detsol->unidad,$det->cantidad,$det->especificaciones,$eliminar,$editar);
         $i++;
       }
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
   $USER = $this->session->userdata('logged_in');
   $mod = $this->User_model->obtenerModulo('Compras/Detalle_solicitud_compra');
   $modulo=$this->input->post('id_modulo');
   $precio=0.0;
   $data = array(
       'id_detalle_solicitud_compra' =>$this->input->post('id_detalle_solicitud_compra'),
       'id_detalleproducto'=>$this->input->post('producto'),
       'cantidad' => $this->input->post('cantidad'),
       'precio' => $precio,
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
    if (!($data['id_detalle_solicitud_compra'] == '')){
         $this->Detalle_solicitud_compra_model->actualizarDetalleSolicitudCompra($data['id_detalle_solicitud_compra'],$data);
         if ($this->Solicitud_Compra_Model->esAutorizante($USER['id_seccion'])) {
           $rastrea['operacion']='ACTUALIZA';
           $rastrea['id_registro']=$this->input->post('id_detalle_solicitud_compra');
           $this->User_model->insertarRastreabilidad($rastrea);
           redirect('/Compras/Detalle_Solicitud_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/'.'111'.'/update');
         } else{
           $rastrea['operacion']='ACTUALIZA';
           $rastrea['id_registro']=$this->input->post('id_detalle_solicitud_compra');
           $this->User_model->insertarRastreabilidad($rastrea);
           redirect('/Compras/Detalle_Solicitud_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/'.$USER['id_rol'].'/update');
         }
     }else{
       $detalles=$this->Detalle_solicitud_compra_model->obtenerDetallesSolicitud($data['id_solicitud_compra']);
       $detalle=$this->Detalle_solicitud_compra_model->obtenerEspecifico($this->input->post('producto'));
       if(!($detalles=='')){
           if($detalles->id_especifico==$detalle->id_especifico){
             if($detalles->id_detalleproducto==$detalle->id_detalleproducto){
               if ($this->Solicitud_Compra_Model->esAutorizante($USER['id_seccion'])) {
                 redirect('/Compras/Detalle_Solicitud_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/'.'111'.'/mismo');
                } else {
                  redirect('/Compras/Detalle_Solicitud_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/'.$USER['id_rol'].'/mismo');
                }
             }else{
               $this->Detalle_solicitud_compra_model->insertarDetalleSolicitudCompra($data);
               if ($this->Solicitud_Compra_Model->esAutorizante($USER['id_seccion'])) {
                 $rastrea['operacion']='INSERTA';
                 $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_compra')-1;
                 $this->User_model->insertarRastreabilidad($rastrea);
                 redirect('/Compras/Detalle_Solicitud_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/'.'111'.'/new');
                } else {
                  $rastrea['operacion']='INSERTA';
                  $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_compra')-1;
                  $this->User_model->insertarRastreabilidad($rastrea);
                  redirect('/Compras/Detalle_Solicitud_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/'.$USER['id_rol'].'/new');
                }
             }
           }else{
             if ($this->Solicitud_Compra_Model->esAutorizante($USER['id_seccion'])) {
               redirect('/Compras/Detalle_Solicitud_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/'.'111'.'/noespecifico');
             }else{
               redirect('/Compras/Detalle_Solicitud_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/'.$USER['id_rol'].'/noespecifico');
             }
           }
       }else{
          $this->Detalle_solicitud_compra_model->insertarDetalleSolicitudCompra($data);
          if ($this->Solicitud_Compra_Model->esAutorizante($USER['id_seccion'])) {
            $rastrea['operacion']='INSERTA';
            $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_compra')-1;
            $this->User_model->insertarRastreabilidad($rastrea);
            redirect('/Compras/Detalle_Solicitud_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/'.'111'.'/new');
          }else{
            $rastrea['operacion']='INSERTA';
            $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_compra')-1;
            $this->User_model->insertarRastreabilidad($rastrea);
            redirect('/Compras/Detalle_Solicitud_Compra/index/'.$data['id_solicitud_compra'].'/'.$modulo.'/'.$USER['id_rol'].'/new');
          }
       }
     }

 }

 public function EliminarDato(){
   $USER = $this->session->userdata('logged_in');
   $mod = $this->User_model->obtenerModulo('Compras/Detalle_Solicitud_Compra');
   $modulo=$this->input->post('id_modulo');
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
     $detalle=$this->Detalle_solicitud_compra_model->obtenerDetalleCompraCompleto($id);
     if($detalle->precio>0){
       if ($this->Solicitud_Compra_Model->esAutorizante($USER['id_seccion'])) {
         redirect('/Compras/Detalle_Solicitud_Compra/index/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.'111'.'/proceso');
       }else{
         redirect('/Compras/Detalle_Solicitud_Compra/index/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$USER['id_rol'].'/proceso');
       }
     }else{
       $this->Detalle_solicitud_compra_model->eliminarDetalleSolicitudCompra($id);
       if ($this->Solicitud_Compra_Model->esAutorizante($USER['id_seccion'])) {
         $this->User_model->insertarRastreabilidad($rastrea);
         redirect('/Compras/Detalle_Solicitud_Compra/index/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.'111'.'/delete');
       }else{
         $this->User_model->insertarRastreabilidad($rastrea);
         redirect('/Compras/Detalle_Solicitud_Compra/index/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$USER['id_rol'].'/delete');
       }
     }
 }

/*Listado de productos de autocompletado con filtro de especifico y nombre del producto*/
 public function AutocompleteEspecificoProductoCompras(){
   $USER = $this->session->userdata('logged_in');
   if($USER){
     $registros = '';
     if ($this->input->is_ajax_request()) {
       if (!($this->input->post('autocomplete') == "")) {
           $registros = $this->Detalle_solicitud_compra_model->buscarEspecificosProductosCompras($this->input->post('autocomplete'));
       } else {
           $registros = $this->Detalle_solicitud_compra_model->obtenerEspecificosProductosCompras();
       }
     } else {
           $registros = $this->Detalle_solicitud_compra_model->obtenerEspecificosProductosCompras();
     }
     if ($registros == '') {
       echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
     }else {
       $i = 1;
       foreach ($registros as $producto) {
           echo '<div id="'.$i.'" class="suggest-element" ida="producto'.$producto->id_detalleproducto.'"><a id="producto'.
           $producto->id_detalleproducto.'" data="'.$producto->id_detalleproducto.'"  data1="'.$producto->id_especifico.' - '.$producto->nombre.' - '.$producto->nombre_unidad.'">'
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
