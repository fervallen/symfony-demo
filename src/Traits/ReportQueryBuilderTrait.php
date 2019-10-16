<?php

namespace App\Traits;

use App\Service\ReportService;
use Doctrine\ORM\QueryBuilder;

trait ReportQueryBuilderTrait
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    public function processQueryBuilder(QueryBuilder $queryBuilder, array $parameters): QueryBuilder
    {
        $this->parameters = $parameters;
        $this->queryBuilder = $queryBuilder;

        $this->populateQueryBuilder();

        return $this->queryBuilder;
    }

    private function populateQueryBuilder(): void
    {
        $this->checkDates();
        $this->checkResultsCount();
        $this->checkLimit();
        $this->checkOffset();
    }

    private function checkDates(): void
    {
        if (!empty($this->parameters[ReportService::START_DATE_PARAMETER]) &&
            !empty($this->parameters[ReportService::END_DATE_PARAMETER]) &&
            !empty(self::REPORT_DATE_FIELD_ALIAS)
        ) {
            $startDate = $this->parseDate($this->parameters[ReportService::START_DATE_PARAMETER]);
            $endDate = $this->parseDate($this->parameters[ReportService::END_DATE_PARAMETER]);

            $this->queryBuilder->andWhere(
                $this->queryBuilder->expr()->between(
                    self::REPORT_DATE_FIELD_ALIAS . '.' . ReportService::CREATED_AT_FIELD,
                    ':' . ReportService::START_DATE_PARAMETER,
                    ':' . ReportService::END_DATE_PARAMETER
                )
            );

            $this->queryBuilder
                ->setParameter(ReportService::START_DATE_PARAMETER, $startDate->format('Y-m-d H:i:s'))
                ->setParameter(ReportService::END_DATE_PARAMETER, $endDate->format('Y-m-d H:i:s'));
        } else {
            throw new \InvalidArgumentException('Invalid date format');
        }
    }

    private function parseDate(string $date = null): \DateTime
    {
        if (empty($date)) {
            throw new \InvalidArgumentException('You need to specify the period start and end dates');
        }

        try {
            $dateTime = new \DateTime($date);
        } catch (\Exception $exception) {
            throw new \InvalidArgumentException('Date is in incorrect format');
        }

        return $dateTime;
    }

    private function checkResultsCount(): void
    {
        if (isset($this->parameters[ReportService::RESULTS_COUNT_PARAMETER]) &&
            is_integer($this->parameters[ReportService::RESULTS_COUNT_PARAMETER])
        ) {
            $field = ReportService::SEARCH_QUERY_LOG_ALIAS . '.' . ReportService::RESULTS_COUNT_PARAMETER;

            $this->queryBuilder->andWhere($field . '= :' . ReportService::RESULTS_COUNT_PARAMETER)
                ->setParameter(ReportService::RESULTS_COUNT_PARAMETER, $this->parameters[ReportService::RESULTS_COUNT_PARAMETER]);
        }
    }

    private function checkLimit(): void
    {
        if (!empty($this->parameters[ReportService::LIMIT_PARAMETER])) {
            $this->queryBuilder->setMaxResults($this->parameters[ReportService::LIMIT_PARAMETER]);
        }
    }

    private function checkOffset(): void
    {
        if (isset($this->parameters[ReportService::OFFSET_PARAMETER]) &&
            is_integer($this->parameters[ReportService::OFFSET_PARAMETER])
        ) {
            $this->queryBuilder->setFirstResult($this->parameters[ReportService::OFFSET_PARAMETER]);
        }
    }
}
