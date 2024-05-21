<?php
  require_once($ROOT_PATH . '/helpers/validation.php');

  function validate_login($login) {
    if(!is_object($login)) return false;

    if(
      !property_exists($login, 'username') ||
      !validate_username($login->username)
      ) return false;

    if(
      !property_exists($login, 'password') ||
      !validate_password($login->password)
      ) return false;

    return true;
  }

  function validate_register($register) {
    $validation = (object) array(
      'name' => false,
      'username' => false,
      'password' => false
    );

    if(!is_object($register)) {
      return $validation;
    }

    if(
      property_exists($register, 'name') &&
      validate_name($register->name)
    ) {
      $validation->name = true;
    }

    if(
      property_exists($register, 'username') &&
      validate_username($register->username)
    ) {
      $validation->username = true;
    }

    if(
      property_exists($register, 'password') &&
      validate_password($register->password)
    ) {
      $validation->password = true;
    }

    return $validation;
  }
?>
