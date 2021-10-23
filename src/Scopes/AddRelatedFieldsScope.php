<?php

namespace DeDmytro\LaravelModelRelatedFields\Scopes;

use DeDmytro\LaravelModelRelatedFields\Exceptions\IncorrectFieldType;
use DeDmytro\LaravelModelRelatedFields\Exceptions\IncorrectRelation;
use DeDmytro\LaravelModelRelatedFields\Exceptions\IncorrectStringParameter;
use DeDmytro\LaravelModelRelatedFields\Helpers\RelationQueryBuilder;
use DeDmytro\LaravelModelRelatedFields\RelatedField;
use DeDmytro\LaravelModelRelatedFields\Traits\HasRelatedFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Str;

class AddRelatedFieldsScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * @param Builder $query
     * @param Model|HasRelatedFields $model
     * @return void
     * @throws IncorrectFieldType
     * @throws IncorrectStringParameter
     * @throws IncorrectRelation
     */
    public function apply(Builder $query, $model)
    {
        $fields = $model->getModelRelatedFields();

        $resultFields = [];

        foreach ($fields as $as => $relatedField) {

            if (RelatedField::is($relatedField)) {
                $parts = explode('.', $relatedField->field);
            } elseif (is_string($relatedField)) {
                if (!Str::contains($relatedField, '.')) {
                    throw new IncorrectStringParameter($relatedField);
                }
                $parts = explode('.', $relatedField);
            } else {
                throw new IncorrectFieldType();
            }

            $field = count($parts) === 1 ? null : array_pop($parts);

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

            RelationQueryBuilder::from($endRelation)->buildWhereColumn($relatedFieldQuery);

            if (RelatedField::is($relatedField) && $relatedField->hasSelectClause()) {
                $relatedFieldQuery->tap($relatedField->select);
            } else {
                $relatedFieldQuery->tap(function ($query) use ($table, $field) {
                    $query->select("$table.$field");
                });
            }

            $resultFields[$as] = $relatedFieldQuery;
        }

        $query->addSelect($resultFields);
    }
}