<?php
  $ROOT_PATH = '.';

  require_once($ROOT_PATH . '/config.php');
  require_once($ROOT_PATH . '/helpers/user.php');
  require_once($ROOT_PATH . '/helpers/movie.php');
  require_once($ROOT_PATH . '/helpers/jwt.php');

  function checkIfAdmin($jwt) {
    $user_name = decodeJWT($jwt);
    if($user_name !== false) {
      $user_name = $user_name->data->username;
      return in_array($user_name, ADMINS);
    }
    return false;
  }

  function manage_movie() {
    if(
      !isset($_POST['user']) ||
      !is_string($_POST['user']) ||
      !checkIfAdmin($_POST['user'])
    ) return json_encode(array(
      'Message' => 'ERROR: INVALID OR UNAUTHORISED USER'
    ));

    if(
      !isset($_POST['type']) ||
      !in_array($_POST['type'], array('add', 'delete'))
    ) return json_encode(array(
      'Message' => 'ERROR: INVALID OPERATION'
    ));

    if(
      $_POST['type'] == 'add' &&
      isset($_POST['movie_data'])
    ) {
      return addMovie(json_decode($_POST['movie_data']));
    } else if (
      $_POST['type'] == 'delete' &&
      isset($_POST['imdb_code'])
    ) {
      $movie_id = getMovieId($_POST['imdb_code']);

      if($movie_id !== false) {
        return deleteMovie($movie_id);
      } else return json_encode(array(
          'Message' => 'ERROR: INVALID ID'
      ));
    } else return json_encode(array(
      'Message' => 'ERROR: INVALID DATA'
    ));
  }

  echo manage_movie();

?>
