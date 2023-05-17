<?php

namespace Exan\Router\Exceptions;

use Exception;

abstract class HttpException extends Exception
{
    abstract public function getHttpErrorCode(): int;
    abstract public function getHttpErrorMessage(): string;
}
