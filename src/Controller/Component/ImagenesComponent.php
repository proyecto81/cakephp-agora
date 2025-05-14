<?php

namespace Agora\Controller\Component;


use Cake\Controller\Component;
use InvalidArgumentException;
use Cake\Utility\Filesystem;
use Cake\I18n\Time;

class ImagenesComponent extends Component
{
    private const VALID_EXTENSIONS = [
        'jpg' => ['mimetype' => 'image/jpeg', 'function' => 'imagecreatefromjpeg'],
        'jpeg' => ['mimetype' => 'image/jpeg', 'function' => 'imagecreatefromjpeg'],
        'png' => ['mimetype' => 'image/png', 'function' => 'imagecreatefrompng'],
        'gif' => ['mimetype' => 'image/gif', 'function' => 'imagecreatefromgif'],
        'webp' => ['mimetype' => 'image/webp', 'function' => 'imagecreatefromwebp']
    ];

    private const SAVE_HANDLERS = [
        'jpg' => ['function' => 'imagejpeg'],
        'jpeg' => ['function' => 'imagejpeg'],
        'png' => ['function' => 'imagepng'],
        'gif' => ['function' => 'imagegif'],
        'webp' => ['function' => 'imagewebp']
    ];

    private const DIMENSIONES = [
        'thumb' => ['width' => 150, 'height' => 150, 'crop' => true],
        'chica' => ['width' => 300, 'height' => null, 'crop' => false],
        'media' => ['width' => 600, 'height' => null, 'crop' => false],
        'grande' => ['width' => 1200, 'height' => null, 'crop' => false],
        'banner' => ['width' => 1980, 'height' => null, 'crop' => false],
        'origen' => ['width' => null, 'height' => null, 'crop' => false]
    ];

    private $image;
    private $imageResized;
    private $width;
    private $height;
    private $file;

    public $errors = [];

    /**
     * Guarda una imagen y genera sus diferentes versiones
     * 
     * @param int $id ID del registro asociado
     * @param array $fileData Datos del archivo subido
     * @param string $baseDir Directorio base donde se guardará (ej: 'articulos', 'categorias')
     * @param array $options Opciones adicionales de procesamiento
     * @return array Resultado del procesamiento
     */
    public function guardarImagen(int $id, array $fileData, string $baseDir, array $options = []): array
    {
        // Eliminar imagen anterior si se especifica
        if (isset($options['imagen_anterior']) && !empty($options['imagen_anterior'])) {
            $this->eliminarArchivoFoto($id, $options['imagen_anterior'], $baseDir);
        }

        $extension = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
        if (!isset(self::VALID_EXTENSIONS[$extension])) {
            throw new InvalidArgumentException("Extensión de archivo no válida: {$extension}");
        }

        // Crear estructura de directorios
        $basePath = WWW_ROOT . 'img/' . $baseDir . '/' . $id . '/';
        $this->crearDirectorios($basePath);

        // Generar nombre único para el archivo
        $fileName = time() . '_' . uniqid() . '.' . $extension;

        $resultado = [
            'id' => $id,
            'nombre_archivo' => $fileName,
            'versiones' => [],
            'errors' => []
        ];

        // Guardar versión original
        $originalPath = $basePath . 'origen/' . $fileName;
        if (!move_uploaded_file($fileData['tmp_name'], $originalPath)) {
            throw new \RuntimeException('No se pudo mover el archivo subido');
        }

        // Determinar qué dimensiones procesar
        $dimensionesAProcesar = self::DIMENSIONES;
        if (isset($options['dimensiones']) && is_array($options['dimensiones']) && !empty($options['dimensiones'])) {
            // Filtrar solo las dimensiones válidas que existen en DIMENSIONES
            $dimensionesValidas = array_intersect(array_keys(self::DIMENSIONES), $options['dimensiones']);
            if (!empty($dimensionesValidas)) {
                $dimensionesAProcesar = array_intersect_key(self::DIMENSIONES, array_flip($dimensionesValidas));
            }
        }

        // Procesar cada versión
        foreach ($dimensionesAProcesar as $version => $config) {
            try {
                if ($version === 'origen') continue;

                $destPath = $basePath . $version . '/' . $fileName;
                $this->procesarVersion($originalPath, $destPath, $config);
                $resultado['versiones'][$version] = str_replace(WWW_ROOT, '/', $destPath);
            } catch (\Exception $e) {
                $resultado['errors'][$version] = $e->getMessage();
            }
        }

        $resultado['versiones']['origen'] = str_replace(WWW_ROOT, '/', $originalPath);

        // Verificar si se debe conservar la versión origen
        $conservarOrigen = isset($options['conservar_origen']) && $options['conservar_origen'] === true;
        if (!$conservarOrigen) {
            // Eliminar archivo origen después de procesar todas las versiones
            if (file_exists($originalPath)) {
                unlink($originalPath);
                // Opcionalmente, podemos marcar que el origen fue eliminado
                $resultado['origen_eliminado'] = true;
            }
        }

        return $resultado;
    }

