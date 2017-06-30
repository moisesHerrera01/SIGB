<section class="dashboard-panel-cen">
  <div class="dashboard-box">
    <div class="dashboard-box-title">
      <span>BIENVENIDO A SIGB</span>
      <span class="icono icon-cancel-circle"></span>
      <span class="icono max-min icon-circle-up"></span>
    </div>
    <div class="dashboard-box-content">
      <h3>MINISTERIO DE TRABAJO Y PREVISION SOCIAL</h3>
      <div class="dashboard-box-info-content">
        <div class="dashboard-box-info">
          <div class="icon-box-info icon-user-check"></div>
          <div class="txt-box-info">
            <?= $usuario?>
          </div>
        </div>

        <div class="dashboard-box-info">
          <div class="icon-box-info icon-accessibility"></div>
          <div class="txt-box-info">
            <?= $rol?>
          </div>
        </div>

        <div class="dashboard-box-info">
          <div class="icon-box-info icon-profile"></div>
          <div class="txt-box-info">
            ULTIMO ACCESO: <?= $fecha_acceso?>
          </div>
        </div>

      </div>
    </div>
  </div>

</section>

<section class="dashboard-panel-izq">
  <div class="dashboard-box">
    <div class="dashboard-box-title">
      <span>Productos con mas movimiento</span>
      <span class="icono icon-cancel-circle"></span>
      <span class="icono max-min icon-circle-up"></span>
    </div>
    <div class="dashboard-box-content">
      <div class="chart-content">
        <canvas id="prod_mov"></canvas>
      </div>
    </div>
  </div>
</section>
<section class="dashboard-panel-der">
  <div class="dashboard-box">
    <div class="dashboard-box-title">
      <span>Retiros y Facturas de Bodega</span>
      <span class="icono icon-cancel-circle"></span>
      <span class="icono max-min icon-circle-up"></span>
    </div>
    <div class="dashboard-box-content">
      <div class="chart-content">
        <canvas id="descargos"></canvas>
      </div>
    </div>
  </div>

</section>

<script src=<?= base_url("assets/js/Chart.js")?>></script>
<script type="text/javascript">
  $(document).ready(function(){
    var lbls = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

    $.ajax({
      type: 'post',
      url: baseurl + "index.php/Dashboard/obtenerDescargosCargos",
      data: {},
      success: function(result) {
        var res = JSON.parse(result);

        var ctx = $("#descargos");
        var data = {
          labels: lbls,
          datasets: [
              {
                  label: "Retiros",
                  fill: false,
                  lineTension: 0.1,
                  backgroundColor: "rgba(119,190,119,0.4)",
                  borderColor: "rgb(119,190,119)",
                  borderCapStyle: 'butt',
                  borderDash: [],
                  borderDashOffset: 0.0,
                  borderJoinStyle: 'miter',
                  pointBorderColor: "rgb(119,190,119)",
                  pointBackgroundColor: "#fff",
                  pointBorderWidth: 1,
                  pointHoverRadius: 5,
                  pointHoverBackgroundColor: "rgb(119,190,119)",
                  pointHoverBorderColor: "rgba(220,220,220,1)",
                  pointHoverBorderWidth: 2,
                  pointRadius: 1,
                  pointHitRadius: 10,
                  data: [res['descargos']['enero'],res['descargos']['febrero'],res['descargos']['marzo'],res['descargos']['abril'],res['descargos']['mayo'],res['descargos']['junio'],res['descargos']['julio'],res['descargos']['agosto'],res['descargos']['septiembre'],res['descargos']['octubre'],res['descargos']['noviembre'],res['descargos']['diciembre']],
                  spanGaps: false,
              },
              {
                  label: "Facturas",
                  fill: false,
                  lineTension: 0.1,
                  backgroundColor: "rgba(75,192,192,0.4)",
                  borderColor: "rgba(75,192,192,1)",
                  borderCapStyle: 'butt',
                  borderDash: [],
                  borderDashOffset: 0.0,
                  borderJoinStyle: 'miter',
                  pointBorderColor: "rgba(75,192,192,1)",
                  pointBackgroundColor: "#fff",
                  pointBorderWidth: 1,
                  pointHoverRadius: 5,
                  pointHoverBackgroundColor: "rgba(75,192,192,1)",
                  pointHoverBorderColor: "rgba(220,220,220,1)",
                  pointHoverBorderWidth: 2,
                  pointRadius: 1,
                  pointHitRadius: 10,
                  data: [res['cargos']['enero'],res['cargos']['febrero'],res['cargos']['marzo'],res['cargos']['abril'],res['cargos']['mayo'],res['cargos']['junio'],res['cargos']['julio'],res['cargos']['agosto'],res['cargos']['septiembre'],res['cargos']['octubre'],res['cargos']['noviembre'],res['cargos']['diciembre']],
                  spanGaps: false,
              }
          ]
        };

        var line = new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                scales: {
                    yAxes: [{
                        stacked: false
                    }]
                }
            }
        });
      },
    });

    $.ajax({
      type: 'post',
      url: baseurl + "index.php/Dashboard/obtenerProductoMovimiento",
      data: {},
      success: function(result) {
        var res = JSON.parse(result);

        var ctx = $("#prod_mov");
        var data = {
          labels: [
              res[0]['nombre_producto'],
              res[1]['nombre_producto'],
              res[2]['nombre_producto'],
              res[3]['nombre_producto'],
              res[4]['nombre_producto']

          ],
          datasets: [
              {
                  data: [
                    res[0]['total'],
                    res[1]['total'],
                    res[2]['total'],
                    res[3]['total'],
                    res[4]['total'],
                  ],
                  backgroundColor: [
                      "#AEC6CF",
                      "#B39EB5",
                      "#FFB347",
                      "#779ECB",
                      "#836953"
                  ],
                  hoverBackgroundColor: [
                      "#AEC6CF",
                      "#B39EB5",
                      "#FFB347",
                      "#779ECB",
                      "#836953"
                  ]
              }
            ]
        };

        var myDoughnutChart = new Chart(ctx, {
          type: 'doughnut',
          data: data,
          animation:{
            animateScale:true
          }
        });
      },
    });

  });
</script>
