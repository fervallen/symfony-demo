<?php

namespace App\EventSubscriber;

use App\Entity\SearchQueryLog;
use App\Event\ArticleSearchEvent;
use App\Traits\ServicesTrait;
use Helpcrunch\Validator\Validator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchLoggerSubscriber implements EventSubscriberInterface
{
    use ServicesTrait;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onArticleSearch(ArticleSearchEvent $event)
    {
        $queryLog = new SearchQueryLog();
        $queryLogData = [
            'text' => $event->searchQuery,
            'resultsCount' => $event->resultsCount,
            'customerId' => $event->customerId,
        ];

        $validator = new Validator($this->container);
        if ($queryLog = $validator->isValid($queryLog, $queryLogData)) {
            $this->getEntityManager()->persist($queryLog);
            $this->getEntityManager()->flush();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ArticleSearchEvent::SEARCH_EVENT => 'onArticleSearch',
        ];
    }
}
