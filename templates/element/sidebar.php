<?php

/**
 * @var \App\View\AppView $this
 */
?>
<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>

        <h1 class="navbar-brand navbar-brand-autodark">
            <?= $this->Html->link(
                $this->Html->image('/agora/img/logo.png', [
                    'alt' => 'Agora Backend',
                    'class' => 'navbar-brand-image'
                ]),
                ['plugin' => 'Agora', 'controller' => 'Backend', 'action' => 'index'],
                ['escape' => false]
            ) ?>
        </h1>

        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v8h-6z"/><path d="M4 16h6v4h-6z"/><path d="M14 12h6v8h-6z"/><path d="M14 4h6v4h-6z"/></svg></span>
                        <span class="nav-link-title">Dashboard</span>',
                        ['plugin' => 'Agora', 'controller' => 'Backend', 'action' => 'index'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>

                <!-- Espacios -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M18.364 4.636a9 9 0 0 1 .203 12.519l-.203 .21l-4.243 4.242a3 3 0 0 1 -4.097 .135l-.144 -.135l-4.244 -4.243a9 9 0 0 1 12.728 -12.728zm-6.364 3.364a3 3 0 1 0 0 6a3 3 0 0 0 0 -6z" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Espacios</span>
                    </a>
                    <div class="dropdown-menu">
                        <?= $this->Html->link(
                            'Dashboard',
                            ['plugin' => 'Agora', 'controller' => 'Espacios', 'action' => 'index'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Pendientes',
                            ['plugin' => 'Agora', 'controller' => 'Espacios', 'action' => 'listar', 'borrador'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Publicados',
                            ['plugin' => 'Agora', 'controller' => 'Espacios', 'action' => 'listar', 'publicado'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Todos',
                            ['plugin' => 'Agora', 'controller' => 'Espacios', 'action' => 'listar', 'todos'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Ver Mapa',
                            ['plugin' => 'Agora', 'controller' => 'Espacios', 'action' => 'mapa'],
                            ['class' => 'dropdown-item']
                        ) ?>
                    </div>
                </li>

                <!-- Blog -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Blog</span>
                    </a>
                    <div class="dropdown-menu">
                        <?= $this->Html->link(
                            'Artículos',
                            ['plugin' => 'Agora', 'controller' => 'Articulos', 'action' => 'index'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Nuevo Artículo',
                            ['plugin' => 'Agora', 'controller' => 'Articulos', 'action' => 'crear'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Categorías',
                            ['plugin' => 'Agora', 'controller' => 'ArticulosCategorias', 'action' => 'index'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Etiquetas',
                            ['plugin' => 'Agora', 'controller' => 'ArticulosEtiquetas', 'action' => 'index'],
                            ['class' => 'dropdown-item']
                        ) ?>
                    </div>
                </li>

                <!-- Partidas -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                                <path d="M12 12l8 -4.5" />
                                <path d="M12 12l0 9" />
                                <path d="M12 12l-8 -4.5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Partidas</span>
                    </a>
                    <div class="dropdown-menu">
                        <?= $this->Html->link(
                            'Listado',
                            ['plugin' => 'Agora', 'controller' => 'Partidas', 'action' => 'index'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Nueva Partida',
                            ['plugin' => 'Agora', 'controller' => 'Partidas', 'action' => 'crear'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Reportes',
                            ['plugin' => 'Agora', 'controller' => 'Partidas', 'action' => 'reportes'],
                            ['class' => 'dropdown-item']
                        ) ?>
                    </div>
                </li>

                <!-- Usuarios -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Usuarios</span>
                    </a>
                    <div class="dropdown-menu">
                        <?= $this->Html->link(
                            'Listado',
                            ['plugin' => 'Agora', 'controller' => 'Users', 'action' => 'index'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Moderadores',
                            ['plugin' => 'Agora', 'controller' => 'Users', 'action' => 'moderadores'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Reportes',
                            ['plugin' => 'Agora', 'controller' => 'Users', 'action' => 'reportes'],
                            ['class' => 'dropdown-item']
                        ) ?>
                    </div>
                </li>

                <!-- Juegos -->
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<span class="nav-link-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 13l-3 -3l3 -3l3 3l-3 3z"/><path d="M5 8l7 -4l7 4l-7 4z"/><path d="M12 20l-7 -4v-8l7 4z"/><path d="M12 20l7 -4v-8l-7 4z"/></svg></span>
                        <span class="nav-link-title">Juegos</span>',
                        ['plugin' => 'Agora', 'controller' => 'Juegos', 'action' => 'index'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>

                <!-- Comunidades -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 21h18" />
                                <path d="M19 21v-4" />
                                <path d="M19 17a2 2 0 0 0 2 -2v-2a2 2 0 1 0 -4 0v2a2 2 0 0 0 2 2z" />
                                <path d="M14 21v-14a3 3 0 0 0 -3 -3h-4a3 3 0 0 0 -3 3v14" />
                                <path d="M9 17v4" />
                                <path d="M8 13h2" />
                                <path d="M8 9h2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Comunidades</span>
                    </a>

                    <div class="dropdown-menu">
                        <?= $this->Html->link(
                            'Crear',
                            ['plugin' => 'Agora', 'controller' => 'Comunidades', 'action' => 'crear'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Listado',
                            ['plugin' => 'Agora', 'controller' => 'Comunidades', 'action' => 'index'],
                            ['class' => 'dropdown-item']
                        ) ?>
                    </div>
                </li>

                <!-- Configuracion -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Configuración</span>
                    </a>
                    <div class="dropdown-menu">
                        <?= $this->Html->link(
                            'General',
                            ['plugin' => 'Agora', 'controller' => 'Config', 'action' => 'general'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Roles y Permisos',
                            ['plugin' => 'Agora', 'controller' => 'Config', 'action' => 'roles'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Logs del Sistema',
                            ['plugin' => 'Agora', 'controller' => 'Config', 'action' => 'logs'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Verificaciones',
                            ['plugin' => 'Agora', 'controller' => 'Verificaciones', 'action' => 'perfiles'],
                            ['class' => 'dropdown-item']
                        ) ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</aside>