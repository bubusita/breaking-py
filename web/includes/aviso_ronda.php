<?php
/**
 * El aviso de "esas respuestas eran de otra ronda". Lo incluyen los
 * ejercicios después del marcador; usa la misma variable $ejercicio.
 */
?>
<?php if (hubo_ronda_vieja($ejercicio)): ?>
    <div class="aviso aviso-error">
        Esas respuestas eran de unas preguntas que ya habían cambiado (¿otra
        pestaña, o el botón «atrás»?), así que no se corrigieron. Acá tenés
        preguntas nuevas.
    </div>
<?php endif; ?>
