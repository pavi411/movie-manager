<?php
  $ROOT_PATH = '.';

  require_once($ROOT_PATH . '/helpers/database.php');

  $keys = array();
  $orders = array();
  $limit = array();

  if(isset($_GET['id'])) {
    $keys['id'] = array(
      'type' => '=',
      'value' => $_GET['id']
    );
  }

  if(isset($_GET['search'])) {
    foreach (explode(' ', $_GET['search']) as $search_term) {
      $keys['title'][] = array(
        'type' => 'LIKE',
        'value' => '%' . $search_term . '%'
      );
    }
  }

  $available_genres = array(
    'Action','Adventure','Animation','Biography','Comedy','Crime','Documentary','Drama','Family','Fantasy','Film-Noir','Game-Show','History','Horror','Music','Musical','Mystery','News','Reality-TV','Romance','Sci-Fi','Sport','Talk-Show','Thriller','War','Western'
  );

  if(
    isset($_GET['genre']) &&
    in_array($_GET['genre'], $available_genres)
  ) {
    $keys['genres'] = array(
      'type' => 'LIKE',
      'value' => '%' . $_GET['genre'] . '%'
    );
  }

  if(
    isset($_GET['imdb']) &&
    is_numeric($_GET['imdb'])
  ) {
    $keys['imdb_rating'] = array(
      'type' => '>=',
      'value' => intval($_GET['imdb'])
    );
  }

  if(
    isset($_GET['mmdb']) &&
    is_numeric($_GET['mmdb'])
  ) {
    $keys['mmdb_rating'] = array(
      'type' => '>=',
      'value' => intval($_GET['mmdb'])
    );
  }

  $available_orders = array('year', 'title', 'runtime', 'id');
  $available_order = array('ASC', 'DESC');

  if(
    isset($_GET['sort_by']) &&
    in_array($_GET['sort_by'], $available_orders) &&
    isset($_GET['sort_order']) &&
    in_array($_GET['sort_order'], $available_order)
  ) {
    $orders[$_GET['sort_by']] = $_GET['sort_order'];
  }

  $page = 1;
  $count = 20;

  if(
    isset($_GET['page']) &&
    is_numeric($_GET['page']) &&
    intval($_GET['page']) > 1
  ) {
    $page = intval($_GET['page']);
  }

  $limit['offset'] = ($page-1)*$count;
  $limit['count'] = $count;

  $response = array();

  $num_results = getValues('movie_data', array('count(*)'), $keys)[0]['count(*)'];

  if($num_results == 0) {
    $response['Response'] = 'False';
    $response['Error'] = 'No results found.';
  } else {
    $response['Response'] = 'True';
    $response['totalResults'] = $num_results;
    $response['Search'] = getValues('movie_data', array('*'), $keys, $orders, $limit);

    for ($r = 0; $r < count($response['Search']); $r++) {
      $response['Search'][$r]['torrents'] = getValues(
        'torrent_data',
        array('*'),
        array('movie_id' => array('type' => '=', 'value' => $response['Search'][$r]['id'])
      ));
    }
  }

  echo json_encode($response);
?>
