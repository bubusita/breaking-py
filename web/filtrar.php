<?php
/**
 * =============================================================================
 *  filtrar.php  —  OPCIÓN [1]: FILTRAR LOS ELEMENTOS POR CARACTERÍSTICA
 * =============================================================================
 *
 *  Traduce el bloque más largo de Breaking_Py.py: el  if primera_elec == "1" .
 *  Allá la idea era:
 *
 *      lista_filtrada = list(elementos.values())
 *      usadas = []                       # filtros ya aplicados
 *      while True:
 *          # mostrar las características que todavía no se usaron
 *          # pedir una, pedir su valor
 *          lista_filtrada = [el for el in lista_filtrada if ...]
 *          # mostrar cuántos quedaron y preguntar si seguir
 *
 *  ┌──────────────────────────────────────────────────────────────────────┐
 *  │  EL PROBLEMA NUEVO: EL PROGRAMA PIERDE LA MEMORIA EN CADA CLIC       │
 *  └──────────────────────────────────────────────────────────────────────┘
 *
 *  En Python, `lista_filtrada` y `usadas` sobrevivían porque el while nunca
 *  terminaba. En la web, PHP termina y borra TODAS sus variables apenas manda
 *  la página. Si el usuario aplica un filtro y después otro, en la segunda
 *  ejecución el programa ya no se acuerda del primero.
 *
 *  La solución es la SESIÓN: un cajón donde PHP guarda cosas asociadas a ESA
 *  persona (identificada con una cookie), y que sobrevive de una página a la
 *  siguiente. Se usa así:
 *
 *      session_start();               // abrir el cajón (siempre, y ANTES de
 *                                     // imprimir cualquier HTML)
 *      $_SESSION['filtros'] = [...];  // guardar
 *      $x = $_SESSION['filtros'];     // leer
 *      unset($_SESSION['filtros']);   // borrar
 *
 *  $_SESSION es un array normal y corriente, con el superpoder de no borrarse
 *  al terminar el programa.
 * =============================================================================
 */

// La sesión se abre en includes/sesion.php, que le da nombre de cookie propio
// para no pisar la del resto del sitio (que sí tiene login). Ahí está el detalle.
// Sigue teniendo que ir antes de imprimir nada: manda una cookie, y las
// cookies viajan en la cabecera, o sea, antes que el HTML.
require_once __DIR__ . '/includes/sesion.php';

require_once __DIR__ . '/includes/funciones.php';
$elementos = require __DIR__ . '/includes/tabla_periodica.php';

/**
 * Las características que se pueden usar para filtrar.
 * Es el diccionario  opciones  del original, con una descripción más larga.
 */
$caracteristicas = [
    'radiactivo' => 'Radiactividad',
    'grupo'      => 'Grupo',
    'periodo'    => 'Período',
    'bloque'     => 'Bloque',
    'masa'       => 'Masa atómica',
];

/**
 * Los cuatro rangos de masa del original (el "match respuesta" de Python).
 *
 * ┌─ UN ERROR DEL ORIGINAL QUE VALE LA PENA MIRAR ────────────────────────┐
 * │ Allá los rangos eran  1..20 ,  21..100 ,  101..200  y  >200 .          │
 * │ ¿Qué pasa con el neón, que pesa 20,18 u? No es <= 20 ni es >= 21:      │
 * │ ¡no entraba en NINGÚN rango y desaparecía de los resultados!           │
 * │ Lo mismo con el cloro (35,45) no, ese sí entra... pero con cualquier   │
 * │ masa entre 20 y 21, o entre 100 y 101, pasaba lo mismo.                │
 * │                                                                        │
 * │ Con números ENTEROS los rangos "21..100" funcionan bien, pero las      │
 * │ masas atómicas tienen decimales. La forma correcta de partir un rango  │
 * │ continuo es pegar los límites: uno termina justo donde empieza el otro │
 * │ y se decide de qué lado queda el borde. Acá: mayor que min, hasta max. │
 * └────────────────────────────────────────────────────────────────────────┘
 *
 * INF es una constante de PHP que significa "infinito": nada la supera.
 */
$rangosMasa = [
    '1' => ['texto' => 'Hasta 20 u',   'min' => 0,   'max' => 20],
    '2' => ['texto' => '20 – 100 u',   'min' => 20,  'max' => 100],
    '3' => ['texto' => '100 – 200 u',  'min' => 100, 'max' => 200],
    '4' => ['texto' => 'Más de 200 u', 'min' => 200, 'max' => INF],
];


