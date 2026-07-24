<?php
/**
 * =============================================================================
 *  funciones.php  —  LAS FUNCIONES QUE USA TODO EL PROYECTO
 * =============================================================================
 *
 *  DE PYTHON A PHP: CÓMO SE DECLARA UNA FUNCIÓN
 *
 *      Python                          PHP
 *      ------------------------------  ------------------------------------
 *      def quitar_tildes(texto):       function quitar_tildes(string $texto)
 *          texto = texto.lower()       {
 *          return texto                    $texto = mb_strtolower($texto);
 *                                          return $texto;
 *                                      }
 *
 *  Diferencias que vas a notar enseguida:
 *
 *   1) PHP usa LLAVES  { }  para marcar el cuerpo de la función.
 *      La indentación en PHP es sólo para que los humanos lean mejor:
 *      al lenguaje le da igual. En Python la indentación ERA obligatoria.
 *   2) Todas las variables llevan  $  adelante.
 *   3) Se puede (y conviene) escribir el TIPO de cada parámetro y el tipo que
 *      devuelve la función:  function f(string $a): bool  { ... }
 *      Es opcional, pero ayuda a que PHP te avise si te equivocás.
 * =============================================================================
 */


/**
 * La versión del proyecto. Se muestra en el pie de todas las páginas.
 *
 * define() crea una CONSTANTE: un valor con nombre que no se puede cambiar
 * después. Se escribe en MAYÚSCULAS por costumbre y se usa sin el  $ .
 * En Python no existían de verdad (se usaba una variable en mayúsculas y
 * confiabas en no tocarla); en PHP el lenguaje te garantiza que nadie la pisa.
 *
 * El número sigue el "versionado semántico": MAYOR.MENOR.PARCHE
 *   - MAYOR  sube cuando algo deja de funcionar como antes,
 *   - MENOR  sube cuando se agregan funciones nuevas,
 *   - PARCHE sube cuando sólo se arreglan errores.
 * Todos los cambios están anotados en CHANGELOG.md.
 */
define('BREAKING_PY_VERSION', '1.4.1');


/**
 * Pasa un texto a minúsculas y le saca las tildes.
 *
 * Es la traducción de esta función de Breaking_Py.py:
 *
 *     def quitar_tildes(texto):
 *         texto = texto.lower()
 *         texto = texto.replace("á", "a")
 *         ...
 *         return texto
 *
 * En PHP el método .replace() de Python se llama str_replace() y se escribe
 * al revés: str_replace(QUÉ_BUSCO, POR_QUÉ_LO_CAMBIO, DÓNDE_BUSCO).
 *
 * Truco: str_replace() acepta ARRAYS. En vez de escribir cinco líneas, le
 * pasamos la lista de vocales con tilde y la lista de vocales sin tilde, y
 * cambia cada una por la que está en la misma posición.
 *
 * ¿Por qué mb_strtolower() y no strtolower()? Porque "mb" significa
 * "multibyte": es la versión que entiende de acentos y de la ñ. La versión
 * sin "mb" es vieja y dejaría la "Á" tal cual estaba.
 */
function quitar_tildes(string $texto): string
{
    $texto = mb_strtolower(trim($texto), 'UTF-8'); // trim() = .strip() de Python

    return str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'ü'],  // lo que busco
        ['a', 'e', 'i', 'o', 'u', 'u'],  // por lo que lo cambio
        $texto
    );
}


/**
 * Escapa un texto antes de mostrarlo en el HTML.
 *
 * ESTO NO EXISTE EN LA VERSIÓN DE CONSOLA Y ES **LA** COSA NUEVA MÁS
 * IMPORTANTE QUE TENÉS QUE APRENDER AL PASAR A LA WEB.
 *
 * En la terminal, si el usuario escribía  <b>hola</b>  se veía tal cual.
 * En una página web, el navegador lo INTERPRETA como código HTML. Si alguien
 * escribe <script>...</script> en un formulario tuyo y vos lo mostrás sin
 * limpiar, el navegador ejecuta ese código. Ese ataque se llama XSS
 * (Cross-Site Scripting) y es de los más comunes que hay.
 *
 * htmlspecialchars() convierte los caracteres peligrosos en su versión
 * "dibujada":  <  pasa a ser  &lt;  , así el navegador lo muestra como texto
 * en vez de ejecutarlo.
 *
 * REGLA DE ORO: todo lo que venga del usuario se imprime con e($algo).
 * Le pusimos nombre corto justamente para no tener excusa para saltearla.
 */
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}


