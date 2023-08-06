<?php

namespace App\Traits;

trait DataFormController
{
  public function jsonData($status, $account_status, $msg, $errors, $data)
  {
    return response()->json([
      "status" => $status,
      "account_status" => $account_status,
      "message" => $msg,
      "errors" => $errors,
      "data" => $data
    ]);
  }
}
