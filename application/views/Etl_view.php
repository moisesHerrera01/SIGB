<?php
$user = $this->session->userdata('logged_in');
echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 3)));

$button = array('class' => 'btn btn-success',);
$atriLabel = array('class' => 'col-lg-2 control-label');

$atributos = array(
  'class' => 'form-horizontal',
  'role' => 'form',
);
?>
