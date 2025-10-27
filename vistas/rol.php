<?php
// Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"]))
{
  header("Location: login.html");
}
else
{
  require 'header.php';

  // ---- Permiso del módulo (ajusta si usas otro índice de sesión) ----
  if ($_SESSION['acceso']==1)
  {
?>
<!--Contenido-->
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">

          <div class="box-header with-border">
            <h1 class="box-title">Roles
              <button class="btn btn-success" id="btnagregar" onclick="mostrarform(true)">
                <i class="fa fa-plus-circle"></i> Agregar
              </button>
            </h1>
            <div class="box-tools pull-right"></div>
          </div>

          <!-- /.box-header -->
          <!-- centro -->
          <div class="panel-body table-responsive" id="listadoregistros">
            <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
              <thead>
                <th>Opciones</th>
                <th>ID</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Creado</th>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <th>Opciones</th>
                <th>ID</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Creado</th>
              </tfoot>
            </table>
          </div>

          <div class="panel-body" style="height: 400px;" id="formularioregistros">
            <form name="formulario" id="formulario" method="POST">
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Nombre (*):</label>
                <input type="hidden" name="idrol" id="idrol">
                <input type="text" class="form-control" name="nombre" id="nombre" maxlength="50" autocomplete="off" pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]{3,50}$" title="Solo letras y espacios (3 a 50 caracteres)" oninput="soloLetras(this)" placeholder="Ej. Supervisor" required>
              </div>

              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <button class="btn btn-primary" type="submit" id="btnGuardar">
                  <i class="fa fa-save"></i> Guardar
                </button>

                <button class="btn btn-danger" type="button" onclick="cancelarform()">
                  <i class="fa fa-arrow-circle-left"></i> Cancelar
                </button>
              </div>
            </form>
          </div>
          <!--Fin centro-->
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>
<!--Fin-Contenido-->

<?php
  }
  else
  {
    require 'noacceso.php';
  }

  require 'footer.php';
?>
<script type="text/javascript" src="scripts/rol.js"></script>
<?php
}
ob_end_flush();
?>

