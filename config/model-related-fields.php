<?php

return [
    'enabled' => env('ENABLE_MODEL_RELATED_FIELDS', true), // Default value for all models.
    // Can be overwritten inside any model by using property.
    // protected $enableRelatedFields = false;

    'method_name' => 'addRelatedFields',
    'property_name' => 'relatedFields',
];
