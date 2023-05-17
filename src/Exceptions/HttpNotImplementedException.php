<?php

namespace Exan\Router\Exceptions;

class HttpNotImplementedException extends HttpException
{
    public function getHttpErrorCode(): int
    {
        return 501;
    }

    public function getHttpErrorMessage(): string
    {
        return 'Not Implemented.';
    }
}
