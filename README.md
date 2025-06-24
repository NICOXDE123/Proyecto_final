
# Proyecto Final — CRUD de Proyectos Personales

Este es un sistema CRUD desarrollado como Proyecto Integrado Final para la asignatura de Diseño y Desarrollo Web + IA. Permite a un usuario autenticado gestionar proyectos personales mediante una interfaz web responsiva.

## 🌐 Tecnologías utilizadas

- PHP 8
- MySQL
- Bootstrap 5 (vía CDN)
- API REST personalizada (con soporte para _method)
- Curl (para comunicación con la API)
- Servidor institucional UCT

---

## 🧩 Funcionalidades implementadas

### 🔐 Autenticación
- Sistema de login con validación de credenciales desde base de datos.
- Inicio de sesión protegido con `$_SESSION`.

### 📄 CRUD de Proyectos
- **Agregar proyecto:** formulario con subida de imagen, descripción, URLs.
- **Editar proyecto:** permite modificar los campos existentes y reemplazar la imagen.
- **Eliminar proyecto:** acción segura mediante `_method=DELETE` con confirmación.
- **Visualización:** panel que muestra todos los proyectos en tarjetas Bootstrap.

### 🗂️ API REST propia
- El backend implementa un archivo `Proyectos.php` que permite:
  - `GET` para listar o ver un proyecto
  - `POST` para crear un nuevo proyecto
  - `PATCH` para editar
  - `DELETE` para eliminar
- Protegido con validación de sesión (solo usuarios logueados pueden editar).

---

## 🧪 Validaciones y mejoras

- Validación de formato de imágenes (`jpg`, `png`, `webp`).
- Límite de tamaño de imagen: 2MB.
- Mensajes de éxito y error que informan al usuario si la acción fue completada o no.
- Redirecciones automáticas tras operaciones exitosas.
- Control de errores `curl` y fallos en la API.

---

## 🤖 Uso de Inteligencia Artificial

- ChatGPT fue utilizado para:
  - Optimizar el diseño del sistema CRUD.
  - Generar estructura segura de la API REST.
  - Identificar errores comunes de PHP y proponer soluciones.
  - Mejorar el diseño visual con Bootstrap.
  - Generar mensajes de retroalimentación al usuario.

---

## 📁 Estructura del proyecto

Proyecto_final/
├── api/
│ ├── config.php
│ └── Proyectos.php
├── crud/
│ ├── index.php
│ ├── add.php
│ ├── edit.php
│ ├── delete.php
├── uploads/
├── login.php
├── logout.php
└── README.md

## 📌 Requisitos para ejecutar

- Servidor PHP 7.4+ o 8.x
- MySQL
- Carpeta `uploads/` con permisos de escritura (`chmod 755` o `777`)
- Extensión `curl` habilitada en PHP

---

## 📅 Autor y entrega

- Autor: **Nicolás Huenchual**
- Fecha de entrega: **24 de junio de 2025**
- Profesor: **[Nombre del docente]**
- Asignatura: **Diseño y Desarrollo Web + IA**

# Proyecto Final — CRUD de Proyectos Personales

🔗 **Enlace en línea:**  
[https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final](https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final)

📁 **Repositorio en GitHub:**  
[https://github.com/nicolas-huenchual/Proyecto_final.git](https://github.com/nicolas-huenchual/Proyecto_final.git)