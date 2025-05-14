<?php

declare(strict_types=1);

namespace Agora\Controller;

use App\Controller\AppController;
use Cake\Utility\Text;

/**
 * Battlemaps Controller para Administración
 *
 * @property \App\Model\Table\BattlemapsTable $Battlemaps
 */
class BattlemapsController extends AppController
{
    // Propiedades para las tablas
    public $Battlemaps;
    public $Etiquetas;
    public $Estados;
    public $Cuentas;

    // Lista de tablas habilitadas
    protected $enabledTables = [
        'Battlemaps',
        'Etiquetas',
        'Estados',
        'Cuentas',
    ];

    /**
     * Initialize method
     * 
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Inicializar tablas necesarias
        foreach ($this->enabledTables as $table) {
            $this->{$table} = $this->fetchTable($table);
        }

        // Cargar componentes necesarios
        $this->loadComponent('Imagenes');

        // Establecer layout del backend
        $this->viewBuilder()->setLayout('Agora.backend');
    }

    /**
     * Index method - Lista todos los mapas para administradores
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $query = $this->Battlemaps->find()
            ->contain(['Cuentas', 'Estados', 'Etiquetas'])
            ->order(['Battlemaps.created' => 'DESC']);

        // Filtros de búsqueda
        if ($this->request->getQuery('buscar')) {
            $buscar = $this->request->getQuery('buscar');
            $query->where(['OR' => [
                'Battlemaps.titulo LIKE' => '%' . $buscar . '%',
                'Battlemaps.slug LIKE' => '%' . $buscar . '%',
                'Cuentas.nickname LIKE' => '%' . $buscar . '%',
            ]]);
        }

        if ($this->request->getQuery('estado_id')) {
            $estado_id = $this->request->getQuery('estado_id');
            $query->where(['Battlemaps.estado_id' => $estado_id]);
        }

        if ($this->request->getQuery('cuenta_id')) {
            $cuenta_id = $this->request->getQuery('cuenta_id');
            $query->where(['Battlemaps.cuenta_id' => $cuenta_id]);
        }

        $battlemaps = $this->paginate($query);

        // Obtener lista de estados y cuentas para filtros
        $estados = $this->Estados->find('list')
            ->where(['tipo' => 'battlemap'])
            ->toArray();

        $cuentas = $this->Cuentas->find('list', [
            'keyField' => 'id',
            'valueField' => function ($cuenta) {
                return $cuenta->nickname . ' (' . $cuenta->nombre . ' ' . $cuenta->apellido . ')';
            }
        ])
            ->matching('Battlemaps')
            ->group(['Cuentas.id'])
            ->toArray();

        $this->set(compact('battlemaps', 'estados', 'cuentas'));
    }

    /**
     * View method - Ver detalle de un mapa específico
     *
     * @param string|null $slug Slug del battlemap.
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Http\Exception\NotFoundException Cuando no se encuentra el registro.
     */
    public function view($slug = null)
    {
        $battlemap = $this->Battlemaps->findBySlug($slug)
            ->contain(['Cuentas', 'Estados', 'Etiquetas', 'CuentasFavoritos'])
            ->firstOrFail();

        $this->set(compact('battlemap'));
    }

