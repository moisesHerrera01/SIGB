<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Estado_solicitud extends CI_Controller {

 public function __construct() {
   parent::__construct();
   if($this->session->userdata('logged_in') == FALSE){
     redirect('login/index/error_no_autenticado');
   }
   $this->load->helper(array('form', 'paginacion'));
   $this->load->library('table');
   $this->load->model(array('Compras/Solicitud_Compra_Model'));
 }
 public function index(){
     $data['title'] = "Estado Sol.";
     $msg['id_solicitud_compra'] = $this->uri->segment(4);
     $solicitud_compra=$this->Solicitud_Compra_Model->obtenerSolicitudCompleta($this->uri->segment(4));
     $estado=$solicitud_compra->estado_solicitud_compra;
     $numero=$this->Solicitud_Compra_Model->obtenerIdSolicitudCompra();
     $men = array('alert' => $this->uri->segment(5),'controller'=>'estado_solicitud','estado'=>$estado,
     'numero'=>$numero-1);

     $data['body'] = $this->load->view('mensajes', $men, TRUE) . $this->load->view('Compras/estado_solicitud_view',$msg,TRUE) .
                     "<br><div class='content_table '>" .
                     "<div class='limit-content-title'><span class='icono icon-table icon-title'> Estado de la Solicitud </span></div>".
                     "<div class='limit-content'>" . $this->mostrarTabla() . "</div>";
     $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
     $this->load->view('base', $data);
 }

 public function mostrarTabla($accion = TRUE){
   $template = array(
       'table_open' => '<table class="table table-striped table-bordered">'
   );
   $this->table->set_template($template);
   $this->table->set_heading('Nivel','Paso','Estado');

   $registros;

   if ($this->input->is_ajax_request()) {
   } else {
         $registros = $this->Solicitud_Compra_Model->obtenerSolicitudCompleta($this->uri->segment(4));
   }

   if (!($registros == FALSE)) {
              switch ($registros->nivel_solicitud) {
           case '0':
              $this->table->add_row('0','INGRESADA','<a class="icono icon-liquidar"></a>');
              $this->table->add_row('1','ENVIADA','AÚN SIN ENVIAR');
              $this->table->add_row('2','APROBACIÓN JEFATURA','');
              $this->table->add_row('3','APROBACIÓN DEPENDENCIA','');
              $this->table->add_row('4','APROBACIÓN COMPRAS','');
              $this->table->add_row('5','DISPONIBILIDAD FINANCIERA','');
              $this->table->add_row('6','ORDEN DE COMPRA','');
              $this->table->add_row('7','COMPROMISO PRESUPUESTARIO','');
              $this->table->add_row('8','RETIRO DE BODEGA','');
             break;
             case '1':
                 if($registros->estado_solicitud_compra=='ENVIADA'){
                   $estado='<a class="icono icon-liquidar"></a>';
                   $estado_sig='EN PROCESO';
                 }
                $this->table->add_row('0','INGRESADA','<a class="icono icon-liquidar"></a>');
                $this->table->add_row('1','ENVIADA',$estado);
                $this->table->add_row('2','APROBACIÓN JEFATURA',$estado_sig);
                $this->table->add_row('3','APROBACIÓN DEPENDENCIA','');
                $this->table->add_row('4','APROBACIÓN COMPRAS','');
                $this->table->add_row('5','DISPONIBILIDAD FINANCIERA','');
                $this->table->add_row('6','ORDEN DE COMPRA','');
                $this->table->add_row('7','COMPROMISO PRESUPUESTARIO','');
                $this->table->add_row('8','RETIRO DE BODEGA','');
               break;
               case '2':
                   if($registros->estado_solicitud_compra=='APROBADA JEFATURA' || $registros->nivel_solicitud==2){
                     $estado='<a class="icono icon-liquidar"></a>';
                     $estado_sig='EN PROCESO';
                   }
                  $this->table->add_row('0','INGRESADA','<a class="icono icon-liquidar"></a>');
                  $this->table->add_row('1','ENVIADA','<a class="icono icon-liquidar"></a>');
                  $this->table->add_row('2','APROBACIÓN JEFATURA',$estado);
                  $this->table->add_row('3','APROBACIÓN DEPENDENCIA',$estado_sig);
                  $this->table->add_row('4','APROBACIÓN COMPRAS','');
                  $this->table->add_row('5','DISPONIBILIDAD FINANCIERA','');
                  $this->table->add_row('6','ORDEN DE COMPRA','');
                  $this->table->add_row('7','COMPROMISO PRESUPUESTARIO','');
                  $this->table->add_row('8','RETIRO DE BODEGA','');
                 break;
                 case '3':
                     if($registros->estado_solicitud_compra=='APROBADA AUTORIZANTE' || $registros->nivel_solicitud==3){
                       $estado='<a class="icono icon-liquidar"></a>';
                       $estado_sig='EN PROCESO';
                     }
                    $this->table->add_row('0','INGRESADA','<a class="icono icon-liquidar"></a>');
                    $this->table->add_row('1','ENVIADA','<a class="icono icon-liquidar"></a>');
                    $this->table->add_row('2','APROBACIÓN JEFATURA','<a class="icono icon-liquidar"></a>');
                    $this->table->add_row('3','APROBACIÓN DEPENDENCIA',$estado);
                    $this->table->add_row('4','APROBACIÓN COMPRAS',$estado_sig);
                    $this->table->add_row('5','DISPONIBILIDAD FINANCIERA','');
                    $this->table->add_row('6','ORDEN DE COMPRA','');
                    $this->table->add_row('7','COMPROMISO PRESUPUESTARIO','');
                    $this->table->add_row('8','RETIRO DE BODEGA','');
                   break;
                   case '4':
                       if($registros->estado_solicitud_compra=='APROBADA COMPRAS' || $registros->nivel_solicitud==4){
                         $estado='<a class="icono icon-liquidar"></a>';
                          $estado_sig='EN PROCESO';
                        }
                      $this->table->add_row('0','INGRESADA','<a class="icono icon-liquidar"></a>');
                      $this->table->add_row('1','ENVIADA','<a class="icono icon-liquidar"></a>');
                      $this->table->add_row('2','APROBACIÓN JEFATURA','<a class="icono icon-liquidar"></a>');
                      $this->table->add_row('3','APROBACIÓN DEPENDENCIA','<a class="icono icon-liquidar"></a>');
                      $this->table->add_row('4','APROBACIÓN COMPRAS',$estado);
                      $this->table->add_row('5','DISPONIBILIDAD FINANCIERA',$estado_sig);
                      $this->table->add_row('6','ORDEN DE COMPRA','');
                      $this->table->add_row('7','COMPROMISO PRESUPUESTARIO','');
                      $this->table->add_row('8','RETIRO DE BODEGA','');
                     break;
                     case '5':
                         if($registros->estado_solicitud_compra=='APROBADA DISPONIBILIDAD'){
                           $estado='<a class="icono icon-liquidar"></a>';
                           $estado_sig='EN PROCESO';
                         }
                        $this->table->add_row('0','INGRESADA','<a class="icono icon-liquidar"></a>');
                        $this->table->add_row('1','ENVIADA','<a class="icono icon-liquidar"></a>');
                        $this->table->add_row('2','APROBACIÓN JEFATURA','<a class="icono icon-liquidar"></a>');
                        $this->table->add_row('3','APROBACIÓN DEPENDENCIA','<a class="icono icon-liquidar"></a>');
                        $this->table->add_row('4','APROBACIÓN COMPRAS','<a class="icono icon-liquidar"></a>');
                        $this->table->add_row('5','DISPONIBILIDAD FINANCIERA',$estado);
                        $this->table->add_row('6','ORDEN DE COMPRA',$estado_sig);
                        $this->table->add_row('7','COMPROMISO PRESUPUESTARIO','');
                        $this->table->add_row('8','RETIRO DE BODEGA','');
                       break;
                       case '6':
                           if($registros->estado_solicitud_compra=='APROBADA ORDEN DE COMPRA'){
                             $estado='<a class="icono icon-liquidar"></a>';
                             $estado_sig='EN PROCESO';
                           }
                           $this->table->add_row('0','INGRESADA','<a class="icono icon-liquidar"></a>');
                           $this->table->add_row('1','ENVIADA','<a class="icono icon-liquidar"></a>');
                           $this->table->add_row('2','APROBACIÓN JEFATURA','<a class="icono icon-liquidar"></a>');
                           $this->table->add_row('3','APROBACIÓN DEPENDENCIA','<a class="icono icon-liquidar"></a>');
                           $this->table->add_row('4','APROBACIÓN COMPRAS','<a class="icono icon-liquidar"></a>');
                           $this->table->add_row('5','DISPONIBILIDAD FINANCIERA','<a class="icono icon-liquidar"></a>');
                           $this->table->add_row('6','ORDEN DE COMPRA',$estado);
                           $this->table->add_row('7','COMPROMISO PRESUPUESTARIO',$estado_sig);
                           $this->table->add_row('8','RETIRO DE BODEGA','');
                         break;
                         case '7':
                             if($registros->estado_solicitud_compra=='APROBADA COMPROMISO'){
                               $estado='<a class="icono icon-liquidar"></a>';
                               $estado_sig='PROCESO DE FACTURA';
                             }
                             $this->table->add_row('0','INGRESADA','<a class="icono icon-liquidar"></a>');
                             $this->table->add_row('1','ENVIADA','<a class="icono icon-liquidar"></a>');
                             $this->table->add_row('2','APROBACIÓN JEFATURA','<a class="icono icon-liquidar"></a>');
                             $this->table->add_row('3','APROBACIÓN DEPENDENCIA','<a class="icono icon-liquidar"></a>');
                             $this->table->add_row('4','APROBACIÓN COMPRAS','<a class="icono icon-liquidar"></a>');
                             $this->table->add_row('5','DISPONIBILIDAD FINANCIERA','<a class="icono icon-liquidar"></a>');
                             $this->table->add_row('6','ORDEN DE COMPRA','<a class="icono icon-liquidar"></a>');
                             $this->table->add_row('7','COMPROMISO PRESUPUESTARIO',$estado);
                             $this->table->add_row('8','RETIRO DE BODEGA',$estado_sig);
                           break;
                           case '8':
                               if($registros->estado_solicitud_compra=='LIQUIDADA'){
                                 $estado='<a class="icono icon-liquidar"></a>';
                               }
                              $this->table->add_row('0','INGRESADA','<a class="icono icon-liquidar"></a>');
                              $this->table->add_row('1','ENVIADA','<a class="icono icon-liquidar"></a>');
                              $this->table->add_row('2','APROBACIÓN JEFATURA','<a class="icono icon-liquidar"></a>');
                              $this->table->add_row('3','APROBACIÓN DEPENDENCIA','<a class="icono icon-liquidar"></a>');
                              $this->table->add_row('4','APROBACIÓN COMPRAS','<a class="icono icon-liquidar"></a>');
                              $this->table->add_row('5','DISPONIBILIDAD FINANCIERA','<a class="icono icon-liquidar"></a>');
                              $this->table->add_row('6','ORDEN DE COMPRA','<a class="icono icon-liquidar"></a>');
                              $this->table->add_row('7','COMPROMISO PRESUPUESTARIO','<a class="icono icon-liquidar"></a>');
                              $this->table->add_row('8','RETIRO DE BODEGA',$estado);
                             break;
                   case '9':
                      $estado='<a class="icono icon-denegar"></a>';
                      $this->table->add_row('0','INGRESADA','<a class="icono icon-liquidar"></a>');
                      $this->table->add_row('1','ENVIADA','<a class="icono icon-liquidar"></a>');
                      $this->table->add_row('2','APROBACIÓN JEFATURA',$estado);
                      $this->table->add_row('3','APROBACIÓN DEPENDENCIA',$estado);
                      $this->table->add_row('4','DISPONIBILIDAD FINANCIERA',$estado);
                      $this->table->add_row('5','COTIZACIONES',$estado);
                      $this->table->add_row('6','ORDEN DE COMPRA',$estado);
                      $this->table->add_row('7','COMPROMISO PRESUPUESTARIO',$estado);
                      $this->table->add_row('8','RETIRO DE BODEGA',$estado);
                     break;
           default:
             break;
         }
   } else {
     $msg = array('data' => "No se encontraron resultados", 'colspan' => "3");
     $this->table->add_row($msg);
   }

   if ($this->input->is_ajax_request()) {
     echo "<div class='table-responsive'>" . $this->table->generate() . "</div>";
   } else {
     return "<div class='table-responsive'>" . $this->table->generate() . "</div>";
   }
 }
}
?>
