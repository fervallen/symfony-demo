<?php

use App\Entity\Article;
use Symfony\Component\Templating\PhpEngine;

/** @var Article $article */
/** @var array $ratings */
/** @var PhpEngine $view */
$view->extend('blocks/inner-layout.html.php');
?>

<script src="/assets/js/lib/storage.js"></script>
<script src="/assets/js/article.js"></script>

<main class="article">
    <section>
        <?= $view->render('blocks/content-header.html.php', [
            'entity' => $article,
            'additionalPageClass' => 'article-header',
        ]) ?>

        <div class="container">
            <div id="article-content">
                <?= $article->content ?>
            </div>
        </div>
    </section>
</main>
