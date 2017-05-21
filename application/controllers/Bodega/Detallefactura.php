<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detallefactura extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Detallefactura_Model', 'Bodega/Factura_Model','Bodega/Producto',
        'Bodega/Especifico'));
  }

  public function index(){

    if ($this->uri->segment(4) == '' || $this->Factura_Model->obtenerFactura($this->uri->segment(4)) == '') {
      $data['body'] = "ERROR";
      $this->load->view('base', $data);
    } else {
    $estado = $this->Factura_Model->retornarEstado($this->uri->segment(4));
    $data['title'] = "Detalle Factura";
    $data['js'] = "assets/js/validate/detfact.js";

    $msg = array('id_factura' => $this->uri->segment(4),'estado'=> $estado);

    $men = array('alert' => $this->uri->segment(5), 'controller' => 'Detallefactura');

		$data['body'] = $this->load->view('mensajes', $men, TRUE) . $this->load->view('Bodega/detalleFactura_view',$msg,TRUE) .
                    "<br><div class='content_table '>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Detalle Factura</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	  }
  }

  public function MostrarDetalleFactura() {

    $this->load->model(array('mtps/Seccion_model', 'Bodega/Fuentefondos_model', 'Bodega/Proveedor'));

    if ($this->uri->segment(4) == '' || $this->Factura_Model->obtenerFactura($this->uri->segment(4)) == '') {
      $data['body'] = "ERRROR";
      $this->load->view('base', $data);
    } else {

      $data['title'] = "Detalle Factura";

      $factura = '';
      foreach ($this->Factura_Model->obtenerTodaFactura($this->uri->segment(4)) as $fac) {
        $factura = $fac;
      }

      $table = $this->mostrarTabla(FALSE);

      $data['body'] = "
        <div>
          <ul class='lista_detalle'>
            <li>Numero de Factura: ".$factura->numero_factura."</li>
            <li>Fecha de Factura: ".$factura->fecha_factura."</li>
            <li>Fecha ingreso de Factura: ".$factura->fecha_ingreso."</li>
            <li>Proveedor: ".$this->Proveedor->obtenerProveedor($factura->id_proveedores)."</li>
            <li>Compromiso: ".$factura->numero_compromiso."</li>
            <li>Orden de Compra: ".$factura->orden_compra."</li>
            <li>Numero del Acta: ".$this->Fuentefondos_model->obtenerFuente($factura->id_fuentes) ."-".
            $this->Factura_Model->obtenerTotalFuentes($factura->id_fuentes, $factura->fecha_factura)."</li>
            <li>Requisitor: ".$this->Seccion_model->obtenerPorIdSeccion($factura->id_seccion)."</li>
          </ul>
          <br>
          ".$table."
        </div>
      ";
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
    ($accion ? $this->table->set_heading('Producto','Unidad de Medida','Cantidad', 'Precio','Total','Estado','Cargar','Eliminar','Editar')
     : $this->table->set_heading('Producto','Unidad de Medida','Cantidad', 'Precio','Total','Estado') );
    /*
    * Filtro a la BD
    */
    /*Obtiene el numero de registros a mostrar por pagina */
    $registros;
    if ($this->input->is_ajax_request()) {
    } else {
          $registros = $this->Detallefactura_Model->obtenerDetalleFacturas($this->uri->segment(4));
    }
    /*
    * vuelve a verificar para mostrar los datos    */

    if (!($registros == FALSE)) {
      $i = 1;
      foreach($registros as $det) {
        if ($accion) {
          if ($this->Factura_Model->retornarEstado($this->uri->segment(4))=='INGRESADA'){
            if($det->estado_factura_producto=='INGRESADO'){
              $botones= '<a class="icono icon-upload2" href="'.base_url('index.php/Bodega/Detallefactura/Cargar/'.$det->id_detalle_factura.'/'.$det->id_factura.'/'.$det->total.'/').'"></a>';
            }
            else{
              $botones= '<a class="icono icon-liquidar" href="'.base_url('index.php/Bodega/Detallefactura/Cargar/'.$det->id_detalle_factura.'/'.$det->id_factura.'/'.$det->total.'/').'"></a>';
            }
            $onClick = "llenarFormulario('form', ['id_detalle_factura', 'producto','autocomplete','cantidad','precio'],
                        ['$det->id_detalle_factura','$det->id_detalleproducto','$det->producto', '$det->cantidad','$det->precio'])";
            $editar='<a class="icono icon-pencil" onClick="'.$onClick.'"></a>';
            $this->table->add_row($det->producto,$det->unidad,$det->cantidad,$det->precio,$det->total,$det->estado_factura_producto,$botones,
            '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/Detallefactura/EliminarDato/'.$det->id_detalle_factura.'/'.$det->id_factura.'/'.$det->total).'></a>',$editar);
          } else {
            $botones= '<a class="icono icon-liquidar" href="'.base_url('index.php/Bodega/Detallefactura/Cargar/'.$det->id_detalle_factura.'/'.$det->id_factura.'/'.$det->total.'/').'"></a>';
            $onClick = "llenarFormulario('form', ['id_detalle_factura', 'producto','autocomplete','cantidad','precio'],
                        ['$det->id_detalle_factura','$det->id_detalleproducto','$det->producto', '$det->cantidad','$det->precio'])";
            $this->table->add_row($det->producto,$det->unidad,$det->cantidad,$det->precio,$det->total,$det->estado_factura_producto,$botones,
            '<a class="icono icon-denegar"></a>','<a class="icono icon-denegar"></a>');

          }

        } else {
          $this->table->add_row($det->producto,$det->unidad,$det->cantidad,$det->precio,$det->total,$det->estado_factura_producto);
        }


        $i++;
      }
    } else {
      $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
      $this->table->add_row($msg);
    }

    if ($this->input->is_ajax_request()) {
      echo "<div class='table-responsive'>" . $this->table->generate(). "</div>" ;
    } else {
      return "<div class='table-responsive'>" . $this->table->generate(). "</div>" ;
    }
  }
  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $data = array(
        'id_detalle_factura' =>$this->input->post('id_detalle_factura'),
        'id_detalleproducto' =>$this->input->post('producto'),
        'cantidad' => $this->input->post('cantidad'),
        'precio' => $this->input->post('precio'),
        'id_factura'=>$this->input->post('factura'),
    );
    $modulo=$this->User_model->obtenerModulo('Bodega/Detallefactura');
    $USER = $this->session->userdata('logged_in');
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
    if (!($data['id_detalle_factura'] == '')){
      $rastrea['operacion']='ACTUALIZA';
      $rastrea['id_registro']=$data['id_detalle_factura'];
      $this->User_model->insertarRastreabilidad($rastrea);
      $data['total']=$data['precio']*$data['cantidad'];
        $this->Detallefactura_Model->actualizarDetalleFactura($data['id_detalle_factura'],$data);
        redirect('/Bodega/Detallefactura/index/'.$data['id_factura'].'/update');
    }else{
      $rastrea['operacion']='INSERTA';
      $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_factura');
      $this->User_model->insertarRastreabilidad($rastrea);
      $this->Detallefactura_Model->insertarDetalleFactura($data);
      redirect('/Bodega/Detallefactura/index/'.$data['id_factura'].'/new');
    }
  }

  public function Cargar(){
      $estado=$this->Detallefactura_Model->retornarEstado($this->uri->segment(4));
      if($estado=='INGRESADO'){
        $sub=$this->Factura_Model->obtenerTotalFactura($this->uri->segment(5));
        $tot=$this->uri->segment(6);
        $this->Detallefactura_Model->cargar($this->uri->segment(4),$this->uri->segment(5),$tot+$sub);
        redirect('/Bodega/Detallefactura/index/'.$this->uri->segment(5).'/cargar');
      }else{
        redirect('/Bodega/Detallefactura/index/'.$this->uri->segment(5).'/cargada');
      }
  }
  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Detallefactura');
    $USER = $this->session->userdata('logged_in');
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
    $estado=$this->Detallefactura_Model->retornarEstado($this->uri->segment(4));
    if($estado=='INGRESADO'){
      $id = $this->uri->segment(4);
      $this->Detallefactura_Model->eliminarDetalleFactura($id);
      $this->User_model->insertarRastreabilidad($rastrea);
      redirect('/Bodega/Detallefactura/index/'.$this->uri->segment(5).'/delete');
    }else{
      $tot=$this->uri->segment(6);
      $sub=$this->Factura_Model->obtenerTotalFactura($this->uri->segment(5));
      $id = $this->uri->segment(4);
      $this->Detallefactura_Model->cargar($this->uri->segment(4),$this->uri->segment(5),$sub-$tot);
      $this->Detallefactura_Model->eliminarDetalleFactura($id);
      $this->User_model->insertarRastreabilidad($rastrea);
      redirect('/Bodega/Detallefactura/index/'.$this->uri->segment(5).'/delete');
    }
  }

  public function AutocompleteEspecificoProducto(){
    $id_factura=$this->uri->segment(4);
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->Detallefactura_Model->buscarEspecificosProductos($id_factura, $this->input->post('autocomplete'));
        } else {
            $registros = $this->Detallefactura_Model->obtenerEspecificosProductos($id_factura);
        }
      } else {
            $registros = $this->Detallefactura_Model->obtenerEspecificosProductos($id_factura);
      }
      if ($registros == '') {
        echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      }else {
        $i = 1;
        foreach ($registros as $producto) {
            echo '<div id="'.$i.'" class="suggest-element" ida="producto'.$producto->id_detalleproducto.'"><a id="producto'.
            $producto->id_detalleproducto.'" data="'.$producto->id_detalleproducto.'"  data1="'.$producto->id_especifico.' - '.$producto->nombre.' - '.$producto->nombre_unidad.'"
             data2="'.$producto->cantidad.'" >'
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
