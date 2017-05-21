<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orden_compra extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
      $this->load->model(array('Compras/Orden_Compra_Model','Compras/Solicitud_Disponibilidad_Model','Compras/Solicitud_Compra_Model'));
  }

  public function index(){
    if($this->session->userdata('logged_in')){
      $data['title'] = "Orden de Compra";
      $data['js'] = "assets/js/validate/orden_compra.js";
      $msg = array('alert' => $this->uri->segment(4));

  		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Compras/orden_compra_view', '', TRUE) .
                      "<br><div class='content_table '>" .
                      "<div class='limit-content-title'><span class='icono icon-table icon-title'> Orden de Compra</span></div>".
                      "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $this->load->view('base', $data);
    } else {
      redirect('login/index/error_no_autenticado');
    }
	}

  public function mostrarTabla() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $anyo_en_curso=date($anyo."y");
    $modulo=$this->User_model->obtenerModulo('Compras/Orden_Compra');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
        /*
        * Configuracion de la tabla
        */

        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Número Orden','Id Solicitud','Fecha', 'Proveedor', 'Observación','Monto','Confirmar',
        'Detener', 'Productos','Detalle Resumen','Modificar', 'Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';

        $registros = $this->Orden_Compra_Model->obtenerOrdenComprasLimit($num, $this->uri->segment(4));
        $pagination = paginacion('index.php/Compras/Orden_Compra/index/', $this->Orden_Compra_Model->totalOrdenes(), $num, '4');

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          $modulo=$this->User_model->obtenerModulo('Compras/Orden_Compra');
          foreach($registros as $orden) {
              $onClick = "llenarFormulario('Orden', ['numero', 'id', 'proveedor','sol_compra','fecha', 'lu_not',
              'autocomplete1', 'autocomplete2', 'det_disponibilidad','monto_total_oc'],
                          ['$orden->numero_orden_compra',$orden->id_orden_compra, '$orden->id_proveedores', $orden->id_solicitud_compra,
                          '$orden->fecha', '$orden->lugar_notificaciones', '$orden->nombre_proveedor', 'Req:$orden->numero_solicitud_compra Linea:$orden->linea_trabajo Monto:$".number_format($orden->monto_sub_total, 2)."',
                          $orden->id_detalle_solicitud_disponibilidad,'$orden->monto_total_oc'],false,false,
                          false,'obs','$orden->observacion')";

                          $aprobar = '<a class="icono icon-liquidar" href="'.base_url('index.php/Compras/Orden_Compra/Aprobar/'.$orden->numero_solicitud_compra.'/').'"></a>';
                          $denegar = '<a class="icono icon-cross" href="'.base_url('index.php/Compras/Orden_Compra/Denegar/'.$orden->numero_solicitud_compra.'/').'"></a>';

                          $botones=  '<a class="icono icon-price" href="'.base_url('index.php/Compras/Detalle_Orden_Compra/index/'.$orden->id_solicitud_compra.'/'.$modulo.'/').'">';
                          $detalle_resumen=  '<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_orden_resumen/index/'.$orden->id_orden_compra.'/'.$modulo.'/').'">';
                          if ($USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'JEFE UACI' || $USER['rol'] == 'ADMINISTRADOR SICBAF'){
                            if($orden->estado_solicitud_compra=='APROBADA DISPONIBILIDAD' || $orden->nivel_solicitud==5){
                               $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                               $aprobar = '<a class="icono icon-liquidar" href="'.base_url('index.php/Compras/Orden_Compra/Aprobar/'.$orden->numero_solicitud_compra.'/').'"></a>';
                               $denegar = '<a class="icono icon-cross" href="'.base_url('index.php/Compras/Orden_Compra/Denegar/'.$orden->numero_solicitud_compra.'/').'"></a>';
                               $eliminar = '<a class="icono icon-eliminar" uri='.base_url('index.php/Compras/Orden_Compra/EliminarDato/'.$orden->id_orden_compra).'></a>';
                            }elseif ($orden->estado_solicitud_compra=='APROBADA ORDEN' || $orden->nivel_solicitud==6) {
                              $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                              $aprobar = '<a class="icono icon-denegar"></a>';
                              $denegar = '<a class="icono icon-denegar"></a>';
                              $eliminar = '<a class="icono icon-eliminar" uri='.base_url('index.php/Compras/Orden_Compra/EliminarDato/'.$orden->id_orden_compra).'></a>';
                            }elseif($orden->nivel_solicitud>6){
                              $actualizar = '<a class="icono icon-denegar"></a>';
                              $denegar = '<a class="icono icon-denegar"></a>';
                              $aprobar = '<a class="icono icon-denegar"></a>';
                              $eliminar='<a class="icono icon-denegar"></a>';
                            }
                          }
                          if ($USER['rol'] == 'COLABORADOR COMPRAS' || $USER['rol'] == 'COLABORADOR UACI'){
                            if($orden->estado_solicitud_compra=='APROBADA DISPONIBILIDAD' || $orden->nivel_solicitud==5){
                               $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                               $aprobar = '<a class="icono icon-denegar"></a>';
                               $denegar = '<a class="icono icon-denegar"></a>';
                               $eliminar = '<a class="icono icon-eliminar" uri='.base_url('index.php/Compras/Orden_Compra/EliminarDato/'.$orden->id_orden_compra).'></a>';
                            }elseif ($orden->estado_solicitud_compra=='APROBADA ORDEN' || $orden->nivel_solicitud==6) {
                              $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                              $aprobar = '<a class="icono icon-denegar"></a>';
                              $denegar = '<a class="icono icon-denegar"></a>';
                              $eliminar = '<a class="icono icon-eliminar" uri='.base_url('index.php/Compras/Orden_Compra/EliminarDato/'.$orden->id_orden_compra).'></a>';
                            }elseif($orden->nivel_solicitud>6){
                              $actualizar = '<a class="icono icon-denegar"></a>';
                              $denegar = '<a class="icono icon-denegar"></a>';
                              $aprobar = '<a class="icono icon-denegar"></a>';
                              $eliminar='<a class="icono icon-denegar"></a>';
                            }
                          }
              $this->table->add_row($orden->numero_orden_compra.'/'.$anyo_en_curso, $orden->id_solicitud_compra,
               $orden->fecha,$orden->nombre_proveedor,$orden->observacion,
                '$'.number_format($orden->monto_total_oc, 2),$aprobar,$denegar,$botones,$detalle_resumen,$actualizar,$eliminar);
          }
        } else {
          $msg = array('data' => "Texto no encontrado", 'colspan' => "13");
          $this->table->add_row($msg);
        }

        /*
        * vuelve a verificar para mostrar los datos
        */
        return "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
      } else {
        redirect('/Bodega/UnidadMedidas/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function RecibirDatos() {
    $modulo=$this->User_model->obtenerModulo('Compras/Orden_Compra');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
        'numero_orden_compra' =>$this->input->post('numero'),
        'id_proveedores' => $this->input->post('proveedor'),
        'id_solicitud_compra' => $this->input->post('sol_compra'),
        'fecha' => $this->input->post('fecha'),
        'observacion' => $this->input->post('obs'),
        'lugar_notificaciones' => $this->input->post('lu_not'),
        'id_detalle_solicitud_disponibilidad' => $this->input->post('det_disponibilidad'),
        'monto_total_oc' =>$this->input->post('monto_total_oc')
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
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Orden_Compra_Model->actualizarOrdenCompra($this->input->post('id'), $data);
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Compras/Orden_Compra/index/update');
        } else {
          redirect('/Compras/Orden_Compra/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Orden_Compra_Model->insertarOrdenCompra($data);
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_orden_compra')-1;
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Compras/Orden_Compra/index/new');
      } else {
        redirect('/Compras/Orden_Compra/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function EliminarDato() {
    $modulo=$this->User_model->obtenerModulo('Compras/Orden_Compra');
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
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
        $id = $this->uri->segment(4);
        $this->Orden_Compra_Model->eliminarOrdenCompra($id);
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Compras/Orden_Compra/index/delete');
      } else {
        redirect('/Compras/Orden_Compra/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function autocomplete() {
    $registros = '';
    $id_orden = $this->uri->segment(4);
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Orden_Compra_Model->buscarProducto($this->input->post('autocomplete'));
      } else {
          $registros = $this->Orden_Compra_Model->obtenerEspecificoProductos($id_orden);
      }
    } else {
          $registros = $this->Orden_Compra_Model->obtenerEspecificoProductos($id_orden);
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $pro) {
        echo '<div id="'.$i.'" class="suggest-element" ida="producto'.$pro->id_detalleproducto.'"><a id="producto'.
        $pro->id_detalleproducto.'" data="'.$pro->id_detalleproducto.'"  data1="'.$pro->id_especifico.'-'.$pro->nombre_producto.'"
        data2="'.$pro->cantidad.'">'
        .$pro->id_especifico.'-'.$pro->nombre_producto.'</a></div>';
        $i++;
      }
    }
  }

  public function AutocompleteOrdenCompra(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete4') == "")) {
          $registros = $this->Orden_Compra_Model->buscarOrdenesAutocomplete($this->input->post('autocomplete4'));
      } else {
          $registros = $this->Orden_Compra_Model->obtenerOrdenesAutocomplete();
      }
    } else {
          $registros = $this->Orden_Compra_Model->obtenerOrdenesAutocomplete();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    } else {
      $i = 1;
      foreach ($registros as $ord) {
        echo '<div id="'.$i.'" class="suggest-element" ida="orden'.$ord->id_orden_compra.'"><a id="orden'.
        $ord->id_orden_compra.'" data="'.$ord->id_orden_compra.'"  data1="'.$ord->numero_orden_compra.' '.$ord->fecha.'" >'
        .$ord->numero_orden_compra.'  '.$ord->fecha.'</a></div>';
        $i++;
      }
    }
  }

  public function Aprobar() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      $estado = $this->Solicitud_Compra_Model->obtenerEstadoSolicitud($id);
      $nivel = $this->Solicitud_Compra_Model->obtenerNivelSolicitud($id);
      if ($estado == 'APROBADA DISPONIBILIDAD'){
        $data = array(
            'estado_solicitud_compra' => 'APROBADA ORDEN DE COMPRA',
            'nivel_solicitud' => $nivel + 1
        );
      }
      $this->Solicitud_Compra_Model->actualizarSolicitudCompra($id,$data);
      redirect('/Compras/Orden_Compra/index/update');
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function Denegar() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      $nivel = $this->Solicitud_Compra_Model->obtenerNivelSolicitud($id);
      $data = array(
          'estado_solicitud_compra' => 'DENEGADA',
          'nivel_anterior' => $nivel,
          'nivel_solicitud' => 10
      );
      $this->Solicitud_Compra_Model->actualizarSolicitudCompra($id,$data);
      redirect('/Compras/Orden_Compra/index/update');
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

}
?>
