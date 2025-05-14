<?php

/**
 * Layout Backend basado en Tabler
 * @var \App\View\AppView $this
 */
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title><?= $this->fetch('title') ?> - Backend JuguemosRol XX</title>

    <!-- Jquery -->
    <?= $this->Html->script('https://code.jquery.com/jquery-3.7.1.min.js') ?>

    <!-- CSS base de Tabler -->
    <?= $this->Html->css(['/agora/tabler/css/tabler.min.css?1692870487']) ?>

    <!-- Autocompletado -->
    <?= $this->Html->css('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css') ?>
    <?= $this->Html->script('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js') ?>

    <!-- Otros contenidos -->
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>

<body>
    <div class="page">
        <!-- Sidebar -->
        <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="<?= $this->Url->build('/agora') ?>">
                        JuguemosRol
                    </a>
                </h1>

                <?= $this->element('Agora.sidebar') ?>
            </div>
        </aside>

        <!-- Header -->
        <?= $this->element('Agora.header') ?>

        <!-- Page wrapper -->
        <div class="page-wrapper">

            <!-- Contenido principal -->
            <div class="page-body">
                <div class="container-fluid">
                    <?= $this->Flash->render() ?>
                    <?= $this->fetch('content') ?>
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer footer-transparent d-print-none">
                <div class="container-fluid">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            Copyright © <?= date('Y') ?> JuguemosRol. Todos los derechos reservados.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Tabler Core -->
    <?= $this->Html->script(['/agora/tabler/js/tabler.min.js?1692870487']) ?>

    <?= $this->fetch('script') ?>
</body>

</html>