<?php

/**
 * Vista para listar espacios
 * 
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Listar Espacios');

// Títulos descriptivos según estado
$titulos = [
    'borrador' => 'Espacios Pendientes de Revisión',
    'verificado' => 'Espacios Verificados',
    'publicado' => 'Espacios Publicados',
    'rechazado' => 'Espacios Rechazados',
    'eliminados' => 'Espacios en Papelera',
    'todos' => 'Todos los Espacios',
];

// Iconos para cada estado
$iconos = [
    'borrador' => 'ti-file',
    'verificado' => 'ti-check',
    'publicado' => 'ti-world',
    'rechazado' => 'ti-x',
    'eliminados' => 'ti-trash',
    'todos' => 'ti-list',
];

// Clases para badges
$badgeClasses = [
    'borrador' => 'bg-blue',
    'verificado' => 'bg-yellow',
    'publicado' => 'bg-green',
    'rechazado' => 'bg-red',
    'eliminados' => 'bg-secondary',
    'todos' => 'bg-primary',
];
?>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?= $titulos[$estado] ?? 'Espacios' ?>
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?= $this->Url->build(['action' => 'mapa']) ?>" class="btn d-none d-sm-inline-block" target="_blank">
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
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <?php foreach (['borrador', 'verificado', 'publicado', 'rechazado', 'eliminados', 'todos'] as $tab): ?>
                        <li class="nav-item">
                            <a href="<?= $this->Url->build(['action' => 'listar', $tab]) ?>" class="nav-link <?= $estado === $tab ? 'active' : '' ?>">
                                <i class="ti <?= $iconos[$tab] ?? 'ti-file' ?> me-2"></i>
                                <?= ucfirst($tab) ?>
                                <span class="badge <?= $badgeClasses[$tab] ?? 'bg-secondary' ?> ms-2"><?= $contadores[$tab] ?? 0 ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="card-body">
                <?php if (count($espacios) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <?php if ($estado === 'todos'): ?>
                                        <th>Estado</th>
                                    <?php endif; ?>
                                    <th>Datos</th>
                                    <th>Fechas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($espacios as $espacio): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                <div class="flex-fill">
                                                    <div class="font-weight-medium"><?= h($espacio->titulo) ?></div>
                                                    <?php if (!empty($espacio->descripcion)): ?>
                                                        <div class="text-muted text-truncate" style="max-width: 300px;">
                                                            <?= h(substr($espacio->descripcion, 0, 80)) ?><?= strlen($espacio->descripcion) > 80 ? '...' : '' ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?= h($espacio->tipo_espacio->valor) ?>
                                        </td>
                                        <?php if ($estado === 'todos'): ?>
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
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= ucfirst($espacio->estado) ?></span>
                                            </td>
                                        <?php endif; ?>
                                        <td>
                                            <div>
                                                <?= h($espacio->organizador ?? 'Sin organizador') ?>
                                            </div>
                                            <?php if (!empty($espacio->email)): ?>
                                                <div class="text-muted small">
                                                    <?= h($espacio->email) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div>Creado: <?= $espacio->created->format('d/m/Y') ?></div>
                                            <div class="text-muted small"><?= $espacio->created->format('H:i') ?></div>
                                        </td>
                                        <td class="text-nowrap">
                                            <div class="dropdown">
                                                <button class="btn dropdown-toggle align-text-top" data-bs-toggle="dropdown">
                                                    Acciones
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="<?= $this->Url->build(['action' => 'ver', $espacio->id]) ?>" class="dropdown-item">
                                                        <i class="ti ti-eye me-2"></i> Ver detalles
                                                    </a>

                                                    <?php if ($estado !== 'eliminados'): ?>
                                                        <?php if ($espacio->estado !== 'publicado'): ?>
                                                            <?= $this->Form->postLink(
                                                                '<i class="ti ti-world me-2"></i> Publicar',
                                                                ['action' => 'cambiarEstado', $espacio->id, 'publicado'],
                                                                ['class' => 'dropdown-item', 'escape' => false, 'confirm' => '¿Publicar este espacio?']
                                                            ) ?>
                                                        <?php endif; ?>

                                                        <?php if ($espacio->estado !== 'borrador'): ?>
                                                            <?= $this->Form->postLink(
                                                                '<i class="ti ti-file me-2"></i> Cambiar a borrador',
                                                                ['action' => 'cambiarEstado', $espacio->id, 'borrador'],
                                                                ['class' => 'dropdown-item', 'escape' => false, 'confirm' => '¿Cambiar a estado borrador?']
                                                            ) ?>
                                                        <?php endif; ?>

                                                        <?php if ($espacio->estado !== 'verificado'): ?>
                                                            <?= $this->Form->postLink(
                                                                '<i class="ti ti-check me-2"></i> Verificar',
                                                                ['action' => 'cambiarEstado', $espacio->id, 'verificado'],
                                                                ['class' => 'dropdown-item', 'escape' => false, 'confirm' => '¿Verificar este espacio?']
                                                            ) ?>
                                                        <?php endif; ?>

                                                        <?php if ($espacio->estado !== 'rechazado'): ?>
                                                            <?= $this->Form->postLink(
                                                                '<i class="ti ti-x me-2"></i> Rechazar',
                                                                ['action' => 'cambiarEstado', $espacio->id, 'rechazado'],
                                                                ['class' => 'dropdown-item', 'escape' => false, 'confirm' => '¿Rechazar este espacio?']
                                                            ) ?>
                                                        <?php endif; ?>

                                                        <div class="dropdown-divider"></div>

                                                        <?= $this->Form->postLink(
                                                            '<i class="ti ti-trash me-2"></i> Eliminar',
                                                            ['action' => 'eliminar', $espacio->id],
                                                            ['class' => 'dropdown-item text-danger', 'escape' => false, 'confirm' => '¿Enviar este espacio a la papelera?']
                                                        ) ?>
                                                    <?php else: ?>
                                                        <?= $this->Form->postLink(
                                                            '<i class="ti ti-arrow-back-up me-2"></i> Recuperar',
                                                            ['action' => 'recuperar', $espacio->id],
                                                            ['class' => 'dropdown-item', 'escape' => false, 'confirm' => '¿Recuperar este espacio de la papelera?']
                                                        ) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-3 d-flex justify-content-center">
                        <ul class="pagination">
                            <?= $this->Paginator->first('<< ' . __('Primera')) ?>
                            <?= $this->Paginator->prev('< ' . __('Anterior')) ?>
                            <?= $this->Paginator->numbers() ?>
                            <?= $this->Paginator->next(__('Siguiente') . ' >') ?>
                            <?= $this->Paginator->last(__('Última') . ' >>') ?>
                        </ul>
                    </div>
                    <p class="text-center text-muted">
                        <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de {{count}} totales')) ?>
                    </p>

                <?php else: ?>
                    <div class="empty">
                        <div class="empty-img">
                            <i class="ti ti-search-off" style="font-size: 3rem;"></i>
                        </div>
                        <p class="empty-title">No se encontraron espacios</p>
                        <p class="empty-subtitle text-muted">
                            No hay espacios registrados con el estado actual.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>