    /**
     * Crear method - Crea un nuevo mapa (admin)
     * 
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function crear()
    {
        $battlemap = $this->Battlemaps->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Estado por defecto (borrador o publicado según checkbox)
            $data['estado_id'] = (isset($data['estado']) && $data['estado'] == 'on') ? 1 : 2;

            $battlemap = $this->Battlemaps->patchEntity($battlemap, $data);

            // Generar slug si no se proporciona
            if (empty($battlemap->slug)) {
                $battlemap->slug = $this->Battlemaps->generateSlug($battlemap);
            }

            if ($this->Battlemaps->save($battlemap)) {
                $this->Flash->success('El mapa ha sido guardado.');

                // Procesar imagen si se proporciona
                if (!empty($_FILES['foto_battlemap']['tmp_name'])) {
                    $resultado = $this->Imagenes->guardarImagen($battlemap->id, $_FILES['foto_battlemap'], 'battlemaps');

                    if (empty($resultado['errors'])) {
                        // Actualizar el nombre del archivo en la entidad
                        $nombreArchivo = basename($resultado['versiones']['media']);
                        $this->Battlemaps->updateAll(
                            ['foto' => $nombreArchivo],
                            ['id' => $battlemap->id]
                        );
                    } else {
                        $this->Flash->error('Hubo un problema al guardar la imagen: ' . implode(', ', $resultado['errors']));
                    }
                }

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('No se pudo guardar el mapa. Por favor, intenta nuevamente.');
        }

        // Obtener listas para select inputs
        $cuentas = $this->Cuentas->find('list', [
            'keyField' => 'id',
            'valueField' => function ($cuenta) {
                return $cuenta->nickname . ' (' . $cuenta->nombre . ' ' . $cuenta->apellido . ')';
            }
        ])
            ->where(['verificada' => 1])
            ->order(['nickname' => 'ASC'])
            ->toArray();

        $estados = $this->Estados->find('list')
            ->where(['tipo' => 'battlemap'])
            ->toArray();

        $etiquetas = $this->Etiquetas->find('list')->toArray();

        $this->set(compact('battlemap', 'cuentas', 'estados', 'etiquetas'));
    }

    /**
     * Editar method - Edita un mapa existente (admin)
     * 
     * @param string|null $slug Slug del battlemap.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException Cuando no se encuentra el registro.
     */
    public function editar($slug = null)
    {
        $battlemap = $this->Battlemaps->findBySlug($slug)
            ->contain(['Etiquetas'])
            ->firstOrFail();

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Estado por defecto (borrador o publicado según checkbox)
            $data['estado_id'] = (isset($data['estado']) && $data['estado'] == 'on') ? 1 : 2;

            $battlemap = $this->Battlemaps->patchEntity($battlemap, $data);

            if ($this->Battlemaps->save($battlemap)) {
                $this->Flash->success('El mapa ha sido actualizado.');

                // Procesar imagen si se proporciona
                if (!empty($_FILES['foto_battlemap']['tmp_name'])) {
                    // Eliminar imagen anterior si existe
                    if (!empty($battlemap->foto)) {
                        $this->Imagenes->eliminarArchivoFoto($battlemap->id, $battlemap->foto, 'battlemaps');
                    }

                    $resultado = $this->Imagenes->guardarImagen($battlemap->id, $_FILES['foto_battlemap'], 'battlemaps');

                    if (empty($resultado['errors'])) {
                        // Actualizar el nombre del archivo en la entidad
                        $nombreArchivo = basename($resultado['versiones']['media']);
                        $this->Battlemaps->updateAll(
                            ['foto' => $nombreArchivo],
                            ['id' => $battlemap->id]
                        );
                    } else {
                        $this->Flash->error('Hubo un problema al guardar la imagen: ' . implode(', ', $resultado['errors']));
                    }
                }

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('No se pudo actualizar el mapa. Por favor, intenta nuevamente.');
        }

        // Obtener listas para select inputs
        $cuentas = $this->Cuentas->find('list', [
            'keyField' => 'id',
            'valueField' => function ($cuenta) {
                return $cuenta->nickname . ' (' . $cuenta->nombre . ' ' . $cuenta->apellido . ')';
            }
        ])
            ->where(['verificada' => 1])
            ->order(['nickname' => 'ASC'])
            ->toArray();

        $estados = $this->Estados->find('list')
            ->where(['tipo' => 'battlemap'])
            ->toArray();

        $etiquetas = $this->Etiquetas->find('list')->toArray();

        $this->set(compact('battlemap', 'cuentas', 'estados', 'etiquetas'));
    }

    /**
     * Eliminar method - Elimina un mapa (admin)
     * 
     * @param string|null $id ID del battlemap.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Http\Exception\NotFoundException Cuando no se encuentra el registro.
     */
    public function eliminar($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $battlemap = $this->Battlemaps->get($id);

        if ($this->Battlemaps->delete($battlemap)) {
            // Eliminar imágenes asociadas
            if (!empty($battlemap->foto)) {
                $this->Imagenes->eliminarArchivoFoto($battlemap->id, $battlemap->foto, 'battlemaps');
            }

            $this->Flash->success('El mapa ha sido eliminado.');
        } else {
            $this->Flash->error('No se pudo eliminar el mapa. Por favor, intenta nuevamente.');
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Cambiar estado - Cambia rápidamente el estado de un mapa (admin)
     * 
     * @param int $id ID del battlemap
     * @param int $estado_id Nuevo estado ID
     * @return \Cake\Http\Response|null Redirects to index
     */
    public function cambiarEstado($id = null, $estado_id = null)
    {
        $this->request->allowMethod(['post', 'put']);
        $battlemap = $this->Battlemaps->get($id);

        // Verificar que el estado exista
        $estadoExiste = $this->Estados->exists(['id' => $estado_id, 'tipo' => 'battlemap']);
        if (!$estadoExiste) {
            $this->Flash->error('El estado seleccionado no es válido.');
            return $this->redirect(['action' => 'index']);
        }

        $battlemap->estado_id = $estado_id;

        if ($this->Battlemaps->save($battlemap)) {
            $estadoNombre = $this->Estados->get($estado_id)->valor;
            $this->Flash->success('El estado del mapa ha sido cambiado a: ' . $estadoNombre);
        } else {
            $this->Flash->error('No se pudo cambiar el estado del mapa.');
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Eliminar imagen del mapa (admin)
     * 
     * @param string|null $id ID del battlemap.
     * @return \Cake\Http\Response|null|void Redirects back
     * @throws \Cake\Http\Exception\NotFoundException Cuando no se encuentra el registro.
     */
    public function eliminarImagen($id = null)
    {
        $battlemap = $this->Battlemaps->get($id);

        if (!empty($battlemap->foto)) {
            // Eliminar archivo físico
            $resultado = $this->Imagenes->eliminarArchivoFoto($battlemap->id, $battlemap->foto, 'battlemaps');

            if ($resultado) {
                // Actualizar la entidad
                $this->Battlemaps->updateAll(['foto' => null], ['id' => $battlemap->id]);
                $this->Flash->success('La imagen ha sido eliminada.');
            } else {
                $this->Flash->error('No se pudo eliminar la imagen.');
            }
        }

        return $this->redirect(['action' => 'editar', $battlemap->slug]);
    }
}
