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

        // Cargar componente de archivos
        $this->loadComponent('Agora.Archivos');
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

        $this->set(compact('articulos'));
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

            // Generar slug único
            $data['slug'] = $this->_generarSlugUnico($data['titulo']);

            $articulo = $this->Articulos->patchEntity($articulo, $data, [
                'associated' => ['Etiquetas', 'ArticuloImagenes', 'ArticuloMultimedias', 'ArticuloDocumentos']
            ]);

            if ($this->Articulos->save($articulo)) {
                $this->Flash->success('Artículo creado correctamente.');
                return $this->redirect(['action' => 'editar', $articulo->id]);
            }

            $this->Flash->error('No se pudo guardar el artículo. Por favor, intente nuevamente.');
        }

        // Contadores para artículo nuevo (siempre 0)
        $nro_imagenes = 0;
        $nro_multimedia = 0;
        $nro_documentos = 0;

        // Datos para los selects
        $this->_cargarDatosFormulario();
        $this->set(compact('articulo', 'nro_imagenes', 'nro_multimedia', 'nro_documentos'));
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
            $nro_imagenes = count($articulo->articulo_imagenes);
            $nro_multimedia = count($articulo->articulo_multimedias);
            $nro_documentos = count($articulo->articulo_documentos);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();

                // Procesar etiquetas
                $data = $this->_procesarEtiquetas($data);

                // Generar nuevo slug si cambió el título
                if (!empty($data['titulo']) && $data['titulo'] !== $articulo->titulo) {
                    $data['slug'] = $this->_generarSlugUnico($data['titulo'], $articulo->id);
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
            $this->_cargarDatosFormulario();
            $this->set(compact('articulo', 'nro_imagenes', 'nro_multimedia', 'nro_documentos'));
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
        $this->request->allowMethod(['post']);

        try {
            // Verificar que el artículo existe
            $this->Articulos->get($id);

            $fileData = $this->request->getData('file');
            if (empty($fileData)) {
                throw new \Exception('No se recibió ningún archivo');
            }

            // Procesar archivo con el componente
            $resultado = $this->Archivos->subirArchivo($fileData, (int)$id, 'imagen');

            // Guardar en la base de datos
            $imagen = $this->ArticuloImagenes->newEntity([
                'articulo_id' => $id,
                'tipo_imagen_id' => 3,  // Por defecto galería
                'file_name' => $resultado['nombre_original'],
                'file_path' => $resultado['ruta_relativa'],
                'file_size' => $resultado['tamaño'],
                'mime_type' => $resultado['tipo_mime'],
                'title_data' => '',
                'alt_data' => '',
                'epigrafe' => ''
            ]);

            if ($this->ArticuloImagenes->save($imagen)) {
                return $this->_construirRespuestaJson(true, 'Imagen agregada correctamente', [
                    'id' => $imagen->id,
                    'file_path' => $imagen->file_path
                ]);
            }

            // Limpiar archivo si falló la BD
            $this->Archivos->eliminarArchivo($resultado['ruta_relativa']);
            throw new \Exception('No se pudo guardar la información de la imagen en la base de datos');
        } catch (RecordNotFoundException $e) {
            return $this->_construirRespuestaJson(false, 'Artículo no encontrado');
        } catch (\Exception $e) {
            return $this->_construirRespuestaJson(false, $e->getMessage());
        }
    }

    public function actualizarImagen($id = null)
    {
        $this->request->allowMethod(['patch', 'put']);

        try {
            $imagen = $this->ArticuloImagenes->get($id);
            $data = $this->request->getData();

            $imagen = $this->ArticuloImagenes->patchEntity($imagen, $data);

            if ($this->ArticuloImagenes->save($imagen)) {
                return $this->_construirRespuestaJson(true, 'Imagen actualizada correctamente');
            }

            throw new \Exception('No se pudo actualizar la imagen');
        } catch (RecordNotFoundException $e) {
            return $this->_construirRespuestaJson(false, 'Imagen no encontrada');
        } catch (\Exception $e) {
            return $this->_construirRespuestaJson(false, $e->getMessage());
        }
    }

    public function eliminarImagen($id = null)
    {
        $this->request->allowMethod(['delete', 'ajax']);

        try {
            $imagen = $this->ArticuloImagenes->get($id);

            if ($this->ArticuloImagenes->delete($imagen)) {
                // Eliminar archivo físico
                $this->Archivos->eliminarArchivo($imagen->file_path);

                return $this->_construirRespuestaJson(true, 'Imagen eliminada correctamente');
            }

            throw new \Exception('No se pudo eliminar la imagen');
        } catch (RecordNotFoundException $e) {
            return $this->_construirRespuestaJson(false, 'Imagen no encontrada');
        } catch (\Exception $e) {
            return $this->_construirRespuestaJson(false, $e->getMessage());
        }
    }

    public function agregarMultimedia($id = null)
    {
        $this->request->allowMethod(['post']);

        try {
            $articulo = $this->Articulos->get($id);
            $data = $this->request->getData();

            $multimedia = $this->ArticuloMultimedias->newEntity($data);
            $multimedia->articulo_id = $articulo->id;

            if ($this->ArticuloMultimedias->save($multimedia)) {
                return $this->_construirRespuestaJson(true, 'Contenido multimedia agregado correctamente', $multimedia);
            }

            throw new \Exception('No se pudo guardar el contenido multimedia');
        } catch (RecordNotFoundException $e) {
            return $this->_construirRespuestaJson(false, 'Artículo no encontrado');
        } catch (\Exception $e) {
            return $this->_construirRespuestaJson(false, $e->getMessage());
        }
    }

    public function eliminarMultimedia($id = null)
    {
        $this->request->allowMethod(['delete', 'ajax']);

        try {
            $multimedia = $this->ArticuloMultimedias->get($id);

            if ($this->ArticuloMultimedias->delete($multimedia)) {
                return $this->_construirRespuestaJson(true, 'Contenido multimedia eliminado correctamente');
            }

            throw new \Exception('No se pudo eliminar el contenido multimedia');
        } catch (RecordNotFoundException $e) {
            return $this->_construirRespuestaJson(false, 'Contenido multimedia no encontrado');
        } catch (\Exception $e) {
            return $this->_construirRespuestaJson(false, $e->getMessage());
        }
    }

    public function agregarDocumento($id = null)
    {
        $this->request->allowMethod(['post']);

        try {
            // Verificar que el artículo existe
            $this->Articulos->get($id);

            $fileData = $this->request->getData('file');
            if (empty($fileData)) {
                throw new \Exception('No se recibió ningún archivo');
            }

            // Procesar archivo con el componente
            $resultado = $this->Archivos->subirArchivo($fileData, (int)$id, 'documento');

            // Crear la entidad
            $documento = $this->ArticuloDocumentos->newEntity([
                'articulo_id' => $id,
                'file_name' => $resultado['nombre_original'],
                'file_path' => $resultado['ruta_relativa'],
                'file_size' => $resultado['tamaño'],
                'mime_type' => $resultado['tipo_mime'],
                'titulo' => pathinfo($resultado['nombre_original'], PATHINFO_FILENAME),
                'descripcion' => '',
                'descargas' => 0
            ]);

            if ($this->ArticuloDocumentos->save($documento)) {
                return $this->_construirRespuestaJson(true, 'Documento agregado correctamente', [
                    'id' => $documento->id,
                    'file_path' => $documento->file_path
                ]);
            }

            // Limpiar archivo si falló la BD
            $this->Archivos->eliminarArchivo($resultado['ruta_relativa']);
            throw new \Exception('No se pudo guardar la información del documento en la base de datos');
        } catch (RecordNotFoundException $e) {
            return $this->_construirRespuestaJson(false, 'Artículo no encontrado');
        } catch (\Exception $e) {
            return $this->_construirRespuestaJson(false, $e->getMessage());
        }
    }

    public function actualizarDocumento($id = null)
    {
        $this->request->allowMethod(['patch', 'put']);

        try {
            $documento = $this->ArticuloDocumentos->get($id);
            $data = $this->request->getData();

            $documento = $this->ArticuloDocumentos->patchEntity($documento, $data);

            if ($this->ArticuloDocumentos->save($documento)) {
                return $this->_construirRespuestaJson(true, 'Documento actualizado correctamente');
            }

            throw new \Exception('No se pudo actualizar el documento');
        } catch (RecordNotFoundException $e) {
            return $this->_construirRespuestaJson(false, 'Documento no encontrado');
        } catch (\Exception $e) {
            return $this->_construirRespuestaJson(false, $e->getMessage());
        }
    }

    public function descargarDocumento($id = null)
    {
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
        } catch (RecordNotFoundException $e) {
            $this->Flash->error('Documento no encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Error al descargar el documento: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function eliminarDocumento($id = null)
    {
        $this->request->allowMethod(['delete', 'ajax']);

        try {
            $documento = $this->ArticuloDocumentos->get($id);

            if ($this->ArticuloDocumentos->delete($documento)) {
                // Eliminar archivo físico
                $this->Archivos->eliminarArchivo($documento->file_path);

                return $this->_construirRespuestaJson(true, 'Documento eliminado correctamente');
            }

            throw new \Exception('No se pudo eliminar el documento');
        } catch (RecordNotFoundException $e) {
            return $this->_construirRespuestaJson(false, 'Documento no encontrado');
        } catch (\Exception $e) {
            return $this->_construirRespuestaJson(false, $e->getMessage());
        }
    }

    public function listarRecursosArticulo($id = null, $tipo = 'imagenes')
    {
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
                        $imagen->file_path = $baseUrl . ltrim($imagen->file_path, '/');
                        return $imagen;
                    });

                return $this->_construirRespuestaJson(true, 'Imágenes obtenidas correctamente', [
                    'imagenes' => $recursos
                ]);
            } elseif ($tipo === 'documentos') {
                $recursos = $this->ArticuloDocumentos->find()
                    ->where(['articulo_id' => $id])
                    ->select(['id', 'file_path', 'file_name', 'titulo', 'descripcion', 'file_size', 'mime_type'])
                    ->order(['id' => 'DESC'])
                    ->all()
                    ->map(function ($documento) use ($baseUrl) {
                        $documento->file_path = $baseUrl . ltrim($documento->file_path, '/');
                        return $documento;
                    });

                return $this->_construirRespuestaJson(true, 'Documentos obtenidos correctamente', [
                    'documentos' => $recursos
                ]);
            }

            throw new \Exception('Tipo de recurso no válido');
        } catch (\Exception $e) {
            return $this->_construirRespuestaJson(false, $e->getMessage());
        }
    }

    /**
     * Métodos privados
     */

    /**
     * Genera un slug único para el artículo
     *
     * @param string $titulo Título del artículo
     * @param int|null $id ID del artículo (para excluir en ediciones)
     * @return string Slug único generado
     */
    private function _generarSlugUnico(string $titulo, ?int $id = null): string
    {
        $slugBase = Text::slug(strtolower($titulo));
        $slugBase = substr($slugBase, 0, 191); // Dejamos espacio para sufijo

        $conditions = ['slug' => $slugBase];
        if ($id !== null) {
            $conditions['id !='] = $id;
        }

        // Si no existe, usar el slug base
        if (!$this->Articulos->exists($conditions)) {
            return $slugBase;
        }

        // Si existe, buscar un sufijo único
        $counter = 1;
        do {
            $slug = $slugBase . '-' . $counter;
            $conditions['slug'] = $slug;
            $counter++;
        } while ($this->Articulos->exists($conditions));

        return $slug;
    }

    /**
     * Procesa las etiquetas, creando nuevas si es necesario
     *
     * @param array $data Datos del formulario
     * @return array Datos procesados
     */
    private function _procesarEtiquetas(array $data): array
    {
        if (!empty($data['etiquetas']['_ids'])) {
            $etiquetas = [];
            foreach ($data['etiquetas']['_ids'] as $etiquetaId) {
                if (strpos($etiquetaId, 'new:') === 0) {
                    // Es una nueva etiqueta
                    $tituloEtiqueta = substr($etiquetaId, 4);
                    $nuevaEtiqueta = $this->Etiquetas->newEntity([
                        'titulo' => $tituloEtiqueta,
                        'slug' => Text::slug(strtolower($tituloEtiqueta)),
                        'estado_id' => 28 // Estado activo para etiquetas
                    ]);

                    if ($this->Etiquetas->save($nuevaEtiqueta)) {
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
     * Construye una respuesta JSON estandarizada
     *
     * @param bool $success Indica si la operación fue exitosa
     * @param string $message Mensaje descriptivo
     * @param mixed $data Datos adicionales (opcional)
     * @param array|null $errors Errores de validación (opcional)
     * @return \Cake\Http\Response Respuesta JSON
     */
    private function _construirRespuestaJson(bool $success, string $message, $data = null, ?array $errors = null): \Cake\Http\Response
    {
        $response = [
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($response));
    }

    /**
     * Carga los datos necesarios para los formularios
     */
    private function _cargarDatosFormulario(): void
    {
        $categorias = $this->Categorias->find('treeList')
            ->where(['estado_id' => 11])
            ->order(['titulo' => 'ASC'])
            ->all();

        $etiquetas = $this->Etiquetas->find('list')
            ->where(['estado_id' => 28])
            ->order(['titulo' => 'ASC']);

        $estados = $this->Articulos->Estados->find('list', keyField: 'id', valueField: 'valor')
            ->where(['tipo' => 'articulo'])
            ->order(['valor' => 'ASC']);

        $tipo_imagenes = $this->ArticuloImagenes->TipoImagenes->find('list')
            ->order(['valor' => 'ASC']);

        $this->set(compact('categorias', 'etiquetas', 'estados', 'tipo_imagenes'));
    }
}
