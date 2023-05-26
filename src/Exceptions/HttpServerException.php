<?php

namespace Exan\Router\Exceptions;

class HttpServerException extends HttpException
{
    public function getHttpErrorCode(): int
    {
        return 500;
    }

    public function getHttpErrorMessage(): string
    {
        return 'Internal server error.';
    }
}
