<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitud_Disponibilidad extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Compras/Solicitud_Compra_Model','Compras/Solicitud_Disponibilidad_Model', 'Notificacion_model'));
	}

	public function index(){
    if($this->session->userdata('logged_in')){
      $data['title'] = "Disponibilidad Financiera";
      $data['js'] = "assets/js/modal_disp.js";
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $fecha_actual=date($anyo."y-m-d");
      $msg = array('alert' => $this->uri->segment(4),'fecha'=>$fecha_actual,'controller'=>'Solicitud_Disponibilidad','nivel'=>4);
      $data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Compras/solicitud_disponibilidad_view',$msg,TRUE) . $this->load->view('modals/memorandum_disponibilidad', '',TRUE) .
                      "<br><div class='content_table '>" .
                      "<div class='limit-content-title'><span class='icono icon-table icon-title'> Historial de solicitudes de disponibilidad financiera</span></div>".
                      "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $this->load->view('base', $data);

    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function mostrarTabla(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      /*
      * Configuracion de la tabla
      */

      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('Disponibilidad financiera','Solicitud de Compra', 'Fecha confirmación', 'Sección','Montos','Productos','Confirmar','Denegar','Editar','Generar');

      /*
      * Filtro a la BD
      */
      /*Obtiene el numero de registros a mostrar por pagina */

 	  $num = '10';
      $pagination = '';
      $registros;
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('busca') == "")) {
            $registros = $this->Solicitud_Disponibilidad_Model->buscarSolicitudesDisponibilidadLimit($this->input->post('busca'));
        } else {
            $registros = $this->Solicitud_Disponibilidad_Model->obtenerSolicitudesDisponibilidadLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Compras/Solicitud_Disponibilidad/index/', $this->Solicitud_Disponibilidad_Model->totalSolicitudesDisponibilidad(),
                          $num, '4');
        }
      } else {
            $registros = $this->Solicitud_Disponibilidad_Model->obtenerSolicitudesDisponibilidadLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Compras/Solicitud_Disponibilidad/index/', $this->Solicitud_Disponibilidad_Model->totalSolicitudesDisponibilidad(),
                          $num, '4');
      }

      /*
      * llena la tabla con los datos consultados
      */

      if (!($registros == FALSE)) {
        foreach($registros as $sol) {
            $modulo=$this->User_model->obtenerModulo('Compras/Solicitud_Disponibilidad');
            $onClick = "llenarFormulario('solicitud', ['id','solicitud_compra','fechaIngreso','autocomplete','seccion',
            'especifico','fecha'],[$sol->id_solicitud_disponibilidad, '$sol->id_solicitud_compra','$sol->fecha_ingreso',
            '$sol->id_solicitud_compra','$sol->nombre_seccion','$sol->id_especifico','$sol->fecha'],
            false,false,false,'observaciones','$sol->observaciones')";
             $botones='<a class="icono icon-price" href="'.base_url('index.php/Compras/Detalle_Solicitud_Disponibilidad/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/').'"></a>';
             $montos='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_disponibilidad_montos/index/'.$sol->id_solicitud_disponibilidad.'/').'"></a>';
             if ($USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'JEFE UACI' || $USER['rol'] == 'ADMINISTRADOR SICBAF'){
               if($sol->estado_solicitud_compra=='APROBADA COMPRAS' || $sol->nivel_solicitud==4){
                  $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                  $aprobar = '<a class="icono icon-liquidar" href="'.base_url('index.php/Compras/Solicitud_Disponibilidad/Aprobar/'.$sol->id_solicitud_compra.'/').'"></a>';
                  //$denegar = '<a class="icono icon-cross" href="'.base_url('index.php/Compras/Solicitud_Disponibilidad/Denegar/'.$sol->id_solicitud_compra.'/').'"></a>';
                  $denegar = '<a class="icono icon-cross modal_open" data-id="'.$sol->id_solicitud_disponibilidad.'"></a>';
                  $solicitud_imp = '<a class="icono icon-acta" href="'.base_url('index.php/Compras/Disponibilidad/index/'.$sol->id_solicitud_compra.'/').'" target="_blank"></a>';
               }elseif ($sol->estado_solicitud_compra=='APROBADA DISPONIBILIDAD' || $sol->nivel_solicitud==5) {
                 $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                 $aprobar = '<a class="icono icon-denegar"></a>';
                 $denegar = '<a class="icono icon-denegar"></a>';
                 $solicitud_imp = '<a class="icono icon-acta" href="'.base_url('index.php/Compras/Disponibilidad/index/'.$sol->id_solicitud_compra.'/').'" target="_blank"></a>';
               }elseif($sol->nivel_solicitud>5){
                 $actualizar = '<a class="icono icon-denegar"></a>';
                 $denegar = '<a class="icono icon-denegar"></a>';
                 $aprobar = '<a class="icono icon-denegar"></a>';
                 $solicitud_imp = '<a class="icono icon-acta" href="'.base_url('index.php/Compras/Disponibilidad/index/'.$sol->id_solicitud_compra.'/').'" target="_blank"></a>';
               }
             }
             if ($USER['rol'] == 'COLABORADOR COMPRAS' || $USER['rol'] == 'COLABORADOR UACI'){
               if($sol->estado_solicitud_compra=='APROBADA COMPRAS' || $sol->nivel_solicitud==4){
                  $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                  $aprobar = '<a class="icono icon-denegar"></a>';
                  $denegar = '<a class="icono icon-denegar"></a>';
                  $solicitud_imp = '<a class="icono icon-acta" href="'.base_url('index.php/Compras/Disponibilidad/index/'.$sol->id_solicitud_compra.'/').'" target="_blank"></a>';
               }elseif ($sol->estado_solicitud_compra=='APROBADA DISPONIBILIDAD' || $sol->nivel_solicitud==5) {
                 $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                 $aprobar = '<a class="icono icon-denegar"></a>';
                 $denegar = '<a class="icono icon-denegar"></a>';
                 $solicitud_imp = '<a class="icono icon-acta" href="'.base_url('index.php/Compras/Disponibilidad/index/'.$sol->id_solicitud_compra.'/').'" target="_blank"></a>';
               }elseif($sol->nivel_solicitud>5){
                 $actualizar = '<a class="icono icon-denegar"></a>';
                 $denegar = '<a class="icono icon-denegar"></a>';
                 $aprobar = '<a class="icono icon-denegar"></a>';
                 $solicitud_imp = '<a class="icono icon-acta" href="'.base_url('index.php/Compras/Disponibilidad/index/'.$sol->id_solicitud_compra.'/').'" target="_blank"></a>';
               }
             }
            $this->table->add_row($sol->id_solicitud_disponibilidad,$sol->id_solicitud_compra,$sol->fecha,
             $sol->nombre_seccion, $montos,$botones,$aprobar,$denegar, $actualizar, $solicitud_imp);
        }
      } else {
        $msg = array('data' => "Texto no encontrado", 'colspan' => "10");
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
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('Compras/Solicitud_Disponibilidad');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'id_solicitud_compra'=>$this->input->post('solicitud_compra'),
          'fecha_ingreso' => $this->input->post('fechaIngreso'),
          'fecha'=>$this->input->post('fecha'),
          'observaciones'=>$this->input->post('observaciones'),
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
          $this->Solicitud_Disponibilidad_Model->actualizarSolicitudDisponibilidad($this->input->post('id'),$data);
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Compras/Solicitud_Disponibilidad/index/update');
        } else {
          redirect('/Compras/Solicitud_Disponibilidad/index/forbidden');
        }
      }
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Solicitud_Disponibilidad_Model->insertarSolicitudDisponibilidad($data);
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_solicitud_disponibilidad')-1;
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Compras/Solicitud_Disponibilidad/index/new');
      } else {
        redirect('/Compras/Solicitud_Disponibilidad/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }


  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Compras/Solicitud_Disponibilidad');
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
        if ($this->Solicitud_Compra_Model->existeSolicitudCompra($id)){
          redirect('/Compras/solicitud_compra/index/existeSol');
        }
        else {
          $this->Solicitud_Disponibilidad_Model->eliminarSolicitudDisponibilidad($id);
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Compras/Solicitud_Disponibilidad/index/delete');
        }
      } else {
        redirect('/Compras/solicitud_compra/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function Aprobar() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      $estado = $this->Solicitud_Compra_Model->obtenerEstadoSolicitud($id);
      $nivel = $this->Solicitud_Compra_Model->obtenerNivelSolicitud($id);
      if ($estado == 'APROBADA COMPRAS' || $nivel==4){
        $data = array(
            'estado_solicitud_compra' => 'APROBADA DISPONIBILIDAD',
            'nivel_solicitud' => $nivel + 1
        );
      }
      $this->Solicitud_Compra_Model->actualizarSolicitudCompra($id,$data);
      redirect('/Compras/Solicitud_Disponibilidad/index/update');
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function Denegar($id, $memo) {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      //$id = $this->uri->segment(4);
      $nivel = $this->Solicitud_Compra_Model->obtenerNivelSolicitud($id);
      $data = array(
          'estado_solicitud_compra' => 'DENEGADA',
          'nivel_anterior' => $nivel,
          'nivel_solicitud' => 10,
          'memorandum' => $memo
      );
      $this->Solicitud_Compra_Model->actualizarSolicitudCompra($id,$data);
      $this->Notificacion_model->NotificacionSolicitudCompra($id, $USER, 10);
      //redirect('/Compras/Solicitud_Disponibilidad/index/update');
    } else {
      redirect('login/index/error_no_autenticado');
    }

  }

  public function Autocomplete(){
    $mod = $this->User_model->obtenerModulo('Compras/Gestionar_Solicitud');
    $USER = $this->session->userdata('logged_in');
    $nivel=4;
    if($USER){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->Solicitud_Disponibilidad_Model->buscarSolicitudCompraAuto($nivel,$this->input->post('autocomplete'));
        } else {
            $registros = $this->Solicitud_Disponibilidad_Model->obtenerSolicitudCompraAuto($nivel);
        }
      } else {
            $registros = $this->Solicitud_Disponibilidad_Model->obtenerSolicitudCompraAuto($nivel);
      }

      if ($registros == '') {
        echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      }else {
        $i = 1;
        foreach ($registros as $sol) {
          $rast = $this->User_model->obtenerRastreabilidad($sol->id_solicitud_compra, $mod, 'ACTUALIZA');
          if ($rast) {
            echo '<div id="'.$i.'" class="suggest-element" ida="solicitud_compra'.$sol->id_solicitud_compra.'"><a id="solicitud_compra'.
            $sol->id_solicitud_compra.'" data="'.$sol->id_solicitud_compra.'"  data1="'.$sol->id_solicitud_compra.'"
             data2="'.$sol->nombre_seccion.'" data3="'.$sol->id_especifico.'" data4="'.$rast->fecha.'">'
            .$sol->id_solicitud_compra.'</a></div>';
            $i++;
          }
        }
      }
    } else {
      echo '<div class="suggest-element"><a>LO SENTIMOS, NO TIENE PERMISOS</a></div>';
    }
  }

  public function AutocompleteLineaTrabajo(){
    $USER = $this->session->userdata('logged_in');
    $nivel=4;
    if($USER){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->Solicitud_Disponibilidad_Model->buscarLineasTrabajo($this->input->post('autocomplete'));
        } else {
            $registros = $this->Solicitud_Disponibilidad_Model->obtenerLineasTrabajo();
        }
      } else {
            $registros = $this->Solicitud_Disponibilidad_Model->obtenerLineasTrabajo();
      }

      if ($registros == '') {
        echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      }else {
        $i = 1;
        foreach ($registros as $sol) {
          echo '<div id="'.$i.'" class="suggest-element" ida="linea_trabajo'.$sol->id_linea_trabajo.'"><a id="linea_trabajo'.
          $sol->id_linea_trabajo.'" data="'.$sol->id_linea_trabajo.'"  data1="'.$sol->linea_trabajo.'" >'
          .$sol->linea_trabajo.'</a></div>';
          $i++;
        }
      }
    } else {
      echo '<div class="suggest-element"><a>LO SENTIMOS, NO TIENE PERMISOS</a></div>';
    }
  }

  public function AutocompleteDisponibilidad() {
    $USER = $this->session->userdata('logged_in');
    $nivel=4;
    if($USER){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->Solicitud_Disponibilidad_Model->buscarDisponibilidadAuto($this->input->post('autocomplete'));
        } else {
            $registros = $this->Solicitud_Disponibilidad_Model->obtenerDisponibilidadAuto();
        }
      } else {
            $registros = $this->Solicitud_Disponibilidad_Model->obtenerDisponibilidadAuto();
      }

      if ($registros == '') {
        echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      }else {
        $i = 1;
        foreach ($registros as $sol) {
          echo '<div id="'.$i.'" class="suggest-element" ida="sol_compra'.$sol->id_detalle_solicitud_disponibilidad.'"><a id="sol_compra'.
          $sol->id_detalle_solicitud_disponibilidad.'" data="'.$sol->id_detalle_solicitud_disponibilidad.'"
          data1="'.'Req:'.$sol->id_solicitud_compra.' Linea:'.$sol->linea_trabajo.' Monto:$'.$sol->monto_sub_total.'"
          data2="'.$sol->id_solicitud_compra.'" >'
          .'Req:'.$sol->id_solicitud_compra.' Linea:'.$sol->linea_trabajo.' Monto:$'.$sol->monto_sub_total.'</a></div>';
          $i++;
        }
      }
    } else {
      echo '<div class="suggest-element"><a>LO SENTIMOS, NO TIENE PERMISOS</a></div>';
    }
  }

  public function cargar_archivo() {

    $config['upload_path'] = "uploads/";
    $config['allowed_types'] = "pdf|jpg|png|gif|jp2";
    $config['max_size'] = "50000";

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('archivo')) {
        //*** ocurrio un error
        $data['uploadError'] = $this->upload->display_errors();
        echo $this->upload->display_errors();
        return;
    }

    echo "El archivo ha sido subido correctamente.";

    $data = $this->upload->data();

    $this->denegar($this->input->post('disp'), $data['file_name']);

	}
}
?>
