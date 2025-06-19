function validarFormulario() {
  const form = document.getElementById("registroForm");
  const clave = form.clave.value;
  const clave2 = form.clave2.value;

  if (clave.length < 6) {
    alert("La contraseña debe tener al menos 6 caracteres.");
    return false;
  }
  if (clave !== clave2) {
    alert("Las contraseñas no coinciden.");
    return false;
  }

  return true;
}
  