# Breaking Py — de Python a la web

Este proyecto empezó como un programa de consola en Python sobre la tabla
periódica, escrito por **Julia López Rocchi** y **Joaquín Moyano**. Acá está el
mismo programa convertido en un sitio web hecho con **PHP + HTML + CSS**, listo
para subir a un servidor.

El código está comentado línea por línea pensando en alguien que ya sabe lo
básico de Python y está aprendiendo PHP: cada comentario muestra cómo se hacía
en Python y cómo se hace lo mismo en PHP.

Los cambios de cada versión están anotados en
[`CHANGELOG.md`](../CHANGELOG.md), en la raíz del repositorio.

---

## Qué hay en cada carpeta

```
BreakingPy/
├── README.md               ← portada del repositorio
├── CHANGELOG.md            ← qué cambió en cada versión
├── .gitignore
│
└── web/                    ← ESTO es lo que se sube al servidor
    ├── README.md               esta guía
    ├── index.php               menú principal        (era la función codigo)
    ├── filtrar.php             opción [1] filtrar    (era el if primera_elec == "1")
    ├── elemento.php            opción [2] buscar     (era la función segundoOp)
    ├── quiz.php                opción [3] quiz       (era la función quiz)
    ├── curiosos.php            opción [4] curiosos   (era la función Datos)
    ├── ejercicios.php          opción [5] ejercicios (nueva, no estaba en Python)
    ├── particulas.php            partículas, número atómico y másico, iones
    ├── misterioso.php            niveles de energía: el elemento misterioso
    ├── clasificar.php            ¿metal, no metal o metaloide?
    ├── tabla_muda.php            carácter metálico: tabla muda y ficha
    ├── propiedades.php           propiedades periódicas
    ├── .htaccess               configuración del servidor Apache
    ├── includes/
    │   ├── tabla_periodica.php   los 118 elementos   (era tabla_periodica.py)
    │   ├── datos_curiosos.php    los 118 datos       (estaba dentro de datos.py)
    │   ├── funciones.php         funciones comunes   (era quitar_tildes y demás)
    │   ├── quimica.php           isótopos, iones, niveles, cargas (para los ejercicios)
    │   ├── marcador.php          el marcador de cada ejercicio
    │   ├── correccion.php        la lista de respuestas corregidas con su porqué
    │   ├── acciones.php          los botones de otra ronda y reiniciar marcador
    │   ├── aviso_ronda.php       el aviso de "esas respuestas eran de otra ronda"
    │   ├── tabla.php             dibuja la tabla periódica con su forma real
    │   ├── leyenda.php           qué significa cada color
    │   ├── dialogo.php           el cuadro que se abre al tocar un elemento
    │   ├── cabecera.php          la parte de arriba de todas las páginas
    │   └── pie.php               la parte de abajo de todas las páginas
    ├── python_original/        EL PROGRAMA ORIGINAL, intacto
    │   ├── Breaking_Py.py        el programa principal
    │   ├── datos.py              los datos curiosos
    │   ├── tabla_periodica.py    los 118 elementos
    │   ├── index.php             el visor con pestañas, para leerlos en la web
    │   └── .htaccess             protecciones de esta carpeta
    └── assets/
        ├── css/estilos.css       todos los colores y tamaños
        └── js/dialogo.js         abre el cuadro al tocar un elemento
```

Los `.py` originales **no se tocaron**: están tal cual en
`web/python_original/`, que es donde viven ahora. Están adentro de `web/` a
propósito, por dos razones: viajan con el sitio cuando lo subís, y se pueden
leer desde el navegador (el enlace del pie de página lleva ahí).

Y siguen funcionando igual que siempre:

```bash
cd web/python_original
python3 Breaking_Py.py
```

---

## Cómo probarlo en tu computadora

PHP trae su propio servidor de prueba. Abrí una terminal y escribí:

```bash
cd ~/Documentos/BreakingPy/web
php -S localhost:8000
```

Después abrí el navegador en **http://localhost:8000** y listo.

Si no tenés PHP instalado:

```bash
sudo apt install php-cli      # Ubuntu / Debian / Mint
```

Para cortar el servidor: `Ctrl + C` en la terminal.

---

## Cómo subirlo a un servidor web

