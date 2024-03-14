<?php
require_once dirname(__DIR__) . "/services/message.php";
require_once dirname(__DIR__) . "/services/datafetcher.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";
class Category extends Message
{

    public $id;
    public $name;
    public $description;
    public $createdAt;
    public $updatedAt;

    public function __construct(
        $data = []
    ) {
        $data = Category::removeBannedFields($data);
        $data = deleteFieldsHasEmptyString($data);
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    private static function removeBannedFields($fields)
    {
        $copiedFields = $fields;
        $bannedFields = ['id', 'createdAt', 'updatedAt'];
        foreach ($bannedFields as $bannedField) {
            if (array_key_exists($bannedField, $copiedFields)) {
                unset($copiedFields[$bannedField]);
            }
        }
        return $copiedFields;
    }

    private static function validateCreate($formData)
    {
        $result = Validator::required($formData, [
            'categoryId',
            'name',
            'imageUrl',
            'price',
            'stockQuantity',
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        $result = Validator::integer($formData, [
            'categoryId',
            'ram',
            'storageCapacity',
            'batteryCapacity',
            'price',
            'stockQuantity',
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        $result = Validator::float($formData, [
            'weight',
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        $result = Validator::url($formData, [
            'imageUrl'
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        return Message::message(true, 'Validate successfully');
    }

    private static function validateDeleteByIds($formData)
    {
        $result = Validator::required($formData, [
            'ids'
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        $result = Validator::array($formData, [
            'ids'
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        return Message::message(true, 'Validate successfully');
    }

    public function createCategory($conn)
    {
        try {
            $stmt = getCreateSQLPrepareStatement($conn, TABLES['CATEGORY'], $this);
            if (!$stmt->execute()) {
                return Message::message(false, 'Something went wrong');
            }
            return Message::message(true, 'Add product successfully');
        } catch (Exception $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();
            if ($errorCode === '23000') {
                if (preg_match("/for key '(\w+)'/", $errorMessage, $matches)) {
                    // $duplicationKey = $matches[1];
                    return Message::message(false, 'Category name cannot be duplicated');
                }
                return Message::message(false, 'Something went wrong');
            }
            return Message::message(false, 'Something went wrong');
        }
    }

    public static function updateCategory($conn, $categoryData)
    {
        /**
         * Write your code here
         */
    }

    public static function deleteCategory($conn, $id)
    {
        try {
            $stmt = getDeleteByIdSQLPrepareStatement($conn, TABLES['CATEGORY'], $id);
            if ($stmt->execute()) {
                return Message::message(true, 'Delete category successfully');
            }
            throw new PDOException('Cannot execute sql statement');
        } catch (Exception $e) {
            return Message::message(false, 'Something went wrong');
        }
    }

    public static function deleteByIds($conn, $ids)
    {
        try {
            $validateResult = Category::validateDeleteByIds(['ids' => $ids]);
            if (!$validateResult['status']) {
                return Message::message(false, $validateResult['message']);
            }

            $stmt = getDeleteByIdsSQLPrepareStatement($conn, TABLES['CATEGORY'], $ids);
            if ($stmt->execute()) {
                return Message::message(true, 'Delete category by ids successfully');
            }
            throw new PDOException('Cannot execute sql statement');
        } catch (Exception $e) {
            return Message::message(false, 'Something went wrong');
        }
    }

    public static function getCategoryById($conn, $id)
    {
        try {
            $projection =  [];
            $join =  [
                'tables' => [
                    TABLES['CATEGORY']
                ],
            ];

            $stmt = getQueryByIdSQLPrepareStatement(
                $conn,
                $id,
                $projection,
                $join,
            );
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return Message::messageData(true, 'Get category by id successfully', [
                'category' => $stmt->fetch()
            ]);
        } catch (Exception $e) {
            return Message::message(false, 'Get category by id failed');
        }
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
            $projection =  [];
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
