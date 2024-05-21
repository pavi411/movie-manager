<?php
  $ROOT_PATH = '.';

  require_once($ROOT_PATH . '/helpers/rating.php');

  if(
    !isset($_POST['type']) ||
    !in_array($_POST['type'], array('get', 'set')) ||
    !isset($_POST['movie']) ||
    ($_POST['type'] == 'set' && !isset($_POST['rating']))
  ) {
    echo json_encode(array(
      'Message' => 'ERROR'
    ));
  } else {
    if(isset($_POST['user'])) {
      $user_name = decodeJWT($_POST['user']);
      if($user_name != false) {
        $user_name = $user_name->data->username;
        $user_id = getUserId($user_name);
        if($user_id != false) {
          if($_POST['type'] == 'set') {
            echo setRating($user_id, $_POST['movie'], $_POST['rating']);
          } else {
            echo getUserRating($user_id, $_POST['movie']);
          }
        } else {
          echo json_encode(array(
            'Message' => 'ERROR: INVALID DATA.'
          ));
        }
      }
    } else if ($_POST['type'] == 'get') {
      echo getRating($_POST['movie']);
    } else {
      echo json_encode(array(
        'Message' => 'ERROR: INVALID DATA.'
      ));
    }
  }
?>
