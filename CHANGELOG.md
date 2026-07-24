# Changelog

Todos los cambios importantes de **Breaking Py** quedan anotados acá.

El formato sigue [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/) y la
numeración usa [versionado semántico](https://semver.org/lang/es/): **MAYOR.MENOR.PARCHE**

- **MAYOR** sube cuando algo deja de funcionar como antes,
- **MENOR** sube cuando se agregan funciones nuevas sin romper lo anterior,
- **PARCHE** sube cuando sólo se arreglan errores.

Cada versión agrupa los cambios en: `Agregado`, `Cambiado`, `Corregido`,
`Movido` y `Seguridad`.

---

## [1.4.1] — 2026-07-24

### Movido
- Los tres archivos `.py` originales pasaron a vivir **sólo** en
  `web/python_original/`, en vez de estar además duplicados en la raíz del
  proyecto. Tener el mismo archivo en dos lugares es pedir problemas: tarde o
  temprano alguien corrige uno y se olvida del otro, y nadie sabe cuál es el
  bueno. Ahora hay una sola copia, y está donde sirve — dentro de `web/`, así
  viaja con el sitio y se puede leer desde el navegador.

### Documentación
- Los dos `README.md` actualizados con la nueva estructura y con cómo correr la
  versión de consola desde su nueva ubicación:
  `cd web/python_original && python3 Breaking_Py.py`

---

## [1.4.0] — 2026-07-24

La página de filtros se dio vuelta: primero el resultado, después los controles.

### Cambiado
- **La tabla ahora va arriba y los filtros abajo.** Antes los controles
  empujaban la tabla fuera de la pantalla y había que hacer scroll para ver
  justo lo que uno acababa de filtrar. La regla que quedó anotada en el código:
  **arriba va el resultado, no el formulario**.
- **Los cinco filtros entran en una sola línea.** Cada uno pasó de ser una
  tarjeta con título, desplegable y botón ancho, a una cajita con el
  desplegable y un botón «＋» al lado. Se usa `flex-wrap`, así que si no entran
  (en un celular, por ejemplo) pasan solos al renglón de abajo, de a dos.
- El título de la página y la cantidad de elementos que cumplen ahora comparten
  un renglón, en vez de ocupar tres.
- Los filtros aplicados (las etiquetas con ×) se mudaron al panel de abajo,
  junto a los controles.
- La leyenda de colores se achicó: menos relleno, letra más chica y una columna
  más.
- Textos más cortos donde se cortaban: los rangos de masa pasaron de
  «Masas bajas (hasta 20 u)» a «Hasta 20 u», y las opciones de radiactividad a
  «Sí» / «No». El nombre completo ya lo dice el título de cada filtro.

### Accesibilidad
- El botón «＋» lleva `aria-label` con el texto de verdad («Aplicar filtro de
  grupo»), porque un símbolo solo no le sirve a quien usa un lector de
  pantalla.

---

## [1.3.0] — 2026-07-24

Entra en juego el tercer lenguaje: JavaScript.

### Agregado
- **La tabla periódica completa en la portada.** Como la tabla ya estaba armada
  como pedazo aparte (`includes/tabla.php`), ponerla fueron dos líneas.
- **Cuadro emergente al hacer clic en un elemento** (`includes/dialogo.php` +
  `assets/js/dialogo.js`): muestra símbolo, los siete datos, la categoría con
  su color y el dato curioso, sin salir de la tabla. Funciona igual en la
  portada y en la página de filtros.
  - Usa la etiqueta `<dialog>` del HTML, que el navegador ya trae: se encarga
    solo de oscurecer el fondo, bloquear lo de atrás, llevar el foco del
    teclado adentro y cerrar con la tecla Escape. También cierra haciendo clic
    afuera o en cualquiera de los dos botones.
  - Los datos viajan en atributos `data-…` de cada casillero, así que el cuadro
    se abre al instante, sin pedirle nada al servidor.
  - **Mejora progresiva**: los casilleros siguen siendo enlaces normales a
    `elemento.php`. Si el JavaScript no carga, el clic lleva a la ficha
    completa como antes; nada de lo que ya funcionaba depende de lo nuevo.
  - Respeta `prefers-reduced-motion`: quien pidió en su sistema no ver
    animaciones, no las recibe.

### Cambiado
- El cálculo del dato curioso del elemento más pesado se mudó de `curiosos.php`
  a la función `datos_curiosos()` en `includes/funciones.php`, porque ahora lo
  necesitan dos lugares. Es la regla de siempre: la primera vez que el mismo
  código hace falta en dos lados, se convierte en función.
- Los casilleros muestran el cursor de mano, para que se note que se tocan.

---

## [1.2.1] — 2026-07-24

### Corregido
- **Error 500 al entrar a la carpeta del código original.** El `.htaccess` de
  `web/python_original/` usaba dos directivas que no todos los hostings
  aceptan, y cuando el servidor encuentra una que no le habilitaron responde
  *Internal Server Error* sin decir cuál es:
  - `Options -ExecCGI` — muchos hostings compartidos sólo permiten cambiar
    algunas `Options` (en general `Indexes`) y rechazan el resto. No hacía
    falta: el `AddType text/plain .py` ya evita que un `.py` se ejecute.
  - `<FilesMatch "^(?!...)">` — la "negación anticipada" de las expresiones
    regulares no la soportan todos los servidores (LiteSpeed, habitual en
    hostings con cPanel, puede rechazarla). Se reemplazó por una lista simple
    de extensiones bloqueadas.

  El archivo quedó con lo mínimo necesario, que es la forma correcta de
  escribir un `.htaccess`: cuanto más corto, en más servidores funciona.

---

## [1.2.0] — 2026-07-24

Reorganización para publicar en GitHub y visor del código original en el sitio.

### Agregado
- **Visor del código original** en `web/python_original/`: muestra los tres
  archivos `.py` desde el navegador, con pestañas para elegir cuál mirar,
  cantidad de líneas, peso, y a qué archivo de la versión web corresponde cada
  uno.
- **Créditos en el pie** de todas las páginas: el código original en Python es
  de **Julia López Rocchi** y **Joaquín Moyano**.
- Enlace en el pie a la carpeta del código original, para poder consultarlo.
- Constante `BREAKING_PY_VERSION` en `includes/funciones.php`: el número de
  versión se muestra en el pie y se cambia en un solo lugar.
- Variable `$base` en `includes/cabecera.php`, para que las páginas que están
  en subcarpetas armen bien sus enlaces al CSS y al menú.
- Este `CHANGELOG.md` y un `.gitignore`.

### Movido
- `README.md` pasó a `web/README.md`.
- `python_original/` pasó a `web/python_original/`, así viaja con el sitio y se
  puede consultar desde el navegador.

### Seguridad
- El visor usa **lista blanca** de archivos: lo que llega por la URL sólo sirve
  para *elegir* de una lista fija, nunca para armar una ruta. Sin eso, un
  pedido como `?archivo=../../../../etc/passwd` (*path traversal*) podría leer
  archivos del servidor. Se probó y no filtra nada.
- El contenido de los archivos se imprime escapado con `htmlspecialchars()`.
- `web/python_original/.htaccess`: obliga a que los `.py` se traten como texto
  plano (`RemoveHandler` + `AddType text/plain`) para que ningún servidor con
  CGI activado los ejecute, apaga `ExecCGI` y el listado de directorios, y sólo
  permite servir los cuatro archivos que corresponden.

---

## [1.1.0] — 2026-07-24

Los colores pasaron a significar algo y la tabla tomó su forma real.

### Agregado
- **Tabla periódica de verdad**: cada elemento va en su posición real
  (columna = grupo, fila = período), con lantánidos y actínidos en las dos
  tiras de abajo, como en cualquier tabla impresa. Se dibuja con CSS Grid en
  `includes/tabla.php`, usando la función `posicion_tabla()`.
- **Categorías químicas estándar** (`categorias()` en `includes/funciones.php`):
  metales alcalinos, alcalinotérreos, de transición, otros metales, no metales,
  metaloides, halógenos, gases nobles, lantánidos, actínidos y elementos de
  propiedades desconocidas. Los 11 grupos suman los 118 elementos.
- **Leyenda de colores** (`includes/leyenda.php`), que además muestra cuántos
  elementos de cada categoría quedaron cuando hay filtros puestos.
- La ficha de cada elemento ahora indica su categoría con nombre y color.
- Los casilleros muestran el nombre de la categoría al pasarles el mouse.

### Cambiado
- **Al filtrar, los elementos que no cumplen ya no desaparecen: se atenúan.**
  Así se ve dónde caen los que sí cumplen dentro de la tabla — filtrando por
  "radiactivos" se enciende toda la parte de abajo. Los que cumplen reciben
  además un aro y un resplandor.
- Los colores dejaron de ser decoración. Antes eran una mezcla arbitraria de
  grupo y bloque; ahora cada color es una categoría química.
- La paleta se eligió **calculando, no a ojo**: se midió luminosidad,
  saturación, contraste contra el fondo y separación para personas con
  daltonismo. Los tonos siguen la convención de las tablas de siempre.
- La lógica de filtrado **no se tocó**: sigue siendo el mismo `array_filter()`.
  Lo único que cambió es qué se hace con su resultado — antes decidía *qué
  dibujar*, ahora decide *qué resaltar*.

### Notas
- Con 11 categorías es imposible elegir 11 colores que todo el mundo distinga:
  el objetivo de separación era 8 y el máximo alcanzable fue 5,2. Por eso el
  color nunca va solo (posición en la tabla + símbolo + nombre + leyenda), y el
  atenuado se hace con opacidad y no cambiando colores.

---

## [1.0.0] — 2026-07-24

Primera versión web: el programa de consola en Python, convertido a PHP.

### Agregado
- Las cuatro funciones del programa original, ahora como páginas:
  - `index.php` — el menú (era la función `codigo()`)
  - `filtrar.php` — filtrar por característica (era la opción `[1]`)
  - `elemento.php` — buscar un elemento (era `segundoOp()`)
  - `quiz.php` — el quiz (era `quiz()`)
  - `curiosos.php` — datos curiosos (era `Datos()`)
- Los datos separados de la lógica, en `includes/tabla_periodica.php` (los 118
  elementos) y `includes/datos_curiosos.php` (los 118 datos curiosos).
- Cabecera y pie compartidos por todas las páginas.
- Hoja de estilos propia, con diseño adaptado a celulares.
- Tabla de sinónimos para buscar: "zinc" encuentra al cinc, "tungsteno" al
  wolframio.
- `README.md` con la guía de Python a PHP y las instrucciones para publicar.

### Cambiado
- El `while True` con `input()` se reemplazó por formularios: en la web el
  programa termina en cada clic, así que el bucle lo hace la persona navegando.
- La variable `global score` y las listas de filtros pasaron a `$_SESSION`,
  que es lo único que sobrevive de una página a la siguiente.
- Se eliminó la recursión que usaba el original para reintentar
  (`segundoOp()` y `codigo()` llamándose a sí mismas), que podía terminar en
  `RecursionError`.
- Los formularios que cambian algo usan POST y después redirigen
  (patrón *POST → Redirect → GET*), así apretar F5 no vuelve a aplicar un
  filtro ni a sumar puntos en el quiz.

### Corregido
- **Lantánidos y actínidos nunca acertaban en el quiz**: la respuesta del
  usuario pasaba por `quitar_tildes()` y quedaba `"lantanidos"`, pero se
  comparaba contra `"Lantánidos"` con tilde. Ahora se normalizan los dos lados.
- **Los rangos de masa dejaban huecos**: eran `1..20`, `21..100`, `101..200` y
  `>200`, así que un elemento de 20,18 u (el neón) no entraba en ninguno. Ahora
  los cuatro rangos se tocan y suman los 118 elementos.
- **Cuatro datos curiosos eran imposibles de mostrar**: `datos.py` usaba las
  claves `zinc`, `tantalo`, `darmstadtio` y `teneso`, pero en la tabla esos
  elementos figuraban como `cinc`, `tantalio`, `darmstadio` y `tennessinio`.
- **El dato del elemento más pesado se lo llevaba el que no era**: el
  tennessinio y el oganesón pesan los dos 294 u y `max()` se queda con el
  primero. Ahora se desempata por número atómico.
- **Un nombre mal escrito**: `"Luctecio"` en vez de `"Lutecio"`, que además
  impedía encontrar ese elemento buscándolo por su nombre.

### Seguridad
- Todo lo que viene del usuario se imprime escapado con `htmlspecialchars()`
  (función `e()`), para evitar **XSS**.
- Lo que el usuario no debe poder tocar (la respuesta correcta del quiz, el
  puntaje) vive en la sesión, en el servidor, y no en campos del HTML.
- Las características de filtrado se validan contra una lista fija antes de
  guardarse.
- `.htaccess` que apaga el listado de directorios y bloquea el acceso directo
  a los archivos de `includes/`.

---

### Autoría

El programa original en Python es de **Julia López Rocchi** y
**Joaquín Moyano**. La versión web mantiene su lógica y su forma de resolver
las cosas; lo que cambió es el medio.

[1.4.1]: #141--2026-07-24
[1.4.0]: #140--2026-07-24
[1.3.0]: #130--2026-07-24
[1.2.1]: #121--2026-07-24
[1.2.0]: #120--2026-07-24
[1.1.0]: #110--2026-07-24
[1.0.0]: #100--2026-07-24
