<?php
/**
 * =============================================================================
 *  tabla_muda.php  —  EJERCICIO: TABLA MUDA, FICHA Y ORDEN
 * =============================================================================
 *
 *  Tema: propiedades periódicas y tabla muda. La consigna es:
 *
 *      Elegí tres elementos: uno con ALTO carácter metálico, uno INTERMEDIO
 *      y uno BAJO.
 *        a. Ubicalos en la tabla muda y armá una ficha de cada uno (radio,
 *           electronegatividad, energía de ionización, carácter metálico,
 *           tendencia a perder o ganar, ion esperable, tipo de elemento)
 *           sin escribir su nombre.
 *        b. Ordenalos de menor a mayor según las cuatro propiedades.
 *
 *  ── LOS SUB-EJERCICIOS ─────────────────────────────────────────────────
 *
 *  Arriba hay un selector para elegir CON QUÉ PROPIEDAD se piden los tres
 *  elementos: carácter metálico (como en la consigna de arriba), radio
 *  atómico, electronegatividad o energía de ionización. "Radio grande" y
 *  "alto carácter metálico" son el mismo lugar de la tabla dicho de otra
 *  forma; practicar con cada una obliga a relacionar las cuatro.
 *
 *  Por dentro, los tres niveles se llaman siempre por el carácter metálico
 *  ('alto', 'intermedio', 'bajo'). Cada sub-ejercicio sólo cambia cómo se
 *  DICE cada nivel: el nivel 'alto' es "radio atómico grande" en uno y
 *  "electronegatividad baja" en otro.
 *
 *  ── LAS LETRAS SE SORTEAN ──────────────────────────────────────────────
 *
 *  En cada ronda se sortea qué nivel le toca a cada letra: A no es siempre
 *  el más metálico. Así el orden de la parte b cambia cada vez y hay que
 *  razonarlo, no memorizar "C < B < A".
 *
 *  ── LOS PASOS ──────────────────────────────────────────────────────────
 *
 *      1. ELEGIR: la tabla muda (sin símbolos ni colores) y se tocan los
 *         casilleros de A, B y C. Cada toque se valida en el servidor.
 *      2. FICHA: con los tres elegidos, se completa la ficha y el orden.
 *      3. CORRECCIÓN: se revela qué elemento era cada uno.
 *
 *  En qué paso estamos lo dice la SESIÓN: qué letras ya tienen elemento y si
 *  ya se corrigió. Es la forma web de un programa "por etapas": en la consola
 *  habrían sido tres input() seguidos; acá son tres visitas a la misma página.
 *
 *  ── POR QUÉ HAY QUE ELEGIRLOS "EN DIAGONAL" ─────────────────────────────
 *
 *  Las tendencias van en dos direcciones a la vez: el radio crece hacia la
 *  izquierda Y hacia abajo. Si el más metálico está más a la izquierda que
 *  el intermedio pero también más ARRIBA, las dos tendencias tiran para lados
 *  distintos y no se puede saber cuál tiene el radio más grande sin mirar los
 *  datos. Por eso, para que la parte b se pueda resolver sólo con las
 *  tendencias, el más metálico tiene que estar a la izquierda y/o abajo del
 *  intermedio, y el intermedio, a la izquierda y/o abajo del menos metálico.
 *  La página lo controla y, si no se cumple, explica por qué.
 * =============================================================================
 */

session_start();

require_once __DIR__ . '/includes/quimica.php';
$elementos = require __DIR__ . '/includes/tabla_periodica.php';

$ejercicio = 'tabla_muda';

// Los tres niveles, de más a menos metálico: qué categorías de la tabla los
// cumplen y dónde buscarlos.
$niveles = [
    'alto' => [
        'categorias' => ['alcalino', 'alcalinoterreo'],
        'pista'      => 'abajo a la izquierda, en los grupos 1 y 2',
    ],
    'intermedio' => [
        'categorias' => ['metaloide', 'postransicion'],
        'pista'      => 'cerca de la línea en escalera que separa los metales de los no metales',
    ],
    'bajo' => [
        'categorias' => ['nometal', 'halogeno'],
        'pista'      => 'arriba a la derecha, sin llegar a la columna de los gases nobles',
    ],
];

