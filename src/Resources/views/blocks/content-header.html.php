<?php

/** @var Object $entity */
/** @var String $additionalPageClass */
?>

<section class="page-header <?= $additionalPageClass ?>">
    <div class="container">
        <h1><?= $entity->title ?></h1>
        <span class="page-header-description">
            <?= $entity->description ?>
        </span>
    </div>
</section>
