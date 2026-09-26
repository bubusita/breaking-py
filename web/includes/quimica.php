<?php
/**
 * =============================================================================
 *  quimica.php  —  LA QUÍMICA QUE NECESITAN LOS EJERCICIOS
 * =============================================================================
 *
 *  Los ejercicios (particulas.php, misterioso.php, clasificar.php,
 *  propiedades.php y tabla_muda.php) necesitan datos que la tabla original no
 *  tenía: qué isótopos existen, qué iones forma cada elemento, si es gas o
 *  sólido, cuántos electrones tiene en el último nivel...
 *
 *  Todo eso vive acá, en UN solo archivo, por la misma razón que las
 *  categorías viven en funciones.php: si mañana hay que corregir un dato, se
 *  corrige en un lugar y se arregla en todos los ejercicios a la vez.
 *
 *  Casi todo se busca por NÚMERO ATÓMICO (Z), que es lo que identifica a un
 *  elemento sin discusión posible.
 * =============================================================================
 */

require_once __DIR__ . '/funciones.php';


/* =============================================================================
 *  BUSCAR UN ELEMENTO POR SU NÚMERO ATÓMICO
 * =============================================================================
 */

/**
 * Devuelve la clave del array ('sodio') del elemento con ese Z.
 *
 * El array de elementos está ordenado por nombre-clave, no por Z, así que hay
 * que recorrerlo. Son 118 vueltas: para una computadora es nada.
 */
function clave_por_z(array $elementos, int $z): ?string
{
    foreach ($elementos as $clave => $el) {
        if ($el['NumeroAtomico'] === $z) {
            return $clave;
        }
    }

    return null;
}


/* =============================================================================
 *  METAL, NO METAL O METALOIDE
 * =============================================================================
 *
 *  Las once categorías de colores (funciones.php) se agrupan en las tres
 *  grandes clases que se piden en la prueba. Los gases nobles son NO METALES:
 *  la categoría aparte es sólo porque tienen la última capa completa.
 */
function tipo_elemento(array $el): ?string
{
    return match (categoria_elemento($el)) {
        'alcalino', 'alcalinoterreo', 'transicion', 'postransicion',
        'lantanido', 'actinido'                  => 'metal',
        'metaloide'                              => 'metaloide',
        'nometal', 'halogeno', 'noble'           => 'no metal',
        default                                  => null,   // 113 a 118: no se sabe
    };
}


/**
 * El estado a temperatura ambiente (unos 25 °C).
 *
 * Son muy pocos los que no son sólidos, así que alcanza con dos listas cortas:
 * once gases y dos líquidos (el bromo y el mercurio). Todo lo demás, sólido.
 */
function estado_ambiente(array $el): string
{
    $gases    = [1, 2, 7, 8, 9, 10, 17, 18, 36, 54, 86];
    $liquidos = [35, 80];

    $z = $el['NumeroAtomico'];

    if (in_array($z, $gases, true)) {
        return 'gas';
    }
    if (in_array($z, $liquidos, true)) {
        return 'líquido';
    }
    return 'sólido';
}


/* =============================================================================
 *  LOS ELECTRONES Y LOS NIVELES
 * =============================================================================
 *
 *  En el modelo que se usa en 3º año (el de Bohr), los electrones se ubican en
 *  niveles: 2 en el primero, 8 en el segundo, 8 en el tercero... y así
 *  hasta el calcio (Z = 20). Más allá del calcio aparecen los metales de
 *  transición y el tercer nivel se sigue llenando hasta 18: para eso hace falta
 *  un modelo más completo, que se ve más adelante. Por eso el ejercicio del
 *  elemento misterioso usa sólo los elementos del 3 al 20.
 */

/**
 * Reparte Z electrones en niveles de 2, 8, 8 y 2. Sirve hasta Z = 20.
 *
 *     distribucion_niveles(17)  →  [2, 8, 7]
 *
 * min() devuelve el más chico de sus argumentos, igual que en Python.
 * El nivel se llena con lo que entre o con lo que quede, lo que sea menor.
 */
function distribucion_niveles(int $z): array
{
    $capacidades = [2, 8, 8, 2];
    $niveles     = [];
    $quedan      = $z;

    foreach ($capacidades as $capacidad) {
        if ($quedan <= 0) {
            break;
        }
        $enEsteNivel = min($capacidad, $quedan);
        $niveles[]   = $enEsteNivel;   // $lista[] = x  es el  lista.append(x)  de Python
        $quedan     -= $enEsteNivel;
    }

    return $niveles;
}


