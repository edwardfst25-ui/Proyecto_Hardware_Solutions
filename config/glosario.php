<?php
/**
 * GLOSARIO EN LENGUAJE SENCILLO
 *
 * Explicaciones cortas para usuarios con poca experiencia en hardware
 * (el 55,6 % de los encuestados se ubicó en nivel novato o básico).
 * Se muestran en el armador, en la ficha de cada componente y en la página "Glosario".
 *
 *   'categorias' -> qué es cada pieza y en qué fijarse al elegirla
 *   'terminos'   -> significado de cada característica técnica
 *   'campos'     -> qué término explica cada columna de la base de datos
 */
return [
    'categorias' => [
        'cpu'            => 'Es el «cerebro» del computador: ejecuta los programas. Más núcleos ayudan a hacer varias tareas a la vez. Debe tener el mismo socket que la placa madre.',
        'placa_madre'    => 'Es la tarjeta donde se conectan todas las demás piezas. Define qué procesador (socket) y qué tipo de memoria RAM se pueden usar, y su tamaño debe caber en el gabinete.',
        'ram'            => 'Es la memoria de trabajo: guarda lo que está abierto en este momento. Con más GB puedes tener más programas y pestañas a la vez. Debe ser del tipo (DDR4 o DDR5) que acepta la placa madre.',
        'gpu'            => 'Es la pieza que genera la imagen. Es la más importante para juegos, diseño y edición de video. Para oficina no hace falta si el procesador trae gráficos integrados.',
        'fuente_poder'   => 'Convierte la corriente del tomacorriente en la energía que usan las piezas. Su potencia (W) debe superar con margen el consumo del equipo.',
        'almacenamiento' => 'Es donde se guardan el sistema, los programas y tus archivos. Las unidades SSD son mucho más rápidas que los discos duros (HDD).',
        'gabinete'       => 'Es la caja donde se instala todo. Debe ser del tamaño de la placa madre o más grande.',
        'cooler'         => 'Mantiene frío el procesador. Puede ser de aire (un disipador con ventilador) o líquido (AIO). Debe traer montaje para el socket del procesador.',
    ],

    'terminos' => [
        'socket'       => ['nombre' => 'Socket', 'texto' => 'Es la forma del conector donde encaja el procesador en la placa madre (por ejemplo AM5 o LGA1700). Si no coinciden, las piezas no encajan.'],
        'nucleos'      => ['nombre' => 'Núcleos', 'texto' => 'Cada núcleo es como un trabajador dentro del procesador. Más núcleos permiten hacer más tareas al mismo tiempo.'],
        'frecuencia'   => ['nombre' => 'Frecuencia (GHz)', 'texto' => 'Indica qué tan rápido trabaja cada núcleo. A mayor número, más rápido, aunque solo se puede comparar entre procesadores de la misma generación.'],
        'consumo'      => ['nombre' => 'Consumo (W)', 'texto' => 'Energía que usa la pieza funcionando a plena carga, medida en vatios. Se suma para saber qué fuente de poder se necesita.'],
        'graficos'     => ['nombre' => 'Gráficos integrados', 'texto' => 'Algunos procesadores pueden generar la imagen por sí mismos, sin tarjeta gráfica. Sirven para oficina, navegación y video, pero no para juegos exigentes.'],
        'tipo_ram'     => ['nombre' => 'DDR4 y DDR5', 'texto' => 'Son dos generaciones de memoria RAM. No son intercambiables: la ranura es distinta, así que la memoria debe ser del tipo que acepta la placa madre.'],
        'capacidad'    => ['nombre' => 'Capacidad (GB)', 'texto' => 'Cuánta información cabe. En la RAM, cuántas cosas puedes tener abiertas a la vez; en el almacenamiento, cuántos archivos y programas puedes guardar (1000 GB = 1 TB).'],
        'velocidad'    => ['nombre' => 'Velocidad (MHz)', 'texto' => 'Qué tan rápido intercambia datos la memoria RAM con el procesador. A mayor número, mejor.'],
        'factor_forma' => ['nombre' => 'Factor de forma', 'texto' => 'Es el tamaño estándar de la placa madre. De mayor a menor: ATX, Micro-ATX y Mini-ITX. Un gabinete ATX recibe los tres tamaños.'],
        'memoria_gpu'  => ['nombre' => 'Memoria de video (GB)', 'texto' => 'Memoria propia de la tarjeta gráfica. Con más GB se pueden usar mayores resoluciones y calidades de imagen.'],
        'potencia'     => ['nombre' => 'Potencia (W)', 'texto' => 'Energía máxima que puede entregar la fuente de poder. Se recomienda que supere en al menos 20 % el consumo estimado del equipo.'],
        'certificacion' => ['nombre' => 'Certificación 80+', 'texto' => 'Indica qué tan eficiente es la fuente: cuánta energía aprovecha y cuánta pierde en calor. De menor a mayor: White, Bronze, Silver, Gold, Platinum y Titanium.'],
        'tipo_disco'   => ['nombre' => 'HDD, SSD SATA y SSD NVMe', 'texto' => 'HDD es el disco duro tradicional: económico pero lento. SSD SATA es bastante más rápido. SSD NVMe es el más rápido y el recomendado para instalar el sistema.'],
        'tipo_cooler'  => ['nombre' => 'Refrigeración por aire o líquida', 'texto' => 'La de aire usa un disipador metálico con ventilador. La líquida (AIO) lleva el calor con líquido hasta un radiador; enfría más, pero cuesta más.'],
        'compatibilidad' => ['nombre' => 'Compatibilidad', 'texto' => 'Dos piezas son compatibles cuando pueden funcionar juntas. La plataforma lo revisa automáticamente y te avisa antes de que compres.'],
    ],

    // [tabla][columna] => término que la explica
    'campos' => [
        'cpu'            => ['socket' => 'socket', 'nucleos' => 'nucleos', 'frecuencia_ghz' => 'frecuencia', 'consumo_w' => 'consumo', 'graficos_integrados' => 'graficos'],
        'placa_madre'    => ['socket' => 'socket', 'tipo_ram' => 'tipo_ram', 'factor_forma' => 'factor_forma'],
        'ram'            => ['tipo' => 'tipo_ram', 'capacidad_gb' => 'capacidad', 'velocidad_mhz' => 'velocidad'],
        'gpu'            => ['memoria_gb' => 'memoria_gpu', 'consumo_w' => 'consumo'],
        'fuente_poder'   => ['potencia_w' => 'potencia', 'certificacion' => 'certificacion'],
        'almacenamiento' => ['tipo' => 'tipo_disco', 'capacidad_gb' => 'capacidad'],
        'gabinete'       => ['factor_forma_soportado' => 'factor_forma'],
        'cooler'         => ['tipo' => 'tipo_cooler', 'consumo_w' => 'consumo'],
    ],
];
