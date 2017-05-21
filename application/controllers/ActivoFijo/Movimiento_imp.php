<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//Para el formato de impresión del movimiento
class Movimiento_imp extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login');
    }
    $this->load->model(array('ActivoFijo/Movimiento_Model', 'mtps/Seccion_model', 'ActivoFijo/Detalle_movimiento_model'));
  }

  public function index(){
    $datos = $this->Movimiento_Model->obtenerTodoMovimiento($this->uri->segment(4));
    $ofi_recibe = $this->Seccion_model->obtenerAlmacenSeccionOficina($datos['id_oficina_recibe']);
    $ofi_entrega = $this->Seccion_model->obtenerAlmacenSeccionOficina($datos['id_oficina_entrega']);
    $datos['nombre_almacen_recibe'] = $ofi_recibe['nombre_almacen'];
    $datos['nombre_seccion_recibe'] = $ofi_recibe['nombre_seccion'];
    $datos['nombre_oficina_recibe'] = $ofi_recibe['nombre_oficina'];
    $datos['nombre_almacen_entrega'] = $ofi_entrega['nombre_almacen'];
    $datos['nombre_seccion_entrega'] = $ofi_entrega['nombre_seccion'];
    $datos['nombre_oficina_entrega'] = $ofi_entrega['nombre_oficina'];
    $datos['elaborado'] = $this->Seccion_model->nombreEmpleado($datos['id_guarda']);

    $data['datos'] = $datos;
    $data['detalles'] = $this->Detalle_movimiento_model->obtenerDetallePorMovimientos($this->uri->segment(4));
    $data['title'] = "Control y Registro de bienes.";
    $this->load->view('ActivoFijo/Movimiento_imp_view',$data);
  }
}
?>
