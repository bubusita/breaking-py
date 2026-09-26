<?php
/**
 * =============================================================================
 *  misterioso.php  —  EJERCICIO: EL ELEMENTO MISTERIOSO
 * =============================================================================
 *
 *  Tema: niveles de energía y electrones. Te describen un elemento sin decir
 *  cuál es ("tiene 3 niveles ocupados, 7 electrones en el último, es un gas,
 *  no conduce...") y hay que deducir:
 *
 *      a. si es metal, no metal o metaloide,
 *      b. cuántos electrones tiene,
 *      c. en qué período está,
 *      d. si tiende a ganar o a perder electrones,
 *      e. qué ion formaría.
 *
 *  Y de yapa, f: ¿qué elemento es?
 *
 *  Se sortea entre los elementos del 3 (litio) al 20 (calcio), que son los
 *  que se pueden resolver con los niveles de 2, 8 y 8 del modelo de Bohr (ver
 *  distribucion_niveles() en includes/quimica.php). El hidrógeno y el helio
 *  quedan afuera porque tienen un solo nivel y rompen casi todas las reglas.
 * =============================================================================
 */

session_start();

require_once __DIR__ . '/includes/quimica.php';
$elementos = require __DIR__ . '/includes/tabla_periodica.php';

$ejercicio = 'misterioso';

$opcionesTipo = [
    'metal'     => 'Metal',
    'no metal'  => 'No metal',
    'metaloide' => 'Metaloide',
];

$opcionesTendencia = [
    'perder'    => 'Perder electrones',
    'ganar'     => 'Ganar electrones',
    'compartir' => 'Compartirlos (no gana ni pierde con facilidad)',
    'ninguna'   => 'Ninguna: ya es estable',
];

// Las claves son TEXTO a propósito: en un <select> todo viaja como texto, y
// así comparamos texto con texto. "ninguno" es para los que no forman iones.
$opcionesIon = [
    '1'       => 'Catión X⁺',
    '2'       => 'Catión X²⁺',
    '3'       => 'Catión X³⁺',
    '-1'      => 'Anión X⁻',
    '-2'      => 'Anión X²⁻',
    '-3'      => 'Anión X³⁻',
    'ninguno' => 'No suele formar iones',
];


/**
 * Arma las pistas que describen al elemento.
 *
 * Las dos primeras (niveles y electrones en el último nivel) van siempre y
 * primero, como suelen venir en las pruebas. Después vienen las propiedades físicas y
 * químicas, que dependen del tipo de elemento, mezcladas.
 */
function pistas_misterioso(array $el): array
{
    $niveles = distribucion_niveles($el['NumeroAtomico']);
    $ultimo  = end($niveles);   // end() devuelve el último de la lista: lista[-1]

    $pistas = [
        'Tiene ' . count($niveles) . ' niveles de energía ocupados.',
        'Posee ' . $ultimo . ($ultimo === 1 ? ' electrón' : ' electrones') . ' en su último nivel.',
    ];

    $categoria = categoria_elemento($el);
    $estado    = estado_ambiente($el);

    $fisicas = match (tipo_elemento($el)) {
        'metal' => [
            'Es buen conductor de la electricidad.',
            'Tiene brillo metálico.',
            'A temperatura ambiente es un sólido.',
            $categoria === 'alcalino'
                ? 'Reacciona violentamente con el agua.'
                : 'Reacciona fácilmente con algunos no metales.',
        ],
        'metaloide' => [
            'Conduce poco la electricidad, pero conduce más si se lo calienta.',
            'Es un sólido de brillo moderado, y es quebradizo.',
            'Su reactividad es intermedia.',
        ],
        default => $categoria === 'noble'
            ? [
                'Es mal conductor de la electricidad.',
                'A temperatura ambiente es un gas.',
                'Prácticamente no reacciona con ninguna sustancia.',
            ]
            : [
                'Es mal conductor de la electricidad.',
                "A temperatura ambiente es un {$estado}.",
                'Reacciona con algunos metales.',
            ],
    };

    shuffle($fisicas);

    return array_merge($pistas, $fisicas);
}


/**
 * Las respuestas correctas, cada una con su explicación.
 */