// Los sub-ejercicios: cómo se dice cada nivel según la propiedad elegida.
// Fijate que en la electronegatividad y la energía de ionización el nivel
// 'alto' (el más metálico) es el de valor BAJO: van al revés.
$modos = [
    'cm' => [
        'nombre' => 'Carácter metálico',
        'pide'   => ['alto' => 'alto carácter metálico', 'intermedio' => 'carácter metálico intermedio',
            'bajo' => 'bajo carácter metálico'],
    ],
    'radio' => [
        'nombre' => 'Radio atómico',
        'pide'   => ['alto' => 'radio atómico grande', 'intermedio' => 'radio atómico intermedio',
            'bajo' => 'radio atómico pequeño'],
    ],
    'en' => [
        'nombre' => 'Electronegatividad',
        'pide'   => ['alto' => 'electronegatividad baja', 'intermedio' => 'electronegatividad intermedia',
            'bajo' => 'electronegatividad alta'],
    ],
    'ei' => [
        'nombre' => 'Energía de ionización',
        'pide'   => ['alto' => 'energía de ionización baja', 'intermedio' => 'energía de ionización intermedia',
            'bajo' => 'energía de ionización alta'],
    ],
];

// Las filas de la ficha, con las opciones de cada una.
$filasFicha = [
    'radio'     => ['Radio atómico',            ['pequeño', 'intermedio', 'grande']],
    'en'        => ['Electronegatividad',       ['baja', 'intermedia', 'alta']],
    'ei'        => ['Energía de ionización',    ['baja', 'intermedia', 'alta']],
    'cm'        => ['Carácter metálico',        ['bajo', 'intermedio', 'alto']],
    'tendencia' => ['Tendencia a perder/ganar', ['perder', 'compartir', 'ganar']],
    'ion'       => ['Tipo de ion esperable',    ['catión', 'ninguno', 'anión']],
    'tipo'      => ['Tipo de elemento',         ['metal', 'metaloide', 'no metal']],
];

// Para la parte b: las seis formas de ordenar tres letras.
$ordenes = ['A < B < C', 'A < C < B', 'B < A < C', 'B < C < A', 'C < A < B', 'C < B < A'];


/**
 * Una ronda nueva: sin elementos elegidos y con los niveles repartidos al
 * azar entre las tres letras.
 *
 * array_combine(claves, valores) arma un array asociativo juntando dos
 * listas: es el  dict(zip(claves, valores))  de Python.
 */
function ronda_tabla_muda(string $modo): array
{
    $orden = ['alto', 'intermedio', 'bajo'];
    shuffle($orden);

    return [
        'modo'     => $modo,
        'niveles'  => array_combine(['A', 'B', 'C'], $orden),   // ['A' => 'bajo', ...]
        'elegidos' => [],
    ];
}


/**
 * Qué nivel de carácter metálico tiene una categoría ('alto', 'intermedio' o
 * 'bajo'), o null si no sirve para este ejercicio.
 */
function nivel_metalico(string $categoria, array $niveles): ?string
{
    foreach ($niveles as $nivel => $datos) {
        if (in_array($categoria, $datos['categorias'], true)) {
            return $nivel;
        }
    }

    return null;
}


/**
 * Revisa si un casillero sirve para el nivel que se está eligiendo.
 * Devuelve null si sirve, o el texto que explica por qué no.
 *
 * $pide es cómo se dice cada nivel en el sub-ejercicio elegido, así el
 * mensaje habla de "radio pequeño" o de "bajo carácter metálico" según toque.
 */
function problema_con(array $el, string $nivelPedido, array $niveles, array $pide): ?string
{
    // mb_strtolower(): en el medio de una oración va "el hierro", no "el Hierro".
    $nombre    = 'el ' . mb_strtolower($el['Nombre']) . " ({$el['Simbolo']})";
    $categoria = categoria_elemento($el);

    if ($el['NumeroAtomico'] === 1) {
        return "Ese es {$nombre}: está en el grupo 1 pero es un no metal, y no se parece a ningún otro elemento. Mejor elegí otro.";
    }

    $motivo = match ($categoria) {
        'noble'      => "Ese es {$nombre}, un gas noble: casi no forma compuestos y no se le mide la electronegatividad. Para esta ficha no sirve.",
        'transicion' => "Ese es {$nombre}, un metal de transición. Es un metal, pero sus propiedades no siguen tan prolijas las tendencias: usá los grupos 1, 2 y 13 al 17.",
        'lantanido', 'actinido' => "Ese es {$nombre}, de las dos filas de abajo. Para este ejercicio usá los grupos 1, 2 y 13 al 17.",
        'desconocido' => "Ese es {$nombre}: se fabricó en un laboratorio y casi no se conocen sus propiedades.",
        default      => null,
    };

    if ($motivo !== null) {
        return $motivo;
    }

    $nivelReal = nivel_metalico($categoria, $niveles);

    if ($nivelReal !== $nivelPedido) {
        return "Ese es {$nombre}, que tiene {$pide[$nivelReal]}. Para {$pide[$nivelPedido]} buscá "
            . "{$niveles[$nivelPedido]['pista']}.";
    }

    return null;
}


