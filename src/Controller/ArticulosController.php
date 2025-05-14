<?php

declare(strict_types=1);

namespace Agora\Controller;

use Agora\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Text;
use Cake\Log\Log;

/**
 * Articulos Controller
 */
class ArticulosController extends AppController
{
    /**
     * Modelos utilizados
     */
    public $Articulos;
    public $Categorias;
    public $Etiquetas;
    public $ArticulosEtiquetas;
    public $ArticuloImagenes;
    public $ArticuloMultimedias;
    public $ArticuloDocumentos;

    /**
     * Initialize method
     */
    public function initialize(): void
    {
        parent::initialize();

        // Carga de modelos
        $this->Articulos = $this->fetchTable('Articulos');
        $this->Categorias = $this->fetchTable('Categorias');
        $this->Etiquetas = $this->fetchTable('Etiquetas');
        $this->ArticulosEtiquetas = $this->fetchTable('ArticulosEtiquetas');
        $this->ArticuloImagenes = $this->fetchTable('ArticuloImagenes');
        $this->ArticuloMultimedias = $this->fetchTable('ArticuloMultimedias');
        $this->ArticuloDocumentos = $this->fetchTable('ArticuloDocumentos');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authorization->skipAuthorization();
    }


    /**
     * Index method - Lista todos los artículos
     */
    public function index()
    {
        $query = $this->Articulos->find()
            ->contain([
                'Categorias',
                'Cuentas',
                'Etiquetas',
                'Estados' => function ($q) {
                    return $q->andWhere(['tipo' => 'articulo']);
                },
                'ArticuloImagenes' => function ($q) {
                    return $q->select(['articulo_id', 'id', 'file_name'])->andWhere(['tipo_imagen_id' => 1])->limit(1);
                }
            ])
            ->order(['Articulos.created' => 'DESC']);

        $articulos = $this->paginate($query);

        // Variables para la vista
        $this->set(compact('articulos'));
    }

    // Control de etiquetas de articulos
    // !(y podria ser utilizado en un componente posteeriormente)

    private function _procesarEtiquetas($data)
    {
        if (!empty($data['etiquetas']['_ids'])) {
            $etiquetas = [];
            foreach ($data['etiquetas']['_ids'] as $etiquetaId) {
                if (strpos($etiquetaId, 'new:') === 0) {
                    // Es una nueva etiqueta
                    $nuevaEtiqueta = $this->Articulos->Etiquetas->newEntity([
                        'titulo' => substr($etiquetaId, 4),
                        'slug' => Text::slug(strtolower(substr($etiquetaId, 4))),
                        'estado_id' => 1
                    ]);

                    if ($this->Articulos->Etiquetas->save($nuevaEtiqueta)) {
                        $etiquetas[] = $nuevaEtiqueta->id;
                    }
                } else {
                    $etiquetas[] = $etiquetaId;
                }
            }
            $data['etiquetas']['_ids'] = $etiquetas;
        }
        return $data;
    }

    /**
     * Crear method - Crea un nuevo artículo
     */
    public function crear()
    {
        $articulo = $this->Articulos->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Procesar etiquetas
            $data = $this->_procesarEtiquetas($data);

            // Asignar usuario actual y estado por defecto
            $data['cuenta_id'] = $this->Authentication->getIdentity()->id;
            $data['estado_id'] = 7; // Borrador por defecto

            // Generar slug único si no se proporcionó
            if (empty($data['slug'])) {
                $slugBase = Text::slug($data['titulo']);
                $slugBase = strtolower(substr($slugBase, 0, 191)); // Dejamos espacio para sufijo

                // Verificar si ya existe y añadir sufijo si es necesario
                $slug = $slugBase;
                $counter = 0;

                while ($this->Articulos->exists(['slug' => $slug])) {
                    $counter++;
                    $slug = $slugBase . '-' . $counter;
                }

                $data['slug'] = $slug;
            }

            // Ahora hacemos un solo patchEntity con todos los datos
            $articulo = $this->Articulos->patchEntity($articulo, $data, [
                'associated' => ['Etiquetas', 'ArticuloImagenes', 'ArticuloMultimedias', 'ArticuloDocumentos']
            ]);

            if ($this->Articulos->save($articulo)) {
                $this->Flash->success('Artículo creado correctamente.');
                return $this->redirect(['action' => 'editar', $articulo->id]);
            }

            $this->Flash->error('No se pudo guardar el artículo. Por favor, intente nuevamente.');
        }

