# Galería — Hardware Solutions

Carpeta central de imágenes del proyecto.

## `componentes/`
Imágenes de los productos del catálogo, una subcarpeta por categoría
(el nombre coincide con la tabla de la base de datos):

`cpu/`, `placa_madre/`, `ram/`, `gpu/`, `fuente_poder/`, `almacenamiento/`, `gabinete/`, `cooler/`

Convención de nombres: `{id}_{nombre-del-producto}.webp` (o `.jpg`/`.png`).
Ejemplo: `galeria/componentes/cpu/12_core-i5-12600kf.webp`.
En la columna `imagen` de la base de datos se guarda la ruta relativa
(`cpu/12_core-i5-12600kf.webp`), no la URL completa.
Las imágenes se suben desde el panel de administración (pendiente de implementar).

## `documento/`
Todas las figuras del documento principal, nombradas `Figura_NN_Titulo`.
La numeración corresponde a la versión del documento de la sesión #4;
si se agregan figuras nuevas, la carpeta se regenera.
Las figuras 54 a 58 (estructura de carpetas y fragmentos de código) son texto
dentro del documento, por eso no aparecen aquí.

## `diagramas/`
Diagramas en resolución QHD o superior (≥ 2560 × 1440), con su archivo
fuente `.dot` (Graphviz) para poder editarlos y volver a generarlos:
`dot -Tpng -Gdpi=300 archivo.dot -o archivo.png`

- `mapa_navegacion` — Figura 10
- `modelo_entidad_relacion` — Figura 15 (generado a partir de `proyecto.sql`)
- `diagrama_clases` — Figura 47