    /**
     * Limpia imágenes huérfanas cuyos registros ya no existen en la base de datos
     * Útil para mantenimiento periódico del sistema mediante tareas programadas
     *
     * @param string $baseDir Directorio base a revisar (ej: 'articulos', 'partidas')
     * @return array Estadísticas del proceso de limpieza
     */
    public function limpiarImagenesNoUtilizadas(string $baseDir): array
    {
        $resultado = [
            'eliminadas' => 0,
            'errores' => 0,
            'detalles' => []
        ];

        $basePath = WWW_ROOT . 'img/' . $baseDir . '/';
        $directorios = glob($basePath . '*', GLOB_ONLYDIR);

        foreach ($directorios as $dir) {
            $id = basename($dir);
            $resultado['detalles'][] = "Revisando directorio: {$dir}";
            /*if (!is_numeric($id)) {
                $resultado['detalles'][] = "ID no válido: {$id}";
                continue;
            }*/

            // Verificar si el registro existe en la base de datos
            $table = ucfirst($baseDir);
            if (!$this->_registry->getController()->fetchTable($table)->exists(['id' => $id])) {
                try {
                    $filesystem = new Filesystem();

                    if ($filesystem->deleteDir($dir)) {
                        $resultado['eliminadas']++;
                        $resultado['detalles'][] = "Eliminado: {$dir}";
                    } else {
                        throw new \RuntimeException("No se pudo eliminar el directorio");
                    }
                } catch (\Exception $e) {
                    $resultado['errores']++;
                    $resultado['detalles'][] = "Error al eliminar {$dir}: " . $e->getMessage();
                }
            }
        }

        return $resultado;
    }

    /**
     * Procesa una versión específica de la imagen con dimensiones configuradas
     * 
     * @param string $sourcePath Ruta completa al archivo de origen
     * @param string $destPath Ruta donde se guardará la versión procesada
     * @param array $config Configuración de dimensiones y opciones de recorte
     * @return void
     * @throws RuntimeException Si no se puede procesar la imagen
     */
    private function procesarVersion(string $sourcePath, string $destPath, array $config): void
    {
        $image = $this->openImage($sourcePath);
        if (!$image) {
            throw new \RuntimeException('No se pudo abrir la imagen origen');
        }

        $this->width = imagesx($image);
        $this->height = imagesy($image);

        // Calcular nuevas dimensiones
        list($newWidth, $newHeight) = $this->calcularDimensiones(
            $this->width,
            $this->height,
            $config['width'],
            $config['height']
        );

        // Crear imagen redimensionada
        $resized = imagecreatetruecolor($newWidth, $newHeight);

        // Manejar transparencia si es necesario
        $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        if (in_array($extension, ['png', 'webp'])) {
            $this->configurarTransparencia($resized);
        }

        // Redimensionar
        imagecopyresampled(
            $resized,
            $image,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $this->width,
            $this->height
        );

        // Recortar si es necesario
        if ($config['crop']) {
            $resized = $this->recortarImagen($resized, $config['width'], $config['height']);
        }

        // Guardar con compresión inteligente
        $this->guardarConCompresion($resized, $destPath, $extension);

        imagedestroy($image);
        imagedestroy($resized);
    }

    /**
     * Guarda la imagen con nivel de compresión optimizado según el tipo y tamaño
     * 
     * @param resource $image Recurso de imagen GD
     * @param string $path Ruta donde guardar la imagen
     * @param string $extension Extensión del archivo (determina el formato)
     * @return void
     */
    private function guardarConCompresion($image, string $path, string $extension): void
    {
        $quality = $this->calcularCalidadOptima($extension, filesize($this->file));

        $saveFunction = self::SAVE_HANDLERS[$extension]['function'];
        if ($extension === 'png') {
            // Para PNG, la calidad va de 0 (sin compresión) a 9 (máxima compresión)
            $quality = min(9, round($quality / 11.111));
            $saveFunction($image, $path, $quality);
        } else {
            $saveFunction($image, $path, $quality);
        }
    }

    /**
     * Calcula la calidad óptima de compresión según el tamaño del archivo original
     * 
     * @param string $extension Extensión del archivo
     * @param int $fileSize Tamaño en bytes del archivo original
     * @return int Nivel de calidad (0-100 para JPG/WEBP, 0-9 para PNG)
     */
    private function calcularCalidadOptima(string $extension, int $fileSize): int
    {
        // Tamaños en bytes
        $smallFile = 500 * 1024;      // 500KB
        $mediumFile = 2 * 1024 * 1024; // 2MB

        if ($fileSize <= $smallFile) {
            return 85; // Calidad alta para archivos pequeños
        } elseif ($fileSize <= $mediumFile) {
            return 75; // Calidad media para archivos medianos
        } else {
            return 65; // Calidad más baja para archivos grandes
        }
    }

