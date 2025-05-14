<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    Battlemaps
                </div>
                <h2 class="page-title">
                    Administrar Mapas
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <span class="d-none d-sm-inline">
                        <?= $this->Html->link(__('Crear Mapa'), ['action' => 'crear'], ['class' => 'btn btn-success']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <!-- Filtros de búsqueda -->
                        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'row align-items-end']) ?>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Buscar</label>
                            <?= $this->Form->control('buscar', [
                                'label' => false,
                                'class' => 'form-control',
                                'placeholder' => 'Título, slug...',
                                'value' => $this->request->getQuery('buscar')
                            ]) ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Estado</label>
                            <?= $this->Form->select(
                                'estado_id',
                                ['' => '-- Todos --'] + $estados,
                                [
                                    'class' => 'form-select',
                                    'value' => $this->request->getQuery('estado_id')
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Autor</label>
                            <?= $this->Form->select(
                                'cuenta_id',
                                ['' => '-- Todos --'] + $cuentas,
                                [
                                    'class' => 'form-select',
                                    'value' => $this->request->getQuery('cuenta_id')
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Filtrar
                                </button>
                                <?php if ($this->request->getQuery()): ?>
                                    <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> Limpiar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>

                    <div class="card-body p-0">
                        <!-- Tabla de battlemaps -->
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-striped">
                                <thead>
                                    <tr>
                                        <th class="w-1">ID</th>
                                        <th class="w-8">Imagen</th>
                                        <th>Título</th>
                                        <th>Autor</th>
                                        <th>Dimensiones</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th class="w-1">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($battlemaps) > 0): ?>
                                        <?php foreach ($battlemaps as $battlemap): ?>
                                            <tr>
                                                <td><?= $battlemap->id ?></td>
                                                <td>
                                                    <?php if ($battlemap->foto): ?>
                                                        <img src="<?= $this->Url->build('/img/battlemaps/' . $battlemap->id . '/thumb/' . $battlemap->foto) ?>"
                                                            alt="<?= h($battlemap->titulo) ?>" class="avatar" style="width: 60px; height: 60px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="avatar bg-secondary-subtle">
                                                            <i class="fas fa-map text-secondary"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= $this->Html->link(h($battlemap->titulo), ['action' => 'view', $battlemap->slug], ['class' => 'text-reset']) ?>
                                                    <div class="text-muted">
                                                        <small><?= h($battlemap->slug) ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?= h($battlemap->cuenta->nickname) ?>
                                                    <div class="text-muted">
                                                        <small><?= h($battlemap->cuenta->email) ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($battlemap->ancho && $battlemap->alto): ?>
                                                        <?= h($battlemap->ancho) ?> x <?= h($battlemap->alto) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">No definido</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $badgeClass = 'bg-secondary';
                                                    if ($battlemap->estado_id == 1) {
                                                        $badgeClass = 'bg-success';
                                                    } else if ($battlemap->estado_id == 2) {
                                                        $badgeClass = 'bg-warning';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>"><?= h($battlemap->estado->valor) ?></span>
                                                </td>
                                                <td>
                                                    <?= $battlemap->created->format('d/m/Y H:i') ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <?= $this->Html->link(
                                                            '<i class="fas fa-eye"></i>',
                                                            ['action' => 'view', $battlemap->slug],
                                                            ['class' => 'btn btn-sm btn-outline-primary', 'escape' => false, 'title' => 'Ver']
                                                        ) ?>

                                                        <?= $this->Html->link(
                                                            '<i class="fas fa-edit"></i>',
                                                            ['action' => 'editar', $battlemap->slug],
                                                            ['class' => 'btn btn-sm btn-outline-warning', 'escape' => false, 'title' => 'Editar']
                                                        ) ?>

                                                        <?= $this->Form->postLink(
                                                            '<i class="fas fa-trash"></i>',
                                                            ['action' => 'eliminar', $battlemap->id],
                                                            [
                                                                'class' => 'btn btn-sm btn-outline-danger',
                                                                'escape' => false,
                                                                'title' => 'Eliminar',
                                                                'confirm' => __('¿Estás seguro de que quieres eliminar este mapa?')
                                                            ]
                                                        ) ?>

                                                        <?php if ($battlemap->estado_id != 1): ?>
                                                            <?= $this->Form->postLink(
                                                                '<i class="fas fa-check"></i>',
                                                                ['action' => 'cambiarEstado', $battlemap->id, 1],
                                                                ['class' => 'btn btn-sm btn-outline-success', 'escape' => false, 'title' => 'Publicar']
                                                            ) ?>
                                                        <?php else: ?>
                                                            <?= $this->Form->postLink(
                                                                '<i class="fas fa-times"></i>',
                                                                ['action' => 'cambiarEstado', $battlemap->id, 2],
                                                                ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false, 'title' => 'Pasar a borrador']
                                                            ) ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="empty">
                                                    <div class="empty-icon">
                                                        <i class="fas fa-search fa-3x text-muted"></i>
                                                    </div>
                                                    <p class="empty-title">No se encontraron mapas</p>
                                                    <p class="empty-subtitle text-muted">
                                                        Intenta con otros criterios de búsqueda o crea un nuevo mapa.
                                                    </p>
                                                    <div class="empty-action">
                                                        <?= $this->Html->link('Crear nuevo mapa', ['action' => 'crear'], ['class' => 'btn btn-primary']) ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Paginación -->
                    <div class="card-footer">
                        <div class="d-flex justify-content-center">
                            <ul class="pagination">
                                <?= $this->Paginator->first('<< ' . __('Primero')) ?>
                                <?= $this->Paginator->prev('< ' . __('Anterior')) ?>
                                <?= $this->Paginator->numbers() ?>
                                <?= $this->Paginator->next(__('Siguiente') . ' >') ?>
                                <?= $this->Paginator->last(__('Último') . ' >>') ?>
                            </ul>
                        </div>
                        <p class="text-center">
                            <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de {{count}} total')) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>