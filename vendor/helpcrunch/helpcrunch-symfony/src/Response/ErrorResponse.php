<?php

namespace Helpcrunch\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorResponse extends JsonResponse
{
    const DEFAULT_ERROR_HEADERS = [
        'Access-Control-Allow-Origin' => '*'
    ];

    public function __construct($message, string $innerErrorCode = null, int $status = self::HTTP_BAD_REQUEST, $errors = [])
    {
        $responseData = [
            'message' => $message,
            'success' => false,
        ];
        if ($innerErrorCode) {
            $responseData['code'] = $innerErrorCode;
        }
        if ($errors) {
            $responseData['errors'] = $errors;
        }

        parent::__construct($responseData, $status, self::DEFAULT_ERROR_HEADERS);
    }
}
