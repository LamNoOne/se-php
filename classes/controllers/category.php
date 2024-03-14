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

    public static function getAllCategories($conn)
    {
        $query = "SELECT * FROM category";
        try {
            $stmt = $conn->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_CLASS, "Category");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return Message::message(false, "Can not get all categories" . $e->getMessage());
        }
    }

    public static function getCategories(
        $conn,
        $filter = [['field' => 'id', 'value' => '1', 'like' => false, 'int' => true]],
        $pagination = [],
        $sort =  ['sortBy' => 'id', 'order' => 'ASC']
    ) {
        try {
            $stmt = getQuerySQLPrepareStatement(
                $conn,
                [
                    [
                        "table" => TABLES['CATEGORY'],
                        "column" => "id"
                    ],
                    [
                        "table" => TABLES['CATEGORY'],
                        "column" => "name"
                    ],
                    [
                        "table" => TABLES['CATEGORY'],
                        "column" => "description"
                    ],
                    [
                        "table" => TABLES['CATEGORY'],
                        "column" => "createdAt"
                    ],
                    [
                        "table" => TABLES['CATEGORY'],
                        "column" => "updatedAt"
                    ],
                ],
                [
                    "tables" => [
                        TABLES['CATEGORY'],
                    ],
                    "on" => [
                        [
                            'table1' => 'product',
                            'table2' => 'category',
                            'column1' => 'categoryId',
                            'column2' => 'id'
                        ]
                    ]
                ],
                $filter,
                $pagination,
                [
                    [
                        'table' => 'product',
                        'column' => $sort['sortBy'],
                        'order' => $sort['order']
                    ]
                ]
            );
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [$stmt, $e->getMessage()];
            return Message::message(false, 'Get all products failed');
        }
    }
}
