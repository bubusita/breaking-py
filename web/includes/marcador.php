<?php
/**
 * =============================================================================
 *  marcador.php  —  EL MARCADOR DE UN EJERCICIO
 * =============================================================================
 *
 *  Se usa igual que cabecera.php: la página define antes qué ejercicio es y
 *  lo incluye.
 *
 *      $ejercicio = 'particulas';
 *      require __DIR__ . '/includes/marcador.php';
 *
 *  Muestra cuántas respuestas salieron bien del total, y el porcentaje. El
 *  color sale del porcentaje: verde desde el 70 %, que es más o menos lo que
 *  hace falta para aprobar.
 * =============================================================================
 */

$m = marcador_de($ejercicio);

// round() redondea. Si todavía no contestó nada, no hay porcentaje que
// calcular (dividir por cero rompe el programa).
$porcentaje = $m['total'] > 0 ? (int) round(100 * $m['bien'] / $m['total']) : null;

$claseMarcador = match (true) {
    $porcentaje === null => 'neutro',
    $porcentaje >= 70    => 'positivo',
    default              => 'negativo',
};
?>
<div class="marcador <?= $claseMarcador ?>">
    <span class="marcador-lbl">Bien</span>
    <span class="marcador-num"><?= $m['bien'] ?><small>/<?= $m['total'] ?></small></span>
    <?php if ($porcentaje !== null): ?>
        <span class="marcador-lbl"><?= $porcentaje ?> %</span>
    <?php endif; ?>
</div>
