<?php

class Validation
{

    public function __construct()
    {
    }

    protected function validateQueryOptions($options)
    {
        // Set default values
        $defaults = [
            'fields' => '*',
            'filters' => [],
            'orderBy' => '',
            'limit' => 20,
            'offset' => 0,
        ];

        // Validate and sanitize each option
        $options = array_merge($defaults, $options);
        $options['fields'] = self::validateFields($options['fields']);
        $options['filters'] = self::validateFilters($options['filters']);
        $options['orderBy'] = self::validateOrderBy($options['orderBy']);
        $options['limit'] = self::validateLimit($options['limit']);
        $options['offset'] = self::validateOffset($options['offset']);

        return $options;
    }

    // Validate and sanitize fields
    private static function validateFields($fields)
    {
        // Add your validation logic here
        return $fields;
    }

    // Validate and sanitize filters
    private static function validateFilters($filters)
    {
        // Add your validation logic here
        return $filters;
    }

    // Validate and sanitize order by clause
    private static function validateOrderBy($orderBy)
    {
        // Add your validation logic here
        return $orderBy;
    }

    // Validate and sanitize limit
    private static function validateLimit($limit)
    {
        // Add your validation logic here
        return (int)$limit;
    }

    // Validate and sanitize offset
    private static function validateOffset($offset)
    {
        // Add your validation logic here
        return (int)$offset;
    }
}