/**
 * Busca un elemento por nombre y devuelve sus datos (o null si no existe).
 *
 * Traduce esta parte de la opción [2] del programa original:
 *
 *     elemento = quitar_tildes(elemento.capitalize())
 *     if elemento in elementos:
 *         datos = elementos[elemento]
 *
 * Las claves del array están sin tildes y en minúscula ("hidrogeno"), por eso
 * primero normalizamos lo que escribió la persona.
 *
 * El  ?array  del tipo de retorno significa "un array O null".
 * El  ??  es el "operador de fusión de null": devuelve lo de la izquierda si
 * existe, y si no, lo de la derecha. Es la forma corta de escribir:
 *
 *     if (isset($elementos[$clave])) { return $elementos[$clave]; }
 *     return null;
 */
function buscar_elemento(array $elementos, string $nombre): ?array
{
    $clave = clave_elemento($elementos, $nombre);

    return $clave === null ? null : $elementos[$clave];
}


/**
 * Igual que buscar_elemento(), pero devuelve la CLAVE del array ('oro') en vez
 * de los datos. Sirve cuando hay que buscar en dos arrays con la misma clave,
 * como pasa en curiosos.php (elementos + datos curiosos).
 *
 * Además acepta nombres alternativos. Varios elementos tienen dos nombres
 * válidos en español y sería feo que el buscador dijera "no existe" porque la
 * persona usó el que no está en los datos. Una tabla de sinónimos como esta es
 * la solución más simple y se lee sola.
 */
function clave_elemento(array $elementos, string $nombre): ?string
{
    $clave = quitar_tildes($nombre);

    $sinonimos = [
        'zinc'        => 'cinc',
        'tungsteno'   => 'wolframio',
        'wolfram'     => 'wolframio',
        'tantalo'     => 'tantalio',
        'teneso'      => 'tennessinio',
        'darmstadtio' => 'darmstadio',
        'azoe'        => 'nitrogeno',
        'columbio'    => 'niobio',
        'kriptón'     => 'kripton',
    ];

    // Si lo escrito es un sinónimo, lo cambiamos por el nombre "oficial".
    // Si no está en la lista, el ?? deja la clave tal como estaba.
    $clave = $sinonimos[$clave] ?? $clave;

    // array_key_exists() pregunta si la clave existe. Se parece al
    // "if clave in diccionario" de Python.
    return array_key_exists($clave, $elementos) ? $clave : null;
}


/**
 * Convierte el valor "Grupo" en algo lindo para mostrar.
 *
 * Casi todos los elementos tienen un número de grupo (1 a 18), pero los
 * lantánidos y actínidos tienen texto ("Lantánidos"). Por eso el valor a veces
 * es int y a veces string: is_numeric() nos dice cuál de los dos es.
 */
function texto_grupo(int|string $grupo): string
{
    return is_numeric($grupo) ? "Grupo $grupo" : (string) $grupo;
}


/**
 * Devuelve el nombre "bonito" de cada campo, para los títulos de las tablas.
 *
 * En el original imprimías la clave cruda:
 *
 *     for clave, valor in datos.items():
 *         print(f"{clave}: {valor}")
 *
 * y salía "NumeroAtomico: 1". Acá lo mostramos como "Número atómico".
 */
function etiqueta_campo(string $campo): string
{
    $etiquetas = [
        'Nombre'        => 'Nombre',
        'NumeroAtomico' => 'Número atómico',
        'Simbolo'       => 'Símbolo',
        'MasaAtomica'   => 'Masa atómica',
        'Grupo'         => 'Grupo',
        'Periodo'       => 'Período',
        'Bloque'        => 'Bloque',
        'Radiactivo'    => 'Radiactivo',
    ];

    return $etiquetas[$campo] ?? $campo;
}


/**
 * Muestra un valor de un elemento como texto legible.
 *
 * Cuidado con los booleanos: si en PHP hacés  echo false;  no se imprime NADA
 * (cadena vacía), y si hacés  echo true;  se imprime  1 . Nunca "True"/"False"
 * como en Python. Por eso lo traducimos a mano a "Sí" / "No".
 */
