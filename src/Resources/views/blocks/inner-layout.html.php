<?php

use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\PhpEngine;

/** @var PhpEngine $view */

$view->extend('layout.html.php');
?>

<?= $view->render('blocks/header.html.php', [
    'displayGreeting' => $displayGreeting ?? false,
]) ?>

<?php
    /** @var SlotsHelper $slots */
    $slots = $view['slots'];
    $slots->output('_content')
?>

<?= $view->render('blocks/footer.html.php') ?>
