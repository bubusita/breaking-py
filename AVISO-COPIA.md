# ⚠️ Esta carpeta es una COPIA

**La versión buena de Breaking Py vive en el repo López Rocchi:**

    ~/Documentos/Proyectos_Github/Lopez-Rocchi/public_html/ProyectosJ/BreakingPy/

Es la que se publica en https://www.lopez-rocchi.com.ar/ProyectosJ/BreakingPy/
y la que tiene el historial completo (su `CHANGELOG.md` está ahí adentro).

## Por qué este aviso

El 26/09/2026 se trabajó acá sin recordar que existía la otra copia. Se subió
por FTP la 1.5.0 hecha en esta carpeta y **pisó arreglos que sólo estaban en
López Rocchi**: la cookie de sesión propia (sin ella, entrar a Breaking Py puede
cerrarle la sesión a quien esté logueado en el panel) y Google Analytics. Se
recuperaron el mismo día en la 1.5.1 y las dos copias quedaron iguales.

## Qué hacer

- **Para cambiar Breaking Py, trabajá en López Rocchi**, no acá.
- Si igual cambiaste algo acá, antes de subir compará las dos copias:

      diff -r web/ ~/Documentos/Proyectos_Github/Lopez-Rocchi/public_html/ProyectosJ/BreakingPy/

  y pasá lo que falte al otro lado. Lo que no esté en López Rocchi se pierde
  en la próxima subida.
- Después de subir, verificá que la cookie sea `breakingpy_sess` con
  `path=/ProyectosJ/BreakingPy/`, y que ninguna página mande `PHPSESSID`.
