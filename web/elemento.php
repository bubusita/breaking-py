<?php
/**
 * =============================================================================
 *  elemento.php  —  OPCIÓN [2]: BUSCAR UN ELEMENTO POR SU NOMBRE
 * =============================================================================
 *
 *  Traduce la función segundoOp() del programa original:
 *
 *      def segundoOp():
 *          elemento = input("\nIngrese el nombre de un elemento: ")
 *          elemento = quitar_tildes(elemento.capitalize())
 *          if elemento in elementos:
 *              datos = elementos[elemento]
 *              for clave, valor in datos.items():
 *                  print(f"{clave}: {valor}")
 *          else:
 *              print("El elemento no fue encontrado")
 *              segundoOp()          # se llamaba a sí misma para reintentar
 *
 *  DOS COSAS QUE CAMBIAN AL PASAR A LA WEB
 *
 *  1) El input() se convierte en un <form>. Lo que la persona escribe viaja
 *     al servidor y PHP lo recibe en el array $_GET (o $_POST).
 *
 *  2) La RECURSIÓN para reintentar desaparece. En el original, si el elemento
 *     no existía, segundoOp() se llamaba a sí misma; si te equivocabas 500
 *     veces, quedaban 500 llamadas apiladas en memoria y el programa podía
 *     reventar con "RecursionError". En la web no hace falta nada de eso:
 *     mostramos el error, el formulario sigue en pantalla, y si la persona
 *     escribe otra vez es una ejecución NUEVA y limpia del programa.
 * =============================================================================
 */

require_once __DIR__ . '/includes/funciones.php';
$elementos = require __DIR__ . '/includes/tabla_periodica.php';

/**
 * $_GET es un array que PHP arma solo con lo que viene en la dirección web.
 * Si el navegador pide  elemento.php?nombre=oro  entonces:
 *
 *     $_GET  vale  ['nombre' => 'oro']
 *
 * "GET" es uno de los dos métodos de formulario:
 *   - GET  → los datos viajan a la vista, en la URL. Ideal para búsquedas,
 *            porque el resultado se puede compartir o guardar en favoritos.
 *   - POST → los datos viajan ocultos en el cuerpo del pedido. Se usa cuando
 *            se envían contraseñas o cuando el envío CAMBIA algo (ver quiz.php).
 *
 * Usamos ?? '' por si todavía no buscó nada: sin eso PHP avisaría que la
 * clave 'nombre' no existe.
 */
$busqueda = trim(texto_recibido($_GET['nombre'] ?? ''));

$datos = null;
$hayError = false;

// En PHP el  !==  compara valor Y tipo, igual que hacía  is  en algunos casos
// de Python. Acá simplemente preguntamos "¿escribió algo?".
if ($busqueda !== '') {
    $datos = buscar_elemento($elementos, $busqueda);
    $hayError = ($datos === null);
}

$titulo = 'Buscar elemento';
$activo = 'elemento';
require __DIR__ . '/includes/cabecera.php';
?>

<h1 class="titulo-pagina">Buscar un elemento</h1>
<p class="bajada">Escribí el nombre del elemento (no hace falta poner las tildes).</p>

<!--
    ESTE ES EL REEMPLAZO DEL input() DE PYTHON.

    action  = a qué página se le manda lo escrito (acá, a sí misma).
    method  = GET, así queda en la URL y se puede compartir el link.
    name    = el nombre con el que PHP lo va a recibir: $_GET['nombre'].

    El  value="<?= e($busqueda) ?>"  hace que, después de buscar, el cuadro
    siga mostrando lo que la persona había escrito. Sin eso se borraría en
    cada búsqueda y sería molesto.
