<?php
/**
 * =============================================================================
 *  python_original/index.php  —  EL VISOR DEL CÓDIGO ORIGINAL
 * =============================================================================
 *
 *  Muestra en pantalla los archivos .py originales del proyecto, para que se
 *  pueda comparar el programa de consola con esta versión web.
 *
 *  ┌──────────────────────────────────────────────────────────────────────┐
 *  │  MOSTRAR ARCHIVOS ES LA COSA MÁS PELIGROSA QUE VA A HACER ESTE SITIO │
 *  └──────────────────────────────────────────────────────────────────────┘
 *
 *  Suena inofensivo, pero "leé el archivo que te pida el usuario" es la puerta
 *  de entrada de dos ataques clásicos. Vale la pena entenderlos, porque son de
 *  los errores más comunes que se cometen al empezar:
 *
 *  1. PATH TRAVERSAL ("atravesar carpetas"). Si escribiéramos esto:
 *
 *         $texto = file_get_contents($_GET['archivo']);     // ¡JAMÁS!
 *
 *     alguien podría pedir  ?archivo=../includes/funciones.php  y leer código
 *     que no le corresponde. Y con  ../../../../etc/passwd  se lleva archivos
 *     del sistema. Los  ..  significan "subí una carpeta", y encadenándolos se
 *     sale de donde uno creía tener encerrado al visitante.
 *
 *     La defensa NO es "revisar que no tenga puntos" (siempre hay una forma
 *     de escribirlo distinto y burlar el filtro). La defensa es no usar nunca
 *     lo que manda el usuario para armar una ruta: se hace una LISTA BLANCA de
 *     los archivos permitidos y lo que llega sólo sirve para ELEGIR de esa
 *     lista. Si lo pedido no está en la lista, no existe. Punto.
 *
 *  2. XSS. El contenido del archivo se imprime dentro del HTML, así que pasa
 *     por e() como todo lo demás. Sin eso, un archivo que contenga
 *     <script> haría que el navegador lo ejecute en vez de mostrarlo.
 *
 *  Además, el archivo .htaccess de esta carpeta se asegura de que los .py se
 *  bajen como texto y nunca se ejecuten en el servidor.
 * =============================================================================
 */

// Esta página está una carpeta adentro: los require llevan ../ y $base también.
require_once __DIR__ . '/../includes/funciones.php';

/**
 * LA LISTA BLANCA. Sólo estos archivos se pueden mirar, y punto.
 * La clave es lo que viaja en la URL; el valor, cómo se describe en pantalla.
 */
$archivos = [
    'Breaking_Py.py' => [
        'titulo' => 'Breaking_Py.py',
        'que_es' => 'El programa principal: el menú, el quiz y los filtros. '
                  . 'Acá está la función codigo() que era el corazón de todo.',
        'ahora'  => 'Hoy es index.php + filtrar.php + elemento.php + quiz.php',
    ],
    'datos.py' => [
        'titulo' => 'datos.py',
        'que_es' => 'La función Datos(), con el diccionario de los 118 datos curiosos.',
        'ahora'  => 'Hoy es curiosos.php + includes/datos_curiosos.php',
    ],
    'tabla_periodica.py' => [
        'titulo' => 'tabla_periodica.py',
        'que_es' => 'El diccionario con los datos de los 118 elementos.',
        'ahora'  => 'Hoy es includes/tabla_periodica.php',
    ],
];

/**
 * Qué archivo se pidió. array_key_first() devuelve la primera clave del array,
 * así que si no pidieron nada (o pidieron algo que no está en la lista) se
 * muestra el primero y listo. Nunca se usa $_GET para armar una ruta.
 */
$pedido = $_GET['archivo'] ?? '';
$elegido = isset($archivos[$pedido]) ? $pedido : array_key_first($archivos);

// La ruta se arma con __DIR__ (la carpeta de ESTE archivo) + un nombre que
// salió de nuestra lista, no del usuario. Por eso no puede apuntar a otro lado.
$ruta = __DIR__ . '/' . $elegido;

// file_get_contents() lee un archivo entero y lo devuelve como texto. Es el
//     with open(ruta) as f: texto = f.read()
// de Python, pero en una sola función.
$codigo = is_readable($ruta) ? file_get_contents($ruta) : null;

$base   = '../';
$titulo = 'Código original';
require __DIR__ . '/../includes/cabecera.php';
?>

<h1 class="titulo-pagina">El código original en Python</h1>
<p class="bajada">
    Este es el programa de consola tal como fue escrito, sin ningún cambio.
    Compararlo con la versión web es la mejor forma de ver qué se traduce
    directo y qué hay que replantear.
</p>

<p class="aviso">
    <strong>Autoría:</strong> el programa original en Python es de
    Julia López Rocchi y Joaquín Moyano.
    La versión web mantiene su lógica y su forma de resolver las cosas.
</p>

<!-- Las pestañas para elegir archivo -->
<nav class="pestanas">
    <?php foreach ($archivos as $nombre => $info): ?>
        <a href="?archivo=<?= urlencode($nombre) ?>"
           class="<?= $nombre === $elegido ? 'activa' : '' ?>">
            <?= e($info['titulo']) ?>
        </a>
    <?php endforeach; ?>
</nav>

<?php if ($codigo === null): ?>
    <p class="aviso aviso-error">No se pudo leer el archivo.</p>
<?php else: ?>
    <section class="visor">
        <header class="visor-cabecera">
            <div>
                <h2><?= e($archivos[$elegido]['titulo']) ?></h2>
                <p class="visor-que-es"><?= e($archivos[$elegido]['que_es']) ?></p>
            </div>
            <div class="visor-datos">
                <!--
                    substr_count() cuenta cuántas veces aparece algo dentro de
                    un texto: acá, cuántos saltos de línea tiene el archivo.
                    number_format() le pone el separador de miles.
                -->
                <span><?= number_format(substr_count($codigo, "\n") + 1) ?> líneas</span>
                <span><?= number_format(strlen($codigo) / 1024, 1) ?> KB</span>
            </div>
        </header>

        <p class="visor-ahora">→ <?= e($archivos[$elegido]['ahora']) ?></p>

        <!--
            <pre> conserva los espacios y los saltos de línea tal cual están
            en el texto. Sin él, el navegador aplastaría toda la indentación
            de Python y el código quedaría ilegible.

            El contenido pasa por e() aunque sea un archivo nuestro: la regla
            "todo lo que se imprime se escapa" no tiene excepciones cómodas.
        -->
        <pre class="codigo"><code><?= e($codigo) ?></code></pre>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/../includes/pie.php';
