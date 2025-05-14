<?php

/**
 * Vista principal del panel de espacios en Agora
 * 
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Dashboard de Espacios');
?>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Dashboard de Espacios</h2>
                <div class="text-muted mt-1">Gestión de Espacios y Eventos</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?= $this->Url->build(['action' => 'mapa']) ?>" class="btn btn-primary d-none d-sm-inline-block">
                        <i class="ti ti-map"></i>
                        Ver Mapa
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Tarjetas de estadísticas -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total de Espacios</div>
                        </div>
                        <div class="h1 mb-3"><?= $stats['total'] ?></div>
                        <div class="d-flex mb-2">
                            <div>Incluye todos los estados</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Pendientes de Revisión</div>
                        </div>
                        <div class="h1 mb-3"><?= $stats['pendientes'] ?></div>
                        <div class="d-flex mb-2">
                            <div>Espacios en estado borrador</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Espacios Publicados</div>
                        </div>
                        <div class="h1 mb-3"><?= $stats['publicados'] ?></div>
                        <div class="d-flex mb-2">
                            <div>Visibles en el mapa</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones rápidas y últimos espacios -->
        <div class="row row-cards">
            <!-- Accesos rápidos a secciones -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Acciones Rápidas</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="<?= $this->Url->build(['action' => 'listar', 'borrador']) ?>" class="list-group-item list-group-item-action">
                            <div class="row align-items-center">
                                <div class="col-auto"><i class="ti ti-file text-blue"></i></div>
                                <div class="col">Revisar Pendientes</div>
                                <div class="col-auto">
                                    <span class="badge bg-blue"><?= $stats['pendientes'] ?></span>
                                </div>
                            </div>
                        </a>
                        <a href="<?= $this->Url->build(['action' => 'listar', 'publicado']) ?>" class="list-group-item list-group-item-action">
                            <div class="row align-items-center">
                                <div class="col-auto"><i class="ti ti-world text-green"></i></div>
                                <div class="col">Ver Publicados</div>
                                <div class="col-auto">
                                    <span class="badge bg-green"><?= $stats['publicados'] ?></span>
                                </div>
                            </div>
                        </a>
                        <a href="<?= $this->Url->build(['action' => 'mapa']) ?>" class="list-group-item list-group-item-action" target="_blank">
                            <div class="row align-items-center">
                                <div class="col-auto"><i class="ti ti-map text-primary"></i></div>
                                <div class="col">Ver Mapa Público</div>
                                <div class="col-auto">
                                    <i class="ti ti-external-link text-muted"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Últimos espacios creados -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Últimos espacios</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th class="w-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($espacios) > 0): ?>
                                    <?php foreach ($espacios as $espacio): ?>
                                        <tr>
                                            <td>
                                                <?= h($espacio->titulo) ?>
                                            </td>
                                            <td class="text-muted">
                                                <?= h($espacio->tipo_espacio->valor) ?>
                                            </td>
                                            <td>
                                                <?php
                                                $badgeClass = 'bg-secondary';
                                                switch ($espacio->estado) {
                                                    case 'borrador':
                                                        $badgeClass = 'bg-blue';
                                                        break;
                                                    case 'verificado':
                                                        $badgeClass = 'bg-yellow';
                                                        break;
                                                    case 'publicado':
                                                        $badgeClass = 'bg-green';
                                                        break;
                                                    case 'rechazado':
                                                        $badgeClass = 'bg-red';
                                                        break;
                                                }
                                                if ($espacio->eliminado == 1) {
                                                    $badgeClass = 'bg-secondary';
                                                }
                                                ?>
                                                <span class="badge <?= $badgeClass ?>">
                                                    <?= h($espacio->eliminado ? 'Eliminado' : $espacio->estado) ?>
                                                </span>
                                            </td>
                                            <td class="text-muted">
                                                <?= $espacio->created->format('d/m/Y H:i') ?>
                                            </td>
                                            <td>
                                                <a href="<?= $this->Url->build(['action' => 'ver', $espacio->id]) ?>" class="btn btn-sm">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-3">No hay espacios registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>