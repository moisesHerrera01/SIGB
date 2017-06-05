<?php

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 3)));

$conteo = array(
    'name' => 'conteo',
    'placeholder' => 'Escribe Conteo',
    'class' => "form-control",
    'autocomplete' => 'off',
);

$button = array('class' => 'btn btn-success',);
$atriLabel = array('class' => 'col-lg-2 control-label');

$atributos = array(
  'class' => 'form-horizontal',
  'role' => 'form',
);

echo "<div class='content-form'>";
  echo "<div class='limit-content-title'>";
    echo "<div class='title-reporte'>";
      echo "Resumen conteo fisico.";
    echo "</div>";
    echo
    "<div class='title-header'>
      <ul>
        <li>Fecha emisión: ".date('d/m/Y')."</li>
        <li>Nombre la compañia: MTPS</li>
        <li>N° pagina: 1/1</li>
        <li>Nombre pantalla:</li>
        <li>Usuario: ".$user['nombre_completo']."</li>
      </ul>
    </div>";
  echo "</div>";
  echo "<div class='limit-content'>";
    echo form_open("/Estrategico/Resumen_conteo/RecibirConteo", $atributos);
      echo "<div class='form-group'>";
        echo form_label('Conteo Fisico:', 'cont', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($conteo);
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";
      echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>
