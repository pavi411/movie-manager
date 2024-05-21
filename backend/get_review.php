<?php
  $ROOT_PATH = '.';

  require_once($ROOT_PATH . '/helpers/review.php');

  if(
    !isset($_POST['type']) ||
    !in_array($_POST['type'], array('get', 'set')) ||
    !isset($_POST['movie'])
  ) {
    echo json_encode(array(
      'Message' => 'ERROR: INVALID DATA'
    ));
  } else {
    if(
      $_POST['type'] == 'set' &&
      isset($_POST['user']) &&
      isset($_POST['review'])
    ) {
      $user_name = decodeJWT($_POST['user']);
      if($user_name != false) {
        $user_name = $user_name->data->username;
        $user_id = getUserId($user_name);
        if($user_id != false) {
          echo addReview($user_id, $_POST['movie'], $_POST['review']);
        } else {
          echo json_encode(array(
            'Message' => 'ERROR: INVALID DATA.'
          ));
        }
      }
    } else if(
      $_POST['type'] == 'get' &&
      isset($_POST['user'])
    ) {
      $user_id = null;
      $user_name = decodeJWT($_POST['user']);
      if($user_name != false) {
        $user_name = $user_name->data->username;
        $id = getUserId($user_name);
        if($id != false) {
          $user_id = $id;
          echo getUserReview($_POST['movie'], $user_id);
        } else echo json_encode(array(
            'Message' => 'ERROR: INVALID DATA.'
        ));
      } else echo json_encode(array(
          'Message' => 'ERROR: INVALID DATA.'
      ));
    } else if (
      $_POST['type'] == 'get' &&
      isset($_POST['page']) &&
      is_numeric($_POST['page']) &&
      intval($_POST['page']) >= 1
    ) {
      echo getReviews($_POST['movie'], $_POST['page']);
    } else {
      echo json_encode(array(
        'Message' => 'ERROR: INVALID DATA.'
      ));
    }
  }
?>
