<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Acta extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login');
    }
    $this->load->helper(array('fecha'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Factura_Model','Bodega/Detallefactura_Model','Bodega/Producto',
    'Bodega/UnidadMedida','Bodega/DetalleProducto_model'));
  }

  public function index(){

    $prueba = $this->Factura_Model->obtenerDatosFactura($this->uri->segment(4));
    foreach ($prueba as $dat) {
      $nombre_fuente=$dat->nombre_fuente;
      $nombre_proveedor=$dat->nombre_proveedor;
      $nombre_entrega=$dat->nombre_entrega;
      $numero_factura=$dat->numero_factura;
      $fecha_factura=fecha($dat->fecha_factura);
      $fecha_ingreso = fecha($dat->fecha_ingreso);
      $hora = $dat->hora;
      $correlativo = $dat->correlativo_fuente_fondo;
      $comentario_productos=$dat->comentario_productos;
    }

    $total_fuentes = $this->Factura_Model->obtenerTotalFuentes($this->uri->segment(5), $fecha_factura);
    $data['title'] = "Acta Recepción";
    $data['js'] = "assets/js/validate/detfact.js";
    $data['table']  = "<br><div class=''>" . $this->mostrarTabla() . "</div>";
    $data['var'] = array('nombre_fuente' => $nombre_fuente,'nombre_proveedor'=>$nombre_proveedor,
    'nombre_entrega'=>$nombre_entrega,'numero_factura'=>$numero_factura,'fecha_factura'=>$fecha_factura,
    'fecha_ingreso'=>$fecha_ingreso,'hora'=>$hora,'correlativo' => $correlativo, 'id_factura'=>$this->uri->segment(4),
    'comentario_productos'=>$comentario_productos);
    $this->load->view('Bodega/acta_view',$data,FALSE);

  }

  public function mostrarTabla(){
    $template = array(
        'table_open' => '<table width="80%" cellpadding="5">'
    );
    $this->table->set_template($template);
    $this->table->set_heading('Producto','Unidad de Medida','Cantidad', 'Precio','Total');

    /*Obtiene el numero de registros a mostrar por pagina */
    $registros = $this->Detallefactura_Model->obtenerDetalleFacturas($this->uri->segment(4));

    if (!($registros == FALSE)) {
      foreach($registros as $det) {
        $id_producto=$this->DetalleProducto_model->obtenerIdProducto($det->id_detalleproducto);
        //$id_especifico=$this->DetalleProducto_model->obtenerIdEspecifico($det->id_detalleproducto);
        $nombre_producto = $this->Producto->obtenerProducto($id_producto);
        //$nombre_especifico = $this->Especifico->obtenerEspecifico($id_especifico);
        $id_unidad=$this->Producto->obtenerIdUnidad($id_producto);
        $unidad=$this->UnidadMedida->obtenerUnidad($id_unidad);
        $this->table->add_row($nombre_producto,$unidad,$det->cantidad,$det->precio,$det->total);
      }
    } else {
      $msg = array('data' => "No se encontraron resultados", 'colspan' => "8");
      $this->table->add_row($msg);
    }

    return $this->table->generate();
  }

}
?>