/**
 * Electrones en el último nivel de un elemento REPRESENTATIVO.
 *
 * Es la regla que conviene saber de memoria:
 *   - grupos 1 y 2  → 1 y 2 electrones,
 *   - grupos 13 a 18 → el grupo menos 10 (13 → 3, ..., 17 → 7, 18 → 8).
 * El helio es la excepción: está en el grupo 18 pero tiene sólo 2, porque el
 * primer nivel no admite más.
 */
function electrones_ultimo_nivel(array $el): ?int
{
    $grupo = $el['Grupo'];

    if (!is_numeric($grupo)) {
        return null;
    }
    if ($el['NumeroAtomico'] === 2) {
        return 2;
    }
    if ($grupo <= 2) {
        return (int) $grupo;
    }
    if ($grupo >= 13) {
        return $grupo - 10;
    }
    return null;   // transición: la regla simple no alcanza
}


/**
 * ¿Qué hace el elemento para estabilizarse? 'perder', 'ganar', 'compartir' o
 * 'ninguna' (si ya tiene la última capa completa).
 *
 * La idea: llegar a 8 electrones en la última capa por el camino más corto.
 *   - Con 1, 2 o 3 es más fácil PERDERLOS que conseguir 5, 6 o 7 más.
 *   - Con 5, 6 o 7 es más fácil GANAR los 3, 2 o 1 que faltan.
 *   - Con 4 (carbono, silicio) los dos caminos cuestan lo mismo: los
 *     COMPARTE. Y los metaloides, en general, también comparten.
 */
function tendencia_electronica(array $el): ?string
{
    $categoria = categoria_elemento($el);
    $ultimo    = electrones_ultimo_nivel($el);

    if ($categoria === 'noble') {
        return 'ninguna';
    }
    if ($categoria === 'metaloide') {
        return 'compartir';
    }
    if (tipo_elemento($el) === 'metal') {
        return 'perder';
    }
    if ($ultimo === null) {
        return null;
    }
    if ($ultimo === 4) {
        return 'compartir';
    }
    return $ultimo >= 5 ? 'ganar' : 'perder';
}


/* =============================================================================
 *  ISÓTOPOS E IONES  (para el ejercicio de partículas)
 * =============================================================================
 *
 *  Los isótopos son átomos del mismo elemento (mismo Z, mismos protones) con
 *  distinta cantidad de neutrones, y por eso distinto número másico A.
 *
 *  La lista tiene los isótopos que existen de verdad: los estables, y algunos
 *  radiactivos muy conocidos (el carbono 14, el tritio, el uranio 235).
 *  Así el ejercicio nunca pide un ²⁵Ne que no existe.
 */
function isotopos(): array
{
    return [
        1  => [1, 2, 3],
        2  => [3, 4],
        3  => [6, 7],
        4  => [9],
        5  => [10, 11],
        6  => [12, 13, 14],
        7  => [14, 15],
        8  => [16, 17, 18],
        9  => [19],
        10 => [20, 21, 22],
        11 => [23],
        12 => [24, 25, 26],
        13 => [27],
        14 => [28, 29, 30],
        15 => [31],
        16 => [32, 33, 34],
        17 => [35, 37],
        18 => [36, 38, 40],
        19 => [39, 41],
        20 => [40, 42, 44],
        26 => [54, 56, 57],
        29 => [63, 65],
        30 => [64, 66, 68],
        35 => [79, 81],
        47 => [107, 109],
        53 => [127],
        79 => [197],
        82 => [206, 207, 208],
        92 => [235, 238],
    ];
}


/**
 * Los iones que forma cada elemento, con su carga.
 *
 * En los representativos la carga sale del grupo (pierde o gana lo justo para
 * llegar a 8). En los de transición no hay una regla simple y hay que
 * saberlos: por eso el hierro y el cobre tienen dos.
 */
function iones_comunes(): array
{
    return [
        1  => [1],          // H⁺ : se queda sin ningún electrón
        3  => [1],
        4  => [2],
        7  => [-3],
        8  => [-2],
        9  => [-1],
        11 => [1],
        12 => [2],
        13 => [3],
        15 => [-3],
        16 => [-2],
        17 => [-1],
        19 => [1],
        20 => [2],
        26 => [2, 3],
        29 => [1, 2],
        30 => [2],
        35 => [-1],
        47 => [1],
        53 => [-1],
        82 => [2],
    ];
}


/**
 * El ion que forma un elemento representativo, o null si no forma uno simple
 * (gases nobles, metaloides, carbono).
 */
