<?php
require_once dirname(__DIR__) . "/services/message.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";
class OrderStatus
{

  public $id;
  public $name;
  public $createdAt;
  public $updatedAt;

  public function __construct(
    $data = []
  ) {
    $data = OrderStatus::removeBannedFields($data);
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
      'name',
    ]);
    if (!$result['status']) {
      return Message::message(false, $result['message']);
    }

    return Message::message(true, 'Validate successfully');
  }

  public function createOrderStatus($conn)
  {
    try {
      $result = $this->validateCreate(get_object_vars($this));
      if (!$result['status']) {
        return $result;
      }

      $stmt = getCreateSQLPrepareStatement($conn, TABLES['ORDER_STATUS'], $this);
      if (!$stmt->execute()) {
        return Message::message(false, 'Something went wrong');
      }
      return Message::message(true, 'Add product successfully');
    } catch (Exception $e) {
      $duplicateKey = getDuplicateKeyWhenSQLInsertUpdate($e);
      if (empty($duplicateKey)) {
        return Message::message(false, 'Something went wrong');
      }
      if ($duplicateKey[1] === 'name') {
        return Message::message(false, "'$duplicateKey[0]' is available");
      }
      return Message::message(false, 'Something went wrong');
    }
  }

  public static function getOrderStatuses(
    $conn,
    $filter = [['field' => 'id', 'value' => '', 'like' => false]],
    $pagination = [],
    $sort =  ['sortBy' => 'createdAt', 'order' => 'ASC']
  ) {
    try {
      $sortConditions = [
        'id' => [['table' => TABLES['ORDER_STATUS'], 'column' => 'id']],
        'name' => [['table' => TABLES['ORDER_STATUS'], 'column' => 'name']],
        'createdAt' => [['table' => TABLES['ORDER_STATUS'], 'column' => 'createdAt']],
        'updatedAt' => [['table' => TABLES['ORDER_STATUS'], 'column' => 'updatedAt']]
      ];

      $sortCondition = $sortConditions[$sort['sortBy']];
      $orderBy = $sort['order'];
      for ($i = 0; $i < count($sortCondition); $i++) {
        $sortCondition[$i]['order'] = $orderBy;
      }

      $projection =  [];
      $join =  [
        'tables' => [
          TABLES['ORDER_STATUS']
        ]
      ];
      $selection = array_map(function ($filterItem) {
        $selectionItem = [
          'table' => TABLES['ORDER_STATUS'],
          'column' => $filterItem['field'],
          'value' => $filterItem['value']
        ];
        if (isset($filterItem['like'])) {
          $selectionItem['like'] = $filterItem['like'];
        }
        return $selectionItem;
      }, $filter);

      $stmt = getQuerySQLPrepareStatement(
        $conn,
        $projection,
        $join,
        $selection,
        $pagination,
        $sortCondition,
      );
      $stmt->setFetchMode(PDO::FETCH_OBJ);
      if (!$stmt->execute()) {
        throw new PDOException('Cannot execute query');
      }
      return Message::messageData(true, 'Get order statuses successfully', [
        'orderStatuses' => $stmt->fetchAll()
      ]);
    } catch (Exception $e) {
      return Message::message(false, 'Get order statuses failed');
    }
  }
}
