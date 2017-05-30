<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Detalle Conteo Fisico</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Bodega/DetalleConteo/RecibirDatos", $atributos);

      $producto = array(
          'name' => 'autocomplete',
          'placeholder' => 'Escribe Producto',
          'class' => "form-control",
          'autocomplete' => 'off'
      );

      $cant = array(
          'name' => 'cantidad',
          'placeholder' => 'Escribe cantidad',
          'type' => 'number',
          'class' => "form-control"
      );

      $oe = array(
          'name' => 'autocomplete3',
          'placeholder' => 'Escribe Nombre Especifico',
          'class' => "form-control",
          'autocomplete' => 'off',
      );

      $atriLabel = array('class' => 'col-lg-2 control-label');

      $button = array('class' => 'btn btn-success',);

      echo "<div class='form-group'>";
        echo form_label('Producto:', 'nom', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($producto);
          echo form_hidden('producto');
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Cantidad:', 'abr', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($cant);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Especifico:', 'abr', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($oe);
          echo form_hidden('especifico');
          echo '<div id="suggestions2" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo form_hidden('nombre', $nombre_conteo);

      echo form_submit('','Guardar', $button);

      echo "<button class='btn btn-warning' type='reset' value='Reset'>Limpiar</button>";

      echo form_close();
    echo "</div>";
  echo "</div>";

  $buscar = array(
    'name' => 'buscar',
    'type' => 'search',
    'placeholder' => 'Buscar',
    'class' => 'form-control',
    'autocomplete' => 'off',
    'id' => 'buscar',
    'url' => 'index.php/Bodega/DetalleConteo/mostrarTabla/'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
