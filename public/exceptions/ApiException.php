<?php

declare(strict_types=1);

namespace app\exceptions;

use Exception;

/**
 * API response exception
 */
class ApiException extends Exception
{
    /**
     * @var string
     */
    public string $request;
    /**
     * @var string
     */
    public string $response;
}