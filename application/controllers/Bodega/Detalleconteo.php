<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DetalleConteo extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Conteofisico_model', 'Bodega/Producto', 'Bodega/DetalleConteoFisico_model',
                        'Bodega/Especifico', 'Bodega/detalleProducto_model'));
  }

  public function index(){

    $conteo = str_replace("_", " ", $this->uri->segment(4));
    if ($conteo == '' || $this->Conteofisico_model->obtenerConteo($conteo) == '') {
      $data['body'] = "ERRROR";
      $this->load->view('base', $data);
    } else {
      $data['title'] = "Detalle Conteo Fisico";
      $data['js'] = "assets/js/validate/dcf.js";

      /*
      * Nombre del conteo se paso por la url
      */
      $msg['nombre_conteo'] = $conteo;

      $men = array('alert' => $this->uri->segment(5),'controller' => 'detalleFactura' );

  		$data['body'] = $this->load->view('mensajes', $men, TRUE) . $this->load->view('Bodega/detalleConteoFisico_view',$msg,TRUE) ."<br><div class='content_table '>".
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Detalle  Conteo Fisico</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div>";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $this->load->view('base', $data);
    }
	}

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */
    $conteo = str_replace("_", " ", $this->uri->segment(4));
    $template = array(
        'table_open' => '<table class="table table-striped table-bordered">'
    );
    $this->table->set_template($template);
    $this->table->set_heading('#','Nombre Producto', 'Cantidad', 'Objeto Especifico', 'Modificar');

    /*
    * Filtro a la BD
    */

    /*Obtiene el numero de registros a mostrar por pagina */
    $num = '5';
    $pagination = '';
    $registros;
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('busca') == "")) {
          $registros = $this->DetalleConteoFisico_model->buscarDetalleConteos($this->input->post('busca'));
      } else {
          $registros = $this->DetalleConteoFisico_model->obtenerDetalleConteosLimit($conteo, $num, $this->uri->segment(5));
          $pagination = paginacion('index.php/Bodega/DetalleConteo/index/'.$conteo, $this->DetalleConteoFisico_model->totalDetalleConteo($conteo),
                        $num, '5');
      }
    } else {
          $registros = $this->DetalleConteoFisico_model->obtenerDetalleConteosLimit($conteo, $num, $this->uri->segment(5));
          $pagination = paginacion('index.php/Bodega/DetalleConteo/index/'.$conteo, $this->DetalleConteoFisico_model->totalDetalleConteo($conteo),
                        $num, '5');
    }

    /*
    * llena la tabla con los datos consultados
    */

    if (!($registros == FALSE)) {
      $i = 1;
      foreach($registros as $conteo) {

        $nombre_producto = $this->Producto->obtenerProducto($conteo->id_producto);
        $nombre_especifico = $this->Especifico->obtenerEspecifico($conteo->id_especifico);
        $onClick = "llenarFormulario('UnidadMedida', ['autocomplete', 'autocomplete3', 'producto', 'nombre','cantidad', 'especifico'],
                    ['$nombre_producto', '$nombre_especifico', '$conteo->id_producto', '$conteo->nombre_conteo','$conteo->cantidad', '$conteo->id_especifico'], false, false,
                    ['autocomplete3', 'uri', 'index.php/Bodega/Especificos/AutocompletePorProducto/$conteo->id_producto'])";

        $this->table->add_row($i, $nombre_producto, $conteo->cantidad, $conteo->id_especifico,
                        '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>');

        $i++;
      }
    } else {
      $msg = array('data' => "No se encontraron resultados", 'colspan' => "5");
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
    $data = array(
        'nombre_conteo' => $this->input->post('nombre'),
        'cantidad' => $this->input->post('cantidad'),
        'id_producto' => $this->input->post('id_producto'),
        'id_especifico' => $this->input->post('id_especifico'),
        'id_detalleproducto' => $this->detalleProducto_model->obtenerDetalleProducto($this->input->post('producto'),
                                $this->input->post('especifico'))
    );

    // if (!($this->DetalleConteoFisico_model->obtenerDetalleConteo($data['id_producto'], $data['nombre_conteo'])) == '') {
    //   $this->DetalleConteoFisico_model->actualizarDetalleConteo($data);
    //   redirect('/Bodega/DetalleConteo/index/'.$data['nombre_conteo'].'/update');
    // }
    $conteo = str_replace(" ", "_", $data['nombre_conteo']);
    if($data['id_detalleproducto']==0){
      redirect('/Bodega/DetalleConteo/index/'.$conteo.'/validar');
    }else{
    $this->DetalleConteoFisico_model->insertarDetalleConteo($data);
    redirect('/Bodega/DetalleConteo/index/'.$conteo.'/new');
  }
}
}
?>
