<?php
include 'includes/validar.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nombre = validar($_POST['nombre']);
  $correo = validar($_POST['correo']);
  $clave = $_POST['clave'];
  $clave2 = $_POST['clave2'];

  if ($clave !== $clave2) {
    die("Las contraseñas no coinciden.");
  }

  if (strlen($clave) < 6) {
    die("La contraseña debe tener al menos 6 caracteres.");
  }

  // Simular almacenamiento
  echo "Usuario $nombre registrado con éxito.";
}
?>
  