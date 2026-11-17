<?php
/**
 * Valida los datos de un formulario según la definición de campos de
 * config/categorias.php. Nunca confía en el navegador: aunque el formulario
 * HTML tenga "required" o "max", todo se revisa otra vez aquí, en el servidor.
 */
class Validador
{
    /** Máximo que cabe en una columna decimal(10,2): 8 enteros + 2 decimales. */
    public const PRECIO_MAX = 99999999.99;

    /**
     * @param array $campos  definición de campos de la categoría
     * @param array $entrada datos recibidos (normalmente $_POST)
     * @return array [$datos, $errores]
     *         $datos   = valores limpios listos para guardar (null si quedó vacío)
     *         $errores = ['campo' => 'mensaje'] (vacío si todo está bien)
     */
    public static function validar(array $campos, array $entrada): array
    {
        $datos = [];
        $errores = [];

        foreach ($campos as $campo => $def) {
            $valor = $entrada[$campo] ?? '';
            $valor = is_string($valor) ? trim($valor) : '';
            if (!empty($def['mayusculas'])) {
                $valor = mb_strtoupper($valor);
            }
            $etiqueta = $def['etiqueta'];

            // 1) Campo vacío: error si es obligatorio; si no, se guarda NULL.
            if ($valor === '') {
                if (!empty($def['requerido'])) {
                    $errores[$campo] = "El campo «{$etiqueta}» es obligatorio.";
                }
                $datos[$campo] = null;
                continue;
            }

            // 2) Validación según el tipo de campo.
            switch ($def['tipo']) {
                case 'texto':
                case 'textolargo':
                    if (mb_strlen($valor) > $def['max']) {
                        $errores[$campo] = "«{$etiqueta}» no puede tener más de {$def['max']} caracteres.";
                    } elseif (isset($def['min']) && mb_strlen($valor) < $def['min']) {
                        $errores[$campo] = "«{$etiqueta}» debe tener al menos {$def['min']} caracteres.";
                    } elseif (isset($def['patron']) && !preg_match($def['patron'], $valor)) {
                        // Formato exigido por una expresión regular (por ejemplo, el ASIN).
                        $errores[$campo] = $def['patron_mensaje'] ?? "«{$etiqueta}» no tiene el formato esperado.";
                    }
                    $datos[$campo] = $valor;
                    break;

                case 'entero':
                    $numero = filter_var($valor, FILTER_VALIDATE_INT);
                    if ($numero === false) {
                        $errores[$campo] = "«{$etiqueta}» debe ser un número entero, sin puntos ni comas.";
                    } elseif ($numero < $def['min'] || $numero > $def['max']) {
                        $errores[$campo] = "«{$etiqueta}» debe estar entre {$def['min']} y {$def['max']}.";
                    }
                    $datos[$campo] = $numero === false ? $valor : $numero;
                    break;

                case 'decimal':
                case 'precio':
                    $min = $def['tipo'] === 'precio' ? ($def['min'] ?? 0) : $def['min'];
                    $max = $def['tipo'] === 'precio' ? self::PRECIO_MAX : $def['max'];
                    $normalizado = str_replace(',', '.', $valor); // acepta 3,5 y 3.5
                    if (!is_numeric($normalizado)) {
                        $errores[$campo] = "«{$etiqueta}» debe ser un número.";
                        $datos[$campo] = $valor;
                    } else {
                        $numero = (float)$normalizado;
                        // Cantidad de decimales permitida: 1 si el paso es 0.1; 2 en los demás casos.
                        $decimales = ($def['paso'] ?? '0.01') === '0.1' ? 1 : 2;
                        if (round($numero, $decimales) != $numero) {
                            $errores[$campo] = "«{$etiqueta}» admite máximo {$decimales} " . ($decimales === 1 ? 'decimal.' : 'decimales.');
                        } elseif ($numero < $min || $numero > $max) {
                            $errores[$campo] = $def['tipo'] === 'precio'
                                ? "«{$etiqueta}» debe estar entre " . number_format($min, 0, ',', '.') . ' y 99.999.999. Revisa el valor.'
                                : "«{$etiqueta}» debe estar entre {$min} y {$max}.";
                        }
                        $datos[$campo] = $numero;
                    }
                    break;

                case 'sino':
                    // Solo se aceptan '1' (Sí) o '0' (No); se guarda como número.
                    if ($valor !== '1' && $valor !== '0') {
                        $errores[$campo] = "Selecciona Sí o No en «{$etiqueta}».";
                    }
                    $datos[$campo] = $valor === '1' ? 1 : 0;
                    break;

                case 'enum':
                    // Solo se aceptan los valores de la lista cerrada (los mismos del ENUM de MySQL).
                    if (!in_array($valor, $def['opciones'], true)) {
                        $errores[$campo] = "Selecciona una opción válida en «{$etiqueta}».";
                    }
                    $datos[$campo] = $valor;
                    break;
            }
        }

        return [$datos, $errores];
    }

    /**
     * Valida una lista de casillas marcadas (los sockets de un cooler).
     * Devuelve solo los valores que existen en la lista permitida, sin repetidos.
     */
    public static function opcionesMultiples($entrada, array $permitidas): array
    {
        if (!is_array($entrada)) {
            return [];
        }
        return array_values(array_intersect($permitidas, $entrada));
    }
}
