<?php
/**
 * =============================================================================
 *  propiedades.php  —  EJERCICIO: LAS CUATRO PROPIEDADES PERIÓDICAS
 * =============================================================================
 *
 *  Tema: propiedades periódicas. Te dan cómo son las propiedades
 *  periódicas de un elemento desconocido (radio atómico, electronegatividad,
 *  energía de ionización y carácter metálico) y hay que deducir:
 *
 *      a. en qué zona de la tabla buscarlo,
 *      b. si es metal, no metal o metaloide,
 *      c. si tiende a perder o a ganar electrones,
 *      d. qué ion formaría,
 *      e. justificarlo con las cuatro propiedades.
 *
 *  La e no se puede corregir sola (es un texto libre), así que se practica
 *  por partes: se pregunta hacia dónde crece una de las propiedades en la
 *  tabla —que es la base de cualquier justificación— y al corregir se
 *  muestra una justificación modelo completa.
 *
 *  La mitad de las veces te dan sólo DOS propiedades y tenés que deducir las
 *  otras dos: las cuatro van siempre juntas (radio chico ⇒ electronegatividad
 *  alta ⇒ energía de ionización alta ⇒ carácter metálico bajo), y darse
 *  cuenta de eso es la mitad del ejercicio.
 * =============================================================================
 */

// La sesión se abre en includes/sesion.php, que le da nombre de cookie propio
// para no pisar la del resto del sitio (que sí tiene login). Ahí está el detalle.
require_once __DIR__ . '/includes/sesion.php';

require_once __DIR__ . '/includes/quimica.php';
$elementos = require __DIR__ . '/includes/tabla_periodica.php';

$ejercicio = 'propiedades';

/**
 * Los tres perfiles posibles. Cada uno dice cómo son las cuatro propiedades
 * y qué hay que contestar en cada pregunta.
 */
$perfiles = [
    'no metal' => [
        'radio' => 'pequeño', 'en' => 'alta', 'ei' => 'alta', 'cm' => 'bajo',
        'zona' => 'arriba-derecha', 'tendencia' => 'ganar', 'ion' => 'anion',
        'ejemplos' => 'flúor, oxígeno, cloro, nitrógeno',
    ],
    'metal' => [
        'radio' => 'grande', 'en' => 'baja', 'ei' => 'baja', 'cm' => 'alto',
        'zona' => 'abajo-izquierda', 'tendencia' => 'perder', 'ion' => 'cation',
        'ejemplos' => 'cesio, potasio, bario, sodio',
    ],
    'metaloide' => [
        'radio' => 'intermedio', 'en' => 'intermedia', 'ei' => 'intermedia', 'cm' => 'intermedio',
        'zona' => 'escalera', 'tendencia' => 'compartir', 'ion' => 'ninguno',
        'ejemplos' => 'silicio, germanio, arsénico, boro',
    ],
];

// Los nombres de las cuatro propiedades y los valores que puede tomar cada una.
$propiedades = [
    'radio' => ['nombre' => 'Radio atómico',          'valores' => ['pequeño', 'intermedio', 'grande']],
    'en'    => ['nombre' => 'Electronegatividad',     'valores' => ['baja', 'intermedia', 'alta']],
    'ei'    => ['nombre' => 'Energía de ionización',  'valores' => ['baja', 'intermedia', 'alta']],
    'cm'    => ['nombre' => 'Carácter metálico',      'valores' => ['bajo', 'intermedio', 'alto']],
];

$opcionesZona = [
    'arriba-derecha'  => 'Arriba a la derecha (sin contar los gases nobles)',
    'abajo-izquierda' => 'Abajo a la izquierda',
    'escalera'        => 'Sobre la línea en escalera, entre metales y no metales',
    'centro'          => 'En el centro, entre los metales de transición',
    'nobles'          => 'En la última columna, la de los gases nobles',
];

$opcionesTipo = [
    'metal'     => 'Metal',
    'no metal'  => 'No metal',
    'metaloide' => 'Metaloide',
];