        // Datos para los selects
        $categorias = $this->Categorias->find('treeList')
            ->where(['estado_id' => 11])
            ->order(['titulo' => 'ASC'])->all();

        $etiquetas = $this->Etiquetas->find('list')
            ->where(['estado_id' => 1])
            ->order(['titulo' => 'ASC']);

        $estados = $this->Articulos->Estados->find('list', ['keyField' => 'id', 'valueField' => 'valor'])
            ->where(['tipo' => 'articulo'])
            ->order(['valor' => 'ASC']);

        $tipo_imagenes = $this->ArticuloImagenes->TipoImagenes->find('list')
            ->order(['valor' => 'ASC']);

        $this->set(compact('articulo', 'categorias', 'etiquetas', 'estados', 'tipo_imagenes'));
    }

    /**
     * Editar method - Edita un artículo existente
     */
    public function editar($id = null)
    {
        try {
            $articulo = $this->Articulos->get($id, [
                'contain' => [
                    'Categorias',
                    'Etiquetas',
                    'ArticuloImagenes',
                    'ArticuloMultimedias' => function ($q) {
                        return $q->order(['orden' => 'ASC']);
                    },
                    'ArticuloDocumentos',
                ]
            ]);

            // Contadores
            $nro_imagenes = $nro_multimedia = $nro_documentos = 0;
            if (count($articulo->articulo_imagenes) > 0) {
                $nro_imagenes = count($articulo->articulo_imagenes);
            }

            if (count($articulo->articulo_multimedias) > 0) {
                $nro_multimedia = count($articulo->articulo_multimedias);
            }

            if (count($articulo->articulo_documentos) > 0) {
                $nro_documentos = count($articulo->articulo_documentos);
            }



            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();

                $data = $this->_procesarEtiquetas($data);
                $articulo = $this->Articulos->patchEntity($articulo, $data);

                // Actualizar slug si cambió el título
                if ($articulo->titulo !== $data['titulo']) {
                    $data['slug'] = Text::slug(strtolower($data['titulo']));
                }

                $articulo = $this->Articulos->patchEntity($articulo, $data, [
                    'associated' => ['Etiquetas', 'ArticuloImagenes', 'ArticuloMultimedias', 'ArticuloDocumentos']
                ]);

                if ($this->Articulos->save($articulo)) {
                    $this->Flash->success('Artículo actualizado correctamente.');
                    return $this->redirect(['action' => 'editar', $articulo->id]);
                }

                $this->Flash->error('No se pudo actualizar el artículo. Por favor, intente nuevamente.');
            }

            // Datos para los selects
            $categorias = $this->Categorias->find('treeList')
                ->where(['estado_id' => 11])
                ->order(['titulo' => 'ASC'])->all();

            $etiquetas = $this->Etiquetas->find('list')
                ->where(['estado_id' => 1])
                ->order(['titulo' => 'ASC']);

            $estados = $this->Articulos->Estados->find('list', ['keyField' => 'id', 'valueField' => 'valor'])
                ->where(['tipo' => 'articulo'])
                ->order(['valor' => 'ASC']);

            $tipo_imagenes = $this->ArticuloImagenes->TipoImagenes->find('list')
                ->order(['valor' => 'ASC']);


            $this->set(compact('articulo', 'categorias', 'etiquetas', 'estados', 'tipo_imagenes', 'nro_imagenes', 'nro_multimedia', 'nro_documentos'));
        } catch (RecordNotFoundException $e) {
            $this->Flash->error('Artículo no encontrado.');
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Eliminar method - Elimina un artículo
     */
    public function eliminar($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        try {
            $articulo = $this->Articulos->get($id);

            if ($this->Articulos->delete($articulo)) {
                $this->Flash->success('Artículo eliminado correctamente.');
            } else {
                $this->Flash->error('No se pudo eliminar el artículo. Por favor, intente nuevamente.');
            }
        } catch (RecordNotFoundException $e) {
            $this->Flash->error('Artículo no encontrado.');
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Métodos Ajax para gestión de contenidos relacionados
     */

    public function agregarImagen($id = null)
    {
        // 1. Saltar la autorización para evitar restricciones de acceso
        $this->Authorization->skipAuthorization();

        // 2. Cargar el componente de imágenes
        if (!isset($this->Imagenes)) {
            $this->loadComponent('Agora.Imagenes');
        }

        try {
            // 3. Verificar que el artículo existe
            if (!$this->Articulos->exists(['id' => $id])) {
                throw new \Exception('El artículo no existe');
            }

            // 4. Obtener el archivo subido
            $fileData = $this->request->getData('file');

            if (empty($fileData) || $fileData->getError() !== UPLOAD_ERR_OK) {
                throw new \Exception('No se recibió ningún archivo o hubo un error en la subida');
            }

            // 5. Preparar datos para el componente
            $uploadedFile = [
                'name' => $fileData->getClientFilename(),
                'type' => $fileData->getClientMediaType(),
                'tmp_name' => $fileData->getStream()->getMetadata('uri'),
                'error' => $fileData->getError(),
                'size' => $fileData->getSize(),
            ];

            // 6. Procesar la imagen con el componente
            $resultado = $this->Imagenes->guardarImagen((int)$id, $uploadedFile, 'articulos');

            if (!empty($resultado['errors'])) {
                throw new \Exception('Error al procesar la imagen: ' . implode(', ', $resultado['errors']));
            }

            // 7. Guardar en la base de datos
            $imagen = $this->ArticuloImagenes->newEntity([
                'articulo_id' => $id,
                'tipo_imagen_id' => 3,  // Por defecto es 3 (galeria)
                'file_name' => $resultado['nombre_archivo'],
                'file_path' => '/img/articulos/' . $id . '/media/' . $resultado['nombre_archivo'],
                'file_size' => $fileData->getSize(),
                'mime_type' => $fileData->getClientMediaType(),
                'title_data' => '',
                'alt_data' => '',
                'epigrafe' => ''
            ]);

            if ($this->ArticuloImagenes->save($imagen)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'message' => 'Imagen agregada correctamente',
                        'data' => [
                            'id' => $imagen->id,
                            'file_path' => $imagen->file_path
                        ]
                    ]));
            }

            // 8. Limpiar en caso de error
            $this->Imagenes->eliminarArchivoFoto($id, $resultado['nombre_archivo'], 'articulos');

            throw new \Exception('No se pudo guardar la información de la imagen en la base de datos');
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }

    public function actualizarImagen($id = null)
    {
        $this->request->allowMethod(['patch']);

        try {
            $imagen = $this->ArticuloImagenes->get($id);
            $data = $this->request->getData();

            $imagen = $this->ArticuloImagenes->patchEntity($imagen, $data);

            if ($this->ArticuloImagenes->save($imagen)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'message' => 'Imagen actualizada correctamente'
                    ]));
            }

            throw new \Exception('No se pudo actualizar la imagen');
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }

    public function eliminarImagen($id = null)
    {
        $this->request->allowMethod(['ajax', 'delete']);

        try {
            $imagen = $this->ArticuloImagenes->get($id);

            if ($this->ArticuloImagenes->delete($imagen)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'message' => 'Imagen eliminada correctamente'
                    ]));
            }

            throw new \Exception('No se pudo eliminar la imagen');
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }

    public function agregarMultimedia($id = null)
    {

        Log::error('ID: ' . $id);

        // 1. Saltar la autorización para evitar restricciones de acceso
        $this->Authorization->skipAuthorization();

        try {
            $articulo = $this->Articulos->get($id);
            $data = $this->request->getData();

            $multimedia = $this->ArticuloMultimedias->newEntity($data);
            $multimedia->articulo_id = $articulo->id;

            if ($this->ArticuloMultimedias->save($multimedia)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'message' => 'Contenido multimedia agregado correctamente',
                        'data' => $multimedia
                    ]));
            }

            throw new \Exception('No se pudo guardar el contenido multimedia');
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }

    public function eliminarMultimedia($id = null)
    {
        $this->request->allowMethod(['ajax', 'delete']);

        try {
            $multimedia = $this->ArticuloMultimedias->get($id);

            if ($this->ArticuloMultimedias->delete($multimedia)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'message' => 'Contenido multimedia eliminado correctamente'
                    ]));
            }

            throw new \Exception('No se pudo eliminar el contenido multimedia');
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }


    public function agregarDocumento($id = null)
    {
        $this->Authorization->skipAuthorization();

        try {
            // Convertir el ID a entero
            $id = (int)$id;

            if (!$this->Articulos->exists(['id' => $id])) {
                throw new \Exception('El artículo no existe');
            }

            $fileData = $this->request->getData('file');

            if (empty($fileData) || $fileData->getError() !== UPLOAD_ERR_OK) {
                throw new \Exception('No se recibió ningún archivo o hubo un error en la subida');
            }

            // Validar tipos de archivo permitidos
            $allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/zip',
                'text/plain'
            ];

            $mimeType = $fileData->getClientMediaType();
            if (!in_array($mimeType, $allowedTypes)) {
                throw new \Exception('Tipo de archivo no permitido');
            }

            // Crear directorio si no existe
            $uploadPath = WWW_ROOT . 'files' . DS . 'articulos' . DS . $id;
            if (!file_exists($uploadPath)) {
                if (!mkdir($uploadPath, 0755, true)) {
                    throw new \Exception('No se pudo crear el directorio de destino');
                }
            }

            // Generar nombre único para el archivo
            $fileName = $fileData->getClientFilename();
            $newFileName = time() . '_' . uniqid() . '_' . $fileName;
            $fullPath = $uploadPath . DS . $newFileName;

            // Mover el archivo
            try {
                // Obtener el stream del archivo temporal
                $stream = $fileData->getStream();
                $tempPath = $stream->getMetadata('uri');

                // Intentar copiar el archivo manualmente
                if (copy($tempPath, $fullPath)) {
                    // Éxito - continuar con el código
                } else {
                    throw new \Exception('No se pudo copiar el archivo. Error: ' . error_get_last()['message']);
                }
            } catch (\Exception $e) {
                throw new \Exception('Error al mover el archivo: ' . $e->getMessage());
            }

            // Path relativo para guardar en la BD
            $filePath = 'files/articulos/' . $id . '/' . $newFileName;

            // Cargar el modelo ArticuloDocumentos si no lo tienes ya
            if (!isset($this->ArticuloDocumentos)) {
                $this->ArticuloDocumentos = $this->fetchTable('ArticuloDocumentos');
            }

            // Crear la entidad
            $documento = $this->ArticuloDocumentos->newEntity([
                'articulo_id' => $id,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_size' => $fileData->getSize(),
                'mime_type' => $mimeType,
                'titulo' => pathinfo($fileName, PATHINFO_FILENAME),
                'descripcion' => '',
                'descargas' => 0
            ]);

            if ($this->ArticuloDocumentos->save($documento)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'message' => 'Documento agregado correctamente',
                        'data' => [
                            'id' => $documento->id,
                            'file_path' => $documento->file_path
                        ]
                    ]));
            }

            // Si no se pudo guardar en la BD, eliminar el archivo
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            throw new \Exception('No se pudo guardar la información del documento en la base de datos');
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }

    public function actualizarDocumento($id = null)
    {
        //$this->request->allowMethod(['patch']);
        $this->Authorization->skipAuthorization();

        try {
            $documento = $this->ArticuloDocumentos->get($id);
            $data = $this->request->getData();

            $documento = $this->ArticuloDocumentos->patchEntity($documento, $data);

            if ($this->ArticuloDocumentos->save($documento)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'message' => 'Documento actualizado correctamente'
                    ]));
            }

            throw new \Exception('No se pudo actualizar el documento');
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }

    public function descargarDocumento($id = null)
    {
        $this->Authorization->skipAuthorization();

        try {
            $documento = $this->ArticuloDocumentos->get($id);
            $filePath = WWW_ROOT . str_replace('/', DS, $documento->file_path);

            if (!file_exists($filePath)) {
                throw new \Exception('El archivo no existe');
            }

            // Incrementar contador de descargas
            $documento->descargas = $documento->descargas + 1;
            $this->ArticuloDocumentos->save($documento);

            // Preparar respuesta para descarga
            return $this->response->withFile(
                $filePath,
                ['download' => true, 'name' => $documento->file_name]
            );
        } catch (\Exception $e) {
            $this->Flash->error('Error al descargar el documento: ' . $e->getMessage());
            return $this->redirect(['action' => 'editar', $documento->articulo_id]);
        }
    }

    public function eliminarDocumento($id = null)
    {
        $this->request->allowMethod(['ajax', 'delete']);
        $this->Authorization->skipAuthorization();

        try {
            $documento = $this->ArticuloDocumentos->get($id);
            $filePath = WWW_ROOT . str_replace('/', DS, $documento->file_path);

            if ($this->ArticuloDocumentos->delete($documento)) {
                // Eliminar el archivo físico
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'message' => 'Documento eliminado correctamente'
                    ]));
            }

            throw new \Exception('No se pudo eliminar el documento');
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }

    public function listarRecursosArticulo($id = null, $tipo = 'imagenes')
    {

        //Log::error('ID: ' . $id);
        //Log::error('TIPO: ' . $tipo);

        $this->Authorization->skipAuthorization();

        /*if (!$this->request->is('ajax')) {
            throw new \Exception('Acceso no permitido');
        }*/

        try {
            // Obtener la URL base de la aplicación
            $baseUrl = $this->request->getAttributes()['webroot'];

            if ($tipo === 'imagenes') {
                $recursos = $this->ArticuloImagenes->find()
                    ->where(['articulo_id' => $id, 'tipo_imagen_id' => 2])
                    ->select(['id', 'file_path', 'file_name', 'title_data', 'alt_data', 'tipo_imagen_id'])
                    ->order(['id' => 'DESC'])
                    ->all()
                    ->map(function ($imagen) use ($baseUrl) {
                        // Convertir la ruta relativa a una URL completa
                        $imagen->file_path = $baseUrl . ltrim($imagen->file_path, '/');
                        return $imagen;
                    });

                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'imagenes' => $recursos
                    ]));
            } elseif ($tipo === 'documentos') {
                $recursos = $this->ArticuloDocumentos->find()
                    ->where(['articulo_id' => $id])
                    ->select(['id', 'file_path', 'file_name', 'titulo', 'descripcion', 'file_size', 'mime_type'])
                    ->order(['id' => 'DESC'])
                    ->all()
                    ->map(function ($documento) use ($baseUrl) {
                        // Convertir la ruta relativa a una URL completa
                        $documento->file_path = $baseUrl . ltrim($documento->file_path, '/');
                        return $documento;
                    });

                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'documentos' => $recursos
                    ]));
            }

            throw new \Exception('Tipo de recurso no válido');
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }
}
