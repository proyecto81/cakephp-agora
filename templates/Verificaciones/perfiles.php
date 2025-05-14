<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Perfiles Pendientes de Verificación</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Experiencia</th>
                            <th>Juegos</th>
                            <th>Fecha Solicitud</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($perfiles as $perfil): ?>
                            <tr>
                                <td>
                                    <?= h($perfil->cuenta->nombre) ?> <?= h($perfil->cuenta->apellido) ?>
                                    <br>
                                    <small class="text-muted"><?= h($perfil->cuenta->nickname) ?></small>
                                </td>
                                <td>
                                    <strong>Como jugador:</strong> <?= h($perfil->anos_ttrpg) ?> años<br>
                                    <strong>Como GM:</strong> <?= h($perfil->anos_gm) ?> años
                                </td>
                                <td><?= count($perfil->perfiles_juegos) ?> juegos</td>
                                <td><?= $perfil->modified->nice() ?></td>
                                <td>
                                    <?= $this->Html->link(
                                        'Revisar',
                                        ['action' => 'revisar', $perfil->id],
                                        ['class' => 'btn btn-sm btn-primary']
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>