<?php
/**
 * =============================================================================
 *  particulas.php  —  EJERCICIO: COMPLETAR LA TABLA DE PARTÍCULAS
 * =============================================================================
 *
 *  Tema: partículas subatómicas, número atómico y másico, iones. Una tabla
 *  con átomos e iones (²⁰Ne, ¹³C, ¹⁹F⁻, ²³Na⁺) donde hay que completar el
 *  tipo de partícula, el símbolo, Z, protones, electrones, neutrones, A y la
 *  carga.
 *
 *  Cada ronda sortea cuatro especies (al menos un átomo neutro, un catión y un
 *  anión). La especie siempre se da escrita, con su número másico y su carga:
 *  de ahí sale todo lo demás. Además viene un número de regalo (Z, p o n).
 *
 *  Las cuentas que hay que saber son tres, y están en la ayuda-memoria:
 *
 *      Z = p            A = p + n            e = p − carga
 *
 *  Sigue el mismo esquema que quiz.php: la ronda sorteada y las respuestas
 *  correctas viven en la SESIÓN (no en el HTML, que se puede editar), y
 *  después de corregir se hace POST → Redirect → GET.
 * =============================================================================
 */

session_start();

require_once __DIR__ . '/includes/quimica.php';
$elementos = require __DIR__ . '/includes/tabla_periodica.php';

$ejercicio = 'particulas';

// Las columnas de la tabla, en el orden en que suelen aparecer.
$columnas = [
    'tipo'    => 'Tipo de partícula',
    'simbolo' => 'Símbolo',
    'Z'       => 'Z',
    'p'       => 'p',
    'e'       => 'e',
    'n'       => 'n',
    'A'       => 'A',
    'carga'   => 'Carga',
];

$tiposParticula = [
    'neutro' => 'Átomo neutro',
    'cation' => 'Catión',
    'anion'  => 'Anión',
];


/**
 * El tipo de partícula según la carga. Con carga 0 es un átomo neutro; si le
 * faltan electrones queda positivo (catión); si le sobran, negativo (anión).
 */
function tipo_particula(int $carga): string
{
    return match (true) {
        $carga > 0 => 'cation',
        $carga < 0 => 'anion',
        default    => 'neutro',
    };
}


/**
 * Sortea las cuatro filas de una ronda.
 *
 * Primero arma TODAS las especies posibles combinando cada isótopo con cada
 * carga que puede tener ese elemento. Después elige una de cada tipo (neutro,
 * catión, anión) y una cuarta de cualquiera, sin repetir elemento.
 */
function sortear_filas(array $elementos): array
{
    $especies = [];
    $iones    = iones_comunes();

    foreach (isotopos() as $z => $masas) {
        // array_merge([0], ...) = "la carga 0 más las de sus iones".
        $cargas = array_merge([0], $iones[$z] ?? []);

        foreach ($masas as $a) {
            foreach ($cargas as $carga) {
                $especies[] = ['z' => $z, 'a' => $a, 'carga' => $carga];
            }
        }
    }

    $tipos = ['neutro', 'cation', 'anion', uno_al_azar(['neutro', 'cation', 'anion'])];
    shuffle($tipos);   // shuffle() mezcla una lista, como random.shuffle()

    $filas  = [];
    $usados = [];

    foreach ($tipos as $tipo) {
        $candidatas = array_filter(
            $especies,
            fn($s) => tipo_particula($s['carga']) === $tipo && !in_array($s['z'], $usados, true)
        );

        $especie  = uno_al_azar($candidatas);
        $usados[] = $especie['z'];

        // Además de la especie escrita, un número de regalo.
        $especie['dados'] = [uno_al_azar(['Z', 'p', 'n'])];

        $especie['clave'] = clave_por_z($elementos, $especie['z']);
        $filas[] = $especie;
    }

    return $filas;
}


/**
 * Los valores correctos de todas las columnas de una fila.
 */
function valores_fila(array $fila, array $elementos): array
{
    $z     = $fila['z'];
    $carga = $fila['carga'];

    return [
        'tipo'    => tipo_particula($carga),
        'simbolo' => $elementos[$fila['clave']]['Simbolo'],
        'Z'       => $z,
        'p'       => $z,                // los protones SON el número atómico
        'e'       => $z - $carga,       // cada carga + es un electrón de menos
        'n'       => $fila['a'] - $z,   // A = p + n   →   n = A − p
        'A'       => $fila['a'],
        'carga'   => $carga,
    ];
}


/**
 * Cómo se muestra un valor correcto en la tabla.
 */
function mostrar_valor(string $campo, mixed $valor, array $tiposParticula): string
{
    return match ($campo) {
        'tipo'  => $tiposParticula[$valor],
        'carga' => carga_texto($valor),
        default => (string) $valor,
    };
}


/**
 * Corrige un casillero. Devuelve si está bien y, si hace falta, una nota.
 */
