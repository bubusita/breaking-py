<?php defined('BREAKING_PY_VERSION') || exit;   // abierto directo desde el navegador: no hace nada (ver includes/.htaccess) ?>
</main>

<footer class="pie">
    <p>
        <strong>Breaking Py</strong> — versión web del programa de tabla periódica.
        Los datos son de los 118 elementos conocidos.
    </p>
    <p>
        Código original en Python de
        <strong>Julia López Rocchi</strong> y <strong>Joaquín Moyano</strong>.
        <!--
            $base sale de cabecera.php: vale '' en las páginas de la raíz y
            '../' cuando la página está una carpeta más adentro. Así este mismo
            enlace funciona desde cualquiera de las dos.
        -->
        <a href="<?= $base ?>python_original/">Ver el código original →</a>
    </p>
    <p class="pie-mini">
        Versión <?= BREAKING_PY_VERSION ?> · hecho con PHP
    </p>
</footer>

</body>
</html>
<?php
/**
 * =============================================================================
 *  pie.php  —  EL CIERRE DE TODAS LAS PÁGINAS
 * =============================================================================
 *
 *  Fijate que este archivo EMPIEZA con HTML y recién al final abre <?php.
 *  Eso está bien: PHP siempre arranca en modo HTML.
 *
 *  BREAKING_PY_VERSION es una CONSTANTE que definimos nosotros en
 *  includes/funciones.php. Antes al lado iba PHP_VERSION, la versión exacta
 *  de PHP del servidor: se sacó porque le sirve a un atacante para buscar
 *  fallas conocidas de esa versión, y a quien visita no le aporta nada.
 *  Las constantes se parecen a las variables pero no llevan  $  adelante y no
 *  se pueden cambiar una vez definidas: son para los datos que no varían
 *  durante el programa, como el número de versión.
 *
 *  Detalle: este archivo NO cierra con  ? >  al final. Es una convención de PHP:
 *  si un archivo termina en código PHP, no se pone la etiqueta de cierre, porque
 *  cualquier espacio o salto de línea que quede después se enviaría al navegador
 *  y puede romper cosas (por ejemplo, las cookies y las sesiones).
 * =============================================================================
 */
