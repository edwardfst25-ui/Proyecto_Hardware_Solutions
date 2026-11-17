# Restricciones de los campos — Hardware Solutions

Generado el 16 de noviembre de 2026 a partir de `config/categorias.php` y de los controladores, que son la fuente de verdad. Todas las reglas se comprueban **en el servidor** (`nucleo/Validador.php`); el navegador solo las sugiere.

## Reglas generales

- Los espacios al inicio y al final de cada dato se eliminan antes de validar.
- Un campo opcional que se deja vacío se guarda como NULL.
- El **nombre** de un componente no puede repetirse dentro de su categoría.
- Los campos de lista cerrada solo aceptan los valores indicados, que coinciden con el ENUM de MySQL.
- Los números decimales aceptan coma o punto (`3,8` o `3.8`).
- La **imagen** de un componente es opcional: JPG, PNG o WebP, máximo 2 MB, y se comprueba que el archivo sea realmente una imagen.
- Todos los formularios que modifican datos exigen el código de verificación de la sesión (CSRF) y solo se aceptan por POST.

## Componentes

### Procesadores (tabla `cpu`)

| Campo | Columna | Tipo | Obligatorio | Restricción |
|---|---|---|---|---|
| Nombre | `nombre` | Texto | Sí | De 2 a 100 caracteres; único en la categoría |
| Marca | `marca` | Texto | Sí | De 2 a 50 caracteres |
| Socket | `socket` | Lista cerrada | Sí | LGA1200, LGA1700, LGA1851, AM4, AM5 |
| Núcleos | `nucleos` | Número entero | Sí | Entre 1 y 128; sin decimales |
| Frecuencia base | `frecuencia_ghz` | Número decimal | Sí | Entre 0,1 y 9,9; máximo 1 decimal |
| Consumo | `consumo_w` | Número entero | Sí | Entre 1 y 500 W; sin decimales |
| Gráficos integrados | `graficos_integrados` | Sí / No | Sí | Sí (1) o No (0) |
| Precio | `precio` | Precio (decimal) | Sí | Entre 1.000 y 99.999.999,99 pesos |
| ASIN | `asin` | Texto | No | Exactamente 10 letras o números; se convierte a mayúsculas |
| Descripción | `descripcion` | Texto largo | No | Máximo 2000 caracteres |

### Placas madre (tabla `placa_madre`)

| Campo | Columna | Tipo | Obligatorio | Restricción |
|---|---|---|---|---|
| Nombre | `nombre` | Texto | Sí | De 2 a 100 caracteres; único en la categoría |
| Marca | `marca` | Texto | Sí | De 2 a 50 caracteres |
| Socket | `socket` | Lista cerrada | Sí | LGA1200, LGA1700, LGA1851, AM4, AM5 |
| Tipo de RAM | `tipo_ram` | Lista cerrada | Sí | DDR4, DDR5 |
| Factor de forma | `factor_forma` | Lista cerrada | Sí | ATX, Micro-ATX, Mini-ITX |
| Precio | `precio` | Precio (decimal) | Sí | Entre 1.000 y 99.999.999,99 pesos |
| ASIN | `asin` | Texto | No | Exactamente 10 letras o números; se convierte a mayúsculas |
| Descripción | `descripcion` | Texto largo | No | Máximo 2000 caracteres |

### Memorias RAM (tabla `ram`)

| Campo | Columna | Tipo | Obligatorio | Restricción |
|---|---|---|---|---|
| Nombre | `nombre` | Texto | Sí | De 2 a 100 caracteres; único en la categoría |
| Marca | `marca` | Texto | Sí | De 2 a 50 caracteres |
| Tipo | `tipo` | Lista cerrada | Sí | DDR4, DDR5 |
| Capacidad | `capacidad_gb` | Número entero | Sí | Entre 1 y 512 GB; sin decimales |
| Velocidad | `velocidad_mhz` | Número entero | Sí | Entre 800 y 12000 MHz; sin decimales |
| Precio | `precio` | Precio (decimal) | Sí | Entre 1.000 y 99.999.999,99 pesos |
| ASIN | `asin` | Texto | No | Exactamente 10 letras o números; se convierte a mayúsculas |
| Descripción | `descripcion` | Texto largo | No | Máximo 2000 caracteres |