$opcionesTendencia = [
    'perder'    => 'Perder electrones',
    'ganar'     => 'Ganar electrones',
    'compartir' => 'Ni una ni otra: tiende a compartirlos',
];

$opcionesIon = [
    'cation'  => 'Un catión (carga +)',
    'anion'   => 'Un anión (carga −)',
    'ninguno' => 'Probablemente ninguno: comparte electrones',
];

/**
 * Hacia dónde crece cada propiedad, y por qué. Es lo que hay que saber para
 * poder justificar cualquier respuesta de este tema.
 */
$tendencias = [
    'radio' => [
        'periodo' => ['disminuye', 'A lo largo de un período se suman protones pero los electrones siguen en el mismo nivel: el núcleo los atrae más fuerte y el átomo se achica.'],
        'grupo'   => ['aumenta', 'Cada período agrega un nivel de energía más, así que el átomo es más grande.'],
    ],
    'en' => [
        'periodo' => ['aumenta', 'El átomo es más chico y tiene más protones: atrae con más fuerza los electrones de otros átomos.'],
        'grupo'   => ['disminuye', 'El átomo es más grande: los electrones de afuera quedan lejos del núcleo y los atrae menos.'],
    ],
    'ei' => [
        'periodo' => ['aumenta', 'Los electrones están más atraídos por el núcleo: cuesta más energía arrancar uno.'],
        'grupo'   => ['disminuye', 'El electrón de afuera está más lejos del núcleo, y sacarlo cuesta menos.'],
    ],
    'cm' => [
        'periodo' => ['disminuye', 'Cada vez cuesta más perder electrones, que es lo que hacen los metales.'],
        'grupo'   => ['aumenta', 'Cada vez es más fácil perder electrones: los metales más metálicos están abajo a la izquierda.'],
    ],
];

$direcciones = [
    'periodo' => 'en un período, de izquierda a derecha',
    'grupo'   => 'en un grupo, de arriba hacia abajo',
];


/**
 * Cómo se lee una propiedad en el enunciado: "elevada electronegatividad".
 */
function describir(string $prop, string $valor, array $propiedades): string
{
    $nombre = mb_strtolower($propiedades[$prop]['nombre']);

    return match ($valor) {
        'pequeño'                => "{$nombre} relativamente pequeño",
        'grande'                 => "{$nombre} relativamente grande",
        'alta', 'alto'           => ($prop === 'cm' ? 'alto ' : 'elevada ') . $nombre,
        'baja', 'bajo'           => ($prop === 'cm' ? 'bajo ' : 'baja ') . $nombre,
        default                  => "{$nombre} intermedi" . ($prop === 'radio' || $prop === 'cm' ? 'o' : 'a'),
    };
}


/**
 * La justificación modelo: la que tendría que escribirse en la e.
 */
function justificacion(string $tipo): string
{
    return match ($tipo) {
        'no metal' => 'Está arriba a la derecha de la tabla. Ahí el radio atómico es pequeño: tiene pocos niveles y muchos '
            . 'protones que atraen fuerte a los electrones. Por eso su electronegatividad es alta (atrae electrones de '
            . 'otros átomos) y su energía de ionización también (cuesta mucho arrancarle un electrón). Como le cuesta '
            . 'perder electrones, su carácter metálico es bajo: es un no metal, tiende a ganar electrones y forma aniones.',
        'metal' => 'Está abajo a la izquierda de la tabla. Ahí el radio atómico es grande: tiene muchos niveles y el '
            . 'electrón de afuera queda lejos del núcleo. Por eso su electronegatividad es baja (atrae poco a los '
            . 'electrones) y su energía de ionización también (cuesta poco arrancarle uno). Como pierde electrones con '
            . 'facilidad, su carácter metálico es alto: es un metal, tiende a perder electrones y forma cationes.',
        'metaloide' => 'Está sobre la línea en escalera que separa metales de no metales. Sus cuatro propiedades son '
            . 'intermedias: el radio no es ni grande ni chico, la electronegatividad y la energía de ionización son '
            . 'medianas, y el carácter metálico también. Por eso no gana ni pierde electrones con facilidad: tiende a '
            . 'compartirlos, y no necesariamente forma un ion.',
    };
}


