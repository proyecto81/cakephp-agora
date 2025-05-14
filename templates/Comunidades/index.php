<div class="container-xl">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Comunidades</h3>
            <div class="card-actions">
                <?= $this->Html->link(
                    '<i class="ti ti-plus"></i> Nueva Comunidad',
                    ['action' => 'crear'],
                    ['class' => 'btn btn-primary', 'escape' => false]
                ) ?>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Creador</th>
                            <th>Estado</th>
                            <th>Verificada</th>
                            <th>Fecha Creación</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comunidades as $comunidad): ?>
                            <tr>
                                <td>
                                    <?= h($comunidad->nombre) ?>
                                    <?php if ($comunidad->descripcion): ?>
                                        <div class="text-muted"><?= $this->Text->truncate($comunidad->descripcion, 100) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= $comunidad->has('cuenta') ? h($comunidad->cuenta->nombre) : '' ?></td>
                                <td>
                                    <span class="badge <?= $comunidad->estado_id == 1 ? 'bg-green' : 'bg-red' ?>">
                                        <?= h($comunidad->estado->valor) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $comunidad->verificada ? '<span class="badge bg-azure"><i class="ti ti-check"></i> Verificada</span>' : '' ?>
                                </td>
                                <td><?= $comunidad->created->format('d/m/Y H:i') ?></td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle align-text-top" data-bs-toggle="dropdown">
                                            Acciones
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <?= $this->Html->link(
                                                '<i class="ti ti-edit"></i> Editar',
                                                ['action' => 'editar', $comunidad->id],
                                                ['class' => 'dropdown-item', 'escape' => false]
                                            ) ?>
                                            <?= $this->Form->postLink(
                                                '<i class="ti ti-trash"></i> Eliminar',
                                                ['action' => 'eliminar', $comunidad->id],
                                                [
                                                    'confirm' => '¿Está seguro de eliminar esta comunidad?',
                                                    'class' => 'dropdown-item text-danger',
                                                    'escape' => false
                                                ]
                                            ) ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-secondary">
                    <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de {{count}} total')) ?>
                </p>
                <ul class="pagination m-0 ms-auto">
                    <?= $this->Paginator->first('«', ['class' => 'page-link']) ?>
                    <?= $this->Paginator->prev('‹', ['class' => 'page-link']) ?>
                    <?= $this->Paginator->numbers(['class' => 'page-link']) ?>
                    <?= $this->Paginator->next('›', ['class' => 'page-link']) ?>
                    <?= $this->Paginator->last('»', ['class' => 'page-link']) ?>
                </ul>
            </div>
        </div>
    </div>
</div>