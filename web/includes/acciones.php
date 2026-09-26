<?php
defined('BREAKING_PY_VERSION') || exit;   // abierto directo desde el navegador: no hace nada (ver includes/.htaccess)
/**
 * =============================================================================
 *  acciones.php  —  LOS BOTONES DE ABAJO DE CADA EJERCICIO
 * =============================================================================
 *
 *  "Otra ronda" y "Reiniciar marcador" tienen que estar SIEMPRE, no sólo
 *  después de corregir: si alguien vuelve al ejercicio más tarde, o quiere
 *  saltear una pregunta que no le sale, los necesita a mano.
 *
 *  La página define antes:
 *      $pagina     'particulas.php'
 *      $corregido  si ya se corrigió (cambia qué botón se destaca)
 *      $urlOtra    (opcional) adónde lleva "otra ronda"; por defecto, $pagina
 *
 *  Reiniciar pide confirmación: el atributo data-confirmar lo lee
 *  assets/js/confirmar.js, que muestra la pregunta antes de seguir el enlace.
 * =============================================================================
 */

$urlOtra = $urlOtra ?? $pagina;
?>
<p class="centrado acciones">
    <?php if ($corregido): ?>
        <a class="boton-grande" href="<?= e($urlOtra) ?>">Otra ronda</a>
    <?php else: ?>
        <a class="boton-secundario" href="<?= e($urlOtra) ?>">Saltear: otra ronda</a>
    <?php endif; ?>
    <a class="boton-secundario" href="<?= e($pagina) ?>?reiniciar=1"
       data-confirmar="¿Poner el marcador de este ejercicio en cero?">Reiniciar marcador</a>
</p>
<script src="assets/js/confirmar.js" defer></script>
