<?php
  $ROOT_PATH = '.';

  require_once($ROOT_PATH . '/helpers/data_validation.php');
  require_once($ROOT_PATH . '/helpers/validation.php');
  require_once($ROOT_PATH . '/helpers/database.php');
  require_once($ROOT_PATH . '/helpers/user.php');
  require_once($ROOT_PATH . '/helpers/json.php');
  require_once($ROOT_PATH . '/config.php');

  require_once($ROOT_PATH . '/libs/php-jwt-master/src/BeforeValidException.php');
  require_once($ROOT_PATH . '/libs/php-jwt-master/src/ExpiredException.php');
  require_once($ROOT_PATH . '/libs/php-jwt-master/src/SignatureInvalidException.php');
  require_once($ROOT_PATH . '/libs/php-jwt-master/src/JWT.php');
  use \Firebase\JWT\JWT;

  function getLogin() {
    if(
      !isset($_POST['login']) ||
      !is_json($_POST['login'])
    ) return json_encode((object) array(
      'message' => 'ERROR'
    ));

    $login = json_decode($_POST['login']);

    if(!validate_login($login)) return json_encode((object) array(
      'message' => 'ERROR'
    ));

    if(checkPassword($login->username, $login->password)){

      $token = array(
        'iss' => AUTH_SERVER,
        'iat' => time(),
        'data' => array(
          'username' => $login->username
        )
      );

      $jwt = JWT::encode($token, openssl_pkey_get_private(RSA_PRIVATE_KEY), 'RS256');
      return json_encode((object) array(
        'message' => 'SUCCESS',
        'jwt' => $jwt,
        'username' => $login->username
      ));
    } else {
      return json_encode((object) array(
        'message' => 'ERROR: INVALID CREDENTIALS'
      ));
    }
  }

  echo getLogin();
?>
