<?php
defined('BREAKING_PY_VERSION') || exit;   // abierto directo desde el navegador: no hace nada (ver includes/.htaccess)
/**
 * =============================================================================
 *  correccion.php  —  LA LISTA DE RESPUESTAS CORREGIDAS
 * =============================================================================
 *
 *  Todos los ejercicios corrigen de la misma forma: cada pregunta con un ✓ o
 *  una ✗, lo que contestaste, lo correcto, y POR QUÉ. Ese porqué es lo más
 *  importante: en la prueba casi todas las preguntas piden justificar.
 *
 *  La página arma antes un array $correccion, con una fila por pregunta:
 *
 *      [
 *          'pregunta'    => 'a. ¿Es metal, no metal o metaloide?',
 *          'acierto'     => true,
 *          'tuya'        => 'no metal',
 *          'correcta'    => 'no metal',
 *          'explicacion' => 'Tiene 7 electrones en el último nivel...',
 *      ]
 *
 *  y después hace  require __DIR__ . '/includes/correccion.php';
 * =============================================================================
 */
?>
<ul class="quiz-resultados">
    <?php foreach ($correccion as $r): ?>
        <li class="<?= $r['acierto'] ? 'ok' : 'mal' ?>">
            <span class="res-icono"><?= $r['acierto'] ? '✓' : '✗' ?></span>
            <div>
                <span class="res-preg"><?= e($r['pregunta']) ?></span>
                <?php if ($r['acierto']): ?>
                    <span class="res-det">¡Bien! → <?= e($r['correcta']) ?></span>
                <?php else: ?>
                    <span class="res-det">
                        Pusiste «<?= e($r['tuya'] === '' ? '(nada)' : $r['tuya']) ?>» ·
                        era <strong><?= e($r['correcta']) ?></strong>
                    </span>
                <?php endif; ?>
                <?php if (!empty($r['explicacion'])): ?>
                    <span class="res-explica"><?= e($r['explicacion']) ?></span>
                <?php endif; ?>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
