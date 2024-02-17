<?php
declare(strict_types=1);

class Validation
{

    public function __construct()
    {
    }

    /**
     * @param mixed $options
     * Validator options
     */
    protected function validateQueryOptions($options)
    {
        /**
         * @param $defaults
         * Default values for options query
         */
        $defaults = [
            'fields' => '*',
            'filters' => [],
            'orderBy' => '',
            'limit' => 20,
            'offset' => 0,
        ];

        if(!self::validateKeys($defaults, $options))
            throw new InvalidArgumentException("Invalid options");

        /**
         * Validate and sanitize each option
         */
        $options = array_merge($defaults, $options);
        $options['fields'] = self::validateFields($options['fields']);
        $options['filters'] = self::validateFilters($options['filters']);
        $options['orderBy'] = self::validateOrderBy($options['orderBy']);
        $options['limit'] = self::validateLimit($options['limit']);
        $options['offset'] = self::validateOffset($options['offset']);

        return $options;
    }

    /**
     * @param string $fields
     * Validate and sanitize fields
     */
    private static function validateFields($fields)
    {
        // Add your validation logic here
        return $fields;
    }

    /**
     * @param mixed $filters
     * Validate and sanitize filters
     */
    private static function validateFilters($filters)
    {
        // Add your validation logic here
        return $filters;
    }

    /**
     * @param string $orderBy
     * Validate and sanitize order by clause
     */
    private static function validateOrderBy($orderBy)
    {
        // Add your validation logic here
        return $orderBy;
    }

    /**
     * @param int $limit
     * Validate and sanitize limit
     */
    private static function validateLimit($limit)
    {
        // Add your validation logic here
        return (int)$limit;
    }

    /**
     * @param int $offset
     * Validate and sanitize offset
     */
    private static function validateOffset($offset)
    {
        // Add your validation logic here
        return (int)$offset;
    }

    /**
     * @param mixed $defaults
     * @param mixed $options
     * @return boolean
     */
    private static function validateKeys($defaults, $options) {
        // Add your validation logic here
        if(empty($options)) return false;
        
        foreach (array_keys($options) as $key) {
            if(!array_key_exists($key, $defaults)) return false;
        }

        return true;
    }
}
