<?php

declare(strict_types=1);

namespace Agora\Controller;

use Cake\Event\EventInterface;

use App\Controller\AppController as BaseController;

class AppController extends BaseController
{
    public function initialize(): void
    {
        parent::initialize();
        // Configuraciones para todo el plugin

        // Establecer el layout del backend / Tabler
        $this->viewBuilder()->setLayout('Agora.backend');

        // Componentes
        $this->loadComponent('Agora.Imagenes');

        // Los siguientes componentes tienen que estar cargados en el AppController principal
        // ('Flash');
        // ('Authentication.Authentication');
        // ('Authorization.Authorization');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        // Verificamos que el usuario esté logueado para el resto de acciones
        if (!$this->Authentication->getIdentity()) {
            //! Más adelante deberíamos redireccionar al usuario a donde quizo acceder luego del login

            return $this->redirect($this->request->getQuery('redirect', [
                'plugin' => null,
                'controller' => 'Cuentas',
                'action' => 'login',
                'prefix' => false
            ]));
        }

        //! En todos los controladores, por ahora utilizaremos el SKIP hasta que resolvamos las Policies dentro del plugin
        //$this->Authorization->authorize($this, 'access');
        //$this->Authorization->skipAuthorization();

        $result = $this->Authentication->getResult();
        $this->set(compact('result'));
    }

    protected function isAjax(): bool
    {
        return $this->request->is('ajax');
    }

    protected function sendJson($data): void
    {
        $this->autoRender = false;
        $this->response = $this->response->withType('application/json')
            ->withStringBody(json_encode($data));
    }
}
