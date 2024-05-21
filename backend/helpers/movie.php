<?php
  require_once($ROOT_PATH . '/helpers/database.php');

  function addMovie($movie_data) {
    $res = true;
    $movie_id = false;
    $movie_data = (array) $movie_data;
    $torrents = $movie_data['torrents'];
    unset($movie_data['torrents']);
    if(
      checkIfRowExists('movie_data', array('imdb_code' => $movie_data['imdb_code']))
    ) {
      $movie_id = updateValues(
        'movie_data',
        $movie_data,
        array('imdb_code' => $movie_data['imdb_code'])
      ) !== false;
    } else {
      $movie_id = insertValues(
        'movie_data',
        $movie_data
      ) !== false;
    }

    if($movie_id === false) return json_encode(array(
      'Message' => 'ERROR: DATABASE INSERT FAILED'
    ));

    $movie_id = json_decode(getMovieId($movie_data['imdb_code']))->MovieID;

    $res = deleteValues(
      'torrent_data',
      array('movie_id' => $movie_id)
    );

    if($res === false) return json_encode(array(
      'Message' => 'ERROR: DATABASE DELETE FAILED'
    ));

    foreach ($torrents as $torrent) {
      $torrent = (array) $torrent;
      $torrent['movie_id'] = $movie_id;
      $res = insertValues(
        'torrent_data',
        $torrent
      );
      if($res === false) return json_encode(array(
        'Message' => 'ERROR: DATABASE INSERT FAILED2'
      ));
    }

    return json_encode(array(
      'Message' => 'SUCCESS'
    ));
  }

  function deleteMovie($id) {
    $res = deleteValues(
      'movie_data',
      array('id' => $id)
    );
    if($res) {
      return json_encode(array(
        'Message' => 'SUCCESS'
      ));
    } else {
      return json_encode(array(
        'Message' => 'ERROR: DATABASE DELETE FAILED'
      ));
    }
  }

  function getMovieId($imdb_code) {
    $movie_id = getValues('movie_data', array('id'), array('imdb_code' => array('type' => '=', 'value' => $imdb_code)));

    if($movie_id !== false) {
      if(count($movie_id) == 0) {
        return json_encode(array(
          'Message' => 'ERROR: INVALID CODE'
        ));
      } else {
        return json_encode(array(
          'Message' => 'SUCCESS',
          'MovieID' => $movie_id[0]['id']
        ));
      }
    } else {
      return json_encode(array(
        'Message' => 'ERROR: DATABASE FETCH FAILED'
      ));
    }
  }
?>
