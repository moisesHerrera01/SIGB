<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Proveedores</span>";
    echo "</div>";
    echo "<div class='limit-content'>";

      echo form_open("/Bodega/Proveedores/RecibirDatos", $atributos);

      $cat = array(
          'name' => 'autocomplete',
          'placeholder' => 'Escribe categoría del proveedor',
          'class' => "form-control",
          'autocomplete' => 'off',
      );

      $nom = array(
        'name' => 'nombreProveedor',
        'placeholder' => 'ESCRIBE EL NOMBRE DE PROVEEDOR',
        'class' => "form-control",
        "rows" => "3"
      );

      $con = array(
          'name' => 'nombre_contacto',
          'placeholder' => 'Escribe el nombre del contacto',
          'class' => "form-control"
      );

      $nit = array(
          'name' => 'nit',
          'placeholder' => 'Escribe el NIT',
          'class' => "form-control"
      );

      $email = array(
          'name' => 'correo',
          'placeholder' => 'ESCRIBE EL CORREO ELECTRÓNICO',
          'class' => "form-control",
          "rows" => "3"
      );

      $tel = array(
          'name' => 'telefono',
          'placeholder' => 'Escribe el Teléfono',
          'class' => "form-control"
      );

    /*  $fax = array(
          'name' => 'fax',
          'placeholder' => 'Escribe el Fax',
          'class' => "form-control"
      );*/

      $dir = array(
          'name' => 'direccion',
          'placeholder' => 'Escribe la Dirección',
          'class' => "form-control",
          "rows" => "3"
      );

      $atriLabel = array('class' => 'col-lg-2 control-label');

      $button = array('class' => 'btn btn-success',);

      echo "<div class='form-group'>";
        echo form_label('Categoría:', 'cat', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($cat);
          echo form_hidden('categoria');
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Nombre Proveedor:', 'nom', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_textarea($nom);
        echo "</div>";
      echo "</div>";


      echo "<div class='form-group'>";
        echo form_label('Nombre Contacto:', 'con', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($con);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('NIT:', 'nit', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($nit);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Correo:', 'email', $atriLabel);
        echo "<div class='col-lg-10'>";
          //echo form_input($email);
          echo form_textarea($email);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Teléfono:', 'tel', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($tel);
        echo "</div>";
      echo "</div>";

      /*echo "<div class='form-group'>";
        echo form_label('Fax:', 'fax', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($fax);
        echo "</div>";
      echo "</div>";*/

      echo "<div class='form-group'>";
        echo form_label('Dirección:', 'dir', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_textarea($dir);
        echo "</div>";
      echo "</div>";

      echo form_hidden('id');

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
    'url' => 'index.php/Bodega/Proveedores/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
