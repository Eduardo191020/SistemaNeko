<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"]))
{
  header("Location: ../login.php");
  exit;
}

require 'header.php';

if ($_SESSION['escritorio']==1)
{
  require_once "../modelos/Consultas.php";
  $consulta = new Consultas();
  
  $rsptac = $consulta->totalcomprahoy();
  $regc=$rsptac->fetch_object();
  $totalc=$regc->total_compra ?? 0;

  $rsptav = $consulta->totalventahoy();
  $regv=$rsptav->fetch_object();
  $totalv=$regv->total_venta ?? 0;

  //Datos para mostrar el gráfico de barras de las compras
  $compras10 = $consulta->comprasultimos_10dias();
  $fechasc='';
  $totalesc='';
  while ($regfechac= $compras10->fetch_object()) {
    $fechasc=$fechasc.'"'.$regfechac->fecha .'",';
    $totalesc=$totalesc.$regfechac->total .','; 
  }

  //Quitamos la última coma
  $fechasc=substr($fechasc, 0, -1);
  $totalesc=substr($totalesc, 0, -1);

   //Datos para mostrar el gráfico de barras de las ventas
  $ventas12 = $consulta->ventasultimos_12meses();
  $fechasv='';
  $totalesv='';
  while ($regfechav= $ventas12->fetch_object()) {
    $fechasv=$fechasv.'"'.$regfechav->fecha .'",';
    $totalesv=$totalesv.$regfechav->total .','; 
  }

  //Quitamos la última coma
  $fechasv=substr($fechasv, 0, -1);
  $totalesv=substr($totalesv, 0, -1);

?>
<!--Contenido-->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">        
        <!-- Main content -->
        <section class="content">
            <div class="row">
              <div class="col-md-12">
                  <div class="box">
                    <div class="box-header with-border">
                          <h1 class="box-title">Escritorio 
                            <a href="?debug=1" class="btn btn-info btn-xs" style="margin-left: 10px;">
                              <i class="fa fa-bug"></i> Ver Permisos
                            </a>
                          </h1>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                    <!-- /.box-header -->
                    
                    <?php 
                    // ✅ MODO DEBUG - Ver permisos del usuario
                    if (isset($_GET['debug']) && $_GET['debug'] == '1') {
                    ?>
                      <div class="panel-body">
                        <div class="alert alert-info">
                          <h4><i class="icon fa fa-info"></i> Información de Permisos del Usuario</h4>
                          <table class="table table-bordered table-striped">
                            <tr><th width="200">Variable</th><th>Valor</th></tr>
                            <tr><td><strong>ID Usuario</strong></td><td><?= $_SESSION['idusuario'] ?? 'NO SET' ?></td></tr>
                            <tr><td><strong>Nombre</strong></td><td><?= $_SESSION['nombre'] ?? 'NO SET' ?></td></tr>
                            <tr><td><strong>Email</strong></td><td><?= $_SESSION['email'] ?? 'NO SET' ?></td></tr>
                            <tr><td><strong>Imagen</strong></td><td><?= $_SESSION['imagen'] ?? 'NO SET' ?></td></tr>
                            <tr><td><strong>Escritorio</strong></td><td><span class="label <?= ($_SESSION['escritorio']??0)==1?'bg-green':'bg-red' ?>"><?= $_SESSION['escritorio'] ?? '0' ?></span></td></tr>
                            <tr><td><strong>Almacén</strong></td><td><span class="label <?= ($_SESSION['almacen']??0)==1?'bg-green':'bg-red' ?>"><?= $_SESSION['almacen'] ?? '0' ?></span></td></tr>
                            <tr><td><strong>Compras</strong></td><td><span class="label <?= ($_SESSION['compras']??0)==1?'bg-green':'bg-red' ?>"><?= $_SESSION['compras'] ?? '0' ?></span></td></tr>
                            <tr><td><strong>Ventas</strong></td><td><span class="label <?= ($_SESSION['ventas']??0)==1?'bg-green':'bg-red' ?>"><?= $_SESSION['ventas'] ?? '0' ?></span></td></tr>
                            <tr><td><strong>Acceso</strong></td><td><span class="label <?= ($_SESSION['acceso']??0)==1?'bg-green':'bg-red' ?>"><?= $_SESSION['acceso'] ?? '0' ?></span></td></tr>
                            <tr><td><strong>Consulta Compras</strong></td><td><span class="label <?= ($_SESSION['consultac']??0)==1?'bg-green':'bg-red' ?>"><?= $_SESSION['consultac'] ?? '0' ?></span></td></tr>
                            <tr><td><strong>Consulta Ventas</strong></td><td><span class="label <?= ($_SESSION['consultav']??0)==1?'bg-green':'bg-red' ?>"><?= $_SESSION['consultav'] ?? '0' ?></span></td></tr>
                          </table>
                          <p class="text-muted"><small><i class="fa fa-lightbulb-o"></i> Para quitar este panel, elimina <code>?debug=1</code> de la URL</small></p>
                        </div>
                      </div>
                    <?php 
                    } 
                    ?>
                    
                    <!-- centro -->
                    <div class="panel-body">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                          <div class="small-box bg-aqua">
                              <div class="inner">
                                <h4 style="font-size:17px;">
                                  <strong>S/ <?php echo number_format($totalc, 2); ?></strong>
                                </h4>
                                <p>Compras del Día</p>
                              </div>
                              <div class="icon">
                                <i class="ion ion-bag"></i>
                              </div>
                              <?php if ($_SESSION['compras']==1): ?>
                                <a href="ingreso.php" class="small-box-footer">Ver Compras <i class="fa fa-arrow-circle-right"></i></a>
                              <?php else: ?>
                                <span class="small-box-footer">Compras del Día</span>
                              <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                          <div class="small-box bg-green">
                              <div class="inner">
                                <h4 style="font-size:17px;">
                                  <strong>S/ <?php echo number_format($totalv, 2); ?></strong>
                                </h4>
                                <p>Ventas del Día</p>
                              </div>
                              <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                              </div>
                              <?php if ($_SESSION['ventas']==1): ?>
                                <a href="venta.php" class="small-box-footer">Ver Ventas <i class="fa fa-arrow-circle-right"></i></a>
                              <?php else: ?>
                                <span class="small-box-footer">Ventas del Día</span>
                              <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel-body">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                          <div class="box box-primary">
                              <div class="box-header with-border">
                                Compras de los últimos 10 días
                              </div>
                              <div class="box-body">
                                <canvas id="compras" width="400" height="300"></canvas>
                              </div>
                          </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                          <div class="box box-primary">
                              <div class="box-header with-border">
                                Ventas de los últimos 12 meses
                              </div>
                              <div class="box-body">
                                <canvas id="ventas" width="400" height="300"></canvas>
                              </div>
                          </div>
                        </div>
                    </div>
                    <!--Fin centro -->
                  </div><!-- /.box -->
              </div><!-- /.col -->
          </div><!-- /.row -->
      </section><!-- /.content -->

    </div><!-- /.content-wrapper -->
  <!--Fin-Contenido-->

<script src="../public/js/Chart.min.js"></script>
<script src="../public/js/Chart.bundle.min.js"></script> 
<script type="text/javascript">
var ctx = document.getElementById("compras").getContext('2d');
var compras = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php echo $fechasc; ?>],
        datasets: [{
            label: 'Compras en S/ de los últimos 10 días',
            data: [<?php echo $totalesc; ?>],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});

var ctx = document.getElementById("ventas").getContext('2d');
var ventas = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php echo $fechasv; ?>],
        datasets: [{
            label: 'Ventas en S/ de los últimos 12 Meses',
            data: [<?php echo $totalesv; ?>],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>

<?php
}
else
{
  require 'noacceso.php';
}

require 'footer.php';
?>
<?php 
ob_end_flush();
?>