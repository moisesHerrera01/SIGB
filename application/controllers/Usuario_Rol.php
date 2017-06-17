<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_Rol extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }

    $this->load->helper(array('form', 'paginacion'));
    $this->load->library(array('table'));
    //$this->load->model('Bodega/UnidadMedida');
  }

  public function index(){
    $data['title'] = "Usuarios";
    $data['js'] = "assets/js/validate/usuario_rol.js";
    $msg = array('alert' => $this->uri->segment(3), );

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('usuario_rol_view', '', TRUE) .
                    "<br><div class='content_table'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Usuarios</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    $modulo=$this->User_model->obtenerModulo('Usuario_Rol');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'select')) {
        /*
        * Configuracion de la tabla
        */

        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Nombre', 'Rol','Modificar', 'Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->User_model->buscarUsuariosSICBAF($this->input->post('busca'));
          } else {
              $registros = $this->User_model->obtenerUsuariosSICBAFLimit($num, $this->uri->segment(3));

              $pagination = paginacion('index.php/Usuario_Rol/index/', $this->User_model->obtenerUsuariosSICBAFTotal(),
                            $num, '3');
          }
        } else {
              $registros = $this->User_model->obtenerUsuariosSICBAFLimit($num, $this->uri->segment(3));
              $pagination = paginacion('index.php/Usuario_Rol/index/', $this->User_model->obtenerUsuariosSICBAFTotal(),
                            $num, '3');
        }
        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $us) {
              $onClick = "llenarFormulario('usuarios', ['id', 'usuario','autocomplete1','rol', 'autocomplete2'],
               [$us->id_usuario_rol, '$us->id_usuario' , '$us->nombre_completo','$us->id_rol','$us->nombre_rol'])";
                 $cadena = $us->nombre_rol;
                 $parte = explode(" ",$cadena);
                 $i=0;
                 $nombre_rol= '';
                 foreach ($parte as $part) {
                   if ($i==0) {
                     $part.='(A)';
                   }elseif ($us->nombre_rol=='DIRECTOR ADMINISTRATIVO' && $i==1) {
                       $part.='(A)';
                   }
                   $nombre_rol.=$part.' ';
                   $i++;
                 }
              $this->table->add_row($us->id_usuario_rol,$us->nombre_completo,$nombre_rol,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                              '<a class="icono icon-eliminar" uri='.base_url('index.php/Usuario_Rol/EliminarDato/'.$us->id_usuario_rol).'></a>');
          }
        } else {
          $msg = array('data' => "Texto no encontrado", 'colspan' => "5");
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
        redirect('/Usuario_Rol/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('Usuario_Rol');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'id_usuario' => $this->input->post('usuario'),
          'id_rol' => $this->input->post('rol')
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
          $this->User_model->actualizarUsuarioRol($this->input->post('id'),$data);
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Usuario_Rol/index/update');
        } else {
          redirect('/Usuario_Rol/index/forbidden');
        }
      }
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->User_model->insertarUsuarioRol($data);
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('org_usuario_rol')-1;
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Usuario_Rol/index/new');
      } else {
        redirect('/Usuario_Rol/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Usuario_Rol');
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
      'id_registro' =>$this->uri->segment(3),
    );
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
        $id = $this->uri->segment(3);
        $this->User_model->eliminarUsuarioRol($id);
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Usuario_Rol/index/delete');
      } else {
        redirect('/Usuario_Rol/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function AutocompleteUsuario(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->User_model->buscarUsuarios($this->input->post('autocomplete'));
      } else {
          $registros = $this->User_model->obtenerUsuarios();
      }
    } else {
          $registros = $this->User_model->obtenerUsuarios();
    }

    if ($registros == '') {
      echo '';
    }else {
      $i = 1;
      foreach ($registros as $usuario) {
        echo '<div id="'.$i.'" class="suggest-element" ida="usuario'.$usuario->id_usuario.'"><a id="usuario'.
        $usuario->id_usuario.'" data="'.$usuario->id_usuario.'"  data1="'.$usuario->nombre_completo.'" >'
        .$usuario->nombre_completo.'</a></div>';
        $i++;
      }
    }
  }
/*Se agrega (A) al nombre del rol para describir masculino/femenino se hace con una funcion de php para que se muestre
de esa manera y no modificar los registros de rol en la base de datos*/
  public function AutocompleteRol(){
    $registros = '';
    $nombre_rol= '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->User_model->buscarRoles($this->input->post('autocomplete'));
      } else {
          $registros = $this->User_model->obtenerRoles();
      }
    } else {
          $registros = $this->User_model->obtenerRoles();
    }

    if ($registros == '') {
      echo '';
    }else {
      $j=1;
      foreach ($registros as $rol) {
        $cadena = $rol->nombre_rol;
        $parte = explode(" ",$cadena);
        $i=0;
        $nombre_rol= '';
        foreach ($parte as $part) {
          if ($i==0) {
            $part.='(A)';
          }elseif ($rol->nombre_rol=='DIRECTOR ADMINISTRATIVO' && $i==1) {
              $part.='(A)';
          }
          $nombre_rol.=$part.' ';
          $i++;
        }
        echo '<div id="'.$j.'" class="suggest-element" ida="rol'.$rol->id_rol.'"><a id="rol'.
        $rol->id_rol.'" data="'.$rol->id_rol.'"  data1="'.$nombre_rol.'" >'
        .$nombre_rol.'</a></div>';
        $j++;
      }
    }
  }
}
?>