### Tarjetas gráficas (tabla `gpu`)

| Campo | Columna | Tipo | Obligatorio | Restricción |
|---|---|---|---|---|
| Nombre | `nombre` | Texto | Sí | De 2 a 100 caracteres; único en la categoría |
| Marca | `marca` | Texto | Sí | De 2 a 50 caracteres |
| Memoria | `memoria_gb` | Número entero | Sí | Entre 1 y 64 GB; sin decimales |
| Consumo | `consumo_w` | Número entero | Sí | Entre 1 y 1000 W; sin decimales |
| Largo | `largo_mm` | Número entero | No | Entre 100 y 500 mm; sin decimales |
| Precio | `precio` | Precio (decimal) | Sí | Entre 1.000 y 99.999.999,99 pesos |
| ASIN | `asin` | Texto | No | Exactamente 10 letras o números; se convierte a mayúsculas |
| Descripción | `descripcion` | Texto largo | No | Máximo 2000 caracteres |

### Fuentes de poder (tabla `fuente_poder`)

| Campo | Columna | Tipo | Obligatorio | Restricción |
|---|---|---|---|---|
| Nombre | `nombre` | Texto | Sí | De 2 a 100 caracteres; único en la categoría |
| Marca | `marca` | Texto | Sí | De 2 a 50 caracteres |
| Potencia | `potencia_w` | Número entero | Sí | Entre 200 y 2500 W; sin decimales |
| Certificación | `certificacion` | Lista cerrada | Sí | Sin certificación, 80+ White, 80+ Bronze, 80+ Silver, 80+ Gold, 80+ Platinum, 80+ Titanium |
| Precio | `precio` | Precio (decimal) | Sí | Entre 1.000 y 99.999.999,99 pesos |
| ASIN | `asin` | Texto | No | Exactamente 10 letras o números; se convierte a mayúsculas |
| Descripción | `descripcion` | Texto largo | No | Máximo 2000 caracteres |

### Almacenamiento (tabla `almacenamiento`)

| Campo | Columna | Tipo | Obligatorio | Restricción |
|---|---|---|---|---|
| Nombre | `nombre` | Texto | Sí | De 2 a 100 caracteres; único en la categoría |
| Marca | `marca` | Texto | Sí | De 2 a 50 caracteres |
| Tipo | `tipo` | Lista cerrada | Sí | HDD, SSD SATA, SSD NVMe |
| Capacidad | `capacidad_gb` | Número entero | Sí | Entre 32 y 100000 GB; sin decimales |
| Precio | `precio` | Precio (decimal) | Sí | Entre 1.000 y 99.999.999,99 pesos |
| ASIN | `asin` | Texto | No | Exactamente 10 letras o números; se convierte a mayúsculas |
| Descripción | `descripcion` | Texto largo | No | Máximo 2000 caracteres |

### Gabinetes (tabla `gabinete`)

| Campo | Columna | Tipo | Obligatorio | Restricción |
|---|---|---|---|---|
| Nombre | `nombre` | Texto | Sí | De 2 a 100 caracteres; único en la categoría |
| Marca | `marca` | Texto | Sí | De 2 a 50 caracteres |
| Factor de forma máximo | `factor_forma_soportado` | Lista cerrada | Sí | ATX, Micro-ATX, Mini-ITX |
| Largo máximo de tarjeta gráfica | `gpu_largo_max_mm` | Número entero | No | Entre 150 y 600 mm; sin decimales |
| Altura máxima de disipador | `cooler_alto_max_mm` | Número entero | No | Entre 30 y 250 mm; sin decimales |
| Precio | `precio` | Precio (decimal) | Sí | Entre 1.000 y 99.999.999,99 pesos |
| ASIN | `asin` | Texto | No | Exactamente 10 letras o números; se convierte a mayúsculas |
| Descripción | `descripcion` | Texto largo | No | Máximo 2000 caracteres |

