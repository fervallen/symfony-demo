<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Response\InnerErrorCodes;
use App\Service\SearchService;
use App\Traits\QueryParametersParserTrait;
use App\Traits\SearchTrait;
use Helpcrunch\Annotation;
use Helpcrunch\Response\ErrorResponse;
use Helpcrunch\Response\SuccessResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends BaseApiController
{
    use SearchTrait, QueryParametersParserTrait;

    const DEFAULT_SEARCH_RESULTS_LIMIT = 10;

    /**
     * @Annotation\UnauthorizedAction()
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchAction(Request $request): JsonResponse
    {
        if (empty($request->query->get('searchQuery'))) {
            return new ErrorResponse('No search query provided', InnerErrorCodes::MISSING_PARAMETER);
        }

        /** @var SearchService $searchService */
        $searchService = $this->container->get(SearchService::class);
        if (!$request->query->has('limit')) {
            $request->query->set('limit', self::DEFAULT_SEARCH_RESULTS_LIMIT);
        }

        return new SuccessResponse($this->searchEntities($request, $searchService));
    }
}
