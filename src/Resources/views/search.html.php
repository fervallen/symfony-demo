<?php

use App\Entity\MenuItem;
use App\Entity\AbstractAddressableEntity;
use App\Entity\KnowledgeBase;
use App\Helper\View\TranslateHelper;
use App\Helper\View\I18ns;
use Symfony\Component\Templating\PhpEngine;

/** @var AbstractAddressableEntity[] $entities */
/** @var int $resultsCount */
/** @var PhpEngine $view */
$view->extend('blocks/inner-layout.html.php');
?>

<main>
    <section class="page-header">
        <div class="container">
            <?php if (empty($entities)): ?>
                <h1>
                    <?= TranslateHelper::text(I18ns::NO_RESULTS) ?>  🙈
                </h1>
            <?php else: ?>
                <h1><?= TranslateHelper::text(I18ns::SEARCH) ?></h1>
                <span class="page-header-description">
                    <?= TranslateHelper::text(I18ns::RESULTS_COUNT, ['%count%' => count($entities)]) ?>
                </span>
            <?php endif ?>
        </div>
    </section>

    <?php if (!empty($entities)): ?>
        <?= $view->render('blocks/search-result-block.html.php', [
            'entities' => $entities,
        ]) ?>
    <?php endif ?>
</main>
