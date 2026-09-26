<?php
/**
 * =============================================================================
 *  sesion.php  —  EL ARRANQUE DE SESIÓN DE BREAKING PY
 * =============================================================================
 *
 *  Todas las páginas que usan $_SESSION empiezan con:
 *
 *      require_once __DIR__ . '/includes/sesion.php';
 *
 *  en lugar de un session_start() "pelado". Tiene que ir antes de imprimir
 *  nada: manda una cookie, y las cookies viajan en la cabecera de la
 *  respuesta, o sea, antes que el HTML.
 *
 *  ── POR QUÉ EXISTE ESTE ARCHIVO ────────────────────────────────────────
 *
 *  Breaking Py es público a propósito, pero vive en el mismo dominio que el
 *  resto de lopez-rocchi.com.ar, que SÍ tiene login. Con session_start() a
 *  secas, PHP usa la cookie por defecto, PHPSESSID, con path=/ : la MISMA
 *  cookie que cualquier otro PHP del dominio. Y el hosting tiene
 *  session.use_strict_mode=1, así que cuando llega un ID que PHP no conoce, lo
 *  descarta y manda uno nuevo. Resultado: entrar a Breaking Py podía pisarle
 *  la cookie a otra parte del sitio y cerrarle la sesión a quien estuviera
 *  logueado.
 *
 *  Esto ya se había arreglado una vez (el 8/9/2026) en la copia publicada,
 *  pero el arreglo no estaba en este repositorio y al subir la versión 1.5.0
 *  se perdió. Por eso ahora vive acá: este repo es la fuente de lo que se
 *  publica, y lo que no está acá se pierde en la próxima subida.
 *
 *  ── LA SOLUCIÓN ────────────────────────────────────────────────────────
 *
 *  Una cookie con NOMBRE PROPIO y ACOTADA A ESTA CARPETA. Así las sesiones no
 *  se tocan: Breaking Py no puede leer ni pisar la del resto del sitio, y el
 *  navegador ni siquiera le manda esta cookie a las otras páginas.
 *
 *  Además:
 *    - httponly:  el JavaScript de la página no puede leer la cookie (si
 *                 alguna vez se colara un script ajeno, no se la lleva);
 *    - samesite:  otro sitio no puede mandar formularios "en nombre" del
 *                 visitante con su sesión;
 *    - secure:    sólo viaja por HTTPS. Se deduce del pedido y no se pone
 *                 fijo, para que siga andando en la computadora con
 *                 php -S (que es HTTP).
 *    - use_strict_mode: PHP no acepta un ID de sesión inventado por el
 *                 visitante (evita la "fijación de sesión").
 * =============================================================================
 */

if (session_status() === PHP_SESSION_ACTIVE) {
    return;
}

/**
 * La carpeta de la app, vista desde el navegador: '/ProyectosJ/BreakingPy/'
 * en el hosting y '/' en la computadora. Sale de SCRIPT_NAME, la dirección
 * de la página que se está ejecutando (todas las que usan sesión están en la
 * raíz de la app). str_replace() cubre a Windows, que usa la barra al revés.
 */
$carpetaApp = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/') . '/';

session_name('breakingpy_sess');

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => $carpetaApp,
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start(['use_strict_mode' => true]);