/* ===========================================================================
 *  PASO 1 — ATENDER LO QUE MANDÓ EL USUARIO
 * ===========================================================================
 *
 * $_POST es el hermano de $_GET: trae lo que se envió con un formulario
 * method="post". Se usa POST cuando el envío CAMBIA algo del servidor (acá,
 * los filtros guardados en la sesión), porque una URL con GET se puede
 * recargar o compartir sin querer y repetiría la acción.
 *
 * $_SERVER es otro array que arma PHP solo, con información técnica del
 * pedido. 'REQUEST_METHOD' vale 'GET' o 'POST' según cómo llegó la visita.
 */

// Arrancamos la lista de filtros guardados (vacía la primera vez).
$_SESSION['filtros'] = $_SESSION['filtros'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // texto_recibido(): si alguien manda un array en vez de un texto, se toma
    // como vacío (ver includes/funciones.php). mb_substr() corta el valor a
    // 20 letras: ningún filtro legítimo es más largo, y así nadie puede
    // guardar un texto enorme en la sesión.
    $cual  = texto_recibido($_POST['caracteristica'] ?? '');
    $valor = mb_substr(trim(texto_recibido($_POST['valor'] ?? '')), 0, 20);

    // ¡NUNCA confiar en lo que llega del navegador! Cualquiera puede mandar
    // cualquier cosa, así que verificamos que la característica exista de
    // verdad antes de guardarla. Esto se llama VALIDAR LA ENTRADA.
    if (isset($caracteristicas[$cual]) && $valor !== '') {
        $_SESSION['filtros'][$cual] = $valor;
    }

    /**
     * PATRÓN "POST → REDIRECT → GET" (muy usado, vale la pena aprenderlo)
     *
     * Después de procesar un POST, en vez de mostrar el HTML, le pedimos al
     * navegador que vaya de nuevo a la página con un GET limpio. ¿Por qué?
     * Porque si el usuario aprieta F5 sobre el resultado de un POST, el
     * navegador reenvía el formulario y el filtro se aplicaría dos veces.
     *
     * header() manda una instrucción al navegador. Location = "andá a".
     * exit detiene el programa acá mismo: sin eso, PHP seguiría ejecutando
     * el resto del archivo al pedazo.
     */
    header('Location: filtrar.php');
    exit;
}

// Quitar un filtro suelto:  filtrar.php?quitar=grupo
if (isset($_GET['quitar'])) {
    unset($_SESSION['filtros'][texto_recibido($_GET['quitar'])]); // unset() = del de Python
    header('Location: filtrar.php');
    exit;
}

// Empezar de cero (el "[6] Terminar filtrado" del original).
if (isset($_GET['reiniciar'])) {
    $_SESSION['filtros'] = [];
    header('Location: filtrar.php');
    exit;
}

$filtros = $_SESSION['filtros'];


/* ===========================================================================
 *  PASO 2 — APLICAR TODOS LOS FILTROS GUARDADOS
 * ===========================================================================
 *
 * Acá está el corazón del original:
 *
 *     lista_filtrada = [el for el in lista_filtrada if el["Grupo"] == valor]
 *
 * En PHP la herramienta equivalente es array_filter($array, $funcion): recorre
 * el array y deja pasar sólo los que devuelven true.
 *
 * El  use ($valor)  de abajo es importante: una función anónima de PHP NO ve
 * las variables de afuera (a diferencia de Python, donde una función interna
 * sí las ve). Hay que pasárselas a mano con  use .
 */
$resultado = $elementos;

foreach ($filtros as $cual => $valor) {
    $resultado = match ($cual) {

        // el["Radiactivo"] == (valor == "si")
        'radiactivo' => array_filter(
            $resultado,
            fn($el) => $el['Radiactivo'] === ($valor === 'si')
        ),

        // Ojo: los lantánidos y actínidos tienen texto en vez de número,
        // por eso comparamos como texto y no con ===.
        'grupo' => array_filter(
            $resultado,
            fn($el) => (string) $el['Grupo'] === $valor
        ),

        'periodo' => array_filter(
            $resultado,
            fn($el) => (string) $el['Periodo'] === $valor
        ),

        'bloque' => array_filter(
            $resultado,
            fn($el) => $el['Bloque'] === $valor
        ),

        // El "match respuesta / case 1..4" del original, resuelto con el rango
        // guardado en $rangosMasa.
        'masa' => array_filter(
            $resultado,
            function ($el) use ($valor, $rangosMasa) {
                $r = $rangosMasa[$valor] ?? null;
                if ($r === null) {
                    return true;
                }
                // Mayor QUE el mínimo (excluido) y hasta el máximo (incluido),
                // así los rangos se tocan sin dejar huecos ni superponerse.
                return $el['MasaAtomica'] > $r['min'] && $el['MasaAtomica'] <= $r['max'];
            }
        ),

        // default es el "else" del match. Si llegara una característica rara,
        // no filtramos nada.
        default => $resultado,
    };
}

