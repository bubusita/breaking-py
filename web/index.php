<?php
/**
 * =============================================================================
 *  index.php  —  EL MENÚ PRINCIPAL
 * =============================================================================
 *
 *  Es la traducción de la función codigo() de Breaking_Py.py:
 *
 *      def codigo():
 *          while True:
 *              print("[1] Característica")
 *              print("[2] Elemento")
 *              print("[3] Quiz")
 *              print("[4] Datos curiosos")
 *              primera_elec = input("elige una opcion numerica:")
 *              if primera_elec == "1": ...
 *
 *  ¡ACÁ ESTÁ EL CAMBIO MÁS GRANDE DE TODO EL PROYECTO! Leelo despacio:
 *
 *  ── En Python (consola) ─────────────────────────────────────────────────
 *  El programa arranca, y se queda VIVO dando vueltas en un  while True .
 *  Cuando llama a input() se congela esperando que escribas algo. Todas tus
 *  variables (score, lista_filtrada...) siguen ahí porque el programa nunca
 *  terminó. Por eso podías llamarte a vos mismo con codigo() y seguir.
 *
 *  ── En PHP (web) ───────────────────────────────────────────────────────
 *  El programa arranca cuando alguien pide una página, escupe el HTML y
 *  SE MUERE. No existe input(): PHP no puede "esperar" al usuario, porque para
 *  cuando el usuario lee la página, el programa ya terminó hace rato.
 *
 *  Entonces, ¿cómo se hace un programa interactivo?
 *
 *      1. PHP manda un HTML con un FORMULARIO (o unos enlaces).
 *      2. El usuario completa y aprieta un botón.
 *      3. El navegador vuelve a pedir la página, MANDANDO lo que escribió.
 *      4. PHP arranca de cero, lee ese dato y manda un HTML nuevo.
 *
 *  O sea: el  while True  ya no lo hace tu código. Lo hace el usuario cada vez
 *  que hace clic. Cada clic es una ejecución completa y nueva del programa.
 *
 *  Y como el programa muere en cada vuelta, lo que necesitemos recordar (el
 *  puntaje del quiz, los filtros aplicados) hay que guardarlo en algún lado:
 *  eso es la SESIÓN, y lo vas a ver en quiz.php y en filtrar.php.
 * =============================================================================
 */

// require_once trae el archivo una sola vez, aunque lo pidamos dos veces.
// Es la versión segura de require.
require_once __DIR__ . '/includes/funciones.php';

// El archivo tabla_periodica.php termina con "return $elementos;", así que
// require nos DEVUELVE ese array y lo guardamos en nuestra propia variable.
$elementos = require __DIR__ . '/includes/tabla_periodica.php';

// count() es el len() de Python.
$totalElementos = count($elementos);

// Contamos los radiactivos. En Python habrías escrito:
//     radiactivos = len([el for el in elementos.values() if el["Radiactivo"]])
// En PHP, array_filter() es el equivalente al "list comprehension con if":
// recorre el array y se queda sólo con los que cumplen la condición.
// El  fn($el) => ...  es una "arrow function": una función chiquita escrita en
// una línea, igual que el  lambda  de Python.
$radiactivos = count(array_filter($elementos, fn($el) => $el['Radiactivo']));

$titulo = 'Inicio';
$activo = 'inicio';
require __DIR__ . '/includes/cabecera.php';
?>

<section class="hero">
    <h1>Breaking&nbsp;Py</h1>
    <p class="hero-sub">
        La tabla periódica completa para explorar, filtrar y ponerte a prueba.
    </p>

    <div class="stats">
        <div class="stat">
            <!-- Los datos calculados arriba se imprimen con la etiqueta corta de echo -->
            <span class="stat-num"><?= $totalElementos ?></span>
            <span class="stat-lbl">elementos</span>
        </div>
        <div class="stat">
            <span class="stat-num"><?= $radiactivos ?></span>
            <span class="stat-lbl">radiactivos</span>
        </div>
        <div class="stat">
            <span class="stat-num">7</span>
            <span class="stat-lbl">períodos</span>
        </div>
    </div>
</section>

<!--
    En la consola el menú eran cuatro print() y un input() que leía "1".
    Acá son cuatro enlaces: cada uno lleva a una página distinta.
    Un enlace <a href="..."> es la forma más simple de "elegir una opción".
-->
<section class="menu">
    <a class="tarjeta" href="filtrar.php">
        <span class="tarjeta-num">1</span>
        <h2>Filtrar por característica</h2>
        <p>Buscá elementos combinando filtros: radiactividad, grupo, período, bloque y masa atómica.</p>
    </a>

    <a class="tarjeta" href="elemento.php">
        <span class="tarjeta-num">2</span>
        <h2>Buscar un elemento</h2>
        <p>Escribí el nombre de un elemento y mirá toda su ficha con sus datos.</p>
    </a>

    <a class="tarjeta" href="quiz.php">
        <span class="tarjeta-num">3</span>
        <h2>Quiz</h2>
        <p>Te toca un elemento al azar y tenés que contestar sus siete características.</p>
    </a>

    <a class="tarjeta" href="curiosos.php">
        <span class="tarjeta-num">4</span>
        <h2>Datos curiosos</h2>
        <p>Un dato sorprendente de cada uno de los 118 elementos de la tabla.</p>
    </a>
</section>

<!-- ── La tabla periódica completa ──────────────────────────────────────── -->
<section class="resultados">
    <h2>La tabla completa</h2>
    <p class="bajada">
        Tocá cualquier elemento para ver sus datos.
    </p>

    <?php
    /**
     * Acá se ve para qué sirve haber armado la tabla como un pedazo aparte:
     * ponerla en otra página son dos líneas, y si algún día se le cambia algo
     * (un color, un dato, el orden), cambia en todos lados a la vez.
     *
     * $resaltados = null significa "sin filtros": se dibujan los 118 elementos
     * normales, sin ninguno apagado. La misma tabla que usa filtrar.php.
     */
    $resaltados = null;

    require __DIR__ . '/includes/leyenda.php';
    require __DIR__ . '/includes/tabla.php';
    ?>
</section>

<?php
// Y para cerrar, el pie de página común a todas las páginas.
require __DIR__ . '/includes/pie.php';
