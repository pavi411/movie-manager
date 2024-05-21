<?php
  require_once($ROOT_PATH . '/config.php');

  function getDBInstance() {
    $host = DB_HOST;
    $db   = DB_NAME;
    $user = DB_USERNAME;
    $pass = DB_PASSWORD;
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
         $pdo = new PDO($dsn, $user, $pass, $options);
         return $pdo;
    } catch (\PDOException $e) {
         throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
  }

  function checkIfRowExists($table, $keys) {
    // Get PDO instance
    $db = getDBInstance();

    $findQuery = '';
    $params = array();
    foreach ($keys as $key => $value) {
      $findQuery .= $key . ' = :' . $key . ' AND ';
      $params[':' . $key] = $value;
    }
    $findQuery = '('. rtrim($findQuery, ' AND ') . ')';

    $sql = 'SELECT EXISTS(SELECT * FROM ' . $table . ' WHERE ' . $findQuery .') as rowExists';

    try {
      $query = $db->prepare($sql);
      $query->execute($params);
      return $query->fetch(PDO::FETCH_ASSOC)['rowExists'] == 1;
    } catch (\PDOException $e) {
         throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
  }

  function insertValues($table, $values) {
    // Get PDO instance
    $db = getDBInstance();

    $field_list = '';
    $placeholder_list = '';
    $params = array();
    foreach ($values as $key => $value) {
      $field_list .= $key . ', ';
      $placeholder_list .= ':' . $key . ', ';
      $params[':' . $key] = $value;
    }
    $field_list = '(' . rtrim($field_list, ', ') . ')';
    $placeholder_list = '(' . rtrim($placeholder_list, ', ') . ')';

    $sql = 'INSERT INTO ' . $table . ' ' . $field_list . ' VALUES ' . $placeholder_list;

    try {
      $query = $db->prepare($sql);
      $query->execute($params);
      return $db->lastInsertId();
    } catch (\PDOException $e) {
      return false;
    }
  }

  function getValues($table, $fields, $keys = null, $orders = null, $limit = null) {
    // Get PDO instance
    $db = getDBInstance();

    $params = array();

    $sql = 'SELECT ' . implode(',', $fields) . ' FROM ' . $table;

    if(
      !is_null($keys) &&
      is_array($keys) &&
      count($keys) > 0
    ) {
      $valid_types = array('>', '<', '=', '>=', '<=', 'LIKE', 'REGEXP');
      $findQuery = '';
      foreach ($keys as $key => $value) {
        if(
          is_array($value) &&
          isset($value['type']) &&
          isset($value['value']) &&
          in_array($value['type'], $valid_types)
        ) {
          $findQuery .= $key . ' ' . $value['type'] . ' :' . $key . ' AND ';
          $params[':' . $key] = $value['value'];
        } else if(is_array($value)) {
          $val_no = 0;
          foreach ($value as $val) {
            if(
              is_array($val) &&
              isset($val['type']) &&
              isset($val['value']) &&
              in_array($val['type'], $valid_types)
            ) {
              $findQuery .= $key . ' ' . $val['type'] . ' :' . $key . '_' . $val_no . ' AND ';
              $params[':' . $key . '_' . $val_no] = $val['value'];
              $val_no++;
            }
          }
        }
      }
      $findQuery = rtrim($findQuery, ' AND ');
      $sql .= ' WHERE ' . $findQuery;
    }

    if(
      !is_null($orders) &&
      is_array($orders) &&
      count($orders) > 0
    ) {
      $orderQuery = '';
      foreach ($orders as $key => $value) {
        $orderQuery .= $key . ' ' . $value . ', ';
      }
      $orderQuery = rtrim($orderQuery, ', ');
      $sql .= ' ORDER BY ' . $orderQuery;
    }

    if(
      !is_null($limit) &&
      isset($limit['offset']) &&
      isset($limit['count'])
    ) {
      $sql .= ' LIMIT ' . $limit['offset'] . ', ' . $limit['count'];
    }

    try {
      $query = $db->prepare($sql);
      $query->execute($params);
      return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      echo $sql;
      throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
  }

  function updateValues($table, $values, $keys) {
    // Get PDO instance
    $db = getDBInstance();

    $sql = 'UPDATE ' . $table . ' SET ';

    $params = array();

    if(count($values) > 0) {
      foreach ($values as $key => $value) {
        $sql .= $key . ' = :' . $key . '_value, ';
        $params[':' . $key . '_value'] = $value;
      }
      $sql = rtrim($sql, ', ');
    }

    if(count($keys) > 0) {
      $findQuery = '';
      foreach ($keys as $key => $value) {
        $findQuery .= $key . ' = :' . $key . '_key AND ';
        $params[':' . $key . '_key'] = $value;
      }
      $findQuery = rtrim($findQuery, ' AND ');
      $sql .= ' WHERE ' . $findQuery;
    }

    try {
      $query = $db->prepare($sql);
      $query->execute($params);
      return true;
    } catch (\PDOException $e) {
      return false;
    }
  }

  function deleteValues($table, $keys) {
    // Get PDO instance
    $db = getDBInstance();

    $findQuery = '';
    $params = array();
    foreach ($keys as $key => $value) {
      $findQuery .= $key . ' = :' . $key . ' AND ';
      $params[':' . $key] = $value;
    }
    $findQuery = '('. rtrim($findQuery, ' AND ') . ')';

    $sql = 'DELETE FROM ' . $table . ' WHERE ' . $findQuery;

    try {
      $query = $db->prepare($sql);
      $query->execute($params);
      return true;
    } catch (\PDOException $e) {
      return false;
    }
  }
?>
