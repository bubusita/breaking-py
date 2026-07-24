<?php
/**
 * =============================================================================
 *  dialogo.php  —  EL CUADRO QUE SE ABRE AL HACER CLIC EN UN ELEMENTO
 * =============================================================================
 *
 *  Esto es un "modal": una ventanita que aparece encima de la página, con el
 *  fondo oscurecido, y que hay que cerrar para volver a lo de atrás.
 *
 *  ── ACÁ APARECE EL TERCER LENGUAJE: JAVASCRIPT ──────────────────────────
 *
 *  Hasta ahora el reparto era:
 *
 *      PHP   → corre en el SERVIDOR, arma el HTML y termina.
 *      HTML  → dice qué es cada cosa.
 *      CSS   → dice cómo se ve.
 *
 *  JavaScript es distinto de todos: corre en la COMPUTADORA DE QUIEN VISITA,
 *  dentro del navegador, y sigue vivo mientras la página esté abierta. Puede
 *  reaccionar a clics, cambiar cosas de la pantalla y hacerlo sin recargar.
 *
 *  Esa es la diferencia clave con PHP, y conviene tenerla clarísima:
 *
 *      PHP ya terminó cuando vos ves la página. JavaScript recién empieza.
 *
 *  Por eso JavaScript NO puede leer tus archivos ni tus datos secretos, y por
 *  eso todo lo importante (validar, decidir quién puede hacer qué) se hace en
 *  PHP. JavaScript es para que la página se sienta viva, no para cuidar nada:
 *  cualquiera puede abrir las herramientas del navegador y cambiarlo.
 *
 *  ── LA ETIQUETA <dialog> ────────────────────────────────────────────────
 *
 *  El HTML ya trae los modales incorporados con la etiqueta <dialog>. Antes
 *  había que armarlos a mano con divs, y salían mal: se podía seguir usando lo
 *  de atrás con el teclado, no se cerraban con Escape, los lectores de pantalla
 *  no entendían nada. Con <dialog> y showModal() el navegador se encarga de
 *  todo eso solo. Cuando el HTML ya trae algo hecho, casi siempre conviene
 *  usar eso antes que inventarlo de nuevo.
 *
 *  ── SI ALGUIEN TIENE JAVASCRIPT APAGADO ─────────────────────────────────
 *
 *  Los casilleros siguen siendo enlaces normales a elemento.php. Si el
 *  JavaScript no carga, hacer clic lleva a la ficha completa, como antes. El
 *  cuadro es un agregado lindo, no un requisito. Eso se llama "mejora
 *  progresiva" y es la forma correcta de sumar cosas a una web: que nada de lo
 *  que ya funcionaba dependa de lo nuevo.
 *
 *  Los campos van vacíos: JavaScript los llena con los data-… del casillero en
 *  el que se hizo clic (ver assets/js/dialogo.js).
 * =============================================================================
 */
?>

<dialog id="dialogo-elemento" class="dialogo" aria-labelledby="dialogo-nombre">

    <button class="dialogo-cerrar" data-cerrar aria-label="Cerrar">&times;</button>

    <div class="dialogo-cabecera">
        <div class="dialogo-simbolo">
            <span class="ficha-z"   data-campo="z"></span>
            <span class="ficha-sim" data-campo="simbolo"></span>
            <span class="ficha-masa" data-campo="masa"></span>
        </div>

        <div>
            <h2 id="dialogo-nombre" data-campo="nombre"></h2>
            <p class="ficha-categoria">
                <span class="leyenda-color" data-campo="color"></span>
                <span data-campo="categoriaNombre"></span>
            </p>
        </div>
    </div>

    <dl class="ficha-datos">
        <dt>Número atómico</dt> <dd data-campo="z"></dd>
        <dt>Símbolo</dt>        <dd data-campo="simbolo"></dd>
        <dt>Masa atómica</dt>   <dd data-campo="masa"></dd>
        <dt>Grupo</dt>          <dd data-campo="grupo"></dd>
        <dt>Período</dt>        <dd data-campo="periodo"></dd>
        <dt>Bloque</dt>         <dd data-campo="bloque"></dd>
        <dt>Radiactivo</dt>     <dd data-campo="radiactivo"></dd>
    </dl>

    <p class="dialogo-curioso" data-campo="curioso"></p>

    <div class="dialogo-acciones">
        <a class="boton-grande" data-campo="fichaUrl" href="#">Ver la ficha completa</a>
        <button class="boton-secundario" data-cerrar>Cerrar</button>
    </div>
</dialog>

<!--
    defer significa "bajá el archivo mientras se arma la página, pero ejecutalo
    recién cuando esté todo listo". Sin defer, el JavaScript podría correr antes
    de que existan los casilleros y no encontraría nada.
-->
<script src="<?= $base ?? '' ?>assets/js/dialogo.js" defer></script>
