<?php

namespace DeDmytro\LaravelModelRelatedFields;

use Closure;

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
     * @param string $relation
     * @param Closure|null $select
     */
    public function __construct(string $relation, Closure $select = null)
    {
        $this->field = $relation;
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