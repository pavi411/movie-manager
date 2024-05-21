<?php
  require_once($ROOT_PATH . '/helpers/database.php');

  function get_collaborative_filtering($user_id) {
    $movie_ids = getValues(
      'user_recommendations',
      array('*'),
      array('user_id' => array('type' => '=', 'value' => $user_id))
    );

    if($movie_ids == false) return array('Message' => 'ERROR');

    $recommendations = array(
      '0' => array(),
      '1' => array()
    );

    foreach ($movie_ids as $movie_id) {
      for($i = 1; $i <= 10; $i++) {
        $recommendations[$movie_id['type']][] = getValues(
          'movie_data',
          array('*'),
          array('id' => array('type' => '=', 'value' => $movie_id['movie_' . $i]))
        )[0];

        $recommendations[$movie_id['type']][$i - 1]['torrents'] = getValues(
          'torrent_data',
          array('*'),
          array('movie_id' => array('type' => '=', 'value' => $recommendations[$movie_id['type']][$i - 1]['id'])
        ));
      }
    }

    return array('Message' => 'SUCCESS', 'Recommendations' => $recommendations);
  }

  function get_similar_movies($movie_id) {
    $movies = getValues(
      'movie_recommendations',
      array('*'),
      array('movie_id' => array('type' => '=', 'value' => $movie_id))
    )[0];

    $similar_movies = array();

    if($movies == false) return array('Message' => 'ERROR');

    for ($i = 1; $i <= 4; $i++) {
      $movie_data = getValues(
        'movie_data',
        array('*'),
        array('id' => array('type' => '=', 'value' => $movies['movie_' . $i]))
      )[0];

      $movie_data['torrents'] = getValues(
        'torrent_data',
        array('*'),
        array('movie_id' => array('type' => '=', 'value' => $movie_data['id']))
      );

      $similar_movies[] = $movie_data;
    }

    return array('Message' => 'SUCCESS', 'Similar' => $similar_movies);
  }

?>
