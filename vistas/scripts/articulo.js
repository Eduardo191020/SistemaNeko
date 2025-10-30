// vistas/scripts/articulo.js
var tabla;

// Nombre: solo letras (con acentos) y espacios, 3..50, sin repeticiones absurdas.
function esNombreValido(nombre) {
  const txt = (nombre || "").trim();

  // 1) Solo letras (con acentos) y espacios, 3..50
  if (!/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{3,50}$/.test(txt)) return false;

  // 2) Evitar repeticiones absurdas tipo "xxx", "aaa", "qqq"
  if (/([A-Za-z])\1{2,}/.test(txt)) return false;

  // 3) Evitar cadenas sin vocales (wqr, fgh)
  if (!/[AEIOUaeiouÁÉÍÓÚáéíóúüÜ]/.test(txt)) return false;

  // 4) Palabras comunes inválidas
  const invalidos = ["xxx", "asdf", "test", "prueba", "rol", "role", "wewqeq", "qwe"];
  if (invalidos.some(p => txt.toLowerCase().includes(p))) return false;

  return true;
}

// Precio: ^\d{1,7}(\.\d{1,2})?$
function esPrecioValido(v) {
  return /^\d{1,7}(\.\d{1,2})?$/.test((v || "").trim());
}

// Código de barras: solo dígitos y largo 8..13
function esCodigoValido(v) {
  const txt = (v || "").trim();
  return /^\d{8,13}$/.test(txt);
} 

// Aviso sutil en el input (un solo mensaje, nada invasivo)
function setValidity(el, ok, msg) {
  if (!el) return;
  el.setCustomValidity(ok ? "" : msg);
  if (!ok) el.reportValidity();
}

// =========================== Inicialización ===========================
function init() {
  mostrarform(false);
  listar();

  // submit del formulario
  $("#formulario").on("submit", function (e) { guardaryeditar(e); });

  // cargar categorías
  $.post("../ajax/articulo.php?op=selectCategoria", function (r) {
    $("#idcategoria").html(r);
    $("#idcategoria").selectpicker('refresh');
  });

  // UI menor
  $("#imagenmuestra").hide();
  $("#mAlmacen").addClass("treeview active");
  $("#lArticulos").addClass("active");

  // Validación "en vivo" de precio y código
  $("#precio_venta").on("input", function () {
    const ok = esPrecioValido(this.value);
    setValidity(this, ok, "Precio inválido. Use solo números y hasta 2 decimales.");
  });

  // Forzar solo dígitos, cortar a 13, y mostrar aviso (8..13)
$("#codigo").on("input", function () {
  // 1) Quitar todo lo que no sea dígito y limitar a 13
  this.value = this.value.replace(/\D+/g, "").slice(0, 13);

  // 2) Validar largo final
  const ok = esCodigoValido(this.value);
  setValidity(this, ok, ok ? "" : "Solo números (8 a 13 dígitos).");
}); 

$("#stock").on("input", function() {
  // Quitar todo lo que no sea dígito y limitar a 5 cifras (0 - 99999)
  this.value = this.value.replace(/[^\d]/g, "").slice(0, 5);

  // Evitar negativo o vacío
  if (this.value === "" || parseInt(this.value) < 0) {
    this.setCustomValidity("El stock debe ser un número mayor o igual a 0.");
  } else {
    this.setCustomValidity("");
  }
});

}

// ============================== Vistas ===============================
function limpiar() {
  $("#codigo").val("");
  $("#nombre").val("");
  $("#descripcion").val("");
  $("#stock").val("");
  $("#precio_compra").val("");
  $("#precio_venta").val("");
  $("#imagenmuestra").attr("src", "").hide();
  $("#imagenactual").val("");
  $("#print").hide();
  $("#idarticulo").val("");

  // limpiar validaciones HTML5
  ["#precio_venta", "#codigo"].forEach(sel => {
    const el = document.querySelector(sel);
    if (el) el.setCustomValidity("");
  });
}

