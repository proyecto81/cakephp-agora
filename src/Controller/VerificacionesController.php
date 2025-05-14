<?php

declare(strict_types=1);

namespace Agora\Controller;

use Agora\Controller\AppController;
use Cake\Event\EventInterface;

class VerificacionesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        // Modelos necesarios
        // Cargar componentes necesarios
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Authentication->addUnauthenticatedActions([]);

        $this->Authorization->skipAuthorization();
    }

    public function perfiles()
    {
        $this->paginate = [
            'limit' => 20,
            'order' => ['created' => 'DESC']
        ];

        $perfiles = $this->fetchTable('Perfiles')->find()
            ->contain(['Cuentas'])
            ->where(['Perfiles.estado_id' => 62]) // pendiente_revision
            ->all();

        $this->set(compact('perfiles'));
    }

    public function revisar($id = null)
    {
        $perfil = $this->fetchTable('Perfiles')->get($id, [
            'contain' => [
                'Cuentas',
                'PerfilesJuegos.Juegos',
                'CuentaDisponibilidades'
            ]
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $aprobado = (bool)$data['aprobar'];
            $perfil->estado_id = $aprobado ? 63 : 64;
            $perfil->motivo_rechazo = $data['motivo_rechazo'] ?? null;

            if ($this->fetchTable('Perfiles')->save($perfil)) {
                // Enviar email
                $emailService = new \App\Service\EmailService();
                $emailService->notificarVerificacionPerfil([
                    'nombre' => $perfil->cuenta->nombre,
                    'email' => $perfil->cuenta->user->email,
                    'aprobado' => $aprobado,
                    'motivo' => $perfil->motivo_rechazo
                ]);

                $this->Flash->success('Perfil actualizado y notificación enviada');
                return $this->redirect(['action' => 'perfiles']);
            }
            $this->Flash->error('No se pudo actualizar el perfil');
        }

        $this->set(compact('perfil'));
    }
}
