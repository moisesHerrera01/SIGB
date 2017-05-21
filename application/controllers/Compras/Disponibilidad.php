<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Disponibilidad extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login');
    }
    $this->load->model(array('Compras/Detalle_disponibilidad_montos_model','Compras/Solicitud_Disponibilidad_Model'));
    $this->load->helper('fecha');
  }

  public function index(){
    $disponibilidad=$this->Solicitud_Disponibilidad_Model->obtenerDisponibilidad($this->uri->segment(4));
    $id_solicitud_disponibilidad;
    foreach ($disponibilidad as $disp) {
      $id_solicitud_disponibilidad=$disp->id_solicitud_disponibilidad;
    }

    $uacis = $this->User_model->empleadoTituloCargo(240);
    $uaci = $uacis[0];

    $ufis = $this->User_model->empleadoTituloCargo(268);
    $ufi = $ufis[0];

    $data['datos']=$disponibilidad;
    $data['datos2']=$this->Detalle_disponibilidad_montos_model->obtenerDetalleDisponibilidad($id_solicitud_disponibilidad);
    $data['uaci'] = $this->AbreviaturaTitulo($uaci->titulo_academico, $uaci->genero) . ' ' . $uaci->nombre_empleado;
    $data['ufi'] = $this->AbreviaturaTitulo($ufi->titulo_academico, $ufi->genero) . ' ' . $ufi->nombre_empleado;
    $data['title'] = "Certificado de asignación presupuestaria.";
    $this->load->view('Compras/Disponibilidad_view',$data);
  }

  public function AbreviaturaTitulo($titulo='', $genero) {
    $grado = explode(" ", $titulo);
    $abr_grd = '';
    switch ($grado[0]) {
      case 'LICENCIATURA':
        $abr_grd = 'Lic';
        if ('FEMENINO' == $genero) {
          $abr_grd .= 'da';
        }
        $abr_grd .= '.';
        return $abr_grd;
        break;

      default:
        return '';
        break;
    }
  }
}
?>
