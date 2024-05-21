<?php
  $ROOT_PATH = '.';

  require_once('helpers/data_validation.php');
  require_once('helpers/validation.php');
  require_once('helpers/database.php');
  require_once('helpers/user.php');
  require_once('helpers/json.php');

  function getRegister() {
    if(
      !isset($_POST['register']) ||
      !is_json($_POST['register'])
    ) return "ERROR";

    $register = json_decode($_POST['register']);

    $validation = validate_register($register);

    if(
      !$validation->name ||
      !$validation->username ||
      !$validation->password
      ) return "ERROR";

    if(addUser(
      $register->username,
      $register->password,
      $register->name,
      ) != false
    ) {
      return "SUCCESS";
    } else {
      return "ERROR";
    }
  }

  echo getRegister();
?>