function mostrarform(flag) {
  limpiar();
  if (flag) {
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

function cancelarform() {
  limpiar();
  mostrarform(false);
}

function listar() {
  tabla = $('#tbllistado').dataTable({
    "aProcessing": true,
    "aServerSide": true,
    dom: 'Bfrtip',
    buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdf'],
    "ajax": {
      url: '../ajax/articulo.php?op=listar',
      type: 'get',
      dataType: 'json',
      error: function (e) {
        console.log(e.responseText);
      }
    },
    "bDestroy": true,
    "iDisplayLength": 5,
    "order": [[0, "desc"]]
  }).DataTable();
}

// ========================= Guardar / Editar ==========================
function guardaryeditar(e) {
  e.preventDefault();
  $("#btnGuardar").prop("disabled", true);

  // Validaciones de UI (1 solo mensaje por campo)
  const nombre = $("#nombre").val();
  if (!esNombreValido(nombre)) {
    bootbox.alert("⚠️ Ingrese un nombre válido (solo letras y sin repeticiones).");
    $("#btnGuardar").prop("disabled", false);
    return;
  }

  const precioEl = document.querySelector("#precio_venta");
  if (!esPrecioValido(precioEl.value)) {
    setValidity(precioEl, false, "Precio inválido. Use solo números y hasta 2 decimales.");
    $("#btnGuardar").prop("disabled", false);
    return;
  } else {
    setValidity(precioEl, true, "");
  }

const stockEl = document.querySelector("#stock");
const stockVal = parseInt(stockEl.value);

if (isNaN(stockVal) || stockVal < 0) {
  stockEl.setCustomValidity("El stock debe ser 0 o mayor.");
  stockEl.reportValidity();
  $("#btnGuardar").prop("disabled", false);
  return;
} else {
  stockEl.setCustomValidity("");
}

  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "../ajax/articulo.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      const raw = (datos == null ? '' : String(datos));
      const msg = raw.replace(/\uFEFF/g, '').trim();
      const duplicado = /duplicado/i;

      if (duplicado.test(msg)) {
        // nombre repetido
        bootbox.alert("⚠️ El nombre ya existe. No se permiten duplicados.");
        mostrarform(false);
        tabla.ajax.reload();
        $("#btnGuardar").prop("disabled", false);
        return;
      }

      // éxito normal (lo que devuelva tu backend)
      bootbox.alert(msg);
      mostrarform(false);
      tabla.ajax.reload();
    }
  });

  limpiar();
}

// ============================= Mostrar ===============================
function mostrar(idarticulo) {
  $.post("../ajax/articulo.php?op=mostrar",
    { idarticulo: idarticulo },
    function (data, status) {
      data = JSON.parse(data);
      mostrarform(true);

      $("#idcategoria").val(data.idcategoria);
      $('#idcategoria').selectpicker('refresh');

      $("#codigo").val(data.codigo);
      $("#nombre").val(data.nombre);
      $("#stock").val(data.stock);
      $("#precio_compra").val(data.precio_compra);
      $("#precio_venta").val(data.precio_venta);
      $("#descripcion").val(data.descripcion);

      if (data.imagen) {
        $("#imagenmuestra").attr("src", "../files/articulos/" + data.imagen).show();
      } else {
        $("#imagenmuestra").attr("src", "").hide();
      }
      $("#imagenactual").val(data.imagen);
      $("#idarticulo").val(data.idarticulo);

    }
  );
}

// ====================== Activar / Desactivar =========================
function desactivar(idarticulo) {
  bootbox.confirm("¿Está seguro de desactivar el artículo?", function (result) {
    if (result) {
      $.post("../ajax/articulo.php?op=desactivar", { idarticulo: idarticulo }, function (e) {
        bootbox.alert(e);
        tabla.ajax.reload();
      });
    }
  });
}

function activar(idarticulo) {
  bootbox.confirm("¿Está seguro de activar el artículo?", function (result) {
    if (result) {
      $.post("../ajax/articulo.php?op=activar", { idarticulo: idarticulo }, function (e) {
        bootbox.alert(e);
        tabla.ajax.reload();
      });
    }
  });
}

//función para generar el código de barras
function generarbarcode()
{
	codigo=$("#codigo").val();
	JsBarcode("#barcode", codigo);
	$("#print").show();
}

//Función para imprimir el Código de barras
function imprimir()
{
	$("#print").printArea();
}

init();