function ion_comun(array $el): ?int
{
    $tendencia = tendencia_electronica($el);
    $ultimo    = electrones_ultimo_nivel($el);

    if ($ultimo === null || !in_array($tendencia, ['perder', 'ganar'], true)) {
        return null;
    }

    // Pierde todos los del último nivel, o gana los que le faltan para 8.
    return $tendencia === 'perder' ? $ultimo : -(8 - $ultimo);
}


/* =============================================================================
 *  ESCRIBIR Y LEER CARGAS
 * =============================================================================
 */

/**
 * La carga como se escribe ARRIBA del símbolo: '+', '2+', '−', '3−'.
 * Convención química: primero el número y después el signo, y el 1 no se
 * escribe (Na⁺, no Na¹⁺).
 *
 * El signo menos es el '−' tipográfico (U+2212), que es más largo que el
 * guión del teclado y queda alineado con el '+'.
 */
function carga_superindice(int $carga): string
{
    if ($carga === 0) {
        return '';
    }

    $signo  = $carga > 0 ? '+' : '−';
    $numero = abs($carga) === 1 ? '' : (string) abs($carga);

    return $numero . $signo;
}


/**
 * La carga como se escribe en una tabla: '0', '+1', '−2'.
 */
function carga_texto(int $carga): string
{
    if ($carga === 0) {
        return '0';
    }
    return ($carga > 0 ? '+' : '−') . abs($carga);
}


/**
 * La notación completa de una especie, en HTML: ²³Na⁺
 *
 * <sup> es "superíndice": el texto va chiquito y arriba. El número másico se
 * escribe arriba a la IZQUIERDA y la carga arriba a la DERECHA.
 */
function notacion_html(int $a, string $simbolo, int $carga): string
{
    $html = '<span class="notacion"><sup>' . $a . '</sup>' . e($simbolo);

    if ($carga !== 0) {
        $html .= '<sup>' . carga_superindice($carga) . '</sup>';
    }

    return $html . '</span>';
}


/**
 * Lee la carga que escribió la persona y la convierte en un número.
 *
 * Acepta todas las formas razonables: '0', '+1', '1+', '+', '-2', '2-', '−'.
 * Devuelve null si no se entiende, Y TAMBIÉN si falta el signo ('2'): una
 * carga sin signo está incompleta, y en la prueba te la marcarían mal.
 *
 * preg_match() busca un PATRÓN (una "expresión regular") en un texto. Es el
 * re.match() de Python. El patrón de acá se lee así:
 *
 *     ^          el principio del texto
 *     ([+-]?)    un signo opcional              → queda en $partes[1]
 *     (\d*)      cero o más dígitos             → queda en $partes[2]
 *     ([+-]?)    otro signo opcional            → queda en $partes[3]
 *     $          el final del texto
 */
function leer_carga(string $texto): ?int
{
    // Los signos "raros" (el menos tipográfico, la raya) pasan a ser un '-'
    // común, y se sacan los espacios.
    $texto = str_replace(['−', '–', '—', ' '], ['-', '-', '-', ''], trim($texto));

    if ($texto === '' || !preg_match('/^([+-]?)(\d*)([+-]?)$/', $texto, $partes)) {
        return null;
    }

    [, $signoAntes, $numero, $signoDespues] = $partes;

    // No vale poner signo adelante Y atrás.
    if ($signoAntes !== '' && $signoDespues !== '') {
        return null;
    }

    $signo = $signoAntes . $signoDespues;

    if ($signo === '') {
        // Sin signo sólo vale el cero.
        return $numero !== '' && (int) $numero === 0 ? 0 : null;
    }

    $valor = $numero === '' ? 1 : (int) $numero;

    return $signo === '-' ? -$valor : $valor;
}


/**
 * Lee un número entero escrito por la persona. Devuelve null si no es un
 * número (por ejemplo, si dejó el casillero vacío o escribió letras).
 *
 * ctype_digit() pregunta si TODOS los caracteres son dígitos. Es el
 * .isdigit() de Python.
 */
function leer_entero(string $texto): ?int
{
    $texto = trim($texto);

    return ctype_digit($texto) ? (int) $texto : null;
}


/* =============================================================================
 *  EL MARCADOR DE CADA EJERCICIO
 * =============================================================================
 *
 *  Igual que el puntaje del quiz, vive en la sesión. Cada ejercicio tiene su
 *  propio marcador, guardado bajo su nombre:
 *
 *      $_SESSION['marcadores']['particulas'] = ['bien' => 12, 'total' => 16]
 *
 *  Acá no se resta por error como en el quiz: la idea es practicar, y lo que
 *  interesa es qué porcentaje sale bien.
 */
