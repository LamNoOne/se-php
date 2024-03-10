<?php

class Validator
{
  public static function validateRequired($formData, $fields)
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

  public static function validateInteger($formData, $fields)
  {
    $status = true; // default validate success
    $errorMessages = [];
    foreach ($fields as $field) {
      if ($formData[$field] !== null) {
        $isInt = filter_var($formData[$field], FILTER_VALIDATE_INT);
        if (!$isInt) {
          $status = false;
          $errorMessages[] = "\"$field\" must be a integer";
        }
      };
    }

    if ($status) {
      return ['status' => $status, 'Validate successfully'];
    }

    return ['status' => $status, 'message' => implode('. ', $errorMessages)];
  }

  public static function validateUrl($formData, $fields)
  {
    $status = true; // default validate success
    $errorMessages = [];
    foreach ($fields as $field) {
      if ($formData[$field] !== null) {
        $isUrl = filter_var($fields['imageUrl'], FILTER_VALIDATE_URL)
          || checkFileExistsInLocalByURL($fields['imageUrl']);
        if (!$isUrl) {
          $status = false;
          $errorMessages[] = "\"$field\" must be a URL";
        }
      };
    }

    if ($status) {
      return ['status' => $status, 'Validate successfully'];
    }

    return ['status' => $status, 'message' => implode('. ', $errorMessages)];
  }
}
