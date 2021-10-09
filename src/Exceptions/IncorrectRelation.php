<?php

namespace DeDmytro\LaravelModelRelatedFields\Exceptions;

use DeDmytro\LaravelModelRelatedFields\RelatedField;
use Throwable;

class IncorrectRelation extends \Exception
{
    public function __construct()
    {
        parent::__construct(
            'Incorrect Relation Type. Relation class should extend Illuminate\Database\Eloquent\Relations\Relation'
        );
    }
}