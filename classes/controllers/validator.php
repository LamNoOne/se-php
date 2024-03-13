<?php

class Validator
{
  public static function required($formData, $fields)
  {
    $status = true; // default validate success
    $errorMessages = [];
    foreach ($fields as $field) {
      if (!isset($formData[$field]) || $formData[$field] === null) {
        $status = false;
        $errorMessages[] = "\"$field\" is required";
      }
    }

    if ($status) {
      return ['status' => $status, 'Validate successfully'];
    }

    return ['status' => $status, 'message' => implode('. ', $errorMessages)];
  }

  public static function integer($formData, $fields)
  {
    $status = true; // default validate success
    $errorMessages = [];
    foreach ($fields as $field) {
      if (isset($formData[$field]) && $formData[$field] !== null) {
        $isInt = filter_var($formData[$field], FILTER_VALIDATE_INT);
        if (!$isInt) {
          $status = false;
          $errorMessages[] = "\"$field\" must be a integer";
        }
      };
    }

    if ($status) {
      return ['status' => $status, 'message' => 'Validate successfully'];
    }

    return ['status' => $status, 'message' => implode('. ', $errorMessages)];
  }

  public static function float($formData, $fields)
  {
    $status = true; // default validate success
    $errorMessages = [];
    foreach ($fields as $field) {
      if (isset($formData[$field]) && $formData[$field] !== null) {
        $isFloat = filter_var($formData[$field], FILTER_VALIDATE_FLOAT);
        if (!$isFloat) {
          $status = false;
          $errorMessages[] = "\"$field\" must be a float number";
        }
      };
    }

    if ($status) {
      return ['status' => $status, 'message' => 'Validate successfully'];
    }

    return ['status' => $status, 'message' => implode('. ', $errorMessages)];
  }

  public static function url($formData, $fields)
  {
    $status = true; // default validate success
    $errorMessages = [];
    foreach ($fields as $field) {
      if (isset($formData[$field]) && $formData[$field] !== null) {
        $isUrl = filter_var($formData[$field], FILTER_VALIDATE_URL)
          || checkFileExistsInLocalByURL($formData[$field]);
        if (!$isUrl) {
          $status = false;
          $errorMessages[] = "\"$field\" must be a URL";
        }
      };
    }

    if ($status) {
      return ['status' => $status, 'message' => 'Validate successfully'];
    }

    return ['status' => $status, 'message' => implode('. ', $errorMessages)];
  }
}