/**
 * ¿Se puede ordenar $masMetalico antes que $menosMetalico sólo con las
 * tendencias? Hace falta que esté a la izquierda y/o abajo del otro.
 */
function en_diagonal(array $masMetalico, array $menosMetalico): bool
{
    [$fila1, $col1] = posicion_tabla($masMetalico);
    [$fila2, $col2] = posicion_tabla($menosMetalico);

    return $col1 <= $col2 && $fila1 >= $fila2;
}


/**
 * Lo que tendría que decir la ficha de un elemento según su nivel.
 *
 * El alto y el bajo son siempre iguales. El intermedio depende de lo que se
 * eligió: un metaloide (comparte electrones) o un "otro metal" como el
 * aluminio (los pierde).
 */
function ficha_esperada(string $nivel, array $el): array
{
    if ($nivel === 'alto') {
        return ['radio' => 'grande', 'en' => 'baja', 'ei' => 'baja', 'cm' => 'alto',
            'tendencia' => 'perder', 'ion' => 'catión', 'tipo' => 'metal'];
    }
    if ($nivel === 'bajo') {
        return ['radio' => 'pequeño', 'en' => 'alta', 'ei' => 'alta', 'cm' => 'bajo',
            'tendencia' => 'ganar', 'ion' => 'anión', 'tipo' => 'no metal'];
    }

    $esMetaloide = categoria_elemento($el) === 'metaloide';

    return ['radio' => 'intermedio', 'en' => 'intermedia', 'ei' => 'intermedia', 'cm' => 'intermedio',
        'tendencia' => $esMetaloide ? 'compartir' : 'perder',
        'ion'       => $esMetaloide ? 'ninguno' : 'catión',
        'tipo'      => $esMetaloide ? 'metaloide' : 'metal'];
}


/* ===========================================================================
 *  CAMBIAR DE SUB-EJERCICIO, EMPEZAR DE NUEVO, REINICIAR
 * =========================================================================== */

$modoActual = $_SESSION['tabla_muda']['modo'] ?? 'cm';

// ?modo=radio → cambia de sub-ejercicio y empieza una ronda nueva. Sólo se
// aceptan los modos que existen: lo que llega por la URL no se usa a ciegas.
if (isset($_GET['modo']) && array_key_exists($_GET['modo'], $modos)) {
    $_SESSION['tabla_muda'] = ronda_tabla_muda($_GET['modo']);
    header('Location: tabla_muda.php');
    exit;
}

if (isset($_GET['nuevo'])) {
    $_SESSION['tabla_muda'] = ronda_tabla_muda($modoActual);
    header('Location: tabla_muda.php');
    exit;
}

if (isset($_GET['reiniciar'])) {
    reiniciar_marcador($ejercicio);
    $_SESSION['tabla_muda'] = ronda_tabla_muda($modoActual);
    header('Location: tabla_muda.php');
    exit;
}

// Si no había ronda (o era de la versión vieja, sin niveles), una nueva.
if (!isset($_SESSION['tabla_muda']['niveles'])) {
    $_SESSION['tabla_muda'] = ronda_tabla_muda($modoActual);
}

$tm = &$_SESSION['tabla_muda'];
// El  &  crea una REFERENCIA: $tm no es una copia sino otro nombre para el
// mismo cajón de la sesión. Lo que se cambie en $tm queda guardado en la
// sesión. En Python pasa solo con los diccionarios; en PHP hay que pedirlo.

$pide = $modos[$tm['modo']]['pide'];

