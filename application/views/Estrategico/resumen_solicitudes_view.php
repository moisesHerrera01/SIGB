<?php
date_default_timezone_set('America/El_Salvador');
$anyo=20;
$fecha_actual=date("d-m-".$anyo."y");
 echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 3)));

$inicio= array(
    'name' => 'minFecha',
    'placeholder' => 'Escribe la fecha de inicio',
    'class' => "form-control",
    'type'=>'date'
);

$fin= array(
    'name' => 'maxFecha',
    'placeholder' => 'Escribe la fecha final',
    'class' => "form-control",
    'type'=>'date'
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
      echo "Reporte resumen de solicitudes de bodega.";
    echo "</div>";
    echo
    "<div class='title-header'>
      <ul>
        <li>Fecha emisión: ".$fecha_actual."</li>
        <li>Nombre la compañia: MTPS</li>
        <li>N° pagina: 1/1</li>
        <li>Usuario: ".$user['nombre_completo']."</li>
      </ul>
    </div>";
  echo "</div>";
  echo "<div class='limit-content'>";

    echo form_open("/Estrategico/Resumen_solicitudes/Recibirfechas", $atributos);

      echo "<div class='form-group'>";
        echo form_label('Fecha Inicial:', 'inicio', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($inicio);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Fecha Final:', 'fin', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($fin);
        echo "</div>";
      echo "</div>";

      echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>
