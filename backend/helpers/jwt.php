<?php
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

  function decodeJWT($jwt) {
    if($jwt) {
      try {
        $decoded = JWT::decode($jwt, openssl_pkey_get_public(RSA_PUBLIC_KEY), array('RS256'));

        if($decoded->iss != AUTH_SERVER) return false;

        return $decoded;
      } catch (Exception $e) {
        return false;
      }
    } else {
      return false;
    }
  }
?>
