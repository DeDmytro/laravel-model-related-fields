<?php

namespace DeDmytro\LaravelModelRelatedFields\Traits;

use DeDmytro\LaravelModelRelatedFields\Scopes\AddRelatedFieldsScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasRelatedFields
 * @package DeDmytro\LaravelModelRelatedFields\Traits
 * @mixin Model
 * @property-read array $relatedFields
 * @property-read bool $enabledRelatedFields
 * @method array addRelatedFields()
 * @method static Builder withoutRelatedFields()
 */
trait HasRelatedFields
{
    /**
     * Define model global scope
     */
    public static function bootHasRelatedFields()
    {
        if (with(new static)->isRelatedFieldsEnabled()) {
            static::addGlobalScope(new AddRelatedFieldsScope);
        }
    }

    /**
     * Disable related fields for current query
     * @param Builder $builder
     * @return Builder
     */
    public function scopeWithoutRelatedFields(Builder $builder): Builder
    {
        return $builder->withoutGlobalScope(AddRelatedFieldsScope::class);
    }

    /**
     * Enable related fields for current query
     * @param Builder $builder
     * @return Builder
     */
    public function scopeWithRelatedFields(Builder $builder): Builder
    {
        return $builder->withGlobalScope('add_related_fields', new AddRelatedFieldsScope);
    }

    /**
     * Defines whether model related fields are enabled for model
     * @return bool
     */
    public function isRelatedFieldsEnabled(): bool
    {
        if (property_exists($this, 'enableRelatedFields') && is_bool($this->enableRelatedFields)) {
            return $this->enableRelatedFields;
        }

        return config('model-related-fields.enabled');
    }

    /**
     * Get model available related fields
     * @return array
     */
    final public function getModelRelatedFields(): array
    {

        $propertyName = config('model-related-fields.property_name');
        $methodName = config('model-related-fields.method_name');

        $fields = [];

        if (property_exists($this, $propertyName) && is_array($this->{$propertyName})) {
            $fields = $this->{$propertyName};
        }

        if (method_exists($this, $methodName)) {
            $fields = array_merge($fields, (array) $this->{$methodName}());
        } else {
            return [];
        }

        return $fields;
    }
}