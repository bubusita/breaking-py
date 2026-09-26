/* ==========================================================================
   confirmar.js  —  PEDIR CONFIRMACIÓN ANTES DE UN ENLACE QUE BORRA ALGO

   Cualquier enlace con  data-confirmar="¿Seguro?"  muestra esa pregunta
   antes de seguir. Si la persona toca "Cancelar", preventDefault() frena el
   enlace y no pasa nada.

   Antes esto era un  onclick="return confirm(...)"  escrito en el HTML, pero
   la política de seguridad del sitio (CSP) no deja ejecutar JavaScript
   escrito adentro de la página: todo tiene que venir de un archivo como este.
   ========================================================================== */
document.querySelectorAll('[data-confirmar]').forEach((enlace) => {
    enlace.addEventListener('click', (evento) => {
        if (!confirm(enlace.dataset.confirmar)) {
            evento.preventDefault();
        }
    });
});