function solucion_misterioso(array $el): array
{
    $z       = $el['NumeroAtomico'];
    $niveles = distribucion_niveles($z);
    $ultimo  = end($niveles);
    $tipo    = tipo_elemento($el);
    $ion     = ion_comun($el);
    $tend    = tendencia_electronica($el);
    $esNoble = categoria_elemento($el) === 'noble';

    // implode() pega los elementos de una lista con un separador. Es el
    // ' + '.join(lista) de Python, con los argumentos al revés.
    $suma = implode(' + ', $niveles);

    $explicaTipo = match (true) {
        $esNoble => "Tiene {$ultimo} electrones en el último nivel: la capa está completa. "
            . 'Es un gas noble, y los gases nobles son no metales.',
        $tipo === 'metaloide' => 'Conduce un poco y más al calentarlo, tiene brillo moderado y es quebradizo: '
            . 'propiedades intermedias entre metal y no metal. Es un metaloide.',
        $tipo === 'metal' => "Tiene pocos electrones en el último nivel ({$ultimo}), conduce y tiene brillo: es un metal.",
        default => "Tiene muchos electrones en el último nivel ({$ultimo}) y no conduce la electricidad: es un no metal.",
    };

    $explicaTendencia = match ($tend) {
        'perder'    => "Con {$ultimo} en el último nivel, le cuesta menos perderlos que conseguir "
            . (8 - $ultimo) . ' más para llegar a 8.',
        'ganar'     => "Con {$ultimo} en el último nivel, le faltan sólo " . (8 - $ultimo)
            . ' para llegar a 8: le conviene ganarlos.',
        'compartir' => $ultimo === 4
            ? 'Con 4 en el último nivel, perder 4 o ganar 4 cuesta lo mismo: los comparte (uniones covalentes).'
            : 'Los metaloides no ganan ni pierden electrones con facilidad: tienden a compartirlos.',
        'ninguna'   => 'Ya tiene 8 electrones en el último nivel: es estable y no necesita ganar ni perder.',
    };

    if ($ion === null) {
        $explicaIon = 'Como no gana ni pierde electrones con facilidad, no suele formar iones simples.';
        $ionTexto   = 'ninguno';
    } else {
        $cambio     = abs($ion) === 1 ? '1 electrón' : abs($ion) . ' electrones';
        $explicaIon = ($ion > 0 ? "Si pierde {$cambio}" : "Si gana {$cambio}")
            . " queda con {$z} protones y " . ($z - $ion) . ' electrones: carga '
            . carga_texto($ion) . ($ion > 0 ? ', un catión' : ', un anión')
            . " ({$el['Simbolo']}" . carga_superindice($ion) . ').';
        $ionTexto   = (string) $ion;
    }

    return [
        'tipo' => [
            'correcta'    => $tipo,
            'explicacion' => $explicaTipo,
        ],
        'electrones' => [
            'correcta'    => (string) $z,
            'explicacion' => 'Los niveles se llenan con 2, 8, 8 electrones. Con ' . count($niveles)
                . " niveles y {$ultimo} en el último: {$suma} = {$z}. En un átomo neutro, e = p = Z.",
        ],
        'periodo' => [
            'correcta'    => (string) count($niveles),
            'explicacion' => 'El período es la cantidad de niveles de energía ocupados: ' . count($niveles) . '.',
        ],
        'tendencia' => [
            'correcta'    => $tend,
            'explicacion' => $explicaTendencia,
        ],
        'ion' => [
            'correcta'    => $ionTexto,
            'explicacion' => $explicaIon,
        ],
        'cual' => [
            'correcta'    => "{$el['Nombre']} ({$el['Simbolo']})",
            'explicacion' => "Z = {$z}: período " . count($niveles) . ', grupo ' . $el['Grupo'] . '.',
        ],
    ];
}


/* ===========================================================================
 *  ATENDER EL FORMULARIO
 * =========================================================================== */

