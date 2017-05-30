<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

    echo $this->breadcrumb->build_breadcrump($modulo);
  if($estado!='LIQUIDADA'){
    echo "<div class='content-form'>";
      echo "<div class='limit-content-title'>";
        echo "<span class='icono icon-paste icon-title'> Detalle Retiro</span>";
      echo "</div>";
      echo "<div class='limit-content'>";
        echo form_open("/Bodega/detalle_retiro/RecibirDatos", $atributos);

        $det = array(
            'name' => 'autocomplete1',
            'placeholder' => 'Escribe Producto',
            'class' => "form-control",
            'autocomplete' => 'off',
        );

        $cant = array(
            'name' => 'cantidad',
            'type' => 'number',
            'placeholder' => 'Escribe la Cantidad',
            'class' => "form-control"
        );

        $pre = array(
            'name' => 'precio',
            'placeholder' => 'Escribe el Precio',
            'class' => "form-control",
            'disabled'=>'disabled'
        );

        $fun = array(
            'name' => 'autocomplete2',
            'placeholder' => 'Escribe Fuente de Fondos',
            'class' => "form-control autocomplete",
            'autocomplete' => 'off',
            'uri' => 'index.php/Bodega/fuenteFondos/Autocomplete',
            'name_op' => 'fuente',
            'siguiente' => 'button',
            'content' => 'suggestions2'
        );

        $atriLabel = array('class' => 'col-lg-2 control-label');
        $button = array('class' => 'btn btn-success',);

        echo "<div class='form-group'>";
          echo form_label('Producto:', 'det', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($det);
            echo '<div id="suggestions1" class="suggestions"></div>';
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

        // echo "<div class='form-group'>";
        //   echo form_label('Fuente de Fondos:', 'fun', $atriLabel);
        //   echo "<div class='col-lg-10'>";
        //     echo form_input($fun);
        //     echo '<div id="suggestions2" class="suggestions"></div>';
        //   echo "</div>";
        // echo "</div>";

        echo form_hidden('solicitud', $id_solicitud);
        echo form_hidden('producto');
        echo form_hidden('fuente', $fuente);
        echo form_hidden('id');
        echo form_hidden('modulo',$modulo);
        echo form_hidden('id_modulo',$id_modulo);
        echo form_submit('guardar','Guardar', $button);

        echo "<button class='btn btn-warning' type='reset' value='Reset'>Limpiar</button>";
        echo form_close();
      echo "</div>";
    echo "</div>";
  }
?>
