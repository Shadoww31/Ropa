<?php

include 'funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

if (isset($_POST['submit'])) {
  $resultado = [
    'error' => false,
    'mensaje' => 'La prenda ' . escapar($_POST['nombre']) . ' ha sido agregada con éxito'
  ];

  $config = include 'config.php';

  try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $ropa = [
      "nombre"   => $_POST['nombre'],
      "marca" => $_POST['marca'],
      "color" => $_POST['color'],
      "precio"    => $_POST['precio'],
      "genero"     => $_POST['genero'],
      "talla" => $_POST['talla'],
    ];

    $consultaSQL = "INSERT INTO ropa (nombre, marca, color, precio, genero, talla)";
    $consultaSQL .= "values (:" . implode(", :", array_keys($ropa)) . ")";

    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute($ropa);

  } catch(PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}
?>

<?php include 'templates/header.php'; ?>

<?php
if (isset($resultado)) {
  ?>
  <div class="container mt-3">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-<?= $resultado['error'] ? 'danger' : 'success' ?>" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2 class="mt-4">Agrega una ropa</h2>
      <hr>
      <form method="post">
        <div class="form-group">
          <label for="nombre">Nombre</label>
          <input type="text" name="nombre" id="nombre" class="form-control">
        </div>
        <div class="form-group">
          <label for="marca">Marca</label>
          <input type="text" name="marca" id="marca" class="form-control">
        </div>
        <div class="form-group">
          <label for="color">Color</label>
          <input type="color" name="color" id="color" class="form-control">
        </div>
        <div class="form-group">
          <label for="precio">Precio</label>
          <input type="text" name="precio" id="precio" class="form-control">
        </div>
        <div class="form-group">
          <label for="genero">Genero</label>
          <input type="text" name="genero" id="genero" class="form-control">
        </div>
        <div class="form-group">
          <label for="talla">Talla</label>
          <input type="text" name="talla" id="talla" class="form-control">
        </div>
        <div class="form-group">
          <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
          <input type="submit" name="submit" class="btn btn-primary" value="Enviar">
          <a class="btn btn-primary" href="index.php">Regresar al inicio</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>