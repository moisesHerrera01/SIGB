<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('paginacion')) {

  function paginacion($url, $count, $num, $segmento) {
      $PG =& get_instance();
      $PG->load->library('pagination');
      $config['base_url'] = base_url() . $url;

      /*Obtiene el total de registros a paginar */
      $config['total_rows'] = $count;

      /*Obtiene el numero de registros a mostrar por pagina */
      $config['per_page'] = $num;

      /*Indica que segmento de la URL tiene la paginación, por default es 3*/
      $config['uri_segment'] = $segmento;

      /*Se personaliza la paginación para que se adapte a bootstrap*/
      $config['full_tag_open'] = '<div class="paginacion"><ul class="pagination pagination-sm">';
      $config['full_tag_close'] = '</ul><div>';
      $config['num_tag_open'] = '<li>';
      $config['num_tag_close'] = '</li>';
      $config['cur_tag_open'] = '<li class="active"><span>';
      $config['cur_tag_close'] = '</span></li>';
      $config['prev_tag_open'] = '<li>';
      $config['prev_tag_close'] = '</li>';
      $config['next_tag_open'] = '<li>';
      $config['next_tag_close'] = '</li>';
      $config['first_link'] = 'Primero';
      $config['prev_link'] = 'Anterior';
      $config['last_link'] = 'Ultimo';
      $config['next_link'] = 'Siguiente';
      $config['first_tag_open'] = '<li>';
      $config['first_tag_close'] = '</li>';
      $config['last_tag_open'] = '<li>';
      $config['last_tag_close'] = '</li>';

      /* Se inicializa la paginacion*/
      $PG->pagination->initialize($config);

      /* Retorna los links de la paginacion */
      return $PG->pagination->create_links();
  }

}

?>