El proyecto no usa base de datos ni ninguna librería externa, así que subirlo es
copiar archivos y nada más.

1. Conseguí un hosting con PHP 8 o superior (los gratuitos como InfinityFree,
   000webhost o ByetHost alcanzan de sobra; también sirve cualquier hosting
   compartido con cPanel).
2. Entrá al panel del hosting y buscá el **administrador de archivos** o
   conectate por **FTP** (con FileZilla, por ejemplo).
3. Copiá **todo el contenido de la carpeta `web/`** (no la carpeta `web` en sí)
   adentro de la carpeta pública del servidor, que suele llamarse
   `public_html`, `htdocs` o `www`. Incluí los archivos `.htaccess`: empiezan
   con punto, así que son invisibles y muchos programas de FTP no los copian si
   no les activás "mostrar archivos ocultos".
4. Entrá a tu dominio. Debería aparecer el menú principal.

Requisitos del servidor: PHP 8.0 o más nuevo y la extensión `mbstring`
(viene activada por defecto en el 99% de los hostings). No hace falta nada más.

### Subirlo a GitHub

El repositorio es la carpeta entera (`BreakingPy/`), no sólo `web/`: así queda
guardado también el historial de cambios y la portada del repositorio.

```bash
cd ~/Documentos/BreakingPy
git init
git add .
git commit -m "Breaking Py 1.2.0"
git branch -M main
git remote add origin https://github.com/TU_USUARIO/breaking-py.git
git push -u origin main
```

Después de cada cambio: anotalo en `CHANGELOG.md`, subí el número en
`BREAKING_PY_VERSION` (dentro de `includes/funciones.php`) y hacé un commit
nuevo. Para marcar una versión publicada se usan las etiquetas:

```bash
git tag -a v1.2.0 -m "Visor del código original"
git push origin v1.2.0
```

**Si algo falla**, lo primero que hay que mirar siempre es el log de errores del
hosting. Y para ver los errores en pantalla mientras probás, podés poner estas
dos líneas al principio de `index.php` (¡y sacarlas antes de publicar!):

```php
ini_set('display_errors', '1');
error_reporting(E_ALL);
```

---

## Lo esencial de Python → PHP

| Python | PHP |
|---|---|
| `nombre = "oro"` | `$nombre = 'oro';` |
| `# comentario` | `// comentario` o `/* varias líneas */` |
| `elementos = {"oro": {...}}` | `$elementos = ['oro' => [...]];` |
| `elementos["oro"]` | `$elementos['oro']` |
| `len(lista)` | `count($lista)` |
| `texto.lower()` | `mb_strtolower($texto)` |
| `texto.strip()` | `trim($texto)` |
| `texto.replace(a, b)` | `str_replace($a, $b, $texto)` |
| `str(5)` | `(string) 5` |
| `int("5")` | `(int) '5'` |
| `f"hola {nombre}"` | `"hola {$nombre}"` |
| `for x in lista:` | `foreach ($lista as $x) { }` |
| `for k, v in d.items():` | `foreach ($d as $k => $v) { }` |
| `if a: ... elif b: ... else: ...` | `if ($a) { } elseif ($b) { } else { }` |
| `[x for x in l if cond]` | `array_filter($l, fn($x) => cond)` |
| `lambda x: x * 2` | `fn($x) => $x * 2` |
| `match x: case 1:` | `match ($x) { 1 => ..., default => ... }` |
| `random.choice(list(d.keys()))` | `array_rand($d)` |
| `masa // 1` | `floor($masa)` |
| `del d["k"]` | `unset($d['k'])` |
| `True` / `False` / `None` | `true` / `false` / `null` |
| `and` / `or` / `not` | `&&` / `\|\|` / `!` |
| la indentación arma los bloques | las llaves `{ }` arman los bloques |
| (no hace falta) | cada línea termina en `;` |

Y tres cosas que en Python no existían:

- **`$`** delante de cada variable.
- **`===`** además de `==`. El `===` compara valor **y** tipo, y es el que
  conviene usar casi siempre: en PHP, `0 == 'texto'` puede dar resultados raros,
  pero `0 === 'texto'` es siempre `false`.
- **Tipos en las funciones**: `function f(string $a): bool`. Es opcional, pero
  hace que PHP te avise cuando te equivocás.

