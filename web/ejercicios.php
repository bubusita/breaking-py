<?php
/**
 * =============================================================================
 *  ejercicios.php  —  LA SECCIÓN DE EJERCICIOS
 * =============================================================================
 *
 *  Es una portada, como index.php: no hace ningún ejercicio, sólo lleva a
 *  cada uno y muestra cómo viene el marcador de cada uno.
 *
 *  Los ejercicios son para repasar los temas de Fisicoquímica de tabla
 *  periódica (partículas, niveles de energía, metales y no metales,
 *  propiedades periódicas). Cada uno sortea un caso nuevo cada vez que se
 *  abre, así que se pueden hacer muchas veces sin que se repitan.
 *
 *  La lista de ejercicios es un array, y las tarjetas se dibujan con un
 *  foreach: agregar un ejercicio nuevo es agregar una fila al array.
 * =============================================================================
 */

session_start();

require_once __DIR__ . '/includes/quimica.php';

$listaEjercicios = [
    [
        'archivo' => 'particulas.php',
        'clave'   => 'particulas',
        'tema'    => 'Partículas e iones',
        'titulo'  => 'Completá la tabla de partículas',
        'texto'   => 'Átomos e iones como ²³Na⁺ o ¹⁹F⁻: tipo de partícula, Z, protones, electrones, neutrones, A y carga.',
    ],
    [
        'archivo' => 'misterioso.php',
        'clave'   => 'misterioso',
        'tema'    => 'Niveles de energía',
        'titulo'  => 'El elemento misterioso',
        'texto'   => 'Con los niveles de energía, los electrones del último nivel y algunas propiedades, descubrí qué elemento es.',
    ],
    [
        'archivo' => 'clasificar.php',
        'clave'   => 'clasificar',
        'tema'    => 'Metales y no metales',
        'titulo'  => '¿Metal, no metal o metaloide?',
        'texto'   => 'Clasificá un elemento recién descubierto por sus propiedades, y decidí cuáles sirven como evidencia y cuáles no.',
    ],
    [
        // La tabla muda es de varios pasos: recargar conserva lo elegido, y
        // entrar desde acá empieza de cero (por eso el ?nuevo=1).
        'archivo' => 'tabla_muda.php?nuevo=1',
        'clave'   => 'tabla_muda',
        'tema'    => 'Propiedades periódicas',
        'titulo'  => 'Tabla muda, ficha y orden',
        'texto'   => 'Ubicá tres elementos en una tabla sin nombres según su carácter metálico, su radio, su electronegatividad o su energía de ionización (elegís cuál), armá su ficha y ordenalos.',
    ],
    [
        'archivo' => 'propiedades.php',
        'clave'   => 'propiedades',
        'tema'    => 'Propiedades periódicas',
        'titulo'  => 'Propiedades periódicas',
        'texto'   => 'Radio, electronegatividad, energía de ionización y carácter metálico: dónde está el elemento y qué hace con sus electrones.',
    ],
];

$titulo = 'Ejercicios';
$activo = 'ejercicios';
require __DIR__ . '/includes/cabecera.php';
?>

<h1 class="titulo-pagina">Ejercicios</h1>
<p class="bajada">
    Para repasar los temas de tabla periódica. Cada ejercicio sortea un caso
    nuevo cada vez que lo abrís, y al corregir te explica el porqué de cada
    respuesta: en las pruebas casi siempre piden justificar.
</p>

<section class="menu">
    <?php foreach ($listaEjercicios as $ej): ?>
        <?php $m = marcador_de($ej['clave']); ?>
        <a class="tarjeta" href="<?= e($ej['archivo']) ?>">
            <span class="tarjeta-tema"><?= e($ej['tema']) ?></span>
            <h2><?= e($ej['titulo']) ?></h2>
            <p><?= e($ej['texto']) ?></p>
            <?php if ($m['total'] > 0): ?>
                <p class="tarjeta-marcador">
                    Llevás <?= $m['bien'] ?> de <?= $m['total'] ?> bien
                    (<?= (int) round(100 * $m['bien'] / $m['total']) ?> %)
                </p>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</section>

<?php require __DIR__ . '/includes/pie.php';
