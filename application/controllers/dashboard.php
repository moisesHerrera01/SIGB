<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

  public $cant_meses = array(
    'enero' => 0,
    'febrero' => 0,
    'marzo' => 0,
    'abril' => 0,
    'mayo' => 0,
    'junio' => 0,
    'julio' => 0,
    'agosto' => 0,
    'septiembre' => 0,
    'octubre' => 0,
    'noviembre' => 0,
    'diciembre' => 0,
  );

  public $meses = array(
    'enero',
    'febrero',
    'marzo',
    'abril',
    'mayo',
    'junio',
    'julio',
    'agosto',
    'septiembre',
    'octubre',
    'noviembre',
    'diciembre',
  );

  public function __construct() {
    parent::__construct();
    $this->load->model(array('Bodega/Solicitud_Model', 'Bodega/Factura_Model', 'Bodega/Producto'));
  }

  public function index(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "Home";
      $data['menu'] = $this->menu_dinamico->menus($USER, $this->uri->segment(1));

      $data['dhb'] = $this->load->view('dashboard/dhb_colaborador_bodega', array('usuario' => $USER['nombre_completo'],
      'rol' => $USER['rol'], 'fecha_acceso' => $this->User_model->obteneFecharUltimaRastreabilidad($USER['id'])), true);

      $msg = array('alert' => $this->uri->segment(3), 'controller'=>'dashboard');
      $data['msg'] = $this->load->view('mensajes', $msg, TRUE);

      $this->load->view('dashboard_view', $data);
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

/*
* JEFE DE BODEGA
*/
  public function obtenerDescargosCargos(){
    $cant_meses_des = $this->cant_meses;
    $datos = $this->Solicitud_Model->obtenerLiquidadas(date("Y"));
    if ($datos) {
      $mes = 0;
      foreach ($datos as $dato) {
        $ar_meses = explode("-", $dato->fecha_solicitud);
        $mes = $ar_meses[1];
        $cant = $cant_meses_des[$this->meses[$mes - 1]] + 1;
        $cant_meses_des[$this->meses[$mes - 1]] = $cant;
      }
    }

    $cant_meses_car = $this->cant_meses;
    $datos = $this->Factura_Model->obtenerLiquidadas(date("Y"));
    if ($datos) {
      $mes = 0;
      foreach ($datos as $dato) {
        $ar_meses = explode("-", $dato->fecha_ingreso);
        $mes = $ar_meses[1];
        $cant = $cant_meses_car[$this->meses[$mes - 1]] + 1;
        $cant_meses_car[$this->meses[$mes - 1]] = $cant;
      }
    }

    $cant_meses = array(
      'descargos' => $cant_meses_des,
      'cargos' => $cant_meses_car
    );

    echo json_encode($cant_meses);
  }

  public function obtenerProductoMovimiento() {
    echo json_encode($this->Producto->obtenerProductoMasMovimiento());
  }

  public function obtenerGastosSeccion() {
    $USER = $this->session->userdata('logged_in');

    //$com = $this->Compromiso_Presupuestario_Model->obtenerGastoComprasSeccion($USER['id_seccion'], date("Y")."-01-01", date("Y")."-12-31");
    $bod = $this->Solicitud_Model->obtenerGastosRetiros($USER['id_seccion'], date("Y")."-01-01", date("Y")."-12-31");

    echo json_encode($com->total + $bod->total);
  }
  /*
  * USUARIO SICBAF
  */

  public function obtenerGastoTotalBodega() {
    $data = array(
      'mes' => $this->Solicitud_Model->obtenerGastoTotalBodegaMes(date("m"), date("Y"), date("t"))->total,
      'mes_ant' => 0
    );

    if (date("m") == 01) {
      $data['mes_ant'] = $this->Solicitud_Model->obtenerGastoTotalBodegaMes(12, date("Y")-1, date("t"))->total;
    } else {
      $data['mes_ant'] = $this->Solicitud_Model->obtenerGastoTotalBodegaMes(date("m")-1, date("Y"), date("t"))->total;
    }

    echo json_encode($data);
  }
}

?>
