<?php

namespace Exan\Router\Exceptions;

class HttpNotFoundException extends HttpException
{
    public function getHttpErrorCode(): int
    {
        return 404;
    }

    public function getHttpErrorMessage(): string
    {
        return 'Not Found.';
    }
}
