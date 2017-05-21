<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends CI_Controller {

  public function __construct() {
    parent::__construct();
  }

  public function index(){

    $data['title'] = "Reportes";
    $data['js'] = "";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
		$data['body'] =
                  '<ul>
                    <li>
                      <a href="'.base_url('/index.php/Bodega/ConteoFisico/Reporte').'">Reporte de conteo fisico</a>
                    </li>
                    <li>
                      <a href="'.base_url('/index.php/Bodega/kardex/ReporteKardex').'">Generación de Kardex</a>
                    </li>
                    <li>
                      <a href="'.base_url('/index.php/Bodega/retiro/reporte').'">Reporte Salidas</a>
                    </li>
                    <li>
                      <a href="'.base_url('/index.php/Bodega/Kardex/ReporteGeneral/').'">Reporte Inventario General</a>
                    </li>
                    <li>
                      <a href="'.base_url('/index.php/Bodega/detalle_solicitud_producto/reporteGastoSeccion/').'">Reporte Gastos Sección</a>
                    </li>
                    <li>
                      <a href="'.base_url('/index.php/Bodega/factura/reporteIngresoSeccion/').'">Reporte Ingresos Sección</a>
                    </li>
                    <li>
                      <a href="'.base_url('/index.php/Bodega/Proveedores/Reporte/').'">Reporte por Proveedor, Factura y Especifico</a>
                    </li>
                    <li>
                      <a href="'.base_url('/index.php/Bodega/Productos/reporte/').'">Reporte de Productos con lento Movimiento</a>
                    </li>
                    <li>
                      <a href="'.base_url('/index.php/Bodega/kardex_todos/ReporteKardex').'">Reporte de Inventario General PEPS.</a>
                    </li>
                  </ul>
                  ';

    $this->load->view('base', $data);
	}
}
?>
