<div class="container-xl">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Nueva Partida</h3>
        </div>
        <div class="card-body">
            <?= $this->Form->create($partida) ?>
            <div class="row">
                <div class="col-md-6">

                    <div class="mb-3">
                        <?= $this->Form->control('titulo', ['class' => 'form-control']) ?>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->control('sinopsis', ['type' => 'textarea', 'class' => 'form-control']) ?>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->control('detalle', ['type' => 'textarea', 'class' => 'form-control']) ?>
                    </div>

                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6"><?= $this->Form->control('cupo_minimo', ['class' => 'form-control']) ?></div>
                            <div class="col-md-6"><?= $this->Form->control('cupo_maximo', ['class' => 'form-control']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <?= $this->Form->control('juego_id', [
                            'label' => 'Juego',
                            'class' => 'form-control select2-juegos',
                            'empty' => true,
                            'data-placeholder' => 'Buscar juego...'
                        ]) ?>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <?= $this->Form->control('language_id', [
                                'label' => 'Idioma',
                                'class' => 'form-control',
                                'empty' => '-- Seleccione un lenguaje --',  // Texto personalizado para la opción vacía
                                'options' => $languages,
                                'required' => true,  // Si es un campo requerido
                                'placeholder' => 'Seleccione un lenguaje',
                                'div' => ['class' => 'form-group'],  // Clase para el div contenedor
                            ]) ?></div>
                        <div class="col-md-6">
                            <?= $this->Form->control('tipo_experiencia_id', [
                                'label' => 'Experiencia necesaria',
                                'class' => 'form-control',
                                'empty' => '-- Seleccione un nivel --',  // Texto personalizado para la opción vacía
                                'options' => $tipoExperiencias,
                                'required' => true,  // Si es un campo requerido
                                'placeholder' => 'Seleccione un nivel',
                                'div' => ['class' => 'form-group'],  // Clase para el div contenedor
                            ]) ?></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6"><?= $this->Form->control('tipo_participacion_id', ['label' => 'Tipo de Participación', 'class' => 'form-control']) ?></div>
                        <div class="col-md-6"><?= $this->Form->control('estado_id', ['label' => 'Estado', 'class' => 'form-control']) ?></div>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->control('fecha_hora_sesion', ['type' => 'datetime-local', 'class' => 'form-control']) ?>
                    </div>
                </div>
            </div>
            <div class="form-footer">
                <?= $this->Form->button(__('Guardar'), ['class' => 'btn btn-primary']) ?>
                <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-link']) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script>
    $('.select2-juegos').select2({
        ajax: {
            url: '<?= $this->Url->build(['plugin' => 'Agora', 'controller' => 'Partidas', 'action' => 'buscarJuegos']) ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return data;
            }
        },
        minimumInputLength: 2,
        placeholder: 'Buscar juego...',
        language: 'es'
    });
</script>