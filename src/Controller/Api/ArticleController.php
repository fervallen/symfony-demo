<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Traits\QueryParametersParserTrait;
use App\Traits\SearchTrait;
use Helpcrunch\Response\EntitiesBatchResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method ArticleRepository getRepository()
 */
class ArticleController extends BaseApiController
{
    use SearchTrait, QueryParametersParserTrait;

    const DEFAULT_SEARCH_RESULTS_LIMIT = 10;

    /**
     * @var string $entityClassName
     */
    public static $entityClassName = Article::class;

    public function cgetAction(Request $request): JsonResponse
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', static::DEFAULT_PAGINATION_LIMIT);

        $articles = $this->getRepository()->findEntities($offset, $limit);

        return new EntitiesBatchResponse($articles);
    }
}
