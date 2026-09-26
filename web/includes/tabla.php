<?php
defined('BREAKING_PY_VERSION') || exit;   // abierto directo desde el navegador: no hace nada (ver includes/.htaccess)
/**
 * =============================================================================
 *  tabla.php  —  DIBUJA LA TABLA PERIÓDICA CON LA FORMA DE VERDAD
 * =============================================================================
 *
 *  Este archivo no es una página: es un PEDAZO de página que otras páginas
 *  incluyen, igual que cabecera.php y pie.php. Se usa así:
 *
 *      $resaltados = null;                      // todos normales
 *      require __DIR__ . '/includes/tabla.php';
 *
 *  o, cuando hay filtros puestos:
 *
 *      $resaltados = $resultado;                // los que pasaron el filtro
 *      require __DIR__ . '/includes/tabla.php';
 *
 *  Los que NO están en $resaltados no desaparecen: se dibujan apagados. Así se
 *  ve al mismo tiempo qué cumple la condición y dónde está parado dentro de la
 *  tabla, que es información que se perdía cuando los sacábamos de la lista.
 *
 *  ── CÓMO SE COLOCA CADA ELEMENTO EN SU LUGAR ────────────────────────────
 *
 *  Con CSS Grid. Uno declara una cuadrícula de 18 columnas (los grupos) y a
 *  cada casillero le dice en qué fila y en qué columna va:
 *
 *      style="grid-row: 3; grid-column: 14"
 *
 *  Los números salen de posicion_tabla(), en includes/funciones.php, que los
 *  saca del Periodo y el Grupo que ya estaban en tus datos desde el principio.
 *  No hubo que agregar ni un dato nuevo: la información de dónde va cada
 *  elemento YA estaba, sólo que en la consola no se podía dibujar.
 * =============================================================================
 */

// El  ??  cubre el caso de que la página se olvide de definir la variable.
$resaltados = $resaltados ?? null;
$hayFiltro  = $resaltados !== null;

// Los datos curiosos, para poder mostrarlos en el cuadro que se abre al hacer
// clic en un elemento.
$curiososTabla = datos_curiosos($elementos);
?>

<div class="tabla-scroll">
    <div class="tabla-periodica">

        <?php
        /**
         * Fila 1: los números de grupo, arriba de cada columna.
         * Van en la fila 1 de la cuadrícula; los elementos empiezan en la 2.
         */
        foreach (range(1, 18) as $g): ?>
            <span class="th-grupo" style="grid-row: 1; grid-column: <?= $g ?>"><?= $g ?></span>
        <?php endforeach; ?>

        <?php foreach ($elementos as $clave => $el): ?>
            <?php
            // La lista devuelta por posicion_tabla() se abre en dos variables
            // de una sola vez. Es igual al  fila, columna = f(x)  de Python.
            [$fila, $columna] = posicion_tabla($el);

            /**
             * ¿Este elemento está resaltado?
             *
             * isset() pregunta si esa clave existe en el array. Como
             * array_filter() CONSERVA las claves originales, alcanza con
             * preguntar si la clave del elemento sigue estando en el
             * resultado del filtro. Por eso no hubo que tocar ni una línea
             * de la lógica de filtrado: la misma lista que antes servía para
             * "qué mostrar" ahora sirve para "qué destacar".
             */
            $apagado = $hayFiltro && !isset($resaltados[$clave]);

            // Tres estados posibles: normal (sin filtros), apagado, o resaltado.
            // El resaltado sólo existe cuando hay filtros puestos: si no, todos
            // estarían "resaltados" y no destacaría ninguno.
            $clases = familia_css($el)
                . ($apagado ? ' apagada' : ($hayFiltro ? ' resaltada' : ''));
            ?>
            <?php
            $categoria = categoria_elemento($el);

            /**
             * LOS ATRIBUTOS  data-…
             *
             * Cualquier atributo que empiece con  data-  es de uso libre: el
             * navegador lo guarda y no hace nada con él. Sirven para dejarle
             * datos preparados al JavaScript, que después los lee con
             * elemento.dataset.nombre , elemento.dataset.masa , etc.
             * (el guión del medio desaparece: data-categoria-nombre se lee
             * como dataset.categoriaNombre).
             *
             * Gracias a esto, cuando alguien hace clic en un casillero el
             * cuadro se abre AL INSTANTE: los datos ya viajaron con la página
             * y no hay que pedirle nada al servidor.
             */
            ?>
            <a class="celda <?= $clases ?>"
               style="grid-row: <?= $fila + 1 ?>; grid-column: <?= $columna ?>"
               href="<?= $base ?? '' ?>elemento.php?nombre=<?= urlencode($el['Nombre']) ?>"
               title="<?= e($el['Nombre']) ?> · <?= e(nombre_categoria($categoria)) ?>"
               data-nombre="<?= e($el['Nombre']) ?>"
               data-z="<?= e((string) $el['NumeroAtomico']) ?>"
               data-simbolo="<?= e($el['Simbolo']) ?>"
               data-masa="<?= e((string) $el['MasaAtomica']) ?>"
               data-grupo="<?= e(texto_grupo($el['Grupo'])) ?>"
               data-periodo="<?= e((string) $el['Periodo']) ?>"
               data-bloque="<?= e($el['Bloque']) ?>"
               data-radiactivo="<?= $el['Radiactivo'] ? 'Sí' : 'No' ?>"
               data-categoria="<?= e($categoria) ?>"
               data-categoria-nombre="<?= e(nombre_categoria($categoria)) ?>"
               data-curioso="<?= e($curiososTabla[$clave] ?? '') ?>">
                <span class="celda-z"><?= e((string) $el['NumeroAtomico']) ?></span>
                <span class="celda-sim"><?= e($el['Simbolo']) ?></span>
                <span class="celda-nom"><?= e($el['Nombre']) ?></span>
            </a>
        <?php endforeach; ?>

    </div>
</div>

<?php
// El cuadro que se abre al hacer clic en un casillero. Va acá porque sólo hace
// falta en las páginas que dibujan la tabla.
require __DIR__ . '/dialogo.php';
