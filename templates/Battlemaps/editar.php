<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    Battlemaps
                </div>
                <h2 class="page-title">
                    Editar Mapa: <?= h($battlemap->titulo) ?>
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <span class="d-none d-sm-inline">
                        <?= $this->Html->link(__('Volver al listado'), ['action' => 'index'], ['class' => 'btn']) ?>
                        <?= $this->Html->link(__('Ver mapa'), ['action' => 'view', $battlemap->slug], ['class' => 'btn btn-primary']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <?= $this->Flash->render() ?>

                    <?= $this->Form->create($battlemap, [
                        'id' => 'form-editar-battlemap',
                        'class' => 'contact-form-style-1',
                        'role' => 'form',
                        'enctype' => 'multipart/form-data'
                    ]) ?>

                    <div class="card-header">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Información principal -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group mb-3">
                                            <label for="titulo" class="form-label"><strong>Título</strong> <span class="text-danger">*</span></label>
                                            <?= $this->Form->text('titulo', [
                                                'id' => 'titulo',
                                                'class' => 'form-control',
                                                'required' => true,
                                                'placeholder' => 'Nombre del mapa'
                                            ]); ?>
                                            <div class="form-text text-muted">Nombre descriptivo para el mapa</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="cuenta_id" class="form-label"><strong>Autor</strong> <span class="text-danger">*</span></label>
                                            <?= $this->Form->select('cuenta_id', $cuentas, [
                                                'id' => 'cuenta_id',
                                                'class' => 'form-select',
                                                'required' => true
                                            ]); ?>
                                            <div class="form-text text-muted">Creador del mapa</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="slug" class="form-label"><strong>Slug</strong></label>
                                            <?= $this->Form->text('slug', [
                                                'id' => 'slug',
                                                'class' => 'form-control',
                                                'placeholder' => 'URL amigable'
                                            ]); ?>
                                            <div class="form-text text-muted">Identificador único en la URL</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="website" class="form-label"><strong>Sitio web</strong></label>
                                            <?= $this->Form->text('website', [
                                                'id' => 'website',
                                                'class' => 'form-control',
                                                'placeholder' => 'URL del sitio web (opcional)',
                                                'type' => 'url'
                                            ]); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="etiqueta_string" class="form-label"><strong>Etiquetas</strong></label>
                                            <?= $this->Form->text('etiqueta_string', [
                                                'id' => 'etiqueta_string',
                                                'class' => 'form-control',
                                                'placeholder' => 'Separadas por comas',
                                                'value' => $battlemap->etiqueta_string
                                            ]); ?>
                                            <div class="form-text text-muted">Ejemplo: bosque, medieval, mazmorra</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="estado_id" class="form-label"><strong>Estado</strong></label>
                                            <?= $this->Form->select('estado_id', $estados, [
                                                'id' => 'estado_id',
                                                'class' => 'form-select',
                                                'required' => true
                                            ]); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="detalle" class="form-label"><strong>Descripción</strong></label>
                                            <?= $this->Form->textarea('detalle', [
                                                'id' => 'detalle',
                                                'rows' => '8',
                                                'class' => 'form-control',
                                                'placeholder' => 'Descripción detallada del mapa'
                                            ]); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detalles y carga de imagen -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="ancho" class="form-label"><strong>Dimensiones</strong></label>
                                    <div class="input-group mb-3">
                                        <?= $this->Form->number('ancho', [
                                            'id' => 'ancho',
                                            'class' => 'form-control',
                                            'placeholder' => 'Ancho',
                                            'min' => 1
                                        ]); ?>
                                        <span class="input-group-text">x</span>
                                        <?= $this->Form->number('alto', [
                                            'id' => 'alto',
                                            'class' => 'form-control',
                                            'placeholder' => 'Alto',
                                            'min' => 1
                                        ]); ?>
                                    </div>
                                    <div class="form-text text-muted">Dimensiones del mapa en unidades o casillas</div>
                                </div>

                                <!-- Imagen actual -->
                                <?php if ($battlemap->foto): ?>
                                    <div class="form-group mb-3">
                                        <label class="form-label"><strong>Imagen actual</strong></label>
                                        <div class="card">
                                            <div class="card-img-top">
                                                <img src="<?= $this->Url->build('/img/battlemaps/' . $battlemap->id . '/media/' . $battlemap->foto) ?>"
                                                    class="img-fluid" alt="<?= h($battlemap->titulo) ?>">
                                            </div>
                                            <div class="card-footer d-flex justify-content-between align-items-center">
                                                <small class="text-muted"><?= h($battlemap->foto) ?></small>

                                                <?= $this->Html->link(
                                                    '<i class="fas fa-trash-alt me-1"></i>Eliminar',
                                                    ['action' => 'eliminarImagen', $battlemap->id],
                                                    [
                                                        'class' => 'btn btn-sm btn-outline-danger',
                                                        'escape' => false,
                                                        'confirm' => '¿Estás seguro de que quieres eliminar esta imagen?'
                                                    ]
                                                ) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Cargar nueva imagen -->
                                <div class="form-group mb-3">
                                    <label for="foto_battlemap" class="form-label">
                                        <strong><?= $battlemap->foto ? 'Reemplazar imagen' : 'Imagen del Mapa' ?></strong>
                                    </label>
                                    <input type="file" class="form-control" id="foto_battlemap" name="foto_battlemap" accept="image/jpeg,image/png,image/webp,image/gif">
                                    <div class="form-text text-muted mt-2">
                                        <ul class="mb-0 ps-3">
                                            <li>Formatos permitidos: JPG, PNG, WEBP, GIF</li>
                                            <li>Tamaño máximo: 5MB</li>
                                            <li>Resolución recomendada: mínimo 1200px de ancho</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-trash me-1"></i>Eliminar Mapa',
                                    ['action' => 'eliminar', $battlemap->id],
                                    [
                                        'class' => 'btn btn-outline-danger',
                                        'escape' => false,
                                        'confirm' => '¿Estás seguro de que quieres eliminar este mapa? Esta acción no se puede deshacer.'
                                    ]
                                ) ?>
                            </div>

                            <div>
                                <?= $this->Html->link(
                                    '<i class="fas fa-times me-1"></i>Cancelar',
                                    ['action' => 'view', $battlemap->slug],
                                    ['class' => 'btn btn-outline-secondary me-2', 'escape' => false]
                                ) ?>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar editor de texto enriquecido si está disponible
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#detalle',
                language: 'es',
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
                height: 400
            });
        }
    });
</script>