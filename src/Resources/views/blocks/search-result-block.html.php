<?php

use App\Helper\View\UrlHelper;
use App\Entity\AbstractAddressableEntity;

/** @var AbstractAddressableEntity[] $entities */
?>

<section class="categories search-articles search">
    <div class="container">
        <div class="categories-wrapper">
            <div>
                <?php foreach ($entities as $entity): ?>
                    <div class="category-item"
                         onclick="goToPath(event, '<?= UrlHelper::getEntityUrl($entity) ?>')"
                    >
                        <div class="category-content <?= $entity->description ? '' : 'without-description' ?>">
                            <h4 title="<?= $entity->title ?>">
                                <a href="<?= UrlHelper::getEntityUrl($entity) ?>" rel="nofollow">
                                    <?= $entity->title ?>
                                </a>
                            </h4>

                            <?php if ($entity->description): ?>
                                <p title="<?= $entity->description ?>"><?= $entity->description ?></p>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
