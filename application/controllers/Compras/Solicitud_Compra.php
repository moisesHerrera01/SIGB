<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitud_compra extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Compras/Solicitud_Compra_Model', 'Notificacion_model'));
  }

  public function index(){
    if($this->session->userdata('logged_in')){
      $data['title'] = "Solicitudes Compra";
      $data['js'] = "assets/js/validate/sc.js";
      $USER = $this->session->userdata('logged_in');
      $solicitante=$USER['nombre_empleado'];
      $id_seccion=$USER['id_seccion'];
      $cargo=$USER['cargo_funcional'];
      $linea=$USER['linea_trabajo'];
      $numero=$this->Solicitud_Compra_Model->obtenerIdSolicitudCompra();
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $fecha_actual=date($anyo."y-m-d");
      $msg = array('alert' => $this->uri->segment(4),'fecha'=>$fecha_actual,'controller'=>'solicitud_compra',
      'solicitante'=>$solicitante,'cargo'=>$cargo,'linea'=>$linea, 'numero'=>$numero);

  		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Compras/solicitud_compra_view',$msg,TRUE) .
                      "<br><div class='content_table '>" .
                      "<div class='limit-content-title'><span class='icono icon-table icon-title'> Historial de solicitudes</span></div>".
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
      if($USER['rol']=='ADMINISTRADOR SICBAF'){
        $seccion=0;
      } else {
        $seccion=$USER['id_seccion'];
      }
        $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('Id', 'Fecha', 'Estado', 'Enviar','Detalle','Imprimir Solicitud','Eliminar','Editar','Estatus');

      /*
      * Filtro a la BD
      */
      /*Obtiene el numero de registros a mostrar por pagina */
      $num = '10';
      $pagination = '';
      $registros;
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('busca') == "")) {
            $registros = $this->Solicitud_Compra_Model->buscarSolicitudesCompraUser($seccion, $this->input->post('busca'));
        } else {
            $registros = $this->Solicitud_Compra_Model->obtenerSolicitudesCompraUserLimit($seccion, $num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Compras/Solicitud_Compra/index/', $this->Solicitud_Compra_Model->totalSolicitudesCompra($seccion)->cantidad,
                          $num, '4');
        }
      } else {
            $registros = $this->Solicitud_Compra_Model->obtenerSolicitudesCompraUserLimit($seccion ,$num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Compras/Solicitud_Compra/index/', $this->Solicitud_Compra_Model->totalSolicitudesCompra($seccion)->cantidad,
                          $num, '4');
      }
      /*
      * llena la tabla con los datos consultados
      */

      if (!($registros == FALSE)) {
        foreach($registros as $sol) {
            $soli=$this->Solicitud_Compra_Model->obtenerEmpleadoDatosId($sol->solicitante);
            $auto=$this->Solicitud_Compra_Model->obtenerEmpleadoDatosId($sol->autorizante);
            $adm=$this->Solicitud_Compra_Model->obtenerEmpleadoDatosId($sol->propuesta_administrador);
            //var_dump($solicitante);
            $id_rol = $USER['id_rol'];
            $modulo=$this->User_model->obtenerModulo('Compras/Solicitud_Compra');
            $solicitante=$USER['nombre_empleado'];

            $onClick = "llenarFormulario('solicitud', ['id','fecha','autocomplete1','cargo','linea', 'sol','autocomplete2','cargo_auto','dep_auto','auto','autocomplete3',
            'cargo_admin','dep_admin','admin','valor','forma','lugar'],
            [$sol->id_solicitud_compra,'$sol->fecha_solicitud_compra','$soli->nombre_empleado','$soli->cargo_funcional','$soli->seccion_padre','$soli->id_empleado',
            '$auto->nombre_empleado','$auto->cargo_funcional','$auto->seccion_padre','$auto->id_empleado','$adm->nombre_empleado','$adm->cargo_funcional','$adm->seccion_padre',
            '$adm->id_empleado','$sol->precio_estimado','$sol->forma_entrega','$sol->lugar_entrega'],
             false, false, false, ['justificacion','otras'], ['$sol->justificacion','$sol->otras_condiciones'])";

            $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'
            .$sol->id_solicitud_compra.'/'.$modulo.'/'.$id_rol.'/').'"></a>';
            $estatus='<a class="icono icon-stats-dots" href="'.base_url('index.php/Compras/Estado_Solicitud/index/'
            .$sol->id_solicitud_compra.'/').'"></a>';
            if($sol->estado_solicitud_compra=='INGRESADA'){
                $solicitud_imp = '<a class="icono icon-acta" href="'.base_url('index.php/Compras/Solicitud/index/'.$sol->id_solicitud_compra.'/').'" target="_blank"></a>';
                $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Compras/Solicitud_Compra/EliminarDato/'.$sol->id_solicitud_compra).'></a>';
                $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                $enviar = '<a class="icono icon-rocket" href='.base_url('index.php/Compras/Solicitud_Compra/EnviarDato/'.$sol->id_solicitud_compra).'></a>';
            }else{
              $solicitud_imp = '<a class="icono icon-acta" href="'.base_url('index.php/Compras/Solicitud/index/'.$sol->id_solicitud_compra.'/').'" target="_blank"></a>';
              $eliminar='<a class="icono icon-denegar"></a>';
              $actualizar='<a class="icono icon-denegar"></a>';
              $enviar='<a class="icono icon-denegar"></a>';
            }
            $this->table->add_row($sol->id_solicitud_compra, $sol->fecha_solicitud_compra,
             $sol->estado_solicitud_compra, $enviar,$botones,$solicitud_imp,$eliminar,$actualizar,$estatus);

        }
      } else {
        $msg = array('data' => "Texto no encontrado", 'colspan' => "9");
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

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('Compras/Solicitud_Compra');
    $USER = $this->session->userdata('logged_in');
    $numero=$this->Solicitud_Compra_Model->obtenerIdSolicitudCompra();
    $secciones=$this->Solicitud_Compra_Model->obtenerEmpleadoSecciones($this->input->post('sol'));
    if($USER){
      $data = array(
          'solicitante'=>$this->input->post('sol'),
          'autorizante'=>$this->input->post('auto'),
          'id_seccion'=>$secciones->id_seccion,
          'propuesta_administrador'=>$this->input->post('admin'),
          'fecha_solicitud_compra' => $this->input->post('fecha'),
          'numero_solicitud_compra' => $numero,
          'justificacion' => $this->input->post('justificacion'),
          'precio_estimado' => $this->input->post('valor'),
          'forma_entrega'=>$this->input->post('forma'),
          'lugar_entrega'=>$this->input->post('lugar'),
          'otras_condiciones'=>$this->input->post('otras'),
          'comentario'=>$this->input->post('comentario')
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
          $this->Solicitud_Compra_Model->actualizarSolicitudCompra($this->input->post('id'),$data);
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Compras/Solicitud_Compra/index/update');
        } else {
          redirect('/Compras/Solicitud_Compra/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $archivo=$this->cargar_archivo();
        foreach ($archivo as $ar) {
          $data['documento_especificaciones']=$ar['file_name'];
        }
        $this->Solicitud_Compra_Model->insertarSolicitudCompra($data);
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_solicitud_compra')-1;
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Compras/Detalle_Solicitud_Compra/index/'.$numero.'/'.$modulo.'/nueva');
      } else {
        redirect('/Compras/Solicitud_Compra/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }


  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Compras/Solicitud_Compra');
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
          redirect('/Compras/Solicitud_Compra/index/existeSol');
        }
        else {
          $this->Solicitud_Compra_Model->eliminarSolicitudCompra($id);
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Compras/Solicitud_Compra/index/delete');
        }
      } else {
        redirect('/Compras/Solicitud_Compra/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function EnviarDato() {
        $id = $this->uri->segment(4);
        if ($this->Solicitud_Compra_Model->existeSolicitudCompra($id)){
          $this->Solicitud_Compra_Model->enviarSolicitudCompra($id);
          $this->Notificacion_model->NotificacionSolicitudCompra($id, $this->session->userdata('logged_in'), 1);
          redirect('/Compras/Solicitud_Compra/index/send');
        }else {
          redirect('/Compras/Solicitud_Compra/index/noexisteSol');
        }
  }

  public function Autocomplete(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->Solicitud_Compra_Model->buscarSolicitudes($this->input->post('autocomplete'));
        } else {
            $registros = $this->Solicitud_Compra_Model->obtenerSolicitudesCompra();
        }
      } else {
            $registros = $this->Solicitud_Compra_Model->obtenerSolicitudesCompra();
      }

      if ($registros == '') {
        echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      }else {
        $i = 1;
        foreach ($registros as $sol) {
          echo '<div id="'.$i.'" class="suggest-element" ida="solicitud'.$sol->id_solicitud_compra.'"><a id="solicitud'.
          $sol->id_solicitud_compra.'" data="'.$sol->id_solicitud_compra.'"  data1="'.$sol->numero_solicitud_compra.'" >'
          .$sol->numero_solicitud_compra.'</a></div>';
          $i++;
        }
      }
    } else {
      redirect('login');
    }
  }

  public function cargar_archivo() {

    $config['upload_path'] = "uploads/";
    $config['allowed_types'] = 'gif|jpg|png|pdf|docx|doc|xls|xlsx';
    $config['max_size'] = "50000";

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('archivo')) {
        //*** ocurrio un error
        $data['uploadError'] = $this->upload->display_errors();
        echo $this->upload->display_errors();
        return;
    }else{
      return $data = array('upload_data' => $this->upload->data());
    }
  }

  public function ConsultarSolicitudJson() {
    $id_sol = $this->input->post('id');
    $sol = $this->Solicitud_Compra_Model->obtenerSolicitudCompleta($id_sol);

    if (!($sol == FALSE)) {
      $data = array();

      $data[] = array('jefe' => $sol->comentario_jefe, 'autorizante' => $sol->comentario_autorizante, 'compras' => $sol->comentario_compras);
      print json_encode($data);
    }
  }
  public function AutocompleteSolicitante(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->Solicitud_Compra_Model->buscarSolicitantes($this->input->post('autocomplete'));
        } else {
            $registros = $this->Solicitud_Compra_Model->obtenerSolicitantes();
        }
      } else {
            $registros = $this->Solicitud_Compra_Model->obtenerSolicitantes();
      }

      if ($registros == '') {
        echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      }else {
        $i = 1;
        foreach ($registros as $sol) {
          echo '<div id="'.$i.'" class="suggest-element" ida="sol'.$sol->id_empleado.'"><a id="sol'.
          $sol->id_empleado.'" data="'.$sol->id_empleado.'"  data1="'.$sol->nombre_empleado.'"
          data2="'.$sol->cargo_funcional.'" data3="'.$sol->seccion_padre.'">'
          .$sol->nombre_empleado.'</a></div>';
          $i++;
        }
      }
    } else {
      redirect('login');
    }
  }

  public function AutocompleteEmpleadoDatos(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->Solicitud_Compra_Model->buscarEmpleadoDatos($this->input->post('autocomplete'));
        } else {
            $registros = $this->Solicitud_Compra_Model->obtenerEmpleadoDatos();
        }
      } else {
            $registros = $this->Solicitud_Compra_Model->obtenerEmpleadoDatos();
      }

      if ($registros == '') {
        echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      }else {
        $i = 1;
        foreach ($registros as $sol) {
          echo '<div id="'.$i.'" class="suggest-element" ida="auto'.$sol->id_empleado.'"><a id="auto'.
          $sol->id_empleado.'" data="'.$sol->id_empleado.'"  data1="'.$sol->nombre_empleado.'"
          data2="'.$sol->cargo_funcional.'" data3="'.$sol->seccion_padre.'">'
          .$sol->nombre_empleado.'</a></div>';
          $i++;
        }
      }
    } else {
      redirect('login');
    }
  }
}
?>