-->
<form class="caja-form" action="elemento.php" method="get">
    <label for="nombre">Nombre del elemento</label>
    <div class="fila-form">
        <input type="text"
               id="nombre"
               name="nombre"
               list="lista-elementos"
               placeholder="Ej: oro, hidrogeno, wolframio..."
               value="<?= e($busqueda) ?>"
               autocomplete="off"
               autofocus>
        <button type="submit">Buscar</button>
    </div>

    <!--
        <datalist> es un regalo del HTML: le da al cuadro de texto una lista de
        sugerencias mientras se escribe. En la consola esto era imposible.

        Abajo hay un bucle foreach: recorre el array igual que el
        "for el in elementos.values():" de Python.

        Fijate la sintaxis rara con  :  y  endforeach;  en vez de llaves.
        Es una forma alternativa de escribir los bucles que PHP permite
        justamente para cuando se mezclan con HTML: se lee muchísimo mejor
        que llenar todo de llaves sueltas.
    -->
    <datalist id="lista-elementos">
        <?php foreach ($elementos as $el): ?>
            <option value="<?= e($el['Nombre']) ?>"></option>
        <?php endforeach; ?>
    </datalist>
</form>

<?php if ($hayError): ?>
    <!-- Equivale al print("El elemento no fue encontrado") -->
    <p class="aviso aviso-error">
        No encontramos ningún elemento llamado «<?= e($busqueda) ?>».
        Revisá cómo se escribe y probá de nuevo.
    </p>
<?php endif; ?>

<?php if ($datos !== null): ?>
    <!--
        LA FICHA DEL ELEMENTO.
        Reemplaza al  for clave, valor in datos.items(): print(...)
        Los mismos datos, pero mostrados como una tarjeta en vez de líneas
        sueltas de texto.
    -->
    <article class="ficha <?= familia_css($datos) ?>">
        <div class="ficha-simbolo">
            <span class="ficha-z"><?= e((string) $datos['NumeroAtomico']) ?></span>
            <span class="ficha-sim"><?= e($datos['Simbolo']) ?></span>
            <span class="ficha-masa"><?= e((string) $datos['MasaAtomica']) ?></span>
        </div>

        <div class="ficha-cuerpo">
            <h2><?= e($datos['Nombre']) ?></h2>

            <!--
                La categoría química: es lo que dice el color de la ficha.
                Va escrita con todas las letras y no sólo pintada, porque el
                color nunca puede ser la única forma de saber algo.
            -->
            <p class="ficha-categoria">
                <span class="leyenda-color <?= familia_css($datos) ?>"></span>
                <?= e(nombre_categoria(categoria_elemento($datos))) ?>
            </p>

            <dl class="ficha-datos">
                <?php
                /**
                 * foreach con CLAVE y VALOR:
                 *
                 *     Python:  for clave, valor in datos.items():
                 *     PHP:     foreach ($datos as $clave => $valor)
                 *
                 * Es exactamente lo mismo: la flecha => separa clave de valor,
                 * igual que cuando se define el array.
                 */
                foreach ($datos as $clave => $valor):
                    if ($clave === 'Nombre') {
                        continue; // ya lo mostramos arriba, en el <h2>
                    }
                ?>
                    <dt><?= e(etiqueta_campo($clave)) ?></dt>
                    <dd><?= e(valor_legible($clave, $valor)) ?></dd>
                <?php endforeach; ?>
            </dl>

            <p class="ficha-extra">
                <a href="curiosos.php?nombre=<?= urlencode($datos['Nombre']) ?>">
                    Ver el dato curioso del <?= e($datos['Nombre']) ?> →
                </a>
            </p>
        </div>
    </article>
<?php endif; ?>

<?php
/**
 * urlencode() (usado arriba en el enlace) traduce el texto para que sea válido
 * dentro de una dirección web: los espacios pasan a ser %20, los acentos a su
 * código, etc. Sin eso, un nombre con acento podría llegar roto a la otra
 * página. Es la pareja de htmlspecialchars(), pero para URLs en vez de HTML.
 */
require __DIR__ . '/includes/pie.php';
