<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Articulo[]|\Cake\Collection\CollectionInterface $articulos
 */
?>
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Artículos</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <?= $this->Html->link(__('Nuevo Artículo'), ['action' => 'crear'], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Autor</th>
                                <th>Estado</th>
                                <th>Creado</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($articulos as $articulo): ?>
                                <tr>
                                    <td>
                                        <?= $this->Html->link(h($articulo->titulo), ['action' => 'editar', $articulo->id], ['class' => 'text-reset']) ?>
                                        <?php if ($articulo->destacado): ?>
                                            <span class="badge bg-yellow">Destacado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $articulo->has('categoria') ? h($articulo->categoria->titulo) : '' ?></td>
                                    <td><?= $articulo->has('cuenta') ? h($articulo->cuenta->nickname) : '' ?></td>
                                    <td>
                                        <?php
                                        $estadoClase = 'bg-success';
                                        if ($articulo->estado_id == 2) $estadoClase = 'bg-warning';
                                        if ($articulo->estado_id == 3) $estadoClase = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $estadoClase ?>">
                                            <?= h($articulo->estado->valor) ?>
                                        </span>
                                    </td>
                                    <td><?= h($articulo->created->format('d/m/Y H:i')) ?></td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <?= $this->Html->link(
                                                '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" /><line x1="13.5" y1="6.5" x2="17.5" y2="10.5" /></svg>',
                                                ['action' => 'editar', $articulo->id],
                                                ['escape' => false, 'class' => 'btn btn-white btn-sm']
                                            ) ?>
                                            <?= $this->Form->postLink(
                                                '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>',
                                                ['action' => 'eliminar', $articulo->id],
                                                [
                                                    'confirm' => __('¿Está seguro de eliminar este artículo?'),
                                                    'escape' => false,
                                                    'class' => 'btn btn-white btn-sm'
                                                ]
                                            ) ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-muted">Mostrando <?= $this->Paginator->counter(__('{{start}} - {{end}} de {{count}} elementos')) ?></p>
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
    </div>
</div>