<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use Helpcrunch\Response\InnerErrorCodes;
use App\Service\ReportService;
use Helpcrunch\Annotation\AuthSpecification\UserAuthSpecification;
use Helpcrunch\Response\EntitiesBatchResponse;
use Helpcrunch\Response\ErrorResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class ReportController extends BaseApiController
{
    /**
     * @Rest\Get("/api/report/search/{type}")
     *
     * @UserAuthSpecification()
     *
     * @param Request $request
     * @param string $type
     * @return JsonResponse
     */
    public function failedSearchReportAction(Request $request, string $type): JsonResponse
    {
        if ($type != ReportService::REPORT_TYPE_FAILED_SEARCH) {
            return new ErrorResponse('Invalid report type', InnerErrorCodes::INVALID_PARAMETER);
        }

        /** @var ReportService $reportService */
        $reportService = $this->container->get(ReportService::class);

        try {
            $queryLogs = $reportService->prepareReport(
                $this->prepareReportParameters($request), $type
            );
        } catch (\InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new EntitiesBatchResponse($queryLogs);
    }

    private function prepareReportParameters(Request $request): array
    {
        $parameters = [];

        $parameters[ReportService::START_DATE_PARAMETER] = $request->query->get('startDate', null);
        $parameters[ReportService::END_DATE_PARAMETER] = $request->query->get('endDate', null);
        $parameters[ReportService::OFFSET_PARAMETER] = $request->query->get(
            'offset', ReportService::DEFAULT_OFFSET
        );
        $parameters[ReportService::LIMIT_PARAMETER]  = $request->query->get(
            'limit', ReportService::DEFAULT_LIMIT
        );

        return $parameters;
    }
}