// Qué se pide en cada letra en esta ronda: ['A' => ['nivel' => 'bajo',
// 'texto' => 'radio atómico pequeño'], ...]
$pedidos = [];
foreach ($tm['niveles'] as $letra => $nivel) {
    $pedidos[$letra] = ['nivel' => $nivel, 'texto' => $pide[$nivel]];
}

// Al revés: qué letra le tocó a cada nivel. ['alto' => 'B', ...]
$letraDeNivel = array_flip($tm['niveles']);

// El orden correcto de menor a mayor. implode() pega las letras con ' < '.
$orden = fn(string ...$nivelesDeMenorAMayor) => implode(' < ', array_map(
    fn($nivel) => $letraDeNivel[$nivel],
    $nivelesDeMenorAMayor
));
$ordenCorrecto = [
    'radio' => $orden('bajo', 'intermedio', 'alto'),   // el radio crece hacia abajo a la izquierda
    'en'    => $orden('alto', 'intermedio', 'bajo'),   // la electronegatividad, hacia arriba a la derecha
    'ei'    => $orden('alto', 'intermedio', 'bajo'),   // la energía de ionización, igual
    'cm'    => $orden('bajo', 'intermedio', 'alto'),   // el carácter metálico, como el radio
];

// La próxima letra que falta elegir (null si ya están las tres).
$letraActual = null;
foreach (array_keys($pedidos) as $letra) {
    if (!isset($tm['elegidos'][$letra])) {
        $letraActual = $letra;
        break;
    }
}


/* ===========================================================================
 *  ATENDER LOS FORMULARIOS
 *
 *  Esta página tiene DOS formularios distintos: el de tocar un casillero
 *  (manda "z") y el de la ficha (manda "ficha"). Se distinguen por qué campo
 *  llegó en el POST.
 * =========================================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['z']) && $letraActual !== null) {

    $clave = clave_por_z($elementos, (int) $_POST['z']);
    $nivel = $pedidos[$letraActual]['nivel'];

    if ($clave === null) {
        $tm['mensaje'] = ['error', 'Ese casillero no existe.'];
    } elseif (in_array($clave, $tm['elegidos'], true)) {
        $tm['mensaje'] = ['error', 'Ese casillero ya lo elegiste para otra letra.'];
    } elseif (($problema = problema_con($elementos[$clave], $nivel, $niveles, $pide)) !== null) {
        $tm['mensaje'] = ['error', $problema];
    } else {
        $tm['elegidos'][$letraActual] = $clave;
        [$fila, $col] = posicion_tabla($elementos[$clave]);
        $siguiente = ['A' => 'B', 'B' => 'C', 'C' => null][$letraActual];
        $tm['mensaje'] = ['ok', "¡Bien! El casillero del período {$fila}, grupo {$col} es tu elemento {$letraActual}."
            . ($siguiente !== null ? " Ahora seguí con el {$siguiente}." : '')];

        // Con los tres elegidos, revisamos que se puedan ordenar (ver arriba):
        // el alto contra el intermedio, y el intermedio contra el bajo.
        if (count($tm['elegidos']) === 3) {
            foreach ([['alto', 'intermedio'], ['intermedio', 'bajo']] as [$mas, $menos]) {
                $letraMas   = $letraDeNivel[$mas];
                $letraMenos = $letraDeNivel[$menos];

                if (!en_diagonal($elementos[$tm['elegidos'][$letraMas]], $elementos[$tm['elegidos'][$letraMenos]])) {
                    // Se vuelve a elegir el que se eligió último de los dos.
                    // max('A', 'C') da 'C': las letras se comparan en orden alfabético.
                    $otraVez = max($letraMas, $letraMenos);
                    unset($tm['elegidos'][$otraVez]);

                    $tm['mensaje'] = ['error', "Los tres cumplen lo pedido, pero {$letraMas} y {$letraMenos} no se "
                        . 'pueden ordenar sólo con las tendencias: uno está más a la izquierda pero el otro más '
                        . "abajo, y las dos tendencias tiran para lados distintos. Volvé a elegir el {$otraVez}: "
                        . "{$letraMas} ({$pide[$mas]}) tiene que quedar a la izquierda y/o abajo de "
                        . "{$letraMenos} ({$pide[$menos]})."];
                    break;
                }
            }
        }
    }

    header('Location: tabla_muda.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ficha']) && $letraActual === null
    && !isset($tm['aciertos'])) {

    $aciertos = [];
    $tuyas    = [];

    foreach ($tm['elegidos'] as $letra => $clave) {
        $esperada = ficha_esperada($tm['niveles'][$letra], $elementos[$clave]);

        foreach (array_keys($filasFicha) as $fila) {
            $valor = trim((string) ($_POST['ficha'][$letra][$fila] ?? ''));
            $tuyas['ficha'][$letra][$fila]    = $valor;
            $aciertos['ficha'][$letra][$fila] = $valor === $esperada[$fila];
        }
    }

    foreach ($ordenCorrecto as $prop => $correcto) {
        $valor = trim((string) ($_POST['orden'][$prop] ?? ''));
        $tuyas['orden'][$prop]    = $valor;
        $aciertos['orden'][$prop] = $valor === $correcto;
    }

    $tm['tuyas']    = $tuyas;
    $tm['aciertos'] = $aciertos;

    // Juntamos los 21 casilleros de la ficha y los 4 órdenes en una sola
    // lista para contarlos. Cuidado con una trampa de PHP: array_merge() con
    // claves de TEXTO ('radio', 'en'...) no las suma, las PISA — la 'radio'
    // de B reemplazaría a la de A. Por eso primero se tiran las claves con
    // array_values() y se juntan listas comunes, numeradas desde 0.
    $todos = array_values($aciertos['orden']);
    foreach ($aciertos['ficha'] as $casilleros) {
        $todos = array_merge($todos, array_values($casilleros));
    }
    sumar_al_marcador($ejercicio, count(array_filter($todos)), count($todos));

    header('Location: tabla_muda.php');
    exit;
}


/* ===========================================================================
 *  PREPARAR LO QUE SE VA A MOSTRAR
 * =========================================================================== */

