<?php
/**
 * =============================================================================
 *  clasificar.php  —  EJERCICIO: ¿METAL, NO METAL O METALOIDE?
 * =============================================================================
 *
 *  Tema: metales, no metales y metaloides. Unos investigadores encuentran
 *  un elemento nuevo, le miden propiedades (conductividad, brillo,
 *  maleabilidad, estado, reactividad) y hay que clasificarlo SIN mirar la
 *  tabla. Se pide:
 *
 *      a. clasificarlo,
 *      b. decir qué propiedades sostienen la clasificación,
 *      c. si formaría un catión o un anión,
 *      d. el signo de esa carga,
 *      e. opinar sobre lo que afirma un compañero.
 *
 *  La parte b es la más sutil, y suele volver a preguntarse de otra forma:
 *  ¿qué propiedades fueron más útiles y cuáles menos concluyentes? Por eso cada propiedad sorteada viene marcada como
 *  EVIDENCIA o no. Las que no son evidencia son siempre las mismas dos:
 *
 *    - el ESTADO: hay metales líquidos (mercurio) y no metales sólidos
 *      (carbono, azufre, yodo). Que sea sólido no dice nada;
 *    - la REACTIVIDAD: reaccionan los metales y reaccionan los no metales.
 * =============================================================================
 */

// La sesión se abre en includes/sesion.php, que le da nombre de cookie propio
// para no pisar la del resto del sitio (que sí tiene login). Ahí está el detalle.
require_once __DIR__ . '/includes/sesion.php';

require_once __DIR__ . '/includes/quimica.php';

$ejercicio = 'clasificar';

$opcionesTipo = [
    'metal'     => 'Metal',
    'no metal'  => 'No metal',
    'metaloide' => 'Metaloide',
];

$opcionesIon = [
    'cation'   => 'Un catión',
    'anion'    => 'Un anión',
    'ninguno'  => 'No necesariamente forma un ion',
];

$opcionesSigno = [
    'positivo' => 'Positivo (+)',
    'negativo' => 'Negativo (−)',
    'ninguno'  => 'No corresponde: puede no formar ion',
];


/**
 * Las propiedades posibles de cada tipo de elemento.
 *
 * Para cada propiedad hay VARIAS formas de decir el resultado, y se sortea
 * una: así el ejercicio no es siempre idéntico. El tercer valor de cada fila
 * dice si esa propiedad sirve como evidencia para clasificarlo.
 *
 * Formato:  [propiedad, [resultados posibles], ¿es evidencia?]
 */
function propiedades_posibles(string $tipo): array
{
    return match ($tipo) {
        'metal' => [
            ['Conductividad eléctrica', ['Alta', 'Muy buena, en estado sólido'], true],
            ['Brillo', ['Metálico', 'Brillante al cortarlo'], true],
            ['Maleabilidad', ['Alta', 'Se puede laminar en hojas finas sin romperse'], true],
            ['Estado a temperatura ambiente', ['Sólido', 'Sólido', 'Sólido', 'Líquido'], false],
            ['Reactividad', ['Reacciona con sustancias no metálicas', 'Reacciona con el oxígeno del aire'], false],
            ['Tendencia a perder o ganar electrones', ['Tiende a perder electrones'], true],
        ],
        'no metal' => [
            ['Conductividad eléctrica', ['Muy baja', 'No conduce la electricidad'], true],
            ['Brillo', ['Opaco, sin brillo', 'No tiene brillo metálico'], true],
            ['Maleabilidad', ['Nula: el sólido se rompe al golpearlo', 'Quebradizo'], true],
            ['Estado a temperatura ambiente', ['Gas', 'Sólido', 'Sólido', 'Líquido'], false],
            ['Reactividad', ['Reacciona con los metales', 'Alta: reacciona con muchas sustancias'], false],
            ['Tendencia a perder o ganar electrones', ['Tiende a ganar electrones'], true],
        ],
        'metaloide' => [
            ['Conductividad eléctrica', ['Baja en estado sólido; aumenta considerablemente al calentarlo',
                'Intermedia: es un semiconductor'], true],
            ['Brillo', ['Moderado', 'Algo de brillo, menos que un metal'], true],
            ['Maleabilidad', ['Baja; es quebradizo'], true],
            ['Estado a temperatura ambiente', ['Sólido'], false],
            ['Reactividad', ['Intermedia'], false],
            ['Tendencia electrónica', ['Puede ganar o compartir electrones'], true],
        ],
    };
}


