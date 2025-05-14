<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Revisar Perfil GM</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <?php if ($perfil->foto): ?>
                                <img src="/img/perfiles/<?= h($perfil->foto) ?>" class="img-fluid rounded">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <h4><?= h($perfil->cuenta->nombre) ?> <?= h($perfil->cuenta->apellido) ?></h4>
                            <p class="text-muted"><?= h($perfil->cuenta->nickname) ?></p>
                            <hr>
                            <p><strong>Experiencia como jugador:</strong> <?= h($perfil->anos_ttrpg) ?> años</p>
                            <p><strong>Experiencia como GM:</strong> <?= h($perfil->anos_gm) ?> años</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Estilo como GM</h5>
                        <p><?= nl2br(h($perfil->estilo_gm)) ?></p>
                    </div>

                    <div class="mb-4">
                        <h5>Juegos Favoritos</h5>
                        <ul>
                            <?php foreach ($perfil->perfiles_juegos as $juego): ?>
                                <li><?= h($juego->juego->titulo) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?= $this->Form->create($perfil) ?>
                    <div class="form-group mb-3">
                        <?= $this->Form->textarea('motivo_rechazo', [
                            'class' => 'form-control',
                            'placeholder' => 'Motivo del rechazo (en caso de rechazar)',
                            'rows' => 3
                        ]) ?>
                    </div>

                    <div class="d-flex justify-content-between">
                        <?= $this->Form->button('Rechazar', [
                            'class' => 'btn btn-danger',
                            'name' => 'aprobar',
                            'value' => '0'
                        ]) ?>

                        <?= $this->Form->button('Aprobar', [
                            'class' => 'btn btn-success',
                            'name' => 'aprobar',
                            'value' => '1'
                        ]) ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Disponibilidad</h4>
                </div>
                <div class="card-body">
                    <!-- Aquí podríamos mostrar una tabla o grid con la disponibilidad -->
                </div>
            </div>
        </div>
    </div>
</div>