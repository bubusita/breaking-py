<?php
/**
 * =============================================================================
 *  curiosos.php  —  OPCIÓN [4]: UN DATO CURIOSO DE CADA ELEMENTO
 * =============================================================================
 *
 *  Traduce la función Datos() del archivo datos.py:
 *
 *      seg_respuesta = input("De que elemento le gustaria...")
 *      seg_respuesta = quitar_tildes(seg_respuesta)
 *      elemento_mayor_masa = max(elementos.items(), key=lambda x: x[1]["MasaAtomica"])
 *      ...
 *      if seg_respuesta in DatosCuriosos and seg_respuesta == "oganeson":
 *          ...
 *      elif seg_respuesta in DatosCuriosos:
 *          print(DatosCuriosos[seg_respuesta])
 *
 *  Se mantiene tal cual la idea del oganesón: su dato curioso no está escrito,
 *  se CALCULA buscando cuál es el elemento de mayor masa atómica.
 * =============================================================================
 */

require_once __DIR__ . '/includes/funciones.php';
$elementos = require __DIR__ . '/includes/tabla_periodica.php';

/**
 * Los datos curiosos, con el del elemento más pesado ya calculado.
 *
 * Ese cálculo antes estaba escrito acá mismo. Se mudó a la función
 * datos_curiosos() de includes/funciones.php porque ahora lo necesitan DOS
 * lugares: esta página y el cuadro que se abre al hacer clic en un elemento
 * de la tabla.
 *
 * Es una regla que conviene agarrar temprano: la primera vez que necesitás el
 * mismo código en dos lados, se convierte en función. Si lo copiás y pegás,
 * el día que corrijas uno te vas a olvidar del otro — y ese error es dificilísimo
 * de encontrar, porque la mitad del programa anda bien.
 */
$datosCuriosos = datos_curiosos($elementos);

/**
 * ── QUÉ ELEMENTO NOS PIDIERON ────────────────────────────────────────────
 *
 * Igual que en elemento.php, lo que se escribe en el formulario llega por $_GET.
 * Además aceptamos  ?azar=1  para que el botón "Sorprendeme" elija uno solo.
 *
 * array_rand() devuelve una CLAVE al azar de un array. Es el equivalente de
 * random.choice(list(elementos.keys())) que usabas en el quiz de Python.
 */
$busqueda = trim(texto_recibido($_GET['nombre'] ?? ''));

if (isset($_GET['azar'])) {
    $claveAzar = array_rand($elementos);
    $busqueda  = $elementos[$claveAzar]['Nombre'];
}

$dato     = null;
$elegido  = null;
$hayError = false;

if ($busqueda !== '') {
    // clave_elemento() nos da la clave ('oro') o null si no existe.
    // Necesitamos la clave, y no los datos, porque con ella buscamos también
    // en el array de datos curiosos, que usa exactamente las mismas claves.
    $clave   = clave_elemento($elementos, $busqueda);
    $elegido = $clave === null ? null : $elementos[$clave];

    if ($elegido === null) {
        $hayError = true;
    } else {
        // Puede existir el elemento pero no tener dato cargado: lo contemplamos.
        $dato = $datosCuriosos[$clave] ?? null;
    }
}

$titulo = 'Datos curiosos';
$activo = 'curiosos';
require __DIR__ . '/includes/cabecera.php';
?>

<h1 class="titulo-pagina">Datos curiosos</h1>
<p class="bajada">¿De qué elemento querés saber algo que casi nadie sabe?</p>

<form class="caja-form" action="curiosos.php" method="get">
    <label for="nombre">Nombre del elemento</label>
    <div class="fila-form">
        <input type="text"
               id="nombre"
               name="nombre"
               list="lista-elementos"
               placeholder="Ej: mercurio, galio, radio..."
               value="<?= e($busqueda) ?>"
               autocomplete="off">
        <button type="submit">Ver dato</button>
    </div>

    <datalist id="lista-elementos">
        <?php foreach ($elementos as $el): ?>
            <option value="<?= e($el['Nombre']) ?>"></option>
        <?php endforeach; ?>
    </datalist>
</form>

<p class="centrado">
    <!-- El "[1] Otro dato" del menú original, pero eligiendo al azar -->
    <a class="boton-secundario" href="curiosos.php?azar=1">🎲 Sorprendeme con uno al azar</a>
</p>

<?php if ($hayError): ?>
    <p class="aviso aviso-error">
        No encontramos ningún elemento llamado «<?= e($busqueda) ?>».
    </p>
<?php endif; ?>

<?php if ($elegido !== null): ?>
    <article class="dato-curioso <?= familia_css($elegido) ?>">
        <div class="dato-cabecera">
            <span class="dato-sim"><?= e($elegido['Simbolo']) ?></span>
            <div>
                <h2><?= e($elegido['Nombre']) ?></h2>
                <p class="dato-meta">
                    Número atómico <?= e((string) $elegido['NumeroAtomico']) ?> ·
                    <?= e(texto_grupo($elegido['Grupo'])) ?> ·
                    Período <?= e((string) $elegido['Periodo']) ?>
                </p>
            </div>
        </div>

        <?php if ($dato !== null && $dato !== ''): ?>
            <p class="dato-texto"><?= e($dato) ?></p>
        <?php else: ?>
            <p class="dato-texto dato-vacio">
                Todavía no hay un dato curioso cargado para este elemento.
                ¡Podés escribirlo vos en <code>includes/datos_curiosos.php</code>!
            </p>
        <?php endif; ?>

        <p class="ficha-extra">
            <a href="elemento.php?nombre=<?= urlencode($elegido['Nombre']) ?>">
                Ver la ficha completa del <?= e($elegido['Nombre']) ?> →
            </a>
        </p>
    </article>
<?php endif; ?>

<?php require __DIR__ . '/includes/pie.php';
