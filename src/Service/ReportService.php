<?php

namespace App\Service;

use App\Entity\SearchQueryLog;
use App\Traits\ServicesTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ReportService
{
    use ServicesTrait;

    const REPORT_TYPE_FAILED_SEARCH = 'failedSearch';

    const FAILED_SEARCH_RESULTS_COUNT = 0;

    const START_DATE_PARAMETER = 'startDate';
    const END_DATE_PARAMETER = 'endDate';
    const RESULTS_COUNT_PARAMETER = 'resultsCount';
    const LIMIT_PARAMETER = 'limit';
    const OFFSET_PARAMETER = 'offset';

    const CREATED_AT_FIELD = 'createdAt';

    const SEARCH_QUERY_LOG_TEXT_FIELD = 'text';

    const DEFAULT_LIMIT = 50;
    const DEFAULT_OFFSET = 0;

    const SEARCH_QUERY_LOG_ALIAS = 'queryLog';
    const ARTICLES_ALIAS = 'articles';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function prepareReport(array $parameters, string $reportType): array
    {
        switch ($reportType) {
            case self::REPORT_TYPE_FAILED_SEARCH:
                $results = $this->prepareFailedSearchReport($parameters);

                break;

            default:
                throw new \InvalidArgumentException('Invalid report type');
        }

        return $results;
    }

    /**
     * @param array $parameters
     * @return SearchQueryLog[]
     */
    private function prepareFailedSearchReport(array $parameters): array
    {
        $parameters[self::RESULTS_COUNT_PARAMETER] = self::FAILED_SEARCH_RESULTS_COUNT;

        return $this->getSearchQueryLogRepository()->findForReporting($parameters);
    }
}
