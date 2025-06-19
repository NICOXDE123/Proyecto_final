<?php
function validar($dato) {
  $dato = trim($dato);
  $dato = stripslashes($dato);
  $dato = htmlspecialchars($dato);
  return $dato;
}
?>
  