<?php
  $id_cat = array(
      'name' => 'autocomplete',
      'placeholder' => 'Nombre de Categoría',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Categoria/Autocomplete',
      'name_op' => 'categoria',
      'siguiente' => 'guardar',
      'content' => 'suggestions'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  $button = array('class' => 'btn btn-success',);
  $atriLabel = array('class' => 'col-lg-2 control-label');

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form',
  );

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Seleccione una categoría:</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Seleccion_categoria/RecibirCategoria", $atributos);

  echo "<div class='form-group'>";
    echo form_label('Nombre:', 'id_cat', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($id_cat);
      echo form_hidden('categoria');
      echo '<div id="suggestions" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";
  echo form_submit('','Seleccionar', $button);

  echo form_close();
  echo "</div>";
echo "</div>";
?>
