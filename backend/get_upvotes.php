<?php
  $ROOT_PATH = '.';

  require_once($ROOT_PATH . '/helpers/review.php');

  if(
    !isset($_POST['type']) ||
    !in_array($_POST['type'], array('get', 'set')) ||
    !isset($_POST['review']) ||
    !isset($_POST['user'])
  ) {
    echo json_encode(array(
      'Message' => 'ERROR: INVALID DATA'
    ));
  } else {
    $user_name = decodeJWT($_POST['user']);
    if($user_name != false) {
      $user_name = $user_name->data->username;
      $user_id = getUserId($user_name);
      if($user_id != false) {
        if(
          $_POST['type'] == 'set' &&
          isset($_POST['vote']) &&
          in_array($_POST['vote'], array(-1, 0, 1))
        ) echo addVote($user_id, $_POST['review'], $_POST['vote']);
        else if(
          $_POST['type'] == 'get'
        ) echo getUserVote($_POST['review'], $user_id);
        else echo json_encode(array(
          'Message' => 'ERROR: INVALID DATA.'
        ));
      } else {
        echo json_encode(array(
          'Message' => 'ERROR: INVALID DATA.'
        ));
      }
    } else echo json_encode(array(
      'Message' => 'ERROR: INVALID DATA.'
    ));
  }
?>