    /**
     * Crea la estructura de directorios necesaria para almacenar las versiones
     * 
     * @param string $basePath Ruta base donde crear los directorios
     * @return void
     */
    private function crearDirectorios(string $basePath): void
    {
        foreach (self::DIMENSIONES as $version => $config) {
            $path = $basePath . $version;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    /**
     * Abre una imagen desde un archivo usando la función adecuada según extensión
     * 
     * @param string $file Ruta completa al archivo
     * @return resource|false Recurso de imagen GD o false en caso de error
     */
    private function openImage(string $file)
    {
        $this->file = $file;
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (!isset(self::VALID_EXTENSIONS[$extension])) {
            return false;
        }

        $function = self::VALID_EXTENSIONS[$extension]['function'];
        return @$function($file);
    }

    /**
     * Configura la transparencia para imágenes PNG y WebP
     * 
     * @param resource $image Recurso de imagen GD pasado por referencia
     * @return void
     */
    private function configurarTransparencia(&$image): void
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), $transparent);
    }

    /**
     * Calcula las dimensiones proporcionales manteniendo la relación de aspecto
     * 
     * @param int $originalWidth Ancho original
     * @param int $originalHeight Alto original
     * @param int|null $targetWidth Ancho objetivo (null si se calcula en base al alto)
     * @param int|null $targetHeight Alto objetivo (null si se calcula en base al ancho)
     * @return array Array con [ancho, alto] calculados
     */
    private function calcularDimensiones(
        int $originalWidth,
        int $originalHeight,
        ?int $targetWidth,
        ?int $targetHeight
    ): array {
        if (!$targetWidth && !$targetHeight) {
            return [$originalWidth, $originalHeight];
        }

        $ratio = $originalWidth / $originalHeight;

        if (!$targetHeight) {
            $targetHeight = (int)($targetWidth / $ratio);
        } elseif (!$targetWidth) {
            $targetWidth = (int)($targetHeight * $ratio);
        }

        return [$targetWidth, $targetHeight];
    }


    /**
     * Recorta una imagen para ajustarla a dimensiones exactas (útil para thumbnails)
     * 
     * @param \GdImage $image Objeto de imagen GD
     * @param int $width Ancho deseado
     * @param int $height Alto deseado
     * @return \GdImage Nuevo objeto de imagen GD con la imagen recortada
     */
    private function recortarImagen($image, int $width, int $height): mixed
    {
        $currentWidth = imagesx($image);
        $currentHeight = imagesy($image);

        $cropX = max(0, ($currentWidth - $width) / 2);
        $cropY = max(0, ($currentHeight - $height) / 2);

        $cropped = imagecreatetruecolor($width, $height);

        // Mantener transparencia si existe
        $this->configurarTransparencia($cropped);

        imagecopy(
            $cropped,
            $image,
            0,
            0,
            (int)$cropX,
            (int)$cropY,
            $width,
            $height
        );

        return $cropped;
    }

    public function eliminarArchivoFoto(int $id, string $file_nombre, string $tipo): int
    {
        // Para grandes cantidad de elementos, recomendable separarlo en conjuntos de a 100
        // Por ahora deshabilitado
        //$conjunto = ceil($id / 100) * 100;
        $tipo = strtolower($tipo);

        // Tamaños posibles
        $thumb = WWW_ROOT . 'img/' . $tipo . "/" . $id . "/thumb/" . $file_nombre;
        $chica = WWW_ROOT . 'img/' . $tipo . "/" . $id . "/chica/" . $file_nombre;
        $media = WWW_ROOT . 'img/' . $tipo . "/" . $id . "/media/" . $file_nombre;
        $grande = WWW_ROOT . 'img/' . $tipo . "/" . $id . "/grande/" . $file_nombre;
        $banner = WWW_ROOT . 'img/' . $tipo . "/" . $id . "/banner/" . $file_nombre;
        $origen = WWW_ROOT . 'img/' . $tipo . "/" . $id . "/origen/" . $file_nombre;

        if (file_exists($thumb)) {
            unlink($thumb);
        }
        if (file_exists($chica)) {
            unlink($chica);
        }
        if (file_exists($media)) {
            unlink($media);
        }
        if (file_exists($grande)) {
            unlink($grande);
        }
        if (file_exists($banner)) {
            unlink($banner);
        }
        if (file_exists($origen)) {
            unlink($origen);
        }

        if (!file_exists($thumb) && !file_exists($chica) && !file_exists($media) && !file_exists($grande) && !file_exists($banner) && !file_exists($origen)) {
            return 1;
        } else {
            return 0;
        }
    }
}