function corregir_casillero(string $campo, string $respuesta, mixed $correcto): array
{
    $respuesta = trim($respuesta);
    $nota      = '';

    switch ($campo) {
        case 'tipo':
            $acierto = $respuesta === $correcto;
            break;

        case 'simbolo':
            // En química las mayúsculas IMPORTAN: Co es el cobalto y CO es el
            // monóxido de carbono. Por eso "na" no vale como "Na", pero le
            // avisamos que el error fue sólo de mayúsculas.
            $acierto = $respuesta === $correcto;
            if (!$acierto && strcasecmp($respuesta, $correcto) === 0) {
                $nota = 'Ojo con las mayúsculas: la primera letra va en mayúscula y la segunda en minúscula.';
            }
            break;

        case 'carga':
            $leida   = leer_carga($respuesta);
            $acierto = $leida === $correcto;
            if ($leida === null && $respuesta !== '' && $correcto !== 0) {
                $nota = 'La carga lleva signo: +1, −2...';
            }
            break;

        default:
            $acierto = leer_entero($respuesta) === $correcto;
    }

    return ['acierto' => $acierto, 'nota' => $nota];
}


/**
 * La explicación paso a paso de una fila, para después de corregir.
 */
function explicar_fila(array $fila, array $v, array $elementos): string
{
    $nombre = mb_strtolower($elementos[$fila['clave']]['Nombre']);
    $cargaT = carga_texto($v['carga']);

    $texto = "Z = {$v['Z']}: es el {$nombre} ({$v['simbolo']}), y p = Z = {$v['p']}. "
        . "n = A − Z = {$v['A']} − {$v['Z']} = {$v['n']}. "
        . "e = p − carga = {$v['p']} − ({$cargaT}) = {$v['e']}. ";

    $texto .= match ($v['tipo']) {
        'neutro' => 'Tiene tantos electrones como protones: es un átomo neutro.',
        'cation' => 'Tiene menos electrones que protones (los perdió): es un catión.',
        'anion'  => 'Tiene más electrones que protones (los ganó): es un anión.',
    };

    return $texto;
}


/* ===========================================================================
 *  ATENDER EL FORMULARIO
 * =========================================================================== */

if (ronda_contestada($ejercicio, 'resultados')) {

    $filas      = $_SESSION['particulas']['filas'];
    $resultados = [];
    $bien       = 0;
    $total      = 0;

    foreach ($filas as $i => $fila) {
        $valores = valores_fila($fila, $elementos);

        foreach ($columnas as $campo => $titulo) {
            // Los datos que venían dados no se corrigen.
            if (in_array($campo, $fila['dados'], true)) {
                continue;
            }

            // El (string) cubre el caso de que alguien mande un array en vez
            // de un texto editando el formulario.
            $respuesta = (string) ($_POST['r'][$i][$campo] ?? '');
            $r = corregir_casillero($campo, $respuesta, $valores[$campo]);
            $r['tuya'] = trim($respuesta);

            $resultados[$i][$campo] = $r;
            $total++;
            $bien += $r['acierto'] ? 1 : 0;
        }
    }

    sumar_al_marcador($ejercicio, $bien, $total);

    $_SESSION['particulas']['resultados'] = $resultados;
    header('Location: particulas.php');
    exit;
}

// Llegaron respuestas, pero de una ronda que ya no está abierta (otra
// pestaña, o el botón "atrás"): no se corrigen, y se avisa.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    descartar_ronda_vieja($ejercicio);
    header('Location: particulas.php');
    exit;
}

if (isset($_GET['reiniciar'])) {
    reiniciar_marcador($ejercicio);
    unset($_SESSION['particulas']);
    header('Location: particulas.php');
    exit;
}


/* ===========================================================================
 *  PREPARAR LO QUE SE VA A MOSTRAR
 * =========================================================================== */

// Cada visita, un caso nuevo (ver preparar_ronda() en includes/quimica.php).
$estado = preparar_ronda($ejercicio, 'resultados', fn() => ['filas' => sortear_filas($elementos)]);

$filas      = $estado['filas'];
$resultados = $estado['resultados'] ?? null;
$corregido  = $resultados !== null;

$titulo = 'Partículas';
$activo = 'ejercicios';
require __DIR__ . '/includes/cabecera.php';
?>

<p class="miga"><a href="ejercicios.php">← Ejercicios</a> · Tema: partículas subatómicas, número atómico y másico, iones</p>

<div class="quiz-cabecera">
    <h1 class="titulo-pagina">Completá la tabla</h1>
    <?php require __DIR__ . '/includes/marcador.php'; ?>
</div>

<?php require __DIR__ . '/includes/aviso_ronda.php'; ?>

<p class="bajada">
    Completá cada casillero vacío a partir del átomo o ion de la primera
    columna: el número de arriba a la izquierda es A, y lo de arriba a la
    derecha, la carga.
</p>

