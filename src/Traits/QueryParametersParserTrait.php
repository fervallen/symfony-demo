<?php

namespace App\Traits;

use App\Entity\Article;
use Helpcrunch\Helper\ParametersValidatorHelper;
use Symfony\Component\HttpFoundation\Request;

trait QueryParametersParserTrait
{
    protected function parseQueryOptions(Request $request): array
    {
        $options = [];
        if ($request->query->has('limit')) {
            $options['limit'] = $request->query->get('limit');
        }

        if ($request->query->has('offset')) {
            $options['offset'] = $request->query->get('offset');
        }

        if ($request->query->has('order')) {
            $options['order'] = $request->query->get('order');
        }

        if ($request->query->has('orderBy')) {
            $options['orderBy'] = $this->checkOrderByField($request->query->get('orderBy'));
        }

        return $options;
    }

    /**
     * @param string $orderBy
     * @return null|string
     */
    private function checkOrderByField(string $orderBy)
    {
        if (!ParametersValidatorHelper::isValuePresented($orderBy, Article::ORDER_BY_FIELDS)) {
            $orderBy = null;
        }

        return $orderBy;
    }
}