/**
 * Lo que puede afirmar "un compañero". Cada una con su respuesta
 * (verdadero o falso) y la explicación.
 */
function afirmaciones(): array
{
    return [
        ['Como es un elemento sólido, necesariamente es un metal.', false,
            'Hay no metales sólidos (carbono, azufre, yodo) y metaloides sólidos (silicio, boro). El estado físico solo no alcanza: hay que mirar varias propiedades juntas.'],
        ['Si conduce la electricidad, seguro que es un metal.', false,
            'El grafito (carbono, un no metal) conduce, y los metaloides conducen algo, sobre todo si se los calienta.'],
        ['Todos los metales son sólidos a temperatura ambiente.', false,
            'El mercurio es un metal y es líquido a temperatura ambiente.'],
        ['Todos los no metales son gases.', false,
            'El carbono, el azufre, el fósforo y el yodo son sólidos, y el bromo es líquido.'],
        ['Los no metales tienden a ganar electrones y a formar aniones.', true,
            'Tienen muchos electrones en el último nivel (5 a 7): les cuesta menos ganar los que faltan para 8. Al ganarlos quedan con carga negativa.'],
        ['Cuando un metal forma un ion, queda con carga negativa porque pierde electrones.', false,
            'Al perder electrones (que son negativos) le sobran protones: la carga queda POSITIVA. Es un catión.'],
        ['Un metaloide siempre forma iones.', false,
            'Los metaloides no ganan ni pierden electrones con facilidad: en general los comparten.'],
        ['Si un elemento es brillante, maleable y buen conductor, lo más probable es que sea un metal.', true,
            'Son tres propiedades típicas de los metales, y juntas son una evidencia fuerte.'],
        ['Un anión tiene más electrones que protones.', true,
            'Ganó electrones: tiene más cargas negativas que positivas, y por eso su carga es negativa.'],
        ['El brillo por sí solo alcanza para decir que un elemento es metal.', false,
            'El yodo y el silicio tienen brillo y no son metales. Una sola propiedad casi nunca alcanza.'],
        ['Un catión se forma cuando un átomo gana protones.', false,
            'Los protones no cambian nunca al formar un ion (si cambiaran, sería otro elemento). El catión se forma al PERDER electrones.'],
        ['Que un elemento sea quebradizo es una pista de que no es un metal.', true,
            'Los metales son maleables: se deforman sin romperse. Los no metales sólidos y los metaloides se rompen.'],
    ];
}


/**
 * Sortea un elemento: un tipo, sus propiedades (cada una con una de sus
 * formas de decirla) y una afirmación del compañero.
 */
function sortear_hallazgo(): array
{
    $tipo = uno_al_azar(['metal', 'no metal', 'metaloide']);

    $filas = [];
    foreach (propiedades_posibles($tipo) as $i => [$propiedad, $resultados, $evidencia]) {
        $filas[] = [
            'id'        => $i,
            'propiedad' => $propiedad,
            'resultado' => uno_al_azar($resultados),
            'evidencia' => $evidencia,
        ];
    }

    // Si salió gas o líquido, la maleabilidad no tiene sentido: un gas o un
    // líquido no se pueden laminar ni golpear. (La fila 2 es la maleabilidad
    // y la 3, el estado: están en ese orden en propiedades_posibles().)
    $estado = $filas[3]['resultado'];
    if ($estado !== 'Sólido') {
        $filas[2]['resultado'] = 'No corresponde: es un ' . mb_strtolower($estado);
        $filas[2]['evidencia'] = false;
    }

    // Una tabla así no siempre trae todas las propiedades: a veces
    // sacamos una al azar (nunca el estado, que es la trampa del punto e).
    if (random_int(0, 1) === 1) {
        unset($filas[uno_al_azar([0, 1, 2, 5])]);
        $filas = array_values($filas);
    }

    $afirmaciones = afirmaciones();

    return [
        'tipo'        => $tipo,
        'filas'       => $filas,
        'afirmacion'  => random_int(0, count($afirmaciones) - 1),
    ];
}


