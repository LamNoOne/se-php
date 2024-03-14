<?php
require_once dirname(__DIR__) . "/services/message.php";
class Category extends Message
{

    public $id;
    public $name;
    public $description;
    public $createdAt;
    public $updatedAt;

    public function __construct($id = null, $name = null, $description = null)
    {
        $id = $id;
        $name = $name;
        $description = $description;
    }

    public static function createCategory($conn, $categoryData)
    {
        /**
         * Write your code here
         */
    }

    public static function updateCategory($conn, $categoryData)
    {
        /**
         * Write your code here
         */
    }

    public static function deleteCategory($conn, $categoryId)
    {
        /**
         * Write your code here
         */
    }

    /**
     $pagination = ['limit' => 10, 'offset' => 0]
     */
    public static function getCategories(
        $conn,
        $filter = [['field' => 'id', 'value' => '', 'like' => false]],
        $pagination = [],
        $sort =  ['sortBy' => 'createdAt', 'order' => 'ASC']
    ) {
        try {
            $projection =  [
                [
                    'table' => TABLES['CATEGORY'],
                    'column' => 'id'
                ],
                [
                    'table' => TABLES['CATEGORY'],
                    'column' => 'name'
                ],
                [
                    'table' => TABLES['CATEGORY'],
                    'column' => 'description'
                ],
                [
                    'table' => TABLES['CATEGORY'],
                    'column' => 'createdAt'
                ],
                [
                    'table' => TABLES['CATEGORY'],
                    'column' => 'updatedAt'
                ],
            ];
            $join =  [
                'tables' => [
                    TABLES['CATEGORY']
                ],
            ];
            $selection = array_map(function ($filterItem) {
                return [
                    'table' => TABLES['CATEGORY'],
                    'column' => $filterItem['field'],
                    'value' => $filterItem['value'],
                    'like' => $filterItem['like'],
                ];
            }, $filter);
            $sort = [
                [
                    'table' => TABLES['CATEGORY'],
                    'column' => $sort['sortBy'],
                    'order' => $sort['order']
                ]
            ];

            $stmt = getQuerySQLPrepareStatement(
                $conn,
                $projection,
                $join,
                $selection,
                $pagination,
                $sort
            );
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return Message::message(false, 'Get categories failed');
        }
    }
}
