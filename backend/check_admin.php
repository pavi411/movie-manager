<?php

  $ROOT_PATH = '.';
  require_once($ROOT_PATH . '/config.php');
  require_once($ROOT_PATH . '/helpers/jwt.php');


  function checkAdmin() {
    if(!isset($_POST['user'])) return json_encode(array(
      'Message' => 'ERROR: INVALID DATA'
    ));

    $username = decodeJWT($_POST['user']);

    if($username === false) return json_encode(array(
      'Message' => 'ERROR: INVALID DATA'
    ));

    return json_encode(array(
      'Message' => 'SUCCESS',
      'isAdmin' => in_array($username->data->username, ADMINS)
    ));
  }

  echo checkAdmin();

?>