<details class="ayuda">
    <summary>Ayuda-memoria</summary>
    <ul>
        <li><strong>Z = p</strong>: el número atómico es la cantidad de protones, y dice qué elemento es.</li>
        <li><strong>A = p + n</strong>: el número másico suma protones y neutrones. Entonces <strong>n = A − Z</strong>.</li>
        <li><strong>e = p − carga</strong>. Átomo neutro: e = p. Catión (+): perdió electrones. Anión (−): ganó electrones.</li>
        <li>En <span class="notacion"><sup>23</sup>Na<sup>+</sup></span> el número de arriba a la izquierda es A, y lo de arriba a la derecha, la carga.</li>
        <li>Los protones y los neutrones <strong>nunca</strong> cambian al formar un ion: sólo cambian los electrones.</li>
    </ul>
</details>

<?php if (!$corregido): ?>
<form method="post" action="particulas.php">
    <input type="hidden" name="ronda" value="<?= e($estado['id']) ?>">
<?php endif; ?>

<div class="tabla-scroll">
    <table class="ej-tabla">
        <thead>
            <tr>
                <th>Elemento</th>
                <?php foreach ($columnas as $tituloCol): ?>
                    <th><?= e($tituloCol) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($filas as $i => $fila): ?>
            <?php $valores = valores_fila($fila, $elementos); ?>
            <tr>
                <th>
                    <?= notacion_html($fila['a'], $valores['simbolo'], $fila['carga']) ?>
                </th>

                <?php foreach ($columnas as $campo => $tituloCol): ?>
                    <?php
                    $esDado   = in_array($campo, $fila['dados'], true);
                    $correcto = mostrar_valor($campo, $valores[$campo], $tiposParticula);
                    ?>

                    <?php if ($esDado): ?>
                        <!-- Un dato que viene de regalo: se muestra y no se edita. -->
                        <td class="dado"><?= e($correcto) ?></td>

                    <?php elseif ($corregido): ?>
                        <?php
                        $r    = $resultados[$i][$campo];
                        $tuya = $campo === 'tipo' ? ($tiposParticula[$r['tuya']] ?? '') : $r['tuya'];
                        ?>
                        <td class="<?= $r['acierto'] ? 'casillero-ok' : 'casillero-mal' ?>"
                            <?php if ($r['nota'] !== ''): ?>title="<?= e($r['nota']) ?>"<?php endif; ?>>
                            <?php if ($r['acierto']): ?>
                                <?= e($correcto) ?>
                            <?php else: ?>
                                <s><?= e($tuya === '' ? '—' : $tuya) ?></s>
                                <strong><?= e($correcto) ?></strong>
                            <?php endif; ?>
                        </td>

                    <?php elseif ($campo === 'tipo'): ?>
                        <td>
                            <select name="r[<?= $i ?>][tipo]" aria-label="Tipo de partícula, fila <?= $i + 1 ?>">
                                <option value="">—</option>
                                <?php foreach ($tiposParticula as $valor => $texto): ?>
                                    <option value="<?= $valor ?>"><?= e($texto) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>

                    <?php else: ?>
                        <td>
                            <input type="text"
                                   name="r[<?= $i ?>][<?= $campo ?>]"
                                   aria-label="<?= e($tituloCol) ?>, fila <?= $i + 1 ?>"
                                   autocomplete="off"
                                   <?= $campo === 'simbolo' || $campo === 'carga' ? '' : 'inputmode="numeric"' ?>>
                        </td>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<p class="nota-tabla">Los números en <span class="dado">azul</span> vienen de regalo.</p>

<?php if (!$corregido): ?>
    <p class="centrado">
        <button type="submit" class="boton-grande">Corregir</button>
    </p>
</form>
<?php else: ?>

    <h2 class="subtitulo">Cómo se resolvía cada fila</h2>
    <ul class="quiz-resultados">
        <?php foreach ($filas as $i => $fila): ?>
            <?php
            $valores = valores_fila($fila, $elementos);
            $notas   = array_filter(array_column($resultados[$i], 'nota'));
            $todoOk  = !in_array(false, array_column($resultados[$i], 'acierto'), true);
            ?>
            <li class="<?= $todoOk ? 'ok' : 'mal' ?>">
                <span class="res-icono"><?= $todoOk ? '✓' : '✗' ?></span>
                <div>
                    <span class="res-preg"><?= notacion_html($fila['a'], $valores['simbolo'], $fila['carga']) ?></span>
                    <span class="res-explica"><?= e(explicar_fila($fila, $valores, $elementos)) ?></span>
                    <?php foreach (array_unique($notas) as $nota): ?>
                        <span class="res-explica">⚠ <?= e($nota) ?></span>
                    <?php endforeach; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

<?php endif; ?>

<?php
$pagina = 'particulas.php';
require __DIR__ . '/includes/acciones.php';
?>

<?php require __DIR__ . '/includes/pie.php';
