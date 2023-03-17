<?php
include 'funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

$config = include 'config.php';

$resultado = [
  'error' => false,
  'mensaje' => ''
];

if (!isset($_GET['id'])) {
  $resultado['error'] = true;
  $resultado['mensaje'] = 'La prenda no existe';
}

if (isset($_POST['submit'])) {
  try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $ropa = [
      "id"        => $_GET['id'],
      "nombre"    => $_POST['nombre'],
      "marca"    => $_POST['marca'],
      "color"  => $_POST['color'],
      "precio"     => $_POST['precio'],
      "genero"      => $_POST['genero']
      "talla"    => $_POST['talla'],
    ];
    
    $consultaSQL = "UPDATE ropas SET
        nombre = :nombre,
        marca = :marca,
        color = :color,
        precio = :precio,
        genero = :genero,
        talla = :talla,
        updated_at = NOW()
        WHERE id = :id";
    $consulta = $conexion->prepare($consultaSQL);
    $consulta->execute($ropa);

  } catch(PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}

try {
  $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
  $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);
    
  $id = $_GET['id'];
  $consultaSQL = "SELECT * FROM ropas WHERE id =" . $id;

  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $ropa = $sentencia->fetch(PDO::FETCH_ASSOC);

  if (!$ropa) {
    $resultado['error'] = true;
    $resultado['mensaje'] = 'No se ha encontrado la ropa';
  }

} catch(PDOException $error) {
  $resultado['error'] = true;
  $resultado['mensaje'] = $error->getMessage();
}
?>

<?php require "templates/header.php"; ?>

<?php
if ($resultado['error']) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php
if (isset($_POST['submit']) && !$resultado['error']) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-success" role="alert">
          La ropa ha sido actualizada correctamente
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php
if (isset($ropa) && $ropa) {
  ?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="mt-4">Editando la ropa <?= escapar($ropa['nombre']) . ' ' . escapar($ropa['marca'])  ?></h2>
        <hr>
        <form method="post">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?= escapar($ropa['nombre']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="marca">Marca</label>
            <input type="text" name="marca" id="marca" value="<?= escapar($ropa['marca']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="color">Color</label>
            <input type="text" name="color" id="color" value="<?= escapar($ropa['color']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="precio">Precio</label>
            <input type="decimal" name="precio" id="precio" value="<?= escapar($ropa['precio']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="genero">Genero</label>
            <input type="text" name="genero" id="genero" value="<?= escapar($ropa['genero']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="talla">Talla</label>
            <input type="text" name="talla" id="talla" value="<?= escapar($ropa['talla']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
            <input type="submit" name="submit" class="btn btn-primary" value="Actualizar">
            <a class="btn btn-primary" href="index.php">Regresar al inicio</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php require "templates/footer.php"; ?>