if (ronda_contestada($ejercicio, 'aciertos')) {

    $el  = $elementos[$_SESSION['misterioso']['clave']];
    $sol = solucion_misterioso($el);

    // Lo que llegó del formulario. El (string) evita sorpresas si alguien
    // manda otra cosa editando el HTML.
    $r = fn(string $campo) => trim((string) ($_POST[$campo] ?? ''));

    // Para "¿qué elemento es?" vale el nombre (con o sin tildes) o el símbolo.
    $cual        = $r('cual');
    $aciertoCual = $cual !== ''
        && (quitar_tildes($cual) === quitar_tildes($el['Nombre'])
            || strcasecmp($cual, $el['Simbolo']) === 0);

    $respuestas = [
        'tipo'       => $r('tipo') === $sol['tipo']['correcta'],
        'electrones' => leer_entero($r('electrones')) === $el['NumeroAtomico'],
        'periodo'    => leer_entero($r('periodo')) === (int) $sol['periodo']['correcta'],
        'tendencia'  => $r('tendencia') === $sol['tendencia']['correcta'],
        'ion'        => $r('ion') === $sol['ion']['correcta'],
        'cual'       => $aciertoCual,
    ];

    // Guardamos qué contestó, para mostrarlo al lado de lo correcto.
    $_SESSION['misterioso']['tuyas'] = [
        'tipo'       => $r('tipo'),
        'electrones' => $r('electrones'),
        'periodo'    => $r('periodo'),
        'tendencia'  => $r('tendencia'),
        'ion'        => $r('ion'),
        'cual'       => $cual,
    ];
    $_SESSION['misterioso']['aciertos'] = $respuestas;

    sumar_al_marcador($ejercicio, count(array_filter($respuestas)), count($respuestas));

    header('Location: misterioso.php');
    exit;
}

// Llegaron respuestas, pero de una ronda que ya no está abierta (otra
// pestaña, o el botón "atrás"): no se corrigen, y se avisa.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    descartar_ronda_vieja($ejercicio);
    header('Location: misterioso.php');
    exit;
}

if (isset($_GET['reiniciar'])) {
    reiniciar_marcador($ejercicio);
    unset($_SESSION['misterioso']);
    header('Location: misterioso.php');
    exit;
}


/* ===========================================================================
 *  PREPARAR LO QUE SE VA A MOSTRAR
 * =========================================================================== */

// Cada visita, un elemento nuevo (ver preparar_ronda() en includes/quimica.php).
// Guardamos también las pistas ya mezcladas: la corrección tiene que
// mostrarlas en el mismo orden en que se vieron.
// function () use ($elementos) { ... } es una función sin nombre que
// "se lleva" la variable $elementos: en PHP, a diferencia de Python, una
// función no ve las variables de afuera si no se las pasa con use.
$estado = preparar_ronda($ejercicio, 'aciertos', function () use ($elementos) {
    $clave = clave_por_z($elementos, random_int(3, 20));
    return ['clave' => $clave, 'pistas' => pistas_misterioso($elementos[$clave])];
});

$el        = $elementos[$estado['clave']];
$pistas    = $estado['pistas'];
$aciertos  = $estado['aciertos'] ?? null;
$corregido = $aciertos !== null;

$preguntas = [
    'tipo'       => 'a. ¿Es metal, no metal o metaloide?',
    'electrones' => 'b. ¿Cuántos electrones tiene?',
    'periodo'    => 'c. ¿En qué período se encuentra?',
    'tendencia'  => 'd. ¿Tenderá a ganar o a perder electrones para alcanzar mayor estabilidad?',
    'ion'        => 'e. ¿Qué tipo de ion podría formar?',
    'cual'       => 'f. De yapa: ¿qué elemento es?',
];

if ($corregido) {
    $sol   = solucion_misterioso($el);
    $tuyas = $estado['tuyas'];

    // Para mostrar las respuestas de los <select> se usa el texto de la
    // opción, no su valor interno ('-2' → 'Anión X²⁻').
    $legible = [
        'tipo'      => fn($v) => $opcionesTipo[$v] ?? $v,
        'tendencia' => fn($v) => $opcionesTendencia[$v] ?? $v,
        'ion'       => fn($v) => $opcionesIon[$v] ?? $v,
    ];

    $correccion = [];
    foreach ($preguntas as $campo => $pregunta) {
        $mostrar = $legible[$campo] ?? fn($v) => $v;

        $correccion[] = [
            'pregunta'    => $pregunta,
            'acierto'     => $aciertos[$campo],
            'tuya'        => $mostrar($tuyas[$campo]),
            'correcta'    => $mostrar($sol[$campo]['correcta']),
            'explicacion' => $sol[$campo]['explicacion'],
        ];
    }
}

