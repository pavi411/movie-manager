<?php
  $currentPage = 'Notifications';
  $ROOT_PATH = '.';

  require_once($ROOT_PATH . '/config.php');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php require_once($ROOT_PATH . '/elements/header.php'); ?>

    <link rel="stylesheet" href="<?php echo $ROOT_PATH; ?>/css/scrollable_div.css">
  </head>
  <body>
    <?php require_once($ROOT_PATH . '/elements/navbar.php'); ?>
    <?php require_once($ROOT_PATH . '/elements/auth.php'); ?>
    <?php require_once($ROOT_PATH . '/elements/notifications.php'); ?>

    <div class="container">
      <ul class="list-group">
        <li class="list-group-item list-group-item-dark d-flex justify-content-between">
          <button type="button" class="btn btn-outline-success">Prev</button>
          <button type="button" class="btn btn-outline-success">Next</button>
        </li>
        <div id="notifList">
          <li class="list-group-item list-group-item-dark">
            <strong>aananthv</strong> voted on your review.
          </li>
        </div>
      </ul>
    </div>

    <script type="text/javascript">
      function addNotificationToList(notification) {
        let notifList = document.querySelector('#notifList');
        let notif = document.createElement('li');
        notif.setAttribute('class', 'list-group-item list-group-item-dark');
        if(notification['type'] == 2) {
          notif.innerHTML = '<strong>' + notification['from'] + '</strong> voted on your review.';
          notif.onclick = function() {
            show_movie_by_id(notification['movie']);
          }
        }
        if(notifList.childElementCount >  10) {
          notifList.removeChild(notifList.childNodes[0]);
        }
        notifList.appendChild(notif);
      }
    </script>

    <?php require_once($ROOT_PATH . '/elements/movie_details.php'); ?>
    <?php require_once($ROOT_PATH . '/elements/footer.php'); ?>
  </body>
</html>
