/* ==========================================================================
   analytics.js  —  LA CONFIGURACIÓN DE GOOGLE ANALYTICS

   Es el bloque que Google indica pegar en el HTML, movido a un archivo
   propio: la política de seguridad del sitio (Content-Security-Policy, en
   includes/funciones.php) no deja ejecutar JavaScript escrito adentro de la
   página, sólo el que viene en archivos del propio sitio.

   El identificador no está escrito acá: viene en el atributo data-id de la
   etiqueta <script> que carga este archivo (ver includes/cabecera.php), así
   se define en un solo lugar. document.currentScript es "la etiqueta
   <script> que me está ejecutando ahora".
   ========================================================================== */
window.dataLayer = window.dataLayer || [];
function gtag() { dataLayer.push(arguments); }
gtag('js', new Date());
gtag('config', document.currentScript.dataset.id);
