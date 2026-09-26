<?php
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
 *  Reiniciar pide confirmación con confirm(), una ventanita del navegador
 *  que devuelve true o false: si la persona toca "Cancelar", el  return false
 *  frena el enlace y no se borra nada. Es JavaScript de una sola línea.
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
       onclick="return confirm('¿Poner el marcador de este ejercicio en cero?');">Reiniciar marcador</a>
</p>
