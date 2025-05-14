<div class="container-xl">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editar Partida</h3>
        </div>
        <div class="card-body">
            <?= $this->Form->create($partida) ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $this->Form->control('juego_id', [
                        'label' => 'Juego',
                        'class' => 'form-control select2-juegos',
                        'empty' => true,
                        'data-placeholder' => 'Buscar juego...'
                    ]) ?>
                    <?= $this->Form->control('titulo', ['class' => 'form-control']) ?>
                    <?= $this->Form->control('sinopsis', ['type' => 'textarea', 'class' => 'form-control']) ?>
                    <?= $this->Form->control('detalle', ['type' => 'textarea', 'class' => 'form-control']) ?>
                    <?= $this->Form->control('cupo_minimo', ['class' => 'form-control']) ?>
                    <?= $this->Form->control('cupo_maximo', ['class' => 'form-control']) ?>
                </div>
                <div class="col-md-6">
                    <?= $this->Form->control('language_id', ['label' => 'Idioma', 'class' => 'form-control', 'empty' => true]) ?>
                    <?= $this->Form->control('nivel_experiencia_id', ['label' => 'Nivel de Experiencia', 'class' => 'form-control', 'empty' => true]) ?>
                    <?= $this->Form->control('tipo_participacion_id', ['label' => 'Tipo de Participación', 'class' => 'form-control']) ?>
                    <?= $this->Form->control('estado_id', ['label' => 'Estado', 'class' => 'form-control']) ?>
                    <?= $this->Form->control('fecha_hora_sesion', ['type' => 'datetime-local', 'class' => 'form-control']) ?>
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