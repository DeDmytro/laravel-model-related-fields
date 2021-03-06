<?php

namespace DeDmytro\LaravelModelRelatedFields\Exceptions;

use DeDmytro\LaravelModelRelatedFields\RelatedField;
use Throwable;

class IncorrectFieldType extends \Exception
{
    public function __construct()
    {
        parent::__construct(
            'Incorrect Field Type. Please check you use are using '.RelatedField::class.' or string with dot notation'
        );
    }
}