$titulo = 'El elemento misterioso';
$activo = 'ejercicios';
require __DIR__ . '/includes/cabecera.php';
?>

<p class="miga"><a href="ejercicios.php">← Ejercicios</a> · Tema: niveles de energía y electrones</p>

<div class="quiz-cabecera">
    <h1 class="titulo-pagina">El elemento misterioso</h1>
    <?php require __DIR__ . '/includes/marcador.php'; ?>
</div>

<?php require __DIR__ . '/includes/aviso_ronda.php'; ?>

<div class="caja-pistas">
    <p>Un elemento desconocido presenta las siguientes características:</p>
    <ul>
        <?php foreach ($pistas as $pista): ?>
            <li><?= e($pista) ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<?php if (!$corregido): ?>

    <details class="ayuda">
        <summary>Ayuda-memoria</summary>
        <ul>
            <li>Los niveles se llenan con <strong>2, 8, 8</strong> electrones (hasta el calcio).</li>
            <li>Cantidad de niveles ocupados = <strong>período</strong>.</li>
            <li>Electrones en el último nivel: 1 a 3 → suele <strong>perderlos</strong> (metal, catión +);
                5 a 7 → suele <strong>ganar</strong> los que faltan para 8 (no metal, anión −);
                4 → los <strong>comparte</strong>; 8 → ya es estable (gas noble).</li>
        </ul>
    </details>

    <form class="quiz-form" method="post" action="misterioso.php">
    <input type="hidden" name="ronda" value="<?= e($estado['id']) ?>">
        <div class="quiz-fila">
            <label for="p-tipo"><?= e($preguntas['tipo']) ?></label>
            <select id="p-tipo" name="tipo">
                <option value="">—</option>
                <?php foreach ($opcionesTipo as $valor => $texto): ?>
                    <option value="<?= e($valor) ?>"><?= e($texto) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="quiz-fila">
            <label for="p-electrones"><?= e($preguntas['electrones']) ?></label>
            <input type="text" id="p-electrones" name="electrones" inputmode="numeric" autocomplete="off">
        </div>

        <div class="quiz-fila">
            <label for="p-periodo"><?= e($preguntas['periodo']) ?></label>
            <input type="text" id="p-periodo" name="periodo" inputmode="numeric" autocomplete="off">
        </div>

        <div class="quiz-fila">
            <label for="p-tendencia"><?= e($preguntas['tendencia']) ?></label>
            <select id="p-tendencia" name="tendencia">
                <option value="">—</option>
                <?php foreach ($opcionesTendencia as $valor => $texto): ?>
                    <option value="<?= e($valor) ?>"><?= e($texto) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="quiz-fila">
            <label for="p-ion"><?= e($preguntas['ion']) ?></label>
            <select id="p-ion" name="ion">
                <option value="">—</option>
                <?php foreach ($opcionesIon as $valor => $texto): ?>
                    <option value="<?= e((string) $valor) ?>"><?= e($texto) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="quiz-fila">
            <label for="p-cual"><?= e($preguntas['cual']) ?> (nombre o símbolo)</label>
            <input type="text" id="p-cual" name="cual" autocomplete="off">
        </div>

        <button type="submit" class="boton-grande">Corregir</button>
    </form>

<?php else: ?>

    <?php require __DIR__ . '/includes/correccion.php'; ?>

    <p class="centrado">
        <a class="boton-secundario" href="elemento.php?nombre=<?= urlencode($el['Nombre']) ?>">
            Ver la ficha del <?= e($el['Nombre']) ?>
        </a>
    </p>

<?php endif; ?>

<?php
$pagina = 'misterioso.php';
require __DIR__ . '/includes/acciones.php';
?>

<?php require __DIR__ . '/includes/pie.php';
