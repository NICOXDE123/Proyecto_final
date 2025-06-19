<form id="registroForm" action="procesar.php" method="POST" onsubmit="return validarFormulario()">
  <input type="text" name="nombre" placeholder="Nombre completo" required><br>
  <input type="email" name="correo" placeholder="Correo electrónico" required><br>
  <input type="password" name="clave" placeholder="Contraseña (mínimo 6 caracteres)" required><br>
  <input type="password" name="clave2" placeholder="Repetir contraseña" required><br>
  <button type="submit">Registrarse</button>
</form>
<script src="script.js"></script>
  