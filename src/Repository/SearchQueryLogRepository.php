<?php

namespace App\Repository;

use App\Entity\SearchQueryLog;
use App\Service\ReportService;
use App\Traits\ReportQueryBuilderTrait;
use Helpcrunch\Repository\HelpcrunchRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SearchQueryLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchQueryLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchQueryLog[]    findAll()
 * @method SearchQueryLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchQueryLogRepository extends HelpcrunchRepository
{
    use ReportQueryBuilderTrait;

    const REPORT_DATE_FIELD_ALIAS = ReportService::SEARCH_QUERY_LOG_ALIAS;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SearchQueryLog::class);
    }

    /**
     * @param array $parameters
     * @return SearchQueryLog[]
     */
    public function findForReporting(array $parameters): array
    {
        $groupByField = ReportService::SEARCH_QUERY_LOG_ALIAS . '.' .
            ReportService::SEARCH_QUERY_LOG_TEXT_FIELD;

        $queryBuilder = $this->createQueryBuilder(ReportService::SEARCH_QUERY_LOG_ALIAS)
            ->select('DISTINCT(' . $groupByField . ') as text', 'COUNT(' . $groupByField . ') as resultsCount');

        return $this->processQueryBuilder($queryBuilder, $parameters)
            ->groupBy($groupByField)
            ->orderBy(ReportService::RESULTS_COUNT_PARAMETER, 'DESC')
            ->getQuery()
            ->getResult();
    }
}
