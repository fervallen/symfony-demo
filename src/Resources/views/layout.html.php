<?php

use App\Entity\MenuItem;
use App\Entity\KnowledgeBase;
use App\Helper\View\UrlHelper;
use Helpcrunch\Helper\CdnHelper;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\PhpEngine;

/** @var PhpEngine $view */
/** @var string[] $metaData */

$package = new Package(new EmptyVersionStrategy());
?><!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <title><?= $metaData['pageTitle'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (!empty($metaData['metaDescription'])): ?>
        <meta name="description" content="<?= $metaData['metaDescription'] ?>"/>
    <?php endif ?>
    <?php if (!empty($metaData['metaKeywords'])): ?>
        <meta name="keywords" content="<?= $metaData['metaKeywords'] ?>"/>
    <?php endif ?>
    <?php if (!empty($metaData['ogTitle'])): ?>
        <meta property="og:title" content="<?= $metaData['ogTitle'] ?>">
        <meta name="twitter:title" content="<?= $metaData['ogTitle'] ?>">
    <?php endif ?>
    <?php if (!empty($metaData['ogDescription'])): ?>
        <meta property="og:description" content="<?= $metaData['ogDescription'] ?>">
        <meta name="twitter:description" content="<?= $metaData['ogDescription'] ?>">
        <meta name="twitter:card" content="<?= $metaData['ogDescription'] ?>">
    <?php endif ?>
    <?php if (!empty($metaData['ogImage'])): ?>
        <?php $ogImage = CdnHelper::getUrl($metaData['ogImage']) ?>
        <link href="<?= $ogImage ?>" rel="image_src">
        <meta name="twitter:image" content="<?= $ogImage ?>">
        <meta property="og:image" content="<?= $ogImage ?>">
    <?php endif ?>
    <meta property="og:type" content="website">
    <link href="https://ucr.helpcrunch.com" rel="dns-prefetch">
    <link href="https://widget.helpcrunch.com" rel="dns-prefetch">
    <link href="https://ucarecdn.com" rel="dns-prefetch">
    <link href="https://helpcrunch.com" rel="dns-prefetch">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:100,400,600,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/assets/css/main.min.css">
</head>
<body>
    <script src="/assets/js/main.js"></script>
    <?php
        /** @var SlotsHelper $slots */
        $slots = $view['slots'];
        $slots->output('_content')
    ?>
</body>
</html>