function valor_legible(string $campo, mixed $valor): string
{
    if ($campo === 'Radiactivo') {
        return $valor ? 'Sí' : 'No';
    }

    if ($campo === 'Grupo') {
        return texto_grupo($valor);
    }

    return (string) $valor;
}


/**
 * Devuelve los 118 datos curiosos, con el del elemento más pesado ya armado.
 *
 * ── EL ELEMENTO DE MAYOR MASA ATÓMICA ────────────────────────────────────
 *
 * En Python lo resolvías en una línea con max() y una lambda:
 *
 *     elemento_mayor_masa = max(elementos.items(), key=lambda x: x[1]["MasaAtomica"])
 *
 * PHP no tiene un max() con "key", así que lo hacemos con un bucle común.
 * No es peor: es más largo pero se entiende de una sola leída, y de paso
 * repasás cómo se recorre un array con foreach.
 *
 * ATENCIÓN AL EMPATE (esto en el original no estaba contemplado):
 * el tennessinio y el oganesón tienen los dos masa 294. Cuando hay empate,
 * max() en Python se queda con el PRIMERO que encontró, así que el dato del
 * "elemento más pesado" terminaba en el tennessinio y el oganesón se quedaba
 * sin nada. Acá desempatamos por número atómico, que es lo que corresponde:
 * el oganesón (118) es el último elemento de la tabla.
 */
function datos_curiosos(array $elementos): array
{
    $curiosos = require __DIR__ . '/datos_curiosos.php';

    $claveMayorMasa = '';
    $masaMayor      = 0;
    $zMayor         = 0;

    foreach ($elementos as $clave => $el) {
        $esMasPesado = $el['MasaAtomica'] > $masaMayor;
        $desempata   = $el['MasaAtomica'] === $masaMayor && $el['NumeroAtomico'] > $zMayor;

        if ($esMasPesado || $desempata) {
            $masaMayor      = $el['MasaAtomica'];
            $zMayor         = $el['NumeroAtomico'];
            $claveMayorMasa = $clave;
        }
    }

    // La comilla DOBLE permite meter variables adentro del texto directamente:
    // es lo más parecido a las f-strings de Python.
    //     Python:  f"...({masa_mayor})..."
    //     PHP:     "...({$masaMayor})..."
    // (Con comilla SIMPLE, en cambio, PHP no reemplaza nada y saldría literal.)
    $curiosos[$claveMayorMasa] = "Tiene la mayor masa atómica conocida ({$masaMayor} u) "
        . 'de todos los elementos de la tabla periódica.';

    return $curiosos;
}


/* =============================================================================
 *  LAS CATEGORÍAS DE LA TABLA PERIÓDICA
 * =============================================================================
 *
 *  Los colores de este sitio NO son decoración: cada color es una CATEGORÍA
 *  química, la misma clasificación estándar que usan las tablas periódicas de
 *  los libros y de Wikipedia. Por eso hay una leyenda en pantalla.
 *
 *  La clasificación se define por NÚMERO ATÓMICO, que es el dato que no admite
 *  discusión. Podría deducirse del grupo y del bloque, pero hay excepciones
 *  (el aluminio es del grupo 13 y es metal, el boro es del grupo 13 y es
 *  metaloide) y saldría un enredo de ifs. Con listas de números se lee de una
 *  ojeada y es facilísimo de corregir.
 * =============================================================================
 */

/**
 * La lista de categorías, en el orden en que se muestran en la leyenda.
 * Para cada una: el nombre que ve la gente y los números atómicos que le tocan.
 *
 * range(21, 30) devuelve [21, 22, ..., 30]. Ojo: en PHP el último número SÍ
 * entra; en Python range(21, 30) llegaba hasta el 29.
 *
 * array_merge() pega varias listas en una sola, como el  +  de las listas de
 * Python:  [1, 2] + [3, 4]  →  [1, 2, 3, 4]
 */
