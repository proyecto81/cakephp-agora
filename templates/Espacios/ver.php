<?php

/**
 * Vista para mostrar detalles de un espacio
 * 
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Ver Espacio');

// Clases para badges de estado
$badgeClass = 'bg-secondary';
switch ($espacio->estado) {
    case 'borrador':
        $badgeClass = 'bg-blue';
        break;
    case 'verificado':
        $badgeClass = 'bg-yellow';
        break;
    case 'publicado':
        $badgeClass = 'bg-green';
        break;
    case 'rechazado':
        $badgeClass = 'bg-red';
        break;
}
if ($espacio->eliminado == 1) {
    $badgeClass = 'bg-secondary';
}
?>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Detalles del Espacio
                </h2>
                <div class="text-muted mt-1">
                    ID: <?= $espacio->id ?> - Creado: <?= $espacio->created->format('d/m/Y H:i') ?>
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?= $this->request->referer() ?>" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i>
                        Volver
                    </a>
                    <?php if ($espacio->estado === 'publicado' && !$espacio->eliminado): ?>
                        <a href="<?= $this->Url->build(['prefix' => false, 'plugin' => null, 'controller' => 'Espacios', 'action' => 'detalle', $espacio->hash_completo]) ?>" class="btn btn-primary" target="_blank">
                            <i class="ti ti-external-link"></i>
                            Ver en el sitio
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información Principal</h3>
                        <div class="card-actions">
                            <span class="badge <?= $badgeClass ?>">
                                <?= h($espacio->eliminado ? 'Eliminado' : $espacio->estado) ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Título</div>
                                <div class="datagrid-content"><?= h($espacio->titulo) ?></div>
                            </div>

                            <div class="datagrid-item">
                                <div class="datagrid-title">Tipo</div>
                                <div class="datagrid-content">
                                    <span class="badge bg-primary"><?= h($espacio->tipo_espacio->valor) ?></span>
                                </div>
                            </div>

                            <div class="datagrid-item">
                                <div class="datagrid-title">Organizador</div>
                                <div class="datagrid-content"><?= h($espacio->organizador) ?></div>
                            </div>

                            <div class="datagrid-item">
                                <div class="datagrid-title">Email</div>
                                <div class="datagrid-content"><?= h($espacio->email) ?></div>
                            </div>

                            <?php if ($espacio->tipo_espacio_id == 2): // Solo para eventos 
                            ?>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Fecha de inicio</div>
                                    <div class="datagrid-content">
                                        <?= $espacio->fecha_inicio ? $espacio->fecha_inicio->format('d/m/Y H:i') : 'No especificada' ?>
                                    </div>
                                </div>

                                <div class="datagrid-item">
                                    <div class="datagrid-title">Fecha de fin</div>
                                    <div class="datagrid-content">
                                        <?= $espacio->fecha_fin ? $espacio->fecha_fin->format('d/m/Y H:i') : 'No especificada' ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="datagrid-item">
                                <div class="datagrid-title">Dirección</div>
                                <div class="datagrid-content"><?= h($espacio->direccion) ?></div>
                            </div>

                            <?php if (!empty($espacio->link_mas_info)): ?>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Enlace</div>
                                    <div class="datagrid-content">
                                        <a href="<?= h($espacio->link_mas_info) ?>" target="_blank" rel="noopener">
                                            <?= h($espacio->link_mas_info) ?>
                                            <i class="ti ti-external-link ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="datagrid-item">
                                <div class="datagrid-title">Fecha de creación</div>
                                <div class="datagrid-content"><?= $espacio->created->format('d/m/Y H:i') ?></div>
                            </div>

                            <?php if ($espacio->eliminado): ?>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Fecha de eliminación</div>
                                    <div class="datagrid-content">
                                        <?= $espacio->fecha_eliminacion ? $espacio->fecha_eliminacion->format('d/m/Y H:i') : 'No disponible' ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descripción -->
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Descripción</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($espacio->descripcion)): ?>
                            <div class="p-3 bg-light rounded">
                                <?= nl2br(h($espacio->descripcion)) ?>
                            </div>
                        <?php else: ?>
                            <div class="text-muted">Sin descripción</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Ubicación en el mapa -->
            <?php if (!empty($espacio->latitud) && !empty($espacio->longitud)): ?>
                <div class="col-12 mt-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ubicación</h3>
                        </div>
                        <div class="card-body p-0">
                            <div id="map-container" style="height: 300px; width: 100%;">
                                <div id="map" style="height: 100%; width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Acciones -->
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Acciones</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!$espacio->eliminado): ?>
                            <div class="d-flex flex-wrap gap-2">
                                <?php if ($espacio->estado !== 'publicado'): ?>
                                    <?= $this->Form->postLink(
                                        '<i class="ti ti-world"></i> Publicar',
                                        ['action' => 'cambiarEstado', $espacio->id, 'publicado'],
                                        ['class' => 'btn btn-success', 'escape' => false, 'confirm' => '¿Publicar este espacio?']
                                    ) ?>
                                <?php endif; ?>

                                <?php if ($espacio->estado !== 'borrador'): ?>
                                    <?= $this->Form->postLink(
                                        '<i class="ti ti-file"></i> Cambiar a borrador',
                                        ['action' => 'cambiarEstado', $espacio->id, 'borrador'],
                                        ['class' => 'btn btn-outline-primary', 'escape' => false, 'confirm' => '¿Cambiar a estado borrador?']
                                    ) ?>
                                <?php endif; ?>

                                <?php if ($espacio->estado !== 'verificado'): ?>
                                    <?= $this->Form->postLink(
                                        '<i class="ti ti-check"></i> Verificar',
                                        ['action' => 'cambiarEstado', $espacio->id, 'verificado'],
                                        ['class' => 'btn btn-primary', 'escape' => false, 'confirm' => '¿Verificar este espacio?']
                                    ) ?>
                                <?php endif; ?>

                                <?php if ($espacio->estado !== 'rechazado'): ?>
                                    <?= $this->Form->postLink(
                                        '<i class="ti ti-x"></i> Rechazar',
                                        ['action' => 'cambiarEstado', $espacio->id, 'rechazado'],
                                        ['class' => 'btn btn-danger', 'escape' => false, 'confirm' => '¿Rechazar este espacio?']
                                    ) ?>
                                <?php endif; ?>

                                <div class="ms-auto">
                                    <?= $this->Form->postLink(
                                        '<i class="ti ti-trash"></i> Eliminar',
                                        ['action' => 'eliminar', $espacio->id],
                                        ['class' => 'btn btn-outline-danger', 'escape' => false, 'confirm' => '¿Enviar este espacio a la papelera?']
                                    ) ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="d-flex">
                                <?= $this->Form->postLink(
                                    '<i class="ti ti-arrow-back-up"></i> Recuperar',
                                    ['action' => 'recuperar', $espacio->id],
                                    ['class' => 'btn btn-primary', 'escape' => false, 'confirm' => '¿Recuperar este espacio de la papelera?']
                                ) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($espacio->latitud) && !empty($espacio->longitud)): ?>
    <!-- Script para el mapa -->
    <?php $this->Html->scriptBlock("
    // Inicializar el mapa cuando la página cargue
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar si la API de Google Maps está cargada
        if (typeof google !== 'undefined') {
            initMap();
        } else {
            // Si no está cargada, cargarla dinámicamente
            loadGoogleMapsAPI();
        }
    });

    // Función para cargar la API de Google Maps
    function loadGoogleMapsAPI() {
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBXQUzPVhX9YEKLXjXJzJeaN2g6Mve44Wc&callback=initMap';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }

    // Inicializar el mapa
    function initMap() {
        const lat = {$espacio->latitud};
        const lng = {$espacio->longitud};
        const mapDiv = document.getElementById('map');

        // Crear el mapa
        const map = new google.maps.Map(mapDiv, {
            center: { lat: lat, lng: lng },
            zoom: 15,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: true,
            streetViewControl: true,
            fullscreenControl: true
        });

        // Añadir marcador
        const marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: map,
            title: '" . addslashes($espacio->titulo) . "'
        });
    }
", ['block' => 'script']); ?>
<?php endif; ?>