/* ===========================================================================
 *  dialogo.js  —  ABRE EL CUADRO DEL ELEMENTO AL HACER CLIC EN LA TABLA
 * ===========================================================================
 *
 *  Tu primer archivo de JavaScript. Buenas noticias: si sabés Python, ya sabés
 *  la mitad. Las diferencias grandes son estas:
 *
 *      Python                          JavaScript
 *      ------------------------------  ------------------------------------
 *      nombre = "oro"                  const nombre = "oro";
 *      def saludar(a):                 function saludar(a) { }
 *      lambda x: x * 2                 (x) => x * 2
 *      None / True / False             null / true / false
 *      # comentario                    // comentario
 *      f"hola {nombre}"                `hola ${nombre}`
 *      lista[0]                        lista[0]          (igual)
 *      dic["clave"]                    obj.clave         (o obj["clave"])
 *
 *  · Las variables se declaran con  const  (no se puede reasignar) o
 *    let  (sí se puede). Usá const siempre que puedas: si más adelante alguien
 *    intenta pisarla por error, JavaScript avisa en vez de dejarlo pasar.
 *  · Las llaves { } arman los bloques, igual que en PHP. La indentación es
 *    sólo para leer mejor.
 *  · Las líneas terminan en  ;  igual que en PHP.
 *
 *  Y la diferencia conceptual más importante, que ya vimos en dialogo.php:
 *  esto NO corre en el servidor. Corre en la computadora de quien visita la
 *  página, después de que PHP terminó su trabajo y se fue.
 * =========================================================================== */


// document es toda la página. querySelector() busca el primer elemento que
// coincida con un selector de CSS — sí, los MISMOS selectores del archivo de
// estilos. '#dialogo-elemento' significa "el que tiene id dialogo-elemento".
const dialogo = document.querySelector('#dialogo-elemento');
const tabla   = document.querySelector('.tabla-periodica');

// Si en esta página no hay tabla o no hay cuadro, no hay nada que hacer.
// Salir temprano cuando falta algo evita errores raros más adelante.
if (dialogo && tabla) {

    /**
     * Llena el cuadro con los datos del casillero en el que se hizo clic.
     *
     * @param {HTMLElement} celda  el casillero clickeado
     */
    const llenarDialogo = (celda) => {

        // dataset trae TODOS los atributos data-… del casillero, ya listos:
        //     data-nombre="Oro"  →  datos.nombre  vale  "Oro"
        // Es un objeto, o sea lo más parecido a un diccionario de Python.
        const datos = celda.dataset;

        // El color de la categoría: se le pone al cuadro la misma clase CSS
        // (cat-noble, cat-alcalino…) que tiene el casillero, y así el borde y
        // el fondo salen del color que corresponde, sin repetir ni un color acá.
        dialogo.className = 'dialogo cat-' + datos.categoria;

        // querySelectorAll() devuelve TODOS los que coincidan, y forEach()
        // los recorre uno por uno. Es el  for … in …  de Python.
        dialogo.querySelectorAll('[data-campo]').forEach((hueco) => {

            const campo = hueco.dataset.campo;   // 'nombre', 'masa', 'z'…
            const valor = datos[campo];          // lo que traía el casillero

            if (campo === 'fichaUrl') {
                // Este no es un texto: es el destino del botón "Ver la ficha".
                hueco.href = celda.href;
                return;                          // return corta esta vuelta
            }

            if (campo === 'color') {
                return;   // el cuadrito de color se pinta solo, con la clase CSS
            }

            /**
             * ¡OJO ACÁ! Se usa textContent y NO innerHTML.
             *
             * textContent mete el valor como TEXTO: si dijera <b>hola</b> se
             * vería tal cual, con los signos y todo.
             * innerHTML lo mete como CÓDIGO HTML: el navegador lo interpreta.
             *
             * Es exactamente el mismo peligro que en PHP nos hacía escribir
             * todo con e() — el ataque se llama XSS. En JavaScript la defensa
             * es esta: usar textContent salvo que tengas una razón muy buena
             * para lo otro (y casi nunca la hay).
             */
            hueco.textContent = valor ?? '';
        });

        // Si el elemento no tiene dato curioso cargado, se esconde ese párrafo
        // en vez de dejar un espacio en blanco.
        const curioso = dialogo.querySelector('.dialogo-curioso');
        curioso.hidden = !datos.curioso;
    };


    /**
     * UN SOLO ESCUCHADOR PARA LOS 118 CASILLEROS
     *
     * Se le podría poner un "escuchá los clics" a cada casillero, pero serían
     * 118 escuchadores haciendo lo mismo. En vez de eso se le pone UNO a la
     * tabla entera: cuando hacés clic en un casillero, el clic "burbujea"
     * hacia arriba hasta la tabla, y ahí preguntamos de dónde vino.
     *
     * evento.target.closest('.celda') = "empezando por donde se hizo clic,
     * subí hasta encontrar algo con la clase celda". Hace falta porque el clic
     * puede caer en el símbolo o en el nombre, que están adentro del casillero.
     *
     * Esto se llama DELEGACIÓN DE EVENTOS y es de las primeras cosas que se
     * aprenden en JavaScript.
     */
    tabla.addEventListener('click', (evento) => {

        const celda = evento.target.closest('.celda');
        if (!celda) {
            return;   // el clic cayó en un hueco de la tabla: no hacemos nada
        }

        // El casillero es un enlace de verdad a elemento.php. preventDefault()
        // le dice al navegador "no sigas el enlace, ya me encargo yo".
        // Si este archivo no cargara, esta línea nunca correría y el enlace
        // funcionaría normal: por eso la página no se rompe sin JavaScript.
        evento.preventDefault();

        llenarDialogo(celda);

        // showModal() es lo que abre el cuadro. El navegador solo se encarga
        // de oscurecer el fondo, bloquear lo de atrás, mover el foco del
        // teclado adentro y cerrar con la tecla Escape.
        dialogo.showModal();
    });


    // Los botones marcados con data-cerrar cierran el cuadro.
    dialogo.querySelectorAll('[data-cerrar]').forEach((boton) => {
        boton.addEventListener('click', () => dialogo.close());
    });


    /**
     * Cerrar al hacer clic afuera.
     *
     * El fondo oscuro es parte del propio <dialog>, así que un clic ahí llega
     * como un clic en el dialogo. La forma de distinguirlo: si el clic fue
     * sobre el <dialog> MISMO (y no sobre algo de adentro), fue en el fondo.
     */
    dialogo.addEventListener('click', (evento) => {
        if (evento.target === dialogo) {
            dialogo.close();
        }
    });
}