/**
 * Sortea un ejercicio: el tipo de elemento, qué propiedades se dan y qué
 * pregunta de tendencia se hace.
 */
function sortear_detective(): array
{
    $todas = ['radio', 'en', 'ei', 'cm'];

    if (random_int(0, 1) === 0) {
        $dadas = $todas;
    } else {
        shuffle($todas);
        $dadas = array_slice($todas, 0, 2);   // array_slice = lista[0:2]
    }

    return [
        'tipo'      => uno_al_azar(['no metal', 'no metal', 'metal', 'metal', 'metaloide']),
        'dadas'     => $dadas,
        'tendencia' => [uno_al_azar(['radio', 'en', 'ei', 'cm']), uno_al_azar(['periodo', 'grupo'])],
    ];
}


/* ===========================================================================
 *  ATENDER EL FORMULARIO
 * =========================================================================== */

if (ronda_contestada($ejercicio, 'aciertos')) {

    $caso   = $_SESSION['propiedades']['caso'];
    $perfil = $perfiles[$caso['tipo']];
    [$tProp, $tDir] = $caso['tendencia'];

    $r = fn(string $campo) => trim(texto_recibido($_POST[$campo] ?? ''));

    $tuyas    = [];
    $aciertos = [];

    // Primero las propiedades que había que deducir (si faltaba alguna).
    foreach (array_keys($propiedades) as $prop) {
        if (!in_array($prop, $caso['dadas'], true)) {
            $tuyas[$prop]    = $r("prop_$prop");
            $aciertos[$prop] = $tuyas[$prop] === $perfil[$prop];
        }
    }

    foreach (['zona', 'tipo', 'tendencia', 'ion'] as $campo) {
        $tuyas[$campo]    = $r($campo);
        $esperada         = $campo === 'tipo' ? $caso['tipo'] : $perfil[$campo];
        $aciertos[$campo] = $tuyas[$campo] === $esperada;
    }

    $tuyas['hacia']    = $r('hacia');
    $aciertos['hacia'] = $tuyas['hacia'] === $tendencias[$tProp][$tDir][0];

    $_SESSION['propiedades']['tuyas']    = $tuyas;
    $_SESSION['propiedades']['aciertos'] = $aciertos;

    sumar_al_marcador($ejercicio, count(array_filter($aciertos)), count($aciertos));

    header('Location: propiedades.php');
    exit;
}

// Llegaron respuestas, pero de una ronda que ya no está abierta (otra
// pestaña, o el botón "atrás"): no se corrigen, y se avisa.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    descartar_ronda_vieja($ejercicio);
    header('Location: propiedades.php');
    exit;
}

if (isset($_GET['reiniciar'])) {
    reiniciar_marcador($ejercicio);
    unset($_SESSION['propiedades']);
    header('Location: propiedades.php');
    exit;
}


/* ===========================================================================
 *  PREPARAR LO QUE SE VA A MOSTRAR
 * =========================================================================== */

// Cada visita, un caso nuevo (ver preparar_ronda() en includes/quimica.php).
$estado = preparar_ronda($ejercicio, 'aciertos', fn() => ['caso' => sortear_detective()]);

$caso      = $estado['caso'];
$perfil    = $perfiles[$caso['tipo']];
$faltan    = array_diff(array_keys($propiedades), $caso['dadas']);   // las que hay que deducir
[$tProp, $tDir] = $caso['tendencia'];
$preguntaHacia  = 'e. Para justificar: ' . $direcciones[$tDir] . ', ¿qué le pasa a la '
    . mb_strtolower($propiedades[$tProp]['nombre']) . '?';
// "¿qué le pasa AL radio / AL carácter?" — son masculinos.
if ($tProp === 'radio' || $tProp === 'cm') {
    $preguntaHacia = str_replace('a la ', 'al ', $preguntaHacia);
}

$aciertos  = $estado['aciertos'] ?? null;
$corregido = $aciertos !== null;

