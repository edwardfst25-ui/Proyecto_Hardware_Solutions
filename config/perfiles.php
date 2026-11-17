<?php
/**
 * CRITERIOS DE RECOMENDACIÓN POR PERFIL DE USO (RF03)
 *
 * La clave es el id del perfil en la tabla `perfiles_uso`.
 * Cada perfil define:
 *   'pesos'     -> qué parte del presupuesto se destina a cada pieza (suman 1.00)
 *   'prioridad' -> en qué orden se mejora cada pieza cuando sobra dinero
 *   'ram_min_gb'-> memoria mínima que se intenta respetar
 *   'sin_gpu'   -> true: el armado NO lleva tarjeta gráfica y se eligen solo procesadores
 *                  con gráficos integrados (su peso de 'gpu' debe ser 0)
 *   'icono'     -> ícono de Bootstrap Icons para la tarjeta del perfil
 *   'criterio'  -> explicación que se le muestra al usuario
 *
 * Si se crea un perfil nuevo en la base de datos y no se agrega aquí,
 * se usa el perfil 'defecto' (reparto equilibrado).
 */
return [
    1 => [ // Gaming
        'icono'      => 'bi-controller',
        'criterio'   => 'Para juegos, la pieza que más influye es la tarjeta gráfica: se le destina la mayor parte del presupuesto, seguida del procesador.',
        'ram_min_gb' => 16,
        'pesos'      => ['cpu' => 0.20, 'placa_madre' => 0.11, 'ram' => 0.08, 'gpu' => 0.36, 'fuente_poder' => 0.07, 'almacenamiento' => 0.07, 'gabinete' => 0.06, 'cooler' => 0.05],
        'prioridad'  => ['gpu', 'cpu', 'ram', 'placa_madre', 'almacenamiento', 'fuente_poder', 'cooler', 'gabinete'],
    ],
    2 => [ // Edición de video
        'icono'      => 'bi-camera-reels',
        'criterio'   => 'La edición de video aprovecha muchos núcleos y mucha memoria: se prioriza el procesador y la RAM, y luego la tarjeta gráfica.',
        'ram_min_gb' => 32,
        'pesos'      => ['cpu' => 0.28, 'placa_madre' => 0.11, 'ram' => 0.14, 'gpu' => 0.22, 'fuente_poder' => 0.06, 'almacenamiento' => 0.09, 'gabinete' => 0.05, 'cooler' => 0.05],
        'prioridad'  => ['cpu', 'ram', 'gpu', 'almacenamiento', 'placa_madre', 'cooler', 'fuente_poder', 'gabinete'],
    ],
    3 => [ // Diseño gráfico
        'icono'      => 'bi-palette',
        'criterio'   => 'El diseño gráfico necesita un equilibrio entre tarjeta gráfica y procesador, con buena memoria para trabajar con archivos grandes.',
        'ram_min_gb' => 16,
        'pesos'      => ['cpu' => 0.24, 'placa_madre' => 0.11, 'ram' => 0.12, 'gpu' => 0.26, 'fuente_poder' => 0.07, 'almacenamiento' => 0.09, 'gabinete' => 0.06, 'cooler' => 0.05],
        'prioridad'  => ['gpu', 'cpu', 'ram', 'almacenamiento', 'placa_madre', 'fuente_poder', 'cooler', 'gabinete'],
    ],
    4 => [ // Ofimática / Básico
        'icono'      => 'bi-briefcase',
        'criterio'   => 'Para oficina y uso general no hace falta tarjeta gráfica: se eligen procesadores con gráficos integrados y el presupuesto se reparte entre procesador, almacenamiento y memoria.',
        'ram_min_gb' => 16,
        'sin_gpu'    => true,
        'pesos'      => ['cpu' => 0.30, 'placa_madre' => 0.16, 'ram' => 0.15, 'gpu' => 0.00, 'fuente_poder' => 0.10, 'almacenamiento' => 0.15, 'gabinete' => 0.09, 'cooler' => 0.05],
        'prioridad'  => ['cpu', 'almacenamiento', 'ram', 'placa_madre', 'fuente_poder', 'gabinete', 'cooler'],
    ],
    'defecto' => [
        'icono'      => 'bi-pc-display',
        'criterio'   => 'Reparto equilibrado del presupuesto entre todas las piezas.',
        'ram_min_gb' => 16,
        'pesos'      => ['cpu' => 0.22, 'placa_madre' => 0.12, 'ram' => 0.11, 'gpu' => 0.26, 'fuente_poder' => 0.08, 'almacenamiento' => 0.09, 'gabinete' => 0.07, 'cooler' => 0.05],
        'prioridad'  => ['cpu', 'gpu', 'ram', 'almacenamiento', 'placa_madre', 'fuente_poder', 'cooler', 'gabinete'],
    ],
];
