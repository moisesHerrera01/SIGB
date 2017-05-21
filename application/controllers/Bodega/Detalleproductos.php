<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DetalleProductos extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/DetalleProducto_model', 'Bodega/Producto','Bodega/Especifico'));
  }

  public function index(){

    $data['title'] = "Detalle Producto";
    $data['js'] = "assets/js/validate/dp.js";
    $id_espec = $this->uri->segment(4);
    $nombre = $this->Especifico->obtenerEspecifico($this->uri->segment(4));
    $msg = array('alert' => $this->uri->segment(5),'nombre'=> $nombre,'id_espec' => $id_espec,'controller'=>'detalle_producto');
		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Bodega/detalleProducto_view',$msg,TRUE) .
                    "<br><div class='content_table '>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Detalle Productos</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */

    $template = array(
        'table_open' => '<table class="table table-striped table-bordered">'
    );
    $this->table->set_template($template);
    $this->table->set_heading('ID Especifico','Nombre del Producto','Modificar', 'Eliminar');

    /*
    * Filtro a la BD
    */

    /*Obtiene el numero de registros a mostrar por pagina */
    $num = '10';
    $pagination = '';
    $registros;
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('busca') == "")) {
          $registros = $this->DetalleProducto_model->buscarDetalleProductos($this->input->post('busca'),$this->uri->segment(4));
      } else {
          $registros = $this->DetalleProducto_model->obtenerDetalleProductosLimit($num, $this->uri->segment(5),$this->uri->segment(4));
          $pagination = paginacion('index.php/Bodega/Detalleproductos/index/'.$this->uri->segment(4), $this->DetalleProducto_model->totalDetalleProductos($this->uri->segment(4)),
                        $num, '5');
      }
    } else {
          $registros = $this->DetalleProducto_model->obtenerDetalleProductosLimit($num, $this->uri->segment(5),$this->uri->segment(4));
          $pagination = paginacion('index.php/Bodega/Detalleproductos/index/'.$this->uri->segment(4), $this->DetalleProducto_model->totalDetalleProductos($this->uri->segment(4)),
                        $num, '5');
    }

    /*
    * llena la tabla con los datos consultados
    */

    if (!($registros == FALSE)) {
      foreach($registros as $detalle) {
          $nom_espe = $this->Especifico->obtenerEspecifico($detalle->id_especifico);
          $nom_pro = $this->Producto->obtenerProducto($detalle->id_producto);
          $onClick = "llenarFormulario('detalleProducto', ['id_especifico','id_producto','autocomplete','id_pro'], ['$nom_espe', '$detalle->id_producto','$nom_pro','$detalle->id_producto'])";

          $nombre_producto = $this->Producto->obtenerProducto($detalle->id_producto);
          $this->table->add_row($detalle->id_especifico, $nombre_producto,
                          //form_button($btn_act), $form_el,
                          '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                          '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/Detalleproductos/EliminarDato/'.$detalle->id_especifico.'/'.$detalle->id_producto).'></a>');
      }
    } else {
      $msg = array('data' => "Texto no encontrado", 'colspan' => "6");
      $this->table->add_row($msg);
    }

    /*
    * vuelve a verificar para mostrar los datos
    */
    if ($this->input->is_ajax_request()) {
      echo "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
    } else {
      return "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Detalleproductos');
    $data = array(
        'id_especifico' => $this->input->post('id_espec'),
        'id_producto' => $this->input->post('id_producto')
    );
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
    if($this->input->post('id_producto')){
      $id_pro =  $this->input->post('id_pro');
      $id_detalle = $this->DetalleProducto_model->obtenerDetalleProducto($id_pro,$data['id_especifico']);
      if (!$id_detalle == ''){
        $rastrea['operacion']='ACTUALIZA';
        $rastrea['id_registro']=$id_detalle;
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->DetalleProducto_model->actualizarDetalleProducto($id_detalle,$data);
        redirect('/Bodega/Detalleproductos/index/'.$data['id_especifico'].'/update');
      }
      else{
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_producto');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->DetalleProducto_model->insertarDetalleProducto($data);
        redirect('/Bodega/Detalleproductos/index/'.$data['id_especifico'].'/new');
      }
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Detalleproductos');
    $USER = $this->session->userdata('logged_in');
    $id_detalle = $this->DetalleProducto_model->obtenerDetalleProducto($this->uri->segment(5),$this->uri->segment(4));
    $data = array(
      'id_especifico' => $this->uri->segment(4),
      'id_producto' => $this->uri->segment(5)
      );
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
        'id_registro' =>$id_detalle,
      );
    $this->DetalleProducto_model->eliminarDetalleProducto($data);
    $this->User_model->insertarRastreabilidad($rastrea);
    redirect('/Bodega/Detalleproductos/index/'.$this->uri->segment(4).'/delete');
  }
}
?>
