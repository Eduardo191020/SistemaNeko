<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"]))
{
  header("Location: login.html");
}
else
{
require 'header.php';
if ($_SESSION['acceso']==1)
{
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
                          <h1 class="box-title">Usuario <button class="btn btn-success" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Agregar</button> <a href="../reportes/rptusuarios.php" target="_blank"><button class="btn btn-info"><i class="fa fa-clipboard"></i> Reporte</button></a></h1>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <!-- centro -->
                    <div class="panel-body table-responsive" id="listadoregistros">
                        <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                          <thead>
                            <th>Opciones</th>
                            <th>Nombre</th>
                            <th>Tipo Doc.</th>
                            <th>Número</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Cargo</th>
                            <th>Foto</th>
                            <th>Estado</th>
                          </thead>
                          <tbody>                            
                          </tbody>
                          <tfoot>
                            <th>Opciones</th>
                            <th>Nombre</th>
                            <th>Tipo Doc.</th>
                            <th>Número</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Cargo</th>
                            <th>Foto</th>
                            <th>Estado</th>
                          </tfoot>
                        </table>
                    </div>
                    
                    <!-- FORMULARIO -->
                    <div class="panel-body" id="formularioregistros">
                        <form name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="idusuario" id="idusuario">
                            
                            <!-- PASO 1: IDENTIFICACIÓN -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <h4 class="text-info"><i class="fa fa-id-card"></i> Paso 1: Identificación</h4>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <label>Tipo Documento (*):</label>
                                    <select class="form-control" name="tipo_documento" id="tipo_documento" required>
                                        <option value="">Seleccione...</option>
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>
                                        <option value="Carnet de Extranjería">Carnet de Extranjería</option>
                                    </select>
                                    <small class="text-muted" id="hint_tipo">Selecciona el tipo de documento</small>
                                </div>

                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <label>Número de Documento (*):</label>
                                    <input type="text" class="form-control" name="num_documento" id="num_documento" required>
                                    <small class="text-muted" id="hint_numero">Ingresa el número de documento</small>
                                </div>
                            </div>

                            <!-- DATOS AUTOCOMPLETADOS -->
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label>Nombre Completo / Razón Social (*):</label>
                                    <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100" placeholder="Se autocompletará con RENIEC/SUNAT" readonly required>
                                    <small class="text-info"><i class="fa fa-info-circle"></i> Este campo se llenará automáticamente al ingresar el documento</small>
                                </div>
                            </div>

                            <!-- PASO 2: DATOS DE CONTACTO -->
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-lg-12">
                                    <h4 class="text-info"><i class="fa fa-address-book"></i> Paso 2: Datos de Contacto</h4>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <label>Email (*):</label>
                                    <div style="position: relative;">
                                        <input type="email" class="form-control" name="email" id="email" maxlength="50" placeholder="ejemplo@dominio.com" required>
                                        <span id="email-status" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:1.2rem;"></span>
                                    </div>
                                    <small class="text-muted" id="email-hint">Se usará como usuario de acceso al sistema</small>
                                </div>

                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <label>Teléfono (*):</label>
                                    <input type="text" class="form-control" name="telefono" id="telefono" maxlength="15" placeholder="Número de teléfono" required>
                                    <small class="text-muted">Solo números, guiones y espacios</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <label>Dirección:</label>
                                    <input type="text" class="form-control" name="direccion" id="direccion" maxlength="70" placeholder="Dirección completa">
                                    <small class="text-muted">Opcional</small>
                                </div>

                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <label>Cargo (*):</label>
                                    <select class="form-control" name="cargo" id="cargo" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Administrador">Administrador</option>
                                        <option value="Almacenero">Almacenero</option>
                                        <option value="Vendedor">Vendedor</option>
                                    </select>
                                    <small class="text-muted">Cargo del usuario en el sistema</small>
                                </div>
                            </div>

                            <!-- PASO 3: SEGURIDAD -->
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-lg-12">
                                    <h4 class="text-info"><i class="fa fa-lock"></i> Paso 3: Seguridad y Accesos</h4>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Contraseña (*):</label>
                                    <div style="position: relative;">
                                        <input type="password" class="form-control" name="clave" id="clave" maxlength="64" placeholder="Mínimo 10 caracteres" required>
                                        <span class="input-eye" id="toggleClave" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;user-select:none;">👁️</span>
                                    </div>
                                    <small class="text-muted">Debe cumplir los requisitos de seguridad</small>
                                    
                                    <!-- Indicadores de fortaleza -->
                                    <div id="pwd-strength" style="margin-top: 8px; display: none;">
                                        <div class="pwd-req" id="r-len"><i class="fa fa-times text-danger"></i> 10-64 caracteres</div>
                                        <div class="pwd-req" id="r-up"><i class="fa fa-times text-danger"></i> 1 mayúscula</div>
                                        <div class="pwd-req" id="r-low"><i class="fa fa-times text-danger"></i> 1 minúscula</div>
                                        <div class="pwd-req" id="r-num"><i class="fa fa-times text-danger"></i> 1 número</div>
                                        <div class="pwd-req" id="r-spe"><i class="fa fa-times text-danger"></i> 1 especial</div>
                                    </div>
                                </div>

                                <div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Permisos (*):</label>
                                    <div class="well well-sm" style="max-height: 200px; overflow-y: auto;">
                                        <ul style="list-style: none; padding-left: 0;" id="permisos">
                                            <!-- Se llenan dinámicamente -->
                                        </ul>
                                    </div>
                                    <small class="text-muted">Selecciona los módulos a los que tendrá acceso</small>
                                </div>
                            </div>

                            <!-- PASO 4: FOTO (OPCIONAL) -->
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-lg-12">
                                    <h4 class="text-info"><i class="fa fa-camera"></i> Paso 4: Foto de Perfil (Opcional)</h4>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <label>Imagen:</label>
                                    <input type="file" class="form-control" name="imagen" id="imagen" accept="image/x-png,image/gif,image/jpeg">
                                    <input type="hidden" name="imagenactual" id="imagenactual">
                                    <small class="text-muted">Formatos: JPG, PNG, GIF (máx. 2MB)</small>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12 text-center">
                                    <img src="" width="150px" height="150px" id="imagenmuestra" style="border: 2px solid #ddd; border-radius: 8px; object-fit: cover; display: none;">
                                </div>
                            </div>

                            <!-- BOTONES -->
                            <div class="row" style="margin-top: 20px;">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <button class="btn btn-primary btn-lg" type="submit" id="btnGuardar">
                                        <i class="fa fa-save"></i> Guardar Usuario
                                    </button>
                                    <button class="btn btn-danger btn-lg" onclick="cancelarform()" type="button">
                                        <i class="fa fa-arrow-circle-left"></i> Cancelar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--Fin centro -->
                  </div><!-- /.box -->
              </div><!-- /.col -->
          </div><!-- /.row -->
      </section><!-- /.content -->

    </div><!-- /.content-wrapper -->
  <!--Fin-Contenido-->

<style>
.pwd-req {
    font-size: 0.85em;
    margin: 3px 0;
}
.pwd-req i {
    width: 16px;
}
.input-eye {
    opacity: 0.7;
}
.input-eye:hover {
    opacity: 1;
}
.text-info {
    color: #17a2b8 !important;
}
</style>

<?php
}
else
{
  require 'noacceso.php';
}
require 'footer.php';
?>

<script type="text/javascript" src="scripts/usuario.js"></script>
<?php 
}
ob_end_flush();
?>