function categorias(): array
{
    return [
        'alcalino' => [
            'nombre'  => 'Metales alcalinos',
            'numeros' => [3, 11, 19, 37, 55, 87],
        ],
        'alcalinoterreo' => [
            'nombre'  => 'Metales alcalinotérreos',
            'numeros' => [4, 12, 20, 38, 56, 88],
        ],
        'transicion' => [
            'nombre'  => 'Metales de transición',
            'numeros' => array_merge(range(21, 30), range(39, 48), range(72, 80), range(104, 112)),
        ],
        'postransicion' => [
            'nombre'  => 'Otros metales',
            'numeros' => [13, 31, 49, 50, 81, 82, 83, 84],
        ],
        'nometal' => [
            'nombre'  => 'No metales',
            'numeros' => [1, 6, 7, 8, 15, 16, 34],
        ],
        'metaloide' => [
            'nombre'  => 'Metaloides',
            'numeros' => [5, 14, 32, 33, 51, 52],
        ],
        'halogeno' => [
            'nombre'  => 'Halógenos',
            'numeros' => [9, 17, 35, 53, 85],
        ],
        'noble' => [
            'nombre'  => 'Gases nobles',
            'numeros' => [2, 10, 18, 36, 54, 86],
        ],
        'lantanido' => [
            'nombre'  => 'Lantánidos',
            'numeros' => range(57, 71),
        ],
        'actinido' => [
            'nombre'  => 'Actínidos',
            'numeros' => range(89, 103),
        ],
        'desconocido' => [
            'nombre'  => 'Propiedades desconocidas',
            'numeros' => range(113, 118),
        ],
    ];
}


/**
 * Devuelve la categoría de un elemento: 'alcalino', 'noble', etc.
 *
 * in_array($aguja, $pajar) pregunta si un valor está en una lista. Es el
 * "if x in lista" de Python. El tercer parámetro  true  pide comparación
 * estricta (de valor Y tipo), que es la que conviene usar siempre.
 */
function categoria_elemento(array $el): string
{
    $z = $el['NumeroAtomico'];

    foreach (categorias() as $clave => $cat) {
        if (in_array($z, $cat['numeros'], true)) {
            return $clave;
        }
    }

    return 'desconocido';
}


/**
 * El nombre para mostrar de una categoría ('noble' → 'Gases nobles').
 */
function nombre_categoria(string $clave): string
{
    return categorias()[$clave]['nombre'] ?? $clave;
}


/**
 * Devuelve la clase CSS que le pone el color de su categoría a un elemento.
 * El color de cada clase está definido en assets/css/estilos.css.
 */
function familia_css(array $el): string
{
    return 'cat-' . categoria_elemento($el);
}


/**
 * Devuelve [fila, columna] : dónde va el elemento en la tabla periódica.
 *
 * La tabla periódica NO es una lista ni una cuadrícula cualquiera: la posición
 * de cada casillero significa algo. La columna es el GRUPO (cuántos electrones
 * tiene en su última capa) y la fila es el PERÍODO (cuántas capas tiene). Por
 * eso los elementos de una misma columna se parecen tanto entre sí: esa es la
 * idea genial de Mendeléiev, y se pierde si los ponemos en una grilla común.
 *
 * Así que la posición sale directamente de los datos que ya tenías:
 *
 *     columna = Grupo        (1 a 18)
 *     fila    = Periodo      (1 a 7)
 *
 * La excepción son los lantánidos y actínidos: son 14 + 15 elementos que
 * entrarían todos en la misma casilla y harían una tabla larguísima. Por eso
 * en todas las tablas del mundo se dibujan aparte, en dos filas abajo. Acá les
 * damos las filas 9 y 10 (la 8 queda vacía a propósito, como separación).
 */
function posicion_tabla(array $el): array
{
    $z = $el['NumeroAtomico'];

    // Las dos tiras de abajo. Del 57 al 70 y del 89 al 102 arrancan en la
    // columna 3, que es donde estarían si la tabla fuera larguísima.
    if ($z >= 57 && $z <= 70) {
        return [9, $z - 57 + 3];
    }
    if ($z >= 89 && $z <= 102) {
        return [10, $z - 89 + 3];
    }

    // El lawrencio (103) cierra los actínidos igual que el lutecio (71) cierra
    // los lantánidos. En los datos originales el lutecio quedó en el grupo 3 y
    // el lawrencio en la serie: es una inconsistencia. Lo tratamos como al
    // lutecio para que las dos tiras queden parejas.
    if ($z === 103) {
        return [7, 3];
    }

    // El caso normal: la posición ES el grupo y el período.
    return [(int) $el['Periodo'], (int) $el['Grupo']];
}
