<?php

namespace App\Event;

use Helpcrunch\Entity\GetterSetterTrait;
use Symfony\Component\EventDispatcher\Event;

/**
 * @property string $searchQuery
 * @property int $resultsCount
 * @property int|null $customerId
 */
class ArticleSearchEvent extends Event
{
    use GetterSetterTrait;

    const SEARCH_EVENT = 'article.search';

    /**
     * @var string
     */
    protected $searchQuery;

    /**
     * @var int
     */
    protected $resultsCount;

    /**
     * @var int|null
     */
    protected $customerId = null;
}
