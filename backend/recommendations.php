<?php
  $ROOT_PATH = '.';

  require_once($ROOT_PATH . '/helpers/database.php');
  require_once($ROOT_PATH . '/helpers/user.php');
  require_once($ROOT_PATH . '/helpers/jwt.php');
  require_once($ROOT_PATH . '/helpers/recommendation.php');

  function get_user_recommendation() {
    if(
      !isset($_POST['JWT'])
    ) return json_encode(array(
      'Message' => 'ERROR: NO JWT'
    ));

    $decoded_jwt = decodeJWT($_POST['JWT']);

    if(
      !property_exists($decoded_jwt, 'data') ||
      !property_exists($decoded_jwt->data, 'username')
    ) return json_encode(array(
      'Message' => 'ERROR: INVALID JWT'
    ));

    $user_id = getUserId($decoded_jwt->data->username);

    if($user_id == false) return json_encode(array(
      'Message' => 'ERROR: INVALID USERNAME'
    ));

    return json_encode(get_collaborative_filtering($user_id));
  }

  echo get_user_recommendation();
?>
