<?php

namespace DeDmytro\LaravelModelRelatedFields\Helpers;

use DeDmytro\LaravelModelRelatedFields\Exceptions\IncorrectRelation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;

class RelationQueryBuilder
{
    /**
     * Contains Laravel model relation object
     * @var HasManyThrough
     */
    protected $relation;

    /**
     * RelationQueryBuilder constructor.
     * @param Relation $relation
     * @throws IncorrectRelation
     */
    public function __construct($relation)
    {
        if(!is_object($relation) && !is_subclass_of($relation, Relation::class)){
           throw new IncorrectRelation();
        }

        $this->relation = $relation;
    }

    /**
     * Static factory to create object
     * @param $relation
     * @return RelationQueryBuilder
     * @throws IncorrectRelation
     */
    public static function from($relation): RelationQueryBuilder
    {
        return new self($relation);
    }

    /**
     * Build whereColumn clause
     * @param $query
     */
    public function buildWhereColumn(&$query){
        $query->whereColumn(
            "{$this->relation->getParent()->getTable()}.{$this->getForeignKey()}",
            "{$this->relation->getRelated()->getTable()}.{$this->getLocalKey()}"
        );
    }

    /**
     * Return
     * @return string
     */
    private function getLocalKey(): string
    {
        switch (get_class($this->relation)){
            case HasMany::class:
            case HasManyThrough::class:
                return $this->relation->getForeignKeyName();
            default:
                return $this->relation->getOwnerKeyName();
        }
    }

    /**
     * Return foreign key
     * @return string
     */
    private function getForeignKey(): string
    {
        switch (get_class($this->relation)){
            case HasMany::class:
            case HasManyThrough::class:
                return $this->relation->getLocalKeyName();
            default:
                return $this->relation->getForeignKeyName();
        }
    }
}