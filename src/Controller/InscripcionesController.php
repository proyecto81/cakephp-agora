<?php

declare(strict_types=1);

namespace Agora\Controller;

class InscripcionesController extends AppController
{
    public $Inscripciones;
    public $Partidas;
    public $Cuentas;

    public function initialize(): void
    {
        parent::initialize();
        $this->Inscripciones = $this->fetchTable('Inscripciones');
        $this->Partidas = $this->fetchTable('Partidas');
        $this->Cuentas = $this->fetchTable('Cuentas');
    }

    public function index()
    {
        $query = $this->Inscripciones->find()
            ->contain(['Partidas', 'Cuentas', 'TipoInscripciones', 'Estados'])
            ->where(['Inscripciones.estado_id !=' => 39]); // No mostrar eliminados

        $inscripciones = $this->paginate($query);

        $this->set(compact('inscripciones'));
    }

    public function crear()
    {
        $inscripcion = $this->Inscripciones->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $inscripcion = $this->Inscripciones->patchEntity($inscripcion, $data);

            if ($this->Inscripciones->save($inscripcion)) {
                $this->Flash->success('Inscripción creada correctamente.');
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('Error al crear la inscripción.');
        }

        $partidas = $this->Partidas->find('list', [
            'keyField' => 'id',
            'valueField' => 'titulo'
        ])->toArray();

        $cuentas = $this->Cuentas->find('list', [
            'keyField' => 'id',
            'valueField' => 'nickname'
        ])->toArray();

        $tipoInscripciones = $this->Inscripciones->TipoInscripciones->find('list', [
            'keyField' => 'id',
            'valueField' => 'valor'
        ])->toArray();

        $estados = $this->Inscripciones->Estados->find('list', [
            'conditions' => ['tipo' => 'inscripcion'],
            'keyField' => 'id',
            'valueField' => 'valor'
        ])->toArray();

        $this->set(compact('inscripcion', 'partidas', 'cuentas', 'tipoInscripciones', 'estados'));
    }

    public function editar($id = null)
    {
        $inscripcion = $this->Inscripciones->get($id, [
            'contain' => ['Partidas', 'Cuentas', 'TipoInscripciones']
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $inscripcion = $this->Inscripciones->patchEntity($inscripcion, $data);

            if ($this->Inscripciones->save($inscripcion)) {
                $this->Flash->success('Inscripción actualizada correctamente.');
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('Error al actualizar la inscripción.');
        }

        $partidas = $this->Partidas->find('list', [
            'keyField' => 'id',
            'valueField' => 'titulo'
        ])->toArray();

        $cuentas = $this->Cuentas->find('list', [
            'keyField' => 'id',
            'valueField' => 'nickname'
        ])->toArray();

        $tipoInscripciones = $this->Inscripciones->TipoInscripciones->find('list', [
            'keyField' => 'id',
            'valueField' => 'valor'
        ])->toArray();

        $estados = $this->Inscripciones->Estados->find('list', [
            'conditions' => ['tipo' => 'inscripcion'],
            'keyField' => 'id',
            'valueField' => 'valor'
        ])->toArray();

        $this->set(compact('inscripcion', 'partidas', 'cuentas', 'tipoInscripciones', 'estados'));
    }

    public function eliminar($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $inscripcion = $this->Inscripciones->get($id);

        // Soft delete: cambiar estado a eliminado
        $inscripcion->estado_id = 39; // Estado eliminado

        if ($this->Inscripciones->save($inscripcion)) {
            $this->Flash->success('Inscripción eliminada correctamente.');
        } else {
            $this->Flash->error('Error al eliminar la inscripción.');
        }

        return $this->redirect(['action' => 'index']);
    }
}
