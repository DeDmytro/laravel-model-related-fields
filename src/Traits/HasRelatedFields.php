<?php

namespace DeDmytro\LaravelModelRelatedFields\Traits;

use DeDmytro\LaravelModelRelatedFields\Exceptions\IncorrectFieldType;
use DeDmytro\LaravelModelRelatedFields\Exceptions\IncorrectStringParameter;
use DeDmytro\LaravelModelRelatedFields\Helpers\RelationQueryBuilder;
use DeDmytro\LaravelModelRelatedFields\RelatedField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Trait HasRelatedFields
 * @package DeDmytro\LaravelModelRelatedFields\Traits
 * @mixin Model
 * @property-read array $relatedFields
 * @method static Builder withoutRelatedFields()
 */
trait HasRelatedFields
{
    /**
     * Define model global scope
     */
    public static function bootHasRelatedFields()
    {
        static::addGlobalScope('add_related_fields', function ($query) {

            $model = new static();

            if (method_exists($model, 'addRelatedFields')) {
                $fields = (array) $model->addRelatedFields();
            }else{
                return;
            }

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

                RelationQueryBuilder::from($endRelation)->buildWhereColumn($relatedFieldQuery);

                if(RelatedField::is($relatedField) && $relatedField->hasSelectClause()){
                    $relatedFieldQuery->tap($relatedField->select);
                }else{
                    $relatedFieldQuery->tap(function ($query) use ($table, $field) {
                        $query->select("$table.$field");
                    });
                }

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
    public function scopeWithoutRelatedFields(Builder $builder): Builder
    {
        return $builder->withoutGlobalScope('add_related_fields');
    }
}