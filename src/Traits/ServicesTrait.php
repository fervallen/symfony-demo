<?php

namespace App\Traits;

use App\Repository\ArticleRepository;
use App\Repository\ArticleRevisionRepository;
use App\Repository\CategoryRepository;
use App\Repository\KnowledgeBaseRepository;
use App\Repository\MenuItemRepository;
use App\Repository\OrganizationRepository;
use App\Repository\RatingRepository;
use App\Repository\SearchQueryLogRepository;
use App\Repository\SectionRepository;
use App\Service\DBConnectionService;
use App\Service\Rating\ArticleRatingFillerService;
use App\Service\Rating\SectionRatingFillerService;
use App\Service\SearchService;
use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Symfony\Component\DependencyInjection\Container;

/**
 * @method SearchService getSearchService
 *
 * @method ArticleRepository getArticleRepository
 * @method SearchQueryLogRepository getSearchQueryLogRepository
 *
 * @property Container container
*/
trait ServicesTrait
{
    use HelpcrunchServicesTrait;

    public function __call($name, $arguments): object
    {
        if (preg_match('/^get(.*)Repository$/', $name, $matches)) {
            if (class_exists("App\Entity\ApiEntity\\{$matches[1]}")) {
                return $this->container->get("App\Repository\\{$matches[1]}Repository");
            }

            $className = "App\Entity\\{$matches[1]}";
            return $this->createRepository($className);
        }

        if (preg_match('/^get(.*)Service/', $name, $matches)) {
            $className = "App\\Service\\{$matches[1]}Service";
        }
        if (empty($className)) {
            throw new \BadMethodCallException("Undefined method $name");
        }

        return $this->container->get($className);
    }

    private function createRepository(string $className): object
    {
        return $this->getEntityManager()->getRepository($className);
    }
}
