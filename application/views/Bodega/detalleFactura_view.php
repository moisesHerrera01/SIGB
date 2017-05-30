<?php
  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());
  if($estado == 'INGRESADA'){

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Detalle Factura</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Bodega/Detallefactura/RecibirDatos", $atributos);

      $det = array(
          'name' => 'autocomplete',
          'placeholder' => 'Escribe Producto',
          'class' => "form-control",
          'autocomplete' => 'off',
          'uri' => 'index.php/Bodega/Detallefactura/AutocompleteEspecificoProducto/'.$id_factura,
      );

      $cant = array(
          'name' => 'cantidad',
          'type' => 'number',
          'placeholder' => 'Escribe la Cantidad',
          'class' => "form-control"
      );

      $pre = array(
          'name' => 'precio',
          'type' => 'number',
          'placeholder' => 'Escribe el Precio',
          'class' => "form-control"
      );

      $atriLabel = array('class' => 'col-lg-2 control-label');
      $button = array('class' => 'btn btn-success');

      echo "<div class='form-group'>";
        echo form_label('Producto:', 'det', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($det);
          echo form_hidden('producto');
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Cantidad:', 'cant', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($cant);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Precio:', 'pre', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($pre);
        echo "</div>";
      echo "</div>";

      echo form_hidden('factura', $id_factura);
      echo form_hidden('id_detalle_factura');
      echo form_submit('guardar','Guardar', $button);

      echo "<button class='btn btn-warning' type='reset' value='Reset'>Limpiar</button>";
      echo form_close();
    echo "</div>";
  echo "</div>";
}
?>
