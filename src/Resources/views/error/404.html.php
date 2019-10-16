<?php

use App\Helper\View\UrlHelper;
use App\Helper\View\I18ns;
use App\Helper\View\TranslateHelper;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

/** @var string[] $metaData */
/** @var PhpEngine $view */
/** @var Package $package */

$view->extend('layout.html.php');
$package = new Package(new EmptyVersionStrategy());
?>
<?= $view->render('blocks/header.html.php', [
    'displayGreeting' => true,
]) ?>

<main>
    <section class="page-header error-404">
        <div class="container">
            <p class="first-string"><?= TranslateHelper::text(I18ns::ERROR_PAGE_MESSAGE_SUBTITLE) ?></p>

            <h1><?= TranslateHelper::text(I18ns::ERROR_PAGE_MESSAGE) ?></h1>
            <div class="page-header-description">
                <?= TranslateHelper::text(I18ns::PAGE_NOT_FOUND_MESSAGE) ?>
            </div>
            <div>
                <?= TranslateHelper::text(I18ns::PAGE_NOT_FOUND_BACK_LINK) ?>
            </div>
        </div>
    </section>
</main>

<?= $view->render('blocks/footer.html.php') ?>