// Las características que TODAVÍA no se usaron (el  usadas  del original).
// array_diff_key() devuelve las claves del primer array que no están en el segundo.
$disponibles = array_diff_key($caracteristicas, $filtros);

// Los grupos que existen de verdad en los datos, para armar el desplegable.
// array_column() saca una columna entera del array; array_unique() borra
// repetidos. Dos funciones que te van a servir un montón.
$gruposPosibles = array_unique(array_column($elementos, 'Grupo'));
sort($gruposPosibles);

$titulo = 'Filtrar';
$activo = 'filtrar';
require __DIR__ . '/includes/cabecera.php';
?>


<!--
    ── EL ORDEN DE LA PÁGINA ────────────────────────────────────────────────

    Primero la tabla, después los controles. Al principio estaba al revés — los
    filtros arriba y la tabla abajo — y el problema era que los controles
    empujaban a la tabla fuera de la pantalla: había que hacer scroll para ver
    lo que uno acababa de filtrar, que es justo lo que uno quiere mirar.

    La regla general es esa: **arriba va el resultado, no el formulario**. Los
    controles son el medio; lo que la persona vino a ver es el efecto. Cuando
    los dos no entran juntos, el que se va abajo es el formulario.
-->

<div class="filtrar-encabezado">
    <h1 class="titulo-pagina">Filtrar por característica</h1>

    <p class="filtrar-resumen">
        <!--
            El print(f"Se imprimio/eron {len(lista_filtrada)} elementos...")
            del original, pero mostrando singular o plural como corresponde.
        -->
        <strong><?= count($resultado) ?></strong>
        <?= count($resultado) === 1 ? 'elemento cumple' : 'elementos cumplen' ?>
        <?= $filtros === [] ? 'con la tabla completa' : 'con esas condiciones' ?>
        <?php if ($filtros !== []): ?>
            <span class="resultados-ayuda">— los demás quedan atenuados</span>
        <?php endif; ?>
    </p>
</div>

<?php if ($resultado === []): ?>
    <p class="aviso aviso-error">
        No queda ningún elemento con esas condiciones.
        Probá quitando alguno de los filtros de más abajo.
    </p>
<?php endif; ?>

<?php
/**
 * LA TABLA, PRIMERO.
 *
 * ANTES se recorría $resultado y se dibujaban sólo esos elementos. AHORA se
 * dibuja la tabla periódica entera y se le pasa $resultado para que sepa
 * cuáles resaltar.
 *
 * La lógica de filtrado de más arriba no cambió ni una coma: sigue siendo el
 * mismo array_filter() que traduce tus list comprehensions de Python. Lo único
 * distinto es qué hacemos con su resultado.
 *
 * $resaltados vale null cuando no hay ningún filtro puesto: en ese caso la
 * tabla se dibuja entera y sin apagar nada.
 */
$resaltados = $filtros === [] ? null : $resultado;

require __DIR__ . '/includes/leyenda.php';
require __DIR__ . '/includes/tabla.php';
?>

