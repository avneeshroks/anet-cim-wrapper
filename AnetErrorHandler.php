<?php 

namespace App\Alonti\ANetWrapper;

class AnetErrorHandler
{

  public function matchResponseErrorCode($respose = [], $errorCode = []) {
    if(empty($respose) || empty($errorCode)) {
      return false;
    }

    $errorMatch = false;

    foreach ($respose->getMessages()->getMessage() as $value) {
      $errorMatch = $value->getCode() == $errorCode;
      if($errorMatch) {
        break;
      } else {
        continue;
      }
    }

    if(!$errorMatch) {
      return false;
    }

    return true; 
  }

  public function checkIfResponseOk($respose = [], $okCode = []) {
    if(empty($respose) || empty($okCode)) {
      return false;
    }

    $responseOk = false;

    foreach ($respose->getMessages()->getMessage() as $value) {
      $responseOk = $value->getCode() == $okCode;
      if($responseOk) {
        break;
      } else {
        continue;
      }
    }

    if(!$responseOk) {
      return false;
    }

    return true; 
  }
}