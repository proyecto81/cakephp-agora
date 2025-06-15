<?php

declare(strict_types=1);

namespace Agora\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Exception\BadRequestException;
use Cake\Utility\Text;

/**
 * Archivos Component - EXCLUSIVO para archivos no-imagen
 * 
 * Maneja la gestión de documentos y archivos no-imagen de manera centralizada.
 * Las imágenes DEBEN procesarse con ImagenesComponent.
 */
class ArchivosComponent extends Component
{
    /**
     * Tipos de archivo permitidos por categoría - SIN IMÁGENES
     */
    private array $tiposPermitidos = [
        'documento' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain'
        ],
        'archivo' => [
            'application/json',
            'application/xml',
            'text/csv',
            'application/zip',
            'application/rar',
            'text/plain'
        ],
        'geoespacial' => [
            'application/json', // GeoJSON
            'text/plain',       // WKT
            'application/xml'   // KML
        ]
    ];

    /**
     * Tamaños máximos por tipo (en MB)
     */
    private array $tamañosMaximos = [
        'documento' => 10,
        'archivo' => 10,
        'geoespacial' => 5
    ];

    /**
     * Estructura de directorios por tipo - SIN imágenes
     */
    private array $estructuraDirectorios = [
        'documento' => 'files/articulos/{id}/documentos/',
        'archivo' => 'files/articulos/{id}/archivos/',
        'geoespacial' => 'files/articulos/{id}/geo/'
    ];

    /**
     * Sube un archivo y retorna información del resultado
     *
     * @param object $archivo Archivo subido (PSR-7 UploadedFile)
     * @param int $entidadId ID de la entidad padre
     * @param string $tipo Tipo de archivo ('documento', 'archivo', 'geoespacial')
     * @return array Resultado con información del archivo subido
     * @throws \Exception Si hay errores en la subida
     */
    public function subirArchivo($archivo, int $entidadId, string $tipo): array
    {
        // Validar archivo (incluye bloqueo de imágenes)
        $this->validarArchivo($archivo, $tipo);

        // Generar nombre único
        $nombreUnico = $this->generarNombreUnico($archivo->getClientFilename());

        // Crear directorio
        $rutaDirectorio = $this->crearDirectorioSubida($entidadId, $tipo);

        // Ruta completa del archivo
        $rutaCompleta = $rutaDirectorio . $nombreUnico;

        // Mover archivo
        $this->moverArchivo($archivo, $rutaCompleta);

        // Generar ruta relativa para BD
        $rutaRelativa = str_replace(WWW_ROOT, '', $rutaCompleta);
        $rutaRelativa = str_replace(DS, '/', $rutaRelativa);

        return [
            'nombre_original' => $archivo->getClientFilename(),
            'nombre_archivo' => $nombreUnico,
            'ruta_completa' => $rutaCompleta,
            'ruta_relativa' => $rutaRelativa,
            'tamaño' => $archivo->getSize(),
            'tipo_mime' => $archivo->getClientMediaType(),
            'tipo_procesado' => $tipo
        ];
    }

    /**
     * Valida un archivo según el tipo especificado
     * BLOQUEA explícitamente las imágenes
     *
     * @param object $archivo Archivo a validar
     * @param string $tipo Tipo de archivo
     * @throws \Exception Si la validación falla
     */
    public function validarArchivo($archivo, string $tipo): void
    {
        // Verificar que no hay errores en la subida
        if ($archivo->getError() !== UPLOAD_ERR_OK) {
            throw new \Exception('Error en la subida del archivo: ' . $this->obtenerMensajeError($archivo->getError()));
        }

        $tipoMime = $archivo->getClientMediaType();

        // BLOQUEAR imágenes - deben usar ImagenesComponent
        if (strpos($tipoMime, 'image/') === 0) {
            throw new \Exception('Las imágenes deben procesarse con ImagenesComponent');
        }

        // Verificar tipo MIME
        if (!in_array($tipoMime, $this->tiposPermitidos[$tipo] ?? [])) {
            throw new \Exception("Tipo de archivo no permitido para {$tipo}. Tipo recibido: {$tipoMime}");
        }

        // Verificar tamaño
        $tamañoMaximo = ($this->tamañosMaximos[$tipo] ?? 10) * 1024 * 1024; // Convertir MB a bytes
        if ($archivo->getSize() > $tamañoMaximo) {
            $maxMB = $this->tamañosMaximos[$tipo] ?? 10;
            throw new \Exception("El archivo excede el tamaño máximo permitido de {$maxMB}MB");
        }

        // Verificar que el archivo no esté vacío
        if ($archivo->getSize() === 0) {
            throw new \Exception('El archivo está vacío');
        }
    }

    /**
     * Determina automáticamente el tipo de archivo según su MIME type
     *
     * @param string $mimeType Tipo MIME del archivo
     * @return string Tipo determinado ('documento', 'archivo', 'geoespacial')
     */
    public function determinarTipoArchivo(string $mimeType): string
    {
        $mapeo = [
            'application/pdf' => 'documento',
            'application/msword' => 'documento',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'documento',
            'application/vnd.ms-excel' => 'documento',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'documento',
            'application/vnd.ms-powerpoint' => 'documento',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'documento',
            'application/json' => 'geoespacial', // Prioritario para GeoJSON
            'application/xml' => 'geoespacial',   // Prioritario para KML
            'text/csv' => 'archivo',
            'application/zip' => 'archivo',
            'application/rar' => 'archivo'
        ];

        // Para text/plain, revisar contexto (por defecto será archivo)
        if ($mimeType === 'text/plain') {
            return 'archivo'; // Podría ser WKT geoespacial, pero por defecto archivo
        }

        return $mapeo[$mimeType] ?? 'archivo';
    }

    /**
     * Procesa automáticamente un archivo detectando su tipo
     *
     * @param object $archivo Archivo subido
     * @param int $entidadId ID de la entidad
     * @return array Resultado del procesamiento
     * @throws \Exception Si es una imagen o hay errores
     */
    public function procesarArchivoAutomatico($archivo, int $entidadId): array
    {
        $mimeType = $archivo->getClientMediaType();

        // Bloqueo temprano de imágenes
        if (strpos($mimeType, 'image/') === 0) {
            throw new \Exception('Las imágenes deben procesarse con ImagenesComponent');
        }

        $tipoDetectado = $this->determinarTipoArchivo($mimeType);

        return $this->subirArchivo($archivo, $entidadId, $tipoDetectado);
    }

    /**
     * Genera un nombre único para el archivo
     *
     * @param string $nombreOriginal Nombre original del archivo
     * @return string Nombre único generado
     */
    public function generarNombreUnico(string $nombreOriginal): string
    {
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $nombreBase = pathinfo($nombreOriginal, PATHINFO_FILENAME);

        // Limpiar nombre base
        $nombreBase = Text::slug($nombreBase);

        // Generar nombre único
        return time() . '_' . uniqid() . '_' . $nombreBase . '.' . $extension;
    }

    /**
     * Crea el directorio de subida si no existe
     *
     * @param int $entidadId ID de la entidad
     * @param string $tipo Tipo de archivo
     * @return string Ruta del directorio creado
     * @throws \Exception Si no se puede crear el directorio
     */
    public function crearDirectorioSubida(int $entidadId, string $tipo): string
    {
        $plantillaRuta = $this->estructuraDirectorios[$tipo] ?? 'files/articulos/{id}/';
        $rutaRelativa = str_replace('{id}', (string)$entidadId, $plantillaRuta);
        $rutaCompleta = WWW_ROOT . str_replace('/', DS, $rutaRelativa);

        if (!file_exists($rutaCompleta)) {
            if (!mkdir($rutaCompleta, 0755, true)) {
                throw new \Exception('No se pudo crear el directorio de destino: ' . $rutaCompleta);
            }
        }

        return $rutaCompleta;
    }

    /**
     * Mueve el archivo desde temporal a destino final
     *
     * @param object $archivo Archivo subido
     * @param string $rutaDestino Ruta de destino
     * @throws \Exception Si no se puede mover el archivo
     */
    private function moverArchivo($archivo, string $rutaDestino): void
    {
        try {
            $stream = $archivo->getStream();
            $rutaTemporal = $stream->getMetadata('uri');

            if (!copy($rutaTemporal, $rutaDestino)) {
                $error = error_get_last();
                throw new \Exception('No se pudo copiar el archivo. Error: ' . ($error['message'] ?? 'Desconocido'));
            }
        } catch (\Exception $e) {
            throw new \Exception('Error al mover el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un archivo del sistema de archivos
     *
     * @param string $rutaArchivo Ruta del archivo a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function eliminarArchivo(string $rutaArchivo): bool
    {
        $rutaCompleta = WWW_ROOT . str_replace('/', DS, $rutaArchivo);

        if (file_exists($rutaCompleta)) {
            return unlink($rutaCompleta);
        }

        return true; // Si no existe, consideramos que está "eliminado"
    }

    /**
     * Obtiene el mensaje de error correspondiente al código de error de subida
     *
     * @param int $codigoError Código de error de PHP
     * @return string Mensaje descriptivo del error
     */
    private function obtenerMensajeError(int $codigoError): string
    {
        switch ($codigoError) {
            case UPLOAD_ERR_INI_SIZE:
                return 'El archivo excede el tamaño máximo permitido por el servidor';
            case UPLOAD_ERR_FORM_SIZE:
                return 'El archivo excede el tamaño máximo permitido por el formulario';
            case UPLOAD_ERR_PARTIAL:
                return 'El archivo se subió parcialmente';
            case UPLOAD_ERR_NO_FILE:
                return 'No se subió ningún archivo';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Falta el directorio temporal';
            case UPLOAD_ERR_CANT_WRITE:
                return 'No se puede escribir el archivo en el disco';
            case UPLOAD_ERR_EXTENSION:
                return 'Una extensión de PHP detuvo la subida del archivo';
            default:
                return 'Error desconocido en la subida';
        }
    }

    /**
     * Obtiene la estructura de directorios para un tipo específico
     *
     * @param string $tipo Tipo de archivo
     * @return string Plantilla de directorio
     */
    public function obtenerEstructuraDirectorio(string $tipo): string
    {
        return $this->estructuraDirectorios[$tipo] ?? 'files/articulos/{id}/';
    }

    /**
     * Obtiene los tipos MIME permitidos para un tipo de archivo
     *
     * @param string $tipo Tipo de archivo
     * @return array Array de tipos MIME permitidos
     */
    public function obtenerTiposPermitidos(string $tipo): array
    {
        return $this->tiposPermitidos[$tipo] ?? [];
    }

    /**
     * Obtiene todos los tipos de archivo soportados
     *
     * @return array Array con todos los tipos soportados
     */
    public function obtenerTiposSoportados(): array
    {
        return array_keys($this->tiposPermitidos);
    }
}
