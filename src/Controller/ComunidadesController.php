<?php

declare(strict_types=1);

namespace Agora\Controller;

use Agora\Controller\AppController;

class ComunidadesController extends AppController
{
    // Propiedades para las tablas
    public $Comunidades;
    public $Cuentas;
    public $ComunidadesCuentasTipoRoles;
    public $TipoRoles;
    public $Redes;
    public $ComunidadesRedes;
    public $Estados;

    // Lista de tablas habilitadas
    protected $enabledTables = [
        'Comunidades',
        'Cuentas',
        'ComunidadesCuentasTipoRoles',
        'TipoRoles',
        'Redes',
        'ComunidadesRedes',
        'Estados'
    ];

    public function initialize(): void
    {
        parent::initialize();

        // Inicializar tablas necesarias
        foreach ($this->enabledTables as $table) {
            $this->{$table} = $this->fetchTable($table);
        }

        // Cargar componentes necesarios
        $this->loadComponent('Imagenes');
        $this->viewBuilder()->setLayout('Agora.backend');
    }

    /**
     * Índice - Listado de comunidades
     *
     * @return void
     */
    public function index()
    {
        // Filtrar por parámetros de búsqueda
        $filtros = $this->request->getQuery();

        $query = $this->Comunidades->find()
            ->contain(['Cuentas']);

        // Filtrar por estado de verificación
        if (isset($filtros['verificada'])) {
            $query->where(['Comunidades.verificada' => (bool)$filtros['verificada']]);
        }

        // Filtrar por estado
        if (isset($filtros['estado_id'])) {
            $query->where(['Comunidades.estado_id' => $filtros['estado_id']]);
        }

        // Búsqueda por texto
        if (!empty($filtros['q'])) {
            $query->where([
                'OR' => [
                    'Comunidades.nombre LIKE' => '%' . $filtros['q'] . '%',
                    'Comunidades.descripcion LIKE' => '%' . $filtros['q'] . '%'
                ]
            ]);
        }

        // Ordenar resultados
        $query->orderBy(['Comunidades.created' => 'DESC']);

        // Paginar resultados
        $comunidades = $this->paginate($query, ['limit' => 10]);

        // Obtener contadores para el dashboard
        $pendientesVerificacion = $this->Comunidades->find()
            ->where(['verificada' => false])
            ->count();

        $totalActivas = $this->Comunidades->find()
            ->where(['estado_id' => 13]) // Estado activa
            ->count();

        $totalInactivas = $this->Comunidades->find()
            ->where(['estado_id' => 14]) // Estado inactiva
            ->count();

        // Obtener estados disponibles (activo, inactivo, etc.)
        $estados = $this->Estados->find('list')
            ->where(['tipo' => 'comunidad'])
            ->toArray();

        $this->set(compact('comunidades', 'filtros', 'pendientesVerificacion', 'totalActivas', 'totalInactivas', 'estados'));
    }

    /**
     * Ver detalle de comunidad
     *
     * @param int|null $id ID de la comunidad
     * @return void
     */
    public function ver($id = null)
    {
        $comunidad = $this->Comunidades->get($id, [
            'contain' => [
                'Cuentas',
                'ComunidadesRedes.Redes',
                'ComunidadesCuentasTipoRoles' => [
                    'Cuentas',
                    'TipoRoles'
                ]
            ]
        ]);

        $this->set('comunidad', $comunidad);
    }

    /**
     * Verificar/Aprobar una comunidad
     *
     * @param int|null $id ID de la comunidad
     * @return \Cake\Http\Response|null
     */
    public function verificar($id = null)
    {
        $this->request->allowMethod(['post', 'put']);

        $comunidad = $this->Comunidades->get($id);
        $comunidad->verificada = true;

        if ($this->Comunidades->save($comunidad)) {
            // Registrar log de verificación
            // TODO: implementar sistema de logs

            // Enviar notificación al creador
            // TODO: implementar sistema de notificaciones

            $this->Flash->success(__('La comunidad ha sido verificada y ya está visible públicamente.'));
        } else {
            $this->Flash->error(__('Ocurrió un error al verificar la comunidad. Por favor, inténtalo nuevamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Rechazar una comunidad
     *
     * @param int|null $id ID de la comunidad
     * @return \Cake\Http\Response|null
     */
    public function rechazar($id = null)
    {
        $this->request->allowMethod(['post', 'put']);

        if ($this->request->is(['post', 'put'])) {
            $comunidad = $this->Comunidades->get($id);
            $data = $this->request->getData();

            $comunidad->estado_id = 15; // Suspendida
            $motivo = $data['motivo'] ?? 'No cumple con los requisitos';

            if ($this->Comunidades->save($comunidad)) {
                // Registrar log de rechazo
                // TODO: implementar sistema de logs

                // Enviar notificación al creador con el motivo
                // TODO: implementar sistema de notificaciones con motivo

                $this->Flash->success(__('La comunidad ha sido rechazada.'));
            } else {
                $this->Flash->error(__('Ocurrió un error al rechazar la comunidad. Por favor, inténtalo nuevamente.'));
            }
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Cambiar estado de una comunidad
     *
     * @param int|null $id ID de la comunidad
     * @param int|null $estado_id ID del nuevo estado
     * @return \Cake\Http\Response|null
     */
    public function cambiarEstado($id = null, $estado_id = null)
    {
        $this->request->allowMethod(['post', 'put']);

        $comunidad = $this->Comunidades->get($id);
        $comunidad->estado_id = $estado_id;

        if ($this->Comunidades->save($comunidad)) {
            // Determinar mensaje según el estado
            $mensajeEstado = 'actualizada';
            if ($estado_id == 13) $mensajeEstado = 'activada';
            if ($estado_id == 14) $mensajeEstado = 'desactivada';
            if ($estado_id == 15) $mensajeEstado = 'suspendida';
            if ($estado_id == 16) $mensajeEstado = 'cerrada permanentemente';

            $this->Flash->success(__('La comunidad ha sido {0} exitosamente.', $mensajeEstado));
        } else {
            $this->Flash->error(__('Ocurrió un error al cambiar el estado de la comunidad. Por favor, inténtalo nuevamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
