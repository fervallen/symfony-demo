<?php

use App\Entity\AbstractAddressableEntity;
use Symfony\Component\Templating\PhpEngine;

/** @var array|null $breadcrumbs */
/** @var AbstractAddressableEntity|null $breadcrumbsEntity */
/** @var bool $displayGreeting */
/** @var PhpEngine $view */
?>

<header
    <?php if (!$displayGreeting): ?>
        class="inside-page-header"
    <?php endif ?>
>
    <div class="container">
        <?= $view->render('blocks/search-form.html.php') ?>
    </div>
</header>


