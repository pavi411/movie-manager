<?php

  require_once($ROOT_PATH . '/config.php');
  require_once($ROOT_PATH . '/helpers/database.php');
  require_once($ROOT_PATH . '/helpers/user.php');
  require_once($ROOT_PATH . '/helpers/jwt.php');

  function setRating($user_id, $movie_id, $rating) {
    // Validate Data.
    if(
      !checkIfRowExists('users', array('id' => $user_id)) ||
      !checkIfRowExists('movie_data', array('id' => $movie_id)) ||
      !is_numeric($rating) ||
      intval($rating) <= 0 ||
      intval($rating) > 10
    ) {
      return json_encode(array(
        'Message' => 'ERROR: INVALID DATA.'
      ));
    }

    $res = true;

    // Add Activity
    $activity_id = insertValues(
      'user_activity',
      array(
        'user_id' => $user_id,
        'movie_id' => $movie_id,
        'type' => 0
      )
    );

    if(
      checkIfRowExists('user_rating', array('user_id' => $user_id, 'movie_id' => $movie_id))
    ) {
      $res = updateValues(
        'user_rating',
        array('activity_id' => $activity_id, 'rating' => $rating),
        array('user_id' => $user_id, 'movie_id' => $movie_id)
      ) !== false;
    } else {
      $res = insertValues(
        'user_rating',
        array(
          'activity_id' => $activity_id,
          'user_id' => $user_id,
          'movie_id' => $movie_id,
          'rating' => $rating
        )
      ) !== false;
    }

    if($res == true) {
      return json_encode(array(
        'Message' => 'SUCCESS'
      ));
    } else {
      return json_encode(array(
        'Message' => 'ERROR: DATABASE UPDATE FAILED'
      ));
    }
  }

  function getUserRating($user_id, $movie_id) {
    $rating = getValues(
      'user_rating',
      array('rating'),
      array(
        'user_id' => array('type' => '=', 'value' => $user_id),
        'movie_id' => array('type' => '=', 'value' => $movie_id)
      )
    );
    if($rating === false) {
      return json_encode(array(
        'Message' => 'ERROR: DATABASE FETCH FAILED'
      ));
    }

    if(count($rating) == 0) {
      return json_encode(array(
        'Message' => 'SUCCESS',
        'Rating' => 0
      ));
    } else {
      return json_encode(array(
        'Message' => 'SUCCESS',
        'Rating' => $rating[0]['rating']
      ));
    }
  }

  function getRating($movie_id) {
    $ratings = getValues(
      'user_rating',
      array('rating'),
      array(
        'movie_id' => array('type' => '=', 'value' => $movie_id)
      )
    );

    if($ratings === false) {
      return json_encode(array(
        'Message' => 'ERROR: DATABASE FETCH FAILED'
      ));
    }

    $rating = 0;
    foreach ($ratings as $rate) {
      $rating += $rate['rating'];
    }

    $num_ratings = count($ratings);

    if($num_ratings == 0) {
      $rating = 0;
    } else {
      $rating /= $num_ratings;
    }

    return json_encode(array(
      'Message' => 'SUCCESS',
      'Rating' => $rating,
      'Ratings' => $num_ratings
    ));
  }
?>
