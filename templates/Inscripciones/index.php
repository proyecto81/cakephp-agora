<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Inscripcion[]|\Cake\Collection\CollectionInterface $inscripciones
 */
?>

<div class="container-xl">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Inscripciones</h3>
            <div class="card-actions">
                <?= $this->Html->link(
                    '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                    Nueva Inscripción',
                    ['action' => 'crear'],
                    ['class' => 'btn btn-primary', 'escape' => false]
                ) ?>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table" id="inscripciones-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Partida</th>
                        <th>Jugador</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="w-1">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscripciones as $inscripcion): ?>
                        <tr>
                            <td><?= $this->Number->format($inscripcion->id) ?></td>
                            <td><?= h($inscripcion->partida->titulo) ?></td>
                            <td><?= h($inscripcion->cuenta->nickname) ?></td>
                            <td><?= h($inscripcion->tipo_inscripcion->valor) ?></td>
                            <td>
                                <span class="badge">
                                    <?= h($inscripcion->estado->valor) ?>
                                </span>
                            </td>
                            <td><?= h($inscripcion->created->format('d/m/Y H:i')) ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <?= $this->Html->link(
                                        '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>',
                                        ['action' => 'editar', $inscripcion->id],
                                        ['class' => 'btn btn-icon', 'escape' => false, 'title' => 'Editar']
                                    ) ?>
                                    <?= $this->Form->postLink(
                                        '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>',
                                        ['action' => 'eliminar', $inscripcion->id],
                                        ['class' => 'btn btn-icon', 'escape' => false, 'title' => 'Eliminar', 'confirm' => '¿Está seguro de eliminar esta inscripción?']
                                    ) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">Mostrando <?= $this->Paginator->counter(__('{{start}} - {{end}} de {{count}} registros')) ?></p>
            <ul class="pagination m-0 ms-auto">
                <?= $this->Paginator->first('<< ' . __('Primero')) ?>
                <?= $this->Paginator->prev('< ' . __('Anterior')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('Siguiente') . ' >') ?>
                <?= $this->Paginator->last(__('Último') . ' >>') ?>
            </ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#inscripciones-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            }
        });
    });
</script>