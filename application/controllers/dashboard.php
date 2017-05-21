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
    $this->load->model(array('Bodega/Solicitud_Model', 'Bodega/Factura_Model', 'Compras/Solicitud_Compra_Model',
                    'Bodega/Producto', 'Compras/Compromiso_Presupuestario_Model', 'ActivoFijo/Datos_Comunes_Model'));
  }

  public function index(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "Home";
      $data['menu'] = $this->menu_dinamico->menus($USER, $this->uri->segment(1));

      switch ($USER['rol']) {
        case 'JEFE BODEGA':
          $data['dhb'] = $this->load->view('dashboard/dhb_jefe_bodega', '', true);
          break;
        case 'USUARIO SICBAF':
          $data['dhb'] = $this->load->view('dashboard/dhb_usuario', '', true);
          break;
        case 'JEFE UNIDAD':
          $data['dhb'] = $this->load->view('dashboard/dhb_jefe_unidad', '', true);
          break;
        case 'JEFE COMPRAS':
          $data['dhb'] = $this->load->view('dashboard/dhb_jefe_compra', '', true);
          break;
        case 'DIRECTOR ADMINISTRATIVO':
          $data['dhb'] = $this->load->view('dashboard/dhb_director_administrativo', '', true);
          break;
        case 'COLABORADOR BODEGA':
          $data['dhb'] = $this->load->view('dashboard/dhb_colaborador_bodega', '', true);
          break;
        case 'COLABORADOR COMPRAS':
          $data['dhb'] = $this->load->view('dashboard/dhb_colaborador_compra', '', true);
          break;
        case 'JEFE UACI':
          $data['dhb'] = $this->load->view('dashboard/dhb_jefe_uaci', '', true);
          break;
        case 'COLABORADOR UACI':
          $data['dhb'] = $this->load->view('dashboard/dhb_colaborador_uaci', '', true);
          break;
        default:
          $data['dhb'] = "";
          break;
      }

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

  public function obtenerSolicitudesCompra(){
    $cant_meses = $this->cant_meses;
    $USER = $this->session->userdata('logged_in');
    $datos = $this->Solicitud_Compra_Model->obtenerSolicitudesCompraUserDHB($USER['id_seccion'], date("Y"));

      if ($datos) {
        $mes = 0;
        foreach ($datos as $dato) {
          $ar_meses = explode("-", $dato->fecha_solicitud_compra);
          $mes = $ar_meses[1];
          $cant = $cant_meses[$this->meses[$mes - 1]] + 1;
          $cant_meses[$this->meses[$mes - 1]] = $cant;
      }
    }

    echo json_encode($cant_meses);
  }

  public function obtenerProductoMovimiento() {
    echo json_encode($this->Producto->obtenerProductoMasMovimiento());
  }

  public function obtenerGastosSeccion() {
    $USER = $this->session->userdata('logged_in');

    $com = $this->Compromiso_Presupuestario_Model->obtenerGastoComprasSeccion($USER['id_seccion'], date("Y")."-01-01", date("Y")."-12-31");
    $bod = $this->Solicitud_Model->obtenerGastosRetiros($USER['id_seccion'], date("Y")."-01-01", date("Y")."-12-31");

    echo json_encode($com->total + $bod->total);
  }
  /*
  * USUARIO SICBAF
  */

  public function obtenerSolicitudesCompraBodegaUsuario() {
    $cant_meses_compra = $this->cant_meses;
    $USER = $this->session->userdata('logged_in');
    $datos = $this->Solicitud_Compra_Model->obtenerSolicitudesCompraUserDHB($USER['id_seccion'], date("Y"));

      if ($datos) {
        $mes = 0;
        foreach ($datos as $dato) {
          $ar_meses = explode("-", $dato->fecha_solicitud_compra);
          $mes = $ar_meses[1];
          $cant = $cant_meses_compra[$this->meses[$mes - 1]] + 1;
          $cant_meses_compra[$this->meses[$mes - 1]] = $cant;
      }
    }

    $cant_meses_bodega = $this->cant_meses;

    $datos = $this->Solicitud_Model->obtenerSolicitudesUserFecha($USER['id'], date("Y"));
    if ($datos) {
      $mes = 0;
      foreach ($datos as $dato) {
        $ar_meses = explode("-", $dato->fecha_solicitud);
        $mes = $ar_meses[1];
        $cant = $cant_meses_bodega[$this->meses[$mes - 1]] + 1;
        $cant_meses_bodega[$this->meses[$mes - 1]] = $cant;
      }
    }

    $data = array($cant_meses_compra , $cant_meses_bodega);

    echo json_encode($data);
  }

  public function obtenerSolicitudesCompraBodegaJefe() {
    $cant_meses_compra = $this->cant_meses;
    $USER = $this->session->userdata('logged_in');
    $datos = $this->Solicitud_Compra_Model->obtenerSolicitudesCompraUserDHB($USER['id_seccion'], date("Y"));

      if ($datos) {
        $mes = 0;
        foreach ($datos as $dato) {
          $ar_meses = explode("-", $dato->fecha_solicitud_compra);
          $mes = $ar_meses[1];
          $cant = $cant_meses_compra[$this->meses[$mes - 1]] + 1;
          $cant_meses_compra[$this->meses[$mes - 1]] = $cant;
      }
    }

    $cant_meses_bodega = $this->cant_meses;

    $datos = $this->Solicitud_Model->obtenerSolicitudesSeccionFecha($USER['id_seccion'], date("Y"));
    if ($datos) {
      $mes = 0;
      foreach ($datos as $dato) {
        $ar_meses = explode("-", $dato->fecha_solicitud);
        $mes = $ar_meses[1];
        $cant = $cant_meses_bodega[$this->meses[$mes - 1]] + 1;
        $cant_meses_bodega[$this->meses[$mes - 1]] = $cant;
      }
    }

    $data = array($cant_meses_compra , $cant_meses_bodega);

    echo json_encode($data);
  }

  public function obtenerActivosFijosUser() {
    $USER = $this->session->userdata('logged_in');
    echo json_encode($this->Datos_Comunes_Model->totalBienesUsuario($USER['id_empleado'])->total);
  }

  public function obtenerSolicitudesDesApr() {
    $USER = $this->session->userdata('logged_in');
    $data = array(
      'bod_ap' => $this->Solicitud_Model->obtenerSolicitudesAprobadasSeccion($USER['id_seccion'], date("Y"))->cantidad,
      'bod_dap' => $this->Solicitud_Model->obtenerSolicitudesNoAprobadasSeccion($USER['id_seccion'], date("Y"))->cantidad,
      'cmp_ap' => $this->Solicitud_Compra_Model->obtenerSolicitudesAprobadasSeccionFecha($USER['id_seccion'], date("Y"))->cantidad,
      'cmp_dap' => $this->Solicitud_Compra_Model->obtenerSolicitudesNoAprobadasSeccionFecha($USER['id_seccion'], date("Y"))->cantidad
    );

    echo json_encode($data);
  }

  public function obtenerSolicitudesCompraEnProceso() {
    echo json_encode($this->Solicitud_Compra_Model->obtenerSolicitudesAprJefe(date("Y"))->cantidad);
  }

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
