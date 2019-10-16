<?php

namespace App\Response;

use Helpcrunch\Helper\FormatterHelper;
use Helpcrunch\Response\ErrorResponse;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpFoundation\Response;

class ExceptionErrorResponse extends ErrorResponse {
    public function __construct($message, \Exception $exception, array $trace = [])
    {
        parent::__construct(
            $message,
            self::generateErrorCode($exception),
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $trace
        );
    }

    private static function generateErrorCode(\Exception $exception): string
    {
        if ($exception instanceof FatalThrowableError) {
            $exceptionName = $exception->getOriginalClassName();
        } else {
            $exceptionClassParts = explode('\\', get_class($exception));
            $exceptionName = end($exceptionClassParts);
        }

        return FormatterHelper::convertCamelCaseToUnderscore($exceptionName);
    }
}