// El mensaje se muestra UNA vez y se borra: si no, seguiría apareciendo en
// cada recarga. Es lo que en otros frameworks se llama "mensaje flash".
$mensaje = $tm['mensaje'] ?? null;
unset($tm['mensaje']);

$aciertos  = $tm['aciertos'] ?? null;
$corregido = $aciertos !== null;
$paso      = $corregido ? 'corregido' : ($letraActual === null ? 'ficha' : 'elegir');

// Al revés que $tm['elegidos'] (letra → clave): clave → letra, para saber
// rápido qué letra dibujar en cada casillero de la tabla muda.
$letraDe = array_flip($tm['elegidos']);

if ($corregido) {
    $tuyas      = $tm['tuyas'];
    $correccion = [];

    foreach ($ordenCorrecto as $prop => $correcto) {
        $correccion[] = [
            'pregunta'    => 'b. ' . $filasFicha[$prop][0] . ', de menor a mayor',
            'acierto'     => $aciertos['orden'][$prop],
            'tuya'        => $tuyas['orden'][$prop],
            'correcta'    => $correcto,
            'explicacion' => match ($prop) {
                'radio' => 'El radio crece hacia abajo (más niveles) y hacia la izquierda (menos protones que atraigan).',
                'en'    => 'La electronegatividad crece hacia arriba a la derecha, al revés que el radio.',
                'ei'    => 'La energía de ionización crece hacia arriba a la derecha: cuesta más sacar un electrón a un átomo chico.',
                'cm'    => 'El carácter metálico crece hacia abajo a la izquierda, igual que el radio.',
            },
        ];
    }
}


$titulo = 'Tabla muda';
$activo = 'ejercicios';
require __DIR__ . '/includes/cabecera.php';
?>

<p class="miga"><a href="ejercicios.php">← Ejercicios</a> · Tema: propiedades periódicas y tabla muda</p>

<div class="quiz-cabecera">
    <h1 class="titulo-pagina">Tabla muda</h1>
    <?php require __DIR__ . '/includes/marcador.php'; ?>
</div>

<!--
    EL SELECTOR DE SUB-EJERCICIO. Son enlaces comunes (?modo=radio): elegir
    otro empieza una ronda nueva con esa propiedad.
-->
<nav class="selector-modo" aria-label="Practicar con">
    <span class="selector-titulo">Practicar con:</span>
    <?php foreach ($modos as $clave => $modo): ?>
        <a href="tabla_muda.php?modo=<?= $clave ?>"
           class="<?= $clave === $tm['modo'] ? 'activa' : '' ?>"
           <?= $clave === $tm['modo'] ? 'aria-current="page"' : '' ?>><?= e($modo['nombre']) ?></a>
    <?php endforeach; ?>
