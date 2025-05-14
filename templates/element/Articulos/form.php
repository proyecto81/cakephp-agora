<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Articulo $articulo
 */

// Assets necesarios para imagenes
$this->Html->css('https://unpkg.com/dropzone@5/dist/min/dropzone.min.css', ['block' => true]);
$this->Html->script('https://unpkg.com/dropzone@5/dist/min/dropzone.min.js', ['block' => true]);

// Editor de texto TinyMCE
$this->Html->script('tinymce/tinymce.min', ['block' => true]);

// jquery, tabler y select2 están configurados en el layout

// Assets necesarios
// ckeditor5 aun no configurado. Buscar otro editor de texto enriquecido.
?>
<style>
    #detalle {
        height: 600px !important;
    }
</style>
<div class="container-xl">
    <!-- Encabezado -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?= isset($articulo->id) ? 'Editar Artículo: ' . h($articulo->titulo) : 'Nuevo Artículo' ?>
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <?= $this->Html->link(
                    __('Volver al listado'),
                    ['action' => 'index'],
                    ['class' => 'btn btn-link']
                ) ?>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="page-body">
        <?= $this->Form->create($articulo, [
            'type' => 'file',
            'class' => 'articulo-form',
            'id' => 'articuloForm'
        ]) ?>

        <div class="row">
            <!-- Columna principal -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tab-general" class="nav-link active" data-bs-toggle="tab">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" />
                                        <path d="M13.5 6.5l4 4" />
                                    </svg>
                                    General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-imagenes" class="nav-link" data-bs-toggle="tab">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M15 8h.01" />
                                        <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                        <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                        <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                                    </svg>
                                    Imágenes (<?= $nro_imagenes; ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-multimedia" class="nav-link" data-bs-toggle="tab">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M15 8h.01" />
                                        <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                        <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                        <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                                    </svg>
                                    Multimedia (<?= $nro_multimedia; ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-documentos" class="nav-link" data-bs-toggle="tab">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <line x1="9" y1="9" x2="10" y2="9" />
                                        <line x1="9" y1="13" x2="15" y2="13" />
                                        <line x1="9" y1="17" x2="15" y2="17" />
                                    </svg>
                                    Documentos (<?= $nro_documentos; ?>)
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Tab General -->
                            <div class="tab-pane active show" id="tab-general">

                                <!-- Campo de título -->
                                <div class="form-group mb-3">
                                    <?= $this->Form->label('titulo', 'Título', ['class' => 'form-label']) ?>
                                    <?= $this->Form->text('titulo', [
                                        'class' => 'form-control',
                                        'id' => 'titulo',
                                        'required' => true,
                                        'placeholder' => 'Ingrese el título del artículo'
                                    ]) ?>
                                </div>

                                <!-- Campo de slug con indicador de generación automática -->
                                <div class="form-group mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <?= $this->Form->label('slug', 'Slug', ['class' => 'form-label']) ?>
                                        <div class="form-text small" id="slug-status">Generación automática activada</div>
                                    </div>
                                    <div class="input-group">
                                        <?= $this->Form->text('slug', [
                                            'class' => 'form-control',
                                            'id' => 'slug',
                                            'placeholder' => 'slug-del-articulo'
                                        ]) ?>
                                        <button type="button" id="btn-reset-slug" class="btn btn-outline-secondary" title="Regenerar desde título">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                                            </svg>
                                        </button>
                                        <button type="button" id="btn-toggle-autoslug" class="btn btn-outline-primary active" title="Activar/desactivar generación automática">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-link">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M9 15l6 -6" />
                                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" />
                                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="form-text">URL amigable para el artículo. Se genera automáticamente desde el título.</div>
                                </div>


                                <div class="mb-3">
                                    <?= $this->Form->control('copete', [
                                        'type' => 'textarea',
                                        'class' => 'form-control',
                                        'label' => ['class' => 'form-label', 'text' => 'Copete']
                                    ]) ?>
                                </div>
                                <div class="mb-3">
                                    <?= $this->Form->control('detalle', [
                                        'type' => 'textarea',
                                        'class' => 'form-control',
                                        'label' => ['class' => 'form-label', 'text' => 'Contenido'],
                                        'required' => true,
                                        'rows' => 15,
                                    ]) ?>
                                </div>
                            </div>

                            <!-- Tab Imágenes -->
                            <div class="tab-pane" id="tab-imagenes">
                                <!-- Zona de carga -->
                                <div class="mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <?php if (isset($articulo->id)): ?>
                                                <div id="dropzoneImagenes"
                                                    class="dropzone"
                                                    data-url="<?= $this->Url->build(['action' => 'agregarImagen', $articulo->id ?? 0]) ?>"
                                                    data-csrf="<?= $this->request->getAttribute('csrfToken') ?>">
                                                    <div class="dz-message">
                                                        <h3 class="text-muted">Arrastra las imágenes aquí o haz clic para seleccionarlas</h3>
                                                        <div class="text-muted">(Las imágenes se subirán automáticamente)</div>
                                                        <div class="text-muted">(Límite de 5MB por archivo)</div>
                                                        <div class="text-muted">(Solo acepta imágenes)</div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-info">
                                                    Guarda el artículo primero para poder agregar imágenes
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Galería de imágenes -->
                                <?php if (!empty($articulo->articulo_imagenes)): ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Imágenes del artículo</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row row-cards">
                                                <?php foreach ($articulo->articulo_imagenes as $imagen): ?>
                                                    <div class="col-sm-6 col-lg-4" id="imagen-contenedor-<?= $imagen->id ?>">
                                                        <div class="card card-sm">
                                                            <a href="#" class="d-block" data-bs-toggle="modal" data-bs-target="#modal-imagen-<?= $imagen->id ?>">
                                                                <?= $this->Html->image($imagen->file_path, [
                                                                    'class' => 'card-img-top',
                                                                    'style' => 'height: 200px; object-fit: cover;'
                                                                ]) ?>
                                                            </a>
                                                            <div class="card-body">
                                                                <div class="d-flex align-items-center">
                                                                    <select class="form-select mb-2"
                                                                        onchange="actualizarTipoImagen(<?= $imagen->id ?>, this.value)">
                                                                        <?php foreach ($tipo_imagenes as $id => $valor): ?>
                                                                            <option value="<?= $id ?>" <?= $imagen->tipo_imagen_id == $id ? 'selected' : '' ?>>
                                                                                <?= h($valor) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <input type="text"
                                                                    class="form-control mb-2"
                                                                    value="<?= h($imagen->title_data) ?>"
                                                                    placeholder="Título"
                                                                    onchange="actualizarDatosImagen(<?= $imagen->id ?>, 'title_data', this.value)">
                                                                <input type="text"
                                                                    class="form-control mb-2"
                                                                    value="<?= h($imagen->alt_data) ?>"
                                                                    placeholder="Texto alternativo"
                                                                    onchange="actualizarDatosImagen(<?= $imagen->id ?>, 'alt_data', this.value)">
                                                                <textarea class="form-control mb-2"
                                                                    placeholder="Epígrafe"
                                                                    onchange="actualizarDatosImagen(<?= $imagen->id ?>, 'epigrafe', this.value)"
                                                                    rows="2"><?= h($imagen->epigrafe) ?></textarea>

                                                                <input type="text"
                                                                    class="form-control mb-2"
                                                                    value="<?= h($imagen->posicion) ?>"
                                                                    placeholder="Posición / Orden"
                                                                    onchange="actualizarDatosImagen(<?= $imagen->id ?>, 'posicion', this.value)">
                                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                                    <button type="button"
                                                                        class="btn btn-danger"
                                                                        onclick="eliminarImagen(<?= $imagen->id ?>)">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                            <line x1="4" y1="7" x2="20" y2="7" />
                                                                            <line x1="10" y1="11" x2="10" y2="17" />
                                                                            <line x1="14" y1="11" x2="14" y2="17" />
                                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                                        </svg>
                                                                        Eliminar
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modal para vista previa -->
                                                    <div class="modal modal-blur fade" id="modal-imagen-<?= $imagen->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Vista previa</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <?= $this->Html->image($imagen->file_path, ['class' => 'img-fluid']) ?>
                                                                    <div class="mt-3">
                                                                        <p><strong>Nombre del archivo:</strong> <?= h($imagen->file_name) ?></p>
                                                                        <p><strong>Tamaño:</strong> <?= $this->Number->toReadableSize($imagen->file_size) ?></p>
                                                                        <p><strong>Tipo:</strong> <?= h($imagen->mime_type) ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Tab contenido multimedia -->
                            <div class="tab-pane" id="tab-multimedia">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h3 class="card-title">Contenido multimedia</h3>

                                        <?php if (isset($articulo->id)): ?>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-multimedia">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <line x1="12" y1="5" x2="12" y2="19" />
                                                    <line x1="5" y1="12" x2="19" y2="12" />
                                                </svg>
                                                Agregar contenido
                                            </button>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                Guarda el artículo primero para poder agregar multimedia
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </div>


                                <?php if (!empty($articulo->articulo_multimedias)): ?>
                                    <!-- Lista de contenido multimedia existente -->
                                    <div class="card mt-3">
                                        <div class="card-header">
                                            <h3 class="card-title">Contenido multimedia</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-vcenter card-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Tipo</th>
                                                            <th>Título</th>
                                                            <th>Contenido</th>
                                                            <th>Orden</th>
                                                            <th>Destacado</th>
                                                            <th class="w-1"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($articulo->articulo_multimedias as $multimedia): ?>
                                                            <tr>
                                                                <td><?= h($multimedia->tipo) ?></td>
                                                                <td><?= h($multimedia->titulo) ?></td>
                                                                <td>
                                                                    <?php if ($multimedia->tipo === 'video'): ?>
                                                                        <a href="<?= h($multimedia->url) ?>" target="_blank">Ver video</a>
                                                                    <?php else: ?>
                                                                        <?= $this->Text->truncate(h($multimedia->contenido), 100) ?>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?= h($multimedia->orden) ?></td>
                                                                <td>
                                                                    <?php if ($multimedia->destacado): ?>
                                                                        <span class="badge bg-success">Sí</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-list flex-nowrap">
                                                                        <button class="btn btn-white btn-sm" onclick="editarMultimedia(<?= $multimedia->id ?>)">
                                                                            Editar
                                                                        </button>
                                                                        <button class="btn btn-danger btn-sm" onclick="eliminarMultimedia(<?= $multimedia->id ?>)">
                                                                            Eliminar
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>


                            </div>

                            <!-- Tab archivos -->
                            <div class="tab-pane" id="tab-documentos">
                                <div class="card">
                                    <div class="card-body">
                                        <?php if (isset($articulo->id)): ?>
                                            <div id="dropzoneDocumentos" class="dropzone">
                                                <div class="dz-message">
                                                    <h3 class="text-muted">Arrastra los documentos aquí o haz clic para seleccionarlos</h3>
                                                    <div class="text-muted">(Los documentos se subirán automáticamente)</div>
                                                    <div class="text-muted">(Límite de 10MB por archivo)</div>
                                                    <div class="text-muted">(PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP)</div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                Guarda el artículo primero para poder agregar documentos
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if (!empty($articulo->articulo_documentos)): ?>
                                    <div class="card mt-3">
                                        <div class="card-header">
                                            <h3 class="card-title">Documentos adjuntos</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-vcenter">
                                                    <thead>
                                                        <tr>
                                                            <th>Título</th>
                                                            <th>Archivo</th>
                                                            <th>Tamaño</th>
                                                            <th>Descargas</th>
                                                            <th class="w-1"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($articulo->articulo_documentos as $documento): ?>
                                                            <tr>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control"
                                                                        value="<?= h($documento->titulo) ?>"
                                                                        onchange="actualizarDocumento(<?= $documento->id ?>, 'titulo', this.value)">
                                                                    <textarea class="form-control mt-2"
                                                                        placeholder="Descripción"
                                                                        onchange="actualizarDocumento(<?= $documento->id ?>, 'descripcion', this.value)"><?= h($documento->descripcion) ?></textarea>
                                                                </td>
                                                                <td>
                                                                    <?= h($documento->file_name) ?>
                                                                </td>
                                                                <td>
                                                                    <?= $this->Number->toReadableSize($documento->file_size) ?>
                                                                </td>
                                                                <td>
                                                                    <?= $this->Number->format($documento->descargas) ?>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-list flex-nowrap">
                                                                        <a href="<?= $this->Url->build(['action' => 'descargarDocumento', $documento->id]) ?>"
                                                                            class="btn btn-white btn-sm">
                                                                            Descargar
                                                                        </a>
                                                                        <button class="btn btn-danger btn-sm"
                                                                            onclick="eliminarDocumento(<?= $documento->id ?>)">
                                                                            Eliminar
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel lateral (configuración) -->
            <div class="col-md-3">
                <!-- Configuración básica -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Configuración</h3>
                    </div>
                    <div class="card-body">
                        <!-- Estado -->
                        <div class="mb-3">
                            <?= $this->Form->control('estado_id', [
                                'type' => 'select',
                                'options' => $estados,
                                'class' => 'form-select',
                                'label' => ['class' => 'form-label', 'text' => 'Estado'],
                                'required' => true,
                                'empty' => '- Seleccione un estado -'
                            ]) ?>
                        </div>

                        <!-- Categoría -->
                        <div class="mb-3">
                            <?= $this->Form->control('categoria_id', [
                                'type' => 'select',
                                'options' => $categorias,
                                'class' => 'form-select',
                                'label' => ['class' => 'form-label', 'text' => 'Categoría'],
                                'required' => true,
                                'empty' => '- Seleccione una categoría -'
                            ]) ?>
                        </div>

                        <!-- Etiquetas -->
                        <div class="mb-3">
                            <?= $this->Form->control('etiquetas._ids', [
                                'type' => 'select',
                                'options' => $etiquetas,
                                'multiple' => true,
                                'class' => 'form-select select2-tags', // Cambiamos la clase para diferenciar
                                'label' => ['class' => 'form-label', 'text' => 'Etiquetas'],
                                'data-placeholder' => 'Escriba o seleccione etiquetas...'
                            ]) ?>
                        </div>



                        <!-- Programación de contenido -->
                        <div class="mb-3">
                            <label class="form-label">Programación de contenido</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                        <path d="M16 3l0 4" />
                                        <path d="M8 3l0 4" />
                                        <path d="M4 11l16 0" />
                                        <path d="M8 15h2v2h-2z" />
                                    </svg>
                                </span>
                                <?= $this->Form->control('fecha_alta', [
                                    'type' => 'datetime-local',
                                    'class' => 'form-control',
                                    'label' => false,
                                    'placeholder' => 'Fecha de publicación',
                                    'templates' => ['inputContainer' => '{{content}}']
                                ]) ?>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                        <path d="M16 3l0 4" />
                                        <path d="M8 3l0 4" />
                                        <path d="M4 11l16 0" />
                                        <path d="M11 15h6" />
                                    </svg>
                                </span>
                                <?= $this->Form->control('fecha_baja', [
                                    'type' => 'datetime-local',
                                    'class' => 'form-control',
                                    'label' => false,
                                    'placeholder' => 'Fecha de despublicación',
                                    'templates' => ['inputContainer' => '{{content}}']
                                ]) ?>
                            </div>
                            <div class="form-hint">
                                Deje los campos vacíos para publicar inmediatamente sin fecha de expiración
                            </div>
                        </div>

                        <!-- Opciones -->
                        <div class="mb-3">
                            <label class="form-label">Opciones</label>
                            <label class="form-check">
                                <?= $this->Form->checkbox('destacado', [
                                    'class' => 'form-check-input'
                                ]) ?>
                                <span class="form-check-label">Destacado</span>
                            </label>
                            <label class="form-check">
                                <?= $this->Form->checkbox('restringido', [
                                    'class' => 'form-check-input'
                                ]) ?>
                                <span class="form-check-label">Contenido restringido</span>
                            </label>
                            <label class="form-check">
                                <?= $this->Form->checkbox('archivado', [
                                    'class' => 'form-check-input'
                                ]) ?>
                                <span class="form-check-label">Archivado</span>
                            </label>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <?= $this->Form->button(__('Guardar'), [
                            'class' => 'btn btn-primary'
                        ]) ?>
                    </div>
                </div>

                <?php if (isset($articulo->id)): ?>
                    <!-- Información adicional -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Información</h3>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Creado</div>
                                    <div class="datagrid-content">
                                        <?= $articulo->created->format('d/m/Y H:i') ?>
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Modificado</div>
                                    <div class="datagrid-content">
                                        <?= $articulo->modified->format('d/m/Y H:i') ?>
                                    </div>
                                </div>
                                <?php if ($articulo->published): ?>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Publicado</div>
                                        <div class="datagrid-content">
                                            <?= $articulo->published->format('d/m/Y H:i') ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($articulo->has('cuenta')): ?>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Autor</div>
                                        <div class="datagrid-content">
                                            <?= h($articulo->cuenta->nickname) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?= $this->Form->end() ?>

    </div>
</div>


<!-- Modal -->
<div class="modal modal-blur fade" id="modal-multimedia" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar contenido multimedia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?= $this->Form->create(null, [
                'id' => 'form-multimedia',
                'url' => ['action' => 'agregarMultimedia', $articulo->id ?? 0],
            ]) ?>
            <div class="modal-body">
                <!-- Contenido del formulario -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">Tipo de contenido</label>
                            <select class="form-select" name="tipo" required>
                                <option value="">Seleccione un tipo...</option>
                                <option value="video">Video (YouTube/Vimeo)</option>
                                <option value="slider">Galería de imágenes</option>
                                <option value="codigo">Código embebido</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">Título</label>
                            <input type="text" class="form-control" name="titulo" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Contenido</label>
                    <textarea class="form-control" name="contenido" rows="4" required></textarea>
                    <div class="form-hint" id="hint-multimedia"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">URL (opcional)</label>
                    <input type="url" class="form-control" name="url">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Orden</label>
                            <input type="number" class="form-control" name="orden" value="0" min="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-check mt-4">
                                <input type="checkbox" class="form-check-input" name="destacado">
                                <span class="form-check-label">Destacado</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    Guardar contenido
                </button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<!-- Modal para selector de recursos -->
<div class="modal modal-blur fade" id="modal-selector" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Contenido dinámico -->
        </div>
    </div>
</div>


<?php $this->append('script'); ?>
<script>
    $(document).ready(function() {
        $('.select2-tags').select2({
            theme: 'default',
            width: '100%',
            tags: true, // Permite crear nuevas etiquetas
            tokenSeparators: [',', ' '], // Permite separar por coma o espacio
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                },
                createOption: function(params) { // Texto para nueva etiqueta
                    return "Crear etiqueta: " + params.term;
                }
            },
            createTag: function(params) {
                // Valida y formatea la nueva etiqueta
                var term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                return {
                    id: 'new:' + term, // Prefijo para identificar nuevas etiquetas
                    text: term,
                    newTag: true
                };
            }
        });
    });

    // Inicialización de Dropzones
    Dropzone.autoDiscover = false;
    document.addEventListener('DOMContentLoaded', function() {
        // Dropzone para imágenes
        let dropzoneImagenes = document.getElementById("dropzoneImagenes");
        if (dropzoneImagenes) {
            new Dropzone("#dropzoneImagenes", {
                url: '<?= $this->Url->build(['action' => 'agregarImagen', $articulo->id ?? 0]) ?>',
                paramName: "file",
                maxFilesize: 5,
                acceptedFiles: 'image/*',
                thumbnailWidth: 200,
                thumbnailHeight: 200,
                addRemoveLinks: true,
                // Estas opciones son cruciales:
                autoProcessQueue: true, // Mantener en true para procesar automáticamente
                uploadMultiple: false, // Procesar archivos uno por uno
                parallelUploads: 1, // Subir archivos de uno en uno
                dictDefaultMessage: "Arrastra las imágenes aquí o haz clic para seleccionarlas",
                dictRemoveFile: "Eliminar",
                headers: {
                    'X-CSRF-Token': '<?= $this->request->getAttribute('csrfToken') ?>'
                },
                init: function() {
                    var myDropzone = this;
                    var completedFiles = 0;
                    var totalFiles = 0;

                    this.on("addedfiles", function(files) {
                        totalFiles = myDropzone.files.length;
                    });

                    this.on("sending", function(file, xhr, formData) {
                        formData.append("_csrfToken", '<?= $this->request->getAttribute('csrfToken') ?>');
                    });

                    this.on("success", function(file, response) {
                        if (response.success) {
                            completedFiles++;
                            file.previewElement.classList.add("dz-success");

                            // Solo recargamos la página cuando todos los archivos se han subido
                            if (completedFiles === totalFiles) {
                                setTimeout(function() {
                                    location.reload();
                                }, 1000); // Pequeño retraso para permitir que Dropzone actualice la UI
                            }
                        } else {
                            file.previewElement.classList.add("dz-error");
                            file._removeLink.textContent = "Eliminar";
                            let message = response.message || "Error al subir el archivo";
                            file.previewElement.querySelector("[data-dz-errormessage]").textContent = message;
                        }
                    });
                }
            });
        }

        // Dropzone para documentos
        let dropzoneDocumentos = document.getElementById("dropzoneDocumentos");
        if (dropzoneDocumentos) {
            new Dropzone("#dropzoneDocumentos", {
                url: '<?= $this->Url->build(['action' => 'agregarDocumento', $articulo->id ?? 0]) ?>',
                paramName: "file",
                maxFilesize: 10,
                acceptedFiles: '.txt,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip',
                dictDefaultMessage: "Arrastra los documentos aquí o haz clic para seleccionarlos",
                dictRemoveFile: "Eliminar",
                dictFileTooBig: "El archivo es demasiado grande ({{filesize}}MB). Tamaño máximo: {{maxFilesize}}MB",
                dictInvalidFileType: "No puedes subir archivos de este tipo",
                // Estas opciones son cruciales:
                autoProcessQueue: true, // Mantener en true para procesar automáticamente
                uploadMultiple: false, // Procesar archivos uno por uno
                parallelUploads: 1, // Subir archivos de uno en uno
                headers: {
                    'X-CSRF-Token': '<?= $this->request->getAttribute('csrfToken') ?>'
                },
                init: function() {
                    var myDropzone = this;
                    var completedFiles = 0;
                    var totalFiles = 0;

                    this.on("addedfiles", function(files) {
                        totalFiles = myDropzone.files.length;
                    });

                    this.on("sending", function(file, xhr, formData) {
                        formData.append("_csrfToken", '<?= $this->request->getAttribute('csrfToken') ?>');
                    });

                    this.on("success", function(file, response) {
                        if (response.success) {
                            completedFiles++;
                            file.previewElement.classList.add("dz-success");

                            // Solo recargamos la página cuando todos los archivos se han subido
                            if (completedFiles === totalFiles) {
                                setTimeout(function() {
                                    location.reload();
                                }, 1000); // Pequeño retraso para permitir que Dropzone actualice la UI
                            }
                        } else {
                            file.previewElement.classList.add("dz-error");

                            // Comprobar si existe _removeLink antes de intentar modificarlo
                            if (file._removeLink) {
                                file._removeLink.textContent = "Eliminar";
                            }

                            let message = response.message || "Error al subir el archivo";

                            // Asegurarse de que existe el elemento para el mensaje de error
                            const errorDisplay = file.previewElement.querySelector("[data-dz-errormessage]");
                            if (errorDisplay) {
                                errorDisplay.textContent = message;
                            }
                        }
                    });

                    this.on("error", function(file, errorMessage) {
                        file.previewElement.classList.add("dz-error");

                        // Comprobar si existe _removeLink antes de intentar modificarlo
                        if (file._removeLink) {
                            file._removeLink.textContent = "Eliminar";
                        }

                        let message = typeof errorMessage === 'string' ? errorMessage : "Error al subir el archivo";

                        // Asegurarse de que existe el elemento para el mensaje de error
                        const errorDisplay = file.previewElement.querySelector("[data-dz-errormessage]");
                        if (errorDisplay) {
                            errorDisplay.textContent = message;
                        }
                    });

                    this.on("queuecomplete", function() {
                        // Este evento se dispara cuando todos los archivos en la cola han terminado
                        // No hacemos nada aquí, porque la recarga la manejamos en el evento success
                    });
                }
            });
        }
    });

    // Funciones para gestionar imágenes
    function actualizarTipoImagen(id, tipo) {
        actualizarDatosImagen(id, 'tipo_imagen_id', tipo);
    }

    function actualizarDatosImagen(id, campo, valor) {
        fetch('<?= $this->Url->build(['action' => 'actualizarImagen']) ?>/' + id, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= $this->request->getAttribute('csrfToken') ?>'
                },
                body: JSON.stringify({
                    [campo]: valor
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Error actualizando imagen:', data.message);
                    // Aquí podrías mostrar una notificación de error
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function cambiarPosicion(id, direccion) {
        fetch('<?= $this->Url->build(['action' => 'cambiarPosicionImagen']) ?>/' + id, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= $this->request->getAttribute('csrfToken') ?>'
                },
                body: JSON.stringify({
                    direccion: direccion
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    console.error('Error cambiando posición:', data.message);
                }
            });
    }

    function eliminarImagen(id) {
        if (!confirm('¿Está seguro de eliminar esta imagen?')) {
            return;
        }

        fetch('<?= $this->Url->build(['action' => 'eliminarImagen']) ?>/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-Token': '<?= $this->request->getAttribute('csrfToken') ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const contenedor = document.getElementById('imagen-contenedor-' + id);
                    contenedor.remove();
                } else {
                    console.error('Error eliminando imagen:', data.message);
                }
            });
    }


    // Ayudantes para el formulario
    document.querySelector('select[name="tipo"]').addEventListener('change', function(e) {
        const hint = document.getElementById('hint-multimedia');
        const tipo = e.target.value;

        switch (tipo) {
            case 'video':
                hint.textContent = 'Pegue aquí el código de inserción del video (iframe)';
                break;
            case 'slider':
                hint.textContent = 'URLs de las imágenes separadas por comas';
                break;
            case 'codigo':
                hint.textContent = 'Pegue aquí el código HTML/JavaScript a insertar';
                break;
            default:
                hint.textContent = '';
        }
    });

    function actualizarDocumento(id, campo, valor) {
        fetch('<?= $this->Url->build(['action' => 'actualizarDocumento']) ?>/' + id, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= $this->request->getAttribute('csrfToken') ?>'
                },
                body: JSON.stringify({
                    [campo]: valor
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Error actualizando documento:', data.message);
                }
            });
    }

    function eliminarDocumento(id) {
        if (!confirm('¿Está seguro de eliminar este documento?')) {
            return;
        }

        fetch('<?= $this->Url->build(['action' => 'eliminarDocumento']) ?>/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-Token': '<?= $this->request->getAttribute('csrfToken') ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error al eliminar el documento');
                }
            });
    }



    // --------------------TRATAMIENTO MULTIMEDIA --------------------
    document.getElementById('form-multimedia').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('articulo_id', '<?= $articulo->id ?? 0 ?>');

        fetch('<?= $this->Url->build(['action' => 'agregarMultimedia', $articulo->id]) ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': '<?= $this->request->getAttribute('csrfToken') ?>'
                },
                body: formData // Quitar los headers ya que FormData los configura automáticamente
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modal-multimedia'));
                    modal.hide();
                    location.reload();
                } else {
                    alert(data.message || 'Error al agregar contenido multimedia');
                }
            });
    });

    // Limpiar formulario al cerrar modal
    document.getElementById('modal-multimedia').addEventListener('hidden.bs.modal', function() {
        document.getElementById('form-multimedia').reset();
        document.getElementById('hint-multimedia').textContent = '';
    });

    function editarMultimedia(id) {
        // Implementar edición
    }

    function eliminarMultimedia(id) {
        if (!confirm('¿Está seguro de eliminar este contenido multimedia?')) {
            return;
        }

        fetch('<?= $this->Url->build(['action' => 'eliminarMultimedia']) ?>/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-Token': '<?= $this->request->getAttribute('csrfToken') ?>',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error al eliminar contenido multimedia');
                }
            });
    }


    document.addEventListener('DOMContentLoaded', function() {
        const tituloInput = document.getElementById('titulo');
        const slugInput = document.getElementById('slug');
        const btnResetSlug = document.getElementById('btn-reset-slug');
        const btnToggleAutoslug = document.getElementById('btn-toggle-autoslug');
        const slugStatus = document.getElementById('slug-status');

        let autoSlug = true; // Estado inicial: generación automática activada

        // Función para convertir texto a slug
        function stringToSlug(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-') // Reemplazar espacios con -
                .replace(/[^\w\-]+/g, '') // Eliminar caracteres no válidos
                .replace(/\-\-+/g, '-') // Reemplazar múltiples - con uno solo
                .replace(/^-+/, '') // Eliminar - del inicio
                .replace(/-+$/, ''); // Eliminar - del final
        }

        // Función para actualizar el slug desde el título
        function updateSlugFromTitle() {
            if (autoSlug && tituloInput.value) {
                slugInput.value = stringToSlug(tituloInput.value);
            }
        }

        // Escuchar cambios en el título
        tituloInput.addEventListener('input', updateSlugFromTitle);

        // Escuchar cuando el usuario edita manualmente el slug
        slugInput.addEventListener('input', function() {
            // Si el usuario edita manualmente, desactivamos la generación automática
            if (autoSlug && slugInput.value !== stringToSlug(tituloInput.value)) {
                autoSlug = false;
                btnToggleAutoslug.classList.remove('active');
                slugStatus.textContent = 'Generación automática desactivada';
                slugStatus.classList.add('text-muted');
            }
        });

        // Botón para restablecer slug desde el título
        btnResetSlug.addEventListener('click', function() {
            if (tituloInput.value) {
                slugInput.value = stringToSlug(tituloInput.value);
            }
        });

        // Botón para activar/desactivar generación automática
        btnToggleAutoslug.addEventListener('click', function() {
            autoSlug = !autoSlug;

            if (autoSlug) {
                btnToggleAutoslug.classList.add('active');
                slugStatus.textContent = 'Generación automática activada';
                slugStatus.classList.remove('text-muted');
                updateSlugFromTitle(); // Actualizar inmediatamente
            } else {
                btnToggleAutoslug.classList.remove('active');
                slugStatus.textContent = 'Generación automática desactivada';
                slugStatus.classList.add('text-muted');
            }
        });
    });






    // -------------------- INICIALIZACIÓN DE TINY MCE --------------------
    tinymce.init({
        selector: '#detalle, #copete',
        language: 'es',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | image | link | media | articulo_imagen articulo_documento | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
        // Configuración de imágenes
        image_advtab: true,
        // Agregar botones personalizados
        setup: function(editor) {
            // Botón para insertar imágenes del artículo
            editor.ui.registry.addButton('articulo_imagen', {
                icon: 'image',
                tooltip: 'Insertar imagen del artículo',
                onAction: function() {
                    // Abrir modal de selección de imágenes
                    mostrarSelectorImagenes(editor);
                }
            });

            // Botón para insertar documentos del artículo
            editor.ui.registry.addButton('articulo_documento', {
                icon: 'document',
                tooltip: 'Insertar enlace a documento',
                onAction: function() {
                    // Abrir modal de selección de documentos
                    mostrarSelectorDocumentos(editor);
                }
            });

            editor.on('change', function() {
                editor.save();
            });
        },
        init_instance_callback: function(editor) {
            editor.on('blur', function() {
                editor.save();
            });
        }
    });

    // Función para mostrar selector de imágenes
    function mostrarSelectorImagenes(editor) {
        // Obtener las imágenes del artículo mediante AJAX
        fetch('<?= $this->Url->build(['action' => 'listarRecursosArticulo', $articulo->id ?? 0, 'imagenes']) ?>')
            .then(response => response.json())
            .then(data => {
                // Verificación defensiva
                if (!data || !data.success || !data.imagenes || data.imagenes.length === 0) {
                    alert('No hay imágenes disponibles para insertar');
                    return;
                }

                // Crear HTML para la ventana modal
                let modalHtml = `
                <div class="modal-header">
                    <h5 class="modal-title">Seleccionar imagen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row row-cards">`;

                // Agregar cada imagen como una tarjeta
                data.imagenes.forEach(imagen => {
                    modalHtml += `
                    <div class="col-sm-6 col-lg-4 mb-3">
                        <div class="card">
                            <img src="${imagen.file_path}" class="card-img-top" style="height: 150px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">${imagen.title_data || 'Sin título'}</h5>
                                <button type="button" class="btn btn-primary btn-sm" 
                                    onclick="insertarImagenEnEditor('${imagen.file_path}', '${imagen.alt_data || ''}', '${imagen.title_data || ''}', '${editor.id}')">
                                    Insertar imagen
                                </button>
                            </div>
                        </div>
                    </div>`;
                });

                modalHtml += `
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>`;

                // Mostrar la ventana modal
                const modalEl = document.getElementById('modal-selector');
                modalEl.querySelector('.modal-content').innerHTML = modalHtml;

                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            });
    }

    // Función para insertar imagen en el editor
    function insertarImagenEnEditor(src, alt, title, editorId) {
        // Obtener todas las instancias de TinyMCE
        const allEditors = tinymce.editors;

        // Si tenemos un ID específico, intentamos usarlo
        let editor = null;
        if (editorId) {
            editor = tinymce.get(editorId);
        }

        // Si no encontramos el editor por ID, usar el primer editor disponible (normalmente 'detalle')
        if (!editor && allEditors.length > 0) {
            editor = allEditors[0];
        }

        // Si todavía no tenemos editor, mostrar error
        if (!editor) {
            console.error('No se pudo encontrar el editor TinyMCE');
            alert('Error al insertar la imagen. Por favor, inténtelo de nuevo.');
            return;
        }

        // Insertar el contenido
        editor.insertContent(`<img src="${src}" alt="${alt}" title="${title}" class="img-fluid" />`);

        // Cerrar la modal
        const modalEl = document.getElementById('modal-selector');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
    }

    // Función para mostrar selector de documentos
    function mostrarSelectorDocumentos(editor) {
        // Obtener los documentos del artículo mediante AJAX
        fetch('<?= $this->Url->build(['action' => 'listarRecursosArticulo', $articulo->id ?? 0, 'documentos']) ?>')
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta del servidor para documentos:', data);
                // Verificación defensiva
                if (!data || !data.success || !data.documentos || data.documentos.length === 0) {
                    alert('No hay documentos disponibles para insertar');
                    return;
                }

                // Crear HTML para la ventana modal
                let modalHtml = `
                <div class="modal-header">
                    <h5 class="modal-title">Seleccionar documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Archivo</th>
                                    <th>Tipo</th>
                                    <th>Tamaño</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>`;

                // Agregar cada documento como una fila
                data.documentos.forEach(documento => {
                    const fileIcon = getFileIconClass(documento.mime_type);
                    modalHtml += `
                    <tr>
                        <td>${documento.titulo || documento.file_name}</td>
                        <td>${documento.file_name}</td>
                        <td><i class="${fileIcon}"></i> ${documento.mime_type}</td>
                        <td>${formatFileSize(documento.file_size)}</td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" 
                                onclick="insertarDocumentoEnEditor('${documento.id}', '${documento.titulo || documento.file_name}', '${editor.id}')">
                                Insertar enlace
                            </button>
                        </td>
                    </tr>`;
                });

                modalHtml += `
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>`;

                // Mostrar la ventana modal
                const modalEl = document.getElementById('modal-selector');
                modalEl.querySelector('.modal-content').innerHTML = modalHtml;

                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            });
    }

    // Función para insertar documento en el editor
    function insertarDocumentoEnEditor(documentoId, titulo, editorId) {
        // Obtener todas las instancias de TinyMCE
        const allEditors = tinymce.editors;

        // Si tenemos un ID específico, intentamos usarlo
        let editor = null;
        if (editorId) {
            editor = tinymce.get(editorId);
        }

        // Si no encontramos el editor por ID, usar el primer editor disponible
        if (!editor && allEditors.length > 0) {
            editor = allEditors[0];
        }

        // Si todavía no tenemos editor, mostrar error
        if (!editor) {
            console.error('No se pudo encontrar el editor TinyMCE');
            alert('Error al insertar el documento. Por favor, inténtelo de nuevo.');
            return;
        }

        const url = '<?= $this->Url->build(['action' => 'descargarDocumento']) ?>/' + documentoId;
        editor.insertContent(`<a href="${url}" target="_blank" class="document-link"><i class="fas fa-file me-1"></i>${titulo}</a>`);

        // Cerrar la modal
        const modalEl = document.getElementById('modal-selector');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
    }

    // Función para obtener ícono según tipo de archivo
    function getFileIconClass(mimeType) {
        if (mimeType.includes('pdf')) return 'fas fa-file-pdf';
        if (mimeType.includes('word') || mimeType.includes('doc')) return 'fas fa-file-word';
        if (mimeType.includes('excel') || mimeType.includes('sheet')) return 'fas fa-file-excel';
        if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'fas fa-file-powerpoint';
        if (mimeType.includes('zip') || mimeType.includes('compressed')) return 'fas fa-file-archive';
        if (mimeType.includes('text')) return 'fas fa-file-alt';
        return 'fas fa-file';
    }

    // Función para formatear tamaño de archivo
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Asegurar que TinyMCE actualice todos los textareas antes del envío
    document.getElementById('articuloForm').addEventListener('submit', function() {
        tinymce.triggerSave();
    });
</script>
<?php $this->end(); ?>