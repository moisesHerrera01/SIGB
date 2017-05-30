<?php
  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Detalle Producto</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Bodega/Detalleproductos/RecibirDatos", $atributos);

      $id_espe = array(
          'name' => 'id_especifico',
          'placeholder' => 'Escribe Número de Especifico',
          'class' => "form-control",
          'value' => $nombre,
          'readonly'=>"readonly"
      );

      // $id_prod = array(
      //     'name' => 'id_producto',
      //     'placeholder' => 'Escribe numero de producto',
      //     'class' => "form-control"
      // );

      $id_prod = array(
          'name' => 'autocomplete',
          'placeholder' => 'Escribe producto',
          'class' => "form-control",
          'autocomplete' => 'off'
      );

      $atriLabel = array('class' => 'col-lg-2 control-label');

      $button = array('class' => 'btn btn-success',);

      echo "<div class='form-group'>";
        echo form_label('Especifico:', 'id_espe', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($id_espe);
          echo form_hidden('id_espec', $id_espec);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Producto:', 'id_prod', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($id_prod);
          echo form_hidden('id_producto');
          echo form_hidden('id_pro');
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";


      echo form_hidden('id');

      echo form_submit('guardar','Guardar', $button);

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
    'url' => 'index.php/Bodega/Detalleproductos/mostrarTabla/'.$id_espec
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