if ($corregido) {
    $tuyas = $estado['tuyas'];

    $correccion = [];

    foreach ($faltan as $prop) {
        $correccion[] = [
            'pregunta'    => 'Deducí: ' . mb_strtolower($propiedades[$prop]['nombre']),
            'acierto'     => $aciertos[$prop],
            'tuya'        => ucfirst($tuyas[$prop]),
            'correcta'    => ucfirst($perfil[$prop]),
            'explicacion' => 'Las cuatro propiedades van juntas: radio chico, electronegatividad y energía de ionización '
                . 'altas y carácter metálico bajo (o todo al revés).',
        ];
    }

    $correccion[] = [
        'pregunta'    => 'a. ¿En qué zona de la tabla lo buscarías?',
        'acierto'     => $aciertos['zona'],
        'tuya'        => $opcionesZona[$tuyas['zona']] ?? '',
        'correcta'    => $opcionesZona[$perfil['zona']],
        'explicacion' => 'Por ejemplo: ' . $perfil['ejemplos'] . '. En la tabla de abajo está resaltada la zona.',
    ];
    $correccion[] = [
        'pregunta'    => 'b. ¿Es más probable que sea metal, no metal o metaloide?',
        'acierto'     => $aciertos['tipo'],
        'tuya'        => $opcionesTipo[$tuyas['tipo']] ?? '',
        'correcta'    => $opcionesTipo[$caso['tipo']],
        'explicacion' => 'El carácter metálico ' . $perfil['cm'] . ' lo dice casi directamente.',
    ];
    $correccion[] = [
        'pregunta'    => 'c. ¿Tendría mayor tendencia a perder o a ganar electrones?',
        'acierto'     => $aciertos['tendencia'],
        'tuya'        => $opcionesTendencia[$tuyas['tendencia']] ?? '',
        'correcta'    => $opcionesTendencia[$perfil['tendencia']],
        'explicacion' => match ($perfil['tendencia']) {
            'ganar'     => 'Con electronegatividad alta atrae electrones, y con energía de ionización alta le cuesta perderlos.',
            'perder'    => 'Con energía de ionización baja pierde electrones con facilidad, y con electronegatividad baja casi no los atrae.',
            'compartir' => 'Con valores intermedios, ni gana ni pierde con facilidad.',
        },
    ];
    $correccion[] = [
        'pregunta'    => 'd. ¿Qué tipo de ion esperarías que formara?',
        'acierto'     => $aciertos['ion'],
        'tuya'        => $opcionesIon[$tuyas['ion']] ?? '',
        'correcta'    => $opcionesIon[$perfil['ion']],
        'explicacion' => match ($perfil['ion']) {
            'anion'   => 'Si gana electrones, le sobran cargas negativas: anión.',
            'cation'  => 'Si pierde electrones, le sobran protones: catión.',
            'ninguno' => 'Si comparte los electrones, no se forma ningún ion.',
        },
    ];
    $correccion[] = [
        'pregunta'    => $preguntaHacia,
        'acierto'     => $aciertos['hacia'],
        'tuya'        => ucfirst($tuyas['hacia']),
        'correcta'    => ucfirst($tendencias[$tProp][$tDir][0]),
        'explicacion' => $tendencias[$tProp][$tDir][1],
    ];

    // Qué zona se resalta en la tabla: los elementos que mejor representan
    // al perfil. array_filter() conserva las claves, que es justo lo que
    // necesita tabla.php para saber cuáles resaltar.
    $categoriasZona = match ($caso['tipo']) {
        'metal'     => ['alcalino', 'alcalinoterreo'],
        'no metal'  => ['nometal', 'halogeno'],
        'metaloide' => ['metaloide'],
    };
    $resaltados = array_filter(
        $elementos,
        fn($el) => in_array(categoria_elemento($el), $categoriasZona, true) && $el['NumeroAtomico'] !== 1
    );
}

$titulo = 'Propiedades periódicas';
$activo = 'ejercicios';
require __DIR__ . '/includes/cabecera.php';
?>

