<?php

declare(strict_types=1);

namespace Agora\Controller;

use Agora\Controller\AppController;
use Cake\Event\EventInterface;

class BackendController extends AppController
{

    public $Cuentas;
    public $Partidas;
    public $Juegos;
    public $Articulos;

    public function initialize(): void
    {
        parent::initialize();

        // Modelos necesarios
        $this->Cuentas = $this->fetchTable('Cuentas');
        $this->Partidas = $this->fetchTable('Partidas');
        $this->Juegos = $this->fetchTable('Juegos');
        $this->Articulos = $this->fetchTable('Articulos');

        // Cargar componentes necesarios
        // $this->loadComponent('Paginator');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Authentication->addUnauthenticatedActions([
            'inicio',
        ]);

        $this->Authorization->skipAuthorization();
    }

    public function index()
    {
        // Obtener estadísticas básicas
        $stats = [
            'partidas_activas' => $this->Partidas->find()
                ->where(['estado_id IN' => [43, 44, 45]]) // publicada, completa, en_curso
                ->count(),

            'usuarios_nuevos' => $this->Cuentas->find()
                ->where(['created >=' => date('Y-m-d', strtotime('-30 days'))])
                ->count(),

            'articulos_pendientes' => $this->Articulos->find()
                ->where(['estado_id' => 8]) // revision
                ->count()
        ];

        // Obtener últimas actividades
        $ultimas_partidas = $this->Partidas->find()
            ->contain(['Cuentas'])
            ->order(['Partidas.created' => 'DESC'])
            ->limit(5);

        $ultimas_cuentas = $this->Cuentas->find()
            ->order(['created' => 'DESC'])
            ->limit(5);

        $this->set(compact('stats', 'ultimas_partidas', 'ultimas_cuentas'));
    }

    public function inicio() {}

    /**
     * Gestiona la limpieza de imágenes no utilizadas
     *
     * @return \Cake\Http\Response|null
     */
    public function limpiarImagenes()
    {
        $resultado = null;

        if ($this->request->is('post')) {
            $baseDir = $this->request->getData('tipo_entidad');

            // Validar que el tipo de entidad es válido para prevenir inyecciones
            $entidadesPermitidas = [
                'partidas',
                'articulos',
                'cuentas',
                'comunidades',
                'perfiles',
                'proyectos',
                'juegos',
                'battlemaps'
            ];

            if (in_array($baseDir, $entidadesPermitidas)) {
                // Cargar componente de imágenes
                $this->loadComponent('Agora.Imagenes');

                // Ejecutar la limpieza
                $resultado = $this->Imagenes->limpiarImagenesNoUtilizadas($baseDir);

                $this->Flash->success('Se ha completado el proceso de limpieza para: ' . $baseDir);
            } else {
                $this->Flash->error('Tipo de entidad no válido');
            }
        }

        $this->set(compact('resultado'));
    }
}
