<?php

namespace DeDmytro\LaravelModelRelatedFields\Exceptions;

use Exception;

class IncorrectStringParameter extends Exception
{
    public function __construct($parameter = '')
    {
        parent::__construct(
            "Incorrect String Parameter Type. Check your string ($parameter) contains dot notation. Eg. user.company.name"
        );
    }
}