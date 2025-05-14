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
                    Detalle de Mapa
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <?= $this->Html->link(
                        '<i class="fas fa-arrow-left"></i> Volver',
                        ['action' => 'index'],
                        ['class' => 'btn', 'escape' => false]
                    ) ?>

                    <?= $this->Html->link(
                        '<i class="fas fa-edit"></i> Editar',
                        ['action' => 'editar', $battlemap->slug],
                        ['class' => 'btn btn-warning', 'escape' => false]
                    ) ?>

                    <?= $this->Form->postLink(
                        '<i class="fas fa-trash"></i> Eliminar',
                        ['action' => 'eliminar', $battlemap->id],
                        ['class' => 'btn btn-danger', 'escape' => false, 'confirm' => __('¿Estás seguro de que quieres eliminar este mapa?')]
                    ) ?>

                    <?= $this->Html->link(
                        '<i class="fas fa-eye"></i> Ver en Frontend',
                        ['prefix' => false, 'controller' => 'Battlemaps', 'action' => 'view', $battlemap->slug],
                        ['class' => 'btn btn-primary', 'escape' => false, 'target' => '_blank']
                    ) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-lg-8">
                <!-- Información principal -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Información principal</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h4>Título</h4>
                                    <p><?= h($battlemap->titulo) ?></p>
                                </div>

                                <div class="mb-3">
                                    <h4>Slug</h4>
                                    <p><?= h($battlemap->slug) ?></p>
                                </div>

                                <div class="mb-3">
                                    <h4>Dimensiones</h4>
                                    <?php if ($battlemap->ancho && $battlemap->alto): ?>
                                        <p><?= h($battlemap->ancho) ?> x <?= h($battlemap->alto) ?> unidades</p>
                                    <?php else: ?>
                                        <p class="text-muted">No definido</p>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <h4>Sitio Web</h4>
                                    <?php if ($battlemap->website): ?>
                                        <p><a href="<?= h($battlemap->website) ?>" target="_blank"><?= h($battlemap->website) ?></a></p>
                                    <?php else: ?>
                                        <p class="text-muted">No especificado</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h4>Autor</h4>
                                    <p>
                                        <?= h($battlemap->cuenta->nickname) ?>
                                        <?php if (!empty($battlemap->cuenta->nombre) || !empty($battlemap->cuenta->apellido)): ?>
                                            (<?= h($battlemap->cuenta->nombre) ?> <?= h($battlemap->cuenta->apellido) ?>)
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-muted"><?= h($battlemap->cuenta->email) ?></p>
                                </div>

                                <div class="mb-3">
                                    <h4>Estado</h4>
                                    <?php
                                    $badgeClass = 'bg-secondary';
                                    if ($battlemap->estado_id == 1) {
                                        $badgeClass = 'bg-success';
                                    } else if ($battlemap->estado_id == 2) {
                                        $badgeClass = 'bg-warning';
                                    }
                                    ?>
                                    <p><span class="badge <?= $badgeClass ?>"><?= h($battlemap->estado->valor) ?></span></p>
                                </div>

                                <div class="mb-3">
                                    <h4>Fechas</h4>
                                    <p>
                                        <strong>Creado:</strong> <?= $battlemap->created->format('d/m/Y H:i') ?><br>
                                        <strong>Modificado:</strong> <?= $battlemap->modified->format('d/m/Y H:i') ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($battlemap->detalle)): ?>
                            <div class="mb-3">
                                <h4>Descripción</h4>
                                <div class="bg-light p-3 rounded">
                                    <?= $battlemap->detalle ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($battlemap->etiquetas)): ?>
                            <div class="mb-3">
                                <h4>Etiquetas</h4>
                                <div>
                                    <?php foreach ($battlemap->etiquetas as $etiqueta): ?>
                                        <span class="badge bg-primary me-1 mb-1"><?= h($etiqueta->titulo) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Favoritos -->
                <?php if (!empty($battlemap->cuentas_favoritos)): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Usuarios que lo tienen en favoritos (<?= count($battlemap->cuentas_favoritos) ?>)</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($battlemap->cuentas_favoritos as $cuenta): ?>
                                            <tr>
                                                <td><?= h($cuenta->nickname) ?></td>
                                                <td><?= h($cuenta->nombre) ?> <?= h($cuenta->apellido) ?></td>
                                                <td><?= h($cuenta->email) ?></td>
                                                <td><?= $cuenta->_joinData->created->format('d/m/Y') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <!-- Vista previa -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Imagen del mapa</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($battlemap->foto): ?>
                            <img src="<?= $this->Url->build('/img/battlemaps/' . $battlemap->id . '/grande/' . $battlemap->foto) ?>"
                                class="img-fluid rounded" alt="<?= h($battlemap->titulo) ?>">

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="text-muted">Nombre: <?= h($battlemap->foto) ?></span>

                                <?= $this->Html->link(
                                    '<i class="fas fa-trash"></i> Eliminar imagen',
                                    ['action' => 'eliminarImagen', $battlemap->id],
                                    [
                                        'class' => 'btn btn-sm btn-outline-danger',
                                        'escape' => false,
                                        'confirm' => '¿Estás seguro de que quieres eliminar esta imagen?'
                                    ]
                                ) ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center p-5 border rounded bg-light">
                                <i class="fas fa-map fa-4x text-muted mb-3"></i>
                                <p class="mb-0">Este mapa no tiene imagen</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Acciones administrativas -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Acciones administrativas</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Cambiar estado</label>
                            <div class="btn-group w-100">
                                <?= $this->Form->postLink(
                                    'Publicar',
                                    ['action' => 'cambiarEstado', $battlemap->id, 1],
                                    [
                                        'class' => 'btn btn-success' . ($battlemap->estado_id == 1 ? ' active' : ''),
                                        'confirm' => '¿Publicar este mapa?'
                                    ]
                                ) ?>

                                <?= $this->Form->postLink(
                                    'Borrador',
                                    ['action' => 'cambiarEstado', $battlemap->id, 2],
                                    [
                                        'class' => 'btn btn-warning' . ($battlemap->estado_id == 2 ? ' active' : ''),
                                        'confirm' => '¿Pasar este mapa a borrador?'
                                    ]
                                ) ?>

                                <?= $this->Form->postLink(
                                    'Ocultar',
                                    ['action' => 'cambiarEstado', $battlemap->id, 3],
                                    [
                                        'class' => 'btn btn-secondary' . ($battlemap->estado_id == 3 ? ' active' : ''),
                                        'confirm' => '¿Ocultar este mapa?'
                                    ]
                                ) ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Otras acciones</label>
                            <div class="d-flex flex-column gap-2">
                                <?= $this->Html->link(
                                    '<i class="fas fa-edit me-1"></i> Editar mapa',
                                    ['action' => 'editar', $battlemap->slug],
                                    ['class' => 'btn btn-outline-primary', 'escape' => false]
                                ) ?>

                                <?= $this->Form->postLink(
                                    '<i class="fas fa-trash me-1"></i> Eliminar mapa',
                                    ['action' => 'eliminar', $battlemap->id],
                                    [
                                        'class' => 'btn btn-outline-danger',
                                        'escape' => false,
                                        'confirm' => '¿Estás seguro de que quieres eliminar este mapa?\nEsta acción no se puede deshacer.'
                                    ]
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>