### Refrigeración (tabla `cooler`)

| Campo | Columna | Tipo | Obligatorio | Restricción |
|---|---|---|---|---|
| Nombre | `nombre` | Texto | Sí | De 2 a 100 caracteres; único en la categoría |
| Marca | `marca` | Texto | Sí | De 2 a 50 caracteres |
| Tipo | `tipo` | Lista cerrada | Sí | Aire, Líquido (AIO) |
| Consumo | `consumo_w` | Número entero | No | Entre 0 y 100 W; sin decimales |
| Altura del disipador | `alto_mm` | Número entero | No | Entre 20 y 200 mm; sin decimales |
| Precio | `precio` | Precio (decimal) | Sí | Entre 1.000 y 99.999.999,99 pesos |
| ASIN | `asin` | Texto | No | Exactamente 10 letras o números; se convierte a mayúsculas |
| Descripción | `descripcion` | Texto largo | No | Máximo 2000 caracteres |
| Sockets compatibles | tabla `cooler_socket` | Selección múltiple | Sí | Al menos uno de: LGA1200, LGA1700, LGA1851, AM4, AM5 |

## Administradores (tabla `administradores`)

| Campo | Columna | Tipo | Obligatorio | Restricción |
|---|---|---|---|---|
| Usuario | `usuario` | Texto | Sí | De 3 a 50 caracteres; solo letras, números, punto, guion y guion bajo; único |
| Nombre completo | `nombre_completo` | Texto | Sí | De 3 a 100 caracteres |
| Correo electrónico | `correo` | Texto | Sí al registrar | Formato de correo válido; máximo 120 caracteres; se guarda en minúsculas; único. Los administradores anteriores a este cambio pueden tenerlo vacío hasta que lo registren en «Mi cuenta» |
| Contraseña | `password_hash` | Texto | Sí | De 8 a 72 caracteres; debe escribirse dos veces; se guarda cifrada |

Para restablecer la contraseña propia se exige primero la contraseña actual; la verificación dura 10 minutos.

## Armados recomendados (tabla `armados_recomendados`)

| Campo | Columna | Obligatorio | Restricción |
|---|---|---|---|
| Perfil de uso | `perfil_id` | Sí | Debe existir en `perfiles_uso` |
| Rango de presupuesto | `rango_presupuesto` | Sí | Económico, Intermedio o Alto |
| Procesador, placa madre, memoria RAM, fuente, almacenamiento, gabinete y refrigeración | `cpu_id`, `placa_id`, `ram_id`, `fuente_id`, `almacenamiento_id`, `gabinete_id`, `cooler_id` | Sí | Cada uno debe existir en su categoría |
| Tarjeta gráfica | `gpu_id` | No | Solo puede omitirse si el procesador tiene gráficos integrados |
| Creado por | `creado_por` | Automático | Administrador con la sesión iniciada |

Un armado solo se guarda si el motor de compatibilidad no reporta ningún error.

## Formularios públicos

| Formulario | Campo | Restricción |
|---|---|---|
| Búsqueda del catálogo | Texto | Máximo 80 caracteres |
| Catálogo | Categoría y orden | Solo valores conocidos; cualquier otro se ignora |
| Comparación | Componentes | De 2 a 4, de la misma categoría |
| Recomendaciones | Perfil | Debe existir en `perfiles_uso` |
| Recomendaciones | Presupuesto | Número mayor que 0 y máximo 99.999.999; se aceptan puntos de miles |
| Armado personalizado | Componentes | Solo se aceptan identificadores que existan en el catálogo |
