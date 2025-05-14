<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Articulo $articulo
 * @var array $categorias
 * @var array $etiquetas
 * @var array $estados
 */

$this->assign('title', 'Editar Artículo: ' . h($articulo->titulo));
$this->Breadcrumbs->add([
    ['title' => 'Inicio', 'url' => '/'],
    ['title' => 'Artículos', 'url' => ['action' => 'index']],
    ['title' => 'Editar Artículo']
]);

// Incluir el template del formulario
echo $this->element('Agora.Articulos/form');
