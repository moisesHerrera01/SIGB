<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detalle_retiro extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Detalle_solicitud_producto_model','Bodega/Solicitud_Model',
    'Bodega/Fuentefondos_model','Bodega/DetalleProducto_model','Bodega/Kardex_model'));
  }

  public function index(){
    $modulo=$this->User_model->obtenerModuloNombre($this->uri->segment(5));
    $id_modulo=$this->uri->segment(5);
    if ($this->uri->segment(4) == '' || $this->Solicitud_Model->obtenerSolicitud($this->uri->segment(4)) == '') {
      $data['body'] = "ERRROR";
      $this->load->view('base', $data);
    } else {
    $data['title'] = "Detalle Retiro";
    $estado=$this->Solicitud_Model->retornarEstado($this->uri->segment(4));
    $data['js'] = "assets/js/validate/detret.js";

    $solicitud=$this->Solicitud_Model->obtenerTodaSolicitud($this->uri->segment(4));
    foreach ($solicitud as $sol) {
      $fuente=$sol->id_fuentes;
    }
    $msg['id_solicitud'] = $this->uri->segment(4);
    $msg['fuente'] = $fuente;
    $men = array('alert' => $this->uri->segment(6),'controller'=>'detalle_retiro','estado'=>$estado,
    'modulo'=>$modulo, 'id_modulo'=>$id_modulo);

		$data['body'] = $this->load->view('mensajes', $men, TRUE) . $this->load->view('Bodega/detalle_retiro_view',$msg,TRUE) .
                    "<br><div class='content_table '>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Detalle Retiro</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}}

  public function mostrarTabla(){
    $USER = $this->session->userdata('logged_in');
    /*
    * Configuracion de la tabla
    */

    $btn_descargo = "";
    $template = array(
        'table_open' => '<table class="table table-striped table-bordered">'
    );
    $this->table->set_template($template);

    if ($USER['rol'] == 'ADMINISTRADOR SICBAF' || $USER['rol'] == 'COLABORADOR BODEGA' || $USER['rol'] == 'JEFE BODEGA' || $USER['rol'] == 'TECNICO BODEGA') {

      $btn_descargo = "<div class=\"content-btn-table\"><a class=\"btn btn-default\" href='".base_url("index.php/Bodega/detalle_retiro/descargarTodos/" . $this->uri->segment(4).'/'.$this->uri->segment(5))
                       . "'>Descargar Todos</a></div>";
      $modulo=$this->User_model->obtenerModuloNombre($this->uri->segment(5));
      if ($modulo=='Bodega/Solicitud_retiro') {
        $this->table->set_heading('Producto','Unidad de Medida','Especifico','Cantidad', 'Precio',
        'Total','Fuente Fondos','Estado','Eliminar', 'Evaluar', 'Calcular','Descargar');
      }else {
        $this->table->set_heading('Producto','Unidad de Medida','Especifico','Cantidad', 'Precio',
        'Total','Fuente Fondos','Estado','Eliminar','Editar', 'Evaluar', 'Calcular','Descargar');
      }


      $registros;

      if ($this->input->is_ajax_request()) {
      } else {
            $registros = $this->Detalle_solicitud_producto_model->obtenerDetalleSolicitudProductos($this->uri->segment(4));
      }

      if (!($registros == FALSE)) {
        $i = 1;
        foreach($registros as $det) {
          $datos=$this->Detalle_solicitud_producto_model->obtenerDatos($det->id_detalleproducto);
          $fuente=$this->Fuentefondos_model->obtenerFuente($det->id_fuentes);
          foreach ($datos as $detsol) {
            $onClick = "llenarFormulario('form', ['id', 'producto','autocomplete1','cantidad','precio',
                        'autocomplete2','fuente','autocomplete3','especifico'],
                        ['$det->id_detalle_solicitud_producto','$detsol->id_producto','$detsol->producto',
                         '$det->cantidad','$det->precio','$fuente','$det->id_fuentes','$detsol->nombre_especifico',
                         '$detsol->id_especifico'], false, false, ['autocomplete3', 'uri', 'index.php/Bodega/Especificos/AutocompletePorProducto/$detsol->id_producto'])";
            $estado = $this->Detalle_solicitud_producto_model->retornarEstado($det->id_detalle_solicitud_producto);
            $evaluar = '<a class="icono icon-stats-dots" href='.base_url('index.php/Bodega/kardex/ReporteKardex/2017-01-01'
            .'/'.date('Y-m-d').'/'.$detsol->id_producto.'/0/'.$this->uri->segment(4).'/'.$this->uri->segment(5)).'></a>';
            $descargar='<a class="icono icon-descargar" href="'.base_url('index.php/Bodega/detalle_retiro/descargar/'.$det->id_detalle_solicitud_producto.'/'.$det->id_solicitud.'/'.$this->uri->segment(5)).'"></a>';
            if ($estado == 'DESCARGADO'){
              if ($this->Detalle_solicitud_producto_model->obtenerEstadoSolicitud($det->id_detalle_solicitud_producto)=='LIQUIDADA') {
                  $modificar = '<a class="icono icon-denegar"></a>';
                  $eliminar = '<a class="icono icon-denegar"></a>';
                  $descargar= '<a class="icono icon-denegar"></a>';
              }else {
                $modificar = '<a class="icono icon-denegar"></a>';
                $eliminar = '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/detalle_retiro/EliminarDato/'.$det->id_detalle_solicitud_producto.'/'.$det->id_solicitud.'/'.$det->total.'/'.$this->uri->segment(5)).'></a>';
                $descargar= '<a class="icono icon-denegar"></a>';
              }
            }elseif ($this->Solicitud_Model->validarDescargar($det->id_detalle_solicitud_producto)) {
              $descargar= '<a class="icono icon-lock"></a>';
              $modificar = '<a class="icono icon-pencil" onClick="'.$onClick.'"></a>';
              $eliminar = '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/detalle_retiro/EliminarDato/'.$det->id_detalle_solicitud_producto.'/'.$det->id_solicitud.'/'.$det->total.'/'.$this->uri->segment(5)).'></a>';
            }
            else {
              $modificar = '<a class="icono icon-pencil" onClick="'.$onClick.'"></a>';
              $eliminar = '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/detalle_retiro/EliminarDato/'.$det->id_detalle_solicitud_producto.'/'.$det->id_solicitud.'/'.$det->total.'/'.$this->uri->segment(5)).'></a>';
              $descargar='<a class="icono icon-descargar" href="'.base_url('index.php/Bodega/detalle_retiro/descargar/'.$det->id_detalle_solicitud_producto.'/'.$det->id_solicitud.'/'.$this->uri->segment(5)).'"></a>';
            }
            if ($modulo=='Bodega/Solicitud_retiro') {
              $this->table->add_row($detsol->producto,$detsol->unidad,$detsol->nombre_especifico,$det->cantidad,$det->precio,$det->total,$fuente,
              $det->estado_solicitud_producto,$eliminar, $evaluar,
            '<a class="icono icon-coin-dollar" href="'.base_url('index.php/Bodega/detalle_retiro/calcularPrecioBoton/'.$det->id_detalle_solicitud_producto.'/'.$det->id_solicitud.'/'.$this->uri->segment(5)).'"></a>',$descargar);
          }else {
            $this->table->add_row($detsol->producto,$detsol->unidad,$detsol->nombre_especifico,$det->cantidad,$det->precio,$det->total,$fuente,
            $det->estado_solicitud_producto,$eliminar,$modificar, $evaluar,
          '<a class="icono icon-coin-dollar" href="'.base_url('index.php/Bodega/detalle_retiro/calcularPrecioBoton/'.$det->id_detalle_solicitud_producto.'/'.$det->id_solicitud.'/'.$this->uri->segment(5)).'"></a>',$descargar);
          }
          $i++;
        }
      }
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "13");
        $this->table->add_row($msg);
      }
    } else {
      $this->table->set_heading('Producto','Unidad de Medida','Especifico','Cantidad', 'Precio',
      'Total','Fuente Fondos','Estado','Eliminar','Editar');

      $registros;

      if ($this->input->is_ajax_request()) {
      } else {
            $registros = $this->Detalle_solicitud_producto_model->obtenerDetalleSolicitudProductos($this->uri->segment(4));
      }

      if (!($registros == FALSE)) {
        $i = 1;
        foreach($registros as $det) {
          $datos=$this->Detalle_solicitud_producto_model->obtenerDatos($det->id_detalleproducto);
          $fuente=$this->Fuentefondos_model->obtenerFuente($det->id_fuentes);
          foreach ($datos as $detsol) {
            $onClick = "llenarFormulario('form', ['id', 'producto','autocomplete1','cantidad','precio',
                        'autocomplete2','fuente','autocomplete3','especifico'],
                        ['$det->id_detalle_solicitud_producto','$detsol->id_producto','$detsol->producto',
                         '$det->cantidad','$det->precio','$fuente','$det->id_fuentes'])";
            $this->table->add_row($detsol->producto,$detsol->unidad,$detsol->nombre_especifico,$det->cantidad,$det->precio,
            $det->total, $fuente, $det->estado_solicitud_producto,
          '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/detalle_retiro/EliminarDato/'.$det->id_detalle_solicitud_producto.'/'.$det->id_solicitud.'/'.$det->total.'/'.$this->uri->segment(5)).'></a>',
          '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>');
          $i++;
        }
      }
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "11");
        $this->table->add_row($msg);
      }
    }



    if ($this->input->is_ajax_request()) {
      echo $btn_descargo . "<div class='table-responsive'>" . $this->table->generate() . "</div>" ;
    } else {
      return $btn_descargo . "<div class='table-responsive'>" . $this->table->generate() . "</div>";
    }
  }
  /*
  * Actualiza o Registra al sistema
  */

  public function calcularPrecioBoton($id){
    $detalle=$this->Detalle_solicitud_producto_model->obtenerDetalleCompleto($id);
    $precio=$this->calcularPrecio($detalle->id_detalleproducto,$detalle->cantidad,$detalle->id_fuentes);
    $data = array(
        'id_detalleproducto'=>$detalle->id_detalleproducto,
        'cantidad' =>$detalle->cantidad,
        'precio' => $precio['precio'],
        'id_solicitud'=>$detalle->id_solicitud,
        'id_fuentes'=>$detalle->id_fuentes,
        'total'=>$precio['precio']*$detalle->cantidad,
    );
    if($detalle->precio!=0){
        $this->Detalle_solicitud_producto_model->actualizarDetalleSolicitudProducto($detalle->id_detalle_solicitud_producto,$data);
        redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$this->uri->segment(6).'/ya_asignado');
    }else{
      if($data['cantidad']>$precio['existencia']){
        if($precio['existencia']<1){
          $data['precio']=0;
          $data['total']=0;
          $this->Detalle_solicitud_producto_model->actualizarDetalleSolicitudProducto($detalle->id_detalle_solicitud_producto,$data);
          redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$this->uri->segment(6).'/noexist');
        }else{
          $data['cantidad']=$precio['existencia'];
          $data['total']=$precio['existencia']*$precio['precio'];
          $this->Detalle_solicitud_producto_model->actualizarDetalleSolicitudProducto($detalle->id_detalle_solicitud_producto,$data);
          redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$this->uri->segment(6).'/exist');
        }
      }else {
          if($precio['diferencia']!=0){
            $precio2=$this->calcularPrecio($detalle->id_detalleproducto,$precio['diferencia'],$detalle->id_fuentes);
            $data2 = array(
                'id_detalleproducto'=>$detalle->id_detalleproducto,
                'cantidad' =>$detalle->cantidad,
                'precio' => $precio2['precio'],
                'id_solicitud'=>$detalle->id_solicitud,
                'id_fuentes'=>$detalle->id_fuentes,
                'total'=>$precio2['precio']*$detalle->cantidad,
            );
            if($data2['cantidad']>$precio2['existencia']){
              if($precio2['existencia']<1){
                $data2['precio']=0;
                  $this->Detalle_solicitud_producto_model->insertarDetalleSolicitudProducto($data2);
              }else{
                $data['cantidad']=$precio2['existencia'];
                  $this->Detalle_solicitud_producto_model->insertarDetalleSolicitudProducto($data2);
              }
            }else {
                $this->Detalle_solicitud_producto_model->insertarDetalleSolicitudProducto($data2);
            }
            $data['cantidad']=$data['cantidad']-$precio['diferencia'];
            $data['total']=$data['cantidad']*$data['precio'];
            $this->Detalle_solicitud_producto_model->actualizarDetalleSolicitudProducto($detalle->id_detalle_solicitud_producto,$data);
            redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$this->uri->segment(6).'/peps');
          }
            $this->Detalle_solicitud_producto_model->actualizarDetalleSolicitudProducto($detalle->id_detalle_solicitud_producto,$data);
            redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$this->uri->segment(6).'/peps');
      }
    }
  }

  public function RecibirDatos(){
    $id_modulo=$this->input->post('id_modulo');

    $precio=$this->calcularPrecio($this->input->post('producto'),$this->input->post('cantidad'),$this->input->post('fuente'));
    $data = array(
        'id_detalleproducto'=>$this->input->post('producto'),
        'cantidad' => $this->input->post('cantidad'),
        'precio' => $precio['precio'],
        'id_solicitud'=>$this->input->post('solicitud'),
        'id_fuentes'=>$this->input->post('fuente'),
        'total'=>$precio['precio']*$this->input->post('cantidad'),
    );
    $modulo=$this->User_model->obtenerModulo('Bodega/detalle_retiro');
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
    if($this->input->post('precio')!=0){
      $this->casos(0,$data,'',$precio,$id_modulo);
    }else{
      if($data['cantidad']>$precio['existencia']){
        $this->casos(1,$data,'',$precio,$id_modulo);
      }else {
          if($precio['diferencia']!=0){
            $precio2=$this->calcularPrecio($this->input->post('producto') ,$precio['diferencia'],$this->input->post('fuente'));
            $data2 = array(
                'id_detalleproducto'=>$this->input->post('producto'),
                'cantidad' => $precio['diferencia'],
                'precio' => $precio2['proximo_precio'],
                'id_solicitud'=>$this->input->post('solicitud'),
                'id_fuentes'=>$this->input->post('fuente'),
                'total'=>$precio2['proximo_precio']*$precio['diferencia'],
            );
            if($data2['cantidad']>$precio2['existencia']){
              if($precio2['existencia']<1){
                $data2['precio']=0;
                  $rastrea['operacion']='INSERTA';
                  $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_producto');
                  $this->User_model->insertarRastreabilidad($rastrea);
                  $this->Detalle_solicitud_producto_model->insertarDetalleSolicitudProducto($data2);
              }else{
                $data['cantidad']=$precio2['existencia'];
                  $rastrea['operacion']='INSERTA';
                  $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_producto');
                  $this->User_model->insertarRastreabilidad($rastrea);
                  $this->Detalle_solicitud_producto_model->insertarDetalleSolicitudProducto($data2);
              }
            }else {
                $rastrea['operacion']='INSERTA';
                $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_producto');
                $this->User_model->insertarRastreabilidad($rastrea);
                $this->Detalle_solicitud_producto_model->insertarDetalleSolicitudProducto($data2);
            }
                $this->casos(2,$data,'',$precio,$id_modulo);
          }
            //$data['total']=$precio['existencia'];
            $this->casos(3,$data,'',$precio,$id_modulo);
      }
    }
  }

  public function casos($i,$data,$data2,$precio,$id_modulo){
    $modulo=$this->User_model->obtenerModulo('Bodega/detalle_retiro');
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
    switch ($i) {
    case 0: //precio ya ha sido asignado por PEPS
        $data['precio']=$this->input->post('precio');
        $data['total']=$this->input->post('precio')*$this->input->post('cantidad');
        if (!($this->input->post('id') == '')){
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          $this->Detalle_solicitud_producto_model->actualizarDetalleSolicitudProducto($this->input->post('id'),$data);
          redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$id_modulo.'/update');
        }
          $rastrea['operacion']='INSERTA';
    case 1: //cantidad solicitada es mayor que las existencias
    if($precio['existencia']<1){
      $data['precio']=0;
      $data['total']=0;
      if (!($this->input->post('id') == '')){
        $rastrea['operacion']='ACTUALIZA';
        $rastrea['id_registro']=$this->input->post('id');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Detalle_solicitud_producto_model->actualizarDetalleSolicitudProducto($this->input->post('id'),$data);
        redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$id_modulo.'/noexist');
      }
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_producto');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Detalle_solicitud_producto_model->insertarDetalleSolicitudProducto($data);
        redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$id_modulo.'/noexist');
    }else{
      $data['cantidad']=$precio['existencia'];
      $data['total']=$precio['existencia']*$precio['precio'];
      if (!($this->input->post('id') == '')){
        $rastrea['operacion']='ACTUALIZA';
        $rastrea['id_registro']=$this->input->post('id');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Detalle_solicitud_producto_model->actualizarDetalleSolicitudProducto($this->input->post('id'),$data);
        redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$id_modulo.'/exist');
      }
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_producto');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Detalle_solicitud_producto_model->insertarDetalleSolicitudProducto($data);
        redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$id_modulo.'/exist');
    }
        break;
    case 2: //añadir cantidad parcial con el primer precio
    $data['cantidad']=$data['cantidad']-$precio['diferencia'];
    $data['total']=$data['cantidad']*$data['precio'];
    if (!($this->input->post('id') == '')){
      $rastrea['operacion']='ACTUALIZA';
      $rastrea['id_registro']=$this->input->post('id');
      $this->User_model->insertarRastreabilidad($rastrea);
      $this->Detalle_solicitud_producto_model->actualizarDetalleSolicitudProducto($this->input->post('id'),$data);
      redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$id_modulo.'/update');
    }
      $rastrea['operacion']='INSERTA';
      $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_producto');
      $this->User_model->insertarRastreabilidad($rastrea);
      $this->Detalle_solicitud_producto_model->insertarDetalleSolicitudProducto($data);
      redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$id_modulo.'/new');
        break;
      case 3: //Se ingresa un producto del cual su catidad solicitada está en un solo precio
      if (!($this->input->post('id') == '')){
        $rastrea['operacion']='ACTUALIZA';
        $rastrea['id_registro']=$this->input->post('id');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Detalle_solicitud_producto_model->actualizarDetalleSolicitudProducto($this->input->post('id'),$data);
        redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$id_modulo.'/update');
      }
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_detalle_solicitud_producto');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Detalle_solicitud_producto_model->insertarDetalleSolicitudProducto($data);
        redirect('/Bodega/detalle_retiro/index/'.$data['id_solicitud'].'/'.$id_modulo.'/new');
        break;
}
  }

  public function descargar(){
      $id = $this->uri->segment(4);
      $estado=$this->Detalle_solicitud_producto_model->retornarEstado($id);
      if($estado=='INGRESADO'){
        $this->Detalle_solicitud_producto_model->descargar($id);
        redirect('/Bodega/detalle_retiro/index/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/descargar');
      }else{
        redirect('/Bodega/detalle_retiro/index/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/descargado');
      }
  }

  public function descargarTodos(){
      $id = $this->uri->segment(4);
      $detalles=$this->Detalle_solicitud_producto_model->obtenerDetalles($id);
      foreach ($detalles as $det) {
        $estado=$this->Detalle_solicitud_producto_model->retornarEstado($det->id_detalle_solicitud_producto);
        if($estado=='INGRESADO'){
          $this->Detalle_solicitud_producto_model->descargar($det->id_detalle_solicitud_producto);
        }
      }
      redirect('/Bodega/detalle_retiro/index/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/descargar');
  }


  public function EliminarDato(){
    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('Bodega/detalle_retiro');
    $id = $this->uri->segment(4);
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
    $detalle=$this->Detalle_solicitud_producto_model->obtenerDetalleCompleto($id);
      //if($detalle->estado_solicitud_producto=='INGRESADO'){
        $this->Detalle_solicitud_producto_model->eliminarDetalleSolicitudProducto($id);
        redirect('/Bodega/detalle_retiro/index/'.$this->uri->segment(5).'/'.$this->uri->segment(7).'/delete');
    // }
      /*else{
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Kardex_model->eliminarKardex($detalle->id_detalle_solicitud_producto,
        $detalle->id_detalleproducto,$detalle->id_fuentes,$detalle->cantidad,$detalle->precio);
        redirect('/Bodega/detalle_retiro/index/'.$this->uri->segment(5).'/'.$this->uri->segment(7).'/delete');
      }*/
  }

  public function calcularPrecio($id,$descargar, $id_fuentes){
    $precios=$this->Detalle_solicitud_producto_model->preciosFecha2($id,$id_fuentes);
    $movimientos=$this->Detalle_solicitud_producto_model->existencia($id, $id_fuentes);
	  $entradas=$movimientos['entrada'];
  var_dump($entradas);
	$salidas=$movimientos['salida'];
  $existencias=$this->Detalle_solicitud_producto_model->existencia($id, $id_fuentes);
	$existencia=$existencias['existencia'];
    $cant=0;
    $diferencia=0;
    $disponible=0;
    $precio=0.0;
    $i=0;
    $i_precio=0;
    foreach ($precios as $pre) {
      if($cant<=$salidas){
        $cant=$cant+$pre->cantidad;
        $i_precio=$i;
      }
      $disponible=$cant-$salidas;
      $i++;
    }
    $prx_precio=$precios[$i_precio+1]->precio;
    $precio=$precios[$i_precio]->precio;
    if($disponible<$descargar){
      $diferencia=$descargar-$disponible;
    }
    $result = array(
        'diferencia' => $diferencia,
        'proximo_precio' => $prx_precio,
        'precio'=>$precio,
        'existencia'=>$existencia
    );
    return $result;
  }
}
?>
