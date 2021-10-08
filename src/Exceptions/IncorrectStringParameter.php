<?php

namespace Dedmytro\LaravelModelRelatedFields\Exceptions;

use Dedmytro\LaravelModelRelatedFields\RelatedField;
use Throwable;

class IncorrectStringParameter extends \Exception
{
    public function __construct()
    {
        parent::__construct(
            'Incorrect String Parameter Type. Check your string contains dot notation. Eg. user.company.name '
        );
    }
}