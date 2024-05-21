<?php
  require_once($ROOT_PATH . '/helpers/database.php');

  function addUser($username, $password, $name) {
    // Hash the password.
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $values = array(
      'username' => $username,
      'password_hashed' => $password_hashed,
      'name' => $name
    );

    return insertValues('users', $values);
  }

  function checkPassword($username, $password) {
    // Retrieve password.
    $retrievedValue = getValues(
      'users',
      array('password_hashed'),
      array('username' => array(
        'type' => '=',
        'value' => $username
      ))
    );

    if($retrievedValue !== false && count($retrievedValue) > 0) {
      $password_hashed = $retrievedValue[0]['password_hashed'];
      return password_verify($password, $password_hashed);
    } else {
      return false;
    }
  }

  function getUserId($username) {
    $userId = getValues('users', array('id'), array('username' => array('type' => '=', 'value' => $username)));
    if($userId !== false) {
      if(count($userId) == 0) {
        return false;
      } else {
        return $userId[0]['id'];
      }
    } else {
      return false;
    }
  }

  function getUsername($user_id) {
    $username = getValues('users', array('username'), array('id' => array('type' => '=', 'value' => $user_id)));
    if($username !== false) {
      if(count($username) == 0) {
        return false;
      } else {
        return $username[0]['username'];
      }
    } else {
      return false;
    }
  }
?>
