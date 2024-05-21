<?php
  $ROOT_PATH = '.';

  require_once($ROOT_PATH . '/helpers/data_validation.php');
  require_once($ROOT_PATH . '/helpers/validation.php');
  require_once($ROOT_PATH . '/helpers/database.php');
  require_once($ROOT_PATH . '/helpers/json.php');

  function getCheck() {
    if(
      !isset($_POST['register']) ||
      !is_json($_POST['register'])
    ) return "ERROR";

    $register = json_decode($_POST['register']);

    $validation = validate_register($register);

    if($validation->username) {
      $validation->username = !checkIfRowExists('users', array('username' => $register->username));
    }

    return json_encode($validation);
  }

  echo getCheck();
?>