<!-- ── Los controles, abajo y compactos ─────────────────────────────────── -->
<section class="panel-filtros">

    <div class="panel-cabecera">
        <h2>Filtros</h2>

        <?php if ($filtros !== []): ?>
            <!-- Los filtros que ya están puestos, cada uno con su × para sacarlo -->
            <div class="filtros-activos">
                <?php foreach ($filtros as $cual => $valor): ?>
                    <?php
                    // Texto lindo para cada filtro puesto.
                    $texto = match ($cual) {
                        'radiactivo' => 'Radiactivo: ' . ($valor === 'si' ? 'sí' : 'no'),
                        'grupo'      => 'Grupo: ' . $valor,
                        'periodo'    => 'Período: ' . $valor,
                        'bloque'     => 'Bloque: ' . $valor,
                        'masa'       => 'Masa: ' . ($rangosMasa[$valor]['texto'] ?? ''),
                        default      => $cual,
                    };
                    ?>
                    <span class="chip">
                        <?= e($texto) ?>
                        <a class="chip-x" href="filtrar.php?quitar=<?= urlencode($cual) ?>" title="Quitar este filtro">×</a>
                    </span>
                <?php endforeach; ?>

                <a class="boton-secundario chico" href="filtrar.php?reiniciar=1">Empezar de nuevo</a>
            </div>
        <?php else: ?>
            <span class="panel-ayuda">
                Se van sumando uno arriba del otro. Cada característica se usa una sola vez.
            </span>
        <?php endif; ?>
    </div>

    <?php if ($disponibles !== []): ?>
        <!--
            Cada filtro es su propio formulario chiquito, todos en una fila.
            Así cada uno tiene el control que le queda mejor sin necesidad de
            una línea de JavaScript.

            El <input type="hidden"> manda un dato que el usuario no ve ni
            toca: acá, cuál es la característica que se está usando.

            El botón dice "＋" en vez de "Aplicar" para que la fila entre en el
            ancho de la pantalla. Como un dibujito solo no lo puede leer un
            lector de pantalla, lleva un aria-label con el texto de verdad:
            eso es lo que se escucha en voz alta.
        -->
        <div class="grilla-filtros">

            <?php if (isset($disponibles['radiactivo'])): ?>
                <form class="filtro" method="post" action="filtrar.php">
                    <input type="hidden" name="caracteristica" value="radiactivo">
                    <h3>Radiactividad</h3>
                    <div class="filtro-fila">
                        <select name="valor">
                            <option value="si">Sí</option>
                            <option value="no">No</option>
                        </select>
                        <button type="submit" aria-label="Aplicar filtro de radiactividad" title="Aplicar">＋</button>
                    </div>
                </form>
            <?php endif; ?>

            <?php if (isset($disponibles['grupo'])): ?>
                <form class="filtro" method="post" action="filtrar.php">
                    <input type="hidden" name="caracteristica" value="grupo">
                    <h3>Grupo</h3>
                    <div class="filtro-fila">
                        <select name="valor">
                            <?php foreach ($gruposPosibles as $g): ?>
                                <option value="<?= e((string) $g) ?>"><?= e(texto_grupo($g)) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" aria-label="Aplicar filtro de grupo" title="Aplicar">＋</button>
                    </div>
                </form>
            <?php endif; ?>

            <?php if (isset($disponibles['periodo'])): ?>
                <form class="filtro" method="post" action="filtrar.php">
                    <input type="hidden" name="caracteristica" value="periodo">
                    <h3>Período</h3>
                    <div class="filtro-fila">
                        <select name="valor">
                            <?php
                            // range(1, 7) existe igual que en Python, pero acá SÍ
                            // incluye el último número. range(1,7) = [1,2,3,4,5,6,7].
                            foreach (range(1, 7) as $p): ?>
                                <option value="<?= $p ?>">Período <?= $p ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" aria-label="Aplicar filtro de período" title="Aplicar">＋</button>
                    </div>
                </form>
            <?php endif; ?>

            <?php if (isset($disponibles['bloque'])): ?>
                <form class="filtro" method="post" action="filtrar.php">
                    <input type="hidden" name="caracteristica" value="bloque">
                    <h3>Bloque</h3>
                    <div class="filtro-fila">
                        <select name="valor">
                            <option value="s">Bloque s</option>
                            <option value="p">Bloque p</option>
                            <option value="d">Bloque d</option>
                            <option value="f">Bloque f</option>
                        </select>
                        <button type="submit" aria-label="Aplicar filtro de bloque" title="Aplicar">＋</button>
                    </div>
                </form>
            <?php endif; ?>

            <?php if (isset($disponibles['masa'])): ?>
                <form class="filtro" method="post" action="filtrar.php">
                    <input type="hidden" name="caracteristica" value="masa">
                    <h3>Masa atómica</h3>
                    <div class="filtro-fila">
                        <select name="valor">
                            <?php foreach ($rangosMasa as $clave => $r): ?>
                                <option value="<?= e($clave) ?>"><?= e($r['texto']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" aria-label="Aplicar filtro de masa atómica" title="Aplicar">＋</button>
                    </div>
                </form>
            <?php endif; ?>

        </div>
    <?php else: ?>
        <p class="panel-ayuda">
            Ya usaste todas las características disponibles.
            <a href="filtrar.php?reiniciar=1">Empezá de nuevo</a> para probar otra combinación.
        </p>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/pie.php';