---

## Las tres ideas grandes de programar para la web

Estas son las que de verdad cambian la forma de pensar el programa. Están
explicadas con más detalle adentro de cada archivo.

### 1. El programa muere y revive en cada clic

En Python el `while True` mantenía vivo el programa y `input()` lo dejaba
esperando. En la web **no existe `input()`**: PHP arranca, arma el HTML, lo
manda y termina. Cuando la persona hace clic, arranca de nuevo desde cero.

El bucle no lo hace tu código: lo hace la persona navegando.

- El `input()` se convierte en un `<form>`.
- Lo que se escribe llega en los arrays `$_GET` o `$_POST`.
- Los `print()` se convierten en HTML.
- La recursión para "volver al menú" (`codigo()` llamándose a sí misma)
  desaparece: ahora es un enlace `<a href="index.php">`.

### 2. Lo que hay que recordar va a la sesión

Como el programa muere en cada clic, las variables se pierden. Todo lo que
tenga que sobrevivir de una página a la siguiente (el puntaje del quiz, los
filtros aplicados) va a `$_SESSION`, un array especial que PHP guarda en el
servidor. Reemplaza al `global score` del original.

### 3. Nunca confiar en lo que llega del usuario

Es lo más importante de todo. En la consola sólo vos usabas el programa; en la
web lo usa cualquiera, y cualquiera puede mandar cualquier cosa.

- **Al mostrar**: todo lo que venga del usuario se imprime con `e($algo)`, que
  usa `htmlspecialchars()`. Si no, alguien puede escribir `<script>` en un
  formulario y ejecutar código en el navegador de quien lo lea (eso se llama
  **XSS**).
- **Al recibir**: se valida antes de usar. En `filtrar.php` se comprueba que la
  característica exista de verdad antes de guardarla.
- **Lo que el usuario no debe tocar, no viaja**: la respuesta correcta del quiz
  y el puntaje viven en la sesión (en el servidor), no en el HTML, porque el
  HTML se puede editar desde el navegador.
- **Nunca armar una ruta de archivo con lo que manda el usuario.** El visor de
  `python_original/` usa una lista fija de archivos permitidos, y lo que llega
  por la URL sólo sirve para elegir de esa lista. Si se usara directamente,
  alguien podría pedir `?archivo=../../../../etc/passwd` y llevarse archivos
  del servidor: ese ataque se llama **path traversal**.

### Publicar el código fuente: cuándo se puede

Este sitio muestra su propio código original y está perfecto, porque son
archivos de ejemplo sin ningún secreto adentro. La regla es esa: **se publica
el código que no tiene secretos**. Nunca subas a un lugar público un archivo
con contraseñas, tokens o datos de conexión a una base de datos — esos van en
un archivo aparte que el `.gitignore` deja afuera del repositorio.

El `.htaccess` de esa carpeta se encarga del resto: obliga a que los `.py` se
traten como texto plano (así ningún servidor con CGI activado los ejecute),
apaga el listado de directorios y sólo permite servir los cuatro archivos que
corresponden.

---

## Los colores significan algo

Cada color del sitio es una **categoría química**, la misma clasificación
estándar que usan las tablas periódicas de los libros:

| Color | Categoría | Cuántos |
|---|---|---|
| 🔴 rojo carmín | Metales alcalinos | 6 |
| 🟠 naranja | Metales alcalinotérreos | 6 |
| 🟡 oro oscuro | Metales de transición | 38 |
| 🟢 verde oscuro | Otros metales (post-transición) | 8 |
| 🟢 verde | No metales | 7 |
| 🩵 verde azulado | Metaloides | 6 |
| 🔵 azul | Halógenos | 5 |
| 🟣 violeta | Gases nobles | 6 |
| 🟣 magenta | Lantánidos | 15 |
| 🩷 rosa | Actínidos | 15 |
| ⚪ gris | Propiedades desconocidas | 6 |

La categoría se decide por número atómico en `categorias()`, dentro de
`includes/funciones.php`, y la leyenda se dibuja sola a partir de esa misma
lista: si mañana cambiás una categoría, la leyenda se actualiza sin que toques
nada más. Los datos y lo que se ve en pantalla nunca se escriben dos veces.

