<?php

  require_once($ROOT_PATH . '/config.php');
  require_once($ROOT_PATH . '/helpers/database.php');
  require_once($ROOT_PATH . '/helpers/user.php');
  require_once($ROOT_PATH . '/helpers/jwt.php');
  require_once($ROOT_PATH . '/helpers/rating.php');

  function addReview($user_id, $movie_id, $review) {
    // Validate Data.
    if(
      !checkIfRowExists('users', array('id' => $user_id)) ||
      !checkIfRowExists('movie_data', array('id' => $movie_id)) ||
      !is_string($review) ||
      strlen($review) > 1024
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
        'type' => 1
      )
    );

    $res = true;

    if(
      checkIfRowExists('user_reviews', array('user_id' => $user_id, 'movie_id' => $movie_id))
    ) {
      $res = updateValues(
        'user_reviews',
        array('activity_id' => $activity_id, 'review' => $review),
        array('user_id' => $user_id, 'movie_id' => $movie_id)
      ) !== false;
    } else {
      $res = insertValues(
        'user_reviews',
        array(
          'activity_id' => $activity_id,
          'user_id' => $user_id,
          'movie_id' => $movie_id,
          'review' => $review
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

  function getReviews($movie_id, $page = 1, $count = 5) {
    $keys = array(
      'movie_id' => array('type' => '=', 'value' => $movie_id)
    );

    $limits = array(
      'offset' => ($page - 1) * $count,
      'count' => $count
    );

    $num_reviews = getValues(
      'user_reviews',
      array('count(*)'),
      $keys
    )[0]['count(*)'];

    $reviews = getValues(
      'user_reviews',
      array('id', 'user_id', 'review'),
      $keys,
      null,
      $limits
    );

    if($reviews === false) {
      return json_encode(array(
        'Message' => 'ERROR: DATABASE FETCH FAILED'
      ));
    }

    for($i = 0; $i < count($reviews); $i++) {
      $reviews[$i]['upvotes'] = getVotes($reviews[$i]['id']);
      $reviews[$i]['username'] = getUsername($reviews[$i]['user_id']);
      $reviews[$i]['rating'] = json_decode(getUserRating($reviews[$i]['user_id'], $movie_id))->Rating;
    }

    return json_encode(array(
      'Message' => 'SUCCESS',
      'totalReviews' => $num_reviews,
      'Reviews' => $reviews
    ));
  }

  function getUserReview($movie_id, $user_id) {
    $keys = array(
      'movie_id' => array('type' => '=', 'value' => $movie_id),
      'user_id' => array('type' => '=', 'value' => $user_id)
    );

    $review = getValues(
      'user_reviews',
      array('id', 'user_id', 'review'),
      $keys
    );

    if($review === false) {
      return json_encode(array(
        'Message' => 'ERROR: DATABASE FETCH FAILED'
      ));
    }

    if(count($review) == 0) return json_encode(array(
        'Message' => 'NOT REVIEWED'
    ));

    $review = $review[0];

    $review['upvotes'] = getVotes($review['id']);
    $review['username'] = getUsername($user_id);
    $review['rating'] = json_decode(getUserRating($user_id, $movie_id))->Rating;

    return json_encode(array(
      'Message' => 'SUCCESS',
      'Review' => $review
    ));
  }

  function addVote($user_id, $review_id, $type) {
    $review_owner_id = getValues(
      'user_reviews',
      array('user_id', 'movie_id'),
      array(
        'id' => array('type' => '=', 'value' => $review_id)
      )
    );
    // Validate Data.
    if(
      !checkIfRowExists('users', array('id' => $user_id)) ||
      count($review_owner_id) == 0
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
        'movie_id' => $review_owner_id[0]['movie_id'],
        'type' => 2
      )
    );

    // Add Notif
    $notif_id = insertValues(
      'user_notifications',
      array(
        'user_id' => $review_owner_id[0]['user_id'],
        'activity_id' => $activity_id
      )
    );

    $res = true;

    if(
      checkIfRowExists('user_upvotes', array('user_id' => $user_id, 'review_id' => $review_id))
    ) {
      $res = updateValues(
        'user_upvotes',
        array('activity_id' => $activity_id, 'type' => $type),
        array('user_id' => $user_id, 'review_id' => $review_id)
      ) !== false;
    } else {
      $res = insertValues(
        'user_upvotes',
        array(
          'activity_id' => $activity_id,
          'user_id' => $user_id,
          'review_id' => $review_id,
          'type' => $type
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

  function getUserVote($review_id, $user_id) {
    $vote = getValues(
      'user_upvotes',
      array('type'),
      array(
        'user_id' => array('type' => '=', 'value' => $user_id),
        'review_id' => array('type' => '=', 'value' => $review_id)
      )
    );
    if($vote === false) {
      return json_encode(array(
        'Message' => 'ERROR: DATABASE FETCH FAILED'
      ));
    }
    if(count($vote) == 0) {
      return json_encode(array(
        'Message' => 'SUCCESS',
        'Vote' => 0
      ));
    } else {
      return json_encode(array(
        'Message' => 'SUCCESS',
        'Vote' => $vote[0]['type']
      ));
    }
  }

  function getVotes($review_id) {
    $votes = getValues(
      'user_upvotes',
      array('type'),
      array(
        'review_id' => array('type' => '=', 'value' => $review_id)
      )
    );

    if($votes === false) {
      return json_encode(array(
        'Message' => 'ERROR: DATABASE FETCH FAILED'
      ));
    }

    $upvotes = 0;
    foreach ($votes as $vote) {
      $upvotes += $vote['type'];
    }

    return $upvotes;
  }

?>
