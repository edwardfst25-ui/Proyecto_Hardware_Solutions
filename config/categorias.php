<?php
/**
 * CONFIGURACIÓN DE LAS CATEGORÍAS DE COMPONENTES
 *
 * Este archivo describe las 8 tablas de componentes. El CRUD genérico
 * (ComponenteControlador + modelo Componente + Validador) lee esta descripción
 * para saber qué campos mostrar, cómo validarlos y qué columnas guardar.
 * Para agregar un campo o una categoría nueva basta con editar este archivo.
 *
 * La clave de cada categoría es el nombre real de la tabla (igual a categorias.tabla).
 *
 * Tipos de campo:
 *   texto      -> una línea de texto ('min' y 'max' = cantidad de caracteres;
 *                 'patron' = expresión regular que debe cumplir; 'mayusculas' = se
 *                 convierte a mayúsculas antes de validar)
 *   textolargo -> párrafo (columna text)
 *   entero     -> número entero entre min y max
 *   decimal    -> número con decimales entre min y max
 *   precio     -> decimal(10,2): entre 'min' y 99.999.999,99
 *   enum       -> lista cerrada; 'opciones' debe coincidir con el ENUM de MySQL
 *   sino       -> Sí / No (columna tinyint(1): se guarda 1 o 0)
 *
 * Otras claves: 'requerido' (obligatorio), 'lista' (se muestra como columna en el
 * listado), 'sufijo' (unidad que acompaña al valor), 'ayuda' (texto bajo el campo).
 */

// Listas cerradas: deben ser idénticas a los ENUM de la base de datos.
$sockets        = ['LGA1200', 'LGA1700', 'LGA1851', 'AM4', 'AM5'];
$tiposRam       = ['DDR4', 'DDR5'];
$factoresForma  = ['ATX', 'Micro-ATX', 'Mini-ITX'];
$certificaciones = ['Sin certificación', '80+ White', '80+ Bronze', '80+ Silver', '80+ Gold', '80+ Platinum', '80+ Titanium'];

// Campos que se repiten en las 8 tablas.
$nombre      = ['etiqueta' => 'Nombre', 'tipo' => 'texto', 'min' => 2, 'max' => 100, 'requerido' => true, 'lista' => true,
                'ayuda' => 'Modelo del producto. No puede repetirse dentro de la categoría.'];
$marca       = ['etiqueta' => 'Marca', 'tipo' => 'texto', 'min' => 2, 'max' => 50, 'requerido' => true, 'lista' => true];
$precio      = ['etiqueta' => 'Precio (COP)', 'tipo' => 'precio', 'min' => 1000, 'requerido' => true, 'lista' => true,
                'ayuda' => 'Precio de referencia en pesos, sin puntos. Mínimo 1.000.'];
$asin        = ['etiqueta' => 'ASIN (Amazon)', 'tipo' => 'texto', 'max' => 10, 'mayusculas' => true,
                'patron' => '/^[A-Z0-9]{10}$/', 'patron_mensaje' => 'El ASIN tiene exactamente 10 letras o números (ejemplo: B0BBJDS62N).',
                'ayuda' => 'Código del producto en Amazon: 10 letras o números. Opcional.'];
$descripcion = ['etiqueta' => 'Descripción', 'tipo' => 'textolargo', 'max' => 2000];

