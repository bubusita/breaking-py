<?php
/**
 * =============================================================================
 *  quiz.php  —  OPCIÓN [3]: EL QUIZ
 * =============================================================================
 *
 *  Traduce la función quiz() de Breaking_Py.py:
 *
 *      elemento_al_azar = random.choice(list(elementos.keys()))
 *      global score
 *      primera_pregunta = input("\nNumero Atómico: ")
 *      ...
 *      if quitar_tildes(primera_pregunta.lower()) == str(datos["NumeroAtomico"]).strip():
 *          print("Número Atómico: Correcto!")
 *          score += 1
 *      else:
 *          score -= 1
 *
 *  ┌──────────────────────────────────────────────────────────────────────┐
 *  │  ADIÓS A LA VARIABLE  global score                                    │
 *  └──────────────────────────────────────────────────────────────────────┘
 *
 *  En Python usabas  global score  para que el puntaje sobreviviera entre
 *  partidas. Funcionaba porque el programa nunca terminaba.
 *
 *  En la web eso no alcanza: cada clic es un programa nuevo y una variable
 *  global se borra igual que cualquier otra. El puntaje va a la SESIÓN:
 *
 *      $_SESSION['puntaje']
 *
 *  Y hay algo más que la sesión resuelve. En la consola, el elemento sorteado
 *  quedaba en una variable mientras vos contestabas. En la web, entre que se
 *  muestra la pregunta y que se contesta, el programa murió y arrancó otro:
 *  hay que GUARDAR cuál era el elemento sorteado, o al corregir no sabríamos
 *  contra qué comparar.
 *
 *  ¿Y por qué no mandarlo en un campo oculto del formulario? Porque el usuario
 *  puede editar el HTML del navegador y cambiarlo. Todo lo que el usuario NO
 *  debe poder tocar (la respuesta correcta, el puntaje) va en la sesión, que
 *  vive en el servidor. Es la misma idea que "no confiar en la entrada".
 * =============================================================================
 */

session_start();

require_once __DIR__ . '/includes/funciones.php';
$elementos = require __DIR__ . '/includes/tabla_periodica.php';

// Las siete preguntas, en el mismo orden que en la consola.
// La clave es el campo del elemento; el valor, el texto que se le muestra.
$preguntas = [
    'NumeroAtomico' => 'Número atómico',
    'Simbolo'       => 'Símbolo',
    'MasaAtomica'   => 'Masa atómica (número entero, sin decimales)',
    'Grupo'         => 'Grupo (si es lantánidos o actínidos, poné el nombre)',
    'Periodo'       => 'Período',
    'Bloque'        => 'Bloque (s, p, d, f)',
    'Radiactivo'    => 'Radiactividad (si / no)',
];

// El puntaje arranca en 0 la primera visita y después vive en la sesión.
$_SESSION['puntaje'] = $_SESSION['puntaje'] ?? 0;


/**
 * Compara una respuesta con el valor correcto del elemento.
 *
 * Devuelve un array con tres cosas: si acertó, qué contestó y cuál era la
 * respuesta buena. En Python habrías devuelto una tupla; en PHP se devuelve
 * un array y listo (no existen las tuplas como tipo aparte).
 *
 * OJO A ESTE DETALLE, que en el original era un bug:
 * el grupo de los lantánidos está guardado como "Lantánidos", CON tilde, pero
 * la respuesta del usuario pasaba por quitar_tildes() y quedaba "lantanidos".
 * Nunca podían coincidir. Acá le pasamos quitar_tildes() a LOS DOS LADOS, así
 * la comparación es justa. Moraleja: cuando compares textos, normalizá siempre
 * los dos, no sólo uno.
 */
function corregir(string $campo, string $respuesta, array $datos): array
{
    $respuesta = quitar_tildes($respuesta);

    // Según el campo, armamos cuál es la respuesta correcta.
    $correcta = match ($campo) {

        // floor() redondea para abajo. Es el  //  de Python:
        //     Masa_atomica = masa_atomica // 1
        'MasaAtomica' => (string) floor($datos['MasaAtomica']),

        // El original aceptaba "si"/"no"; guardamos el texto que corresponda.
        'Radiactivo'  => $datos['Radiactivo'] ? 'si' : 'no',

        // Para todo lo demás alcanza con pasar el valor a texto.
        default       => (string) $datos[$campo],
    };

    $correctaNormalizada = quitar_tildes($correcta);

    return [
        'acierto'  => $respuesta === $correctaNormalizada,
        'tuya'     => $respuesta,
        'correcta' => $correcta,
    ];
}


/* ===========================================================================
 *  ATENDER EL FORMULARIO (corregir las respuestas)
 * =========================================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['quiz_clave'])) {

    $clave = $_SESSION['quiz_clave'];
    $datos = $elementos[$clave];

    $resultados = [];

    // Recorremos las siete preguntas y corregimos una por una.
    foreach ($preguntas as $campo => $textoPregunta) {

        // $_POST['respuestas'] es un ARRAY, porque en el HTML los campos se
        // llaman  respuestas[NumeroAtomico],  respuestas[Simbolo], etc.
        // PHP arma solo el array a partir de esos corchetes: un truco muy útil
        // para formularios con muchos campos parecidos.
        $respuesta = trim($_POST['respuestas'][$campo] ?? '');

        $resultado = corregir($campo, $respuesta, $datos);
        $resultado['pregunta'] = $textoPregunta;
        $resultados[$campo] = $resultado;

        // El +1 / -1 del original, tal cual.
        $_SESSION['puntaje'] += $resultado['acierto'] ? 1 : -1;
    }

    // Guardamos la corrección para mostrarla después del redirect.
    $_SESSION['quiz_resultados'] = $resultados;
    $_SESSION['quiz_corregido']  = $clave;

    // Mismo patrón POST → Redirect → GET que en filtrar.php: evita que apretar
    // F5 vuelva a corregir las respuestas y sume o reste puntos de nuevo.
    header('Location: quiz.php');
    exit;
}

// "¿Querés jugar de nuevo?" → sortear otro elemento.
if (isset($_GET['nuevo'])) {
    unset($_SESSION['quiz_clave'], $_SESSION['quiz_resultados'], $_SESSION['quiz_corregido']);
    header('Location: quiz.php');
    exit;
}

// Poner el marcador en cero.
if (isset($_GET['reiniciar'])) {
    $_SESSION['puntaje'] = 0;
    unset($_SESSION['quiz_clave'], $_SESSION['quiz_resultados'], $_SESSION['quiz_corregido']);
    header('Location: quiz.php');
    exit;
}


/* ===========================================================================
 *  PREPARAR LO QUE SE VA A MOSTRAR
 * =========================================================================== */

