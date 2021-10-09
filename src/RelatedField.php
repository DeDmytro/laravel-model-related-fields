<?php

namespace DeDmytro\LaravelModelRelatedFields;

use Closure;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RelatedField
{
    /**
     * Related field
     * @var string
     */
    public $field;

    /**
     * Select statement
     * @var Closure
     */
    public $select;

    /**
     * RelatedField constructor.
     * @param string $field
     * @param Closure|null $select
     */
    public function __construct(string $field, Closure $select = null)
    {
        $this->field = $field;
        $this->select = $select;
    }

    /**
     * Define whether variable is instance of RelatedField
     * @param $var
     * @return bool
     */
    public static function is($var): bool
    {
        return is_object($var) && $var instanceof self;
    }

    /**
     * Return whether has select clause for relation
     * @return bool
     */
    public function hasSelectClause(): bool
    {
        return !is_null($this->select);
    }
}