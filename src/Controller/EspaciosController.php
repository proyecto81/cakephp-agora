<?php

declare(strict_types=1);

namespace Agora\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use App\Controller\AppController as BaseController;

/**
 * Espacios Controller
 *
 * Controlador para la gestión administrativa de espacios dentro del plugin Agora
 */
class EspaciosController extends BaseController
{
    /**
     * Componentes utilizados
     */
    public $components = ['Flash', 'Paginator'];

    /**
     * Modelos a cargar
     */
    protected $enabledTables = [
        'Espacios',
        'TipoEspacios',
        'Verificaciones'
    ];

    /**
     * Inicialización
     */
    public function initialize(): void
    {
        parent::initialize();

        // Cargar modelos necesarios
        foreach ($this->enabledTables as $table) {
            $this->{$table} = $this->fetchTable($table);
        }

        // Establecer layout específico del plugin
        $this->viewBuilder()->setLayout('Agora.backend');

        // Configurar cabecera para no indexar
        $this->response = $this->response->withHeader('X-Robots-Tag', 'noindex, nofollow');
    }


    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authorization->skipAuthorization();
    }

    /**
     * Dashboard principal
     */
    public function index()
    {
        // Obtener estadísticas rápidas
        $stats = [
            'total' => $this->Espacios->find()->count(),
            'pendientes' => $this->Espacios->find()
                ->where(['estado' => 'borrador', 'eliminado' => 0])
                ->count(),
            'publicados' => $this->Espacios->find()
                ->where(['estado' => 'publicado', 'eliminado' => 0])
                ->count()
        ];

        // Últimos espacios
        $espacios = $this->Espacios->find()
            ->contain(['TipoEspacios'])
            ->orderBy(['created' => 'DESC'])
            ->limit(10)
            ->all();

        $this->set(compact('stats', 'espacios'));
    }

    /**
     * Listar espacios por estado
     *
     * @param string|null $estado Estado a filtrar
     */
    public function listar($estado = 'borrador')
    {
        // Estados válidos
        $estadosValidos = ['borrador', 'verificado', 'publicado', 'rechazado', 'eliminados', 'todos'];

        // Validar estado
        if (!in_array($estado, $estadosValidos)) {
            $estado = 'borrador';
        }

        // Construir consulta
        $query = $this->Espacios->find()
            ->contain(['TipoEspacios']);

        // Filtrar por estado
        if ($estado === 'todos') {
            $query->where(['eliminado' => 0]);
        } elseif ($estado === 'eliminados') {
            $query->where(['eliminado' => 1]);
        } else {
            $query->where(['estado' => $estado, 'eliminado' => 0]);
        }

        // Ordenar por fecha de creación (más recientes primero)
        $query->orderBy(['Espacios.created' => 'DESC']);

        // Paginar resultados
        $espacios = $this->paginate($query, ['limit' => 20]);

        // Obtener tipos de espacios para filtro
        $tipoEspacios = $this->TipoEspacios->find('list')->toArray();

        // Obtener contadores para cada estado
        $contadores = [
            'borrador' => $this->Espacios->find()->where(['estado' => 'borrador', 'eliminado' => 0])->count(),
            'verificado' => $this->Espacios->find()->where(['estado' => 'verificado', 'eliminado' => 0])->count(),
            'publicado' => $this->Espacios->find()->where(['estado' => 'publicado', 'eliminado' => 0])->count(),
            'rechazado' => $this->Espacios->find()->where(['estado' => 'rechazado', 'eliminado' => 0])->count(),
            'eliminados' => $this->Espacios->find()->where(['eliminado' => 1])->count(),
            'todos' => $this->Espacios->find()->where(['eliminado' => 0])->count()
        ];

        $this->set(compact('espacios', 'estado', 'tipoEspacios', 'contadores'));
    }

    /**
     * Ver detalles de un espacio
     *
     * @param int $id ID del espacio
     */
    public function ver($id)
    {
        $espacio = $this->Espacios->get($id, contain: ['TipoEspacios']);
        $this->set(compact('espacio'));
    }

    /**
     * Cambiar el estado de un espacio
     *
     * @param int $id ID del espacio
     * @param string $nuevoEstado Nuevo estado
     */
    public function cambiarEstado($id, $nuevoEstado)
    {
        $this->request->allowMethod(['post', 'put']);

        // Validar estado
        $estadosValidos = ['borrador', 'verificado', 'publicado', 'rechazado'];
        if (!in_array($nuevoEstado, $estadosValidos)) {
            $this->Flash->error('Estado no válido.');
            return $this->redirect($this->referer());
        }

        $espacio = $this->Espacios->get($id);
        $antiguoEstado = $espacio->estado;

        // Cambiar estado
        $espacio->estado = $nuevoEstado;

        if ($this->Espacios->save($espacio)) {
            // Si cambia a publicado, crear tokens de gestión
            if ($nuevoEstado === 'publicado' && $antiguoEstado !== 'publicado') {
                $this->_generarTokensParaEspacio($espacio);
            }

            $this->Flash->success('El estado del espacio ha sido actualizado.');
        } else {
            $this->Flash->error('No se pudo actualizar el estado. Por favor, intenta nuevamente.');
        }

        return $this->redirect($this->referer());
    }

    /**
     * Enviar espacio a la papelera (eliminación lógica)
     *
     * @param int $id ID del espacio
     */
    public function eliminar($id)
    {
        $this->request->allowMethod(['post', 'put']);

        $espacio = $this->Espacios->get($id);

        $espacio->eliminado = 1;
        $espacio->fecha_eliminacion = FrozenTime::now();
        $espacio->estado = 'borrador';

        if ($this->Espacios->save($espacio)) {
            $this->Flash->success('El espacio ha sido enviado a la papelera.');
        } else {
            $this->Flash->error('No se pudo eliminar el espacio. Por favor, intenta nuevamente.');
        }

        return $this->redirect($this->referer());
    }

    /**
     * Recuperar espacio de la papelera
     *
     * @param int $id ID del espacio
     */
    public function recuperar($id)
    {
        $this->request->allowMethod(['post', 'put']);

        $espacio = $this->Espacios->get($id);

        $espacio->eliminado = 0;
        $espacio->fecha_eliminacion = null;
        $espacio->estado = 'borrador'; // Vuelve como borrador

        if ($this->Espacios->save($espacio)) {
            $this->Flash->success('El espacio ha sido recuperado de la papelera.');
        } else {
            $this->Flash->error('No se pudo recuperar el espacio. Por favor, intenta nuevamente.');
        }

        return $this->redirect(['action' => 'listar', 'eliminados']);
    }

    /**
     * Ver mapa público de espacios
     */
    public function mapa()
    {
        // Esta acción redirige a la vista pública del mapa
        return $this->redirect(['prefix' => false, 'plugin' => null, 'controller' => 'Espacios', 'action' => 'mapa']);
    }

    /**
     * Genera tokens para un espacio (edición y eliminación)
     *
     * @param object $espacio Espacio para el cual generar tokens
     * @return bool Resultado de la operación
     */
    protected function _generarTokensParaEspacio($espacio)
    {
        // Verificar si ya tiene tokens activos
        $tokensExistentes = $this->Verificaciones->find()
            ->where([
                'espacio_id' => $espacio->id,
                'tipo IN' => ['edicion', 'eliminacion'],
                'usado' => 0,
                'fecha_vencimiento >=' => FrozenTime::now()
            ])
            ->count();

        if ($tokensExistentes > 0) {
            return true; // Ya tiene tokens, no es necesario crear nuevos
        }

        // Crear token de edición
        $fechaVencimiento = (new FrozenTime())->modify('+30 days');

        $tokenEdicion = $this->Verificaciones->newEntity([
            'espacio_id' => $espacio->id,
            'token' => $this->_generarTokenUnico(),
            'tipo' => 'edicion',
            'usado' => 0,
            'fecha_vencimiento' => $fechaVencimiento
        ]);

        $tokenEliminacion = $this->Verificaciones->newEntity([
            'espacio_id' => $espacio->id,
            'token' => $this->_generarTokenUnico(),
            'tipo' => 'eliminacion',
            'usado' => 0,
            'fecha_vencimiento' => $fechaVencimiento
        ]);

        $this->Verificaciones->saveMany([$tokenEdicion, $tokenEliminacion]);

        // Enviar email con tokens
        try {
            $mailer = new \App\Mailer\EspacioMailer();
            $mailer->confirmacionVerificacion($espacio, $tokenEdicion->token, $tokenEliminacion->token)->deliver();
            return true;
        } catch (\Exception $e) {
            // Registrar error pero continuar
            $this->log('Error al enviar email: ' . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Genera un token único
     *
     * @return string Token generado
     */
    protected function _generarTokenUnico()
    {
        return bin2hex(random_bytes(32));
    }
}