<p class="miga"><a href="ejercicios.php">← Ejercicios</a> · Tema: propiedades periódicas</p>

<div class="quiz-cabecera">
    <h1 class="titulo-pagina">Propiedades periódicas</h1>
    <?php require __DIR__ . '/includes/marcador.php'; ?>
</div>

<?php require __DIR__ . '/includes/aviso_ronda.php'; ?>

<div class="caja-pistas">
    <p>Un elemento desconocido presenta:</p>
    <ul>
        <?php foreach ($caso['dadas'] as $prop): ?>
            <li><?= e(describir($prop, $perfil[$prop], $propiedades)) ?>;</li>
        <?php endforeach; ?>
    </ul>
</div>

<?php if (!$corregido): ?>

    <details class="ayuda">
        <summary>Ayuda-memoria</summary>
        <ul>
            <li>Hacia <strong>arriba a la derecha</strong>: el radio <strong>disminuye</strong>; la electronegatividad
                y la energía de ionización <strong>aumentan</strong>; el carácter metálico <strong>disminuye</strong>.</li>
            <li>Hacia <strong>abajo a la izquierda</strong>, todo al revés.</li>
            <li>Los gases nobles casi no forman compuestos: no se les mide la electronegatividad.</li>
        </ul>
    </details>

    <form class="quiz-form" method="post" action="propiedades.php">
    <input type="hidden" name="ronda" value="<?= e($estado['id']) ?>">

        <?php foreach ($faltan as $prop): ?>
            <div class="quiz-fila">
                <label for="p-<?= $prop ?>">Deducí: ¿cómo será su <?= e(mb_strtolower($propiedades[$prop]['nombre'])) ?>?</label>
                <select id="p-<?= $prop ?>" name="prop_<?= $prop ?>">
                    <option value="">—</option>
                    <?php foreach ($propiedades[$prop]['valores'] as $valor): ?>
                        <option value="<?= e($valor) ?>"><?= e(ucfirst($valor)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endforeach; ?>

        <?php
        // Las cuatro preguntas con opciones se arman con el mismo bucle: cada
        // una es [nombre del campo, texto de la pregunta, opciones].
        $selects = [
            ['zona', 'a. ¿En qué zona de la tabla lo buscarías?', $opcionesZona],
            ['tipo', 'b. ¿Es más probable que sea metal, no metal o metaloide?', $opcionesTipo],
            ['tendencia', 'c. ¿Tendría mayor tendencia a perder o a ganar electrones?', $opcionesTendencia],
            ['ion', 'd. ¿Qué tipo de ion esperarías que formara, si formara uno?', $opcionesIon],
        ];
        ?>
        <?php foreach ($selects as [$campo, $pregunta, $opciones]): ?>
            <div class="quiz-fila">
                <label for="p-<?= $campo ?>"><?= e($pregunta) ?></label>
                <select id="p-<?= $campo ?>" name="<?= $campo ?>">
                    <option value="">—</option>
                    <?php foreach ($opciones as $valor => $texto): ?>
                        <option value="<?= e($valor) ?>"><?= e($texto) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endforeach; ?>

        <div class="quiz-fila">
            <label for="p-hacia"><?= e($preguntaHacia) ?></label>
            <select id="p-hacia" name="hacia">
                <option value="">—</option>
                <option value="aumenta">Aumenta</option>
                <option value="disminuye">Disminuye</option>
            </select>
        </div>

        <button type="submit" class="boton-grande">Corregir</button>
    </form>

<?php else: ?>

    <?php require __DIR__ . '/includes/correccion.php'; ?>

    <div class="aviso">
        <strong>Una justificación completa (la e):</strong>
        <?= e(justificacion($caso['tipo'])) ?>
    </div>


    <section class="resultados">
        <h2>La zona en la tabla</h2>
        <?php require __DIR__ . '/includes/tabla.php'; ?>
    </section>

<?php endif; ?>

<?php
$pagina = 'propiedades.php';
require __DIR__ . '/includes/acciones.php';
?>

<?php require __DIR__ . '/includes/pie.php';
