<?php
defined('BREAKING_PY_VERSION') || exit;   // abierto directo desde el navegador: no hace nada (ver includes/.htaccess)
/**
 * =============================================================================
 *  cabecera.php  —  LA PARTE DE ARRIBA QUE SE REPITE EN TODAS LAS PÁGINAS
 * =============================================================================
 *
 *  En la versión de consola, cada vez que querías volver al menú llamabas a la
 *  función codigo() y se re-imprimía todo. En la web pasa algo parecido pero
 *  con el HTML: el <head>, el logo y el menú son IDÉNTICOS en las 5 páginas.
 *
 *  Copiar y pegar eso cinco veces sería un desastre (cambiás una coma y tenés
 *  que corregirla en cinco lugares). Por eso lo escribimos UNA sola vez acá y
 *  cada página hace:
 *
 *      require __DIR__ . '/includes/cabecera.php';
 *
 *  require = "traé este archivo y ejecutalo acá adentro". Es lo más parecido al
 *  import de Python, con una diferencia: import trae funciones para usarlas
 *  después; require pega el contenido y lo ejecuta EN ESE MOMENTO.
 *
 *  __DIR__ es una constante mágica de PHP: la carpeta donde está ESTE archivo.
 *  Usarla evita que se rompa el require según desde dónde se abra la página.
 *
 *  Antes de este require, cada página define  $titulo  y (si quiere) $activo.
 *  Si se olvidó, el  ??  pone un valor por defecto y no explota nada.
 * =============================================================================
 */

$titulo = $titulo ?? 'Breaking Py';
$activo = $activo ?? '';

/**
 * $base = cómo volver a la carpeta principal desde donde está la página.
 *
 * Las páginas que están en la raíz del sitio (index.php, quiz.php...) no
 * necesitan nada: dejan $base vacío. Pero python_original/index.php está una
 * carpeta más adentro, y para ella el logo tiene que apuntar a "../index.php"
 * y el CSS a "../assets/css/estilos.css".
 *
 * ¿Por qué no usar direcciones absolutas tipo "/assets/css/estilos.css"?
 * Porque la barra inicial significa "desde la raíz del dominio", y si algún
 * día subís el sitio a una subcarpeta (misitio.com/breakingpy/) todos los
 * enlaces se romperían. Con rutas relativas anda en los dos casos.
 */
$base = $base ?? '';

// Las instrucciones de seguridad para el navegador (ver funciones.php).
// Tienen que salir ANTES que el HTML, como las cookies.
enviar_cabeceras_de_seguridad();

// ¿Estamos en el sitio publicado? Google Analytics sólo mide ahí.
// str_ends_with() pregunta si un texto termina en otro (el .endswith() de
// Python): así vale tanto lopez-rocchi.com.ar como www.lopez-rocchi.com.ar.
$enProduccion = str_ends_with($_SERVER['SERVER_NAME'] ?? '', DOMINIO_PUBLICADO);

/**
 * PHP arranca en "modo HTML" y sólo entra en modo código entre <?php y ? >.
 * Abajo cerramos el bloque de código con  ? >  y escribimos HTML directo.
 * Cuando necesitamos meter un dato de PHP en el medio del HTML usamos la
 * forma corta  <?= $variable ? >  que es igual a  <?php echo $variable; ? >
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> · Breaking Py</title>
    <link rel="stylesheet" href="<?= $base ?>assets/css/estilos.css">

    <?php if ($enProduccion): ?>
        <!--
            Google Analytics (se agregó el 8/9/2026). La medición es anónima:
            no recibe ningún dato de quien navega. El pedacito de
            configuración está en assets/js/analytics.js y no escrito acá,
            porque la política de seguridad (CSP) no deja ejecutar
            JavaScript escrito adentro del HTML.
        -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e(GOOGLE_ANALYTICS_ID) ?>"></script>
        <script src="<?= $base ?>assets/js/analytics.js" data-id="<?= e(GOOGLE_ANALYTICS_ID) ?>"></script>
    <?php endif; ?>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚗️</text></svg>">
</head>
<body>

<header class="cabecera">
    <a class="logo" href="<?= $base ?>index.php">
        <span class="logo-simbolo">Py</span>
        <span class="logo-texto">Breaking&nbsp;Py</span>
    </a>

    <nav class="nav">
        <!--
            El código que hay dentro de cada class decide si el botón se ve
            "encendido". Es un IF escrito en una sola línea con el operador
            ternario:

                condición ? valor_si_es_verdadero : valor_si_es_falso

            En Python lo escribías al revés: "a if cond else b".
        -->
        <a href="<?= $base ?>filtrar.php"  class="<?= $activo === 'filtrar'  ? 'activo' : '' ?>">Filtrar</a>
        <a href="<?= $base ?>elemento.php" class="<?= $activo === 'elemento' ? 'activo' : '' ?>">Elemento</a>
        <a href="<?= $base ?>quiz.php"     class="<?= $activo === 'quiz'     ? 'activo' : '' ?>">Quiz</a>
        <a href="<?= $base ?>curiosos.php" class="<?= $activo === 'curiosos' ? 'activo' : '' ?>">Datos curiosos</a>
        <a href="<?= $base ?>ejercicios.php" class="<?= $activo === 'ejercicios' ? 'activo' : '' ?>">Ejercicios</a>
    </nav>
</header>

<main class="contenido">
