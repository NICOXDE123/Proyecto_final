<?php
session_start(); // Inicia la sesión

session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión

// Redirigir al login o portada
header("Location: login.php"); // o "index.html" si usas una portada
exit();
?>