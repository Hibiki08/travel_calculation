<?php

namespace App\Exception;

/**
 * Class ValidationFailedException
 * @package App\Exception
 */
class ValidationFailedException extends \Exception
{
    public function __construct(
        string $error = 'Validation failed',
    )
    {
        parent::__construct($error, 400);
    }
}