**Cómo se eligieron los colores.** No a ojo: se calcularon con un validador que
mide cuatro cosas de cada color (luminosidad, saturación, contraste contra el
fondo y qué tan distinguibles son entre sí para las personas con daltonismo).
Los tonos siguen la convención de siempre — alcalinos rojos, gases nobles
violetas — pero los valores exactos salieron de una búsqueda por computadora.

Vale la pena saber el límite que apareció: **con once categorías es imposible
elegir once colores que todo el mundo distinga**. El objetivo del validador es
una separación de 8 y el máximo alcanzable con tantas categorías fue 5,2 —
o sea que alguien con daltonismo va a confundir algunos pares. Por eso el color
nunca va solo:

- la **posición** en la tabla ya dice la categoría (para eso está ordenada),
- cada casillero muestra su **símbolo y su nombre**,
- pasando el mouse aparece el **nombre de la categoría**,
- la **leyenda** está siempre a la vista, con las cantidades.

Y por eso los elementos filtrados se atenúan con **opacidad**, no cambiándoles
el color: el color ya está ocupado diciendo la categoría, y una misma señal no
puede significar dos cosas a la vez.

---

## Por qué la tabla tiene esa forma

Los elementos no están en una grilla cualquiera: cada casillero está en su
lugar de la tabla periódica de verdad, y esa posición **es información**.

    columna = Grupo      (1 a 18)
    fila    = Período    (1 a 7)

Los elementos de una misma columna se parecen entre sí porque tienen los mismos
electrones en la última capa: esa es la idea genial de Mendeléiev, y se pierde
si los ponemos uno atrás del otro. Los lantánidos y actínidos van en dos tiras
aparte abajo, como en todas las tablas, porque si no la tabla sería
larguísima.

Todo eso sale de datos que **ya estaban** en `tabla_periodica.py` desde el
principio (Grupo y Período): no hubo que agregar ni un dato nuevo, sólo se pudo
dibujar recién ahora. El trabajo lo hace `posicion_tabla()` en
`includes/funciones.php` y lo dibuja `includes/tabla.php` con CSS Grid.

Al filtrar, los que no cumplen **no desaparecen: se atenúan**. Así se ve dónde
caen los que sí cumplen dentro de la tabla, que es una información que se
perdía cuando se listaban sueltos. Probá filtrar por "radiactivos" y mirá cómo
se enciende toda la parte de abajo.

La lógica de filtrado **no cambió ni una coma** para que esto funcionara: sigue
siendo el mismo `array_filter()` que traduce tus list comprehensions. Lo único
distinto es qué se hace con el resultado — antes decidía *qué dibujar*, ahora
decide *qué resaltar*.

---

## El tercer lenguaje: JavaScript

Al hacer clic en un elemento de la tabla se abre un cuadro con sus datos. Eso
ya no lo puede hacer PHP, y entender por qué es la mejor forma de ver para qué
sirve cada lenguaje:

| | Dónde corre | Cuándo |
|---|---|---|
| **PHP** | en el servidor | arma el HTML y termina, antes de que veas nada |
| **JavaScript** | en tu computadora, dentro del navegador | sigue vivo mientras la página esté abierta |

> **PHP ya terminó cuando vos ves la página. JavaScript recién empieza.**

Por eso JavaScript puede reaccionar a un clic sin recargar, y por eso **no**
sirve para cuidar nada: cualquiera puede abrir las herramientas del navegador y
cambiarlo. Validar, decidir y guardar sigue siendo trabajo de PHP.

Tres cosas que vale la pena mirar en `assets/js/dialogo.js`:

- **Delegación de eventos**: en vez de 118 "escuchadores" de clic (uno por
  casillero) hay **uno solo**, puesto en la tabla entera. El clic "burbujea"
  hacia arriba y ahí se pregunta de qué casillero vino.
- **`textContent`, nunca `innerHTML`**: es el mismo peligro que en PHP nos hacía
  escribir todo con `e()`. `textContent` mete el valor como texto;
  `innerHTML` lo interpreta como código. Otra vez XSS, en otro lenguaje.
- **Mejora progresiva**: los casilleros siguen siendo enlaces normales a
  `elemento.php`. El JavaScript llama a `preventDefault()` para quedarse con el
  clic, pero si el archivo no carga, el enlace funciona como siempre. Lo nuevo
  no puede romper lo que ya andaba.

