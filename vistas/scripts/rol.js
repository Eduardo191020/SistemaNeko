var tabla;
// Permitir solo letras (con acentos) y espacios; compacta espacios
function soloLetras(el){
  el.value = el.value
    .replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g, "") // quita todo menos letras y espacio
    .replace(/\s{2,}/g, " ")                    // evita espacios dobles
    .replace(/^\s+/, "");                       // sin espacio inicial
}
// Valida nombre con el mismo criterio del pattern
function esNombreValido(txt){
  return /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]{3,50}$/.test(txt.trim());
}

//Función que se ejecuta al inicio
function init(){
  mostrarform(false);
  listar();

  $("#formulario").on("submit", function(e){
    guardaryeditar(e);
  });

  // Activar menú (ajusta el ID si tu menú se llama distinto)
  $("#mAcceso").addClass("treeview active");
  $("#lRoles").addClass("active");
}

//Función limpiar
function limpiar(){
  $("#idrol").val("");
  $("#nombre").val("");
}

//Función mostrar formulario
function mostrarform(flag){
  limpiar();
  if(flag){
    $("#listadoregistros").hide();
    $("#formularioregistros").show();
    $("#btnGuardar").prop("disabled", false);
    $("#btnagregar").hide();
  } else {
    $("#listadoregistros").show();
    $("#formularioregistros").hide();
    $("#btnagregar").show();
  }
}

//Función cancelar formulario
function cancelarform(){
  limpiar();
  mostrarform(false);
}

//Función listar
function listar(){
  tabla = $('#tbllistado').dataTable({
    "aProcessing": true, //Activamos el procesamiento del datatables
    "aServerSide": true, //Paginación y filtrado realizados por el servidor
    dom: 'Bfrtip', //Definimos los elementos del control de tabla
    buttons: [
      'copyHtml5',
      'excelHtml5',
      'csvHtml5',
      'pdf'
    ],
    "ajax": {
      url: '../ajax/rol.php?op=listar',
      type: "get",
      dataType: "json",
      error: function(e){
        console.log(e.responseText);
      }
    },
    "bDestroy": true,
    "iDisplayLength": 10, //Cantidad de registros por página
    "order": [[1, "asc"]] //Ordenar (columna, orden)
  }).DataTable();
}

//Función para guardar o editar
function guardaryeditar(e){
  e.preventDefault(); //No se activará la acción predeterminada del evento
  $("#btnGuardar").prop("disabled", true);
  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "../ajax/rol.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function(datos){
      bootbox.alert(datos);
      mostrarform(false);
      tabla.ajax.reload();
    }
  });

  limpiar();
}

//Función para mostrar los datos de un registro
function mostrar(idrol){
  $.post("../ajax/rol.php?op=mostrar", {idrol: idrol}, function(data, status){
    data = JSON.parse(data);
    mostrarform(true);

    $("#idrol").val(data.id_rol);
    $("#nombre").val(data.nombre);
  });
}

//Función para desactivar registros
function desactivar(idrol){
  bootbox.confirm("¿Está seguro de desactivar el rol?", function(result){
    if(result){
      $.post("../ajax/rol.php?op=desactivar", {idrol: idrol}, function(e){
        bootbox.alert(e);
        tabla.ajax.reload();
      });
    }
  });
}

//Función para activar registros
function activar(idrol){
  bootbox.confirm("¿Está seguro de activar el rol?", function(result){
    if(result){
      $.post("../ajax/rol.php?op=activar", {idrol: idrol}, function(e){
        bootbox.alert(e);
        tabla.ajax.reload();
      });
    }
  });
}

init();
