<?php

namespace App\Service;

use App\Entity\AbstractAddressableEntity;
use App\Entity\Article;
use App\Event\ArticleSearchEvent;
use App\EventSubscriber\SearchLoggerSubscriber;
use App\Repository\ArticleRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SearchService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->articleRepository = $this->container->get('doctrine')->getRepository(Article::class);
    }

    /**
     * @param string $searchString
     * @param array $options
     * @return AbstractAddressableEntity[]
     */
    public function search(string $searchString, array $options = []): array
    {
        $resultsCount = $this->articleRepository->countArticlesByText($searchString);

        $results = [
            'entities' => $this->searchForArticles($searchString, $options),
            'resultsCount' => $resultsCount,
        ];

        $this->logQuery($searchString, $resultsCount);

        return $results;
    }

    private function searchForArticles(string $searchQuery, array $options): array
    {
        return $this->articleRepository->findArticlesByText($searchQuery, $options);
    }

    private function logQuery(string $searchQuery, int $resultsCount): void
    {
        $event = new ArticleSearchEvent();
        $event->searchQuery = $searchQuery;
        $event->resultsCount = $resultsCount;

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new SearchLoggerSubscriber($this->container));
        $dispatcher->dispatch(ArticleSearchEvent::SEARCH_EVENT, $event);
    }
}
