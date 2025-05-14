<?php

/**
 * @var \App\View\AppView $this
 */
?>
<div class="container-xl">
    <!-- Encabezado de página -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Dashboard</h2>
            </div>
        </div>
    </div>

    <!-- Cards de estadísticas -->
    <div class="row row-deck row-cards">
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Partidas Activas</div>
                    </div>
                    <div class="h1 mb-3"><?= $stats['partidas_activas'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Usuarios Nuevos (30 días)</div>
                    </div>
                    <div class="h1 mb-3"><?= $stats['usuarios_nuevos'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Artículos Pendientes</div>
                    </div>
                    <div class="h1 mb-3"><?= $stats['articulos_pendientes'] ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas actividades -->
    <div class="row mt-4">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Últimas Partidas</h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php foreach ($ultimas_partidas as $partida): ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-truncate">
                                            <?= h($partida->titulo) ?>
                                        </div>
                                        <div class="text-muted">
                                            <?= h($partida->cuenta->nickname) ?> -
                                            <?= $partida->created->format('d/m/Y H:i') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Últimos Usuarios</h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php foreach ($ultimas_cuentas as $cuenta): ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-truncate">
                                            <?= h($cuenta->nickname) ?>
                                        </div>
                                        <div class="text-muted">
                                            <?= $cuenta->created->format('d/m/Y H:i') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-deck row-cards">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <?PHP var_dump($result); ?>
                </div>
            </div>
        </div>
    </div>
</div>