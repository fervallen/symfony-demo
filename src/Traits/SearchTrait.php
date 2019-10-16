<?php

namespace App\Traits;

use App\Entity\AbstractAddressableEntity;
use Helpcrunch\Helper\FormatterHelper;
use Helpcrunch\Helper\ParametersValidatorHelper;
use App\Service\SearchService;
use Symfony\Component\HttpFoundation\Request;

trait SearchTrait
{
    /**
     * @param Request $request
     * @param SearchService $searchService
     * @return AbstractAddressableEntity[]
     */
    protected function searchEntities(Request $request, SearchService $searchService): array
    {
        $queryString = $request->query->get('searchQuery', null);
        if (!ParametersValidatorHelper::isStringAndNotEmpty($queryString)) {
            return [];
        }

        $clearedQueryString = FormatterHelper::clearString($queryString);
        $options = $this->parseQueryOptions($request);
        $result = $searchService->search($clearedQueryString, $options);

        return $result;
    }
}
