<?php
defined('BREAKING_PY_VERSION') || exit;   // abierto directo desde el navegador: no hace nada (ver includes/.htaccess)
/**
 * =============================================================================
 *  leyenda.php  —  QUÉ SIGNIFICA CADA COLOR
 * =============================================================================
 *
 *  REGLA DE ORO DE CUALQUIER GRÁFICO: si usás color para decir algo, tiene que
 *  haber una leyenda que explique qué dice. Un color sin leyenda es decoración,
 *  y la decoración que parece información confunde más de lo que ayuda.
 *
 *  Y al revés: el color nunca puede ser la ÚNICA forma de saber qué es cada
 *  cosa. Acá la categoría se puede saber de tres maneras distintas:
 *
 *    1. por el color del casillero,
 *    2. por su posición en la tabla (los gases nobles son la última columna,
 *       los alcalinos la primera... para eso está ordenada la tabla),
 *    3. pasando el mouse por encima, que muestra el nombre de la categoría.
 *
 *  Eso importa porque cerca de 1 de cada 12 varones tiene algún tipo de
 *  daltonismo. Con 10 categorías es imposible elegir 10 colores que TODO el
 *  mundo distinga entre sí: por eso el color acompaña, pero nunca va solo.
 *
 *  Cuando hay un filtro puesto, la leyenda muestra además cuántos elementos de
 *  cada categoría quedaron resaltados.
 * =============================================================================
 */

$resaltados = $resaltados ?? null;

// Contamos cuántos elementos resaltados hay de cada categoría.
// Es el típico "contador con diccionario" que ya hacías en Python:
//     cuenta[cat] = cuenta.get(cat, 0) + 1
$cuenta = [];
foreach (($resaltados ?? []) as $el) {
    $cat = categoria_elemento($el);
    $cuenta[$cat] = ($cuenta[$cat] ?? 0) + 1;
}
?>

<div class="leyenda">
    <span class="leyenda-titulo">Cada color es una categoría química:</span>

    <ul class="leyenda-lista">
        <?php foreach (categorias() as $clave => $cat): ?>
            <?php
            // count() sobre la lista de números atómicos = cuántos elementos
            // tiene esa categoría en total.
            $total = count($cat['numeros']);
            $marcados = $cuenta[$clave] ?? 0;
            $vacia = $resaltados !== null && $marcados === 0;
            ?>
            <li class="<?= $vacia ? 'sin-resultados' : '' ?>">
                <span class="leyenda-color cat-<?= e($clave) ?>"></span>
                <span class="leyenda-nombre"><?= e($cat['nombre']) ?></span>
                <span class="leyenda-cuenta">
                    <?php if ($resaltados === null): ?>
                        <?= $total ?>
                    <?php else: ?>
                        <?= $marcados ?>/<?= $total ?>
                    <?php endif; ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
