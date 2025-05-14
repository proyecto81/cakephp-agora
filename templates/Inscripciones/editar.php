<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Inscripcion $inscripcion
 */
?>
<div class="container-xl">
    <div class="row row-cards">
        <div class="col-12">
            <?= $this->Form->create($inscripcion) ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Inscripción</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $this->Form->control('partida_id', [
                                'options' => $partidas,
                                'empty' => 'Seleccione una partida',
                                'class' => 'form-select select2',
                                'label' => ['class' => 'form-label', 'text' => 'Partida'],
                                'required' => true
                            ]); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $this->Form->control('cuenta_id', [
                                'options' => $cuentas,
                                'empty' => 'Seleccione un jugador',
                                'class' => 'form-select select2',
                                'label' => ['class' => 'form-label', 'text' => 'Jugador'],
                                'required' => true
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $this->Form->control('tipo_inscripcion_id', [
                                'options' => $tipoInscripciones,
                                'empty' => 'Seleccione un tipo',
                                'class' => 'form-select',
                                'label' => ['class' => 'form-label', 'text' => 'Tipo de Inscripción'],
                                'required' => true
                            ]); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $this->Form->control('estado_id', [
                                'options' => $estados,
                                'empty' => 'Seleccione un estado',
                                'class' => 'form-select',
                                'label' => ['class' => 'form-label', 'text' => 'Estado'],
                                'required' => true
                            ]); ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <?= $this->Html->link('Cancelar', ['action' => 'index'], ['class' => 'btn btn-link']) ?>
                    <?= $this->Form->button('Guardar cambios', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            theme: 'bootstrap-5'
        });
    });
</script>