function sumar_al_marcador(string $ejercicio, int $bien, int $total): void
{
    $actual = $_SESSION['marcadores'][$ejercicio] ?? ['bien' => 0, 'total' => 0];

    $_SESSION['marcadores'][$ejercicio] = [
        'bien'  => $actual['bien'] + $bien,
        'total' => $actual['total'] + $total,
    ];
}

function marcador_de(string $ejercicio): array
{
    return $_SESSION['marcadores'][$ejercicio] ?? ['bien' => 0, 'total' => 0];
}

function reiniciar_marcador(string $ejercicio): void
{
    unset($_SESSION['marcadores'][$ejercicio]);
}


/**
 * Elige un elemento al azar de una lista.
 *
 * random_int() da un entero al azar entre dos números (los dos incluidos).
 * Es más seguro que rand(), pero acá lo usamos porque es igual de fácil.
 * El array_values() renumera la lista desde 0, por si venía con huecos.
 */
function uno_al_azar(array $lista): mixed
{
    $lista = array_values($lista);

    return $lista[random_int(0, count($lista) - 1)];
}


/**
 * Une una lista como se escribe en castellano: «a, b y c».
 *
 * array_pop() saca el último elemento de la lista y lo devuelve (es el
 * .pop() de Python). Lo que queda se une con comas y el último va con «y».
 */
function lista_con_y(array $lista): string
{
    $lista = array_values($lista);

    if (count($lista) <= 1) {
        return $lista[0] ?? '';
    }

    $ultimo = array_pop($lista);

    return implode(', ', $lista) . ' y ' . $ultimo;
}


/* =============================================================================
 *  LA RONDA DE CADA EJERCICIO
 * =============================================================================
 *
 *  Regla: CADA VEZ QUE SE ABRE LA PANTALLA, PREGUNTAS NUEVAS. Si alguien
 *  entra, se va y vuelve, o recarga la página, le toca otro caso. La única
 *  excepción es justo después de corregir: ahí se muestra la corrección UNA
 *  vez, y la próxima visita ya sortea de nuevo.
 *
 *  Cada ronda lleva un 'id' al azar que viaja en un campo oculto del
 *  formulario. Sirve para una cosa: si hay dos pestañas abiertas, la de atrás
 *  tiene preguntas que ya no existen, y sin el id se corregirían sus
 *  respuestas contra las preguntas de la otra pestaña. (El id no es un
 *  secreto: las respuestas correctas siguen viviendo sólo en la sesión.)
 */

/**
 * Devuelve la ronda que hay que mostrar: la recién corregida (y la borra,
 * para que no se muestre dos veces) o una nueva, sorteada con $sortear.
 *
 * $sortear es una FUNCIÓN que se pasa como parámetro. En Python también se
 * podía (las funciones son valores como cualquier otro); en PHP el tipo se
 * llama  callable . Cada ejercicio le pasa su propia forma de sortear.
 */
function preparar_ronda(string $ejercicio, string $claveCorreccion, callable $sortear): array
{
    $estado = $_SESSION[$ejercicio] ?? [];

    if (isset($estado[$claveCorreccion])) {
        unset($_SESSION[$ejercicio]);
        return $estado;
    }

    // random_bytes() da bytes al azar y bin2hex() los pasa a texto: 'a3f09c1e'.
    $estado = $sortear() + ['id' => bin2hex(random_bytes(4))];
    $_SESSION[$ejercicio] = $estado;

    return $estado;
}

/**
 * ¿Llegaron respuestas para la ronda que está abierta ahora (y que todavía
 * no se corrigió)?
 *
 * hash_equals() compara dos textos como  ===  pero tardando siempre lo mismo;
 * es la forma recomendada de comparar cualquier código que venga del usuario.
 */
function ronda_contestada(string $ejercicio, string $claveCorreccion): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_SESSION[$ejercicio]['id'])
        && !isset($_SESSION[$ejercicio][$claveCorreccion])   // no corregir dos veces
        && hash_equals($_SESSION[$ejercicio]['id'], texto_recibido($_POST['ronda'] ?? ''));
}

/**
 * Llegaron respuestas de una ronda vieja (otra pestaña, o el botón "atrás").
 * Se anota para avisarlo, y la página vuelve a empezar con un caso nuevo.
 */
function descartar_ronda_vieja(string $ejercicio): void
{
    $_SESSION['ronda_vieja'][$ejercicio] = true;
    unset($_SESSION[$ejercicio]);
}

/**
 * ¿Hay que avisar que la ronda anterior se descartó? Se avisa una sola vez.
 */
function hubo_ronda_vieja(string $ejercicio): bool
{
    $hubo = $_SESSION['ronda_vieja'][$ejercicio] ?? false;
    unset($_SESSION['ronda_vieja'][$ejercicio]);

    return $hubo;
}
