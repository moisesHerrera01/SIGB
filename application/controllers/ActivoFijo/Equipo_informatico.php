<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Equipo_informatico extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Equipo_informatico_Model','ActivoFijo/Categoria_model',
    'ActivoFijo/Subcategoria_Model','ActivoFijo/Procesador_model','ActivoFijo/Disco_Duro_Model',
    'ActivoFijo/Memoria_Model','ActivoFijo/Sistema_operativo_model','ActivoFijo/Office_model'));
  }

  public function index(){
    $id = $this->uri->segment(5);
    $id_equipo_informatico = $this->uri->segment(4);
    $nombre_subcategoria = $this->Equipo_informatico_Model->obtenerCategoria($id)->nombre_subcategoria;
    $data['title'] = "Equipo Informático";
    //$data['js'] = "assets/js/validate/subcategoria.js";
    $msg = array('alert' => $this->uri->segment(6),'subcategoria' => $id,
    'nombre_subcategoria'=>$nombre_subcategoria,'id_equipo_informatico'=>$id_equipo_informatico);
		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/equipo_informatico_view',$msg, TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Historial de registros</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */

    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Equipo_informatico');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'select')) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Categoria AF','Categoria UDT', 'Subcategoria','Descripción AF',
        'Editar','Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Equipo_informatico_Model->buscarEquipoInformatico($this->input->post('busca'),$this->uri->segment(5),$this->uri->segment(4));
          } else {
              $registros = $this->Equipo_informatico_Model->obtenerEquipoInformaticoLimit($num, $this->uri->segment(6),$this->uri->segment(5),$this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Equipo_informatico/index/'.
              $this->uri->segment(5), $this->Equipo_informatico_Model->totalEquipoInformatico($this->uri->segment(5),$this->uri->segment(4))->total,
              $num, '6');
          }
        } else {
              $registros = $this->Equipo_informatico_Model->obtenerEquipoInformaticoLimit($num,$this->uri->segment(6),
              $this->uri->segment(5),$this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Equipo_informatico/index/'.$this->uri->segment(5),
              $this->Equipo_informatico_Model->totalEquipoInformatico($this->uri->segment(5),$this->uri->segment(4))->total,
                            $num, '6');
        }
        /*
        * llena la tabla con los datos consultados
        */
        $i=1;
        if (!($registros == FALSE)) {
          $id_subcategoria=$this->uri->segment(5);
          foreach($registros as $sub) {
              $datos = $this->Equipo_informatico_Model->obtenerDatos($sub->id_dato_comun);
              $nombre_categoria = $this->Equipo_informatico_Model->obtenerCategoria($this->uri->segment(5));
              switch ($nombre_categoria->nombre_subcategoria) {
                case 'DISCO DURO':
                  $onClick = "llenarFormulario('equipo', ['id','hdd',
                  'autocomplete3','v_hdd'], [$sub->id_detalle_equipo_informatico,'$sub->id_disco_duro',
                  '$sub->capacidad','$sub->velocidad_disco_duro'])";
                  break;
                case 'PROCESADOR':
                  $onClick = "llenarFormulario('equipo', ['id','procesador',
                  'autocomplete2','v_procesador'], [$sub->id_detalle_equipo_informatico,
                  '$sub->id_procesador','$sub->nombre_procesador','$sub->velocidad_procesador'])";
                  break;
                case 'MEMORIA':
                  $onClick = "llenarFormulario('equipo', ['id','memoria',
                  'autocomplete4','v_memoria'], [$sub->id_detalle_equipo_informatico,
                  '$sub->id_memoria','$sub->tipo_memoria','$sub->velocidad_memoria'])";
                  break;
                case 'SISTEMA OPERATIVO':
                  $onClick = "llenarFormulario('equipo', ['id','bien','autocomplete','so',
                  'autocomplete5','clave_so'], [$sub->id_detalle_equipo_informatico,
                  '$sub->id_sistema_operativo','$sub->version_sistema_operativo',
                  '$sub->clave_sistema_operativo'])";
                  break;
                case 'OFFICE':
                  $onClick = "llenarFormulario('equipo', ['id','office',
                  'autocomplete6','clave_office'], [$sub->id_detalle_equipo_informatico,
                  '$sub->id_office','$sub->version_office',
                  '$sub->clave_office'])";
                  break;
                  case 'IP':
                    $onClick = "llenarFormulario('equipo', ['id','ip'],
                    [$sub->id_detalle_equipo_informatico,'$sub->direccion_ip'])";
                    break;
                  case 'PUNTO DE RED':
                    $onClick = "llenarFormulario('equipo', ['id','punto'],
                    [$sub->id_detalle_equipo_informatico,'$sub->numero_de_punto'])";
                    break;
                default:
                  break;
              }
              $this->table->add_row($i,$datos->nombre_categoria,$sub->nombre_categoria,$sub->nombre_subcategoria,
              $sub->descripcion, '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
              '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Equipo_informatico/EliminarDato/'.$sub->id_equipo_informatico.'/'.$id_subcategoria.'/'.$sub->id_detalle_equipo_informatico).'></a>');
              $i++;
          }
        } else {
          $msg = array('data' => "Texto no encontrado", 'colspan' => "7");
          $this->table->add_row($msg);
        }

        /*
        * vuelve a verificar para mostrar los datos
        */
        if ($this->input->is_ajax_request()) {
          echo $this->table->generate() . $pagination;
        } else {
          return $this->table->generate() . $pagination;
        }
      } else {
        redirect('/ActivoFijo/Equipo_informatico/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Equipo_informatico');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'id_equipo_informatico' => $this->input->post('id_equipo_informatico'),
          'id_subcategoria' => $this->input->post('subcategoria'),
          'id_procesador' => $this->input->post('procesador'),
          'id_disco_duro' => $this->input->post('hdd'),
          'id_memoria' => $this->input->post('memoria'),
          'id_sistema_operativo' => $this->input->post('so'),
          'id_office' => $this->input->post('office'),
          'velocidad_procesador' => $this->input->post('v_procesador'),
          'velocidad_disco_duro' => $this->input->post('v_hdd'),
          'velocidad_memoria' => $this->input->post('v_memoria'),
          'clave_sistema_operativo' => $this->input->post('clave_so'),
          'clave_office' => $this->input->post('clave_office'),
          'direccion_ip' => $this->input->post('ip'),
          'numero_de_punto' => $this->input->post('punto')
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Equipo_informatico_Model->actualizarEquipoInformatico($this->input->post('id'),$data);
          redirect('/ActivoFijo/Equipo_informatico/index/'.$data['id_equipo_informatico'].'/'.$data['id_subcategoria'].'/update');
        } else {
          redirect('/ActivoFijo/Equipo_informatico/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Equipo_informatico_Model->insertarEquipoInformatico($data);
        redirect('/ActivoFijo/Equipo_informatico/index/'.$data['id_equipo_informatico'].'/'.$data['id_subcategoria'].'/new');
      } else {
        redirect('/ActivoFijo/Equipo_informatico/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Equipo_informatico');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
        $id_detalle_equipo_informatico = $this->uri->segment(6);
        $id_equipo_informatico=$this->uri->segment(4);
        $id_subcategoria=$this->uri->segment(5);
        $this->Equipo_informatico_Model->eliminarEquipoInformatico($id_detalle_equipo_informatico);
        redirect('/ActivoFijo/Equipo_informatico/index/'.$id_equipo_informatico.'/'.$id_subcategoria.'/delete');
      } else {
        redirect('/ActivoFijo/Equipo_informatico/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function AutocompleteProcesador(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Procesador_model->buscarProcesadores($this->input->post('autocomplete'));
      } else {
          $registros = $this->Procesador_model->obtenerProcesadores();
      }
    } else {
          $registros = $this->Procesador_model->obtenerProcesadores();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    } else {
      $i = 1;
      foreach ($registros as $cat) {
        echo '<div id="'.$i.'" class="suggest-element" ida="procesador'.$cat->id_procesador.'"><a id="procesador'.
        $cat->id_procesador.'" data="'.$cat->id_procesador.'"  data1="'.$cat->nombre_procesador.'" >'
        .$cat->id_procesador.' - '.$cat->nombre_procesador.'</a></div>';
        $i++;
      }
    }
  }

  public function AutocompleteDiscoDuro(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Disco_Duro_Model->buscarDiscos($this->input->post('autocomplete'));
      } else {
          $registros = $this->Disco_Duro_Model->obtenerDiscosDuros();
      }
    } else {
          $registros = $this->Disco_Duro_Model->obtenerDiscosDuros();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    } else {
      $i = 1;
      foreach ($registros as $cat) {
        echo '<div id="'.$i.'" class="suggest-element" ida="disco'.$cat->id_disco_duro.'"><a id="disco'.
        $cat->id_disco_duro.'" data="'.$cat->id_disco_duro.'"  data1="'.$cat->capacidad.'" >'
        .$cat->capacidad.'</a></div>';
        $i++;
      }
    }
  }

  public function AutocompleteMemoria(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Memoria_Model->buscarMemorias($this->input->post('autocomplete'));
      } else {
          $registros = $this->Memoria_Model->obtenerMemorias();
      }
    } else {
          $registros = $this->Memoria_Model->obtenerMemorias();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    } else {
      $i = 1;
      foreach ($registros as $cat) {
        echo '<div id="'.$i.'" class="suggest-element" ida="memoria'.$cat->id_memoria.'"><a id="memoria'.
        $cat->id_memoria.'" data="'.$cat->id_memoria.'"  data1="'.$cat->tipo_memoria.'" >'
        .$cat->tipo_memoria.'</a></div>';
      }
    }
  }

  public function AutocompleteSistemaOperativo(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Sistema_operativo_model->buscarSistemas_operativos($this->input->post('autocomplete'));
      } else {
          $registros = $this->Sistema_operativo_model->obtenerSistemas_operativos();
      }
    } else {
          $registros = $this->Sistema_operativo_model->obtenerSistemas_operativos();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    } else {
      $i = 1;
      foreach ($registros as $cat) {
        echo '<div id="'.$i.'" class="suggest-element" ida="so'.$cat->id_sistema_operativo.'"><a id="so'.
        $cat->id_sistema_operativo.'" data="'.$cat->id_sistema_operativo.'"  data1="'.$cat->version_sistema_operativo.'" >'
        .$cat->version_sistema_operativo.'</a></div>';
        $i++;
      }
    }
  }

  public function AutocompleteOffice(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Office_model->buscarOffices($this->input->post('autocomplete'));
      } else {
          $registros = $this->Office_model->obtenerOffices();
      }
    } else {
          $registros = $this->Office_model->obtenerOffices();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    } else {
      $i = 1;
      foreach ($registros as $cat) {
        echo '<div id="'.$i.'" class="suggest-element" ida="office'.$cat->id_office.'"><a id="office'.
        $cat->id_office.'" data="'.$cat->id_office.'"  data1="'.$cat->version_office.'" >'
        .$cat->version_office.'</a></div>';
        $i++;
      }
    }
  }
}
?>
