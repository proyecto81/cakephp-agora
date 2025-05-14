<?= $this->Html->css('https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css') ?>

<div class="container-xl">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Gestión de Partidas</h3>
            <div class="card-actions">
                <?= $this->Html->link(__('Nueva Partida'), ['action' => 'crear'], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-vcenter" id="partidasTable">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Juego</th>
                        <th>Master</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($partidas as $partida): ?>
                        <tr>
                            <td><?= h($partida->titulo) ?></td>
                            <td><?= $partida->juego->titulo ?></td>
                            <td><?= $partida->cuenta->nickname ?></td>
                            <td><?= $partida->estado->valor ?></td>
                            <td><?= $partida->fecha_hora_sesion->format('d/m/Y H:i') ?></td>
                            <td class="text-end">
                                <?= $this->Html->link(__('Editar'), ['action' => 'editar', $partida->id], ['class' => 'btn btn-primary btn-sm']) ?>
                                <?= $this->Form->postLink(__('Eliminar'), ['action' => 'eliminar', $partida->id], ['confirm' => __('¿Eliminar partida {0}?', $partida->titulo), 'class' => 'btn btn-danger btn-sm']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->Html->script('https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js') ?>
<?= $this->Html->script('https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js') ?>
<script>
    $(document).ready(function() {
        $('#partidasTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            }
        });
    });
</script>