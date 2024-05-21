<?php

  $ROOT_PATH = '.';

  require_once($ROOT_PATH . '/config.php');
  require_once($ROOT_PATH . '/helpers/database.php');
  require_once($ROOT_PATH . '/helpers/jwt.php');
  require_once($ROOT_PATH . '/helpers/user.php');

  function getNotifications() {
    if(
      !isset($_POST['user'])
    ) return json_encode(array(
      'Message' => 'ERROR:'
    ));

    $username = decodeJWT($_POST['user']);
    if($username == false) return json_encode(array(
      'Message' => 'ERROR: INVALID JWT'
    ));
    $username = $username->data->username;

    $user_id = getUserId($username);
    if($user_id === false) return json_encode(array(
      'Message' => 'ERROR: INVALID USERNAME'
    ));

    $keys = array('user_id' => array('type' => '=', 'value' =>$user_id));
    $orders = array('id' => 'DESC');
    $limits = array('offset' => 0, 'count' => 5);

    if(
      !isset($_POST['num']) ||
      !is_numeric($_POST['num']) ||
      $_POST['num'] < 0 ||
      !isset($_POST['page']) ||
      !is_numeric($_POST['page']) ||
      $_POST['page'] < 1
    ) {
      $limits['count'] = intval($_POST['num']);
      $limits['offset'] = intval(($_POST['page'] - 1) * $_POST['num']);
    }

    $num_notifications = getValues(
      'user_notifications',
      array('count(*)'),
      $keys
    )[0]['count(*)'];

    $keys['unread'] = array('type' => '=', 'value' => '1');

    $num_unread = getValues(
      'user_notifications',
      array('count(*)'),
      $keys
    )[0]['count(*)'];

    $notifications = array();

    if($_POST['num'] > 0) {
      $notifications = getValues(
        'user_notifications',
        array('*'),
        $keys,
        $orders,
        $limits
      );
    }

    for($i = 0; $i < count($notifications); $i++) {
      $activity = getValues(
        'user_activity',
        array('*'),
        array(
          'id' => array('type' => '=', 'value' => $notifications[$i]['activity_id'])
        )
      )[0];
      $notifications[$i]['type'] = $activity['type'];
      $notifications[$i]['from'] = getUsername($activity['user_id']);
      $notifications[$i]['movie'] = $activity['movie_id'];

      updateValues(
        'user_notifications',
        array('unread' => 0),
        array('id' => $notifications[$i]['id'])
      );
    }

    return json_encode(array(
      'Message' => 'SUCCESS',
      'totalNotifications' => $num_notifications,
      'unreadNotifications' => $num_unread,
      'Notifications' => $notifications
    ));
  }

  echo getNotifications();

?>