$resultados = $_SESSION['quiz_resultados'] ?? null;

if ($resultados !== null) {
    // Venimos de corregir: mostramos el elemento que se acaba de contestar.
    $clave = $_SESSION['quiz_corregido'];
} else {
    // Pregunta nueva: sorteamos un elemento si todavía no hay uno pendiente.
    //
    //     Python:  random.choice(list(elementos.keys()))
    //     PHP:     array_rand($elementos)     ← ya devuelve una clave al azar
    //
    // Guardamos SÓLO la clave en la sesión (no todos los datos): ocupa menos y
    // con la clave siempre podemos volver a buscar el elemento completo.
    if (!isset($_SESSION['quiz_clave'])) {
        $_SESSION['quiz_clave'] = array_rand($elementos);
    }
    $clave = $_SESSION['quiz_clave'];
}

$datos   = $elementos[$clave];
$puntaje = $_SESSION['puntaje'];

$titulo = 'Quiz';
$activo = 'quiz';
require __DIR__ . '/includes/cabecera.php';
?>

<div class="quiz-cabecera">
    <h1 class="titulo-pagina">Quiz</h1>

    <!--
        El marcador. La clase cambia según el signo del puntaje, así que se
        pinta verde, gris o rojo. Es un ternario adentro de otro ternario:
        se lee "si es mayor a 0 → positivo; si no, si es menor a 0 → negativo;
        si no → neutro".
    -->
    <div class="marcador <?= $puntaje > 0 ? 'positivo' : ($puntaje < 0 ? 'negativo' : 'neutro') ?>">
        <span class="marcador-lbl">Puntaje</span>
        <span class="marcador-num"><?= $puntaje ?></span>
    </div>
</div>

<?php if ($resultados === null): ?>

    <!-- ── MODO PREGUNTA ──────────────────────────────────────────────── -->
    <p class="bajada">
        Te tocó un elemento al azar. Escribí sus siete características:
        cada acierto suma 1 punto y cada error resta 1.
    </p>

    <div class="quiz-elemento">
        <span class="quiz-nombre"><?= e($datos['Nombre']) ?></span>
    </div>

    <form class="quiz-form" method="post" action="quiz.php">
        <?php foreach ($preguntas as $campo => $textoPregunta): ?>
            <div class="quiz-fila">
                <label for="p-<?= e($campo) ?>"><?= e($textoPregunta) ?></label>
                <!--
                    El name con corchetes hace que PHP reciba todo junto en
                    $_POST['respuestas'], como se explicó más arriba.
                -->
                <input type="text"
                       id="p-<?= e($campo) ?>"
                       name="respuestas[<?= e($campo) ?>]"
                       autocomplete="off">
            </div>
        <?php endforeach; ?>

        <button type="submit" class="boton-grande">Corregir</button>
    </form>

<?php else: ?>

    <!-- ── MODO RESULTADOS ────────────────────────────────────────────── -->
    <?php
    // array_filter + count para contar los aciertos, en vez de un contador
    // a mano dentro de un for.
    $aciertos = count(array_filter($resultados, fn($r) => $r['acierto']));
    ?>

    <p class="bajada">
        Resultados del <strong><?= e($datos['Nombre']) ?></strong>:
        acertaste <strong><?= $aciertos ?> de <?= count($resultados) ?></strong>.
    </p>

    <ul class="quiz-resultados">
        <?php foreach ($resultados as $r): ?>
            <li class="<?= $r['acierto'] ? 'ok' : 'mal' ?>">
                <span class="res-icono"><?= $r['acierto'] ? '✓' : '✗' ?></span>
                <div>
                    <span class="res-preg"><?= e($r['pregunta']) ?></span>
                    <?php if ($r['acierto']): ?>
                        <span class="res-det">¡Correcto! → <?= e($r['correcta']) ?></span>
                    <?php else: ?>
                        <span class="res-det">
                            Pusiste «<?= e($r['tuya'] === '' ? '(nada)' : $r['tuya']) ?>» ·
                            era <strong><?= e($r['correcta']) ?></strong>
                        </span>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <p class="centrado">
        <!-- El input("Quiere jugar de nuevo? (si/no)") del original -->
        <a class="boton-grande" href="quiz.php?nuevo=1">Jugar de nuevo</a>
        <a class="boton-secundario" href="quiz.php?reiniciar=1">Reiniciar puntaje</a>
        <a class="boton-secundario" href="elemento.php?nombre=<?= urlencode($datos['Nombre']) ?>">
            Ver la ficha del <?= e($datos['Nombre']) ?>
        </a>
    </p>

<?php endif; ?>

<?php require __DIR__ . '/includes/pie.php';
