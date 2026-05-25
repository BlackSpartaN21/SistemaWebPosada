Estructura de vendor para recursos externos

Coloca aquí los recursos de terceros para que la app funcione sin Internet.

Estructura sugerida:

- public/vendor/css/
  - bootstrap.min.css
  - datatables.min.css
  - select2.min.css
  - sweetalert2.min.css

- public/vendor/js/
  - jquery-3.7.1.min.js
  - bootstrap.bundle.min.js
  - datatables.min.js
  - select2.min.js
  - sweetalert2.min.js

- public/vendor/font-awesome/
  - css/ (contiene all.min.css o similares)
  - webfonts/ (contiene archivos .woff2/.ttf)

Instrucciones:

1) Mueve los ficheros listados arriba desde `public/css` y `public/js` a las rutas correspondientes dentro de `public/vendor/`.
2) Si algún archivo falta, descarga las versiones indicadas (ej. Bootstrap 5.3.3, jQuery 3.7.1, Select2, DataTables, SweetAlert2, Font Awesome 6.x) y colócalas en estas rutas.
3) Tras mover/añadir los ficheros, ejecuta la tarea de reemplazo de referencias (esta repo ya contiene cambios automáticos para `views/header.php`).

Nota: No realicé descargas externas desde este entorno. Sólo preparé la estructura y README para que coloques los archivos.