/**
 * Las respuestas correctas de c y d según el tipo.
 */
function ion_esperado(string $tipo): array
{
    return match ($tipo) {
        'metal'     => ['cation', 'positivo'],
        'no metal'  => ['anion', 'negativo'],
        'metaloide' => ['ninguno', 'ninguno'],
    };
}


/* ===========================================================================
 *  ATENDER EL FORMULARIO
 * =========================================================================== */

if (ronda_contestada($ejercicio, 'aciertos')) {

    $h = $_SESSION['clasificar']['hallazgo'];
    [$ionOk, $signoOk] = ion_esperado($h['tipo']);
    $afirmacion = afirmaciones()[$h['afirmacion']];

    $r = fn(string $campo) => trim(texto_recibido($_POST[$campo] ?? ''));

    // Los casilleros tildados llegan como una lista: evidencias[] = 0, 2, 5...
    // Si no tildó ninguno, $_POST['evidencias'] ni siquiera existe.
    $tildadas = array_map('intval', (array) ($_POST['evidencias'] ?? []));

    // En la b se corrige casillero por casillero: cada propiedad bien
    // tildada (o bien destildada) cuenta como un acierto.
    $evidenciasBien = 0;
    foreach ($h['filas'] as $fila) {
        $tildada = in_array($fila['id'], $tildadas, true);
        if ($tildada === $fila['evidencia']) {
            $evidenciasBien++;
        }
    }

    $_SESSION['clasificar']['respuestas'] = [
        'tipo'        => $r('tipo'),
        'tildadas'    => $tildadas,
        'ion'         => $r('ion'),
        'signo'       => $r('signo'),
        'afirmacion'  => $r('afirmacion'),
    ];

    $aciertos = [
        'tipo'       => $r('tipo') === $h['tipo'],
        'evidencias' => $evidenciasBien === count($h['filas']),
        'ion'        => $r('ion') === $ionOk,
        'signo'      => $r('signo') === $signoOk,
        'afirmacion' => $r('afirmacion') === ($afirmacion[1] ? 'v' : 'f'),
    ];
    $_SESSION['clasificar']['aciertos'] = $aciertos;
    $_SESSION['clasificar']['evidencias_bien'] = $evidenciasBien;

    // La b cuenta por casillero; las otras cuatro, una cada una.
    $bien  = $evidenciasBien + count(array_filter([$aciertos['tipo'], $aciertos['ion'],
        $aciertos['signo'], $aciertos['afirmacion']]));
    $total = count($h['filas']) + 4;
    sumar_al_marcador($ejercicio, $bien, $total);

    header('Location: clasificar.php');
    exit;
}

// Llegaron respuestas, pero de una ronda que ya no está abierta (otra
// pestaña, o el botón "atrás"): no se corrigen, y se avisa.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    descartar_ronda_vieja($ejercicio);
    header('Location: clasificar.php');
    exit;
}

if (isset($_GET['reiniciar'])) {
    reiniciar_marcador($ejercicio);
    unset($_SESSION['clasificar']);
    header('Location: clasificar.php');
    exit;
}


/* ===========================================================================
 *  PREPARAR LO QUE SE VA A MOSTRAR
 * =========================================================================== */

// Cada visita, un caso nuevo (ver preparar_ronda() en includes/quimica.php).
$estado = preparar_ronda($ejercicio, 'aciertos', fn() => ['hallazgo' => sortear_hallazgo()]);

$h          = $estado['hallazgo'];
$afirmacion = afirmaciones()[$h['afirmacion']];
$aciertos   = $estado['aciertos'] ?? null;
$corregido  = $aciertos !== null;

