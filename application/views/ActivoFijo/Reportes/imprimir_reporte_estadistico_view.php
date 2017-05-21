<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Reporte Estadistico</title>
    <link rel="shortcut icon" href="<?= base_url("assets/image/logo.jpg")?>" />
    <link href=<?= base_url("vendor/twbs/bootstrap/dist/css/bootstrap.min.css")?> rel="stylesheet">
    <link href=<?= base_url("assets/css/main.css")?> rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url("assets/image/logo.jpg")?>" />
    <style>
      table th, td {
        text-align: center;
      }

      .direccion th:first-child {
        width: 15%;
      }

      .header {
        width: 100%;
        padding: 0.5em;
      }

      .logo-inst {
        float: left;
        width: 25%;
        margin: 0 auto;
      }

      .logo-inst > img {
        margin-top: 1.75em;
      }

      .header-inst {
        float: right;
        margin: 0 auto;
        width: 75%;
        text-align: center;
        font-size: 1.2em;
      }

      .content-column {
        width: 100%;
      }

      .column-a {
        float: left;
        width: 50%;
        margin: 0 auto;
      }

      .column-b {
        float: right;
        width: 50%;
        margin: 0 auto;
      }

      ul {
        list-style: none;
      }

      .column-a ul {
        margin-left: 1em;
      }

      .column-a li {
        margin-left: 0.75em;
      }

      .subindice {
        margin-top: -1.50em;
        font-size: 0.75em;
      }
    </style>
  </head>
  <body onload="window.print()">
    <div>
      <div class="header">
        <div class="logo-inst"><img src=<?= base_url("assets/image/icono.jpg")?> alt="" width="250px" /></div>
        <div class="header-inst">
          <h3>MINISTERIO DE TRABAJO Y PREVISION SOCIAL</h3>
          <h4>Oficina de Estadistica e Informacion Laboral</h4>
          <h3>EQUIPO INFORMATICO Y MOBILIARIO</h3>
          <p>Fecha de Elaboracion: <?= $this->uri->segment(6)?></p>
        </div>
      </div>
      <div>
        <table class='table table-bordered'>
          <tr class="direccion">
            <th rowspan="3">DIRECCIONES</th>
            <th colspan="20">EQUIPO INFORMATICO</th>
          </tr>
          <tr>
            <th colspan="4">P.C. (4)*</th>
            <th colspan="4">U.P.S</th>
            <th colspan="4">IMPRESOR</th>
            <th colspan="4">SCANNERS</th>
            <th colspan="4">LAPTOP</th>
          </tr>
          <tr>
            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>

            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>

            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>

            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>

            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>
          </tr>
          <tr>
            <th>OFICINAS REGIONALES Y DEPARTAMENTALES</th>
            <?php for ($i = 3; $i < 8; $i++): ?>
              <?php for ($j = 0; $j < 4; $j++): ?>
                <td><?= $result[$i][$j]?></td>
              <?php endfor; ?>
            <?php endfor;?>
          </tr>
          <tr>
            <th>CENTRO DE RECREACION A TRABAJADORES</th>
            <?php for ($i = 3; $i < 8; $i++): ?>
              <?php for ($j = 4; $j < 8; $j++): ?>
                <td><?= $result[$i][$j]?></td>
              <?php endfor; ?>
            <?php endfor;?>
          </tr>
          <tr>
            <th>TOTAL</th>
            <?php for ($i = 3; $i < 8; $i++): ?>
              <?php for ($j = 0; $j < 4; $j++): ?>
                <th><?= $result[$i][$j] + $result[$i][$j+4]?></th>
              <?php endfor; ?>
            <?php endfor;?>
          </tr>
        </table>
        <p class="subindice">FUENTE:ACTIVO FIJO</p>

        <table class='table table-bordered'>
          <tr class="direccion">
            <th rowspan="3">DIRECCIONES</th>
            <th colspan="8">MOBILIARIO</th>
          </tr>
          <tr>
            <th colspan="4">ESCRITORIO</th>
            <th colspan="4">SILLA</th>
          </tr>
          <tr>
            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>

            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>
          </tr>
          <tr>
            <th>OFICINAS REGIONALES Y DEPARTAMENTALES</th>
            <?php for ($i = 8; $i < 10; $i++): ?>
              <?php for ($j = 0; $j < 4; $j++): ?>
                <td><?= $result[$i][$j]?></td>
              <?php endfor; ?>
            <?php endfor;?>
          </tr>
          <tr>
            <th>CENTRO DE RECREACION A TRABAJADORES</th>
            <?php for ($i = 8; $i < 10; $i++): ?>
              <?php for ($j = 4; $j < 8; $j++): ?>
                <td><?= $result[$i][$j]?></td>
              <?php endfor; ?>
            <?php endfor;?>
          </tr>
          <tr>
            <th>TOTAL</th>
            <?php for ($i = 8; $i < 10; $i++): ?>
              <?php for ($j = 4; $j < 8; $j++): ?>
                <th><?= $result[$i][$j-4] + $result[$i][$j]?></th>
              <?php endfor; ?>
            <?php endfor;?>
          </tr>
        </table>
        <p class="subindice">FUENTE:ACTIVO FIJO</p>
      </div>
      <div class="content-column">
        <div class="column-a">
          <ul>
            *
            <li>1 MONITOR</li>
            <li>2 CPU</li>
            <li>3 TECLADO</li>
            <li>4 MOUSE</li>
          </ul>
        </div>
        <div class="column-b">
          <br>
          <ul>
            <li>N NUEVO</li>
            <li>U USO</li>
            <li>O OBSOLETO</li>
            <li>D DESCARGADO Y DESALOJADO</li>
          </ul>
        </div>
      </div>
    </div>
  </body>
</html>