</nav>

<?php if ($mensaje !== null): ?>
    <div class="aviso <?= $mensaje[0] === 'error' ? 'aviso-error' : 'aviso-ok' ?>"><?= e($mensaje[1]) ?></div>
<?php endif; ?>

<?php if ($paso !== 'corregido'): ?>
    <!--
        LOS PASOS. Una lista ordenada (<ol>) con las tres letras: las que ya
        están elegidas llevan ✓, la que toca ahora se destaca, y las que
        faltan quedan apagadas. aria-current="step" le avisa lo mismo a quien
        usa un lector de pantalla, que no ve los colores.
    -->
    <ol class="pasos">
        <?php foreach ($pedidos as $letra => $pedido): ?>
            <?php
            $estadoPaso = isset($tm['elegidos'][$letra]) ? 'hecho'
                : ($letra === $letraActual ? 'actual' : 'pendiente');
            ?>
            <li class="paso paso-<?= $estadoPaso ?>" <?= $estadoPaso === 'actual' ? 'aria-current="step"' : '' ?>>
                <span class="paso-letra"><?= $estadoPaso === 'hecho' ? '✓' : $letra ?></span>
                <span class="paso-texto">
                    <strong>Elemento <?= $letra ?></strong>
                    <small><?= e($pedido['texto']) ?></small>
                </span>
            </li>
        <?php endforeach; ?>
        <li class="paso <?= $paso === 'ficha' ? 'paso-actual' : 'paso-pendiente' ?>"
            <?= $paso === 'ficha' ? 'aria-current="step"' : '' ?>>
            <span class="paso-letra">✎</span>
            <span class="paso-texto">
                <strong>Ficha y orden</strong>
                <small>sin escribir los nombres</small>
            </span>
        </li>
    </ol>
<?php endif; ?>

<?php if ($paso === 'elegir'): ?>
    <!-- La consigna del paso actual, bien grande y pegada a la tabla. -->
    <div class="paso-ahora <?= $mensaje !== null && $mensaje[0] === 'ok' ? 'recien' : '' ?>">
        <span class="paso-ahora-letra"><?= $letraActual ?></span>
        <div>
            <strong>Tocá en la tabla muda el casillero de tu elemento <?= $letraActual ?></strong>
            <span>uno con <strong><?= e($pedidos[$letraActual]['texto']) ?></strong>.</span>
        </div>
    </div>
    <details class="ayuda">
        <summary>Consejo para la parte b</summary>
        Para poder ordenarlos sólo con las tendencias, elegilos «en diagonal»:
        el de <?= e($pide['alto']) ?> abajo a la izquierda, el de
        <?= e($pide['bajo']) ?> arriba a la derecha, y el de
        <?= e($pide['intermedio']) ?> en el medio: a la derecha y/o arriba
        del primero, y a la izquierda y/o abajo del último. Si no, dos
        tendencias tiran para lados distintos y no se puede saber cuál es
        mayor.
    </details>
<?php elseif ($paso === 'ficha'): ?>
    <p class="bajada">
        Ya ubicaste los tres. Ahora completá la ficha de cada uno —sin su
        nombre— y ordenalos.
    </p>
<?php endif; ?>

<!-- ── LA TABLA MUDA ────────────────────────────────────────────────────── -->
<?php if ($paso === 'elegir'): ?>
<form method="post" action="tabla_muda.php">
<?php endif; ?>

<div class="tabla-scroll">
    <div class="tabla-periodica tabla-muda">
        <?php foreach ($elementos as $clave => $el): ?>
            <?php
            [$fila, $columna] = posicion_tabla($el);
            $letra = $letraDe[$clave] ?? null;
            $lugar = "grid-row: " . ($fila + 1) . "; grid-column: {$columna}";
            ?>
            <?php if ($paso === 'elegir' && $letra === null): ?>
                <!--
                    Cada casillero es un botón del formulario. Al tocarlo se
                    manda  z = número atómico  y PHP decide si sirve. El
                    número viaja en el HTML, pero no es un secreto: lo que
                    importa (si ese elemento cumple) se decide en el servidor.
                -->
                <button type="submit" name="z" value="<?= $el['NumeroAtomico'] ?>"
                        class="celda-muda" style="<?= $lugar ?>"
                        aria-label="Período <?= $fila ?>, columna <?= $columna ?>"></button>
            <?php else: ?>
                <span class="celda-muda <?= $letra !== null ? 'elegida' : '' ?>" style="<?= $lugar ?>">
                    <?php if ($letra !== null): ?>
                        <strong><?= $letra ?></strong>
                        <?php if ($corregido): ?>
                            <small><?= e($el['Simbolo']) ?></small>
                        <?php endif; ?>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($paso === 'elegir'): ?>