if ($corregido) {
    $tuyas = $estado['respuestas'];
    [$ionOk, $signoOk] = ion_esperado($h['tipo']);

    $explicaTipo = match ($h['tipo']) {
        'metal'     => 'Conduce bien, tiene brillo metálico, es maleable y tiende a perder electrones: todas propiedades de los metales.',
        'no metal'  => 'No conduce, no tiene brillo, no es maleable y tiende a ganar electrones: todas propiedades de los no metales.',
        'metaloide' => 'Sus propiedades son intermedias: conduce poco pero más al calentarlo (semiconductor), brilla algo y es quebradizo.',
    };

    $explicaIon = match ($h['tipo']) {
        'metal'     => 'Tiende a perder electrones: le quedan más protones que electrones, así que forma un catión.',
        'no metal'  => 'Tiende a ganar electrones: le quedan más electrones que protones, así que forma un anión.',
        'metaloide' => 'Puede ganar o compartir electrones. Cuando los comparte (unión covalente) no forma ningún ion, así que no necesariamente forma uno.',
    };

    $explicaSigno = match ($h['tipo']) {
        'metal'     => 'Un catión tiene carga positiva: perdió cargas negativas (electrones).',
        'no metal'  => 'Un anión tiene carga negativa: ganó cargas negativas (electrones).',
        'metaloide' => 'Si no forma un ion, no hay carga de la cual hablar.',
    };

    // La b: listamos las que eran evidencia y las que no.
    $siEran = array_column(array_filter($h['filas'], fn($f) => $f['evidencia']), 'propiedad');
    $noEran = array_column(array_filter($h['filas'], fn($f) => !$f['evidencia']), 'propiedad');

    $correccion = [
        [
            'pregunta'    => 'a. ¿Cómo lo clasificarías?',
            'acierto'     => $aciertos['tipo'],
            'tuya'        => $opcionesTipo[$tuyas['tipo']] ?? '',
            'correcta'    => $opcionesTipo[$h['tipo']],
            'explicacion' => $explicaTipo,
        ],
        [
            'pregunta'    => 'b. ¿Qué propiedades sostienen tu clasificación?',
            'acierto'     => $aciertos['evidencias'],
            'tuya'        => $estado['evidencias_bien'] . ' de ' . count($h['filas']) . ' casilleros bien',
            'correcta'    => lista_con_y($siEran),
            'explicacion' => 'No son concluyentes: ' . mb_strtolower(lista_con_y($noEran))
                . '. El estado no alcanza (hay metales líquidos y no metales sólidos) y reaccionan tanto los metales'
                . ' como los no metales.'
                . (in_array('Maleabilidad', $noEran, true) ? ' Y en un gas o un líquido la maleabilidad no se puede medir.' : ''),
        ],
        [
            'pregunta'    => 'c. Si formara un ion, ¿sería catión o anión?',
            'acierto'     => $aciertos['ion'],
            'tuya'        => $opcionesIon[$tuyas['ion']] ?? '',
            'correcta'    => $opcionesIon[$ionOk],
            'explicacion' => $explicaIon,
        ],
        [
            'pregunta'    => 'd. ¿Qué signo tendría la carga?',
            'acierto'     => $aciertos['signo'],
            'tuya'        => $opcionesSigno[$tuyas['signo']] ?? '',
            'correcta'    => $opcionesSigno[$signoOk],
            'explicacion' => $explicaSigno,
        ],
        [
            'pregunta'    => 'e. «' . $afirmacion[0] . '»',
            'acierto'     => $aciertos['afirmacion'],
            'tuya'        => ['v' => 'De acuerdo', 'f' => 'No estoy de acuerdo'][$tuyas['afirmacion']] ?? '',
            'correcta'    => $afirmacion[1] ? 'De acuerdo' : 'No estoy de acuerdo',
            'explicacion' => $afirmacion[2],
        ],
    ];
}

$titulo = '¿Metal, no metal o metaloide?';
$activo = 'ejercicios';
require __DIR__ . '/includes/cabecera.php';
?>

