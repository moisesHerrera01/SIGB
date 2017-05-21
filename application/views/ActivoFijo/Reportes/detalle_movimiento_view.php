<?php
  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );
    echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());
      $atriLabel = array('class' => 'col-lg-2 control-label');
      echo form_hidden('id_detalle_movimiento');
      echo form_hidden('movimiento', $id_mov);
?>
