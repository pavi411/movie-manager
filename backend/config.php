<?php
  /*
  * The domain where the Form Builder is hosted.
  * It must end in a forward slash '/'
  * 'http://www.example.com/' is valid while 'http://www.example.com' is not.
  */
  //define('DOMAIN', 'http://localhost:8080/form_manager/');
  define('BACKEND', 'http://localhost/movie_manager/backend/');
  define('AUTH_SERVER', BACKEND);

  define('CLIENT', 'http://localhost/movie_manager/frontend/');

  /*
  * The MYSQL database configuration.
  */
  define('DB_HOST', 'localhost');
  define('DB_NAME', 'movie_manager');
  define('DB_USERNAME', 'root');
  define('DB_PASSWORD', '');

  /*
  * Default timezone.
  * Set this to the timezone your MYSQL Databse uses.
  * Must be in IntlTimeZone format.
  */
  define('SERVER_TIMEZONE', "Asia/Calcutta");

  define('RSA_PRIVATE_KEY', 'file://C:/xampp/htdocs/movie_manager/backend/keys/private.pem');
  define('RSA_PUBLIC_KEY', 'file://C:/xampp/htdocs/movie_manager/backend/keys/public.pem');

  define('ADMINS', array('aananthv'));
?>