<p class="miga"><a href="ejercicios.php">← Ejercicios</a> · Tema: metales, no metales y metaloides</p>

<div class="quiz-cabecera">
    <h1 class="titulo-pagina">¿Metal, no metal o metaloide?</h1>
    <?php require __DIR__ . '/includes/marcador.php'; ?>
</div>

<?php require __DIR__ . '/includes/aviso_ronda.php'; ?>

<p class="bajada">
    Un equipo de investigadores encontró un elemento que no había sido
    identificado. Antes de ubicarlo en la tabla, midieron sus propiedades:
</p>

<?php if (!$corregido): ?>
<form method="post" action="clasificar.php">
    <input type="hidden" name="ronda" value="<?= e($estado['id']) ?>">
<?php endif; ?>

<div class="tabla-scroll">
    <table class="ej-tabla ej-propiedades">
        <thead>
            <tr>
                <th>Propiedad observada</th>
                <th>Resultado</th>
                <th>¿Es evidencia? <small>(para la b)</small></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($h['filas'] as $fila): ?>
                <?php
                $tildada = $corregido && in_array($fila['id'], $tuyas['tildadas'], true);
                $bienMarcada = $tildada === $fila['evidencia'];
                ?>
                <tr>
                    <th><?= e($fila['propiedad']) ?></th>
                    <td><?= e($fila['resultado']) ?></td>
                    <?php if (!$corregido): ?>
                        <td>
                            <input type="checkbox" name="evidencias[]" value="<?= $fila['id'] ?>"
                                   aria-label="<?= e($fila['propiedad']) ?> es evidencia">
                        </td>
                    <?php else: ?>
                        <td class="<?= $bienMarcada ? 'casillero-ok' : 'casillero-mal' ?>">
                            <?= $fila['evidencia'] ? 'Sí' : 'No' ?>
                            <?php if (!$bienMarcada): ?>
                                <small>(marcaste «<?= $tildada ? 'sí' : 'no' ?>»)</small>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (!$corregido): ?>

    <div class="quiz-form">
        <div class="quiz-fila">
            <label for="p-tipo">a. Sin consultar la tabla periódica, ¿cómo clasificarías este elemento?</label>
            <select id="p-tipo" name="tipo">
                <option value="">—</option>
                <?php foreach ($opcionesTipo as $valor => $texto): ?>
                    <option value="<?= e($valor) ?>"><?= e($texto) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <p class="quiz-nota">
            b. ¿Qué propiedades te permiten sostener tu clasificación? Tildalas
            en la columna «¿Es evidencia?» de la tabla.
        </p>

        <div class="quiz-fila">
            <label for="p-ion">c. Si este elemento formara un ion, ¿esperarías que fuera un catión o un anión?</label>
            <select id="p-ion" name="ion">
                <option value="">—</option>
                <?php foreach ($opcionesIon as $valor => $texto): ?>
                    <option value="<?= e($valor) ?>"><?= e($texto) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="quiz-fila">
            <label for="p-signo">d. ¿Qué signo tendría la carga de ese ion?</label>
            <select id="p-signo" name="signo">
                <option value="">—</option>
                <?php foreach ($opcionesSigno as $valor => $texto): ?>
                    <option value="<?= e($valor) ?>"><?= e($texto) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="quiz-fila">
            <label for="p-afirmacion">e. Un compañero afirma: «<?= e($afirmacion[0]) ?>» ¿Estás de acuerdo?</label>
            <select id="p-afirmacion" name="afirmacion">
                <option value="">—</option>
                <option value="v">De acuerdo</option>
                <option value="f">No estoy de acuerdo</option>
            </select>
        </div>

        <button type="submit" class="boton-grande">Corregir</button>
    </div>
</form>

<?php else: ?>

    <?php require __DIR__ . '/includes/correccion.php'; ?>


<?php endif; ?>

<?php
$pagina = 'clasificar.php';
require __DIR__ . '/includes/acciones.php';
?>

<?php require __DIR__ . '/includes/pie.php';