El cuadro usa la etiqueta `<dialog>`, que el HTML ya trae hecha: el navegador se
encarga solo de oscurecer el fondo, bloquear lo de atrás, llevar el foco del
teclado adentro y cerrar con Escape. Antes había que armar todo eso a mano con
`div`s, y salía mal. **Cuando el HTML ya trae algo hecho, casi siempre conviene
usar eso antes que inventarlo de nuevo.**

---

## Cosas que se corrigieron al traducir

El programa original funcionaba, pero tenía algunos errores escondidos. Están
marcados con un comentario en el código, y son de los que más se aprenden:

1. **Lantánidos y actínidos nunca acertaban en el quiz.** La respuesta del
   usuario pasaba por `quitar_tildes()` y quedaba `"lantanidos"`, pero se
   comparaba contra `"Lantánidos"` con tilde. Al comparar textos hay que
   normalizar **los dos lados**, no uno solo. → `quiz.php`

2. **Los rangos de masa dejaban huecos.** Los rangos eran `1..20`, `21..100`,
   `101..200` y `>200`. Como las masas tienen decimales, cualquier elemento
   entre 20 y 21 (el neón pesa 20,18) no entraba en ningún rango y desaparecía.
   Ahora los rangos se tocan: los cuatro suman los 118 elementos. → `filtrar.php`

3. **Claves que no coincidían entre archivos.** `datos.py` usaba `"zinc"`,
   `"tantalo"`, `"darmstadtio"` y `"teneso"`, pero en `tabla_periodica.py` esos
   elementos se llamaban `cinc`, `tantalio`, `darmstadio` y `tennessinio`. Esos
   cuatro datos curiosos eran imposibles de mostrar. Es el peor tipo de error:
   no rompe nada, simplemente no pasa nada. → `includes/datos_curiosos.php`

4. **El dato del elemento más pesado se lo llevaba el que no era.** El
   tennessinio y el oganesón pesan los dos 294 u, y `max()` se queda con el
   primero que encuentra. Ahora se desempata por número atómico. → `curiosos.php`

5. **Un nombre mal escrito**: `"Luctecio"` en vez de `"Lutecio"`. Además de la
   falta de ortografía, hacía que el elemento no se pudiera encontrar buscándolo
   por su nombre. → `includes/tabla_periodica.php`

6. **Recursión infinita evitable.** En el original, cada reintento
   (`segundoOp()` llamándose a sí misma, `codigo()` llamándose a sí misma) dejaba
   una llamada apilada en memoria; con la paciencia suficiente el programa se
   caía con `RecursionError`. En la web el problema no existe, porque cada clic
   es una ejecución nueva y limpia.

---

## Ideas para seguir

Cuando quieras agregarle cosas, estas son las siguientes en dificultad:

- **Resaltar en la tabla** el elemento que se buscó en `elemento.php`, pasándole
  `$resaltados = ['oro' => $datos];`
- **Pedirle los datos al servidor con `fetch()`** en vez de mandarlos todos en
  atributos `data-…`. Hoy los 118 elementos viajan con la página, que es lo más
  simple y lo más rápido para este tamaño. Con muchos más datos convendría
  pedirlos de a uno: eso se llama **AJAX**, se hace con `fetch()` y un archivo
  PHP que responda **JSON** en vez de HTML. Es el próximo escalón natural.
- Un **ranking de puntajes** del quiz. Ahí sí vas a necesitar guardar datos de
  forma permanente: es el momento de aprender bases de datos con **MySQL** y
  **PDO**.
- Un **quiz al revés**: se muestra el símbolo y hay que decir el nombre.
- Que el **quiz corrija sin recargar** la página, con el mismo `fetch()`.

---

## Autoría y versiones

El programa original en Python es de **Julia López Rocchi** y
**Joaquín Moyano**. La versión web mantiene su lógica y su forma de resolver
las cosas; lo que cambió es el medio.

El código original se puede leer desde el propio sitio, en el enlace del pie de
página, o directamente en la carpeta `python_original/`.

Versión actual: **1.4.1**. El detalle de cada versión está en
[`CHANGELOG.md`](../CHANGELOG.md).
