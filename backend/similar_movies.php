<?php
  $ROOT_PATH = '.';

  require_once($ROOT_PATH . '/helpers/database.php');
  require_once($ROOT_PATH . '/helpers/recommendation.php');

  function similar_movies() {
    if(
      !isset($_GET['movie_id']) ||
      !checkIfRowExists('movie_recommendations', array('movie_id' => $_GET['movie_id']))
      ) return array('Message' => 'ERROR', 'ERROR' => 'Invalid ID.');

    return json_encode(get_similar_movies($_GET['movie_id']));
  }

  echo similar_movies();
?>
