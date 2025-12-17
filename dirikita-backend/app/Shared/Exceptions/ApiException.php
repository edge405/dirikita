<?php

namespace App\Shared\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected string $errorCode;

    protected $details;

    public function __construct(string $message, string $errorCode = 'API_ERROR', $details = null, int $code = 400, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
        $this->details = $details;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getDetails()
    {
        return $this->details;
    }
}