return [
    'cpu' => [
        'singular' => 'procesador',
        'plural'   => 'Procesadores',
        'campos'   => [
            'nombre'         => $nombre,
            'marca'          => $marca,
            'socket'         => ['etiqueta' => 'Socket', 'tipo' => 'enum', 'opciones' => $sockets, 'requerido' => true, 'lista' => true],
            'nucleos'        => ['etiqueta' => 'Núcleos', 'tipo' => 'entero', 'min' => 1, 'max' => 128, 'requerido' => true, 'lista' => true],
            'frecuencia_ghz' => ['etiqueta' => 'Frecuencia base (GHz)', 'tipo' => 'decimal', 'min' => 0.1, 'max' => 9.9, 'paso' => '0.1', 'requerido' => true],
            'consumo_w'      => ['etiqueta' => 'Consumo (W)', 'tipo' => 'entero', 'min' => 1, 'max' => 500, 'requerido' => true, 'lista' => true, 'sufijo' => 'W'],
            'graficos_integrados' => ['etiqueta' => 'Gráficos integrados', 'tipo' => 'sino', 'requerido' => true, 'lista' => true,
                                      'ayuda' => 'Sí: el equipo puede dar imagen sin tarjeta gráfica. En Intel, los modelos F y KF no los traen; en AMD AM4, solo los modelos G.'],
            'precio'         => $precio,
            'asin'           => $asin,
            'descripcion'    => $descripcion,
        ],
    ],

    'placa_madre' => [
        'singular' => 'placa madre',
        'plural'   => 'Placas madre',
        'campos'   => [
            'nombre'       => $nombre,
            'marca'        => $marca,
            'socket'       => ['etiqueta' => 'Socket', 'tipo' => 'enum', 'opciones' => $sockets, 'requerido' => true, 'lista' => true],
            'tipo_ram'     => ['etiqueta' => 'Tipo de RAM', 'tipo' => 'enum', 'opciones' => $tiposRam, 'requerido' => true, 'lista' => true],
            'factor_forma' => ['etiqueta' => 'Factor de forma', 'tipo' => 'enum', 'opciones' => $factoresForma, 'requerido' => true, 'lista' => true],
            'precio'       => $precio,
            'asin'         => $asin,
            'descripcion'  => $descripcion,
        ],
    ],

    'ram' => [
        'singular' => 'memoria RAM',
        'plural'   => 'Memorias RAM',
        'campos'   => [
            'nombre'        => $nombre,
            'marca'         => $marca,
            'tipo'          => ['etiqueta' => 'Tipo', 'tipo' => 'enum', 'opciones' => $tiposRam, 'requerido' => true, 'lista' => true],
            'capacidad_gb'  => ['etiqueta' => 'Capacidad (GB)', 'tipo' => 'entero', 'min' => 1, 'max' => 512, 'requerido' => true, 'lista' => true, 'sufijo' => 'GB',
                                'ayuda' => 'Capacidad total del kit.'],
            'velocidad_mhz' => ['etiqueta' => 'Velocidad (MHz)', 'tipo' => 'entero', 'min' => 800, 'max' => 12000, 'requerido' => true, 'lista' => true, 'sufijo' => 'MHz'],
            'precio'        => $precio,
            'asin'          => $asin,
            'descripcion'   => $descripcion,
        ],
    ],

    'gpu' => [
        'singular' => 'tarjeta gráfica',
        'plural'   => 'Tarjetas gráficas',
        'campos'   => [
            'nombre'      => $nombre,
            'marca'       => $marca,
            'memoria_gb'  => ['etiqueta' => 'Memoria (GB)', 'tipo' => 'entero', 'min' => 1, 'max' => 64, 'requerido' => true, 'lista' => true, 'sufijo' => 'GB'],
            'consumo_w'   => ['etiqueta' => 'Consumo (W)', 'tipo' => 'entero', 'min' => 1, 'max' => 1000, 'requerido' => true, 'lista' => true, 'sufijo' => 'W'],
            'largo_mm'    => ['etiqueta' => 'Largo (mm)', 'tipo' => 'entero', 'min' => 100, 'max' => 500, 'sufijo' => 'mm',
                              'ayuda' => 'Largo de la tarjeta según el fabricante. Si se deja vacío, no se verifica si cabe en el gabinete.'],
            'precio'      => $precio,
            'asin'        => $asin,
            'descripcion' => $descripcion,
        ],
    ],

    'fuente_poder' => [
        'singular' => 'fuente de poder',
        'plural'   => 'Fuentes de poder',
        'campos'   => [
            'nombre'        => $nombre,
            'marca'         => $marca,
            'potencia_w'    => ['etiqueta' => 'Potencia (W)', 'tipo' => 'entero', 'min' => 200, 'max' => 2500, 'requerido' => true, 'lista' => true, 'sufijo' => 'W'],
            'certificacion' => ['etiqueta' => 'Certificación', 'tipo' => 'enum', 'opciones' => $certificaciones, 'requerido' => true, 'lista' => true,
                                'ayuda' => 'Si no tiene, elige «Sin certificación».'],
            'precio'        => $precio,
            'asin'          => $asin,
            'descripcion'   => $descripcion,
        ],
    ],

    'almacenamiento' => [
        'singular' => 'unidad de almacenamiento',
        'plural'   => 'Almacenamiento',
        'campos'   => [
            'nombre'       => $nombre,
            'marca'        => $marca,
            'tipo'         => ['etiqueta' => 'Tipo', 'tipo' => 'enum', 'opciones' => ['HDD', 'SSD SATA', 'SSD NVMe'], 'requerido' => true, 'lista' => true],
            'capacidad_gb' => ['etiqueta' => 'Capacidad (GB)', 'tipo' => 'entero', 'min' => 32, 'max' => 100000, 'requerido' => true, 'lista' => true, 'sufijo' => 'GB',
                               'ayuda' => '1 TB = 1000 GB.'],
            'precio'       => $precio,
            'asin'         => $asin,
            'descripcion'  => $descripcion,
        ],
    ],

    'gabinete' => [
        'singular' => 'gabinete',
        'plural'   => 'Gabinetes',
        'campos'   => [
            'nombre'                 => $nombre,
            'marca'                  => $marca,
            'factor_forma_soportado' => ['etiqueta' => 'Factor de forma máximo', 'tipo' => 'enum', 'opciones' => $factoresForma, 'requerido' => true, 'lista' => true,
                                         'ayuda' => 'El tamaño de placa más grande que cabe. Un gabinete ATX también recibe Micro-ATX y Mini-ITX.'],
            'gpu_largo_max_mm'       => ['etiqueta' => 'Largo máximo de tarjeta gráfica (mm)', 'tipo' => 'entero', 'min' => 150, 'max' => 600, 'sufijo' => 'mm',
                                         'ayuda' => 'Según el fabricante. Si se deja vacío, no se verifica el largo de la gráfica.'],
            'cooler_alto_max_mm'     => ['etiqueta' => 'Altura máxima de disipador (mm)', 'tipo' => 'entero', 'min' => 30, 'max' => 250, 'sufijo' => 'mm',
                                         'ayuda' => 'Según el fabricante. Si se deja vacío, no se verifica la altura del disipador.'],
            'precio'                 => $precio,
            'asin'                   => $asin,
            'descripcion'            => $descripcion,
        ],
    ],

    'cooler' => [
        'singular' => 'sistema de refrigeración',
        'plural'   => 'Refrigeración',
        // Esta categoría tiene además una relación muchos-a-muchos con los sockets
        // (tabla cooler_socket). El CRUD la gestiona con casillas de verificación.
        'sockets'  => $sockets,
        'campos'   => [
            'nombre'      => $nombre,
            'marca'       => $marca,
            'tipo'        => ['etiqueta' => 'Tipo', 'tipo' => 'enum', 'opciones' => ['Aire', 'Líquido (AIO)'], 'requerido' => true, 'lista' => true],
            'consumo_w'   => ['etiqueta' => 'Consumo (W)', 'tipo' => 'entero', 'min' => 0, 'max' => 100, 'lista' => true, 'sufijo' => 'W',
                              'ayuda' => 'Si lo dejas vacío, el motor de compatibilidad asume 5 W.'],
            'alto_mm'     => ['etiqueta' => 'Altura del disipador (mm)', 'tipo' => 'entero', 'min' => 20, 'max' => 200, 'sufijo' => 'mm',
                              'ayuda' => 'Solo para refrigeración por aire. En la líquida se deja vacío.'],
            'precio'      => $precio,
            'asin'        => $asin,
            'descripcion' => $descripcion,
        ],
    ],
];
