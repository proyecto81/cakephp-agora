<div class="container-xl">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Nueva Comunidad</h3>
        </div>
        <div class="card-body">
            <?= $this->Form->create($comunidad, ['type' => 'file']) ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <?= $this->Form->control('nombre', [
                            'class' => 'form-control',
                            'label' => ['class' => 'form-label', 'text' => 'Nombre de la Comunidad']
                        ]) ?>
                    </div>
                    <div class="mb-3">
                        <?= $this->Form->control('descripcion', [
                            'type' => 'textarea',
                            'class' => 'form-control',
                            'label' => ['class' => 'form-label'],
                            'rows' => 3
                        ]) ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <?= $this->Form->control('mision', [
                                    'type' => 'textarea',
                                    'class' => 'form-control',
                                    'label' => ['class' => 'form-label'],
                                    'rows' => 3
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <?= $this->Form->control('vision', [
                                    'type' => 'textarea',
                                    'class' => 'form-control',
                                    'label' => ['class' => 'form-label'],
                                    'rows' => 3
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <?= $this->Form->control('historia', [
                            'type' => 'textarea',
                            'class' => 'form-control',
                            'label' => ['class' => 'form-label'],
                            'rows' => 4
                        ]) ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <?= $this->Form->control('creador_id', [
                            'class' => 'form-select',
                            'label' => ['class' => 'form-label']
                        ]) ?>
                    </div>
                    <div class="mb-3">
                        <?= $this->Form->control('estado_id', [
                            'class' => 'form-select',
                            'label' => ['class' => 'form-label']
                        ]) ?>
                    </div>
                    <div class="mb-3">
                        <div class="form-label">Verificación</div>
                        <label class="form-check form-switch">
                            <?= $this->Form->checkbox('verificada', [
                                'class' => 'form-check-input',
                                'label' => false
                            ]) ?>
                            <span class="form-check-label">Comunidad Verificada</span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <?= $this->Form->control('contacto', [
                            'type' => 'textarea',
                            'class' => 'form-control',
                            'label' => ['class' => 'form-label'],
                            'rows' => 2
                        ]) ?>
                    </div>
                    <div class="mb-3">
                        <?= $this->Form->control('foto', [
                            'type' => 'file',
                            'class' => 'form-control',
                            'label' => ['class' => 'form-label', 'text' => 'Foto de Perfil']
                        ]) ?>
                    </div>
                    <div class="mb-3">
                        <?= $this->Form->control('banner', [
                            'type' => 'file',
                            'class' => 'form-control',
                            'label' => ['class' => 'form-label', 'text' => 'Banner']
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <?= $this->Form->button('Crear Comunidad', ['class' => 'btn btn-primary']) ?>
                <?= $this->Html->link('Cancelar', ['action' => 'index'], ['class' => 'btn btn-link']) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>