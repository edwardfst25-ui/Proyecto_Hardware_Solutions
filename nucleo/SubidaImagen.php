<?php
/**
 * Recibe la foto de un componente, la valida y la guarda en
 * galeria/componentes/{tabla}/{id}_{nombre-del-producto}.{ext}
 * (convención definida en galeria/README.md).
 * En la base de datos se guarda la ruta relativa: "cpu/12_core-i5-12600kf.webp".
 */
class SubidaImagen
{
    public const TAMANO_MAX = 2 * 1024 * 1024; // 2 MB

    /** Tipos permitidos: tipo real del archivo => extensión con la que se guarda. */
    private const TIPOS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /** Carpeta base de las imágenes de componentes en el disco. */
    public static function carpetaBase(): string
    {
        return RAIZ . '/galeria/componentes';
    }

    /** ¿El formulario trajo un archivo? (si el campo quedó vacío, no hay nada que procesar) */
    public static function hayArchivo(?array $archivo): bool
    {
        return $archivo !== null && ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * Valida y guarda la imagen.
     * @param array  $archivo elemento de $_FILES
     * @return string ruta relativa para la columna `imagen`
     * @throws RuntimeException con un mensaje claro si la imagen no sirve
     */
    public static function guardar(array $archivo, string $tabla, int $id, string $nombreProducto): string
    {
        // 1) Errores que reporta PHP al recibir el archivo.
        if ($archivo['error'] === UPLOAD_ERR_INI_SIZE || $archivo['error'] === UPLOAD_ERR_FORM_SIZE) {
            throw new RuntimeException('La imagen pesa demasiado. El máximo es 2 MB.');
        }
        if ($archivo['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($archivo['tmp_name'])) {
            throw new RuntimeException('No se pudo recibir la imagen. Inténtalo de nuevo.');
        }

        // 2) Tamaño.
        if ($archivo['size'] > self::TAMANO_MAX) {
            throw new RuntimeException('La imagen pesa demasiado. El máximo es 2 MB.');
        }

        // 3) Tipo REAL del archivo. No se confía en la extensión ni en lo que dice el
        //    navegador: getimagesize() abre el archivo y comprueba que sea una imagen.
        $info = @getimagesize($archivo['tmp_name']);
        if ($info === false || !isset(self::TIPOS[$info['mime']])) {
            throw new RuntimeException('El archivo no es una imagen válida. Usa JPG, PNG o WebP.');
        }
        $extension = self::TIPOS[$info['mime']];

        // 4) Nombre final según la convención y carpeta de destino.
        $carpeta = self::carpetaBase() . '/' . $tabla;
        if (!is_dir($carpeta) && !mkdir($carpeta, 0775, true)) {
            throw new RuntimeException('No existe la carpeta de imágenes de esta categoría.');
        }
        $rutaRelativa = $tabla . '/' . $id . '_' . self::slug($nombreProducto) . '.' . $extension;

        // 5) Mover el archivo temporal a la galería.
        if (!move_uploaded_file($archivo['tmp_name'], self::carpetaBase() . '/' . $rutaRelativa)) {
            throw new RuntimeException('No se pudo guardar la imagen en la galería.');
        }
        return $rutaRelativa;
    }

    /** Borra del disco una imagen guardada (si existe). */
    public static function borrar(?string $rutaRelativa): void
    {
        // La ruta debe tener exactamente la forma "tabla/archivo.ext": así es imposible
        // que un valor extraño en la base de datos haga borrar algo fuera de la galería.
        if ($rutaRelativa === null || !preg_match('#^[a-z_]+/[A-Za-z0-9._-]+$#', $rutaRelativa)) {
            return;
        }
        $archivo = self::carpetaBase() . '/' . $rutaRelativa;
        if (is_file($archivo)) {
            unlink($archivo);
        }
    }

    /** Convierte "Core i5-12600KF" en "core-i5-12600kf" (minúsculas, sin tildes ni espacios). */
    public static function slug(string $texto): string
    {
        $texto = mb_strtolower($texto);
        $texto = strtr($texto, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto); // todo lo demás se vuelve guion
        $texto = trim(substr($texto, 0, 60), '-');
        return $texto !== '' ? $texto : 'producto';
    }
}
