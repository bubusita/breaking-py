# Breaking Py

Un programa sobre la **tabla periódica**: buscá cualquiera de los 118 elementos,
filtralos por sus características, poné a prueba lo que sabés con un quiz,
enterate de un dato curioso de cada uno y repasá los temas con la
sección de **ejercicios**.

Empezó como un programa de consola en Python, escrito por
**Julia López Rocchi** y **Joaquín Moyano**, y hoy es también un sitio web hecho
con PHP, HTML y CSS.

**Versión actual: 1.5.0**

---

## Dónde está cada cosa

| | |
|---|---|
| 📘 **[Guía completa del proyecto](web/README.md)** | Cómo está armado, cómo probarlo, cómo subirlo a un servidor y una guía de Python → PHP |
| 📝 **[CHANGELOG.md](CHANGELOG.md)** | Qué cambió en cada versión |
| 🌐 `web/` | El sitio web: esta carpeta es la que se sube al servidor |
| 🐍 [`web/python_original/`](web/python_original/) | El programa original en Python, intacto. Viaja con el sitio y se puede leer desde el navegador, en el enlace del pie de página |

---

## Probarlo en dos pasos

```bash
cd web
php -S localhost:8000
```

Y abrir **http://localhost:8000** en el navegador.

La versión de consola sigue funcionando igual que siempre:

```bash
cd web/python_original
python3 Breaking_Py.py
```

---

## Sobre el proyecto

La versión web mantiene la lógica del programa original: las mismas cuatro
opciones del menú, el mismo sistema de puntaje del quiz y la misma forma de
filtrar. Lo que cambió es el medio, y eso obligó a repensar tres cosas — que no
exista `input()`, que el programa termine en cada clic y que cualquiera pueda
mandar cualquier cosa desde un formulario. Todo eso está explicado, con
comentarios, adentro del propio código.

El código original en Python se puede leer desde el mismo sitio, en el enlace
del pie de página.
