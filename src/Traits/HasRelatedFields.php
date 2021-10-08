<?php

namespace Dedmytro\LaravelModelRelatedFields\Traits;

use Dedmytro\LaravelModelRelatedFields\Exceptions\IncorrectFieldType;
use Dedmytro\LaravelModelRelatedFields\Exceptions\IncorrectStringParameter;
use Dedmytro\LaravelModelRelatedFields\RelatedField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Trait HasRelatedFields
 * @package Dedmytro\LaravelModelRelatedFields\Traits
 * @mixin Model
 * @property-read array $relatedFields
 * @method static Builder withoutRelatedFields()
 */
trait HasRelatedFields
{
    /**
     * Define model global scope
     */
    public function bootHasRelatedFields()
    {
        static::addGlobalScope('add_related_fields', function ($query) {

            $fields = [];

            if (method_exists($this, 'addRelatedFields')) {
                $fields = (array) $this->addRelatedFields();
            }

            $model = new static();

            $resultFields = [];

            foreach ($fields as $as => $relatedField) {

                if (RelatedField::is($relatedField)) {
                    $parts = explode('.', $relatedField->field);
                } elseif (is_string($relatedField)) {
                    if (!Str::contains($relatedField, '.')) {
                        throw new IncorrectStringParameter();
                    }
                    $parts = explode('.', $relatedField);
                } else {
                    throw new IncorrectFieldType();
                }

                $field = array_pop($parts);

                $endModelRelationMethod = array_shift($parts);
                $endRelation = $model->{$endModelRelationMethod}();

                $relations = $parts;

                $relatedFieldQuery = $endRelation->getModel()->query();

                $currentModel = $endRelation->getRelated();

                foreach ($relations as $index => $relationMethod) {

                    $currentRelation = $currentModel->{$relationMethod}();

                    $currentModel = $currentRelation->getRelated();

                    $relatedFieldQuery->leftJoin(
                        $currentModel->getTable(),
                        "{$currentRelation->getParent()->getTable()}.{$currentRelation->getForeignKeyName()}",
                        "{$currentModel->getTable()}.{$currentRelation->getOwnerKeyName()}"
                    );
                }

                $table = $currentModel->getTable();

                $relatedFieldQuery
                    ->whereColumn(
                        "{$endRelation->getParent()->getTable()}.{$endRelation->getForeignKeyName()}",
                        "{$endRelation->getRelated()->getTable()}.{$endRelation->getOwnerKeyName()}"
                    );

                /* @var $relatedFieldQuery Builder */
                /* @var $relatedField RelatedField */

                $relatedFieldQuery->when(
                    RelatedField::is($relatedField) && $relatedField->hasSelectClause(),
                    $relatedField->select,
                    function ($query) use ($table, $field) {
                        $query->select("$table.$field");
                    }
                );

                $resultFields[$as] = $relatedFieldQuery;
            }

            $query->addSelect($resultFields);
        });
    }

    /**
     * Disable related fields for current query
     * @param Builder $builder
     * @return Builder
     */
    public function scopeWithoutRelatedFields(Builder $builder)
    {
        return $builder->withoutGlobalScope('add_related_fields');
    }
}