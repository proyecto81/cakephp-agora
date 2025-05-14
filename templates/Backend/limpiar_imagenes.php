<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Limpieza de Imágenes No Utilizadas</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <?= $this->Form->create(null, ['url' => ['action' => 'limpiarImagenes']]) ?>
                        <div class="mb-3">
                            <label for="tipo_entidad" class="form-label">Seleccione el tipo de entidad</label>
                            <?= $this->Form->select('tipo_entidad', [
                                'partidas' => 'Partidas',
                                'articulos' => 'Artículos',
                                'cuentas' => 'Cuentas de Usuario',
                                'comunidades' => 'Comunidades',
                                'perfiles' => 'Perfiles',
                                'proyectos' => 'Proyectos',
                                'juegos' => 'Juegos',
                                'battlemaps' => 'Battlemaps'
                            ], ['class' => 'form-select', 'required' => true]) ?>
                            <div class="form-text">
                                Se eliminarán las imágenes de registros que ya no existen en la base de datos.
                            </div>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Advertencia:</strong> Este proceso es irreversible. Se recomienda hacer una copia de seguridad antes de continuar.
                        </div>
                        <?= $this->Form->button('Iniciar Limpieza', ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                        <?= $this->Form->end() ?>
                    </div>

                    <div class="col-md-6">
                        <?php if (isset($resultado)): ?>
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">Resultado de la limpieza</h6>
                                </div>
                                <div class="card-body">
                                    <p>
                                        <strong>Archivos eliminados:</strong> <?= $resultado['eliminadas'] ?><br>
                                        <strong>Errores encontrados:</strong> <?= $resultado['errores'] ?>
                                    </p>

                                    <?php if (!empty($resultado['detalles'])): ?>
                                        <div class="mt-3">
                                            <h6>Detalles:</h6>
                                            <div class="overflow-auto" style="max-height: 300px;">
                                                <ul class="list-group">
                                                    <?php foreach ($resultado['detalles'] as $detalle): ?>
                                                        <li class="list-group-item small">
                                                            <?= h($detalle) ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>