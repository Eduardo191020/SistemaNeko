<?php
// ======= arranque de sesión + guardas =======
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

// Si NO hay login válido, redirige
if (empty($_SESSION['idusuario'])) {
  header('Location: ../login.php');
  exit;
}

// Valores seguros para evitar notices
$sesNombre = htmlspecialchars($_SESSION['nombre'] ?? 'Usuario', ENT_QUOTES, 'UTF-8');
$sesImagen = $_SESSION['imagen'] ?? 'default.png'; // pon un default en /files/usuarios/default.png
$sesImagen = htmlspecialchars($sesImagen, ENT_QUOTES, 'UTF-8');

// Helper: devuelve true si el flag está activo (1)
function flag($k){ return !empty($_SESSION[$k]) && (int)$_SESSION[$k] === 1; }
?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Ferretería Neko | Panel</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Core CSS -->
  <link rel="stylesheet" href="../public/css/bootstrap.min.css">
  <link rel="stylesheet" href="../public/css/font-awesome.css">
  <link rel="stylesheet" href="../public/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../public/css/_all-skins.min.css">
  <link rel="stylesheet" href="../public/css/neko-corporate.css">
  <link rel="apple-touch-icon" href="../public/img/apple-touch-icon.png">
  <link rel="shortcut icon" href="../public/img/favicon.ico">

  <!-- DATATABLES -->
  <link rel="stylesheet" href="../public/datatables/jquery.dataTables.min.css">
  <link rel="stylesheet" href="../public/datatables/buttons.dataTables.min.css"/>
  <link rel="stylesheet" href="../public/datatables/responsive.dataTables.min.css"/>

  <link rel="stylesheet" href="../public/css/bootstrap-select.min.css">

  <!-- Fuente (opcional, look corporativo) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- Tema azul corporativo (override) -->
  <style>
    :root{
      --neko-primary:#1565c0;    /* azul principal */
      --neko-primary-dark:#0d47a1;
      --neko-primary-700:#1e88e5;
      --neko-primary-600:#1976d2;
      --neko-bg:#0f172a;         /* para textos sobre azul */
      --neko-white:#ffffff;
    }
    html,body{ font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif; }
    /* ===== Skin Neko Blue ===== */
    
    .skin-neko-blue .main-header .logo{
      background: linear-gradient(90deg,var(--neko-primary-dark),var(--neko-primary));
      color:var(--neko-white)!important; border-right:0;
      font-weight:600; letter-spacing:.3px;
    }
    .skin-neko-blue .main-header .logo:hover{ opacity:.95; }

    .skin-neko-blue .main-header .navbar{
      background: linear-gradient(90deg,var(--neko-primary),var(--neko-primary-600));
      box-shadow: 0 2px 8px rgba(0,0,0,.15);
    }
    .skin-neko-blue .main-header .navbar .nav>li>a{ color:#eaf2ff; }
    .skin-neko-blue .main-header .navbar .nav>li>a:hover{ background:rgba(255,255,255,.08); color:#fff; }
    .skin-neko-blue .main-header .navbar .sidebar-toggle{ color:#eaf2ff; }
    .skin-neko-blue .main-header .navbar .sidebar-toggle:hover{ background:rgba(255,255,255,.08); }

    .skin-neko-blue .logo .logo-mini{ font-weight:700; }
    .skin-neko-blue .logo .logo-lg{ font-weight:700; }

    /* Dropdown usuario */
    .skin-neko-blue .main-header .navbar .dropdown-menu{
      border:0; box-shadow:0 10px 20px rgba(2,31,77,.15); border-radius:10px; overflow:hidden;
    }
    .skin-neko-blue .main-header li.user-header{
      background: linear-gradient(90deg,var(--neko-primary-dark),var(--neko-primary));
      color:#fff;
    }

    /* Sidebar azul */
    .skin-neko-blue .main-sidebar{ background:#0b3a7a; }
    .skin-neko-blue .sidebar a{ color:#dbeafe; }
    .skin-neko-blue .sidebar-menu>li>a{ border-left:3px solid transparent; }
    .skin-neko-blue .sidebar-menu>li:hover>a{ background:rgba(255,255,255,.06); color:#fff; }
    .skin-neko-blue .sidebar-menu>li.active>a{
      background:rgba(255,255,255,.12); color:#fff; border-left-color:#90caf9;
    }
    .skin-neko-blue .sidebar-menu .treeview-menu{
      background:rgba(0,0,0,.12);
    }
    .skin-neko-blue .sidebar-menu .treeview-menu>li>a{ color:#e3f2fd; }
    .skin-neko-blue .sidebar-menu .treeview-menu>li>a:hover{ color:#fff; }

    /* Badges del menú */
    .menu-badge{
      background:#e3f2fd; color:#0b3a7a; font-weight:600; border-radius:999px; padding:.05rem .45rem; font-size:.72rem;
      margin-left:.35rem;
    }

    /* Avatar en header */
    .navbar .user-image{ box-shadow:0 0 0 2px rgba(255,255,255,.35); }

    /* Sutil separación debajo del header */
    .content-wrapper, .right-side{ background:#f5f7fb; }

    /* Accesibilidad */
    .sidebar-toggle:focus, a:focus{ outline:2px solid #bbdefb; outline-offset:2px; }

    /* Responsivo: logo más compacto */
    @media (max-width: 480px){
      .skin-neko-blue .logo .logo-lg{ display:none; }
      .skin-neko-blue .logo .logo-mini{ display:block; }
    }
    /* --- Layout flexible: llena la pantalla y permite scroll --- */
html, body {
  height: auto;            /* no fijar 100% */
  min-height: 100%;
  background: #f5f7fb;
  overflow-y: auto !important;  /* asegura scroll en body */
}

.wrapper {
  min-height: 100vh;       /* ocupa al menos el alto de la ventana */
  display: flex;
  flex-direction: column;  /* header, contenido, footer en columna */
}

/* Header y footer tamaño natural */
.main-header,
.main-footer {
  flex: 0 0 auto;
}

/* Contenido crece y scrollea si es más alto */
.content-wrapper,
.right-side {
  flex: 1 0 auto;
  background: #f5f7fb;
  min-height: auto;        /* no limites el alto */
  padding-bottom: 20px;    /* separa del footer */
}

/* Sidebar que cubra la altura y no deje huecos */
.main-sidebar { min-height: 100%; }

/* Footer con color consistente */
.main-footer {
  background: #eef2f7;
  border-top: 1px solid rgba(0,0,0,.05);
  color: #334155;
}

  </style>
</head>

  <body class="hold-transition skin-neko-blue sidebar-mini">
    <div class="wrapper">

     <header class="main-header">
  <a href="escritorio.php" class="logo" title="Inicio">
    <span class="logo-mini">
      <i class="fa fa-wrench"></i>
    </span>
    <span class="logo-lg">
      <i class="fa fa-industry" style="margin-right:6px;"></i>
      Ferretería <strong>Neko</strong>
    </span>
  </a>

  <nav class="navbar navbar-static-top" role="navigation" aria-label="Barra principal">
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" aria-label="Mostrar/ocultar menú">
      <span class="sr-only">Navegación</span>
    </a>

    <!-- Acciones rápidas (opcionales) -->
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <!-- Botón de ayuda / contacto -->
        <li>
          <a 
            href="https://api.whatsapp.com/send?phone=51940367492&text=TE%20CONTACTAS%20CON%20EL%20SCRUM%20MASTER"
            target="_blank"
            rel="noopener"
            title="Soporte vía WhatsApp"
            style="color:#25D366;font-weight:500;"
          >
            <i class="fa fa-whatsapp"></i><span class="hidden-xs"> Soporte</span>
          </a>
        </li>

        <!-- Usuario -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="../files/usuarios/<?= $sesImagen ?>" class="user-image" alt="Foto de usuario">
            <span class="hidden-xs"><?= $sesNombre ?></span>
          </a>
          <ul class="dropdown-menu">
            <!-- Cabecera del usuario -->
            <li class="user-header">
              <img src="../files/usuarios/<?= $sesImagen ?>" class="img-circle" alt="Foto de usuario">
              <p>
                <?= $sesNombre ?><br>
                <small>www.FerreteriaNeko.com</small>
              </p>
            </li>
            <!-- Footer del usuario -->
            <li class="user-footer">
              <div class="pull-left">
                <a href="usuario_perfil.php" class="btn btn-default btn-flat">
                  <i class="fa fa-user"></i> Mi Perfil
                </a>
              </div>
              <div class="pull-right">
                <a href="../ajax/usuario.php?op=salir" class="btn btn-default btn-flat">
                  <i class="fa fa-sign-out"></i> Cerrar
                </a>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>

      <aside class="main-sidebar">
        <section class="sidebar">
          <ul class="sidebar-menu">
            <li class="header"></li>

            <?php if (flag('escritorio')): ?>
              <li id="mEscritorio"><a href="escritorio.php"><i class="fa fa-tasks"></i> <span>Escritorio</span></a></li>
            <?php endif; ?>

            <?php if (flag('almacen')): ?>
              <li id="mAlmacen" class="treeview">
                <a href="#"><i class="fa fa-laptop"></i><span>Almacén</span><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li id="lArticulos"><a href="articulo.php"><i class="fa fa-circle-o"></i> Artículos</a></li>
                  <li id="lCategorias"><a href="categoria.php"><i class="fa fa-circle-o"></i> Categorías</a></li>
                </ul>
              </li>
            <?php endif; ?>

            <?php if (flag('compras')): ?>
              <li id="mCompras" class="treeview">
                <a href="#"><i class="fa fa-th"></i><span>Compras</span><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li id="lIngresos"><a href="ingreso.php"><i class="fa fa-circle-o"></i> Ingresos</a></li>
                  <li id="lProveedores"><a href="proveedor.php"><i class="fa fa-circle-o"></i> Proveedores</a></li>
                </ul>
              </li>
            <?php endif; ?>

            <?php if (flag('ventas')): ?>
              <li id="mVentas" class="treeview">
                <a href="#"><i class="fa fa-shopping-cart"></i><span>Ventas</span><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li id="lVentas"><a href="venta.php"><i class="fa fa-circle-o"></i> Ventas</a></li>
                  <li id="lClientes"><a href="cliente.php"><i class="fa fa-circle-o"></i> Clientes</a></li>
                </ul>
              </li>
            <?php endif; ?>

            <?php if (flag('acceso')): ?>
              <li id="mAcceso" class="treeview">
                <a href="#"><i class="fa fa-folder"></i> <span>Acceso</span><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li id="lUsuarios"><a href="usuario.php"><i class="fa fa-circle-o"></i> Usuarios</a></li>
                  <li id="lRol"><a href="rol.php"><i class="fa fa-circle-o"></i> Roles Usuario</a></li>
                </ul>
              </li>
            <?php endif; ?>

            <?php if (flag('consultac')): ?>
              <li id="mConsultaC" class="treeview">
                <a href="#"><i class="fa fa-bar-chart"></i><span>Consulta Compras</span><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li id="lConsulasC"><a href="comprasfecha.php"><i class="fa fa-circle-o"></i> Consulta Compras</a></li>
                </ul>
              </li>
            <?php endif; ?>

            <?php if (flag('consultav')): ?>
              <li id="mConsultaV" class="treeview">
                <a href="#"><i class="fa fa-bar-chart"></i><span>Consulta Ventas</span><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li id="lConsulasV"><a href="ventasfechacliente.php"><i class="fa fa-circle-o"></i> Consulta Ventas</a></li>
                </ul>
              </li>
            <?php endif; ?>

          </ul>
        </section>
      </aside>