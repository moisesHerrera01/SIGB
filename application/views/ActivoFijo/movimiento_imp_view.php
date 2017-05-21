<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?= $title?></title>
    <link rel="shortcut icon" href="<?= base_url("assets/image/logo.jpg")?>" />
    <link href=<?= base_url("vendor/twbs/bootstrap/dist/css/bootstrap.min.css")?> rel="stylesheet" media="screen">
    <style>

      ul {
        width: 95%;
      }

      li{
        list-style: none;
        border: 1px solid;
        border-left: 2px solid;
        border-right: 2px solid;
      }

      li:first-child {
        border-top: 2px solid;
      }

      li:last-child {
        border-bottom: 2px solid;
      }

      ul:nth-child(2) > li {
        padding-bottom: 0.5em;
        padding-top: 0.5em;
      }

      table {
        text-align: center;
        margin: 0 auto;
        width: 100%;
      }

      .content {
        width: 86%;
        margin: 2em auto;
      }

      .content-table {
        margin-top: 20px;
        margin-bottom: 20px;
      }

      .titulo {
        font-weight: bold;
      }

      .autoriza > li {
        height: 3em;
      }

      .marcar {
        position: relative;
        bottom: -1em;
        float:right;
        font-weight: bold;
      }

      .content-column {
        width: 100%;
        height: 10em;
      }

      .column {
        width: 100%;
        padding: 2em;
      }

      .column-a {
        width: 25%;
        float: left;
      }

      .column-b {
        width: 50%;
        text-align: center;
        float: right;
      }

      .column > p {
        text-transform: uppercase;
      }

    </style>
  </head>
  <body onload="window.print()">
    <div class="content">
      <div class="content-column">
        <div class="column column-a"><img src=<?= base_url("assets/image/icono.jpg")?> width="200px" /></div>
        <div class="column column-b">
          <p>ministerio de trabajo y previsión social</p>
          <p>unidad de activo fijo</p>
          <p>control y registro de bienes</p>
        </div>
      </div>
      <div class="content-table">
        <ul>
          <li><span class="titulo">Id de movimiento: </span><?= $datos['id_movimiento']?></li>
          <li><span class="titulo">Almacen, Unidad, Sección, Oficina que Entrega: </span><?= $datos['nombre_almacen_entrega']?> - <?= $datos['nombre_seccion_entrega']?> - <?= $datos['nombre_oficina_entrega']?></li>
          <li><span class="titulo">Almacen, Unidad, Sección, Oficina que Recibe: </span><?= $datos['nombre_almacen_recibe']?> - <?= $datos['nombre_seccion_recibe']?> - <?= $datos['nombre_oficina_recibe']?></li>
          <li><span class="titulo">Tipo de Movimiento: </span><?= $datos['nombre_movimiento']?></td>
          <li><span class="titulo">Elaborado por: </span><?= $datos['elaborado']?></li>
          <li><span class="titulo">Asignado a: </span><?= $datos['recibido_por']?></li>
        </ul>
      </div>
      <div class="content-table">
        <ul class="autoriza">
          <li><span class="titulo">Usuario externo: </span><?= $datos['usuario_externo']?><span class="marcar">Firma y Sello</span></li>
          <li><span class="titulo">Entregado por: </span><?= $datos['entregado_por']?><span class="marcar">Firma y Sello</span></li>
          <li><span class="titulo">Recibido por: </span><?= $datos['recibido_por']?><span class="marcar">Firma y Sello</span></li>
          <li><span class="titulo">Autorizado por: </span><?= $datos['autorizado_por']?><span class="marcar">Firma y Sello</span></li>
          <li><span class="titulo">Visto bueno por: </span><?= $datos['visto_bueno_por']?><span class="marcar">Firma y Sello</span></li>
        </ul>
      </div>
      <div class="content-table">
        <ul>
          <li><span class="titulo">Observación: </span><?= $datos['observacion']?></li>
          <li><span class="titulo">Fecha actual: </span><?php
            setlocale(LC_TIME,"mx_MX");
            echo strftime("%d/%m/%Y");
          ?></li>
          <li><span class="titulo">Fecha de elaboración: </span><?= $datos['fecha_guarda']?></li>
        </ul>
      </div>
      <div class="content-table">
        <table border="1">
          <tr>
            <th>Id Movimiento</th>
            <th>Descripción</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Color</th>
            <th>Serie/Chasis</th>
            <th>Código</th>
            <th>Código anterior</th>
          </tr>
          <?php
            foreach ($detalles as $detalle) {
              echo "<tr>";
                echo "<td>".$detalle['id_movimiento']."</td>";
                echo "<td>".$detalle['descripcion']."</td>";
                echo "<td>".$detalle['nombre_marca']."</td>";
                echo "<td>".$detalle['modelo']."</td>";
                echo "<td>".$detalle['color']."</td>";
                echo "<td>".$detalle['serie']."</td>";
                echo "<td>".$detalle['codigo']."</td>";
                echo "<td>".$detalle['codigo_anterior']."</td>";
              echo "</tr>";
            }
          ?>
        </table>
      </div>
    </div>
  </body>
</html>