</form>
<?php endif; ?>

<!-- ── LA FICHA ─────────────────────────────────────────────────────────── -->
<?php if ($paso !== 'elegir'): ?>

    <?php if ($paso === 'ficha'): ?>
    <form method="post" action="tabla_muda.php">
    <?php endif; ?>

    <h2 class="subtitulo">a. La ficha</h2>
    <div class="tabla-scroll">
        <table class="ej-tabla ej-ficha">
            <thead>
                <tr>
                    <th>Propiedad</th>
                    <?php foreach ($tm['elegidos'] as $letra => $clave): ?>
                        <th>
                            Elemento <?= $letra ?>
                            <?php if ($corregido): ?>
                                <br><small><?= e($elementos[$clave]['Nombre']) ?> (<?= e($elementos[$clave]['Simbolo']) ?>)</small>
                            <?php endif; ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filasFicha as $fila => [$nombreFila, $opciones]): ?>
                    <tr>
                        <th><?= e($nombreFila) ?></th>
                        <?php foreach ($tm['elegidos'] as $letra => $clave): ?>
                            <?php if ($paso === 'ficha'): ?>
                                <td>
                                    <select name="ficha[<?= $letra ?>][<?= $fila ?>]"
                                            aria-label="<?= e($nombreFila) ?> del elemento <?= $letra ?>">
                                        <option value="">—</option>
                                        <?php foreach ($opciones as $opcion): ?>
                                            <option value="<?= e($opcion) ?>"><?= e($opcion) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            <?php else: ?>
                                <?php
                                $ok       = $aciertos['ficha'][$letra][$fila];
                                $esperada = ficha_esperada($tm['niveles'][$letra], $elementos[$clave])[$fila];
                                $tuya     = $tuyas['ficha'][$letra][$fila];
                                ?>
                                <td class="<?= $ok ? 'casillero-ok' : 'casillero-mal' ?>">
                                    <?php if ($ok): ?>
                                        <?= e($esperada) ?>
                                    <?php else: ?>
                                        <s><?= e($tuya === '' ? '—' : $tuya) ?></s> <strong><?= e($esperada) ?></strong>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($paso === 'ficha'): ?>

        <h2 class="subtitulo">b. Ordenalos de menor a mayor</h2>
        <div class="quiz-form">
            <?php foreach (array_keys($ordenCorrecto) as $prop): ?>
                <div class="quiz-fila">
                    <label for="o-<?= $prop ?>"><?= e($filasFicha[$prop][0]) ?></label>
                    <select id="o-<?= $prop ?>" name="orden[<?= $prop ?>]">
                        <option value="">—</option>
                        <?php foreach ($ordenes as $orden): ?>
                            <option value="<?= e($orden) ?>"><?= e($orden) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="boton-grande">Corregir</button>
        </div>
    </form>

    <?php else: ?>

        <h2 class="subtitulo">b. El orden</h2>
        <?php require __DIR__ . '/includes/correccion.php'; ?>

        <?php $letraIntermedio = $letraDeNivel['intermedio']; ?>
        <?php if (categoria_elemento($elementos[$tm['elegidos'][$letraIntermedio]]) === 'postransicion'): ?>
            <div class="aviso">
                Tu elemento <?= $letraIntermedio ?> es un «otro metal» (como el aluminio): tiene
                carácter metálico intermedio pero sigue siendo un metal, así
                que pierde electrones y forma cationes. Si hubieras elegido un
                metaloide (silicio, germanio...), la ficha diría «compartir» y
                «ninguno».
            </div>
        <?php endif; ?>

    <?php endif; ?>

<?php endif; ?>

<?php
$pagina = 'tabla_muda.php';
$urlOtra = 'tabla_muda.php?nuevo=1';   // acá recargar no alcanza: hay que pedirla
require __DIR__ . '/includes/acciones.php';
?>

<?php require __DIR__ . '/includes/pie.php';
