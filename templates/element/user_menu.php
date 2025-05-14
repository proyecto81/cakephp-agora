<div class="nav-item dropdown">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
        <div class="d-none d-xl-block ps-2">
            <div>Juan Carlos Admin<?PHP //echo $this->Identity->get('email') 
                                    ?></div>
            <div class="mt-1 small text-muted">Administrador</div>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <?= $this->Html->link(
            'Salir',
            ['controller' => 'Auth', 'action' => 'logout'],
            ['class' => 'dropdown-item']
        ) ?>
    </div>
</div>
