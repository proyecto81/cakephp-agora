<?php

declare(strict_types=1);

namespace Agora\Controller;

use Agora\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Datasource\Exception\RecordNotFoundException;

class PartidasController extends AppController
{
    public $Juegos;
    public $Cuentas;
    public $Partidas;
    public $Inscripciones;

    public function initialize(): void
    {
        parent::initialize();
        $this->Partidas = $this->fetchTable('Partidas');
        $this->Inscripciones = $this->fetchTable('Inscripciones');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authorization->skipAuthorization();
    }


    public function index()
    {
        $query = $this->Partidas->find()
            ->contain([
                'Juegos',
                'Cuentas',
                'Languages',
                'Eventos',
                'Proyectos',
                'Comunidades',
                'TipoParticipaciones',
                'Estados',
                'Inscripciones'
            ])
            ->order(['Partidas.created' => 'DESC']);

        $partidas = $this->paginate($query);
        $this->set(compact('partidas'));
    }

    public function crear()
    {
        $partida = $this->Partidas->newEmptyEntity();
        // Cheequeo que el usuario este autorizado a crear partidas
        $this->Authorization->authorize($this);

        if ($this->request->is('post')) {
            $partida = $this->Partidas->patchEntity($partida, $this->request->getData());
            if ($this->Partidas->save($partida)) {
                $this->Flash->success('La partida ha sido creada correctamente.');
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('No se pudo crear la partida. Por favor, intente nuevamente.');
        }

        $this->loadLists();
        $this->set(compact('partida'));
    }

    protected function loadLists()
    {
        $this->set([
            'juegos' => $this->Partidas->Juegos->find('list', [
                'keyField' => 'id',
                'valueField' => 'titulo'
            ])
                ->where(['Juegos.estado_id' => 30])
                ->orderBy(['Juegos.titulo' => 'ASC']),
            'languages' => $this->Partidas->Languages->find('list', [
                'keyField' => 'id',
                'valueField' => 'language_name'
            ])
                ->where(['Languages.estado_id' => 32])
                ->orderBy(['Languages.language_name' => 'ASC']),
            'eventos' => $this->Partidas->Eventos->find('list'),
            'proyectos' => $this->Partidas->Proyectos->find('list'),
            'comunidades' => $this->Partidas->Comunidades->find('list'),
            'tipoParticipaciones' => $this->Partidas->TipoParticipaciones->find('list'),

            'tipoExperiencias' => $this->Partidas->TipoExperiencias->find('list', [
                'keyField' => 'id',
                'valueField' => 'valor'
            ])->orderBy(['TipoExperiencias.id' => 'ASC']),


            'estados' => $this->Partidas->Estados->find('list', [
                'keyField' => 'id',
                'valueField' => 'valor'
            ])
                ->where(['Estados.tipo' => 'partida'])
                ->orderBy(['Estados.id' => 'ASC']),

        ]);
    }

    public function editar($id = null)
    {
        try {
            $partida = $this->Partidas->get($id, [
                'contain' => ['Inscripciones']
            ]);
            // Cheequeo que el usuario este autorizado a modificar partidas
            $this->Authorization->authorize($partida);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error('Partida no encontrada.');
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $partida = $this->Partidas->patchEntity($partida, $this->request->getData());
            if ($this->Partidas->save($partida)) {
                $this->Flash->success('La partida ha sido actualizada.');
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('No se pudo actualizar la partida. Por favor, intente nuevamente.');
        }

        $this->loadLists();
        $this->set(compact('partida'));
    }

    public function eliminar($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        try {
            $partida = $this->Partidas->get($id);
            if ($this->Partidas->delete($partida)) {
                $this->Flash->success('La partida ha sido eliminada.');
            } else {
                $this->Flash->error('No se pudo eliminar la partida.');
            }
        } catch (RecordNotFoundException $e) {
            $this->Flash->error('Partida no encontrada.');
        }

        return $this->redirect(['action' => 'index']);
    }

    /*
        $juegos = $this->Partidas->Juegos->find('list', ['keyField' => 'id', 'valueField' => 'titulo'])
            ->where(['Juegos.titulo LIKE' => '%' . $query . '%'])
            ->orderBy(['Juegos.titulo' => 'ASC']);

            */

    public function buscarJuegos()
    {
        $this->request->allowMethod('ajax');
        $query = $this->request->getQuery('q');

        $juegos = $this->Partidas->Juegos->find()
            ->select(['id', 'titulo'])
            ->where(['titulo LIKE' => '%' . $query . '%'])
            ->limit(10)
            ->all();

        $results = [];
        foreach ($juegos as $juego) {
            $results[] = [
                'id' => $juego->id,
                'text' => $juego->titulo
            ];
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode(['results' => $results]));
    }
}
