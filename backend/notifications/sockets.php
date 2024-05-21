#!/usr/bin/env php
<?php

$ROOT_PATH = '..';

require_once($ROOT_PATH . '/libs/PHPWebsockets/websockets.php');
require_once($ROOT_PATH . '/helpers/json.php');
require_once($ROOT_PATH . '/helpers/jwt.php');

class MyUser extends WebSocketUser {

  public $username = '';

  function __construct($id, $socket) {
    parent::__construct($id, $socket);
  }
}

class notificationServer extends WebSocketServer {

  public $users = array();

  function __construct($addr, $port, $bufferLength) {
    parent::__construct($addr, $port, $bufferLength);
    $this->userClass = 'MyUser';
  }

  //protected $maxBufferSize = 1048576; //1MB... overkill for an echo server, but potentially plausible for other applications.

  private function processMessage($user, $message) {
    if(!is_json($message)) return json_encode(array(
      'Message' => 'UNKNOWN ERROR'
    ));

    $message = json_decode($message);

    if(
      !property_exists($message, 'type') ||
      !in_array($message->type, array('CONNECT', 'SEND'))
    ) return json_encode(array(
      'Message' => 'ERROR: INVALID MESSAGE 1'
    ));

    if($message->type == 'CONNECT') {
      if(
        !property_exists($message, 'user')
      ) return json_encode(array(
          'Message' => 'ERROR: INVALID MESSAGE 2'
      ));

      $decoded = decodeJWT($message->user);
      if($decoded === false) return json_encode(array(
          'Message' => 'ERROR: INVALID JWT'
      ));

      $username = $decoded->data->username;

      $user->username = $username;

      $this->users[$username] = $user;

      return json_encode(array(
          'Message' => 'CONNECTED'
      ));
    }

    if($message->type == 'SEND') {
      if(
        $user->username == ''
      ) return json_encode(array(
          'Message' => 'ERROR: NOT AUTHORISED'
      ));

      if(
        !property_exists($message, 'to_user') ||
        !property_exists($message, 'message')
      ) return json_encode(array(
          'Message' => 'ERROR: INVALID MESSAGE'
      ));

      if(
        isset($this->users[$message->to_user])
      ) {
        $this->send($this->users[$message->to_user], $message->message);
      }

      return json_encode(array(
          'Message' => 'SENT'
      ));
    }
  }

  protected function process ($user, $message) {
    $this->send($user, $this->processMessage($user, $message));
  }

  protected function connected ($user) {
  }

  protected function closed ($user) {
    if($user->username != '') {
      unset($this->users[$user->username]);
    }
  }
}

$notif = new notificationServer("localhost","9000", 1024);

try {
  $notif->run();
}
catch (Exception $e) {
  $notif->stdout($e->getMessage());
}
